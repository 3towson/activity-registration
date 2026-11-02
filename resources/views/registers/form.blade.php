@extends('layouts.main', [
    'title' => 'สมัครสมาชิก',
])

@section('content')
    <form action="{{ route('register') }}" method="post" class="app-cmp-data-form" style="max-width: 26rem;">
        @csrf

        <label>
            อีเมล
            <input type="email" name="email" value="{{ old('email') }}" required autofocus />
        </label>

        <label>
            รหัสผ่าน
            <input type="password" name="password" required />
        </label>

        <label>
            ชื่อ
            <input type="text" name="name" value="{{ old('name') }}" required />
        </label>

        <div class="app-cmp-form-actions">
            <button type="submit" class="app-cl-button app-cl-filled app-cl-primary">สมัครสมาชิก</button>
        </div>

        <p class="app-cmp-hint">มีบัญชีแล้ว? <a href="{{ route('login') }}">เข้าสู่ระบบ</a></p>
    </form>
@endsection
