# กฎการทำ Term Project — ภาคเรียน 1/2569

> เอกสารนี้สรุปกฎและเกณฑ์จาก requirement ของอาจารย์ เพื่อใช้เป็นหลักยึดของกลุ่มตลอดการทำโปรเจกต์
> **ถ้ามีข้อไหนไม่แน่ใจ ให้ยึดคำสั่งอาจารย์เป็นหลัก และถามอาจารย์ก่อนทำ**

---

## 1. ข้อกำหนดทั่วไป

| หัวข้อ | กฎ |
|---|---|
| จำนวนสมาชิก | ไม่เกิน 3 คน (น้อยกว่า 3 ได้ แต่ห้ามเกิน 3) |
| Framework | ต้องใช้ **Laravel** |
| หน้าเว็บ | ใช้ **Blade templates** เท่านั้น |
| ห้าม SPA | ห้ามทำเป็น Single Page Application (เช่น React / Vue / Inertia ที่ทำเป็น SPA, Livewire `wire:navigate`) ทุกหน้าต้องโหลดผ่าน route + Blade ปกติ |
| ที่มาของโปรเจกต์ | **ห้ามใช้โปรเจกต์จากแล็บ** ต้องเป็นงานใหม่ของกลุ่ม |
| ความยาก | ไม่ต้องทำให้ยากเกินไป ทำให้ **ครบทุก requirement** ก็ได้คะแนนเต็ม |

---

## 2. Requirements และคะแนน (รวม 20 คะแนน)

| # | Requirement | คะแนน |
|---|---|:---:|
| R1 | มี custom Eloquent model อย่างน้อย 3 ตัว (ไม่นับ `User`) และมี relationship ระหว่างกัน | 2 |
| R2 | CRUD ได้ครบอย่างน้อย 3 model | 6 |
| R3 | แสดง notification ทุกครั้งที่จัดการฐานข้อมูล (ผ่าน session) รวมถึงกรณี error | 2 |
| R4 | ระบบ authentication — protected routes ที่ต้อง login พร้อม 2 role ที่ต่างกัน และมี public routes | 6 |
| R5 | ใช้ CSS | 2 |
| R6 | ใช้ PHP third-party library/framework อย่างน้อย 1 ตัวที่ **ไม่ได้สอนในคาบ** | 2 |
| | **รวม** | **20** |

> ⚠️ ทุกข้อต้อง **implement อย่างสมเหตุสมผล** ถ้าทำแบบแค่ให้มีไว้ (ไม่ได้ใช้งานจริง / ไม่สมเหตุสมผล) ข้อนั้นจะได้ **0 คะแนน**

---

## 3. รายละเอียดและเกณฑ์ "ผ่าน" ของแต่ละข้อ

### R1 — Eloquent Models + Relationships (2 คะแนน)
- [ ] มี model ที่สร้างเองอย่างน้อย **3 ตัว** — `User` ไม่นับ
- [ ] ทุก model มี migration และตารางจริงในฐานข้อมูล
- [ ] model **มี relationship เชื่อมกัน** (เช่น `hasMany` / `belongsTo` / `belongsToMany`) และกำหนด foreign key ใน migration
- [ ] relationship ถูก **ใช้งานจริง** ในหน้าเว็บ (เช่น แสดงรายการลูกของ record แม่) ไม่ใช่แค่ประกาศไว้ใน model
- [ ] relationship ต้องสมเหตุสมผลกับเรื่องของเว็บ

### R2 — CRUD อย่างน้อย 3 Models (6 คะแนน)
สำหรับ **แต่ละ** model ที่นับ ต้องทำได้ครบ:
- [ ] **Create** — มีฟอร์มเพิ่มข้อมูล + บันทึกลงฐานข้อมูล
- [ ] **Retrieve** — มีหน้ารายการ (index) และ/หรือหน้ารายละเอียด (show)
- [ ] **Update** — มีฟอร์มแก้ไข + บันทึกการแก้ไข
- [ ] **Delete** — ลบข้อมูลได้ (ใช้ form + `@method('DELETE')` + `@csrf`)
- [ ] มี **validation** ข้อมูลก่อนบันทึก
- [ ] ระวังการลบ record ที่มีข้อมูลลูกผูกอยู่ (กำหนด `cascade` หรือจัดการ error ให้เรียบร้อย)

### R3 — Notification ผ่าน Session (2 คะแนน)
- [ ] ทุกการ **create / update / delete** ต้องแสดงข้อความแจ้งผล เช่น `redirect()->...->with('success', '...')`
- [ ] แสดงข้อความใน **layout หลัก** ที่ทุกหน้าใช้ร่วมกัน (เขียนครั้งเดียว แสดงได้ทุกหน้า)
- [ ] มีการ **จัดการ error** ด้วย:
  - validation error → แสดงผ่าน `$errors` ในฟอร์ม
  - error จากฐานข้อมูล / กรณีผิดพลาด → ดักด้วย `try/catch` แล้วส่ง `with('error', '...')`
