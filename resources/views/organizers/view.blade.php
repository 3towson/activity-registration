@extends('organizers.main', [
    'title' => $organizer->name,
])

@section('header')
    @php
        session()->put('bookmarks.organizers.view-activities', url()->full());
        session()->put('bookmarks.organizers.update-form', url()->full());
        session()->put('bookmarks.organizers.update', session()->get('bookmarks.organizers.view'));
        session()->put('bookmarks.organizers.delete', session()->get('bookmarks.organizers.view'));
    @endphp

    <form action="{{ route('organizers.delete', ['organizer' => $organizer->code]) }}" method="post" id="app-form-delete">
        @csrf
    </form>

    <div class="app-cmp-links-bar">
        <a href="{{ session()->get('bookmarks.organizers.view') ?? route('organizers.index') }}"
            class="app-cl-button app-cl-primary">&lt; กลับ</a>

        <a href="{{ route('organizers.view-activities', ['organizer' => $organizer->code]) }}"
            class="app-cl-button app-cl-primary">ดูกิจกรรมของผู้จัด</a>

        @can('update', $organizer)
            <a href="{{ route('organizers.update-form', ['organizer' => $organizer->code]) }}"
                class="app-cl-button app-cl-filled app-cl-primary">แก้ไข</a>
        @endcan

        @can('delete', $organizer)
            <button type="submit" form="app-form-delete"
                class="app-cl-button app-cl-filled app-cl-warning">ลบ</button>
        @endcan
    </div>
@endsection

@section('content')
    <dl class="app-cmp-data-detail">
        <dt>รหัส</dt>
        <dd><span class="app-cl-code">{{ $organizer->code }}</span></dd>

        <dt>ชื่อผู้จัด</dt>
        <dd>{{ $organizer->name }}</dd>

        <dt>ติดต่อ</dt>
        <dd>{{ $organizer->contact }}</dd>
    </dl>
@endsection
