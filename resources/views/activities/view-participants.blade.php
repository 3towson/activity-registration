@extends('activities.main', ['title' => 'ผู้สมัคร: ' . $activity->name])

@section('header')
    @parent
    <search>
        <form method="get" class="app-cmp-search-form"
            action="{{ route('activities.view-participants', ['activity' => $activity->code]) }}">
            <fieldset>
                <legend>ค้นหาผู้สมัคร</legend>
                <label>ชื่อหรืออีเมล <input name="term" value="{{ $term }}" /></label>
                <button class="app-cl-button">ค้นหา</button>
            </fieldset>
        </form>
    </search>
@endsection

@section('content')
    <table class="app-cmp-data-list">
        <thead><tr><th>ชื่อ</th><th>อีเมล</th><th>เวลาสมัคร</th></tr></thead>
        <tbody>
            @forelse ($participants as $participant)
                <tr>
                    <td>{{ $participant->name }}</td>
                    <td>{{ $participant->email }}</td>
                    <td>{{ $participant->pivot->created_at->format('d/m/Y H:i') }}</td>
                </tr>
            @empty
                <tr><td colspan="3">ไม่พบผู้สมัคร</td></tr>
            @endforelse
        </tbody>
    </table>
    {{ $participants->withQueryString()->links() }}
    <a class="app-cl-button"
        href="{{ route('activities.view', ['activity' => $activity->code]) }}">กลับรายละเอียด</a>
@endsection
