<?php

namespace App\Http\Controllers;

use App\Models\Organizer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Psr\Http\Message\ServerRequestInterface;

class OrganizerController extends SearchableController
{
    function getQuery(): Builder
    {
        return Organizer::orderBy('code');
    }

    function list(ServerRequestInterface $request): View
    {
        Gate::authorize('list', Organizer::class);

        $criteria = $this->prepareCriteria($request->getQueryParams());
        $query = $this->search($criteria)->withCount('activities');

        return view('organizers.list', [
            'criteria' => $criteria,
            'organizers' => $query->paginate(self::MAX_ITEMS),
        ]);
    }

    function view(string $organizerCode): View
    {
        $organizer = $this->find($organizerCode);
        Gate::authorize('view', $organizer);

        return view('organizers.view', [
            'organizer' => $organizer,
        ]);
    }

    function showCreateForm(): View
    {
        Gate::authorize('create', Organizer::class);

        return view('organizers.create-form');
    }

    function create(ServerRequestInterface $request): RedirectResponse
    {
        Gate::authorize('create', Organizer::class);

        try {
            $organizer = Organizer::create($request->getParsedBody());

            if (session()->has('bookmarks.organizers.create')) {
                session()->put(
                    'bookmarks.organizers.view',
                    session()->get('bookmarks.organizers.create'),
                );
            }

            session()->forget('bookmarks.organizers.create');

            return redirect()
                ->route('organizers.view', ['organizer' => $organizer->code])
                ->with('status', "ผู้จัด {$organizer->code} ถูกสร้างแล้ว");
        } catch (QueryException $excp) {
            return redirect()->back()->withInput()->withErrors([
                'alert' => $excp->errorInfo[2],
            ]);
        }
    }

    function showUpdateForm(string $organizerCode): View
    {
        $organizer = $this->find($organizerCode);
        Gate::authorize('update', $organizer);

        return view('organizers.update-form', [
            'organizer' => $organizer,
        ]);
    }

    function update(ServerRequestInterface $request, string $organizerCode): RedirectResponse
    {
        $organizer = $this->find($organizerCode);
        Gate::authorize('update', $organizer);

        try {
            $organizer->fill($request->getParsedBody());
            $organizer->save();

            if (session()->has('bookmarks.organizers.update')) {
                session()->put(
                    'bookmarks.organizers.view',
                    session()->get('bookmarks.organizers.update'),
                );
            }

            session()->forget('bookmarks.organizers.update');

            return redirect()
                ->route('organizers.view', ['organizer' => $organizer->code])
                ->with('status', "ผู้จัด {$organizer->code} ถูกแก้ไขแล้ว");
        } catch (QueryException $excp) {
            return redirect()->back()->withInput()->withErrors([
                'alert' => $excp->errorInfo[2],
            ]);
        }
    }

    function delete(string $organizerCode): RedirectResponse
    {
        $organizer = $this->find($organizerCode);
        Gate::authorize('delete', $organizer);

        try {
            $organizer->delete();

            return redirect(session()->get('bookmarks.organizers.delete') ?? route('organizers.index'))
                ->with('status', "ผู้จัด {$organizer->code} ถูกลบแล้ว");
        } catch (QueryException $excp) {
            return redirect()->back()->withErrors([
                'alert' => $excp->errorInfo[2],
            ]);
        }
    }

    function viewActivities(string $organizerCode, ServerRequestInterface $request): View
    {
        $organizer = $this->find($organizerCode);
        Gate::authorize('view', $organizer);

        $activityController = \resolve(ActivityController::class);
        $criteria = $activityController->prepareCriteria($request->getQueryParams());

        // ทุกกิจกรรมในหน้านี้มีผู้จัดคนเดียวกัน จึงไม่ค้นด้วยชื่อผู้จัด
        $filterOptions = $activityController->getFilterOptions();
        unset($filterOptions['term']['organizer']);

        $query = $organizer->activities()->with('category')->orderBy('code');
        $activityController->filter($query, $criteria, $filterOptions);

        return view('organizers.view-activities', [
            'criteria' => $criteria,
            'organizer' => $organizer,
            'activities' => $query->paginate($activityController::MAX_ITEMS),
        ]);
    }
}
