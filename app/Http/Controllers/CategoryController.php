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
                ->with('status', "หมวดหมู่ {$category->code} ถูกสร้างแล้ว");
        } catch (QueryException $excp) {
            return redirect()->back()->withInput()->withErrors([
                'alert' => $excp->errorInfo[2],
            ]);
        }
    }

    function showUpdateForm(string $categoryCode): View
    {
        $category = $this->find($categoryCode);
        Gate::authorize('update', $category);

        return view('categories.update-form', [
            'category' => $category,
        ]);
    }

    function update(ServerRequestInterface $request, string $categoryCode): RedirectResponse
    {
        $category = $this->find($categoryCode);
        Gate::authorize('update', $category);

        try {
            $category->fill($request->getParsedBody());
            $category->save();

            if (session()->has('bookmarks.categories.update')) {
                session()->put(
                    'bookmarks.categories.view',
                    session()->get('bookmarks.categories.update'),
                );
            }

            session()->forget('bookmarks.categories.update');

            return redirect()
                ->route('categories.view', ['category' => $category->code])
                ->with('status', "หมวดหมู่ {$category->code} ถูกแก้ไขแล้ว");
        } catch (QueryException $excp) {
            return redirect()->back()->withInput()->withErrors([
                'alert' => $excp->errorInfo[2],
            ]);
        }
    }

    function delete(string $categoryCode): RedirectResponse
    {
        $category = $this->find($categoryCode);
        Gate::authorize('delete', $category);

        try {
            $category->delete();

            return redirect(session()->get('bookmarks.categories.delete') ?? route('categories.index'))
                ->with('status', "หมวดหมู่ {$category->code} ถูกลบแล้ว");
        } catch (QueryException $excp) {
            return redirect()->back()->withErrors([
                'alert' => $excp->errorInfo[2],
            ]);
        }
    }

    function viewActivities(string $categoryCode, ServerRequestInterface $request): View
    {
        $category = $this->find($categoryCode);
        Gate::authorize('view', $category);

        $activityController = \resolve(ActivityController::class);
        $criteria = $activityController->prepareCriteria($request->getQueryParams());

        // ทุกกิจกรรมในหน้านี้อยู่หมวดเดียวกัน จึงไม่ค้นด้วยชื่อหมวด (แบบ view-products ในคาบ)
        $filterOptions = $activityController->getFilterOptions();
        unset($filterOptions['term']['category']);

        $query = $category->activities()->orderBy('code');
        $activityController->filter($query, $criteria, $filterOptions);

        return view('categories.view-activities', [
            'criteria' => $criteria,
            'category' => $category,
            'activities' => $query->paginate($activityController::MAX_ITEMS),
        ]);
    }
}
