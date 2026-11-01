@extends('categories.main', [
    'title' => "{$category->name}: Activities",
])

@section('header')
    <div class="app-cmp-links-bar">
        <a href="{{ session()->get('bookmarks.categories.view-activities') ?? route('categories.view', ['category' => $category->code]) }}"
            class="app-cl-button app-cl-primary">&lt; Back</a>

        {{ $activities->withQueryString()->links() }}
    </div>
@endsection

@section('content')
    <table class="app-cmp-data-list">
        <caption>List of Activities</caption>
        <thead>
            <tr>
                <th>Code</th>
                <th>Name</th>
                <th>Date</th>
                <th>Location</th>
                <th class="app-cl-number">Capacity</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($activities as $activity)
                <tr>
                    <th class="app-cl-code">
                        {{-- ลิงก์จะโผล่เองเมื่อคนที่ 1 เพิ่ม route activities.view --}}
                        @if (Route::has('activities.view'))
                            <a href="{{ route('activities.view', ['activity' => $activity->code]) }}">
                                {{ $activity->code }}
                            </a>
                        @else
                            {{ $activity->code }}
                        @endif
                    </th>
                    <td>{{ $activity->name }}</td>
                    <td class="app-cl-nowrap">{{ $activity->date->format('d/m/Y') }}</td>
                    <td>{{ $activity->location }}</td>
                    <td class="app-cl-number">{{ $activity->capacity }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
