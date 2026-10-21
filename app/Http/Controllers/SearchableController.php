<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

/**
 * คลาสแม่สำหรับ controller ที่ค้นหา + แบ่งหน้า + หา record ด้วย code
 * (แนวเดียวกับ SearchableController ที่เรียนใน Week 08–09)
 */
abstract class SearchableController extends Controller
{
    const int MAX_ITEMS = 5;

    // คลาสลูกต้อง override เช่น return Category::orderBy('code');
    abstract function getQuery(): Builder;

    function prepareCriteria(array $criteria): array
    {
        return [
            'term' => null,
            ...$criteria,
        ];
    }

    /**
     * ฟิลด์ที่ใช้ค้นด้วย search term — คลาสลูกเพิ่มฟิลด์ได้
     */
    function getFilterOptions(): array
    {
        return [
            'term' => [
                'code' => static fn(Builder $query, string $word)
                    => $query->where('code', 'LIKE', "%{$word}%"),
                'name' => static fn(Builder $query, string $word)
                    => $query->where('name', 'LIKE', "%{$word}%"),
            ],
        ];
    }

    /**
     * แต่ละคำใน term เชื่อมด้วย AND ส่วนแต่ละฟิลด์ของคำเดียวกันเชื่อมด้วย OR
     * term = "laravel ws" → (code LIKE %laravel% OR name LIKE %laravel%) AND (code LIKE %ws% OR ...)
     */
    final function filterByTerm(
        Builder|Relation $query,
        string $term,
        array $termOptions,
    ): void {
        foreach (\preg_split('/\s+/', \trim($term), flags: \PREG_SPLIT_NO_EMPTY) as $word) {
            $query->where(
                static function (Builder $wordQuery) use ($word, $termOptions): void {
                    foreach ($termOptions as $fieldFn) {
                        $wordQuery->orWhere(
                            static fn(Builder $fieldQuery) => $fieldFn($fieldQuery, $word),
                        );
                    }
                },
            );
        }
    }

    function filter(
        Builder|Relation $query,
        array $criteria,
        array $filterOptions,
    ): void {
        if ($criteria['term'] !== null) {
            $this->filterByTerm($query, $criteria['term'], $filterOptions['term']);
        }
    }

    final function search(array $criteria, ?array $filterOptions = null): Builder
    {
        $filterOptions ??= $this->getFilterOptions();

        $query = $this->getQuery();
        $this->filter($query, $criteria, $filterOptions);

        return $query;
    }

    // หา record ด้วย code ถ้าไม่เจอ → 404
    function find(string $code): Model
    {
        return $this->getQuery()->where('code', $code)->firstOrFail();
    }
}
