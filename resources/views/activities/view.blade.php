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
        <dd>
            @if (!$activity->isOpen())
                <span class="app-cmp-status app-cl-closed">จบแล้ว</span>
            @elseif ($activity->isFull())
                <span class="app-cmp-status app-cl-full">เต็มแล้ว</span>
            @else
                <span class="app-cmp-status app-cl-open">เปิดรับ</span>
            @endif
        </dd>
    </dl>

    @guest
        <section class="app-cmp-signup-box">
            <h2>การสมัครเข้าร่วม</h2>
            <div class="app-cmp-form-actions">
                <a class="app-cl-button" href="{{ route('login') }}">เข้าสู่ระบบเพื่อสมัคร</a>
            </div>
        </section>
    @endguest

    @auth
        @if (\Auth::user()->isMember())
            <section class="app-cmp-signup-box">
                <h2>การสมัครเข้าร่วม</h2>

                @if ($registered)
                    <p>คุณสมัครกิจกรรมนี้แล้ว</p>
                    @cannot('cancel', $activity)
                        <p>จบแล้ว ยกเลิกการสมัครไม่ได้</p>
                    @endcannot

                    <div class="app-cmp-form-actions">
                        @can('cancel', $activity)
                            <form method="post" action="{{ route('activities.cancel', ['activity' => $activity->code]) }}">
                                @csrf
                                <button class="app-cl-button app-cl-warning">ยกเลิกการสมัคร</button>
                            </form>
                        @endcan
                        @if (Route::has('activities.ticket'))
                            <a class="app-cl-button"
                                href="{{ route('activities.ticket', ['activity' => $activity->code]) }}">ดาวน์โหลดบัตร</a>
                        @endif
                    </div>
                @else
                    @can('register', $activity)
                        <div class="app-cmp-form-actions">
                            <form method="post" action="{{ route('activities.register', ['activity' => $activity->code]) }}">
                                @csrf
                                <button class="app-cl-button app-cl-filled app-cl-primary">สมัครกิจกรรม</button>
                            </form>
                        </div>
                    @else
                        <p>{{ !$activity->isOpen() ? 'จบแล้ว' : 'เต็มแล้ว' }}</p>
                    @endcan
                @endif
            </section>
        @endif
    @endauth

    <div class="app-cmp-form-actions">
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
