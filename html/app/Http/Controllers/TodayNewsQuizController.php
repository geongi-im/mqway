<?php

namespace App\Http\Controllers;

use App\Models\NewsQuiz;
use App\Models\NewsQuizHistory;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TodayNewsQuizController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();
        $quiz = NewsQuiz::active()
            ->inRandomOrder()
            ->first();

        $todayQuiz = $quiz ? $this->formatQuizForView($quiz) : null;
        $selectedNewsDate = $todayQuiz ? $todayQuiz['mq_news_date'] : $today;
        $rankingItems = $todayQuiz ? $this->getRankingItems($selectedNewsDate) : [];

        $streakInfo = Auth::check()
            ? [
                'days' => $this->calculateCurrentStreak(Auth::user()->mq_user_id),
                'played_today' => $this->hasPlayedDate(Auth::user()->mq_user_id, $today),
            ]
            : null;

        return view('tools.today-news-quiz', compact('today', 'todayQuiz', 'rankingItems', 'streakInfo'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'quiz_idx' => 'required|integer',
            'selected_answer' => 'required|in:option_a,option_b',
        ]);

        $quiz = NewsQuiz::active()->find($validatedData['quiz_idx']);
        if (!$quiz) {
            return response()->json(['error' => '오늘의 뉴스 퀴즈를 찾을 수 없습니다.'], 404);
        }

        $quizContent = $this->normalizeQuizContent($quiz->mq_quiz_content);
        $isCorrect = $validatedData['selected_answer'] === $quizContent['answer'];

        if (!Auth::check()) {
            return response()->json([
                'success' => true,
                'recorded' => false,
                'is_correct' => $isCorrect,
                'explanation' => $quizContent['explanation'],
            ]);
        }

        $userId = Auth::user()->mq_user_id;
        $newsDate = $this->formatDate($quiz->mq_news_date);
        $playedDate = now()->toDateString();

        try {
            $streakDays = $this->calculateStreakForDate($userId, $playedDate);

            $history = NewsQuizHistory::firstOrNew([
                'mq_user_id' => $userId,
                'mq_news_quiz_idx' => $quiz->idx,
            ]);

            if (!$history->exists) {
                $history->mq_reg_date = now();
            }

            $history->fill([
                'mq_news_date' => $newsDate,
                'mq_selected_answer' => $validatedData['selected_answer'],
                'mq_is_correct' => $isCorrect ? 1 : 0,
                'mq_streak_days' => $streakDays,
                'mq_status' => 1,
                'mq_update_date' => now(),
            ]);
            $history->save();

            return response()->json([
                'success' => true,
                'recorded' => true,
                'is_correct' => $isCorrect,
                'explanation' => $quizContent['explanation'],
                'streak_days' => $streakDays,
            ]);
        } catch (Exception $e) {
            Log::error('뉴스 퀴즈 기록 저장 실패: ' . $e->getMessage() . ' --- Trace: ' . $e->getTraceAsString());

            return response()->json([
                'error' => '뉴스 퀴즈 기록 저장에 실패했습니다.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    private function formatQuizForView(NewsQuiz $quiz)
    {
        return [
            'idx' => $quiz->idx,
            'mq_news_date' => $this->formatDate($quiz->mq_news_date),
            'mq_company' => $quiz->mq_company,
            'mq_title' => $quiz->mq_title,
            'mq_source_url' => $quiz->mq_source_url,
            'mq_preview_image_url' => $this->fetchPreviewImageUrl($quiz->mq_source_url),
            'mq_keyword' => $quiz->mq_keyword,
            'mq_keyword_description' => $quiz->mq_keyword_description,
            'mq_quiz_content' => $this->normalizeQuizContent($quiz->mq_quiz_content),
        ];
    }

    private function normalizeQuizContent($content)
    {
        if (is_string($content)) {
            $content = json_decode($content, true) ?: [];
        }

        $answer = strtoupper($content['answer'] ?? '');
        if ($answer === 'A') {
            $answer = 'option_a';
        } elseif ($answer === 'B') {
            $answer = 'option_b';
        }

        return [
            'question' => $content['question'] ?? '',
            'option_a' => $content['option_a'] ?? '',
            'option_b' => $content['option_b'] ?? '',
            'answer' => $answer,
            'explanation' => $content['explanation'] ?? '',
        ];
    }

    private function getRankingItems($date)
    {
        return NewsQuizHistory::with(['user' => function ($query) {
                $query->select('mq_user_id', 'mq_user_name');
            }])
            ->where('mq_status', 1)
            ->whereDate('mq_news_date', $date)
            ->orderBy('mq_is_correct', 'desc')
            ->orderBy('mq_streak_days', 'desc')
            ->orderBy('mq_reg_date', 'asc')
            ->take(5)
            ->get()
            ->values()
            ->map(function ($item, $index) {
                return [
                    'rank' => $index + 1,
                    'name' => $this->maskUserName($item->user ? $item->user->mq_user_name : null),
                    'score' => (int) $item->mq_is_correct,
                    'streak' => (int) $item->mq_streak_days,
                ];
            })
            ->all();
    }

    private function calculateCurrentStreak($userId)
    {
        $today = Carbon::parse(now()->toDateString());
        $dates = $this->getUserPlayedDateSet($userId);

        $startDate = isset($dates[$today->toDateString()])
            ? $today
            : $today->copy()->subDay();

        if (!isset($dates[$startDate->toDateString()])) {
            return 0;
        }

        return $this->countStreakFromDate($dates, $startDate);
    }

    private function calculateStreakForDate($userId, $date)
    {
        $dates = $this->getUserPlayedDateSet($userId);
        $targetDate = Carbon::parse($date);
        $dates[$targetDate->toDateString()] = true;

        return max(1, $this->countStreakFromDate($dates, $targetDate));
    }

    private function countStreakFromDate(array $dates, Carbon $startDate)
    {
        $streak = 0;
        $cursor = $startDate->copy();

        while (isset($dates[$cursor->toDateString()])) {
            $streak++;
            $cursor->subDay();
        }

        return $streak;
    }

    private function getUserPlayedDateSet($userId)
    {
        return NewsQuizHistory::where('mq_user_id', $userId)
            ->where('mq_status', 1)
            ->selectRaw('DATE(COALESCE(mq_update_date, mq_reg_date)) as played_date')
            ->pluck('played_date')
            ->map(function ($date) {
                return $this->formatDate($date);
            })
            ->unique()
            ->flip()
            ->map(function () {
                return true;
            })
            ->all();
    }

    private function hasPlayedDate($userId, $date)
    {
        return NewsQuizHistory::where('mq_user_id', $userId)
            ->where('mq_status', 1)
            ->whereRaw('DATE(COALESCE(mq_update_date, mq_reg_date)) = ?', [$date])
            ->exists();
    }

    private function fetchPreviewImageUrl($url)
    {
        if (!$url || $url === '#') {
            return null;
        }

        $html = $this->fetchHtml($url);
        if (!$html) {
            return null;
        }

        if (!preg_match_all('/<meta\s+[^>]*>/i', $html, $matches)) {
            return null;
        }

        foreach ($matches[0] as $tag) {
            if (
                stripos($tag, 'og:image') === false
                && stripos($tag, 'twitter:image') === false
            ) {
                continue;
            }

            if (preg_match('/content=["\']([^"\']+)["\']/i', $tag, $contentMatch)) {
                return html_entity_decode($contentMatch[1], ENT_QUOTES, 'UTF-8');
            }
        }

        return null;
    }

    private function fetchHtml($url)
    {
        if (function_exists('curl_init')) {
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_CONNECTTIMEOUT => 2,
                CURLOPT_TIMEOUT => 3,
                CURLOPT_USERAGENT => 'Mozilla/5.0 (compatible; MQWAY News Quiz Preview)',
            ]);

            $html = curl_exec($ch);
            $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            return $statusCode >= 200 && $statusCode < 400 ? $html : null;
        }

        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'timeout' => 3,
                'header' => "User-Agent: Mozilla/5.0 (compatible; MQWAY News Quiz Preview)\r\n",
            ],
        ]);

        return @file_get_contents($url, false, $context) ?: null;
    }

    private function maskUserName($name)
    {
        if (!$name) {
            return '익명';
        }

        $length = mb_strlen($name);
        if ($length <= 1) {
            return $name;
        }

        return mb_substr($name, 0, 1) . str_repeat('*', max(1, $length - 1));
    }

    private function formatDate($date)
    {
        if ($date instanceof Carbon) {
            return $date->toDateString();
        }

        return Carbon::parse($date)->toDateString();
    }
}
