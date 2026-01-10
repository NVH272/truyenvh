<?php

namespace App\Services;

use App\Models\BannedWord;

class ProfanityFilter
{
    public static function findHits(string $content): array
    {
        $content = self::normalize($content);

        $words = BannedWord::query()
            ->where('is_active', 1)
            ->pluck('word')
            ->all();

        $hit = [];
        foreach ($words as $w) {
            $wNorm = self::normalize($w);
            if ($wNorm === '') continue;

            // match theo "từ" (đỡ match nhầm)
            if (preg_match('/(^|[^a-z0-9])' . preg_quote($wNorm, '/') . '([^a-z0-9]|$)/u', $content)) {
                $hit[] = $w;
            }
        }
        return array_values(array_unique($hit));
    }

    private static function normalize(string $text): string
    {
        $text = mb_strtolower(trim($text));
        $text = preg_replace('/\s+/u', ' ', $text);
        return $text;
    }
}
