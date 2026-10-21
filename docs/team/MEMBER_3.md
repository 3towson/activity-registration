# คนที่ 3 — โครงโปรเจกต์, บัญชีผู้ใช้ และ Organizers

ชื่อสมาชิก: __________
Branch: `member-3/organizers-accounts`

รับผิดชอบการตั้งโปรเจกต์ ระบบ login / สมัครสมาชิก / role, layout หลัก, ผู้จัดกิจกรรม และหน้าฝั่งผู้ใช้ (หน้าแรก, กิจกรรมของฉัน) อ่าน [ข้อตกลงร่วม](README.md) ก่อนเริ่ม

| เทียบกับเว็บในคาบ (`my-db`) | งานนี้ |
|---|---|
| `LoginController` + `logins/form` (Lab 12) | เหมือนเดิม + เพิ่ม `RegisterController` (สมัครสมาชิก) |
| เพิ่ม `role` ใน users + บัญชี admin/user (Lab 12–13) | สร้าง `role` ตั้งแต่ migration แรก (โปรเจกต์ใหม่ ไม่ต้องทำ 3 ขั้น) |
| สร้าง User: `new User($data)` แล้วตั้ง `email`, `role` เอง (Lab 13) | ใช้วิธีเดียวกันในหน้าสมัครสมาชิก · role = `USER` เสมอ |
| Shop + `ShopController` (CRUD ธรรมดา มี code, name) | Organizer + `OrganizerController` |
| `layouts/main.blade.php` (เมนู, ชื่อ user, Logout, `status`/`alert`) | เหมือนเดิม + `@guest` แสดงปุ่มเข้าสู่ระบบ / สมัครสมาชิก |
| ทุกหน้าต้อง login | **มีหน้า public**: หน้าแรก, สมัครสมาชิก (+ หน้ากิจกรรมของคนที่ 1) |

---

## งานในฐานร่วม (ขั้นที่ 1) — คนที่ 3 เริ่มก่อน

1. `laravel new` (Laravel 13) → `git init` → push ขึ้น GitHub → เพิ่มเพื่อนเป็น collaborator
2. `.env.example` ตั้ง `DB_CONNECTION=mysql`, `DB_DATABASE=activity_db`, `APP_LOCALE=th`, timezone `Asia/Bangkok` (**ห้าม commit `.env`**)
3. `composer require nyholm/psr7 symfony/psr-http-message-bridge` (รับ request แบบ `ServerRequestInterface`)
4. migration users เพิ่ม `$table->string('role', 32);` · `UserFactory` ใส่ `'role' => 'USER'` (บั๊กใน `my-db`: ไม่ใส่แล้ว `db:seed` พัง)
5. `app/Models/User.php` — `#[Fillable(['name', 'password'])]` (ไม่ใส่ email, role) · `isAdministrator()`, `isMember()` · `activities()` belongsToMany Activity
6. migration `organizers` (`code` unique, `name`, `contact`) + `app/Models/Organizer.php` (`activities()` hasMany)
7. `UserSeeder` (`admin@activity.test` + `user@activity.test`, `user2`–`user5@activity.test` รหัส `1234` ผ่าน `Hash::make`) · `OrganizerSeeder` (OG001–OG004, **OG004 ไม่มีกิจกรรม**) · `DatabaseSeeder` เรียกตามลำดับ User → Category → Organizer → Activity → Registration
8. `LoginController` — `Validator` → `Auth::attempt()` → `session()->regenerate()` → `redirect()->intended()` · logout: `Auth::logout()` + `invalidate()` + `regenerateToken()`
9. `layouts/main.blade.php` — เมนูตามสิทธิ์ (`@auth`, `@guest`, `@can('list', Category::class)`), ชื่อผู้ใช้ + role, ปุ่ม Logout (POST + `@csrf`), กล่อง `@session('status')` / `@error('alert')`
10. `routes/web.php` — ครอบทุก route ด้วย `cache.headers:no_store;no_cache;must_revalidate;max_age=0` แล้ว `require` ไฟล์ route ของทุกคน

## งานหลัก (ขั้นที่ 2)

