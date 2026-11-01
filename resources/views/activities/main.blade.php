@extends('layouts.main')

@section('header')
    <nav class="app-cmp-links-bar">
        <a class="app-cl-link" href="{{ route('activities.index') }}">กิจกรรมทั้งหมด</a>
        @can('create', \App\Models\Activity::class)
            <a class="app-cl-button app-cl-filled app-cl-primary"
                href="{{ route('activities.create-form') }}">สร้างกิจกรรม</a>
        @endcan
    </nav>
@endsection
