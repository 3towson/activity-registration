@extends('organizers.main', [
    'title' => 'รายการ',
])

@section('header')
    <search>
        <form action="{{ route('organizers.list') }}" method="get" class="app-cmp-search-form">
            <fieldset>
                <legend>ค้นหาผู้จัด</legend>
                <label>
                    <b>คำค้น</b>
                    <input type="text" name="term" value="{{ $criteria['term'] }}" />
                </label>
                <br />
                <button type="submit">ค้นหา</button>
                <a href="{{ route('organizers.list') }}">ล้างคำค้น</a>
            </fieldset>
        </form>
    </search>

    <div class="app-cmp-links-bar">
        @can('create', \App\Models\Organizer::class)
            @php
                session()->put('bookmarks.organizers.create-form', url()->full());
                session()->put('bookmarks.organizers.create', url()->full());
            @endphp
            <a href="{{ route('organizers.create-form') }}" class="app-cl-button app-cl-filled app-cl-primary">สร้างผู้จัด</a>
        @endcan

        {{ $organizers->withQueryString()->links() }}
    </div>
@endsection


@section('content')
    <table class="app-cmp-data-list">
        <caption>รายการผู้จัด</caption>
        <thead>
            <tr>
                <th>รหัส</th>
                <th>ชื่อผู้จัด</th>
                <th>ติดต่อ</th>
                <th class="app-cl-number">จำนวนกิจกรรม</th>
            </tr>
        </thead>
        <tbody>
            @php
                session()->put('bookmarks.organizers.view', url()->full());
            @endphp
            @foreach ($organizers as $organizer)
                <tr>
                    <th class="app-cl-code">
                        <a href="{{ route('organizers.view', ['organizer' => $organizer->code]) }}">
                            {{ $organizer->code }}
                        </a>
                    </th>
                    <td>{{ $organizer->name }}</td>
                    <td>{{ $organizer->contact }}</td>
                    <td class="app-cl-number">{{ $organizer->activities_count }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