1. **Organizer CRUD** — list / view / create / update / delete + หน้า "กิจกรรมของผู้จัดนี้" โครงเดียวกับ Category ของคนที่ 2 (ดู: ทุกคนที่ login · สร้าง/แก้/ลบ: ADMIN · ลบได้เฉพาะผู้จัดที่ไม่มีกิจกรรม) · ไม่ใช้ `Validator` · ปุ่ม Back/Cancel ใช้ bookmarks
2. **สมัครสมาชิก (public)** — เขียนแบบ `UserController::create` ใน Lab 13 (ไม่ใช้ `Validator`)
   - ฟอร์ม: อีเมล (`type="email"`), รหัสผ่าน, ชื่อ — ทุกช่อง `required` · ไม่มีช่องเลือก role
   - ใน `try`: `new User($data)` แล้ว `$user->email = $data['email']`, `$user->role = 'USER'` (**ต่างจากคาบ: ล็อกเป็น USER เสมอ ห้ามรับ role จากฟอร์ม** แม้มีคนแอบส่ง `role=ADMIN` มา) แล้ว `save()`
   - อีเมลซ้ำ → `QueryException` → กล่องแดง `Duplicate entry ...` · คืนค่าฟอร์มด้วย `withInput()` แต่ **ไม่คืนรหัสผ่าน**
   - `Auth::login($user)` + `session()->regenerate()` แล้วพาไปหน้ารายการกิจกรรม
   - หน้า login / สมัครสมาชิกใช้ middleware `guest` (login แล้วเข้าไม่ได้)
3. **หน้าแรก (public)** — กิจกรรมที่ใกล้จะจัด 6 รายการ (การ์ด), ตัวเลขสรุป, ปุ่มหมวดหมู่ที่ลิงก์ไป `activities.list?category=...`
4. **กิจกรรมของฉัน (USER)** — `$user->activities()` แยกแท็บ "กำลังจะมาถึง" / "ที่ผ่านมาแล้ว" · มีลิงก์บัตร PDF · ADMIN เข้าแล้วได้ 403
5. (ไม่บังคับ) **ข้อความ validation ของ login เป็นภาษาไทย** — `lang/th/validation.php` (`required`, `email`) — CRUD ไม่ได้ใช้ `Validator` แล้ว
6. ดูแลการรวม `routes/web.php` และ layout เมื่อเพื่อนขอเพิ่มเมนู

## ไฟล์ที่รับผิดชอบ

| ไฟล์ | งาน |
|---|---|
| `.env.example`, `composer.json` (ส่วน PSR-7) | ตั้งค่าโปรเจกต์ |
| `database/migrations/0001_*_create_users_table.php` | `role` |
| `database/migrations/*_create_organizers_table.php` | schema |
| `database/factories/UserFactory.php` | ใส่ `role` |
| `database/seeders/DatabaseSeeder.php`, `UserSeeder.php`, `OrganizerSeeder.php` | ข้อมูลตัวอย่าง + ลำดับ |
| `app/Models/User.php`, `app/Models/Organizer.php` | role helper + relationships |
| `app/Http/Controllers/LoginController.php`, `RegisterController.php` | login / logout / สมัครสมาชิก |
| `app/Http/Controllers/OrganizerController.php`, `app/Policies/OrganizerPolicy.php` | Organizer CRUD |
| `app/Http/Controllers/HomeController.php`, `MyActivityController.php` | หน้าแรก, กิจกรรมของฉัน |
| `resources/views/layouts/main.blade.php` | layout หลัก |
| `resources/views/logins/*`, `registers/*`, `organizers/*`, `home.blade.php`, `my-activities/*` | หน้าของตัวเอง |
| `routes/web.php`, `routes/auth.php`, `routes/organizers.php`, `routes/my-activities.php` | routes |
| `lang/th/*` (ไม่บังคับ) | ข้อความภาษาไทยของ login / pagination |
| `README.md` (ราก repo) | วิธีติดตั้ง + บัญชีทดสอบ |

## Routes

| Method / URL | Name | ทำอะไร | สิทธิ์ |
|---|---|---|---|
| GET `/` | `home` | หน้าแรก | Public |
| GET `/auth/login` | `login` | ฟอร์ม login (ต้องชื่อ `login` เพราะ middleware `auth` redirect มาที่ชื่อนี้) | Guest |
| POST `/auth/login` | `authentication` | ตรวจ login | Guest |
| GET `/auth/register` | `register-form` | ฟอร์มสมัครสมาชิก | Guest (public) |
| POST `/auth/register` | `register` | สร้างบัญชี role USER | Guest |
| POST `/auth/logout` | `logout` | ออกจากระบบ | login แล้ว |
| GET `/my-activities` | `my-activities.list` | กิจกรรมของฉัน | USER |
| `/organizers/...` | `organizers.index`, `.list`, `.view`, `.create-form`, `.create`, `.update-form`, `.update`, `.delete`, `.view-activities` | เหมือน `/categories` (`GET /organizers/list` + `ANY /organizers` → redirect) | ดู: ทุกคนที่ login · แก้: ADMIN |

## การตรวจข้อมูล

| ฟอร์ม | ตรวจอย่างไร |
|---|---|
| Login | `Validator` (เหมือนในคาบ — ที่เดียวที่ใช้) `email`, `password` required · ผิด → "อีเมลหรือรหัสผ่านไม่ถูกต้อง" (คืนค่า email ได้ **ไม่คืน password**) |
| สมัครสมาชิก | แบบ `UserController::create`: HTML `required` + `type="email"` · อีเมลซ้ำ → error จากฐานข้อมูล (unique) ผ่าน `try/catch` |
| Organizer | แบบ `CategoryController`: HTML `required` · code ซ้ำ → error จากฐานข้อมูล ผ่าน `try/catch` |

