@extends('categories.main', [
    'title' => $category->name,
])

@section('header')
    <nav>
        <ul class="app-cmp-links">
            <li>
                <a href="{{ session()->get('bookmarks.categories.view') ?? route('categories.index') }}">&lt; Back</a>
            </li>
        </ul>
    </nav>
@endsection

@section('content')
    <dl class="app-cmp-data-detail">
        <dt>Code</dt>
        <dd><span class="app-cl-code">{{ $category->code }}</span></dd>

        <dt>Name</dt>
        <dd>{{ $category->name }}</dd>

        <dt>Description</dt>
        <dd>{{ $category->description }}</dd>
    </dl>
@endsection
