@extends('activities.main', ['title' => 'กิจกรรมทั้งหมด'])

@section('header')
    @parent
    <search>
        <form action="{{ route('activities.list') }}" method="get" class="app-cmp-search-form">
            <fieldset>
                <legend>ค้นหากิจกรรม</legend>
                <label>คำค้น <input name="term" value="{{ $criteria['term'] }}" /></label>
                <label>หมวดหมู่
                    <select name="category">
                        <option value="">ทุกหมวดหมู่</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->code }}"
                                @selected($criteria['category'] === $category->code)>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </label>
                <label>
                    <input type="checkbox" name="upcoming" value="1" @checked($criteria['upcoming']) />
                    เฉพาะกิจกรรมวันนี้และอนาคต
                </label>
                <button class="app-cl-button" type="submit">ค้นหา</button>
                <a href="{{ route('activities.list') }}">ล้างคำค้น</a>
            </fieldset>
        </form>
    </search>
@endsection

@section('content')
    <table class="app-cmp-data-list">
        <thead>
            <tr><th>รหัส</th><th>ชื่อกิจกรรม</th><th>หมวด</th><th>ผู้จัด</th>
                <th>วันที่</th><th>สถานที่</th><th>สมัคร / รับ</th><th>สถานะ</th></tr>
        </thead>
        <tbody>
            @forelse ($activities as $activity)
                <tr>
                    <th class="app-cl-code">{{ $activity->code }}</th>
                    <td><a href="{{ route('activities.view', ['activity' => $activity->code]) }}">
                        {{ $activity->name }}</a></td>
                    <td>{{ $activity->category->name }}</td>
                    <td>{{ $activity->organizer->name }}</td>
                    <td>{{ $activity->date->format('d/m/Y') }}</td>
                    <td>{{ $activity->location }}</td>
                    <td>{{ $activity->participants_count }} / {{ $activity->capacity }}</td>
                    <td>{{ !$activity->isOpen() ? 'จบแล้ว' : ($activity->isFull() ? 'เต็มแล้ว' : 'เปิดรับ') }}</td>
                </tr>
            @empty
                <tr><td colspan="8">ไม่พบกิจกรรม</td></tr>
            @endforelse
        </tbody>
    </table>
    {{ $activities->withQueryString()->links() }}
@endsection