## รับ / ส่งต่อ

- **ส่งให้ทุกคน (ต้นขั้นที่ 1):** repo + วิธีติดตั้ง, `User::isAdministrator()` / `isMember()`, บัญชีทดสอบ, layout (`@extends('layouts.main', ['title' => ...])`, `@section('header')`, `@section('content')`), วิธีส่งข้อความ (`status` / `alert`)
- **ส่งให้คนที่ 1:** `Organizer` model + `User::activities()`
- **รับจากคนที่ 2:** `SearchableController`, CSS class, โครง CategoryController / views เพื่อทำ Organizer
- **รับจากคนที่ 1:** `ActivityController::filter()` (ยืมผ่าน `\resolve()`) สำหรับ `organizers.view-activities` (ตารางเขียนเองในไฟล์ แบบ `view-products` ในคาบ), `Activity::isOpen()` / `isFull()` สำหรับการ์ดหน้าแรก

## Checklist ว่างานเสร็จ

- [ ] เพื่อนทั้ง 2 คน clone แล้วติดตั้งตาม README ได้บนเครื่องตัวเอง (MySQL)
- [ ] `admin@activity.test` และ `user@activity.test` login / logout ได้ · หลัง logout กด Back แล้วไม่เห็นหน้าเดิม (cache.headers)
- [ ] รหัสผิด → ข้อความ error, ช่อง email ยังมีค่า, ช่อง password ว่าง
- [ ] เข้าหน้าที่ต้อง login ตอนยังไม่ login → ไป login → login แล้วกลับมาหน้าเดิม (`intended`)
- [ ] สมัครสมาชิกได้ แล้ว login ให้อัตโนมัติ · ในฐานข้อมูล role = `USER` แม้ส่ง `role=ADMIN` มาเอง
- [ ] สมัครด้วยอีเมลซ้ำ → กล่องแดง `Duplicate entry ...` · ช่องชื่อ/อีเมลยังมีค่า ช่องรหัสผ่านว่าง
- [ ] login แล้วเปิด `/auth/login` หรือ `/auth/register` → ถูกพาไปหน้าแรก
- [ ] Guest เปิดหน้าแรกได้ · เมนูแสดง "เข้าสู่ระบบ / สมัครสมาชิก" · USER เห็น "กิจกรรมของฉัน", "หมวดหมู่", "ผู้จัด" · ADMIN เห็น "หมวดหมู่ / ผู้จัด" (ไม่มี "กิจกรรมของฉัน")
- [ ] ADMIN สร้าง / ดู / แก้ / ลบ Organizer ได้ · OG001 ลบไม่ได้ · OG004 ลบได้
- [ ] USER เห็นเมนูผู้จัด ดูได้แต่ไม่มีปุ่มแก้ · พิมพ์ `/organizers/create` เองได้ 403
- [ ] USER เห็นกิจกรรมของฉันถูกต้องทั้ง 2 แท็บ · ADMIN เปิด `/my-activities` ได้ 403

## สิ่งที่ต้องอธิบายในรายงานและ demo

- ขั้นตอนสร้างโปรเจกต์, migration / seeder, ทำไม `migrate:fresh --seed` ทำให้ทุกเครื่องได้ข้อมูลเหมือนกัน
- **Authentication** (คุณคือใคร: login, session) กับ **Authorization** (ทำอะไรได้: role, Policy) ต่างกันอย่างไร
- `session()->regenerate()` ตอน login และ `invalidate()` + `regenerateToken()` ตอน logout ป้องกันอะไร
- password ถูก hash อย่างไร (`'password' => 'hashed'` ใน casts)
- ทำไม `email` / `role` ไม่อยู่ใน `#[Fillable]` และทำไมหน้าสมัครสมาชิกตั้ง role ในโค้ด (mass assignment)
- protected routes (`auth`) กับ public routes ต่างกันอย่างไร · `guest` middleware ใช้ทำอะไร
- Organizer → Activity (hasMany) และ `$user->activities()` (belongsToMany) ในหน้ากิจกรรมของฉัน
- layout หลายชั้น (`layouts.main` → `organizers.main` → หน้าต่าง ๆ) และกล่องแจ้งเตือนที่เขียนครั้งเดียวใช้ได้ทุกหน้า

ในรายงานเขียน 4 หัวข้อ: **งานที่รับผิดชอบ / วิธีพัฒนา / ไฟล์สำคัญ / ผลการทดสอบ** และเป็นคนรวบรวมส่วน **วิธีติดตั้ง (How to develop)** ของทั้งกลุ่ม
