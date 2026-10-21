# คนที่ 2 — Categories, CSS และบัตรเข้าร่วม PDF

ชื่อสมาชิก: __________
Branch: `member-2/categories-css-pdf`

รับผิดชอบหมวดหมู่กิจกรรม ระบบค้นหา/แบ่งหน้าที่ทุกคนใช้ หน้าตาของทั้งเว็บ (R5) และ PHP library (R6) อ่าน [ข้อตกลงร่วม](README.md) ก่อนเริ่ม

| เทียบกับเว็บในคาบ (`my-db`) | งานนี้ |
|---|---|
| Category + `CategoryController` + `CategoryPolicy` (Lab 09, 12–13) | Category เหมือนเดิมเกือบทั้งหมด แต่ลูกคือ Activity แทน Product |
| `CategoryPolicy::delete` ลบได้เมื่อ `products_count === 0` (Lab 13) | ลบได้เมื่อ `activities_count === 0` |
| `SearchableController` (Lab 07–08) | เขียนใหม่ให้ทุก controller ใช้ |
| `lec04-styles.css` ของรายวิชา | `public/css/app.css` เขียนเองทั้งหมด |
| (ไม่มีในคาบ) | **dompdf** สร้างบัตรเข้าร่วมเป็น PDF |

---

## งานในฐานร่วม (ขั้นที่ 1)

1. migration `categories` — `code` unique (64), `name`, `description` (text)
2. `app/Models/Category.php` — `#[Fillable(['code', 'name', 'description'])]` · `activities()` hasMany
3. `CategorySeeder` — CT001 กีฬาและสุขภาพ, CT002 จิตอาสา, CT003 วิชาการ, CT004 ศิลปวัฒนธรรม (**ไม่มีกิจกรรม** ไว้ทดสอบลบ)
4. `app/Http/Controllers/SearchableController.php` — abstract `getQuery()`, `prepareCriteria()`, `getFilterOptions()` (default: code, name), `filterByTerm()` (แยกคำด้วยช่องว่าง แต่ละคำ AND กัน ฟิลด์ OR กัน), `filter()`, `search()`, `find($code)` → `firstOrFail()` (ไม่เจอ = 404), `MAX_ITEMS = 5`
5. `AppServiceProvider::boot()` — `Paginator::defaultView('pagination::semantic-ui')` (template ที่ไม่ต้องใช้ Tailwind แล้วแต่งเองใน CSS)
6. `public/css/app.css` ตั้งต้น — ตัวแปรสีใน `:root`, header/เมนู, ปุ่ม, ฟอร์ม, ตาราง `app-cmp-data-list`, กล่องแจ้งเตือน `[role=status]` (เขียว) / `[role=alert]` (แดง)

## งานหลัก (ขั้นที่ 2)

### ก. Category CRUD (ดู: ทุกคนที่ login · สร้าง/แก้/ลบ: ADMIN — เหมือน `CategoryPolicy` ในคาบ)

1. list (ค้นหา + แบ่งหน้า + จำนวนกิจกรรม `withCount('activities')`), view, create, update, delete
2. `required` ใน HTML + `try/catch QueryException` + ข้อความ `status` / `alert` ทุก action (ไม่ใช้ `Validator`)
3. ปุ่ม Back / Cancel ใช้ bookmarks แบบ Lab 11 (ดูตัวอย่างใน `categories/list`, `view`, `create-form` ของ my-db)
3. ปุ่ม Delete โผล่เฉพาะหมวดที่ไม่มีกิจกรรม (`@can('delete', $category)`) และกันที่ controller ด้วย `Gate::authorize('delete', ...)`
4. หน้า "กิจกรรมในหมวดนี้" — แบบหน้า `view-products` ของ Category ในคาบ: ใช้ `$category->activities()` + ยืม `prepareCriteria()` / `filter()` ของ `ActivityController` ผ่าน `\resolve(ActivityController::class)` · **เขียนตารางกิจกรรมเองในไฟล์ `categories/view-activities.blade.php`** (ในคาบเขียนตารางซ้ำเอง ไม่ใช้ `@include`) ระหว่างที่ `ActivityController` ยังไม่เสร็จ ใช้ `$category->activities()->paginate()` ตรง ๆ ไปก่อนได้

