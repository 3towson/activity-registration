@extends('categories.main', [
    'title' => 'สร้างใหม่',
])

@section('content')
    <form action="{{ route('categories.create') }}" method="post" class="app-cmp-data-form">
        @csrf

        <label>
            <b>รหัส</b>
            <input type="text" name="code" value="{{ old('code') }}" required />
        </label>

        <label>
            <b>ชื่อหมวดหมู่</b>
            <input type="text" name="name" value="{{ old('name') }}" required />
        </label>

        <label>
            <b>รายละเอียด</b>
            <textarea name="description" rows="5" required>{{ old('description') }}</textarea>
        </label>

        <div class="app-cmp-form-actions">
            <button type="submit" class="app-cl-button app-cl-filled app-cl-primary">บันทึก</button>

            <div class="app-cmp-form-secondary-actions">
                <a href="{{ session()->get('bookmarks.categories.create-form') ?? route('categories.index') }}"
                    class="app-cl-button app-cl-filled app-cl-warning">ยกเลิก</a>
            </div>
        </div>
    </form>
@endsection
