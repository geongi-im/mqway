<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdminBoardRegistry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * 관리자 페이지 게시판관리. 여러 게시판의 글을 한 목록으로 보고 숨기거나 되살린다.
 *
 * 숨김은 각 게시판이 원래 쓰는 소프트 삭제(mq_status = 0)와 같다. 사이트 목록과 글 보기에서 빠지고,
 * 복구하면 그대로 돌아온다. DB 에서 지우는 기능은 되돌릴 수 없어 두지 않았다.
 */
class BoardController extends Controller
{
    const PER_PAGE = 30;

    public function index(Request $request)
    {
        $board = (string) $request->query('board', '');
        $status = (string) $request->query('status', '');
        $q = trim((string) $request->query('q', ''));

        $keys = AdminBoardRegistry::exists($board) ? [$board] : [];

        $query = DB::query()->fromSub(AdminBoardRegistry::unionQuery($keys), 'p');

        if ($status === '1' || $status === '0') {
            $query->where('mq_status', (int) $status);
        }
        if ($q !== '') {
            $like = '%' . addcslashes($q, '%_\\') . '%';
            $query->where(function ($w) use ($like) {
                $w->where('mq_title', 'like', $like)->orWhere('mq_user_id', 'like', $like);
            });
        }

        $posts = $query->orderByDesc('mq_reg_date')->orderByDesc('idx')
            ->paginate(self::PER_PAGE)
            ->appends($request->query());

        return view('admin.boards.index', [
            'posts'   => $posts,
            'boards'  => AdminBoardRegistry::BOARDS,
            'filters' => ['board' => $board, 'status' => $status, 'q' => $q],
        ]);
    }

    public function updateStatus(Request $request, $board, $idx)
    {
        abort_unless(AdminBoardRegistry::exists($board), 404);

        $data = $request->validate(['mq_status' => 'required|in:0,1']);

        $updated = DB::table(AdminBoardRegistry::table($board))
            ->where('idx', (int) $idx)
            ->update(['mq_status' => (int) $data['mq_status']]);

        abort_if(!$updated && !DB::table(AdminBoardRegistry::table($board))->where('idx', (int) $idx)->exists(), 404);

        Log::info('관리자 게시글 상태 변경', [
            'admin'  => Auth::user()->mq_user_id,
            'board'  => $board,
            'idx'    => (int) $idx,
            'status' => (int) $data['mq_status'],
        ]);

        $label = (int) $data['mq_status'] === 1 ? '복구' : '숨김';

        return back()->with('admin_message', AdminBoardRegistry::name($board) . ' #' . (int) $idx . ' 글을 ' . $label . ' 처리했습니다.');
    }
}
