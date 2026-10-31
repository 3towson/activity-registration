@extends('categories.main', [
    'title' => $category->name,
])

@section('header')
    <div class="app-cmp-links-bar">
        <a href="{{ session()->get('bookmarks.categories.view') ?? route('categories.index') }}"
            class="app-cl-button app-cl-primary">&lt; Back</a>
    </div>
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
