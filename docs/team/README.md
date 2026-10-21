# แบ่งงาน 3 คน — ระบบสมัครเข้าร่วมกิจกรรม

เอกสารนี้ขยายข้อ 12–13 ของ [แผนโปรเจกต์](../PROJECT_PLAN.md) (ฉบับที่กลุ่มตกลงเมื่อ 14 ต.ค. 2569) ใช้แบบ **ทำฐานร่วมด้วยกันก่อน → แยกกันทำ → รวมงาน**
ถ้าขัดกับ [กฎโปรเจกต์](../PROJECT_RULES.md) ให้ยึดกฎและคำสั่งอาจารย์เป็นหลัก

> โครงสร้างและวิธีเขียนยึดตามเว็บในคาบ (`my-db`: Category – Product – Shop) ให้มากที่สุด เพื่อให้ทุกคนตอบอาจารย์ได้ด้วยการเทียบกับ Lab ที่เคยทำ
> ใช้ `my-db` เป็นแบบอย่างได้ แต่ต้อง **สร้างโปรเจกต์ใหม่และพิมพ์โค้ดเอง** ห้าม copy โฟลเดอร์ Lab มาแก้ชื่อ

## ใครทำอะไร

| สมาชิก | งานในฐานร่วม | งานหลัก (แยกกันทำ) | CRUD ของตัวเอง | เอกสาร |
|---|---|---|---|---|
| คนที่ 1 — ชื่อ: __________ | Activity + ตารางกลาง `activity_user` + seeders | Activity CRUD · สมัคร/ยกเลิก + กฎ · รายชื่อผู้สมัคร | Activity | [MEMBER_1](MEMBER_1.md) |
| คนที่ 2 — ชื่อ: __________ | Category + `SearchableController` + seeder | Category CRUD · CSS ทั้งเว็บ · บัตรเข้าร่วม PDF (dompdf) | Category | [MEMBER_2](MEMBER_2.md) |
| คนที่ 3 — ชื่อ: __________ | สร้างโปรเจกต์ · role · Login/Logout · layout · `web.php` · Organizer + seeders | Organizer CRUD · สมัครสมาชิก · หน้าแรก · กิจกรรมของฉัน | Organizer | [MEMBER_3](MEMBER_3.md) |

ทุกคนมี CRUD 1 model (R2) มี migration/model ของตัวเอง (R1) และมี Policy ของตัวเอง (R4) จึงอธิบายได้ทุกคน

---

## ขั้นที่ 0 — เตรียมเครื่อง (ทุกคน ทำที่บ้าน)

