<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Category;
use App\Models\Organizer;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Psr\Http\Message\ServerRequestInterface;

class ActivityController extends SearchableController
{
    public function getQuery(): Builder
    {
        return Activity::with(['category', 'organizer'])
            ->withCount('participants')
            ->orderBy('code');
    }

    public function prepareCriteria(array $criteria): array
    {
        $criteria = parent::prepareCriteria($criteria);

        return [
            'term' => is_string($criteria['term']) ? $criteria['term'] : null,
            'category' => is_string($criteria['category'] ?? null)
                ? $criteria['category'] : '',
            'upcoming' => ($criteria['upcoming'] ?? '') === '1',
        ];
    }

    public function getFilterOptions(): array
    {
        $options = parent::getFilterOptions();
        $options['term']['location'] = static fn (Builder $query, string $word)
            => $query->where('location', 'LIKE', "%{$word}%");
        $options['term']['category'] = static fn (Builder $query, string $word)
            => $query->whereHas('category', static fn (Builder $related)
                => $related->where('name', 'LIKE', "%{$word}%"));
        $options['term']['organizer'] = static fn (Builder $query, string $word)
            => $query->whereHas('organizer', static fn (Builder $related)
                => $related->where('name', 'LIKE', "%{$word}%"));

        return $options;
    }

    public function filter(
        Builder|Relation $query,
        array $criteria,
        array $filterOptions,
    ): void {
        parent::filter($query, $criteria, $filterOptions);

        if (($criteria['category'] ?? '') !== '') {
            $query->whereHas('category', static fn (Builder $category)
                => $category->where('code', $criteria['category']));
        }

        if ($criteria['upcoming'] ?? false) {
            $query->whereDate('date', '>=', today());
        }
    }

    public function list(ServerRequestInterface $request): View
    {
        Gate::authorize('list', Activity::class);
        $criteria = $this->prepareCriteria($request->getQueryParams());
        session()->put('bookmarks.activities.view', url()->full());

        return view('activities.list', [
            'activities' => $this->search($criteria)->paginate(self::MAX_ITEMS),
            'criteria' => $criteria,
            'categories' => Category::orderBy('code')->get(),
        ]);
    }

    public function view(string $activity): View
    {
        $item = $this->find($activity);
        Gate::authorize('view', $item);

        return view('activities.view', [
            'activity' => $item,
            'registered' => Auth::check()
                && $item->participants()->whereKey(Auth::id())->exists(),
        ]);
    }

    public function showCreateForm(): View
    {
        Gate::authorize('create', Activity::class);

        return view('activities.create-form', [
            'categories' => Category::orderBy('code')->get(),
            'organizers' => Organizer::orderBy('code')->get(),
        ]);
    }

    public function showUpdateForm(string $activity): View
    {
        $item = $this->find($activity);
        Gate::authorize('update', $item);

        return view('activities.update-form', [
            'activity' => $item,
            'categories' => Category::orderBy('code')->get(),
            'organizers' => Organizer::orderBy('code')->get(),
        ]);
    }

