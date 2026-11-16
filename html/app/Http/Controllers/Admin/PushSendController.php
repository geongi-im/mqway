<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\PushService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * 관리자 페이지 앱 푸시관리 > 회원 지정 발송.
 *
 * 관리자가 고른 회원에게 지금 바로 알림을 보낸다. 받는 사람 규칙은 다른 발송과 같다.
 * (활성 회원 + 마케팅 정보 수신 동의 + 알림을 켠 기기) 고른 회원 중 이 규칙에 걸리는 회원은 받지 못한다.
 *
 * 자동 발송 규칙과 달리 발송 시간 제한이 없다. 관리자가 직접 고른 회원에게 보내는 것이라 언제든 보낼 수 있게 했다.
 */
class PushSendController extends Controller
{
    /** 회원 검색 결과 수 */
    const SEARCH_LIMIT = 20;

    /** @var PushService */
    protected $push;

    public function __construct(PushService $push)
    {
        $this->push = $push;
    }

    /**
     * 발송 화면. ?users=hong,kim 으로 들어오면 그 회원을 미리 고른다. (회원 상세의 "알림 보내기")
     */
    public function create(Request $request)
    {
        // 보내다 검사에 걸려 돌아왔으면 그때 고른 회원을 그대로 쓴다. (주소의 users 는 처음 열 때만)
        if ($request->session()->hasOldInput()) {
            $userIds = (array) old('user_ids', []);
        } else {
            $userIds = explode(',', (string) $request->query('users', ''));
        }
        $userIds = array_values(array_unique(array_filter(array_map('trim', $userIds))));

        return view('admin.push.send', [
            'selected'    => $userIds ? $this->describe($this->memberQuery()->whereIn('m.mq_user_id', $userIds)->get()) : [],
            // 같은 화면에서 두 번 눌러도 한 번만 나가도록 화면마다 새 키를 준다.
            'sendKey'     => Str::random(32),
            'placeholder' => PushService::NAME_PLACEHOLDER,
            'maxUsers'    => PushService::USER_IDS_MAX,
        ]);
    }

    /**
     * 받는 회원 검색. 아이디 / 이름 / 이메일 일부로 찾는다.
     */
    public function recipients(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        if (mb_strlen($q, 'UTF-8') < 1) {
            return response()->json(['members' => []]);
        }

        $like = '%' . addcslashes($q, '%_\\') . '%';
        $rows = $this->memberQuery()
            ->where('m.mq_status', '!=', 2)
            ->where(function ($w) use ($like) {
                $w->where('m.mq_user_id', 'like', $like)
                    ->orWhere('m.mq_user_name', 'like', $like)
                    ->orWhere('m.mq_user_email', 'like', $like);
            })
            ->orderByDesc('m.idx')
            ->limit(self::SEARCH_LIMIT)
            ->get();

        return response()->json(['members' => $this->describe($rows)]);
    }

