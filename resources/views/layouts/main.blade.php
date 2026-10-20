<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ $title }} — ระบบสมัครเข้าร่วมกิจกรรม</title>

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Thai:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('css/app.css') }}" />
</head>
<body>
    <header id="app-main-header">
        <div class="app-cmp-header-bar">
            <a href="{{ route('home') }}" class="app-cmp-brand">
                <span class="app-cmp-brand-mark" aria-hidden="true">✦</span>
                สมัครกิจกรรม
            </a>

            {{--
                เมนูหลัก
                - Route::has(...) → เมนูของเพื่อนจะโผล่เองเมื่อเพื่อนเพิ่ม route นั้นแล้ว (แยกกันทำได้ไม่พัง)
                - @can(...) → แสดงตามสิทธิ์ (Policy ของเจ้าของ model)
            --}}
            <nav class="app-cmp-main-nav" aria-label="เมนูหลัก">
                <ul>
                    <li><a href="{{ route('home') }}" @class(['active' => Route::is('home')])>หน้าแรก</a></li>

                    @if (Route::has('activities.list'))
                        <li><a href="{{ route('activities.list') }}" @class(['active' => Route::is('activities.*')])>กิจกรรม</a></li>
                    @endif

                    @auth
                        @if (\Auth::user()->isMember() && Route::has('my-activities.list'))
                            <li><a href="{{ route('my-activities.list') }}" @class(['active' => Route::is('my-activities.*')])>กิจกรรมของฉัน</a></li>
                        @endif
                    @endauth

                    @if (Route::has('categories.list'))
                        @can('list', \App\Models\Category::class)
                            <li><a href="{{ route('categories.list') }}" @class(['active' => Route::is('categories.*')])>หมวดหมู่</a></li>
                        @endcan
                    @endif

                    @if (Route::has('organizers.list'))
                        @can('list', \App\Models\Organizer::class)
                            <li><a href="{{ route('organizers.list') }}" @class(['active' => Route::is('organizers.*')])>ผู้จัด</a></li>
                        @endcan
                    @endif
                </ul>
            </nav>

            {{-- แผงผู้ใช้: login แล้วแสดงชื่อ + role + Logout / ยังไม่ login แสดงปุ่มเข้าสู่ระบบ --}}
            <div class="app-cmp-user-panel">
                @auth
                    <form action="{{ route('logout') }}" method="post" id="app-form-logout">
                        @csrf
                    </form>
                    <span class="app-cmp-user-name">
                        {{ \Auth::user()->name }}
                        <span @class([
                            'app-cmp-role',
                            'app-cl-admin' => \Auth::user()->isAdministrator(),
                        ])>{{ \Auth::user()->isAdministrator() ? 'ผู้ดูแล' : 'สมาชิก' }}</span>
                    </span>
                    <button type="submit" form="app-form-logout" class="app-cl-ghost">ออกจากระบบ</button>
                @endauth

                @guest
                    <a href="{{ route('login') }}" class="app-cl-ghost">เข้าสู่ระบบ</a>
                    @if (Route::has('register-form'))
                        <a href="{{ route('register-form') }}" class="app-cl-button">สมัครสมาชิก</a>
                    @endif
                @endguest
            </div>
        </div>
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

            @yield('header')
        </header>

        @yield('content')
    </main>

    <footer id="app-main-footer">
        &#xA9; {{ date('Y') }} ระบบสมัครเข้าร่วมกิจกรรม — Term Project 954348
    </footer>
</body>
</html>
