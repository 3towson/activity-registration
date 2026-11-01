@extends('activities.main', ['title' => $activity->name])

@section('content')
    <dl class="app-cmp-data-detail">
        <dt>รหัส</dt><dd>{{ $activity->code }}</dd>
        <dt>หมวดหมู่</dt><dd>{{ $activity->category->name }}</dd>
        <dt>ผู้จัด</dt><dd>{{ $activity->organizer->name }}</dd>
        <dt>วันที่</dt><dd>{{ $activity->date->format('d/m/Y') }}</dd>
        <dt>สถานที่</dt><dd>{{ $activity->location }}</dd>
        <dt>สมัคร / รับ</dt><dd>{{ $activity->participants_count }} / {{ $activity->capacity }}</dd>
        <dt>รายละเอียด</dt><dd style="white-space: pre-line">{{ $activity->description }}</dd>
        <dt>สถานะ</dt>
        <dd>{{ !$activity->isOpen() ? 'จบแล้ว' : ($activity->isFull() ? 'เต็มแล้ว' : 'เปิดรับ') }}</dd>
    </dl>

    <div class="app-cmp-form-actions">
        @guest
            <a class="app-cl-button" href="{{ route('login') }}">เข้าสู่ระบบเพื่อสมัคร</a>
        @endguest

        @auth
            @if (\Auth::user()->isMember())
                @if ($registered)
                    <p>คุณสมัครกิจกรรมนี้แล้ว</p>
                    @can('cancel', $activity)
                        <form method="post" action="{{ route('activities.cancel', ['activity' => $activity->code]) }}">
                            @csrf
                            <button class="app-cl-button app-cl-warning">ยกเลิกการสมัคร</button>
                        </form>
                    @else
                        <p>จบแล้ว ยกเลิกการสมัครไม่ได้</p>
                    @endcan
                    @if (Route::has('activities.ticket'))
                        <a class="app-cl-button"
                            href="{{ route('activities.ticket', ['activity' => $activity->code]) }}">ดาวน์โหลดบัตร</a>
                    @endif
                @else
                    @can('register', $activity)
                        <form method="post" action="{{ route('activities.register', ['activity' => $activity->code]) }}">
                            @csrf
                            <button class="app-cl-button app-cl-filled app-cl-primary">สมัครกิจกรรม</button>
                        </form>
                    @else
                        <p>{{ !$activity->isOpen() ? 'จบแล้ว' : 'เต็มแล้ว' }}</p>
                    @endcan
                @endif
            @endif
        @endauth

        @can('update', $activity)
            <a class="app-cl-button"
                href="{{ route('activities.update-form', ['activity' => $activity->code]) }}">แก้ไข</a>
        @endcan
        @can('viewParticipants', $activity)
            <a class="app-cl-button"
                href="{{ route('activities.view-participants', ['activity' => $activity->code]) }}">รายชื่อผู้สมัคร</a>
        @endcan
        @can('delete', $activity)
            <form id="app-form-delete" method="post"
                action="{{ route('activities.delete', ['activity' => $activity->code]) }}">
                @csrf
            </form>
            <button type="submit" form="app-form-delete" class="app-cl-button app-cl-warning"
                onclick="return confirm('ยืนยันการลบกิจกรรม?')">ลบกิจกรรม</button>
        @endcan
    </div>
    <div class="app-cmp-form-secondary-actions">
        <a class="app-cl-button"
            href="{{ session('bookmarks.activities.view') ?? route('activities.index') }}">กลับรายการ</a>
    </div>
@endsection
