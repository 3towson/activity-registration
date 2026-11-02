@extends('layouts.main', [
    'title' => 'กิจกรรมของฉัน',
])

@section('header')
    <div class="app-cmp-links-bar">
        <a href="{{ route('my-activities.list') }}"
            @class(['app-cl-button', 'app-cl-filled app-cl-primary' => !$showPast])>กำลังจะมาถึง</a>
        <a href="{{ route('my-activities.list', ['tab' => 'past']) }}"
            @class(['app-cl-button', 'app-cl-filled app-cl-primary' => $showPast])>ที่ผ่านมาแล้ว</a>

        {{ $activities->withQueryString()->links() }}
    </div>
@endsection

@section('content')
    <table class="app-cmp-data-list">
        <caption>{{ $showPast ? 'กิจกรรมที่ผ่านมาแล้ว' : 'กิจกรรมที่กำลังจะมาถึง' }}</caption>
        <thead>
            <tr>
                <th>รหัส</th>
                <th>ชื่อกิจกรรม</th>
                <th>หมวด</th>
                <th>ผู้จัด</th>
                <th>วันที่</th>
                <th>สถานที่</th>
                <th>เวลาสมัคร</th>
                <th>บัตร</th>
            </tr>
        </thead>
        <tbody>
            @php
                session()->put('bookmarks.activities.view', url()->full());
            @endphp
            @forelse ($activities as $activity)
                <tr>
                    <th class="app-cl-code">
                        <a href="{{ route('activities.view', ['activity' => $activity->code]) }}">
                            {{ $activity->code }}
                        </a>
                    </th>
                    <td>{{ $activity->name }}</td>
                    <td>{{ $activity->category->name }}</td>
                    <td>{{ $activity->organizer->name }}</td>
                    <td class="app-cl-nowrap">{{ $activity->date->format('d/m/Y') }}</td>
                    <td>{{ $activity->location }}</td>
                    <td class="app-cl-nowrap">{{ $activity->pivot->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        <a class="app-cl-button"
                            href="{{ route('activities.ticket', ['activity' => $activity->code]) }}">ดาวน์โหลด</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8">ไม่มีกิจกรรม</td></tr>
            @endforelse
        </tbody>
    </table>
@endsection
