<?php

if (!function_exists('formatFileSize')) {
    /**
     * 파일 크기를 읽기 쉬운 형태로 포맷팅
     * 
     * @param int $bytes 바이트 단위 용량
     * @param int $precision 소수점 자리수
     * @return string 포맷팅된 파일 크기
     */
    function formatFileSize($bytes, $precision = 2) {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= (1 << (10 * $pow));
        
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}

if (!function_exists('hasImagesInContent')) {
    /**
     * 본문 내용에 이미지 태그가 포함되어 있는지 확인
     * 
     * @param string $content HTML 콘텐츠
     * @return bool 이미지 태그 포함 여부
     */
    function hasImagesInContent($content) {
        // 정규식으로 <img> 태그가 있는지 확인
        return preg_match('/<img[^>]+>/i', $content) === 1;
    }
}

if (!function_exists('extractFirstImageSrc')) {
    /**
     * 본문에서 첫 번째 이미지의 src 속성 추출
     * 
     * @param string $content HTML 콘텐츠
     * @return string|null 첫 번째 이미지의 src 속성값 또는 null
     */
    function extractFirstImageSrc($content) {
        $pattern = '/<img[^>]+src=[\'"]([^\'"]+)[\'"][^>]*>/i';
        if (preg_match($pattern, $content, $matches)) {
            return $matches[1];
        }
        return null;
    }
} 