    private function readActivityData(ServerRequestInterface $request): array
    {
        $body = (array) $request->getParsedBody();
        $data = [];

        foreach (['code', 'name', 'category', 'organizer', 'date', 'location',
            'capacity', 'description'] as $field) {
            if (!is_string($body[$field] ?? null) || trim($body[$field]) === '') {
                throw new \RuntimeException('กรุณากรอกข้อมูลให้ครบ');
            }
            $data[$field] = trim($body[$field]);
        }

        if (strlen($data['code']) > 64
            || preg_match('/\A[A-Za-z0-9_-]+\z/', $data['code']) !== 1) {
            throw new \RuntimeException('รหัสต้องเป็น A-Z, a-z, 0-9, _ หรือ - และไม่เกิน 64 ตัว');
        }
        if (in_array(strtolower($data['code']), ['list', 'create'], true)) {
            throw new \RuntimeException('รหัสนี้สงวนไว้สำหรับ URL ของระบบ');
        }
        if (mb_strlen($data['name']) > 255 || mb_strlen($data['location']) > 255) {
            throw new \RuntimeException('ชื่อและสถานที่ต้องไม่เกิน 255 ตัวอักษร');
        }
        $capacity = filter_var($data['capacity'], FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 1, 'max_range' => 4294967295]]);
        if ($capacity === false) {
            throw new \RuntimeException('จำนวนรับต้องเป็นจำนวนเต็มบวก');
        }
        $date = \DateTimeImmutable::createFromFormat('!Y-m-d', $data['date']);
        if ($date === false || $date->format('Y-m-d') !== $data['date']) {
            throw new \RuntimeException('วันที่ไม่ถูกต้อง');
        }
        $data['capacity'] = $capacity;

        return $data;
    }

    private function fillActivity(Activity $item, array $data): void
    {
        $item->fill(array_intersect_key($data, array_flip([
            'code', 'name', 'date', 'location', 'capacity', 'description',
        ])));
        $category = Category::where('code', $data['category'])->firstOrFail();
        $organizer = Organizer::where('code', $data['organizer'])->firstOrFail();
        $item->category()->associate($category);
        $item->organizer()->associate($organizer);
    }

    public function create(ServerRequestInterface $request): RedirectResponse
    {
        Gate::authorize('create', Activity::class);

        try {
            $data = $this->readActivityData($request);
            $item = new Activity;
            $this->fillActivity($item, $data);
            $item->save();

            return redirect()->route('activities.view', ['activity' => $item->code])
                ->with('status', "กิจกรรม {$item->code} ถูกสร้างแล้ว");
        } catch (QueryException $e) {
            return redirect()->back()->withInput()
                ->withErrors(['alert' => $e->errorInfo[2] ?? $e->getMessage()]);
        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                ->withErrors(['alert' => $e->getMessage()]);
        }
    }

    public function update(
        ServerRequestInterface $request,
        string $activity,
    ): RedirectResponse {
        $item = $this->find($activity);
        Gate::authorize('update', $item);

        try {
            $data = $this->readActivityData($request);
            $saved = DB::transaction(function () use ($activity, $data): Activity {
                $item = Activity::where('code', $activity)->lockForUpdate()->firstOrFail();
                $item->loadCount('participants');
                if ($data['capacity'] < $item->participants_count) {
                    throw new \RuntimeException(
                        "จำนวนรับต้องไม่น้อยกว่าจำนวนผู้สมัคร ({$item->participants_count} คน)",
                    );
                }
                $this->fillActivity($item, $data);
                $item->save();

                return $item;
            });

            return redirect()->route('activities.view', ['activity' => $saved->code])
                ->with('status', "กิจกรรม {$saved->code} ถูกแก้ไขแล้ว");
        } catch (QueryException $e) {
            return redirect()->back()->withInput()
                ->withErrors(['alert' => $e->errorInfo[2] ?? $e->getMessage()]);
        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                ->withErrors(['alert' => $e->getMessage()]);
        }
    }

    public function delete(string $activity): RedirectResponse
    {
        $item = $this->find($activity);
        Gate::authorize('delete', $item);

        try {
            $item->delete();

            return redirect()->route('activities.list')
                ->with('status', "กิจกรรม {$activity} ถูกลบแล้ว");
        } catch (QueryException $e) {
            return redirect()->back()
                ->withErrors(['alert' => $e->errorInfo[2] ?? $e->getMessage()]);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['alert' => $e->getMessage()]);
        }
    }

    public function register(string $activity): RedirectResponse
    {
        $item = $this->find($activity);
        Gate::authorize('register', $item);
        $user = Auth::user();
        assert($user instanceof User);

        try {
            DB::transaction(function () use ($activity, $user): void {
                $item = Activity::where('code', $activity)->lockForUpdate()->firstOrFail();
                Gate::authorize('register', $item);
                $available = Activity::whereDoesntHave('participants',
                    static fn (Builder $query) => $query->whereKey($user->id))
                    ->where('code', $activity)->firstOrFail();
                $available->participants()->attach($user->id);
            });

            return redirect()->route('activities.view', ['activity' => $activity])
                ->with('status', "สมัครกิจกรรม {$item->name} สำเร็จ");
        } catch (AuthorizationException $e) {
            throw $e;
        } catch (QueryException $e) {
            return redirect()->back()
                ->withErrors(['alert' => $e->errorInfo[2] ?? $e->getMessage()]);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['alert' => 'สมัครไม่สำเร็จ: คุณอาจสมัครกิจกรรมนี้แล้ว']);
        }
    }

    public function cancel(string $activity): RedirectResponse
    {
        $item = $this->find($activity);
        Gate::authorize('cancel', $item);

        try {
            DB::transaction(function () use ($activity): void {
                $locked = Activity::where('code', $activity)->lockForUpdate()->firstOrFail();
                Gate::authorize('cancel', $locked);
                $locked->participants()->detach(Auth::id());
            });

            return redirect()->route('activities.view', ['activity' => $activity])
                ->with('status', "ยกเลิกการสมัคร {$item->name} แล้ว");
        } catch (AuthorizationException $e) {
            throw $e;
        } catch (QueryException $e) {
            return redirect()->back()
                ->withErrors(['alert' => $e->errorInfo[2] ?? $e->getMessage()]);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['alert' => $e->getMessage()]);
        }
    }

    public function viewParticipants(
        ServerRequestInterface $request,
        string $activity,
    ): View {
        $item = $this->find($activity);
        Gate::authorize('viewParticipants', $item);
        $params = $request->getQueryParams();
        $term = is_string($params['term'] ?? null) ? trim($params['term']) : '';
        $query = $item->participants()->orderBy('name');
        if ($term !== '') {
            $query->where(static function (Builder $query) use ($term): void {
                $query->where('name', 'LIKE', "%{$term}%")
                    ->orWhere('email', 'LIKE', "%{$term}%");
            });
        }

        return view('activities.view-participants', [
            'activity' => $item,
            'participants' => $query->paginate(self::MAX_ITEMS),
            'term' => $term,
        ]);
    }
}
