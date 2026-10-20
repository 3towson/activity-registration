@extends('layouts.main', [
    'title' => 'ตรวจฐานร่วม',
])

{{-- หน้าชั่วคราวสำหรับตรวจว่าฐานร่วมใช้ได้ — คนที่ 3 จะเปลี่ยนเป็นหน้าแรกจริงในขั้นที่ 2 --}}

@section('header')
    <p class="app-cmp-lead">
        ถ้าหน้านี้แสดงตัวเลขครบและ login เป็นผู้ดูแล / สมาชิกได้ แปลว่าฐานร่วมพร้อม แยกกันทำต่อได้เลย
    </p>
@endsection

@section('content')
    <section class="app-cmp-section">
        <h2>จำนวนข้อมูลในฐานข้อมูล</h2>
        <table class="app-cmp-data-list">
            <thead>
                <tr><th>ตาราง</th><th class="app-cl-number">จำนวน</th><th class="app-cl-number">ควรได้</th></tr>
            </thead>
            <tbody>
                @foreach (['users' => 6, 'categories' => 4, 'organizers' => 4, 'activities' => 12, 'activity_user' => 15] as $table => $expected)
                    <tr>
                        <th>{{ $table }}</th>
                        <td class="app-cl-number">{{ $counts[$table] }}</td>
                        <td class="app-cl-number">{{ $expected }} {{ $counts[$table] === $expected ? '✓' : '✗' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </section>

    <section class="app-cmp-section">
        <h2>สถานะการ login</h2>
        @auth
            <p>
                เข้าสู่ระบบเป็น <strong>{{ \Auth::user()->name }}</strong> ({{ \Auth::user()->email }})
                — role <strong>{{ \Auth::user()->role }}</strong>
                · สมัครไว้ {{ \Auth::user()->activities()->count() }} กิจกรรม
            </p>
        @else
            <p>
                ยังไม่ได้เข้าสู่ระบบ — <a href="{{ route('login') }}">ลอง login</a> ด้วย
                <code>admin@activity.test</code> หรือ <code>user@activity.test</code> (รหัส <code>1234</code>)
            </p>
        @endauth
    </section>

    <section class="app-cmp-section">
        <h2>กิจกรรมตัวอย่าง (ทดสอบ relationships)</h2>
        <table class="app-cmp-data-list">
            <thead>
                <tr>
                    <th>รหัส</th>
                    <th>ชื่อกิจกรรม</th>
                    <th>หมวดหมู่ <small>category()</small></th>
                    <th>ผู้จัด <small>organizer()</small></th>
                    <th>วันที่จัด</th>
                    <th class="app-cl-number">สมัคร / รับ <small>participants()</small></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($activities as $activity)
                    <tr>
                        <th>{{ $activity->code }}</th>
                        <td>{{ $activity->name }}</td>
                        <td>{{ $activity->category->name }}</td>
                        <td>{{ $activity->organizer->name }}</td>
                        <td class="app-cl-nowrap">{{ $activity->date->translatedFormat('j M Y') }}</td>
                        <td class="app-cl-number">{{ $activity->participants_count }} / {{ $activity->capacity }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </section>
@endsection
