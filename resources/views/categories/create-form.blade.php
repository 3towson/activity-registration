@extends('categories.main', [
    'title' => 'Create',
])

@section('content')
    <form action="{{ route('categories.create') }}" method="post" class="app-cmp-data-form">
        @csrf

        <label>
            <b>Code</b>
            <input type="text" name="code" value="{{ old('code') }}" required />
        </label>

        <label>
            <b>Name</b>
            <input type="text" name="name" value="{{ old('name') }}" required />
        </label>

        <label>
            <b>Description</b>
            <textarea name="description" rows="5" required>{{ old('description') }}</textarea>
        </label>

        <div class="app-cmp-form-actions">
            <button type="submit" class="app-cl-button app-cl-filled app-cl-primary">Create</button>

            <div class="app-cmp-form-secondary-actions">
                <a href="{{ session()->get('bookmarks.categories.create-form') ?? route('categories.index') }}"
                    class="app-cl-button app-cl-filled app-cl-warning">Cancel</a>
            </div>
        </div>
    </form>
@endsection
