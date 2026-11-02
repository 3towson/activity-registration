@extends('layouts.main', [
    'title' => 'ยินดีต้อนรับ',
])

@section('header')
    <p class="app-cmp-lead">ค้นหากิจกรรมที่สนใจ แล้วสมัครเข้าร่วมได้ในไม่กี่ขั้นตอน</p>

    <div class="app-cmp-links-bar">
        <a href="{{ route('activities.index') }}" class="app-cl-button app-cl-filled app-cl-primary">ดูกิจกรรมทั้งหมด</a>

        @foreach ($categories as $category)
            <a href="{{ route('activities.list', ['category' => $category->code]) }}"
                class="app-cl-button">{{ $category->name }}</a>
        @endforeach
    </div>
@endsection

@section('content')
    <section class="app-cmp-section">
        <h2>กิจกรรมที่ใกล้จะจัด</h2>

        @php
            session()->put('bookmarks.activities.view', url()->full());
        @endphp

        @if ($activities->isEmpty())
            <p>ยังไม่มีกิจกรรมที่กำลังจะจัด</p>
        @else
            <div class="app-cmp-activity-cards">
                @foreach ($activities as $activity)
                    <article class="app-cmp-activity-card">
                        @if ($activity->isFull())
                            <span class="app-cmp-status app-cl-full">เต็มแล้ว</span>
                        @else
                            <span class="app-cmp-status app-cl-open">เปิดรับ</span>
                        @endif

                        <h3>{{ $activity->name }}</h3>
                        <p>{{ $activity->date->format('d/m/Y') }} · {{ $activity->location }}</p>
                        <p>{{ $activity->category->name }} · {{ $activity->organizer->name }}</p>
                        <p>สมัครแล้ว {{ $activity->participants_count }} / {{ $activity->capacity }} คน</p>

                        <footer>
                            <a href="{{ route('activities.view', ['activity' => $activity->code]) }}"
                                class="app-cl-button">ดูรายละเอียด</a>
                        </footer>
                    </article>
                @endforeach
            </div>
        @endif
    </section>

    <section class="app-cmp-section">
        <h2>ตัวเลขสรุป</h2>
        <dl class="app-cmp-data-detail">
            <dt>กิจกรรมทั้งหมด</dt>
            <dd>{{ $counts['activities'] }}</dd>

            <dt>กิจกรรมที่กำลังจะจัด</dt>
            <dd>{{ $counts['upcoming'] }}</dd>

            <dt>หมวดหมู่</dt>
            <dd>{{ $counts['categories'] }}</dd>

            <dt>ผู้จัด</dt>
            <dd>{{ $counts['organizers'] }}</dd>
        </dl>
    </section>
@endsection
