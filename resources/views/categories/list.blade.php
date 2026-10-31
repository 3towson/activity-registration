@extends('categories.main', [
    'title' => 'List',
])

@section('header')
    <search>
        <form action="{{ route('categories.list') }}" method="get" class="app-cmp-search-form">
            <fieldset>
                <legend>Search</legend>
                <label>
                    <b>Term</b>
                    <input type="text" name="term" value="{{ $criteria['term'] }}" />
                </label>
                <br />
                <button type="submit">Filter</button>
                <a href="{{ route('categories.list') }}">Clear</a>
            </fieldset>
        </form>
    </search>

    <div class="app-cmp-links-bar">
        @can('create', \App\Models\Category::class)
            @php
                session()->put('bookmarks.categories.create-form', url()->full());
            @endphp
            <a href="{{ route('categories.create-form') }}" class="app-cl-button app-cl-filled app-cl-primary">New Category</a>
        @endcan

        {{ $categories->withQueryString()->links() }}
    </div>
@endsection


@section('content')
    <table class="app-cmp-data-list">
        <caption>List of Categories</caption>
        <thead>
            <tr>
                <th>Code</th>
                <th>Name</th>
                <th class="app-cl-number">No. of Activities</th>
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
