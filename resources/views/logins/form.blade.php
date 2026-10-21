@extends('layouts.main', [
    'title' => 'เข้าสู่ระบบ',
])

@section('content')
    <form action="{{ route('authentication') }}" method="post" class="app-cmp-data-form" style="max-width: 26rem;">
        @csrf

        <label>
            อีเมล
            <input type="email" name="email" value="{{ old('email') }}" required autofocus />
            @error('email') <small class="app-cmp-field-error">{{ $message }}</small> @enderror
        </label>

        <label>
            รหัสผ่าน
            <input type="password" name="password" required />
            @error('password') <small class="app-cmp-field-error">{{ $message }}</small> @enderror
        </label>

        <div class="app-cmp-notifications">
            @error('credentials')
                <div role="alert">{{ $message }}</div>
            @enderror
        </div>

        <div class="app-cmp-form-actions">
            <button type="submit" class="app-cl-button app-cl-filled app-cl-primary">เข้าสู่ระบบ</button>
        </div>

        {{-- หน้าสมัครสมาชิกเป็นงานของคนที่ 3 ในขั้นที่ 2 — ลิงก์จะโผล่เองเมื่อมี route ชื่อ register-form --}}
        @if (Route::has('register-form'))
            <p class="app-cmp-hint">ยังไม่มีบัญชี? <a href="{{ route('register-form') }}">สมัครสมาชิก</a></p>
        @endif
    </form>
@endsection
