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
            @foreach ($categories as $category)
                <tr>
                    <th class="app-cl-code">{{ $category->code }}</th>
                    <td>{{ $category->name }}</td>
                    <td class="app-cl-number">{{ $category->activities_count }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection