# คนที่ 1 — Activities และการสมัคร

ชื่อสมาชิก: __________
Branch: `member-1/activities`

รับผิดชอบ model หลักของระบบ (Activity) ตารางกลางการสมัคร (`activity_user`) และทุกอย่างที่เกี่ยวกับการสมัครกิจกรรม อ่าน [ข้อตกลงร่วม](README.md) ก่อนเริ่ม

| เทียบกับเว็บในคาบ (`my-db`) | งานนี้ |
|---|---|
| Product + `ProductController` + `ProductPolicy` | Activity + `ActivityController` + `ActivityPolicy` |
| Product belongsTo Category · เลือก Category ใน `<select>` + `associate()` (Lab 10) | Activity belongsTo Category **และ** Organizer · `associate()` ทั้งสองตัว |
| Product N–N Shop ผ่าน `product_shop` · `attach()` / `detach()` (Lab 09–10) | Activity N–N User ผ่าน `activity_user` · สมัคร = `attach()`, ยกเลิก = `detach()` |
| หน้า `view-shops` | หน้า `view-participants` (รายชื่อผู้สมัคร) |

---

## งานในฐานร่วม (ขั้นที่ 1)

1. migration `activities` — `code` unique (64), `name`, `category_id` + `organizer_id` (`constrained()->restrictOnDelete()`), `date` (date), `location`, `capacity` (unsigned int), `description` (text)
2. migration `activity_user` — `activity_id`, `user_id` (`constrained()->cascadeOnDelete()`), timestamps, `primary(['activity_id', 'user_id'])` → สมัครซ้ำไม่ได้ในระดับฐานข้อมูล
3. `app/Models/Activity.php` — `#[Fillable]` ไม่ใส่ FK · cast `date` เป็น date · `category()`, `organizer()`, `participants()` (`belongsToMany(User::class)->withTimestamps()`)
4. `ActivitySeeder` (AC001–AC012 วันที่คิดจาก `today()`) และ `RegistrationSeeder` (ตามตารางข้อมูลตัวอย่างใน README)

## งานหลัก (ขั้นที่ 2)

1. **หน้า public** — รายการกิจกรรม (ค้นหาด้วย code/ชื่อ/สถานที่/ชื่อหมวด/ชื่อผู้จัด, กรองหมวด, เฉพาะที่ยังไม่จัด, แบ่งหน้า) และรายละเอียดกิจกรรม (แสดงหมวด, ผู้จัด, จำนวน "สมัครแล้ว / รับ", สถานะ เปิดรับ / เต็มแล้ว / จบแล้ว)
2. **Activity CRUD (ADMIN)** — สร้าง / แก้ไข / ลบ แบบ Product ในคาบ (`required` ใน HTML + `try/catch`, ปุ่ม Back/Cancel ใช้ bookmarks)
3. **สมัคร / ยกเลิก (USER)** พร้อมกฎ:
   - ADMIN สมัครไม่ได้
   - สมัครซ้ำไม่ได้
   - สมัครเมื่อเต็มไม่ได้ (`loadCount('participants')` เทียบ `capacity`)
   - สมัคร / ยกเลิกหลังวันจัดไม่ได้
   - ข้อความบอกเหตุผลเป็นภาษาไทย (Policy คืน `Response::deny('...')` แล้ว controller ใช้ `Gate::inspect()`)
4. **รายชื่อผู้สมัคร (ADMIN)** — ตารางชื่อ, อีเมล, เวลาที่สมัคร (`$participant->pivot->created_at`) ค้นด้วยชื่อ/อีเมลได้
5. แก้ `capacity` ให้น้อยกว่าจำนวนที่สมัครแล้วไม่ได้ (เช็กใน controller ตอน update แล้วส่ง `alert`)
6. ปุ่มในหน้ารายละเอียดตามสิทธิ์: Guest → "เข้าสู่ระบบเพื่อสมัคร" · USER → สมัคร / ยกเลิก / ดาวน์โหลดบัตร (ลิงก์ไป `activities.ticket` ของคนที่ 2) · ADMIN → แก้ไข / ลบ / รายชื่อผู้สมัคร