### ข. CSS ทั้งเว็บ (R5)

- ทุกหน้าใช้ไฟล์เดียวผ่าน layout หลัก · ชื่อ class `app-cmp-*` / `app-cl-*`
- ชื่อ class ตาม `lec04-styles.css` (ตั้งไว้แล้วในฐานร่วม) — view ของทุกคนจึงเขียน class เหมือนในคาบได้เลย
- ต้องครอบคลุม: หน้าแรก (การ์ดกิจกรรม), ตาราง, ฟอร์ม, ป้ายสถานะ (เปิดรับ / เต็มแล้ว / จบแล้ว), กล่องสมัคร, pagination, เมนูที่ active
- ใช้ได้บนมือถือ (`@media (max-width: 760px)`) — ตาราง/ฟอร์มไม่ล้นจอ
- ไม่ใช้ CSS framework (ถ้าอยากใช้ ต้องถามอาจารย์ก่อน)

### ค. บัตรเข้าร่วม PDF ด้วย dompdf (R6)

| หัวข้อ | รายละเอียด |
|---|---|
| Package | `barryvdh/laravel-dompdf` (ห่อ `dompdf/dompdf`) |
| ติดตั้ง | `composer require barryvdh/laravel-dompdf` (commit `composer.json` + `composer.lock`) |
| ตั้งค่า | ไม่ต้อง publish config · ฟอนต์ไทยวางที่ `resources/fonts/` · dompdf ใช้ `storage/fonts/` เป็น cache |
| ฟีเจอร์ | USER ที่สมัครแล้วกด "ดาวน์โหลดบัตรเข้าร่วม (PDF)" ในหน้ารายละเอียดกิจกรรม หรือในหน้ากิจกรรมของฉัน |
| เนื้อหาบัตร | ชื่อ, อีเมล, ชื่อ/รหัสกิจกรรม, หมวด, วันที่, สถานที่, ผู้จัด, เลขที่บัตร, เวลาที่สมัคร (`pivot->created_at`) |
| สิทธิ์ | ค้นกิจกรรมจาก `$user->activities()` (เฉพาะที่ตัวเองสมัคร) → ไม่ได้สมัคร / ADMIN = 404 |

```php
// app/Http/Controllers/TicketController.php
use Barryvdh\DomPDF\Facade\Pdf;

function download(string $activityCode): Response
{
    $user = Auth::user();
    $activity = $user->activities()
        ->where('activities.code', $activityCode)
        ->with(['category', 'organizer'])
        ->firstOrFail();

    return Pdf::loadView('tickets.pdf', [
        'activity' => $activity,
        'user' => $user,
        'registeredAt' => $activity->pivot->created_at,
    ])->setPaper('a5', 'landscape')->download("ticket-{$activity->code}.pdf");
}
```

> ⚠️ **ทำเรื่องภาษาไทยเป็นอย่างแรก:** dompdf ไม่มีฟอนต์ไทยในตัว ถ้าไม่ตั้งจะได้ `???` หรือสี่เหลี่ยม
> ดาวน์โหลด **Sarabun** (Google Fonts, สัญญาอนุญาต OFL ใช้ฟรี) `Sarabun-Regular.ttf` + `Sarabun-Bold.ttf` วางที่ `resources/fonts/` แล้วประกาศใน view:
>
> ```css
> @font-face { font-family: 'Sarabun'; font-weight: normal; src: url('{{ resource_path('fonts/Sarabun-Regular.ttf') }}'); }
> @font-face { font-family: 'Sarabun'; font-weight: bold;   src: url('{{ resource_path('fonts/Sarabun-Bold.ttf') }}'); }
> body { font-family: 'Sarabun', sans-serif; }
> ```
>
> dompdf รองรับ CSS จำกัด (ไม่มี flexbox / grid) → จัดหน้าบัตรด้วย `<table>` · view ของ PDF เป็น HTML ธรรมดา **ไม่ extends layout**

