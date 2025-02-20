<?php

namespace App\Traits;

use App\Models\Board;

trait BoardCategoryColorTrait
{
    protected $predefinedColors = [
        'bg-yellow-100 text-yellow-800',
        'bg-blue-100 text-blue-800',
        'bg-green-100 text-green-800',
        'bg-red-100 text-red-800',
        'bg-purple-100 text-purple-800',
        'bg-indigo-100 text-indigo-800',
        'bg-pink-100 text-pink-800',
        'bg-orange-100 text-orange-800',
        'bg-teal-100 text-teal-800',
        'bg-gray-100 text-gray-800'
    ];

    protected function getCategoryColors()
    {
        $categories = $this->getCategories();
        $categoryColors = [];
        foreach ($categories as $index => $category) {
            $categoryColors[$category] = $this->predefinedColors[$index % count($this->predefinedColors)];
        }

        return $categoryColors;
    }

    protected function getCategories()
    {
        return Board::select('mq_category')
            ->distinct()
            ->orderBy('mq_category')
            ->pluck('mq_category')
            ->toArray();
    }
} 