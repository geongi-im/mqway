<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PushLog;
use App\Models\PushRule;
use App\Services\PushRuleService;
use App\Services\PushService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

/**
 * 관리자 페이지 앱 푸시관리. 자동 발송 규칙 목록 / 편집과 발송 이력.
 *
 * 실제 발송은 스케줄러(php artisan push:run-rules)가 한다. 이 화면은 규칙을 저장만 한다.
 */
class PushRuleController extends Controller
{
    const LOGS_PER_PAGE = 30;

    /** @var PushRuleService */
    protected $rules;

    public function __construct(PushRuleService $rules)
    {
        $this->rules = $rules;
    }

    public function index()
    {
        $now = Carbon::now();
        $rules = PushRule::with('lastLog')->orderBy('idx')->get();

        foreach ($rules as $rule) {
            $rule->audience = $this->rules->countAudience($rule->mq_target, $rule->mq_target_days);
            $rule->next_run = $rule->nextRunAt($now);
        }

        return view('admin.push.rules', [
            'rules'       => $rules,
            'targets'     => PushRule::TARGET_LABELS,
            'weekdays'    => PushRule::WEEKDAY_LABELS,
            'sendTimes'   => PushRule::sendTimeOptions(),
            'placeholder' => PushService::NAME_PLACEHOLDER,
        ]);
    }

    public function logs()
    {
        $logs = PushLog::with('rule')->orderByDesc('idx')->paginate(self::LOGS_PER_PAGE);

        return view('admin.push.logs', compact('logs'));
    }

    /**
     * 편집 화면에서 대상 조건을 바꿀 때 지금 기준 대상 수를 센다.
     */
    public function audience(Request $request)
    {
        $data = $request->validate([
            'target' => ['required', Rule::in(array_keys(PushRule::TARGET_LABELS))],
            'days'   => 'nullable|integer|min:1|max:365',
        ]);

        return response()->json([
            'count' => $this->rules->countAudience($data['target'], $data['days'] ?? null),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        PushRule::create($data + [
            'mq_reg_user_id' => Auth::user()->mq_user_id,
            'mq_reg_date'    => Carbon::now(),
        ]);

        return redirect()->route('admin.push.index')->with('admin_message', '규칙 "' . $data['mq_name'] . '" 을 만들었습니다.');
    }

    public function update(Request $request, $idx)
    {
        $rule = PushRule::findOrFail((int) $idx);
        $data = $this->validated($request);

        $rule->fill($data + ['mq_update_date' => Carbon::now()])->save();

        return redirect()->route('admin.push.index')->with('admin_message', '규칙 "' . $rule->mq_name . '" 을 저장했습니다.');
    }

    public function updateStatus(Request $request, $idx)
    {
        $rule = PushRule::findOrFail((int) $idx);
        $data = $request->validate(['mq_status' => 'required|in:0,1']);

        $rule->mq_status = (int) $data['mq_status'];
        $rule->mq_update_date = Carbon::now();
        $rule->save();

        return redirect()->route('admin.push.index')->with(
            'admin_message',
            '규칙 "' . $rule->mq_name . '" 을 ' . ($rule->isOn() ? '켰습니다.' : '껐습니다.')
        );
    }

    public function destroy($idx)
    {
        $rule = PushRule::findOrFail((int) $idx);
        $rule->delete();

        return redirect()->route('admin.push.index')->with('admin_message', '규칙 "' . $rule->mq_name . '" 을 삭제했습니다.');
    }

    /**
     * 규칙 입력값 검사. 화면의 편집 모달이 보내는 이름 그대로 받는다.
     *
     * @return array mq_push_rules 에 바로 넣을 값
     */
    protected function validated(Request $request)
    {
        $data = $request->validate([
            'mq_name'        => 'required|string|max:100',
            'mq_target'      => ['required', Rule::in(array_keys(PushRule::TARGET_LABELS))],
            'mq_target_days' => 'required_unless:mq_target,' . PushRule::TARGET_ALL . '|nullable|integer|min:1|max:365',
            'mq_frequency'   => ['required', Rule::in([PushRule::FREQ_DAILY, PushRule::FREQ_WEEKLY, PushRule::FREQ_MONTHLY])],
            'mq_weekdays'    => 'required_if:mq_frequency,' . PushRule::FREQ_WEEKLY . '|array',
            'mq_weekdays.*'  => 'integer|between:1,7',
            'mq_month_day'   => 'required_if:mq_frequency,' . PushRule::FREQ_MONTHLY . '|nullable|integer|between:1,31',
            'mq_send_time'   => ['required', Rule::in(PushRule::sendTimeOptions())],
            'mq_title'       => 'required|string|max:100',
            'mq_body'        => 'nullable|string|max:500',
            // 사이트 안의 경로만 받는다. (예: /board-scrap/create)
            'mq_link'        => ['nullable', 'string', 'max:500', 'regex:#^/(?!/)#'],
            'mq_status'      => 'nullable|in:0,1',
        ], [
            'mq_link.regex' => '누르면 열 주소는 / 로 시작하는 사이트 안의 경로로 적어주세요. (예: /board-scrap/create)',
        ], [
            'mq_name'        => '규칙 이름',
            'mq_target'      => '대상',
            'mq_target_days' => '기간(일)',
            'mq_frequency'   => '주기',
            'mq_weekdays'    => '요일',
            'mq_month_day'   => '날짜',
            'mq_send_time'   => '발송 시각',
            'mq_title'       => '제목',
            'mq_body'        => '본문',
            'mq_link'        => '누르면 열 주소',
        ]);

        $frequency = $data['mq_frequency'];
        $weekdays = array_values(array_unique(array_map('intval', $data['mq_weekdays'] ?? [])));
        sort($weekdays);

        return [
            'mq_name'        => $data['mq_name'],
            'mq_target'      => $data['mq_target'],
            'mq_target_days' => $data['mq_target'] === PushRule::TARGET_ALL ? null : (int) $data['mq_target_days'],
            'mq_frequency'   => $frequency,
            'mq_weekdays'    => $frequency === PushRule::FREQ_WEEKLY ? implode(',', $weekdays) : null,
            'mq_month_day'   => $frequency === PushRule::FREQ_MONTHLY ? (int) $data['mq_month_day'] : null,
            'mq_send_time'   => $data['mq_send_time'],
            'mq_title'       => $data['mq_title'],
            'mq_body'        => $data['mq_body'] ?? null,
            'mq_link'        => $data['mq_link'] ?? null,
            'mq_status'      => (int) ($data['mq_status'] ?? 0),
        ];
    }
}
