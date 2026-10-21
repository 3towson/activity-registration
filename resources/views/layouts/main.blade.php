<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $title }} — ระบบสมัครเข้าร่วมกิจกรรม</title>

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Thai:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href="{{ asset('css/app.css') }}" />
</head>

<body>
    <header id="app-main-header">
        <nav>
            <a href="{{ route('home') }}" class="app-cmp-brand">สมัครกิจกรรม</a>

            {{--
                เมนูหลัก (โครงเดียวกับ layout ในคาบ: ul.app-cmp-links + li.app-cl-active)
                - Route::has(...) → เมนูของเพื่อนจะโผล่เองเมื่อเพื่อนเพิ่ม route นั้นแล้ว (แยกกันทำได้ไม่พัง)
                - @can(...)       → แสดงตามสิทธิ์ (Policy ของเจ้าของ model)
                - ลิงก์ไป route xxx.index แบบในคาบ (index redirect ไป xxx.list)
            --}}
            <ul class="app-cmp-links">
                <li @class(['app-cl-active' => Route::is('home')])>
                    <a href="{{ route('home') }}">หน้าแรก</a>
                </li>

                @if (Route::has('activities.index'))
                    <li @class(['app-cl-active' => Route::is('activities.*')])>
                        <a href="{{ route('activities.index') }}">กิจกรรม</a>
                    </li>
                @endif

                @auth
                    @if (\Auth::user()->isMember() && Route::has('my-activities.list'))
                        <li @class(['app-cl-active' => Route::is('my-activities.*')])>
                            <a href="{{ route('my-activities.list') }}">กิจกรรมของฉัน</a>
                        </li>
                    @endif
                @endauth

                @if (Route::has('categories.index'))
                    @can('list', \App\Models\Category::class)
                        <li @class(['app-cl-active' => Route::is('categories.*')])>
                            <a href="{{ route('categories.index') }}">หมวดหมู่</a>
                        </li>
                    @endcan
                @endif

                @if (Route::has('organizers.index'))
                    @can('list', \App\Models\Organizer::class)
                        <li @class(['app-cl-active' => Route::is('organizers.*')])>
                            <a href="{{ route('organizers.index') }}">ผู้จัด</a>
                        </li>
                    @endcan
                @endif
            </ul>

            {{-- แผงผู้ใช้: login แล้วแสดงชื่อ + role + Logout / ยังไม่ login แสดงปุ่มเข้าสู่ระบบ --}}
            @auth
                <form action="{{ route('logout') }}" method="post" id="app-form-logout">
                    @csrf
                </form>
                <ul class="app-cmp-user-panel">
                    <li>
                        {{ \Auth::user()->name }}
                        <span @class([
                            'app-cmp-role',
                            'app-cl-admin' => \Auth::user()->isAdministrator(),
                        ])>{{ \Auth::user()->isAdministrator() ? 'ผู้ดูแล' : 'สมาชิก' }}</span>
                    </li>
                    <li>
                        <button type="submit" form="app-form-logout" class="app-cl-button">ออกจากระบบ</button>
                    </li>
                </ul>
            @endauth

            @guest
                <ul class="app-cmp-user-panel">
                    <li><a href="{{ route('login') }}" class="app-cl-button">เข้าสู่ระบบ</a></li>
                    @if (Route::has('register-form'))
                        <li><a href="{{ route('register-form') }}" class="app-cl-button app-cl-filled">สมัครสมาชิก</a></li>
                    @endif
                </ul>
            @endguest
        </nav>
    </header>

    <main id="app-main-content">
        <header class="app-cmp-content-header">
            <h1>@section('title'){{ $title }}@show</h1>

            {{-- ข้อความแจ้งเตือนทุกหน้า: status = สำเร็จ (เขียว), alert = ผิดพลาด (แดง) --}}
            <div class="app-cmp-notifications">
                @session('status')
                    <div role="status">{{ $value }}</div>
                @endsession

                @error('alert')
                    <div role="alert">{{ $message }}</div>
                @enderror
            </div>

            {{-- for other header parts: filtering, content navigation --}}
            @yield('header')
        </header>

        @yield('content')
    </main>

    {{-- ดู bookmarks ตอน debug (Lab 11): เอา comment ออก --}}
    {{-- @dump(session()->get('bookmarks')) --}}

    <footer id="app-main-footer">
        &#xA9; {{ date('Y') }} ระบบสมัครเข้าร่วมกิจกรรม — Term Project 954348
    </footer>
</body>
</html>