- [ ] ข้อความสำเร็จกับข้อความ error ควรแยกสีให้เห็นชัด

### R4 — Authentication + 2 Roles (6 คะแนน)
- [ ] มีระบบ **login / logout**
- [ ] มี **2 role ที่ต่างกัน** (เช่น `admin` กับ `member`) — เก็บ role ไว้ในฐานข้อมูล
- [ ] **Protected routes**: ต้อง login ก่อนเข้าได้ (ใช้ middleware `auth`)
- [ ] แต่ละ role **ทำสิ่งต่างกันได้จริง** — มี route/หน้าที่เฉพาะ role หนึ่งเข้าได้ และอีก role เข้าไม่ได้ (เช่น ใช้ middleware ตรวจ role)
- [ ] **Public routes**: มีหน้าที่ทุกคนเข้าได้โดยไม่ต้อง login — **นอกเหนือจากหน้า login** (เช่น หน้าแรก, หน้ารายการสาธารณะ)
- [ ] ซ่อนปุ่ม/เมนูที่ role นั้นไม่มีสิทธิ์ และกันที่ฝั่ง route ด้วย (ไม่ใช่แค่ซ่อนปุ่ม)

### R5 — CSS (2 คะแนน)
- [ ] มีการตกแต่งหน้าเว็บด้วย CSS ที่เห็นผลชัดและใช้ทั่วทั้งเว็บ
- [ ] ทุกหน้าใช้ layout/สไตล์เดียวกัน ดูเป็นระบบ
- [ ] ถ้าจะใช้ CSS framework (เช่น Bootstrap / Tailwind) ควรมี CSS ที่กลุ่มเขียนเองด้วย และ **ถามอาจารย์ว่านับหรือไม่**

### R6 — PHP Third-party Library (2 คะแนน)
- [ ] ติดตั้งผ่าน **Composer** และเป็น library ฝั่ง **PHP** (ไม่ใช่ JS/CSS)
- [ ] **ต้องไม่ใช่สิ่งที่สอนในคาบ** → ตรวจกับเนื้อหาที่เรียนก่อนเลือก
- [ ] ต้อง **ใช้งานจริงในฟีเจอร์ของเว็บ** ไม่ใช่แค่ติดตั้งไว้
- [ ] ต้อง **อธิบายได้** ว่าติดตั้งอย่างไร ตั้งค่าอย่างไร และเรียกใช้ตรงไหนในโค้ด
- [ ] ตรวจกับรายการ "ถือว่าสอนแล้ว" ในหัวข้อ 7.2 — ตัวที่เลือกอยู่ใน `PROJECT_PLAN.md` ข้อ 9

---

## 4. กฎการทำงานในกลุ่ม

1. **ทำเองเท่านั้น** — ใช้ AI / internet เพื่อเรียนรู้ได้ แต่ต้องเข้าใจโค้ดทุกบรรทัดที่ตัวเองรับผิดชอบ
2. **แบ่งงานชัดเจน** — ทุกคนต้องมีส่วนงานของตัวเองที่ระบุได้ (จะบันทึกไว้ในส่วนแผนของกลุ่ม)
3. **ทุกคนต้องอธิบายงานตัวเองได้** และเข้าใจภาพรวมของระบบทั้งหมดคร่าว ๆ
4. **ห้ามตอบอาจารย์แบบนี้** (โดนหักคะแนน และหักได้จนเหลือ 0):
   - "ไม่รู้ครับ/ค่ะ เพื่อนสอนมา"
   - "ไม่รู้ ลอกมาจากเน็ต"
   - "ไม่รู้ AI เขียนให้"
   - "ไม่รู้..." ทุกรูปแบบ
5. **ทำให้เรียบง่าย** — ทำให้ครบ requirement ก่อน อย่าเพิ่มฟีเจอร์ที่อธิบายไม่ได้
6. แนะนำให้ใช้ **Git** และให้แต่ละคน commit งานของตัวเอง เพื่อเป็นหลักฐานการทำงาน

---

## 5. การนำเสนอ

สิ่งที่ต้องเตรียม:
- [ ] **เอกสารโปรแกรม** ส่งอาจารย์ ประกอบด้วย
  - งานของสมาชิกแต่ละคน
  - วิธีการพัฒนา (how to develop)
- [ ] **สาธิตโปรแกรม** ให้ครบทุก requirement
- [ ] **อธิบายการใช้ third-party library** (ติดตั้ง / ตั้งค่า / ใช้งาน)
- [ ] **สมาชิกแต่ละคนอธิบายงานของตัวเอง**
- [ ] **ตอบคำถามอาจารย์** ให้ได้

---

## 6. Checklist ก่อนส่ง

