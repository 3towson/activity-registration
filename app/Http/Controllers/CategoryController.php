<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
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

    function view(string $categoryCode): View
    {
        $category = $this->find($categoryCode);
        Gate::authorize('view', $category);

        return view('categories.view', [
            'category' => $category,
        ]);
    }

    function showCreateForm(): View
    {
        Gate::authorize('create', Category::class);

        return view('categories.create-form');
    }

    function create(ServerRequestInterface $request): RedirectResponse
    {
        Gate::authorize('create', Category::class);

        try {
            $category = Category::create($request->getParsedBody());

            if (session()->has('bookmarks.categories.create')) {
                session()->put(
                    'bookmarks.categories.view',
                    session()->get('bookmarks.categories.create'),
                );
            }

            session()->forget('bookmarks.categories.create');
            return redirect()
                ->route('categories.view', ['category' => $category->code])
                ->with('status', "Category {$category->code} was created.");
        } catch (QueryException $excp) {
            return redirect()->back()->withInput()->withErrors([
                'alert' => $excp->errorInfo[2],
            ]);
        }
    }
}
