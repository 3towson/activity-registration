@extends('organizers.main', [
    'title' => "{$organizer->name}: กิจกรรม",
])

@section('header')
    <search>
        <form action="{{ route('organizers.view-activities', ['organizer' => $organizer->code]) }}" method="get"
            class="app-cmp-search-form">
            <fieldset>
                <legend>ค้นหากิจกรรมของผู้จัด</legend>
                <label>
                    <b>คำค้น</b>
                    <input type="text" name="term" value="{{ $criteria['term'] }}" />
                </label>
                <br />
                <button type="submit">ค้นหา</button>
                <a href="{{ route('organizers.view-activities', ['organizer' => $organizer->code]) }}">ล้างคำค้น</a>
            </fieldset>
        </form>
    </search>

    <div class="app-cmp-links-bar">
        <a href="{{ session()->get('bookmarks.organizers.view-activities') ?? route('organizers.view', ['organizer' => $organizer->code]) }}"
            class="app-cl-button app-cl-primary">&lt; กลับ</a>

        {{ $activities->withQueryString()->links() }}
    </div>
@endsection

@section('content')
    <table class="app-cmp-data-list">
        <caption>รายการกิจกรรม</caption>
        <thead>
            <tr>
                <th>รหัส</th>
                <th>ชื่อกิจกรรม</th>
                <th>หมวด</th>
                <th>วันที่</th>
                <th>สถานที่</th>
                <th class="app-cl-number">จำนวนรับ</th>
            </tr>
        </thead>
        <tbody>
            @php
                session()->put('bookmarks.activities.view', url()->full());
            @endphp
            @foreach ($activities as $activity)
                <tr>
                    <th class="app-cl-code">
                        <a href="{{ route('activities.view', ['activity' => $activity->code]) }}">
                            {{ $activity->code }}
                        </a>
                    </th>
                    <td>{{ $activity->name }}</td>
                    <td>{{ $activity->category->name }}</td>
                    <td class="app-cl-nowrap">{{ $activity->date->format('d/m/Y') }}</td>
                    <td>{{ $activity->location }}</td>
                    <td class="app-cl-number">{{ $activity->capacity }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