## ไฟล์ที่รับผิดชอบ

| ไฟล์ | งาน |
|---|---|
| `database/migrations/*_create_activities_table.php` | schema + FK restrict |
| `database/migrations/*_create_activity_user_table.php` | ตารางกลาง + PK คู่ + FK cascade |
| `database/seeders/ActivitySeeder.php`, `RegistrationSeeder.php` | ข้อมูลตัวอย่าง |
| `app/Models/Activity.php` | relationships + helper `isOpen()`, `isFull()`, `statusLabel()` |
| `app/Http/Controllers/ActivityController.php` | list, view, CRUD, viewParticipants, register, cancel |
| `app/Policies/ActivityPolicy.php` | `list`/`view` ใช้ `?User` · create/update/delete/viewParticipants = ADMIN · register/cancel ตามกฎ |
| `resources/views/activities/*` | main, list, search-form, table, view, form-fields, create-form, update-form, view-participants |
| `routes/activities.php` | route ทั้งหมดของ `/activities` (ยกเว้น `/ticket`) |

`views/activities/table.blade.php` และ `search-form.blade.php` ทำเป็นชิ้นส่วนที่ `@include` ได้ คนที่ 2 และ 3 จะใช้ในหน้า "กิจกรรมในหมวด/ผู้จัด" และหน้าแรก

## Routes

> ⚠️ ลำดับใน `routes/activities.php` สำคัญ 2 ข้อ (ใช้ตามตัวอย่างในคอมเมนต์ของไฟล์ได้เลย):
> 1. `ANY ''` (index) ต้องอยู่ **ก่อน** `POST ''` (create) — ถ้าสลับ ANY จะทับ POST แล้วกดสร้างกิจกรรมจะโดน redirect แทนการบันทึก
> 2. กลุ่ม `auth` ต้องอยู่ **ก่อน** `GET /{activity}` — ไม่งั้นคำว่า `create` ใน `/activities/create` จะถูกจับเป็นรหัสกิจกรรม

| Method / URL | Name | ทำอะไร | สิทธิ์ |
|---|---|---|---|
| GET `/activities/list` | `activities.list` | รายการ + ค้นหา + แบ่งหน้า | Public |
| ANY `/activities` | `activities.index` | redirect → list (เมนูลิงก์มาที่นี่) | Public |
| GET `/activities/{activity}` | `activities.view` | รายละเอียด + ปุ่มตามสิทธิ์ | Public |
| GET `/activities/create` | `activities.create-form` | ฟอร์มสร้าง | ADMIN |
| POST `/activities` | `activities.create` | บันทึกใหม่ | ADMIN |
| GET `/activities/{activity}/update` | `activities.update-form` | ฟอร์มแก้ไข | ADMIN |
| POST `/activities/{activity}` | `activities.update` | บันทึกแก้ไข | ADMIN |
| POST `/activities/{activity}/delete` | `activities.delete` | ลบ (การสมัครถูกลบตาม) | ADMIN |
| GET `/activities/{activity}/participants` | `activities.view-participants` | รายชื่อผู้สมัคร | ADMIN |
| POST `/activities/{activity}/register` | `activities.register` | สมัคร (`attach`) | USER |
| POST `/activities/{activity}/cancel` | `activities.cancel` | ยกเลิก (`detach`) | USER ที่สมัครแล้ว |

## การตรวจข้อมูล (แบบในคาบ — ไม่ใช้ Validator)

เหมือน `ProductController::create/update` ใน my-db: ใช้ `required` ใน HTML + `try/catch` จับ error จากฐานข้อมูล + กฎพิเศษเช็กใน controller

| ช่อง | ตรวจที่ไหน |
|---|---|
| ทุกช่อง | HTML `required` · `date` ใช้ `type="date"` · `capacity` ใช้ `type="number" min="1"` |
| `code` ซ้ำ | ฐานข้อมูล (unique) → `QueryException` → กล่องแดง `Duplicate entry ...` |
| `category`, `organizer` | `Category::where('code', $data['category'])->firstOrFail()` แล้ว `associate()` (แบบ Product → Category ในคาบ) |
| `capacity` ตอน update | controller: `$activity->loadCount('participants')` ถ้า `capacity` ใหม่น้อยกว่า → `redirect()->back()->withInput()->withErrors(['alert' => 'จำนวนรับต้องไม่น้อยกว่าจำนวนผู้สมัคร (N คน)'])` |