ไม่ต้องรอระบบสมัครของคนที่ 1 — `user@activity.test` สมัคร AC001, AC006 ไว้แล้วใน seeder ทดสอบบัตรได้ทันที

## ไฟล์ที่รับผิดชอบ

| ไฟล์ | งาน |
|---|---|
| `database/migrations/*_create_categories_table.php`, `database/seeders/CategorySeeder.php` | schema + ข้อมูลตัวอย่าง |
| `app/Models/Category.php` | `activities()` |
| `app/Http/Controllers/SearchableController.php` | คลาสแม่ค้นหา/แบ่งหน้า |
| `app/Http/Controllers/CategoryController.php` | Category CRUD + viewActivities |
| `app/Policies/CategoryPolicy.php` | list/view = `true` (ทุกคนที่ login) · create/update = ADMIN · delete = ADMIN + ไม่มีกิจกรรม |
| `resources/views/categories/*` | main, list, view, form-fields, create-form, update-form, view-activities |
| `app/Http/Controllers/TicketController.php` | ดาวน์โหลดบัตร PDF |
| `resources/views/tickets/pdf.blade.php` | หน้าตาบัตร |
| `resources/fonts/Sarabun-*.ttf`, `resources/fonts/OFL.txt` | ฟอนต์ไทย + สัญญาอนุญาต |
| `routes/categories.php`, `routes/tickets.php` | routes |
| `app/Providers/AppServiceProvider.php` | pagination view |
| `public/css/app.css` | CSS ทั้งเว็บ |

## Routes

| Method / URL | Name | ทำอะไร | สิทธิ์ |
|---|---|---|---|
| GET `/categories/list` | `categories.list` | รายการ + ค้นหา + แบ่งหน้า | ทุกคนที่ login |
| ANY `/categories` | `categories.index` | redirect → list (เมนูลิงก์มาที่นี่) | ทุกคนที่ login |
| POST `/categories` | `categories.create` | บันทึกใหม่ | ADMIN |
| GET `/categories/create` | `categories.create-form` | ฟอร์มสร้าง | ADMIN |
| GET `/categories/{category}` | `categories.view` | รายละเอียด | ทุกคนที่ login |
| POST `/categories/{category}` | `categories.update` | บันทึกแก้ไข | ADMIN |
| GET `/categories/{category}/update` | `categories.update-form` | ฟอร์มแก้ไข | ADMIN |
| POST `/categories/{category}/delete` | `categories.delete` | ลบ (เฉพาะหมวดว่าง) | ADMIN |
| GET `/categories/{category}/activities` | `categories.view-activities` | กิจกรรมในหมวด | ทุกคนที่ login |
| GET `/activities/{activity}/ticket` | `activities.ticket` | ดาวน์โหลดบัตร PDF | USER ที่สมัครแล้ว |

เขียนตามโครง `/categories` ใน `web.php` ของ my-db (มีตัวอย่างในคอมเมนต์ของ `routes/categories.php`) · `/create` ต้องประกาศก่อน `/{category}` · ทั้งไฟล์ครอบด้วย middleware `auth`

## การตรวจข้อมูล (แบบในคาบ — ไม่ใช้ Validator)

เหมือน `CategoryController` ใน my-db ทุกอย่าง: `Category::create($request->getParsedBody())` ใน `try` · ฟอร์มใช้ `required` · code ซ้ำ → `QueryException` → กล่องแดง `Duplicate entry ...` · ฟอร์มเติมค่าเดิมด้วย `old()`

## รับ / ส่งต่อ

