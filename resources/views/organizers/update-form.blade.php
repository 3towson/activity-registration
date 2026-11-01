@extends('organizers.main', [
    'title' => "แก้ไข {$organizer->code}",
])

@section('content')
    <form action="{{ route('organizers.update', ['organizer' => $organizer->code]) }}" method="post" class="app-cmp-data-form">
        @csrf

        <label>
            <b>รหัส</b>
            <input type="text" name="code" value="{{ old('code', $organizer->code) }}" required />
        </label>

        <label>
            <b>ชื่อผู้จัด</b>
            <input type="text" name="name" value="{{ old('name', $organizer->name) }}" required />
        </label>

        <label>
            <b>ติดต่อ</b>
            <input type="text" name="contact" value="{{ old('contact', $organizer->contact) }}" required />
        </label>

        <div class="app-cmp-form-actions">
            <button type="submit" class="app-cl-button app-cl-filled app-cl-primary">บันทึก</button>

            <div class="app-cmp-form-secondary-actions">
                <a href="{{ session()->get('bookmarks.organizers.update-form') ?? route('organizers.view', ['organizer' => $organizer->code]) }}"
                    class="app-cl-button app-cl-filled app-cl-warning">ยกเลิก</a>
            </div>
        </div>
    </form>
@endsection
