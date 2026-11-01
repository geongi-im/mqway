<?php

namespace App\Http\Controllers;

use App\Models\BoardInsights;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Traits\BoardCategoryColorTrait;
use ZipArchive;

class BoardInsightsController extends AbstractBoardController
{
    use BoardCategoryColorTrait;

    protected $modelClass = BoardInsights::class;
    protected $viewPath = 'board_insights';
    protected $routePrefix = 'board-insights';
    protected $uploadPath = 'uploads/board_insights';

    // 첨부파일 제한 (게시글당 개수, 파일당 KB)
    const ATTACH_MAX_COUNT = 5;
    const ATTACH_MAX_KB = 10240;

    /**
     * 허용 확장자 -> 파일 내용 검사 방식
     * - pdf   : %PDF- 시그니처
     * - ole   : MS 복합 문서(OLE2) 시그니처 (구형 오피스, 한글 hwp)
     * - 나머지 : ZIP 기반 문서로, 확장자에 맞는 내부 구조가 있는지 확인
     */
    protected $attachTypes = [
        'pdf' => 'pdf',
        'ppt' => 'ole',
        'doc' => 'ole',
        'xls' => 'ole',
        'hwp' => 'ole',
        'pptx' => 'ppt/presentation.xml',
        'docx' => 'word/document.xml',
        'xlsx' => 'xl/workbook.xml',
        'hwpx' => 'hwpx',
    ];

    public function __construct()
    {
        $this->middleware('auth');
    }

    protected function getItemsPerPage()
    {
        return 15;
    }

    protected function useHardDelete()
    {
        return true;
    }

    public function uploadImage(Request $request)
    {
        if ($request->hasFile('upload')) {
            $file = $request->file('upload');

            $request->validate([
                'upload' => 'required|image|max:5120'
            ]);

            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();

            $randomName = Str::random(32) . '.' . $extension;

            $path = $file->storeAs($this->uploadPath.'/editor', $randomName, 'public');

            return response()->json([
                'url' => asset('storage/' . $path)
            ]);
        }

        return response()->json([
            'error' => [
                'message' => '이미지 업로드에 실패했습니다.'
            ]
        ], 400);
    }

    public function create()
    {
        $categories = $this->getBoardInsightsCategories();

        return view($this->viewPath.'.create', [
            'categories' => $categories
        ]);
    }

    /**
     * 첨부파일 다운로드
     * 파일은 웹 루트 밖에 있으므로 이 라우트로만 받을 수 있다.
     * 브라우저가 내용을 해석하지 않도록 항상 octet-stream + attachment 로 내려준다.
     */
    public function downloadFile($idx, $filename)
    {
        $post = BoardInsights::where('mq_status', 1)->findOrFail($idx);

        $files = is_array($post->mq_attach_file) ? $post->mq_attach_file : [];
        $index = array_search($filename, $files, true);
        if ($index === false) {
            abort(404);
        }

        $path = BoardInsights::ATTACH_PATH . '/' . $filename;
        if (!Storage::disk('local')->exists($path)) {
            abort(404);
        }

        $downloadName = $post->mq_attach_original[$index] ?? $filename;

        return response()->download(Storage::disk('local')->path($path), $downloadName, [
            'Content-Type' => 'application/octet-stream',
            'X-Content-Type-Options' => 'nosniff',
            'Content-Security-Policy' => "default-src 'none'; sandbox",
            'Cache-Control' => 'private, no-store',
        ]);
    }

    /**
     * 첨부파일 삭제 API (작성자만)
     */
    public function deleteFile($idx, $filename)
    {
        try {
            $board = BoardInsights::findOrFail($idx);

            if (!$this->canEdit($board)) {
                return response()->json([
                    'success' => false,
                    'message' => '삭제 권한이 없습니다.'
                ], 403);
            }

            $files = is_array($board->mq_attach_file) ? $board->mq_attach_file : [];
            $index = array_search($filename, $files, true);
            if ($index === false) {
                return response()->json([
                    'success' => false,
                    'message' => '첨부파일을 찾을 수 없습니다.'
                ], 404);
            }

            Storage::disk('local')->delete(BoardInsights::ATTACH_PATH . '/' . $filename);

            $originals = is_array($board->mq_attach_original) ? $board->mq_attach_original : [];
            unset($files[$index], $originals[$index]);

            $board->mq_attach_file = array_values($files);
            $board->mq_attach_original = array_values($originals);
            $board->save();

            return response()->json([
                'success' => true,
                'message' => '첨부파일이 삭제되었습니다.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '첨부파일 삭제 중 오류가 발생했습니다.'
            ], 500);
        }
    }