    public function send(Request $request)
    {
        $data = $request->validate([
            'user_ids'   => 'required|array|min:1|max:' . PushService::USER_IDS_MAX,
            'user_ids.*' => 'required|string|max:255',
            'mq_title'   => 'required|string|max:' . PushService::TITLE_MAX,
            'mq_body'    => 'nullable|string|max:' . PushService::BODY_MAX,
            'mq_link'    => ['nullable', 'string', 'max:500', 'regex:#^/(?!/)#'],
            'send_key'   => 'required|string|size:32',
        ], [
            'user_ids.required' => '받을 회원을 한 명 이상 골라주세요.',
            'user_ids.max'      => '한 번에 ' . PushService::USER_IDS_MAX . '명까지 보낼 수 있습니다.',
            'mq_link.regex'     => '누르면 열 주소는 / 로 시작하는 사이트 안의 경로로 적어주세요. (예: /board-scrap/create)',
        ], [
            'mq_title' => '제목',
            'mq_body'  => '본문',
            'mq_link'  => '누르면 열 주소',
        ]);

        $userIds = array_values(array_unique(array_map('trim', $data['user_ids'])));

        // 받을 수 있는 회원이 없으면 이력을 만들지 않고 돌려보낸다. (고쳐서 다시 보낼 수 있게)
        $members = $this->describe($this->memberQuery()->whereIn('m.mq_user_id', $userIds)->get());
        $reachable = array_values(array_filter($members, function ($m) { return $m['reachable']; }));
        if (!$reachable) {
            return back()->withInput()->with('admin_error', '고른 회원 중 알림을 받을 수 있는 회원이 없습니다.');
        }

        $prepared = $this->push->prepareForUsers(
            $userIds,
            $data['mq_title'],
            $data['mq_body'] ?? null,
            $this->push->normalizeLink($data['mq_link'] ?? '/') ?: $this->push->normalizeLink('/'),
            PushService::SOURCE_MANUAL,
            'manual:' . $data['send_key']
        );

        switch ($prepared['result']) {
            case 'not_configured':
                return back()->withInput()->with('admin_error', 'FCM 서비스 계정 키가 설정되지 않아 보낼 수 없습니다.');
            case 'duplicate':
                return redirect()->route('admin.push.logs')->with('admin_message', '이미 보낸 요청입니다. 발송 이력을 확인해주세요.');
            case 'no_target':
                return redirect()->route('admin.push.logs')->with('admin_error', '알림을 받을 기기가 없어 보내지 않았습니다.');
        }

        // 큐가 없으므로 화면을 먼저 돌려주고 같은 프로세스에서 이어서 보낸다. (API 발송과 같은 방식)
        $log = $prepared['log'];
        app()->terminating(function () use ($log) {
            app(PushService::class)->deliver($log);
        });

        Log::info('[admin] 회원 지정 푸시 발송', [
            'admin'    => Auth::user()->mq_user_id,
            'log_idx'  => $log->idx,
            'users'    => count($userIds),
            'skipped'  => $prepared['skipped_user_ids'],
        ]);

        $message = '회원 ' . count($reachable) . '명(기기 ' . $prepared['target_count'] . '대)에게 발송을 시작했습니다.';
        if ($prepared['skipped_user_ids']) {
            $message .= ' 받지 못하는 회원 ' . count($prepared['skipped_user_ids']) . '명은 빠졌습니다: ' . implode(', ', $prepared['skipped_user_ids']);
        }

        return redirect()->route('admin.push.logs')->with('admin_message', $message);
    }

    /**
     * 회원과 알림 켠 기기 수.
     */
    protected function memberQuery()
    {
        $devices = DB::table('mq_push_tokens')
            ->selectRaw('member_idx, COUNT(*) AS cnt')
            ->where('mq_status', 1)
            ->groupBy('member_idx');

        return DB::table('mq_member as m')
            ->leftJoinSub($devices, 'd', 'd.member_idx', '=', 'm.idx')
            ->select('m.idx', 'm.mq_user_id', 'm.mq_user_name', 'm.mq_user_email', 'm.mq_status', 'm.mq_marketing_agree', DB::raw('COALESCE(d.cnt, 0) AS devices'));
    }

    /**
     * 화면에 보여줄 형태로 바꾸고, 받을 수 없는 회원은 이유를 붙인다.
     */
    protected function describe($rows)
    {
        $out = [];
        foreach ($rows as $m) {
            $reason = null;
            if ((int) $m->mq_status !== 1) {
                $reason = (int) $m->mq_status === 2 ? '해지 회원' : '비활성 회원';
            } elseif (!(int) $m->mq_marketing_agree) {
                $reason = '마케팅 수신 미동의';
            } elseif ((int) $m->devices === 0) {
                $reason = '알림 켠 기기 없음';
            }

            $out[] = [
                'user_id'   => $m->mq_user_id,
                'name'      => $m->mq_user_name,
                'email'     => $m->mq_user_email,
                'devices'   => (int) $m->devices,
                'reachable' => $reason === null,
                'reason'    => $reason,
            ];
        }

        return $out;
    }
}
