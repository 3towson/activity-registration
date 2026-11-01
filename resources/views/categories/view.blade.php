@extends('categories.main', [
    'title' => $category->name,
])

@section('header')
    @php
        session()->put('bookmarks.categories.view-activities', url()->full());
        session()->put('bookmarks.categories.update-form', url()->full());
        session()->put('bookmarks.categories.update', session()->get('bookmarks.categories.view'));
        session()->put('bookmarks.categories.delete', session()->get('bookmarks.categories.view'));
    @endphp

    <form action="{{ route('categories.delete', ['category' => $category->code]) }}" method="post" id="app-form-delete">
        @csrf
    </form>

    <div class="app-cmp-links-bar">
        <a href="{{ session()->get('bookmarks.categories.view') ?? route('categories.index') }}"
            class="app-cl-button app-cl-primary">&lt; กลับ</a>

        <a href="{{ route('categories.view-activities', ['category' => $category->code]) }}"
            class="app-cl-button app-cl-primary">ดูกิจกรรมในหมวด</a>

        @can('update', $category)
            <a href="{{ route('categories.update-form', ['category' => $category->code]) }}"
                class="app-cl-button app-cl-filled app-cl-primary">แก้ไข</a>
        @endcan

        @can('delete', $category)
            <button type="submit" form="app-form-delete"
                class="app-cl-button app-cl-filled app-cl-warning">ลบ</button>
        @endcan
    </div>
@endsection

@section('content')
    <dl class="app-cmp-data-detail">
        <dt>รหัส</dt>
        <dd><span class="app-cl-code">{{ $category->code }}</span></dd>

        <dt>ชื่อหมวดหมู่</dt>
        <dd>{{ $category->name }}</dd>

        <dt>รายละเอียด</dt>
        <dd>{{ $category->description }}</dd>
    </dl>
@endsection
