<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * บัญชีทดสอบ: รหัสผ่านทุกบัญชีคือ 1234
     */
    public function run(): void
    {
        $now = now();
        $password = Hash::make('1234');

        $users = [
            ['Administrator', 'admin@activity.test', 'ADMIN'],
            ['สมชาย ใจดี', 'user@activity.test', 'USER'],
            ['สมหญิง รักเรียน', 'user2@activity.test', 'USER'],
            ['มานะ ขยันยิ่ง', 'user3@activity.test', 'USER'],
            ['ปิติ ยินดี', 'user4@activity.test', 'USER'],
            ['ชูใจ ใฝ่ดี', 'user5@activity.test', 'USER'],
        ];

        DB::table('users')->insert(array_map(static fn(array $u): array => [
            'name' => $u[0],
            'email' => $u[1],
            'role' => $u[2],
            'email_verified_at' => $now,
            'password' => $password,
            'remember_token' => '',
            'created_at' => $now,
            'updated_at' => $now,
        ], $users));
    }
}
