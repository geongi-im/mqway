<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseProgress extends Model
{
    protected $table = 'mq_course_progress';
    protected $primaryKey = 'idx';

    // 타임스탬프 사용 (updated_at만)
    public $timestamps = false;

    protected $fillable = [
        'member_idx',
        'course_code',
        'step_number',
        'is_completed',
        'mq_complete_date',
        'mq_update_date',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'mq_complete_date' => 'datetime',
        'mq_update_date' => 'datetime',
        'mq_reg_date' => 'datetime',
    ];

    /**
     * 사용자와의 관계
     */
    public function member()
    {
        return $this->belongsTo(Member::class, 'member_idx', 'idx');
    }

    /**
     * 특정 사용자의 특정 코스 진행 상태 조회
     */
    public static function getUserCourseProgress($memberIdx, $courseCode)
    {
        return self::where('member_idx', $memberIdx)
            ->where('course_code', $courseCode)
            ->get()
            ->keyBy('step_number');
    }

    /**
     * 진행 상태 토글
     */
    public static function toggleStep($memberIdx, $courseCode, $stepNumber)
    {
        $progress = self::firstOrNew([
            'member_idx' => $memberIdx,
            'course_code' => $courseCode,
            'step_number' => $stepNumber,
        ]);

        $progress->is_completed = !$progress->is_completed;
        $progress->mq_complete_date = $progress->is_completed ? now() : null;
        $progress->mq_update_date = now();
        $progress->save();

        return $progress;
    }

    /**
     * 특정 코스의 완료율 계산
     */
    public static function getCompletionRate($memberIdx, $courseCode, $totalSteps = 8)
    {
        $completedCount = self::where('member_idx', $memberIdx)
            ->where('course_code', $courseCode)
            ->where('is_completed', 1)
            ->count();

        return round(($completedCount / $totalSteps) * 100);
    }
}
