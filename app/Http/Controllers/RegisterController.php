<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Psr\Http\Message\ServerRequestInterface;

class RegisterController extends Controller
{
    function showForm(): View
    {
        return view('registers.form');
    }

    function register(ServerRequestInterface $request): RedirectResponse
    {
        $data = $request->getParsedBody();

        try {
            // email และ role ไม่อยู่ใน Fillable → new User($data) รับแค่ name, password
            $user = new User($data);
            $user->email = $data['email'] ?? null;

            // ล็อกเป็น USER เสมอ ไม่อ่าน role จากฟอร์ม แม้มีคนแอบส่ง role=ADMIN มา
            $user->role = User::ROLE_USER;
            $user->save();

            Auth::login($user);

            // สร้าง session ID ใหม่ กัน session fixation
            session()->regenerate();

            return redirect()
                ->route('activities.list')
                ->with('status', 'สมัครสมาชิกสำเร็จ ยินดีต้อนรับ ' . $user->name);
        } catch (QueryException $excp) {
            // คืนค่าเฉพาะชื่อและอีเมลให้ฟอร์ม — ไม่เก็บ password ไว้ใน session
            return redirect()->back()
                ->withInput([
                    'name' => $data['name'] ?? '',
                    'email' => $data['email'] ?? '',
                ])
                ->withErrors([
                    'alert' => $excp->errorInfo[2],
                ]);
        }
    }
}
