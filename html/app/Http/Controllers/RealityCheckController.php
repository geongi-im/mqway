<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RealityCheck;
use App\Models\RealityCheckSample;
use Exception;
use Illuminate\Support\Facades\Auth;

class RealityCheckController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');  // 로그인 필요
    }

    /**
     * 현실 점검 페이지 표시
     */
    public function index()
    {
        // 빈 컬렉션을 전달하여 초기 페이지 로드 시 데이터를 표시하지 않음
        // 실제 데이터는 Ajax를 통해 불러옴
        $expenses = collect([]);
        
        return view('guidebook.reality-check', compact('expenses'));
    }

    /**
     * 샘플 데이터 가져오기
     */
    public function getSamples(Request $request)
    {
        $gender = $request->query('gender');
        $age = $request->query('age');

        // 파라미터가 없는 경우 기본값 설정
        if (empty($gender) || empty($age)) {
            return response()->json([
                'status' => 'error',
                'message' => '성별과 연령대 파라미터가 필요합니다.'
            ], 400);
        }

        // DB에서 해당 성별과 연령대에 맞는 샘플 데이터 조회
        $samples = RealityCheckSample::where('mq_s_gender', $gender)
            ->where('mq_s_age', $age)
            ->get();

        // 클라이언트에 맞게 데이터 포맷팅
        $formattedSamples = $samples->map(function($sample) {
            $content = $sample->mq_s_content;
            
            // 지출 항목 포맷팅
            $expenses = [];
            if (isset($content['expenses']) && is_array($content['expenses'])) {
                foreach ($content['expenses'] as $expense) {
                    if (isset($expense['category']) && isset($expense['price'])) {
                        $expenses[$expense['category']] = $expense['price'];
                    }
                }
            }
            
            // 수입 항목 포맷팅 - 배열 형태로 유지
            $income = [];
            if (isset($content['income']) && is_array($content['income'])) {
                $income = $content['income'];
            }
            
            return [
                'name' => $sample->mq_s_name,
                'description' => $sample->mq_s_description,
                'expenses' => $expenses,
                'income' => $income
            ];
        });

        return response()->json($formattedSamples);
    }

    /**
     * 샘플 데이터 적용하기
     */
    public function applySample(Request $request)
    {
        $gender = $request->input('gender');
        $age = $request->input('age');
        $sampleIndex = $request->input('sampleIndex', 0);

        try {
            // DB에서 해당 성별과 연령대에 맞는 샘플 데이터 조회
            $samples = RealityCheckSample::where('mq_s_gender', $gender)
                ->where('mq_s_age', $age)
                ->get();

            if ($samples->isEmpty() || !isset($samples[$sampleIndex])) {
                return response()->json([
                    'status' => 'error', 
                    'message' => '해당하는 샘플 데이터가 없습니다.'
                ], 404);
            }

            $sample = $samples[$sampleIndex];
            $content = $sample->mq_s_content;
            
            // 트랜잭션 시작
            \DB::beginTransaction();
            
            // 지출 항목 추가
            if (isset($content['expenses']) && is_array($content['expenses'])) {
                foreach ($content['expenses'] as $expense) {
                    if (isset($expense['category']) && isset($expense['price'])) {
                        RealityCheck::create([
                            'mq_user_id' => Auth::user()->mq_user_id,
                            'mq_category' => $expense['category'],
                            'mq_price' => $expense['price'],
                            'mq_type' => 0, // 지출
                            'mq_reg_date' => now(),
                            'mq_update_date' => now()
                        ]);
                    }
                }
            }
            
            // 수입 항목 추가
            if (isset($content['income']) && is_array($content['income'])) {
                foreach ($content['income'] as $income) {
                    if (isset($income['category']) && isset($income['price'])) {
                        RealityCheck::create([
                            'mq_user_id' => Auth::user()->mq_user_id,
                            'mq_category' => $income['category'],
                            'mq_price' => $income['price'],
                            'mq_type' => 1, // 수입
                            'mq_reg_date' => now(),
                            'mq_update_date' => now()
                        ]);
                    }
                }
            }
            
            // 트랜잭션 커밋
            \DB::commit();

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            // 트랜잭션 롤백
            \DB::rollBack();
            
            return response()->json([
                'status' => 'error', 
                'message' => '샘플 적용 중 오류가 발생했습니다: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 지출 항목 생성
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'mq_category' => 'required|string|max:50',
                'mq_price' => 'required|integer|min:0',
                'mq_type' => 'required|integer|in:0,1'
            ]);

            $expense = RealityCheck::create([
                'mq_user_id' => auth()->user()->mq_user_id,
                'mq_type' => $request->input('mq_type'),
                'mq_category' => $validated['mq_category'],
                'mq_price' => $validated['mq_price'],
                'mq_reg_date' => now()
            ]);

            return response()->json([
                'message' => '지출 항목이 추가되었습니다.',
                'status' => 'success'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => '지출 항목 추가 중 오류가 발생했습니다.',
                'error' => $e->getMessage(),
                'status' => 'error'
            ], 500);
        }
    }

    /**
     * 지출 항목 수정
     */
    public function update(Request $request, $idx)
    {
        try {
            $expense = RealityCheck::where('idx', $idx)
                ->where('mq_user_id', auth()->user()->mq_user_id)
                ->firstOrFail();

            $validated = $request->validate([
                'mq_category' => 'required|string|max:50',
                'mq_price' => 'required|integer|min:0',
                'mq_type' => 'required|integer|in:0,1'
            ]);

            $expense->update([
                'mq_type' => $request->input('mq_type'),
                'mq_category' => $validated['mq_category'],
                'mq_price' => $validated['mq_price'],
                'mq_update_date' => now()
            ]);

            return response()->json([
                'message' => '지출 항목이 수정되었습니다.',
                'status' => 'success'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => '지출 항목 수정 중 오류가 발생했습니다.',
                'error' => $e->getMessage(),
                'status' => 'error'
            ], 500);
        }
    }

    /**
     * 지출 항목 삭제
     */
    public function destroy($idx)
    {
        try {
            $expense = RealityCheck::where('idx', $idx)
                ->where('mq_user_id', auth()->user()->mq_user_id)
                ->firstOrFail();

            $expense->delete();

            return response()->json([
                'message' => '지출 항목이 삭제되었습니다.',
                'status' => 'success'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => '지출 항목 삭제 중 오류가 발생했습니다.',
                'error' => $e->getMessage(),
                'status' => 'error'
            ], 500);
        }
    }

    /**
     * Ajax 요청을 통해 지출/수입 데이터 가져오기
     */
    public function getExpenses(Request $request)
    {
        $type = $request->input('type');
        $query = RealityCheck::where('mq_user_id', Auth::user()->mq_user_id);
        
        // type이 'all'이 아닌 경우 mq_type으로 필터링
        if ($type !== 'all') {
            $query->where('mq_type', $type);
        }
        
        $expenses = $query->orderBy('idx', 'desc')->get();
        
        return response()->json([
            'status' => 'success',
            'data' => $expenses
        ]);
    }
} 