<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Psr\Http\Message\ServerRequestInterface;

class CategoryController extends SearchableController
{
    function getQuery(): Builder
    {
        return Category::orderBy('code');
    }

    function list(ServerRequestInterface $request): View
    {
        Gate::authorize('list', Category::class);

        $criteria = $this->prepareCriteria($request->getQueryParams());
        $query = $this->search($criteria)->withCount('activities');

        return view('categories.list', [
            'criteria' => $criteria,
            'categories' => $query->paginate(self::MAX_ITEMS),
        ]);
    }
}
