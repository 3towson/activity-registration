@extends('categories.main', [
    'title' => 'รายการ',
])

@section('header')
    <search>
        <form action="{{ route('categories.list') }}" method="get" class="app-cmp-search-form">
            <fieldset>
                <legend>ค้นหาหมวดหมู่</legend>
                <label>
                    <b>คำค้น</b>
                    <input type="text" name="term" value="{{ $criteria['term'] }}" />
                </label>
                <br />
                <button type="submit">ค้นหา</button>
                <a href="{{ route('categories.list') }}">ล้างคำค้น</a>
            </fieldset>
        </form>
    </search>

    <div class="app-cmp-links-bar">
        @can('create', \App\Models\Category::class)
            @php
                session()->put('bookmarks.categories.create-form', url()->full());
                session()->put('bookmarks.categories.create', url()->full());
            @endphp
            <a href="{{ route('categories.create-form') }}" class="app-cl-button app-cl-filled app-cl-primary">สร้างหมวดหมู่</a>
        @endcan

        {{ $categories->withQueryString()->links() }}
    </div>
@endsection


@section('content')
    <table class="app-cmp-data-list">
        <caption>รายการหมวดหมู่</caption>
        <thead>
            <tr>
                <th>รหัส</th>
                <th>ชื่อหมวดหมู่</th>
                <th class="app-cl-number">จำนวนกิจกรรม</th>
            </tr>
        </thead>
        <tbody>
            @php
                session()->put('bookmarks.categories.view', url()->full());
            @endphp
            @foreach ($categories as $category)
                <tr>
                    <th class="app-cl-code">
                        <a href="{{ route('categories.view', ['category' => $category->code]) }}">
                            {{ $category->code }}
                        </a>
                    </th>
                    <td>{{ $category->name }}</td>
                    <td class="app-cl-number">{{ $category->activities_count }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
