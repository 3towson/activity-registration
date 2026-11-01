@extends('categories.main', [
    'title' => "แก้ไข {$category->code}",
])

@section('content')
    <form action="{{ route('categories.update', ['category' => $category->code]) }}" method="post" class="app-cmp-data-form">
        @csrf

        <label>
            <b>รหัส</b>
            <input type="text" name="code" value="{{ old('code', $category->code) }}" required />
        </label>

        <label>
            <b>ชื่อหมวดหมู่</b>
            <input type="text" name="name" value="{{ old('name', $category->name) }}" required />
        </label>

        <label>
            <b>รายละเอียด</b>
            <textarea name="description" rows="5" required>{{ old('description', $category->description) }}</textarea>
        </label>

        <div class="app-cmp-form-actions">
            <button type="submit" class="app-cl-button app-cl-filled app-cl-primary">บันทึก</button>

            <div class="app-cmp-form-secondary-actions">
                <a href="{{ session()->get('bookmarks.categories.update-form') ?? route('categories.view', ['category' => $category->code]) }}"
                    class="app-cl-button app-cl-filled app-cl-warning">ยกเลิก</a>
            </div>
        </div>
    </form>
@endsection
