# ระบบสมัครเข้าร่วมกิจกรรม (Activity Registration)

Term Project วิชา 954348 — ภาคเรียน 1/2569 · Laravel 13 + Blade + MySQL

> **สถานะ: ฐานร่วม (ขั้นที่ 1) เสร็จแล้ว** — ทุกคนติดตั้งตามขั้นที่ 0 แล้วตรวจตาม [ฐานร่วมเสร็จเมื่อ](#ฐานร่วมเสร็จเมื่อ) จากนั้นแยกกันทำในขั้นที่ 2
> แผนเต็มอยู่ใน [`docs/PROJECT_PLAN.md`](docs/PROJECT_PLAN.md) · งานของแต่ละคนอยู่ใน [`docs/team/`](docs/team/README.md)

---

## ขั้นที่ 0 — เตรียมเครื่อง (ทุกคน ทำที่บ้าน)

### 0.1 PHP 8.3 ขึ้นไป + Composer

Laravel 13 ต้องใช้ **PHP 8.3 ขึ้นไป** — ⚠️ XAMPP บางเวอร์ชันมากับ PHP 8.2 ซึ่ง **ใช้ไม่ได้** ให้เช็ก `php -v` ก่อน

ถ้ายังไม่มี PHP 8.3+ ใช้ตัวติดตั้งทางการของ Laravel (ได้ PHP 8.5 + Composer + Laravel installer):

```powershell
# Windows PowerShell (เปิดแบบ Administrator)
Set-ExecutionPolicy Bypass -Scope Process -Force; [System.Net.ServicePointManager]::SecurityProtocol = [System.Net.ServicePointManager]::SecurityProtocol -bor 3072; iex ((New-Object System.Net.WebClient).DownloadString('https://php.new/install/windows/8.5'))
```

```bash
# macOS
/bin/bash -c "$(curl -fsSL https://php.new/install/mac/8.5)"
```

ปิดแล้วเปิด terminal ใหม่ แล้วเช็ก:

```bash
php -v          # ต้องได้ 8.3 ขึ้นไป
composer -V
php -m          # ต้องมี pdo_mysql, mbstring, openssl, fileinfo
```

**ทั้ง 3 คนควรใช้ PHP เวอร์ชันเดียวกัน** — แจ้งผล `php -v` ในกลุ่ม

### 0.2 MySQL (ใช้ XAMPP เฉพาะส่วน MySQL ได้)

1. ติดตั้ง [XAMPP](https://www.apachefriends.org/) → เปิด XAMPP Control Panel → กด **Start** ที่ **MySQL** (ไม่ต้องเปิด Apache เพราะเราใช้ `php artisan serve`)
2. เปิด phpMyAdmin (`http://localhost/phpmyadmin` — ถ้าเข้าไม่ได้ให้ Start Apache ด้วย) → แท็บ SQL → รัน:

   ```sql
   CREATE DATABASE activity_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

   ต้องเป็น `utf8mb4` ไม่งั้นภาษาไทยจะกลายเป็น `???`

### 0.3 Git

- ติดตั้ง Git และตั้งชื่อ/อีเมลให้ตรงกับบัญชี GitHub ของตัวเอง (commit จะเป็นหลักฐานงานของแต่ละคน)

  ```bash
  git config --global user.name "ชื่อ GitHub"
  git config --global user.email "อีเมลที่ใช้กับ GitHub"
  ```

- ให้เจ้าของ repo เพิ่มเป็น collaborator (Settings → Collaborators)

---

## ติดตั้งโปรเจกต์

```bash
git clone <URL ของ repo กลุ่ม>
cd <ชื่อโฟลเดอร์>
composer install
cp .env.example .env            # Windows CMD: copy .env.example .env
php artisan key:generate
```

แก้ `.env` ให้ตรงกับ MySQL ในเครื่อง (XAMPP ค่าเริ่มต้นคือ `root` ไม่มีรหัสผ่าน — ตรงกับ `.env.example` อยู่แล้ว):

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=activity_db
DB_USERNAME=root
DB_PASSWORD=
```

สร้างตาราง + ข้อมูลตัวอย่าง แล้วเปิดเว็บ:

```bash
php artisan migrate:fresh --seed
php artisan serve               # เปิด http://127.0.0.1:8000
```

> `migrate:fresh` ลบตารางทั้งหมดแล้วสร้างใหม่ — ใช้ได้เพราะข้อมูลทั้งหมดมาจาก seeder ทุกเครื่องจึงได้ข้อมูลเหมือนกัน
> **ห้าม commit `.env`** (อยู่ใน `.gitignore` แล้ว)

### บัญชีทดสอบ (รหัสผ่าน `1234`)

| Email | Role |
|---|---|
| `admin@activity.test` | ADMIN |
| `user@activity.test` | USER (สมัครไว้แล้ว AC001, AC006, AC008) |
| `user2@activity.test` – `user5@activity.test` | USER |

### แก้ปัญหาที่พบบ่อย

| อาการ | วิธีแก้ |
|---|---|
| `could not find driver` | เปิด `extension=pdo_mysql` ใน `php.ini` (ดูตำแหน่งไฟล์ด้วย `php --ini`) |
| `SQLSTATE[HY000] [2002] Connection refused` | ยังไม่ได้ Start MySQL ใน XAMPP |
| `Access denied for user 'root'` | แก้ `DB_USERNAME` / `DB_PASSWORD` ใน `.env` แล้วรัน `php artisan config:clear` |
| `Unknown database 'activity_db'` | ยังไม่ได้สร้างฐานข้อมูล (ข้อ 0.2) |
| ภาษาไทยเป็น `???` ในฐานข้อมูล | ฐานข้อมูลไม่ใช่ `utf8mb4` → ลบแล้วสร้างใหม่ตามข้อ 0.2 แล้ว `migrate:fresh --seed` |
| `Your requirements could not be resolved ... php ^8.3` | PHP ต่ำกว่า 8.3 → ทำข้อ 0.1 |
| `No application encryption key` | `php artisan key:generate` |

---

## ฐานร่วม (ขั้นที่ 1) — มีอะไรแล้ว

แต่ละ commit ขึ้นต้นด้วยชื่อคนที่รับผิดชอบส่วนนั้น (`git log --oneline`)

| Commit | คน | ไฟล์ |
|---|---|---|
| สร้างโปรเจกต์ + MySQL + PSR-7 | คนที่ 3 | `composer.json`, `.env.example`, `config/app.php` (timezone) |
| role ใน users + User helper | คนที่ 3 | `database/migrations/0001_*_create_users_table.php`, `app/Models/User.php`, `database/factories/UserFactory.php` |
| Category | คนที่ 2 | `*_create_categories_table.php`, `app/Models/Category.php`, `CategorySeeder.php` |
| Organizer + บัญชีทดสอบ | คนที่ 3 | `*_create_organizers_table.php`, `app/Models/Organizer.php`, `OrganizerSeeder.php`, `UserSeeder.php` |
| Activity + `activity_user` | คนที่ 1 | `*_create_activities_table.php`, `*_create_activity_user_table.php`, `app/Models/Activity.php`, `ActivitySeeder.php`, `RegistrationSeeder.php` |
| SearchableController | คนที่ 2 | `app/Http/Controllers/SearchableController.php`, `app/Providers/AppServiceProvider.php` |
| Login/Logout + layout + routes | คนที่ 3 | `LoginController.php`, `views/layouts/main.blade.php`, `views/logins/form.blade.php`, `routes/web.php`, `routes/auth.php`, route ว่างของทุกคน, หน้าแรกชั่วคราว |
| CSS ตั้งต้น | คนที่ 2 | `public/css/app.css` |

```
Category 1 ── N Activity N ── 1 Organizer
                   N
                   │  activity_user (ตารางกลาง = การสมัคร)
                   N
                  User (role: ADMIN / USER)
```

**ทุกคนต้องอ่านและอธิบายฐานร่วมได้ทั้งหมด** ไม่ใช่แค่ส่วนของตัวเอง — ถ้าเพื่อนพิมพ์ส่วนไหน ให้ดูโค้ดด้วยกันจนเข้าใจทุกบรรทัด

---

## ฐานร่วมเสร็จเมื่อ

ตรวจในเครื่อง **ของทุกคน** แล้วติ๊กในกลุ่ม:

- [ ] `composer install` → `php artisan key:generate` → `php artisan migrate:fresh --seed` ผ่านไม่มี error
- [ ] เปิด `http://127.0.0.1:8000` เห็นหน้า **"ตรวจฐานร่วม"** และตารางจำนวนข้อมูลขึ้น ✓ ครบ 5 แถว (users 6, categories 4, organizers 4, activities 12, activity_user 15)
- [ ] ตาราง "กิจกรรมตัวอย่าง" แสดงชื่อหมวด ชื่อผู้จัด และจำนวนผู้สมัครครบทุกแถว (relationships ใช้ได้) · ภาษาไทยไม่เป็น `???`
- [ ] login `admin@activity.test` / `1234` ได้ → มุมขวาบนขึ้นชื่อ + ป้าย "ผู้ดูแล" · หน้าแรกบอก role `ADMIN`
- [ ] logout แล้ว login `user@activity.test` / `1234` ได้ → ป้าย "สมาชิก" · "สมัครไว้ 3 กิจกรรม"
- [ ] ใส่รหัสผิด → ข้อความแดง "อีเมลหรือรหัสผ่านไม่ถูกต้อง" และช่องอีเมลยังมีค่าเดิม
- [ ] login แล้วเปิด `/auth/login` → ถูกพากลับหน้าแรก
- [ ] phpMyAdmin → `activity_db` มีตาราง `users, categories, organizers, activities, activity_user`

ครบแล้ว → ไปขั้นที่ 2

---

## ขั้นที่ 2 — แยกกันทำ

```bash
git checkout main && git pull
git checkout -b member-1/activities          # คนที่ 1
git checkout -b member-2/categories-css-pdf  # คนที่ 2
git checkout -b member-3/organizers-accounts # คนที่ 3
```

| คน | เริ่มที่ไฟล์ | งานแรกที่แนะนำ |
|---|---|---|
| คนที่ 1 | `routes/activities.php`, `ActivityController`, `ActivityPolicy` | หน้า public รายการ + รายละเอียดกิจกรรม (เมนู "กิจกรรม" จะโผล่เองเมื่อมี route `activities.list`) |
| คนที่ 2 | `routes/categories.php`, `CategoryController`, `CategoryPolicy` | Category list/view → create/update/delete · แล้วทดสอบ dompdf ฟอนต์ไทยให้ผ่านก่อน |
| คนที่ 3 | `routes/organizers.php`, `routes/auth.php`, `OrganizerController` | หน้าสมัครสมาชิก (ลิงก์ในหน้า login/เมนูจะโผล่เอง) → Organizer CRUD → หน้าแรกจริง |

รายละเอียดทั้งหมด (routes, validation, checklist, สิ่งที่ต้องอธิบาย): [`docs/team/MEMBER_1.md`](docs/team/MEMBER_1.md) · [`MEMBER_2.md`](docs/team/MEMBER_2.md) · [`MEMBER_3.md`](docs/team/MEMBER_3.md)

**กติกา**
- แตะเฉพาะไฟล์ของตัวเอง · ไฟล์ร่วม (`web.php`, layout, `User.php`, `app.css`, migrations) ต้องบอกเจ้าของก่อนแก้
- เพิ่ม/แก้คอลัมน์ → บอกในกลุ่ม แล้วทุกคน `migrate:fresh --seed`
- merge เข้า `main` ทีละส่วนเมื่อเสร็จ (เปิด Pull Request ให้เพื่อนดู) แล้วทุกคน `git pull` + `migrate:fresh --seed`

---

## โครงสร้างโฟลเดอร์

```
app/
  Http/Controllers/   SearchableController, LoginController, HomeController (+ ของแต่ละคนในขั้นที่ 2)
  Models/             User, Category, Organizer, Activity
  Providers/          AppServiceProvider (pagination)
database/
  migrations/         users(+role), categories, organizers, activities, activity_user
  seeders/            User → Category → Organizer → Activity → Registration
resources/views/
  layouts/main.blade.php   layout หลัก (เมนู + กล่องแจ้งเตือน)
  logins/form.blade.php
  home.blade.php           หน้าตรวจฐานร่วม (ชั่วคราว)
routes/
  web.php              รวมทุกไฟล์ด้านล่าง
  auth.php             คนที่ 3
  activities.php       คนที่ 1
  categories.php       คนที่ 2
  tickets.php          คนที่ 2
  organizers.php       คนที่ 3
  my-activities.php    คนที่ 3
public/css/app.css     คนที่ 2
docs/                  แผน กฎ เอกสารแบ่งงาน และเนื้อหาจากคาบ
```
