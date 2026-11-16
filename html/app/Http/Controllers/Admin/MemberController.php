<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Services\ExpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * 관리자 페이지 회원관리. 1차는 목록 / 상세 / 계정 상태와 권한 레벨 변경만 한다.
 */
class MemberController extends Controller
{
    const STATUS_LABELS = [1 => '활성', 0 => '비활성', 2 => '해지'];

    /** 고를 수 있는 권한 레벨. 코드에서 쓰는 값은 1(일반), 9(매니저), 10(최고관리자) 뿐이다. */
    const LEVEL_LABELS = [1 => '일반', 9 => '매니저', 10 => '최고관리자'];

    const PER_PAGE = 20;

    /** @var ExpService */
    protected $exp;

    public function __construct(ExpService $exp)
    {
        $this->exp = $exp;
    }

    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $status = $request->query('status', '');
        $level = $request->query('level', '');

        $query = $this->baseQuery();

        if ($q !== '') {
            $like = '%' . addcslashes($q, '%_\\') . '%';
            $query->where(function ($w) use ($like) {
                $w->where('m.mq_user_id', 'like', $like)
                    ->orWhere('m.mq_user_name', 'like', $like)
                    ->orWhere('m.mq_user_email', 'like', $like);
            });
        }
        if ($status !== '' && isset(self::STATUS_LABELS[(int) $status])) {
            $query->where('m.mq_status', (int) $status);
        }
        if ($level === 'normal') {
            $query->where('m.mq_level', '<', 9);
        } elseif ($level === 'manager') {
            $query->where('m.mq_level', 9);
        } elseif ($level === 'admin') {
            $query->where('m.mq_level', '>=', 10);
        }

        $members = $query->orderByDesc('m.idx')->paginate(self::PER_PAGE)->appends($request->query());

        foreach ($members as $m) {
            $m->grade = $this->exp->gradeFor((int) $m->total_exp);
        }

        return view('admin.members.index', [
            'members'      => $members,
            'filters'      => compact('q', 'status', 'level'),
            'statusLabels' => self::STATUS_LABELS,
            'levelLabels'  => self::LEVEL_LABELS,
        ]);
    }

    /**
     * 상세 서랍에 채울 값. (목록에서 회원을 누르면 불러온다)
     */
    public function show($idx)
    {
        $m = $this->baseQuery()->where('m.idx', (int) $idx)->first();
        abort_if(!$m, 404);

        $grade = $this->exp->gradeFor((int) $m->total_exp);
        $devices = DB::table('mq_push_tokens')->where('member_idx', $m->idx)->where('mq_status', 1)->count();

        return response()->json([
            'idx'          => $m->idx,
            'user_id'      => $m->mq_user_id,
            'name'         => $m->mq_user_name,
            'email'        => $m->mq_user_email,
            'phone'        => $m->mq_phone,
            'provider'     => $m->mq_provider ?: 'email',
            'reg_date'     => $m->mq_reg_date,
            'last_seen'    => $m->last_seen,
            'exp'          => (int) $m->total_exp,
            'grade'        => $grade,
            'marketing'    => (bool) $m->mq_marketing_agree,
            'marketing_date' => $m->mq_marketing_agree_date,
            'devices'      => $devices,
            'status'       => (int) $m->mq_status,
            'level'        => (int) $m->mq_level,
            'is_me'        => (int) $m->idx === (int) Auth::id(),
            'update_url'   => route('admin.members.update', $m->idx),
            'push_url'     => route('admin.push.send.create', ['users' => $m->mq_user_id]),
        ]);
    }

    public function update(Request $request, $idx)
    {
        $member = Member::findOrFail((int) $idx);

        $data = $request->validate([
            'mq_status' => 'required|in:' . implode(',', array_keys(self::STATUS_LABELS)),
            'mq_level'  => 'required|in:' . implode(',', array_keys(self::LEVEL_LABELS)) . ',' . (int) $member->mq_level,
        ], [], [
            'mq_status' => '계정 상태',
            'mq_level'  => '권한 레벨',
        ]);

        // 자기 계정을 막거나 권한을 내리면 관리자 페이지에 다시 들어올 수 없다.
        if ((int) $member->idx === (int) Auth::id()
            && ((int) $data['mq_status'] !== (int) $member->mq_status || (int) $data['mq_level'] !== (int) $member->mq_level)) {
            return back()->with('admin_error', '내 계정의 상태와 권한은 여기서 바꿀 수 없습니다.');
        }

        $before = ['mq_status' => (int) $member->mq_status, 'mq_level' => (int) $member->mq_level];
        $member->mq_status = (int) $data['mq_status'];
        $member->mq_level = (int) $data['mq_level'];
        $member->save();

        Log::info('관리자 회원 정보 변경', [
            'admin'  => Auth::user()->mq_user_id,
            'member' => $member->mq_user_id,
            'before' => $before,
            'after'  => ['mq_status' => $member->mq_status, 'mq_level' => $member->mq_level],
        ]);

        return back()->with('admin_message', $member->mq_user_id . ' 회원 정보를 저장했습니다.');
    }

    /**
     * 목록/상세 공통 쿼리. 등급(경험치)과 마지막 접속(로그인과 하루 첫 접속 중 늦은 쪽)을 붙인다.
     */
    protected function baseQuery()
    {
        return DB::table('mq_member as m')
            ->leftJoin('mq_member_exp as e', 'e.mq_user_id', '=', 'm.mq_user_id')
            ->select([
                'm.idx', 'm.mq_user_id', 'm.mq_user_name', 'm.mq_user_email', 'm.mq_phone', 'm.mq_provider',
                'm.mq_status', 'm.mq_level', 'm.mq_reg_date', 'm.mq_marketing_agree', 'm.mq_marketing_agree_date',
                DB::raw('COALESCE(e.mq_total_exp, 0) as total_exp'),
                DB::raw('NULLIF(GREATEST(COALESCE(m.mq_last_login_date, \'1970-01-01\'), COALESCE(e.mq_last_visit_date, \'1970-01-01\')), \'1970-01-01\') as last_seen'),
            ]);
    }
}
