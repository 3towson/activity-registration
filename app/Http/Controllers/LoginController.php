<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Psr\Http\Message\ServerRequestInterface;

class LoginController extends Controller
{
    function showForm(): View
    {
        return view('logins.form');
    }

    function authenticate(ServerRequestInterface $request): RedirectResponse
    {
        $validator = Validator::make(
            $request->getParsedBody(),
            [
                'email' => ['required', 'email'],
                'password' => ['required'],
            ],
        );

        if ($validator->passes()) {
            if (Auth::attempt($validator->safe()->only(['email', 'password']))) {
                // สร้าง session ID ใหม่ กัน session fixation
                session()->regenerate();

                // กลับไปหน้าที่ตั้งใจจะเข้าก่อนถูกพามา login
                return redirect()
                    ->intended(route('home'))
                    ->with('status', 'เข้าสู่ระบบสำเร็จ ยินดีต้อนรับ ' . Auth::user()->name);
            }

            $validator->errors()->add(
                'credentials',
                'อีเมลหรือรหัสผ่านไม่ถูกต้อง',
            );
        }

        // คืนค่าเฉพาะ email ให้ฟอร์ม — ไม่เก็บ password ไว้ใน session
        return redirect()->back()
            ->withInput(['email' => $request->getParsedBody()['email'] ?? ''])
            ->withErrors($validator);
    }

    function logout(): RedirectResponse
    {
        Auth::logout();
        session()->invalidate();

        // สร้าง CSRF token ใหม่
        session()->regenerateToken();

        return redirect()->route('login')->with('status', 'ออกจากระบบแล้ว');
    }
}
