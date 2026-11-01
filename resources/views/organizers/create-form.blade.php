@extends('organizers.main', [
    'title' => 'สร้างใหม่',
])

@section('content')
    <form action="{{ route('organizers.create') }}" method="post" class="app-cmp-data-form">
        @csrf

        <label>
            <b>รหัส</b>
            <input type="text" name="code" value="{{ old('code') }}" required />
        </label>

        <label>
            <b>ชื่อผู้จัด</b>
            <input type="text" name="name" value="{{ old('name') }}" required />
        </label>

        <label>
            <b>ติดต่อ</b>
            <input type="text" name="contact" value="{{ old('contact') }}" required />
        </label>

        <div class="app-cmp-form-actions">
            <button type="submit" class="app-cl-button app-cl-filled app-cl-primary">บันทึก</button>

            <div class="app-cmp-form-secondary-actions">
                <a href="{{ session()->get('bookmarks.organizers.create-form') ?? route('organizers.index') }}"
                    class="app-cl-button app-cl-filled app-cl-warning">ยกเลิก</a>
            </div>
        </div>
    </form>
@endsection