## รับ / ส่งต่อ

- **รับจากคนที่ 2:** `SearchableController`, `Category` model, CSS class
- **รับจากคนที่ 3:** `User::activities()`, `isAdministrator()`, `isMember()`, `Organizer` model, layout
- **ส่งให้คนที่ 2:** `participants()` (ใช้ทำบัตร), ปุ่มลิงก์ `activities.ticket` ในหน้ารายละเอียด, `views/activities/table` + `search-form` สำหรับหน้า `categories.view-activities`
- **ส่งให้คนที่ 3:** `views/activities/table` สำหรับหน้า `organizers.view-activities`, helper `statusLabel()` สำหรับหน้าแรก

## Checklist ว่างานเสร็จ

- [ ] Guest เปิด `/activities` (ไป `/activities/list`), `/activities/AC001` ได้ · ค้นหา "จิตอาสา" แล้วเจอกิจกรรมในหมวดนั้น · เปลี่ยนหน้าแล้วคำค้นยังอยู่
- [ ] Guest เปิด `/activities/create` แล้วถูกพาไป login · USER เปิดแล้วได้ 403
- [ ] ADMIN สร้าง / แก้ไข / ลบกิจกรรมได้ · แต่ละครั้งมีข้อความสำเร็จ
- [ ] ช่องว่างกดส่งไม่ได้ (`required`) · สร้างด้วย code ซ้ำ → กล่องแดงจากฐานข้อมูล และค่าที่กรอกยังอยู่ (`old()`)
- [ ] กด Back จากหน้ารายละเอียด → กลับหน้ารายการเดิมพร้อมคำค้นและหน้าที่เปิดไว้ (bookmarks)
- [ ] แก้ AC001 ให้รับ 2 คน (สมัครแล้ว 3) → ไม่ได้ มีกล่องแดงบอกเหตุผล
- [ ] USER สมัคร AC003 ได้ → สมัครซ้ำได้ข้อความ "สมัครไปแล้ว"
- [ ] USER สมัคร AC004 (เต็ม) → "มีผู้สมัครเต็มแล้ว" · ยกเลิก AC008 (จบแล้ว) → "จบไปแล้ว"
- [ ] ADMIN กดสมัคร (ส่ง POST เอง) → "ผู้ดูแลระบบไม่สามารถสมัครกิจกรรมได้"
- [ ] ลบ AC001 แล้วในตาราง `activity_user` ไม่มีแถวของ AC001 เหลือ
- [ ] รายชื่อผู้สมัครแสดงเวลาสมัครจากตารางกลาง

## สิ่งที่ต้องอธิบายในรายงานและ demo

- ทำไมการสมัครเป็น **ตารางกลาง (belongsToMany)** และ `attach()` / `detach()` ทำงานอย่างไร (เทียบ Product–Shop ใน Lab 10)
- PK คู่ `(activity_id, user_id)` กันสมัครซ้ำได้อย่างไร ทำไมยังต้องเช็กใน Policy ด้วย
- `restrictOnDelete` กับ `cascadeOnDelete` ต่างกันอย่างไร และทำไมเลือกแต่ละแบบ
- `associate()` ทำไมไม่เซ็ต `category_id` ตรง ๆ และทำไม FK ไม่อยู่ใน `#[Fillable]`
- `withCount('participants')` กัน N+1 อย่างไร
- `Gate::authorize()` (403) กับ `Gate::inspect()` (เอาข้อความไปแสดง) ต่างกันอย่างไร
- ทำไม Policy `view` ต้องเป็น `?User $user`

ในรายงานเขียน 4 หัวข้อ: **งานที่รับผิดชอบ / วิธีพัฒนา / ไฟล์สำคัญ / ผลการทดสอบ** (ใช้ checklist ด้านบนเป็นผลการทดสอบ)
