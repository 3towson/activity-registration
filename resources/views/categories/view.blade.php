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
            class="app-cl-button app-cl-primary">&lt; Back</a>

        <a href="{{ route('categories.view-activities', ['category' => $category->code]) }}"
            class="app-cl-button app-cl-primary">View Activities</a>

        @can('update', $category)
            <a href="{{ route('categories.update-form', ['category' => $category->code]) }}"
                class="app-cl-button app-cl-filled app-cl-primary">Update</a>
        @endcan

        @can('delete', $category)
            <button type="submit" form="app-form-delete"
                class="app-cl-button app-cl-filled app-cl-warning">Delete</button>
        @endcan
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
