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