    /**
     * 기본 검사 + 첨부파일 검사
     */
    protected function validatePostRequest(Request $request, array $validCategories)
    {
        parent::validatePostRequest($request, $validCategories);

        // 수정 시에는 이미 올라간 첨부파일 개수까지 합쳐서 제한
        $existingCount = 0;
        if ($request->route('idx')) {
            $post = BoardInsights::find($request->route('idx'));
            if ($post && is_array($post->mq_attach_file)) {
                $existingCount = count($post->mq_attach_file);
            }
        }
        $remainCount = max(0, self::ATTACH_MAX_COUNT - $existingCount);

        $request->validate([
            'mq_attach_file' => 'nullable|array|max:' . $remainCount,
            'mq_attach_file.*' => [
                'bail',
                'file',
                'max:' . self::ATTACH_MAX_KB,
                function ($attribute, $file, $fail) {
                    $error = $this->inspectAttachment($file);
                    if ($error !== null) {
                        $fail($error);
                    }
                },
            ],
        ], [
            'mq_attach_file.array' => '첨부파일 형식이 올바르지 않습니다.',
            'mq_attach_file.max' => '첨부파일은 게시글당 최대 ' . self::ATTACH_MAX_COUNT . '개까지 올릴 수 있습니다.',
            'mq_attach_file.*.file' => '첨부파일 업로드에 실패했습니다. 파일 크기를 확인해주세요.',
            'mq_attach_file.*.max' => '첨부파일은 파일당 ' . (self::ATTACH_MAX_KB / 1024) . 'MB 이하만 올릴 수 있습니다.',
        ]);
    }

    /**
     * 첨부파일 내용 검사
     * 확장자는 화이트리스트로 제한하고, 파일 앞부분/내부 구조가 확장자와 맞는지 확인한다.
     * 문제가 없으면 null, 있으면 오류 문구를 돌려준다.
     */
    protected function inspectAttachment($file)
    {
        if (!$file instanceof UploadedFile || !$file->isValid()) {
            return '첨부파일 업로드에 실패했습니다.';
        }

        $extension = strtolower($file->getClientOriginalExtension());
        if (!isset($this->attachTypes[$extension])) {
            return '허용되지 않는 파일 형식입니다. (' . strtoupper(implode(', ', array_keys($this->attachTypes))) . ')';
        }

        $path = $file->getRealPath();
        $head = (string) @file_get_contents($path, false, null, 0, 1024);
        $type = $this->attachTypes[$extension];
        $invalidMessage = '파일 내용이 확장자(' . $extension . ')와 맞지 않습니다.';

        if ($type === 'pdf') {
            return strpos($head, '%PDF-') !== false ? null : $invalidMessage;
        }

        if ($type === 'ole') {
            return strncmp($head, "\xD0\xCF\x11\xE0\xA1\xB1\x1A\xE1", 8) === 0 ? null : $invalidMessage;
        }

        // ZIP 기반 문서 (pptx/docx/xlsx/hwpx)
        if (strncmp($head, "PK\x03\x04", 4) !== 0) {
            return $invalidMessage;
        }

        $zip = new ZipArchive();
        if ($zip->open($path) !== true) {
            return $invalidMessage;
        }

        try {
            if ($type === 'hwpx') {
                $mimetype = $zip->getFromName('mimetype', 64);
                return ($mimetype !== false && strpos($mimetype, 'hwp') !== false) ? null : $invalidMessage;
            }

            if ($zip->locateName('[Content_Types].xml') === false || $zip->locateName($type) === false) {
                return $invalidMessage;
            }

            // 매크로(VBA) 프로젝트가 들어 있으면 거부 (확장자만 바꾼 docm/pptm/xlsm 등)
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $entryName = strtolower((string) $zip->getNameIndex($i));
                if (Str::endsWith($entryName, 'vbaproject.bin')) {
                    return '매크로가 포함된 문서는 올릴 수 없습니다.';
                }
            }

            return null;
        } finally {
            $zip->close();
        }
    }

    /**
     * 첨부파일 저장 (store/update 공통)
     * 저장명은 난수 + 검사를 통과한 소문자 확장자로 만들고 웹 루트 밖(local 디스크)에 둔다.
     */
    protected function saveExtraFiles(Request $request, $board)
    {
        if (!$request->hasFile('mq_attach_file')) {
            return;
        }

        $files = is_array($board->mq_attach_file) ? $board->mq_attach_file : [];
        $originals = is_array($board->mq_attach_original) ? $board->mq_attach_original : [];

        foreach ($request->file('mq_attach_file') as $file) {
            $extension = strtolower($file->getClientOriginalExtension());
            $storedName = Str::random(32) . '.' . $extension;
            $file->storeAs(BoardInsights::ATTACH_PATH, $storedName, 'local');

            $files[] = $storedName;
            $originals[] = $this->sanitizeOriginalName($file->getClientOriginalName(), $extension);
        }

        $board->mq_attach_file = $files;
        $board->mq_attach_original = $originals;
    }

    /**
     * 게시글 삭제 시 첨부파일도 삭제
     */
    protected function deleteExtraFiles($board)
    {
        if (is_array($board->mq_attach_file)) {
            foreach ($board->mq_attach_file as $filename) {
                Storage::disk('local')->delete(BoardInsights::ATTACH_PATH . '/' . $filename);
            }
        }
    }

    /**
     * 원본 파일명 정리
     * 경로 구분자/제어문자/따옴표를 없애고 길이를 제한한다. (다운로드 헤더에 그대로 쓰이므로)
     */
    protected function sanitizeOriginalName($name, $extension)
    {
        // basename() 은 로케일에 따라 한글 앞부분을 잘라먹으므로 직접 자른다
        $name = str_replace('\\', '/', (string) $name);
        $name = substr($name, strrpos($name, '/') === false ? 0 : strrpos($name, '/') + 1);
        $name = preg_replace('/[\x00-\x1F\x7F"\/\\\\]/u', '', $name);

        if ($name === null || trim($name) === '') {
            return 'attachment.' . $extension;
        }

        if (mb_strlen($name) > 150) {
            $name = mb_substr($name, 0, 140) . '.' . $extension;
        }

        return $name;
    }
}