- [ ] PHP **8.3 ขึ้นไป** เปิด extension `pdo_mysql`, `mbstring`, `gd`, `dom` (ทุกเครื่องควรเวอร์ชันเดียวกัน → รัน `php -v` แล้วแจ้งในกลุ่ม)
- [ ] Composer (`composer -V`)
- [ ] MySQL / MariaDB เช่น XAMPP (⚠️ XAMPP บางเวอร์ชันมากับ PHP 8.2 ซึ่งใช้กับ Laravel 13 ไม่ได้ — ใช้ XAMPP เฉพาะ MySQL แล้วลง PHP แยก ดูวิธีใน [README หลัก](../../README.md#ขั้นที่-0--เตรียมเครื่อง-ทุกคน-ทำที่บ้าน))
- [ ] สร้างฐานข้อมูลใน phpMyAdmin:
  ```sql
  CREATE DATABASE activity_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
  ```
- [ ] Git + บัญชี GitHub ที่ถูกเพิ่มเป็น collaborator ของ repo กลุ่มแล้ว

**ไม่มีฐานข้อมูลกลาง** — ทุกคนใช้ MySQL ในเครื่องตัวเอง สิ่งที่แชร์ผ่าน Git คือ migrations + seeders พอ `git pull` แล้วรัน `php artisan migrate:fresh --seed` ทุกเครื่องจะได้ตารางและข้อมูลเหมือนกัน

---

## ขั้นที่ 1 — ฐานร่วม (นั่งทำพร้อมกัน ~ครึ่งวัน)

ทำบน branch `main` ตามลำดับนี้ ใครพิมพ์ส่วนไหนให้ commit เอง (เป็นหลักฐานงานของแต่ละคน)

| ลำดับ | ใคร | งาน |
|---|---|---|
| 1 | คนที่ 3 | `laravel new` · `git init` + push ขึ้น GitHub · `.env.example` (MySQL) · `composer require nyholm/psr7 symfony/psr-http-message-bridge` |
| 2 | คนที่ 3 | เพิ่มคอลัมน์ `role` (string 32, NOT NULL) ใน migration ของ users · `User.php` (`#[Fillable(['name','password'])]`, `isAdministrator()`, `isMember()`, `activities()`) · `UserFactory` ใส่ `role` |
| 3 | คนที่ 2 | migration + `Category.php` (`activities()`) · `CategorySeeder` (CT001–CT004) |
| 4 | คนที่ 3 | migration + `Organizer.php` (`activities()`) · `OrganizerSeeder` (OG001–OG004) · `UserSeeder` (admin + user1–5) |
| 5 | คนที่ 1 | migration `activities` (FK `restrictOnDelete`) + `activity_user` (FK `cascadeOnDelete`, PK คู่) · `Activity.php` (`category()`, `organizer()`, `participants()`) · `ActivitySeeder` + `RegistrationSeeder` |
| 6 | คนที่ 2 | `SearchableController` (term หลายคำ + `paginate` + `find($code)`) · ตั้ง pagination view ใน `AppServiceProvider` |
| 7 | คนที่ 3 | `LoginController` (login/logout) · `layouts/main.blade.php` (เมนู + กล่อง `status`/`alert`) · `logins/form.blade.php` · `routes/web.php` ที่ `require` ไฟล์ route ของแต่ละคน |
| 8 | คนที่ 2 | `public/css/app.css` ตั้งต้น (ตัวแปรสี, layout, ปุ่ม, ตาราง, ฟอร์ม, กล่องแจ้งเตือน) |

**ฐานร่วมเสร็จเมื่อ** (ตรวจในเครื่อง **ทุกคน**):
- [ ] `git pull` → `composer install` → `cp .env.example .env` → `php artisan key:generate` → `php artisan migrate:fresh --seed` ผ่าน
- [ ] ใน phpMyAdmin มีตาราง `users, categories, organizers, activities, activity_user` และมีข้อมูลตัวอย่าง (ภาษาไทยไม่เป็น `???`)
- [ ] login เป็น `admin@activity.test` / `user@activity.test` (รหัส `1234`) ได้ และ logout ได้
- [ ] ทุกหน้าใช้ layout และ CSS กลาง

### ข้อมูลตัวอย่างที่ seeder ต้องมี (ให้ทุกคนทดสอบได้โดยไม่ต้องรอกัน)

| ข้อมูล | ไว้ทดสอบอะไร |
|---|---|
| `admin@activity.test` (ADMIN), `user@activity.test` … `user5@activity.test` (USER) รหัส `1234` | 2 role |
| CT001–CT003 มีกิจกรรม · **CT004 ไม่มีกิจกรรม** | ลบหมวดได้/ไม่ได้ |
| OG001–OG003 มีกิจกรรม · **OG004 ไม่มีกิจกรรม** | ลบผู้จัดได้/ไม่ได้ |
| AC003 รับ 3 สมัครแล้ว 2 · **AC004 รับ 2 สมัครแล้ว 2** | ใกล้เต็ม / เต็มแล้ว |
| **AC008, AC009 วันที่ผ่านมาแล้ว** (คิดจาก `today()` ตอน seed) | สมัคร/ยกเลิกหลังวันจัดไม่ได้ |
| `user@activity.test` สมัครไว้แล้ว AC001, AC006, AC008 | บัตร PDF, กิจกรรมของฉัน, ยกเลิก |

---

## ขั้นที่ 2 — แยกกันทำ

แต่ละคนสร้าง branch จาก `main` แล้วแตะ **เฉพาะไฟล์ของตัวเอง** (ดูในเอกสารรายคน)

| คน | branch |
|---|---|
| คนที่ 1 | `member-1/activities` |
| คนที่ 2 | `member-2/categories-css-pdf` |
| คนที่ 3 | `member-3/organizers-accounts` |

```bash
git checkout main && git pull
git checkout -b member-1/activities      # ชื่อ branch ของตัวเอง
# ... เขียนโค้ด ...
git add -A && git commit -m "Activity CRUD: list/view"
git push -u origin member-1/activities
```

route แยกไฟล์ตามเจ้าของ แล้ว `routes/web.php` (ของคนที่ 3) `require` มารวม จึงไม่ชนกัน:

| ไฟล์ route | เจ้าของ |
|---|---|
| `routes/activities.php` | คนที่ 1 |
| `routes/categories.php`, `routes/tickets.php` | คนที่ 2 |
| `routes/organizers.php`, `routes/auth.php`, `routes/my-activities.php`, `routes/web.php` | คนที่ 3 |

---

## ขั้นที่ 3 — รวมงาน

- merge เข้า `main` **ทีละส่วนเมื่อเสร็จ** ไม่ต้องรอรวมทีเดียวตอนท้าย (เปิด Pull Request ให้อีกคนดูก่อน merge)
- หลัง merge ทุกครั้ง ทุกคน `git pull` + `php artisan migrate:fresh --seed` แล้วลองกดหน้าของคนอื่นดู
- ลำดับแนะนำ: Category/Organizer CRUD → Activity CRUD → สมัคร/ยกเลิก → สมัครสมาชิก/หน้าแรก/กิจกรรมของฉัน → บัตร PDF → ตกแต่ง CSS
- รวมครบแล้วทดสอบด้วยกันตาม [checklist ก่อนส่ง](#ตรวจร่วมกันก่อนส่ง) และซ้อม demo Guest → USER → ADMIN

---

## ข้อตกลงร่วม (ใช้ชื่อเดียวกัน ตอนรวมจะต่อติดกันเอง)

### ฐานข้อมูลและ Model

| Model / เจ้าของ | ฟิลด์ (นอกจาก id, timestamps) | Relationships |
|---|---|---|
| User / คนที่ 3 | name, email unique, password, **role** (`ADMIN` / `USER`) | `activities()` belongsToMany |
| Category / คนที่ 2 | code unique, name, description | `activities()` hasMany |
| Organizer / คนที่ 3 | code unique, name, contact | `activities()` hasMany |
| Activity / คนที่ 1 | code unique, name, category_id, organizer_id, date, location, capacity, description | `category()`, `organizer()` belongsTo · `participants()` belongsToMany User |
| `activity_user` (ตารางกลาง) / คนที่ 1 | activity_id, user_id, timestamps · PK คู่ | — |

- FK: Activity → Category/Organizer = `restrictOnDelete` · activity_user → Activity/User = `cascadeOnDelete`
- `#[Fillable]` **ไม่ใส่** FK, `email`, `role`
- ตั้ง FK ด้วย `associate()` · สมัคร/ยกเลิกด้วย `attach()` / `detach()`

### สิทธิ์

| การใช้งาน | Guest | USER | ADMIN |
|---|:---:|:---:|:---:|
| หน้าแรก, รายการกิจกรรม, รายละเอียดกิจกรรม | ✅ | ✅ | ✅ |
| สมัครสมาชิก | ✅ | – | – |
| สมัคร / ยกเลิกกิจกรรม | ไป login | ✅ | ❌ |
| กิจกรรมของฉัน, ดาวน์โหลดบัตร PDF | ไป login | ✅ (เฉพาะที่สมัคร) | ❌ |
| ดูหมวดหมู่ / ผู้จัด (รายการ, รายละเอียด, กิจกรรมในหมวด/ผู้จัด) | ไป login | ✅ (ไม่เห็นปุ่มแก้) | ✅ |
| CRUD กิจกรรม, สร้าง/แก้/ลบ หมวดหมู่ / ผู้จัด, รายชื่อผู้สมัคร | ไป login | ❌ 403 | ✅ |

- กันที่ server ทุกครั้ง: `Gate::authorize()` ใน controller (ไม่ใช้ `$this->authorize()`) + `@can` ซ่อนปุ่มใน view
- Policy ของหน้า public ใช้ `?User $user` ให้ Guest ผ่านได้
- User ที่ทำรายการเอามาจาก `Auth::user()` เสมอ **ห้ามรับ `user_id` หรือ `role` จากฟอร์ม**

### วิธีเขียนโค้ด (ตามคาบ)

| เรื่อง | ใช้แบบนี้ | อ้างอิง |
|---|---|---|
| รับ request | `ServerRequestInterface` → `getQueryParams()` / `getParsedBody()` | Week 07 |
| URL | ใช้ `code` เช่น `/activities/AC001` · route คงที่ (`/create`) อยู่ก่อน `/{code}` | Week 07 |
| หน้ารายการ | `GET /xxx/list` (`xxx.list`) + `ANY /xxx` → redirect (`xxx.index`) · เมนู/ปุ่ม Back ลิงก์ไป `xxx.index` · ⚠️ `ANY ''` ต้องประกาศ **ก่อน** `POST ''` (create) ไม่งั้น ANY ทับ POST | `web.php` ของ my-db |
| ค้นหา + แบ่งหน้า | `SearchableController` + `paginate(5)` + `withQueryString()->links()` · ฟอร์ม `<search><form class="app-cmp-search-form"><fieldset><legend>` | Week 08 |
| แก้ไข | `GET /{code}/update` (ฟอร์ม) + `POST /{code}` (บันทึก) | Week 08 |
| ลบ | `POST /{code}/delete` + ฟอร์มแยก `id="app-form-delete"` + ปุ่ม `form="app-form-delete"` | Week 08 |
| กัน N+1 | `with([...])`, `withCount('participants')` | Week 09 |
| ข้อความสำเร็จ | `->with('status', '...')` | Week 11 |
| ปุ่ม Back / Cancel | bookmarks: หน้าที่จะพาไปต่อจด `session()->put('bookmarks.xxx.view', url()->full())` · ปุ่มใช้ `session()->get('bookmarks.xxx.view') ?? route('xxx.index')` | Week 11 (Lab 11) |
| ข้อความ error | `->withErrors(['alert' => ...])` + `try/catch QueryException` → `errorInfo[2]` | Week 13 |
| ตรวจข้อมูล | **ไม่ใช้ `Validator` ใน CRUD** (เหมือน my-db): `required` / `type` ใน HTML + error จากฐานข้อมูล (code ซ้ำ ฯลฯ) ผ่าน `try/catch` + กฎพิเศษเช็กใน controller → `withInput()->withErrors(['alert' => ...])` · view ใช้ `old()` · `Validator` มีแค่ใน login | Week 12–13 |
| CSS class | ชื่อเดียวกับ `lec04-styles.css`: ปุ่ม `app-cl-button app-cl-filled app-cl-primary` / `app-cl-warning` · ตาราง `app-cmp-data-list` · ฟอร์ม `app-cmp-data-form` · รายละเอียด `<dl class="app-cmp-data-detail">` · `app-cmp-links-bar`, `app-cmp-form-actions`, `app-cmp-form-secondary-actions`, `app-cl-code`, `app-cl-link`, `app-cl-active` | `lec04-styles.css` |

### ชื่อ route (ลิงก์ไปหน้าคนอื่นได้เลย แม้หน้านั้นยังไม่เสร็จ)

| ของ | ชื่อ route |
|---|---|
| คนที่ 1 | `activities.index`, `activities.list`, `activities.view`, `activities.create-form`, `activities.create`, `activities.update-form`, `activities.update`, `activities.delete`, `activities.view-participants`, `activities.register`, `activities.cancel` |
| คนที่ 2 | `categories.index`, `categories.list`, `.view`, `.create-form`, `.create`, `.update-form`, `.update`, `.delete`, `.view-activities` · `activities.ticket` |
| คนที่ 3 | `home`, `login`, `authentication`, `logout`, `register-form`, `register`, `my-activities.list` · `organizers.*` (ชื่อเหมือน categories) |

### ไฟล์ที่ใช้ร่วมกัน (ห้ามแก้เองโดยไม่บอกเจ้าของ)

| ไฟล์ | เจ้าของ |
|---|---|
| `routes/web.php`, `layouts/main.blade.php`, `User.php`, `DatabaseSeeder.php`, `.env.example` | คนที่ 3 |
| `public/css/app.css`, `SearchableController.php`, `AppServiceProvider.php` | คนที่ 2 |
| `Activity.php` และ migration ของ activities / activity_user | คนที่ 1 |

### กติกาหลังฐานร่วมเสร็จ

- จะเพิ่ม/แก้คอลัมน์ → **บอกในกลุ่มก่อน** แก้แล้วให้ทุกคน `migrate:fresh --seed`
- **ห้าม commit `.env`** (รหัส MySQL แต่ละเครื่องไม่เหมือนกัน) และ `vendor/`
- commit บ่อย ๆ ข้อความ commit บอกว่าทำอะไรจริง ๆ เช่น `Category create/update + try/catch`

---

## ใครรับผิดชอบ requirement ไหน

| Requirement | คะแนน | ผู้รับผิดชอบ |
|---|:---:|---|
| R1 — 3 custom models + relationships ใช้จริง | 2 | ทุกคน model ของตัวเอง · คนที่ 1 ตาราง N–N |
| R2 — CRUD ครบ 3 models | 6 | คนที่ 1 Activity · คนที่ 2 Category · คนที่ 3 Organizer |
| R3 — แจ้งเตือน session + error | 2 | ทุกคนใน controller ตัวเอง · คนที่ 3 กล่องแสดงผลใน layout · คนที่ 2 สีเขียว/แดง |
| R4 — Auth, 2 roles, protected + public routes | 6 | คนที่ 3 login/สมัครสมาชิก/หน้าแรก · คนที่ 1 หน้ากิจกรรม public + สิทธิ์สมัคร · ทุกคน Policy ของตัวเอง |
| R5 — CSS | 2 | คนที่ 2 |
| R6 — PHP library (dompdf) | 2 | คนที่ 2 |

---

## ตรวจร่วมกันก่อนส่ง

- [ ] เครื่องใหม่ติดตั้งตาม README ได้ และ `migrate:fresh --seed` ผ่านบน MySQL
- [ ] CRUD ครบทั้ง Activity, Category, Organizer · ช่องว่างส่งไม่ได้ (`required`) · code ซ้ำขึ้นกล่องแดงจากฐานข้อมูล
- [ ] ทุก create / update / delete / สมัคร / ยกเลิก มีข้อความสำเร็จ (เขียว) และกรณีผิดพลาดมีข้อความ error (แดง)
- [ ] เห็น relationships ในหน้าเว็บจริง: ชื่อหมวด/ผู้จัดในหน้ากิจกรรม, กิจกรรมในหมวด/ผู้จัด, รายชื่อผู้สมัคร, กิจกรรมของฉัน
- [ ] Guest เปิดหน้าแรก/รายการ/รายละเอียดกิจกรรม/สมัครสมาชิกได้ แต่เข้า protected routes แล้วถูกพาไป login
- [ ] USER ดูหน้าหมวดหมู่/ผู้จัดได้แต่ไม่เห็นปุ่มสร้าง/แก้/ลบ · เปิด URL ของ ADMIN เอง (เช่น `/categories/create`, `/activities/create`) ได้ **403**
- [ ] ปุ่ม Back / Cancel กลับไปหน้ารายการเดิมพร้อมคำค้นและหน้าที่เปิดไว้ (bookmarks)
- [ ] ADMIN สมัครกิจกรรมไม่ได้ · สมัครซ้ำ/เต็ม/จบแล้ว ไม่ได้ และมีข้อความบอกเหตุผล
- [ ] ลบหมวด/ผู้จัดที่มีกิจกรรมไม่ได้ · ลบ CT004/OG004 ได้
- [ ] บัตร PDF ภาษาไทยอ่านได้ และดาวน์โหลดได้เฉพาะกิจกรรมที่ตัวเองสมัคร
- [ ] ทุกหน้าเป็น Blade (ไม่เป็น SPA) ใช้ CSS กลาง ลองบนมือถือแล้วไม่ล้นจอ
- [ ] ถามอาจารย์ครบตาม [ข้อ 15 ของแผน](../PROJECT_PLAN.md#15-คำถามที่ต้องถามอาจารย์) แล้ว
- [ ] ทุกคนเขียนส่วนของตัวเองในรายงาน: **งานที่รับผิดชอบ / วิธีพัฒนา / ไฟล์สำคัญ / ผลการทดสอบ**
- [ ] ทุกคนซ้อมอธิบายโค้ดของตัวเองทีละบรรทัด (ห้ามตอบ "ไม่รู้ เพื่อนทำ / AI ทำ")

### ลำดับ demo (ประมาณ 10 นาที)

1. **Guest** — หน้าแรก → ค้นหากิจกรรม → ดูรายละเอียด → กดสมัครแล้วถูกพาไป login → เปิด `/categories` ตรง ๆ แล้วถูกพาไป login
2. **สมัครสมาชิก** (คนที่ 3) — สร้างบัญชีใหม่ → ได้ role USER
3. **USER** (คนที่ 1, 2, 3) — สมัคร AC003 → สมัครซ้ำไม่ได้ → สมัคร AC004 (เต็ม) ไม่ได้ → ดาวน์โหลดบัตร PDF → กิจกรรมของฉัน → ยกเลิก → ดูหน้าหมวดหมู่ได้แต่ไม่มีปุ่มแก้ → พิมพ์ `/categories/create` ได้ 403
4. **ADMIN** (คนที่ 1, 2, 3) — CRUD หมวด → CRUD ผู้จัด → CRUD กิจกรรม (ลองใส่ code ซ้ำ → กล่องแดงจากฐานข้อมูล) → ลบหมวดที่มีกิจกรรมไม่ได้ → รายชื่อผู้สมัคร → สมัครกิจกรรมไม่ได้
5. **อธิบาย library** (คนที่ 2) — `composer require`, config ฟอนต์ไทย, จุดเรียก `Pdf::loadView()`

---

## เอกสารอ้างอิง

- [แผนโปรเจกต์](../PROJECT_PLAN.md) — schema, routes, business rules, การตรวจข้อมูล ฉบับเต็ม
- [กฎและเกณฑ์คะแนน](../PROJECT_RULES.md)
- [สรุปเว็บตัวอย่าง my-db](../my-db-project-summary.md)
- [Database / CRUD / Relationships — Week 07–09](../954348-week07-08-09-database-combined.md)
- [Relationships / Session / Auth / Errors — Week 10–13](../Laravel_Week10-11-12-13_Combined.md)