- [ ] R1 — model ≥ 3 ตัว (ไม่นับ User) + relationship ใช้งานจริง
- [ ] R2 — CRUD ครบ 4 อย่าง ใน ≥ 3 model
- [ ] R3 — flash message ทุกการ create/update/delete + แสดง error
- [ ] R4 — login/logout, 2 role, protected routes, public routes (นอกจากหน้า login)
- [ ] R5 — มี CSS ใช้ทั้งเว็บ
- [ ] R6 — PHP library ที่ไม่ได้สอน ใช้งานจริงและอธิบายได้
- [ ] ใช้ Blade ทั้งหมด ไม่เป็น SPA
- [ ] ไม่ได้ใช้โปรเจกต์จากแล็บ
- [ ] เอกสารโปรแกรมพร้อมส่ง
- [ ] ทุกคนซ้อมอธิบายงานตัวเองแล้ว

---

## 7. เนื้อหาที่เรียน และแนวทางที่ต้องยึด

### 7.1 เนื้อหาที่เรียนในคาบ (Week 07–13, Laravel 13)

| Week | หัวข้อ | สิ่งที่ใช้ได้ในโปรเจกต์ |
|---|---|---|
| 07 | Basic Database / Eloquent | migration, model, `where` / `orderBy` / `firstOrFail`, layout หลายชั้น (`layouts.main` → `xxx.main`) |
| 08 | Database Manipulation | search term (`SearchableController`), pagination (`paginate`, `withQueryString()->links()`), create/update/delete, `#[Fillable]` |
| 09 | Relationships | `hasMany` / `belongsTo` / `belongsToMany`, pivot table, `withCount`, `with` (กัน N+1) |
| 10 | Updating with Relationship | `associate()`, `attach()` / `detach()`, `whereDoesntHave`, `@selected` |
| 11 | Session | flash `->with('status', ...)`, `@session`, bookmarks (ปุ่ม Back) |
| 12 | Authentication & Authorization | `Auth::attempt`, logout, middleware `auth`, Policies, `Gate::authorize`, `@can`, `@auth`, `cache.headers`, role ADMIN/USER |
| 13 | Errors Handling | `try/catch`, `QueryException` → `errorInfo[2]`, `withErrors(['alert' => ...])`, `withInput()` + `old()` |

### 7.2 Library ที่ "ถือว่าสอนแล้ว" → ใช้นับเป็น R6 ไม่ได้
- `nyholm/psr7`, `symfony/psr-http-message-bridge` (ใช้รับ request แบบ PSR-7 ตั้งแต่ Week 07)
- ทุกอย่างที่มากับ Laravel เอง (Eloquent, Blade, Validator, Auth, Gate, Carbon, pagination ฯลฯ)
- ที่พูดถึงในคาบ: `laravel/scout` (full-text search)
- CSS ของรายวิชา (`lec04-styles.css`, `sematic-ui.css`) — ไม่ใช่ PHP library อยู่แล้ว
- ⚠️ ยังไม่ได้ดูเนื้อหา Week 01–06 — ตรวจอีกครั้งว่าไม่มี library ที่จะเลือกอยู่ในช่วงนั้น

### 7.3 แนวทางเขียนโค้ดให้ตรงกับที่เรียน
- รับข้อมูลผ่าน `ServerRequestInterface` (`getQueryParams()` / `getParsedBody()`)
- route ใช้ **code** ของ record เป็นพารามิเตอร์ (เช่น `/activities/AC001`) ไม่ใช้ id
- FK ตั้งผ่าน `associate()` ห้ามเซ็ต `xxx_id` ตรง ๆ
- ทุกฟอร์ม POST มี `@csrf`, ปุ่ม Delete ใช้ form แยก + `form="..."`
- `role`, `email` ไม่ใส่ใน `#[Fillable]` ของ User
- route คงที่ (เช่น `/create`, `/self`) ต้องวาง **ก่อน** route ที่มี `{param}`

### 7.4 ตัวอย่างที่เรียน (`my-db`) กับกฎ "ห้ามใช้โปรเจกต์จากแล็บ"
- `my-db` (Product – Shop – Category – User) ใช้เป็น **แบบอย่างวิธีเขียน** ได้
- โปรเจกต์กลุ่มต้อง **สร้างใหม่** (`laravel new`), **เนื้อเรื่องใหม่**, เขียน model/controller/view ใหม่เอง — ห้าม copy โปรเจกต์ `my-db` มาเปลี่ยนชื่อ
- ข้อที่ต่างจาก `my-db` แน่นอน: ต้องมี **public routes** (ใน `my-db` ทุกหน้าต้อง login) และต้องมี **PHP library ภายนอก**
- ถามอาจารย์ให้ชัดว่าใช้โครงสร้างแบบเดียวกันได้แค่ไหน

### 7.5 แผนของกลุ่ม
ดูไฟล์ **`PROJECT_PLAN.md`** — ระบบสมัครเข้าร่วมกิจกรรม (Activity Registration)
