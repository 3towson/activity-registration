@extends('activities.main', ['title' => 'แก้ไขกิจกรรม'])

@section('content')
    <form method="post" class="app-cmp-data-form" action="{{ route('activities.update', ['activity' => $activity->code]) }}">
        @csrf
        <div>
            <label for="code">รหัส</label>
            <input id="code" name="code" type="text" maxlength="64" pattern="[A-Za-z0-9_-]+" required
                value="{{ old('code', $activity->code) }}" />
        </div>
        <div>
            <label for="name">ชื่อกิจกรรม</label>
            <input id="name" name="name" type="text" maxlength="255" required
                value="{{ old('name', $activity->name) }}" />
        </div>
        <div>
            <label for="date">วันที่จัด</label>
            <input id="date" name="date" type="date" required
                value="{{ old('date', $activity->date->format('Y-m-d')) }}" />
        </div>
        <div>
            <label for="location">สถานที่</label>
            <input id="location" name="location" type="text" maxlength="255" required
                value="{{ old('location', $activity->location) }}" />
        </div>
        <div>
            <label for="capacity">จำนวนรับ</label>
            <input id="capacity" name="capacity" type="number" min="1" max="4294967295" required
                value="{{ old('capacity', $activity->capacity) }}" />
        </div>
        <div>
            <label for="category">หมวดหมู่</label>
            <select id="category" name="category" required>
                <option value="">เลือกหมวดหมู่</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->code }}"
                        @selected(old('category', $activity->category->code) === $category->code)>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="organizer">ผู้จัด</label>
            <select id="organizer" name="organizer" required>
                <option value="">เลือกผู้จัด</option>
                @foreach ($organizers as $organizer)
                    <option value="{{ $organizer->code }}"
                        @selected(old('organizer', $activity->organizer->code) === $organizer->code)>
                        {{ $organizer->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="description">รายละเอียด</label>
            <textarea id="description" name="description" rows="5" required>{{ old('description', $activity->description) }}</textarea>
        </div>
        <div class="app-cmp-form-actions">
            <button class="app-cl-button app-cl-filled app-cl-primary">บันทึก</button>
        </div>
    </form>
    <div class="app-cmp-form-secondary-actions">
        <a class="app-cl-button"
            href="{{ session('bookmarks.activities.view') ?? route('activities.index') }}">ยกเลิก</a>
    </div>
@endsection