- **ส่งให้ทุกคน (ต้นขั้นที่ 1):** `SearchableController` และชื่อ class CSS ที่ใช้ได้ (ตาราง, ฟอร์ม, ปุ่ม, ป้ายสถานะ)
- **ส่งให้คนที่ 3:** แจ้งว่า `OrganizerController` / `OrganizerPolicy` / views ใช้โครงเดียวกับ Category ได้เลย
- **รับจากคนที่ 1:** `Activity::participants()`, `ActivityController::filter()` (ยืมผ่าน `\resolve()`), ปุ่มลิงก์ไป `activities.ticket`
- **รับจากคนที่ 3:** layout หลัก (คนที่ 2 แต่งเฉพาะ CSS — ถ้าต้องเพิ่ม class ใน layout ให้บอกคนที่ 3)

## Checklist ว่างานเสร็จ

- [ ] Guest เปิด `/categories` ถูกพาไป login
- [ ] ADMIN สร้าง / ดู / แก้ / ลบหมวดได้ และมีข้อความสำเร็จทุกครั้ง
- [ ] ช่องว่างกดส่งไม่ได้ (`required`) · code ซ้ำ → กล่องแดงจากฐานข้อมูล และค่าที่กรอกยังอยู่
- [ ] USER เห็นเมนูหมวดหมู่ ดูรายการ/รายละเอียดได้ แต่ไม่เห็นปุ่มสร้าง/แก้/ลบ · พิมพ์ `/categories/create` เองได้ 403
- [ ] ปุ่ม Back / Cancel กลับหน้ารายการเดิมพร้อมคำค้น (bookmarks)
- [ ] CT001 (มีกิจกรรม) ไม่มีปุ่มลบ และส่ง POST ลบเองได้ 403 · CT004 ลบได้
- [ ] ค้นหา 2 คำพร้อมกันได้ · เปลี่ยนหน้าแล้วคำค้นไม่หาย
- [ ] หน้า "กิจกรรมในหมวด" แสดงเฉพาะกิจกรรมของหมวดนั้น
- [ ] ทุกหน้าใช้ CSS เดียวกัน ข้อความสำเร็จสีเขียว error สีแดง · ลองบนมือถือแล้วไม่ล้นจอ
- [ ] บัตร PDF ภาษาไทยอ่านได้ทุกตัว (ไม่มี `???`) ทั้งตัวปกติและตัวหนา
- [ ] `user@activity.test` โหลดบัตร AC001 ได้ · โหลดบัตร AC002 (ไม่ได้สมัคร) ได้ 404 · ADMIN โหลดไม่ได้
- [ ] `composer install` บนเครื่องเพื่อนแล้วบัตรยังใช้ได้ (ฟอนต์อยู่ใน repo)

## สิ่งที่ต้องอธิบายในรายงานและ demo

- `SearchableController`: ทำไมเป็น abstract class, ค้นหาหลายคำทำงานอย่างไร, `withQueryString()` ทำอะไร
- Category → Activity (hasMany) และทำไมลบหมวดที่มีกิจกรรมไม่ได้ (Policy + FK restrict 2 ชั้น)
- `@can` (ซ่อนปุ่ม) กับ `Gate::authorize` (กันจริง) ต่างกันอย่างไร
- CSS: ตัวแปรสีใน `:root`, ชื่อ class `app-cmp-*`, `@media` สำหรับมือถือ
- **dompdf (R6):** ติดตั้งด้วยคำสั่งอะไร, ตั้งค่าฟอนต์ไทยอย่างไร, เรียกใช้ตรงไหนในโค้ด (`Pdf::loadView()->download()`), ทำไมเลือกตัวนี้, ทำไมคนอื่นโหลดบัตรของเราไม่ได้ (ค้นจาก `$user->activities()`)
- ตรวจแล้วว่า dompdf ไม่ได้สอนในคาบ (ถามอาจารย์ให้ชัด)

ในรายงานเขียน 4 หัวข้อ: **งานที่รับผิดชอบ / วิธีพัฒนา / ไฟล์สำคัญ / ผลการทดสอบ** และเพิ่มหัวข้อ **การใช้ PHP library** (ติดตั้ง / ตั้งค่า / เรียกใช้ / ภาพบัตรตัวอย่าง)
