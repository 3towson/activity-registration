# ลงมือทำ Activity Registration ด้วยตัวเอง

## คู่มือภาษาไทยสำหรับ Member 1 · Mac

**จาก namespace และ use → หน้ารายการ → CRUD → สมัคร/ยกเลิก → ทดสอบ → ส่งงาน**

สำหรับผู้เริ่มต้นที่อยากเขียนเองและอธิบายโค้ดได้ ไม่ต้องรอถามทีละข้อความ

โครงการ: `3towson/activity-registration`  
Branch: `member-1/activities`  
จัดทำ: 22 ตุลาคม 2026  
ตรวจตัวอย่างกับ Laravel 13.38.0 ที่ติดตั้งใน repository

เอกสารนี้เป็นบทเรียน คุณยังต้องลงมือพิมพ์ แก้ไข และทดสอบในโปรเจกต์จริง ไม่มีการนำโค้ดเฉลยในเล่มไปแทนไฟล์แอปของคุณโดยอัตโนมัติ

**วิธีเรียน:** อ่านเหตุผล → ลองเขียนก่อน → ดูเฉลยตรวจงาน → ทดสอบ → commit แล้วค่อยไปบทถัดไป

[TOC]

# 0 · แผนการเรียนและสิ่งที่ถือว่าเสร็จ

งานของคุณคือ “กิจกรรม” ทั้งเส้นทาง ตั้งแต่คนทั่วไปดูข้อมูล ผู้ดูแลจัดการกิจกรรม ไปจนถึงสมาชิกสมัครและยกเลิก สมาชิกคนที่ 2 ทำหมวดหมู่/CSS/PDF ticket ส่วนสมาชิกคนที่ 3 ทำผู้จัด/บัญชี/หน้าแรก/กิจกรรมของฉัน

| ช่วง | ผลที่ต้องเห็น | บท |
|---|---|---|
| เข้าใจโครงสร้าง | รู้ว่า URL ไปถึง HTML ได้อย่างไร | 1–3 |
| อ่านข้อมูล | รายการ ค้นหา กรอง แบ่งหน้า รายละเอียด | 4–7 |
| จัดการข้อมูล | ADMIN สร้าง แก้ไข ลบได้ | 8–12 |
| จัดการการสมัคร | USER สมัคร/ยกเลิกตามกฎได้ | 13–15 |
| ส่งมอบ | ทดสอบครบ อธิบายได้ และ push branch | 16–19 |

แบ่งเรียนหลายช่วงได้ หยุดที่ checkpoint แล้ว commit ไม่จำเป็นต้องจบในครั้งเดียว

## กติกาการใช้เฉลย

โค้ดในกรอบ “เฉลย” เป็นตัวอย่างที่ทำงานร่วมกันได้ ใช้ตรวจหลังลองเอง อย่าอ่านข้ามคำอธิบายแล้ววางทั้งหมด หากไม่เข้าใจ ให้เขียนบันทึกภาษาตัวเองข้างนอกโค้ดว่าแต่ละบรรทัดทำอะไร

- เมื่อบอก “เพิ่มเมธอด” ให้เพิ่มภายในปีกกาของ class เดิม ไม่สร้าง class ซ้ำ
- เมื่อบอก “แทนเมธอดเดิม” ให้ลบเมธอดเก่าชื่อนั้นก่อน อย่าให้มีเมธอดชื่อซ้ำ
- `namespace` และ `use` อยู่ด้านบนของไฟล์ นอก class
- Blade แต่ละกรอบที่ระบุว่า “ไฟล์เต็ม” คือเนื้อหาทั้งไฟล์นั้น
- เส้นทางไฟล์ทั้งหมดเริ่มจากโฟลเดอร์ repository

## ฐานร่วมที่มีแล้ว

มี Activity/Category/Organizer/User, migrations, seeders, login/logout, CSS, layout และหน้าแรกตรวจฐานร่วมแล้ว ให้ใช้ของเดิม งานใหม่ของคุณเพิ่มเป็นหลักใน ActivityController, ActivityPolicy, Activity model, routes/activities.php และ views/activities

อย่าแก้ SearchableController หรือไฟล์ของเพื่อนเพื่อแก้ปัญหาที่เกิดจาก controller ของเรา อ่านและเรียกใช้สิ่งที่มีอยู่ก่อน

**ขอบเขตคำว่าเสร็จในเล่ม:** งาน Member 1 ครบตาม checklist ทีม ปุ่ม PDF จะแสดงเมื่อเพื่อนเพิ่ม route ticket แล้ว การสร้าง PDF ticket และการเผยแพร่เว็บเป็นงานนอกบทเรียนนี้

# 1 · ตรวจเครื่องและเข้าใจเส้นทางของข้อมูล

เปิด VS Code ที่ `~/Developer/activity-registration` เปิด terminal สองช่อง ช่องแรกเปิดเว็บ ช่องที่สองใช้ Git และ Artisan

```bash
cd ~/Developer/activity-registration
php -v
composer -V
git branch --show-current
git status --short
```

ควรอยู่ branch `member-1/activities` ถ้าอยู่ branch อื่น ให้ตรวจว่างานค้างเป็นของ branch ไหนก่อนสลับ ผู้ใช้เคยติดตั้งและเปิดเว็บแล้ว ไม่ต้องสร้าง `.env` หรือ key ใหม่ทุกครั้ง

```bash
php artisan serve
```

เปิด `http://127.0.0.1:8000` และลอง login ADMIN/USER รหัส `1234` ตาม README หากเซิร์ฟเวอร์แจ้งพอร์ตอื่น ให้ใช้ URL ที่ terminal แสดง

## คิดตามหนึ่ง request

`GET /activities/list` → route → `ActivityController::list()` → `Activity` model/query → database → Blade view → HTML → browser

- Route เลือกว่าจะเรียกเมธอดไหน
- Controller ประสานงานและเลือกข้อมูล
- Model แทนข้อมูลและความสัมพันธ์ในฐานข้อมูล
- View แสดงผล ไม่ควรเป็นที่รับผิดชอบบันทึกข้อมูล

## แก้จุดที่เรียนค้าง: namespace กับ use

ไฟล์ ActivityController ปัจจุบันตอนตรวจพบมี `namespace` สองบรรทัด บรรทัดที่สองเป็น `namespace App\Models\Activity;` ซึ่งเปลี่ยน namespace ของคลาสด้านล่าง แทนที่จะนำชื่อ model มาใช้งาน

**ลองเอง:** เหลือ namespace ของ controller เพียงบรรทัดเดียว แล้วเปลี่ยนบรรทัดที่ชี้ model เป็น `use` จากนั้น import Builder

เฉลยโครงเริ่มต้นของ `app/Http/Controllers/ActivityController.php`:

```php
<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Database\Eloquent\Builder;

class ActivityController extends SearchableController
{
    public function getQuery(): Builder
    {
        return Activity::orderBy('code');
    }
}
```

## อ่านไวยากรณ์ทีละส่วน

| ส่วน | ความหมาย |
|---|---|
| `namespace App\Http\Controllers;` | คลาสนี้อยู่ใน namespace ของ controller |
| `use App\Models\Activity;` | ชื่อสั้น Activity หมายถึง model นี้ |
| `extends SearchableController` | สืบทอดตัวช่วยค้นหาจากคลาสแม่ |
| `public function` | ประกาศเมธอดที่ภายนอกเรียกได้ |
| `getQuery()` | ชื่อเมธอด; วงเล็บว่างแปลว่าไม่มีพารามิเตอร์ |
| `: Builder` | ต้องส่งคืนวัตถุคำสั่ง query |
| `return` | ส่งค่ากลับให้ผู้เรียก แล้วจบเมธอด |
| `Activity::` | เริ่มเรียกผ่านชื่อคลาส |
| `->` | เรียกเมธอด/อ่าน property ของวัตถุ |
| `;` | จบคำสั่ง PHP |

SearchableController มี `abstract function getQuery(): Builder;` จึงบังคับให้คลาสลูกประกาศเมธอดนี้เอง เรายังไม่ใส่ `get()` เพราะคลาสแม่ต้องเพิ่มเงื่อนไขค้นหาก่อนรัน query

**ฝึกคิด:** เปลี่ยนการเรียงตาม code ให้เป็นวันที่ได้อย่างไร? คำตอบคือเปลี่ยน argument เป็น `'date'` ไม่ใช่เปลี่ยนชื่อ `orderBy`

**Checkpoint:** รัน `php -l app/Http/Controllers/ActivityController.php` ต้องไม่มี syntax error การตรวจนี้ยังไม่พิสูจน์ว่า query หรือหน้าเว็บทำงานครบ

# 2 · อ่าน Activity และความสัมพันธ์ก่อนใช้

เปิด `app/Models/Activity.php` แล้วหา `category()`, `organizer()`, `participants()` ไม่ต้องเขียนใหม่ เพราะฐานร่วมมีแล้ว

- กิจกรรมหนึ่งอยู่ในหนึ่งหมวด: `belongsTo(Category::class)`
- กิจกรรมหนึ่งมีหนึ่งผู้จัด: `belongsTo(Organizer::class)`
- กิจกรรมหนึ่งมีผู้สมัครหลายคน และคนหนึ่งสมัครหลายกิจกรรม: `belongsToMany(User::class)`

ตาราง `activity_user` เก็บคู่ activity_id/user_id พร้อมเวลาสมัคร `withTimestamps()` ทำให้ Eloquent จัดการเวลาบนตารางกลางให้

## ฝึกอ่าน property กับเมธอด

`$activity->category->name` อ่านชื่อหมวดที่สัมพันธ์กัน ส่วน `$activity->participants()` เริ่ม query ความสัมพันธ์เพื่อค้นต่อ/นับ/attach/detach

## เพิ่มตัวช่วยสถานะสองตัว

นโยบายในเล่มนี้กำหนดว่า “วันที่จัดตั้งแต่วันนี้ขึ้นไป” ยังเปิดรับในแง่วันที่ เพราะข้อมูลมีเพียงวัน ไม่มีเวลาปิดรับ ดังนั้นวันเดียวกับกิจกรรมยังสมัครและยกเลิกได้ เมื่อวันจัดน้อยกว่าวันนี้จึงถือว่าจบแล้ว

**ลองเอง:** เขียน `isOpen(): bool` เปรียบเทียบวันจัดกับวันนี้ และ `isFull(): bool` เปรียบเทียบจำนวนสมัครกับ capacity

เฉลย: เพิ่มก่อนปีกกาสุดท้ายของ Activity class:

```php
    public function isOpen(): bool
    {
        return $this->date->greaterThanOrEqualTo(today());
    }

    public function isFull(): bool
    {
        $count = $this->participants_count ?? $this->participants()->count();

        return $count >= $this->capacity;
    }
```

`??` หมายถึงใช้ค่าด้านขวาเมื่อด้านซ้ายเป็น null หรือยังไม่มีค่า หากโหลด `participants_count` มากับ query แล้วก็ใช้ค่านั้น หากยังไม่มีจึงนับผ่าน relationship

**ฝึกคิด:** สมัครแล้ว 2 รับ 2 เต็มหรือไม่? เต็ม เพราะใช้ `>=` ไม่ใช่ `>`

## โหลดข้อมูลโดยไม่ query ซ้ำทีละแถว

แทน `getQuery()` เดิมใน ActivityController ด้วย:

```php
    public function getQuery(): Builder
    {
        return Activity::with(['category', 'organizer'])
            ->withCount('participants')
            ->orderBy('code');
    }
```

`with()` เตรียมโหลดหมวด/ผู้จัดเป็นชุด และ `withCount()` เพิ่ม participant count มากับผล query ลดการยิง query นับ/อ่านความสัมพันธ์แยกทุกแถว ตัวช่วย `isFull()` จึงใช้ count ที่โหลดมาได้

**Checkpoint:** ไฟล์ Activity ยังมี relationship เดิมครบ และ getQuery ยังคืน Builder ไม่มี `get()` ต่อท้าย

# 3 · กำหนดสิทธิ์ด้วย Policy

Authentication ตอบว่า “ใครเข้าสู่ระบบอยู่” ส่วน authorization ตอบว่า “คนนี้ทำสิ่งนี้ได้ไหม” แค่ซ่อนปุ่มยังกัน request ที่ส่งเองไม่ได้ จึงต้องตรวจใน controller ด้วย

| งาน | Guest | USER | ADMIN |
|---|---|---|---|
| ดูรายการ/รายละเอียด | ได้ | ได้ | ได้ |
| สร้าง แก้ ลบ ดูผู้สมัคร | ไป login | 403 | ได้ |
| สมัคร/ยกเลิก | ไป login | ตามกฎวันที่/จำนวน/การสมัคร | 403 |

สร้างไฟล์ด้วย Artisan ถ้ายังไม่มี:

```bash
php artisan make:policy ActivityPolicy --model=Activity
```

Laravel ค้นพบ policy จากชื่อและตำแหน่ง `app/Policies/ActivityPolicy.php` ที่ตรงกับ model ได้ ให้เขียนเมธอดด้านล่างแทน scaffold เดิม เพื่อไม่ให้เหลือเมธอดที่ return ว่าง

**ลองเองก่อน:** เขียนเมธอด `create` ให้คืนผล `$user->isAdministrator()` แล้วเปรียบเทียบกับเฉลยไฟล์เต็ม:

```php
<?php

namespace App\Policies;

use App\Models\Activity;
use App\Models\User;

class ActivityPolicy
{
    public function list(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Activity $activity): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->isAdministrator();
    }

    public function update(User $user, Activity $activity): bool
    {
        return $user->isAdministrator();
    }

    public function delete(User $user, Activity $activity): bool
    {
        return $user->isAdministrator();
    }

    public function viewParticipants(User $user, Activity $activity): bool
    {
        return $user->isAdministrator();
    }

    public function register(User $user, Activity $activity): bool
    {
        return $user->isMember() && $activity->isOpen() && !$activity->isFull();
    }

    public function cancel(User $user, Activity $activity): bool
    {
        return $user->isMember() && $activity->isOpen()
            && $activity->participants()->whereKey($user->id)->exists();
    }
}
```

`?User` แปลว่าเป็น User หรือ null ก็ได้ จึงให้ Guest ใช้ public policy ได้ ส่วน cancel ตรวจด้วยว่าผู้ใช้นี้สมัครจริง ไม่ใช่เพียงมี role USER

การสมัครซ้ำแยกตรวจใน controller แบบที่เรียนในคาบ register policy จึงตรวจ role/วัน/จำนวน ไม่ตรวจการสมัครซ้ำ

**คำถามตรวจความเข้าใจ:** ทำไมเมธอด view ไม่ใช้ `User` อย่างเดียว? เพราะ Guest ไม่มี User และจะถูกปฏิเสธก่อนถึงการอนุญาตในเมธอด

**Checkpoint:** รัน `php -l app/Policies/ActivityPolicy.php` และตรวจว่าทุกเมธอดคืน boolean

# 4 · หน้าแรกของเรา: รายการกิจกรรมแบบง่าย

เริ่มจากข้อมูลออกจอให้ได้ก่อน แล้วค่อยเพิ่ม search และปุ่มต่าง ๆ

## 4.1 เพิ่ม imports ใน ActivityController

เพิ่มด้านบน นอก class และอย่าเพิ่มซ้ำกับที่มี:

```php
use App\Models\Category;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Psr\Http\Message\ServerRequestInterface;
```

โปรเจกต์ใช้ PSR-7 request ตามในคาบ อ่าน query string เช่น `?term=Laravel` ผ่าน `getQueryParams()` ส่วน form POST จะใช้ `getParsedBody()` ในบทหลัง

## 4.2 เตรียม criteria และเพิ่ม list

เพิ่มเมธอดสองตัวต่อจาก getQuery ภายใน class:

```php
    public function prepareCriteria(array $criteria): array
    {
        $criteria = parent::prepareCriteria($criteria);

        return [
            'term' => is_string($criteria['term']) ? $criteria['term'] : null,
            'category' => is_string($criteria['category'] ?? null)
                ? $criteria['category'] : '',
            'upcoming' => ($criteria['upcoming'] ?? '') === '1',
        ];
    }
```

```php
    public function list(ServerRequestInterface $request): View
    {
        Gate::authorize('list', Activity::class);
        $criteria = $this->prepareCriteria($request->getQueryParams());
        session()->put('bookmarks.activities.view', url()->full());

        return view('activities.list', [
            'activities' => $this->search($criteria)->paginate(self::MAX_ITEMS),
            'criteria' => $criteria,
            'categories' => Category::orderBy('code')->get(),
        ]);
    }
```

`parent::prepareCriteria()` เรียกคลาสแม่เพื่อเติมค่า term จากนั้นเราจัดรูปแบบ category และ upcoming ให้แน่นอน `is_string()` กันค่าผิดรูป เช่นคนส่ง term เป็น array

`$this` คือ controller ตัวปัจจุบัน `search()` เป็นเมธอดที่สืบทอดจาก SearchableController มันเรียก getQuery แล้วเพิ่ม filter ก่อนส่ง Builder กลับมา `paginate(self::MAX_ITEMS)` รัน query แบบแบ่งหน้า 5 รายการตามค่าคงที่ในคลาสแม่

อาร์เรย์ที่ส่งให้ `view()` เป็นตัวแปรของหน้า Blade เช่น key `activities` กลายเป็น `$activities`

`session()->put(...)` จด URL รายการปัจจุบันไว้ให้ปุ่ม Back ภายหลัง

## 4.3 เปิด route เฉพาะที่เขียนแล้ว

แทนเนื้อหา `routes/activities.php` ด้วยไฟล์เริ่มต้นนี้ ยังไม่เปิด route ของเมธอดที่ยังไม่ได้ทำ:

```php
<?php

use App\Http\Controllers\ActivityController;
use Illuminate\Support\Facades\Route;

Route::controller(ActivityController::class)
    ->prefix('/activities')
    ->name('activities.')
    ->group(static function (): void {
        Route::get('/list', 'list')->name('list');
        Route::any('', static fn () => redirect()->route('activities.list'))
            ->name('index');
    });
```

ชื่อ route เต็มได้จาก prefix ของชื่อ `activities.` รวมกับ `list` จึงเป็น `activities.list` ส่วน URL ได้จาก `/activities` รวมกับ `/list`

`static fn () => ...` คือฟังก์ชันสั้นที่คืนค่าหลังลูกศร ตรงนี้คืน redirect ไปหน้ารายการ

## 4.4 สร้าง Blade รายการฉบับเริ่มต้น

สร้างโฟลเดอร์ `resources/views/activities` และไฟล์ `list.blade.php` พิมพ์ไฟล์เต็มนี้:

```blade
@extends('layouts.main', ['title' => 'กิจกรรมทั้งหมด'])

@section('content')
    <table class="app-cmp-data-list">
        <thead><tr><th>รหัส</th><th>ชื่อ</th><th>หมวด</th><th>สมัคร / รับ</th></tr></thead>
        <tbody>
            @forelse ($activities as $activity)
                <tr>
                    <td>{{ $activity->code }}</td>
                    <td>{{ $activity->name }}</td>
                    <td>{{ $activity->category->name }}</td>
                    <td>{{ $activity->participants_count }} / {{ $activity->capacity }}</td>
                </tr>
            @empty
                <tr><td colspan="4">ไม่พบกิจกรรม</td></tr>
            @endforelse
        </tbody>
    </table>
    {{ $activities->withQueryString()->links() }}
@endsection
```

`@extends` ใช้ layout ร่วม `@section('content')` เติมส่วนเนื้อหา `{{ ... }}` แสดงข้อมูลพร้อม escape HTML และ `@forelse` วนรายการโดยมีกรณีไม่พบข้อมูล

**Checkpoint:** เปิด `/activities` ต้อง redirect ไป `/activities/list` เห็น 5 กิจกรรมและปุ่มเปลี่ยนหน้า เมนู “กิจกรรม” ใน layout จะปรากฏเองเมื่อมี route index ลองเปิดโดย logout ด้วย

ถ้าติด: 404 ให้ดู `php artisan route:list --path=activities`; View not found ให้ตรวจชื่อไฟล์; Class not found ให้ตรวจ namespace/use

```bash
git add app/Models/Activity.php app/Policies/ActivityPolicy.php app/Http/Controllers/ActivityController.php routes/activities.php resources/views/activities/list.blade.php
git commit -m "Build public activity list"
```

# 5 · รายละเอียดกิจกรรมและความหมายของ code

ลิงก์ใช้ `AC001` ซึ่งเป็น code ไม่ใช่ id ที่ database สร้างเอง SearchableController มี `find(string $code)` ที่ค้นด้วย `where('code', $code)->firstOrFail()` อยู่แล้ว หากไม่พบ Laravel ตอบ 404

## 5.1 เพิ่ม view ใน controller

เพิ่ม import `use Illuminate\Support\Facades\Auth;` แล้วเพิ่มเมธอด:

```php
    public function view(string $activity): View
    {
        $item = $this->find($activity);
        Gate::authorize('view', $item);

        return view('activities.view', [
            'activity' => $item,
            'registered' => Auth::check()
                && $item->participants()->whereKey(Auth::id())->exists(),
        ]);
    }
```

ตัวแปร `$activity` ใน argument คือข้อความจาก URL ส่วน `$item` คือ model ที่ค้นพบ เราส่งเข้า Blade ด้วย key `activity` เพื่อให้ view ใช้ `$activity`

`Auth::check() && ...` จะไม่ค้นการสมัครต่อเมื่อยังไม่ login เพราะ `&&` หยุดประเมินเมื่อด้านซ้ายเป็น false

## 5.2 เพิ่ม route รายละเอียด

ใน group เดิมของ routes/activities.php เพิ่มบรรทัดนี้ **หลัง** route `/list` และ index:

```php
Route::get('/{activity}', 'view')->name('view');
```

เมื่อถึงบทสร้างฟอร์ม ต้องวาง route คงที่ `/create` ก่อน `/{activity}` เพื่อไม่ให้ Laravel มองคำว่า create เป็น code

## 5.3 สร้าง view ฉบับเล็ก

สร้าง `resources/views/activities/view.blade.php`:

```blade
@extends('layouts.main', ['title' => $activity->name])

@section('content')
    <dl class="app-cmp-data-detail">
        <dt>รหัส</dt><dd>{{ $activity->code }}</dd>
        <dt>หมวด</dt><dd>{{ $activity->category->name }}</dd>
        <dt>ผู้จัด</dt><dd>{{ $activity->organizer->name }}</dd>
        <dt>วันที่</dt><dd>{{ $activity->date->format('d/m/Y') }}</dd>
        <dt>สถานที่</dt><dd>{{ $activity->location }}</dd>
        <dt>สมัคร / รับ</dt><dd>{{ $activity->participants_count }} / {{ $activity->capacity }}</dd>
        <dt>รายละเอียด</dt><dd>{{ $activity->description }}</dd>
    </dl>
    <a href="{{ session('bookmarks.activities.view') ?? route('activities.index') }}">กลับรายการ</a>
@endsection
```

**ลองเอง:** เปลี่ยนชื่อกิจกรรมในตาราง list ให้เป็น `<a>` ลิงก์ไป `activities.view` โดยส่ง code ผ่าน key `activity` เฉลยอยู่ในรายการฉบับเต็มบทถัดไป

**Checkpoint:** เปิด AC001 ได้ เปิด `/activities/NOTFOUND` ได้ 404 และ Back กลับ URL รายการเดิมได้ การแสดง `date->format()` ใช้ได้เพราะ model cast date เป็นวัตถุวันที่ไว้แล้ว

# 6 · เพิ่มค้นหา กรองหมวด และกิจกรรมที่ยังไม่จบ

เป้าหมายของคำค้นคือค้นได้จาก code/ชื่อ/สถานที่/ชื่อหมวด/ชื่อผู้จัด คลาสแม่มี code/name และแยกหลายคำให้อยู่แล้ว จึงขยายแทนเขียนระบบใหม่

## 6.1 เข้าใจ closure ก่อนอ่านโค้ด

`static fn (Builder $query, string $word) => ...` เป็นฟังก์ชันสั้นที่รับ query กับคำค้นและส่งผลต่อ query นั้น ส่วน `function (...) use ($term) { ... }` เป็นฟังก์ชันหลายบรรทัดที่นำตัวแปรภายนอกเข้ามาใช้ผ่าน `use` ตรงนี้เป็นคนละหน้าที่กับ import ที่หัวไฟล์

## 6.2 เพิ่มตัวเลือกค้นหา

เพิ่ม import `use Illuminate\Database\Eloquent\Relations\Relation;` และเพิ่มสองเมธอดนี้ใน controller:

```php
    public function getFilterOptions(): array
    {
        $options = parent::getFilterOptions();
        $options['term']['location'] = static fn (Builder $query, string $word)
            => $query->where('location', 'LIKE', "%{$word}%");
        $options['term']['category'] = static fn (Builder $query, string $word)
            => $query->whereHas('category', static fn (Builder $related)
                => $related->where('name', 'LIKE', "%{$word}%"));
        $options['term']['organizer'] = static fn (Builder $query, string $word)
            => $query->whereHas('organizer', static fn (Builder $related)
                => $related->where('name', 'LIKE', "%{$word}%"));

        return $options;
    }
```

```php
    public function filter(
        Builder|Relation $query,
        array $criteria,
        array $filterOptions,
    ): void {
        parent::filter($query, $criteria, $filterOptions);

        if (($criteria['category'] ?? '') !== '') {
            $query->whereHas('category', static fn (Builder $category)
                => $category->where('code', $criteria['category']));
        }

        if ($criteria['upcoming'] ?? false) {
            $query->whereDate('date', '>=', today());
        }
    }
```

- เรียก parent เพื่อคง code/name และ logic แยกคำของทีมไว้
- `LIKE "%{$word}%"` หมายถึงมีคำนั้นอยู่ตรงใดก็ได้ของข้อความ
- `whereHas('category', ...)` จำกัดกิจกรรมโดยใช้เงื่อนไขในตารางหมวด
- `Builder|Relation` รับได้ทั้ง query ปกติและ query จาก relationship เพื่อให้เพื่อนนำ filter ไปใช้หน้าหมวด/ผู้จัดได้
- `whereDate(..., '>=', today())` รวมวันนี้ตามกติกาเดียวกับ isOpen

**ลองเอง:** อธิบายด้วยคำตัวเองว่า why ค้น “จิตอาสา” เจอกิจกรรมที่ชื่อไม่ประกอบด้วยคำนั้นได้ คำตอบคือเราค้นผ่านชื่อหมวดด้วย

## 6.3 เตรียม layout ของหน้ากิจกรรม

สร้าง `resources/views/activities/main.blade.php` ตามไฟล์นี้ก่อน จากนั้นบท 9 จะเพิ่มปุ่มสร้าง:

```blade
@extends('layouts.main')

@section('header')
    <nav class="app-cmp-links-bar">
        <a class="app-cl-link" href="{{ route('activities.index') }}">กิจกรรมทั้งหมด</a>
    </nav>
@endsection
```

นี่เป็นการสืบทอด Blade สองชั้น: list → activities.main → layouts.main `@parent` ใน list จะเก็บ header ของ activities.main ไว้ก่อนเพิ่มฟอร์มค้นหา

## 6.4 แทน list.blade.php ด้วยฉบับเต็ม

**ไฟล์เต็ม:**

```blade
@extends('activities.main', ['title' => 'กิจกรรมทั้งหมด'])

@section('header')
    @parent
    <search>
        <form action="{{ route('activities.list') }}" method="get" class="app-cmp-search-form">
            <fieldset>
                <legend>ค้นหากิจกรรม</legend>
                <label>คำค้น <input name="term" value="{{ $criteria['term'] }}" /></label>
                <label>หมวดหมู่
                    <select name="category">
                        <option value="">ทุกหมวดหมู่</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->code }}"
                                @selected($criteria['category'] === $category->code)>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </label>
                <label>
                    <input type="checkbox" name="upcoming" value="1" @checked($criteria['upcoming']) />
                    เฉพาะกิจกรรมวันนี้และอนาคต
                </label>
                <button class="app-cl-button" type="submit">ค้นหา</button>
                <a href="{{ route('activities.list') }}">ล้างคำค้น</a>
            </fieldset>
        </form>
    </search>
@endsection

@section('content')
    <table class="app-cmp-data-list">
        <thead>
            <tr><th>รหัส</th><th>ชื่อกิจกรรม</th><th>หมวด</th><th>ผู้จัด</th>
                <th>วันที่</th><th>สถานที่</th><th>สมัคร / รับ</th><th>สถานะ</th></tr>
        </thead>
        <tbody>
            @forelse ($activities as $activity)
                <tr>
                    <th class="app-cl-code">{{ $activity->code }}</th>
                    <td><a href="{{ route('activities.view', ['activity' => $activity->code]) }}">
                        {{ $activity->name }}</a></td>
                    <td>{{ $activity->category->name }}</td>
                    <td>{{ $activity->organizer->name }}</td>
                    <td>{{ $activity->date->format('d/m/Y') }}</td>
                    <td>{{ $activity->location }}</td>
                    <td>{{ $activity->participants_count }} / {{ $activity->capacity }}</td>
                    <td>{{ !$activity->isOpen() ? 'จบแล้ว' : ($activity->isFull() ? 'เต็มแล้ว' : 'เปิดรับ') }}</td>
                </tr>
            @empty
                <tr><td colspan="8">ไม่พบกิจกรรม</td></tr>
            @endforelse
        </tbody>
    </table>
    {{ $activities->withQueryString()->links() }}
@endsection
```

GET form เปลี่ยน query string จึงคัดลอก URL ผลค้นหาไปเปิดใหม่ได้ ไม่ต้องมี `@csrf` เพราะไม่เปลี่ยนข้อมูล ส่วน POST form ในบทหลังต้องมี

`withQueryString()` ทำให้ลิงก์หน้าที่ 2 ยังเก็บ term/category/upcoming และปุ่มล้างคำค้นกลับไป URL ที่ไม่มี filters

**Checkpoint:** ค้น Laravel, ค้นสถานที่, ค้นจิตอาสา, เลือก CT004 ต้องไม่พบกิจกรรม และติ๊ก upcoming ต้องไม่แสดงวันที่ก่อนวันนี้ ลองค้นแล้วเปลี่ยนหน้า จากรายละเอียดกด Back ต้องกลับพร้อมคำค้น

```bash
git add app/Http/Controllers/ActivityController.php resources/views/activities
git commit -m "Add activity search filters and detail page"
```

# 7 · อ่านผลผิดพลาดและรู้ว่าต้องแก้ชั้นไหน

ก่อนทำการบันทึก ให้รู้วิธีแยกปัญหา เพื่อจะอ่านหนังสือต่อได้โดยไม่ต้องเดาทุกครั้ง

| อาการ | ตรวจอะไรเป็นอันดับแรก |
|---|---|
| 404 | URL/route หรือ code ไม่มีใน database |
| 403 | policy และ role ไม่ผ่าน ไม่ใช่ปัญหา CSS |
| 419 | POST form ขาด CSRF หรือ session เก่า |
| View not found | ชื่อ activities.xxx ตรงกับไฟล์ xxx.blade.php หรือไม่ |
| Undefined variable | key ที่ controller ส่งมาตรงชื่อใน view หรือไม่ |
| Class not found | namespace/import/ชื่อไฟล์ โดยเฉพาะ namespace บรรทัดที่สอง |
| Method getQuery missing | class ลูกยังไม่ implement abstract method |
| Cannot redeclare | มีเมธอดชื่อซ้ำหลังเพิ่มเฉลยหรือไม่ |
| Database connection refused | MySQL ทำงานอยู่หรือไม่ และ .env ตรงหรือไม่ |
| Route activities.xxx not defined | เปิดปุ่มก่อนเพิ่ม route หรือสะกดชื่อต่างจากทีม |

คำสั่งที่ช่วยตรวจ:

```bash
php artisan route:list --path=activities
php artisan optimize:clear
php artisan view:cache
```

`view:cache` ตรวจว่า Blade compile ได้ แต่ไม่ยืนยันว่าทุกตัวแปรมีค่าในการใช้งานจริง หลังเปลี่ยนไฟล์แล้วต้องเปิดหน้าจริงด้วย หากสงสัย cache ให้ `php artisan view:clear`

ดู error ล่าสุดที่ `storage/logs/laravel.log` และดู terminal ของ `artisan serve` อ่านชนิด error กับตำแหน่งไฟล์ก่อน อย่าแก้หลายจุดพร้อมกัน

**แบบฝึกหัด:** ถ้าตารางมีข้อมูลแต่ลิงก์รายละเอียด 404 ให้ตรวจ route และ code ก่อนแก้ model ความสัมพันธ์

# 8 · เตรียมการบันทึก: request, fill และ associate

Create/Update ต้องอ่านข้อมูลจาก form ตรวจข้อมูล แล้วส่งให้ model โครงนี้ใช้ HTML constraints + การตรวจด้วย controller + try/catch ตามข้อตกลงทีม ไม่ใช้ Laravel Validator ใน CRUD

HTML required ช่วยผู้ใช้ใน browser แต่คนสามารถส่ง request เองได้ เราจึงตรวจค่าพื้นฐานใน server ด้วย เช่น capacity ต้องเป็นจำนวนเต็มบวก และวันที่ต้องมีจริง

## 8.1 เพิ่ม imports สำหรับบทบันทึก

เพิ่มหัว ActivityController โดยไม่ซ้ำ:

```php
use App\Models\Organizer;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
```

User และ AuthorizationException จะใช้ในบทสมัครภายหลัง ไม่ได้แทน imports เดิมที่ใช้ list/view

## 8.2 เพิ่มตัวช่วยอ่านข้อมูล

เพิ่ม private method นี้ใน class:

```php
    private function readActivityData(ServerRequestInterface $request): array
    {
        $body = (array) $request->getParsedBody();
        $data = [];

        foreach (['code', 'name', 'category', 'organizer', 'date', 'location',
            'capacity', 'description'] as $field) {
            if (!is_string($body[$field] ?? null) || trim($body[$field]) === '') {
                throw new \RuntimeException('กรุณากรอกข้อมูลให้ครบ');
            }
            $data[$field] = trim($body[$field]);
        }

        if (strlen($data['code']) > 64
            || preg_match('/\A[A-Za-z0-9_-]+\z/', $data['code']) !== 1) {
            throw new \RuntimeException('รหัสต้องเป็น A-Z, a-z, 0-9, _ หรือ - และไม่เกิน 64 ตัว');
        }
        if (in_array(strtolower($data['code']), ['list', 'create'], true)) {
            throw new \RuntimeException('รหัสนี้สงวนไว้สำหรับ URL ของระบบ');
        }
        if (mb_strlen($data['name']) > 255 || mb_strlen($data['location']) > 255) {
            throw new \RuntimeException('ชื่อและสถานที่ต้องไม่เกิน 255 ตัวอักษร');
        }
        $capacity = filter_var($data['capacity'], FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 1, 'max_range' => 4294967295]]);
        if ($capacity === false) {
            throw new \RuntimeException('จำนวนรับต้องเป็นจำนวนเต็มบวก');
        }
        $date = \DateTimeImmutable::createFromFormat('!Y-m-d', $data['date']);
        if ($date === false || $date->format('Y-m-d') !== $data['date']) {
            throw new \RuntimeException('วันที่ไม่ถูกต้อง');
        }
        $data['capacity'] = $capacity;

        return $data;
    }
```

`private` หมายถึงเมธอดภายใน ใช้จากเมธอดอื่นของ controller นี้ ข้อมูล `$body` มาจาก POST form แต่เราเลือกเฉพาะ field ที่ต้องการ ไม่รับ user_id/role จากผู้ใช้

อ่านเงื่อนไขทีละเรื่อง:

1. ช่องที่กำหนดต้องเป็น string และไม่ว่างหลัง trim
2. code ใช้ตัวอักษรอังกฤษ ตัวเลข ขีดกลาง/ขีดล่าง เพื่อใช้ใน URL ได้ตรงไปตรงมา เช่น AC001 ปฏิเสธรหัส list/create ที่ชน route และแนะนำใช้รูปแบบ AC ตามทีม
3. name/location จำกัดความยาวตาม schema
4. capacity เป็นจำนวนเต็ม 1 ถึงช่วง unsigned integer ของ MySQL
5. วันที่ต้องสร้างและแปลงกลับมาเป็นข้อความเดิมได้ จึงปฏิเสธ 30 กุมภาพันธ์

`throw new RuntimeException(...)` ส่งข้อผิดพลาดออกไปให้ catch ใน create/update รับ ไม่ต้องเขียน redirect ซ้ำในตัวช่วย

`preg_match` คือการตรวจรูปแบบข้อความ ส่วน FILTER_VALIDATE_INT เป็นตัวช่วย PHP ไม่ใช่ Laravel Validator หากยังอ่าน regex ไม่คล่อง ให้อ่านเงื่อนไขภาษาคนด้านบนและทดลองรหัส AC099/AC-099/รหัสที่มีช่องว่าง

## 8.3 เพิ่มตัวช่วยเติม model

```php
    private function fillActivity(Activity $item, array $data): void
    {
        $item->fill(array_intersect_key($data, array_flip([
            'code', 'name', 'date', 'location', 'capacity', 'description',
        ])));
        $category = Category::where('code', $data['category'])->firstOrFail();
        $organizer = Organizer::where('code', $data['organizer'])->firstOrFail();
        $item->category()->associate($category);
        $item->organizer()->associate($organizer);
    }
```

`array_intersect_key` เลือกเฉพาะช่องที่อนุญาตให้ fill และ `array_flip` เปลี่ยนรายชื่อช่องให้เป็น keys ใช้คู่กับ Fillable ของ model อีกชั้น

`associate()` ตั้งความสัมพันธ์ให้ Eloquent จัดการ foreign key เราจึงไม่ใส่ category_id/organizer_id ลง Fillable และไม่เซ็ต FK ตรง ๆ ฟอร์มส่งรหัสหมวด/ผู้จัดมา จากนั้น controller หา model จริงก่อน associate

**Checkpoint:** ยังไม่บันทึกจากบทนี้ ตัวช่วยทั้งสองถูกเรียกจาก create/update ในบทถัดไป ลองอธิบายความต่างระหว่าง fill (ตั้งค่าใน model) กับ save (บันทึก database)

# 9 · สร้างกิจกรรม: ฟอร์มและ POST

การสร้างใช้สอง request: GET แสดงฟอร์ม และ POST รับค่าบันทึก จึงมีสองเมธอด

## 9.1 เพิ่ม controller methods

**ลองเอง:** เริ่ม showCreateForm ด้วย Gate ที่ตรวจ create แล้วดึง categories/organizers ส่งให้ view

เฉลย:

```php
    public function showCreateForm(): View
    {
        Gate::authorize('create', Activity::class);

        return view('activities.create-form', [
            'categories' => Category::orderBy('code')->get(),
            'organizers' => Organizer::orderBy('code')->get(),
        ]);
    }
```

```php
    public function create(ServerRequestInterface $request): RedirectResponse
    {
        Gate::authorize('create', Activity::class);

        try {
            $data = $this->readActivityData($request);
            $item = new Activity;
            $this->fillActivity($item, $data);
            $item->save();

            return redirect()->route('activities.view', ['activity' => $item->code])
                ->with('status', "กิจกรรม {$item->code} ถูกสร้างแล้ว");
        } catch (QueryException $e) {
            return redirect()->back()->withInput()
                ->withErrors(['alert' => $e->errorInfo[2] ?? $e->getMessage()]);
        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                ->withErrors(['alert' => $e->getMessage()]);
        }
    }
```

Gate อยู่นอก try เพื่อให้ไม่มีสิทธิ์ได้ 403 ตามเดิม ไม่ถูก catch กลายเป็นกล่องแจ้งเตือนทั่วไป `new Activity` สร้าง model ในหน่วยความจำ `save()` จึงบันทึกจริง

QueryException ถูก catch ก่อน Exception เพื่ออ่านข้อความจาก database เช่น code ซ้ำ จากนั้น catch ทั่วไปจับกฎที่เรา throw เอง `withInput()` เก็บค่าฟอร์มไว้ให้ `old()` ส่วน status/alert ถูก layout หลักแสดงให้แล้ว

## 9.2 เพิ่ม routes ในตำแหน่งที่ถูก

ใน group activities เดิม วาง group นี้หลัง ANY index และก่อน GET /{activity}:

```php
Route::middleware('auth')->group(static function (): void {
    Route::post('', 'create')->name('create');
    Route::get('/create', 'showCreateForm')->name('create-form');
});
```

ต้องมี auth ก่อนเรียก action และมี Gate ภายใน action อีกชั้น ให้คง ANY index ก่อน POST create ตามโครง route ของ repository นี้ ตรวจ route ที่ลงทะเบียนจริงหลังแก้ อย่าเพิ่ม route POST create ซ้ำใน group อื่น

## 9.3 สร้าง create-form.blade.php

**ไฟล์เต็ม `resources/views/activities/create-form.blade.php`:**

```blade
@extends('activities.main', ['title' => 'สร้างกิจกรรม'])

@section('content')
    <form method="post" class="app-cmp-data-form" action="{{ route('activities.create') }}">
        @csrf
        <div>
            <label for="code">รหัส</label>
            <input id="code" name="code" type="text" maxlength="64" pattern="[A-Za-z0-9_-]+" required
                value="{{ old('code', '') }}" />
        </div>
        <div>
            <label for="name">ชื่อกิจกรรม</label>
            <input id="name" name="name" type="text" maxlength="255" required
                value="{{ old('name', '') }}" />
        </div>
        <div>
            <label for="date">วันที่จัด</label>
            <input id="date" name="date" type="date"  required
                value="{{ old('date', '') }}" />
        </div>
        <div>
            <label for="location">สถานที่</label>
            <input id="location" name="location" type="text" maxlength="255" required
                value="{{ old('location', '') }}" />
        </div>
        <div>
            <label for="capacity">จำนวนรับ</label>
            <input id="capacity" name="capacity" type="number" min="1" max="4294967295" required
                value="{{ old('capacity', 1) }}" />
        </div>
        <div>
            <label for="category">หมวดหมู่</label>
            <select id="category" name="category" required>
                <option value="">เลือกหมวดหมู่</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->code }}"
                        @selected(old('category', '') === $category->code)>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="organizer">ผู้จัด</label>
            <select id="organizer" name="organizer" required>
                <option value="">เลือกผู้จัด</option>
                @foreach ($organizers as $organizer)
                    <option value="{{ $organizer->code }}"
                        @selected(old('organizer', '') === $organizer->code)>
                        {{ $organizer->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="description">รายละเอียด</label>
            <textarea id="description" name="description" rows="5" required>{{ old('description', '') }}</textarea>
        </div>
        <div class="app-cmp-form-actions">
            <button class="app-cl-button app-cl-filled app-cl-primary">บันทึก</button>
        </div>
    </form>
    <div class="app-cmp-form-secondary-actions">
        <a class="app-cl-button"
            href="{{ session('bookmarks.activities.view') ?? route('activities.index') }}">ยกเลิก</a>
    </div>
@endsection
```

name ของ input เป็น key ที่ controller อ่าน `@csrf` สร้าง token สำหรับ POST ส่วน `old('name', '')` เลือกค่าที่กรอกล่าสุดเมื่อบันทึกผิดพลาด และ `@selected` เลือก option เดิมให้

## 9.4 เพิ่มปุ่มสร้างใน activities/main.blade.php

เมื่อ route create-form มีแล้ว แทน main ด้วยฉบับเต็ม:

```blade
@extends('layouts.main')

@section('header')
    <nav class="app-cmp-links-bar">
        <a class="app-cl-link" href="{{ route('activities.index') }}">กิจกรรมทั้งหมด</a>
        @can('create', \App\Models\Activity::class)
            <a class="app-cl-button app-cl-filled app-cl-primary"
                href="{{ route('activities.create-form') }}">สร้างกิจกรรม</a>
        @endcan
    </nav>
@endsection
```

`@can` ตรวจ policy เพื่อแสดงปุ่ม ADMIN เท่านั้น แต่การกันจริงยังเป็น Gate ใน controller

**Checkpoint:** ADMIN สร้าง AC099 ได้และเห็นข้อความสำเร็จ ลองสร้าง code ซ้ำ ต้องได้ alert และฟอร์มยังมีค่าที่กรอก Guest เปิด /activities/create ต้องไป login USER ต้องได้ 403

**ทดสอบค่าผิด:** ลอง capacity 0 และวันที่ผิดผ่าน request จำลองในบท 17 อย่าถอด required ในไฟล์จริงเพื่อทดสอบแล้วลืมใส่กลับ

# 10 · แก้ไขกิจกรรมโดยไม่ลดจำนวนรับต่ำกว่าคนสมัคร

ฟอร์มแก้ต้องเติมค่าจาก model เมื่อยังไม่มี old input และบันทึกกลับ record เดิมที่ค้นด้วย code จาก URL

## 10.1 เพิ่มเมธอดใน controller

```php
    public function showUpdateForm(string $activity): View
    {
        $item = $this->find($activity);
        Gate::authorize('update', $item);

        return view('activities.update-form', [
            'activity' => $item,
            'categories' => Category::orderBy('code')->get(),
            'organizers' => Organizer::orderBy('code')->get(),
        ]);
    }
```

```php
    public function update(
        ServerRequestInterface $request,
        string $activity,
    ): RedirectResponse {
        $item = $this->find($activity);
        Gate::authorize('update', $item);

        try {
            $data = $this->readActivityData($request);
            $saved = DB::transaction(function () use ($activity, $data): Activity {
                $item = Activity::where('code', $activity)->lockForUpdate()->firstOrFail();
                $item->loadCount('participants');
                if ($data['capacity'] < $item->participants_count) {
                    throw new \RuntimeException(
                        "จำนวนรับต้องไม่น้อยกว่าจำนวนผู้สมัคร ({$item->participants_count} คน)",
                    );
                }
                $this->fillActivity($item, $data);
                $item->save();

                return $item;
            });

            return redirect()->route('activities.view', ['activity' => $saved->code])
                ->with('status', "กิจกรรม {$saved->code} ถูกแก้ไขแล้ว");
        } catch (QueryException $e) {
            return redirect()->back()->withInput()
                ->withErrors(['alert' => $e->errorInfo[2] ?? $e->getMessage()]);
        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                ->withErrors(['alert' => $e->getMessage()]);
        }
    }
```

`DB::transaction()` ทำให้คำสั่งในฟังก์ชันสำเร็จร่วมกันหรือย้อนกลับเมื่อเกิดข้อผิดพลาด `lockForUpdate()` บน MySQL ล็อกแถวกิจกรรมระหว่าง transaction เพื่อจัดลำดับการแก้ capacity กับการสมัครซึ่งจะใช้ lock แถวเดียวกันในบท 13

ข้อสำคัญคือโหลด count หลังได้ lock แล้ว และเทียบ capacity ใหม่ก่อน save ไม่ใช้ count ที่ค้างจากหน้าเว็บ

`use ($activity, $data)` ทำให้ closure เข้าถึงตัวแปรข้างนอก ส่วน `$saved` คือ model ที่ transaction ส่งกลับ ถ้าเปลี่ยน code ก็ redirect ด้วย code ใหม่

## 10.2 ขยาย auth group เดิม

ภายใน auth group เดิม เพิ่ม:

```php
Route::prefix('/{activity}')->group(static function (): void {
    Route::post('', 'update')->name('update');
    Route::get('/update', 'showUpdateForm')->name('update-form');
});
```

บทถัดไปเพิ่ม route ลงใน prefix group นี้ อย่าสร้าง update route ซ้ำ

## 10.3 สร้าง update-form.blade.php

**ไฟล์เต็ม `resources/views/activities/update-form.blade.php`:**

```blade
@extends('activities.main', ['title' => 'แก้ไขกิจกรรม'])

@section('content')
    <form method="post" class="app-cmp-data-form" action="{{ route('activities.update', ['activity' => $activity->code]) }}">
        @csrf
        <div>
            <label for="code">รหัส</label>
            <input id="code" name="code" type="text" maxlength="64" pattern="[A-Za-z0-9_-]+" required
                value="{{ old('code', $activity->code) }}" />
        </div>
        <div>
            <label for="name">ชื่อกิจกรรม</label>
            <input id="name" name="name" type="text" maxlength="255" required
                value="{{ old('name', $activity->name) }}" />
        </div>
        <div>
            <label for="date">วันที่จัด</label>
            <input id="date" name="date" type="date"  required
                value="{{ old('date', $activity->date->format('Y-m-d')) }}" />
        </div>
        <div>
            <label for="location">สถานที่</label>
            <input id="location" name="location" type="text" maxlength="255" required
                value="{{ old('location', $activity->location) }}" />
        </div>
        <div>
            <label for="capacity">จำนวนรับ</label>
            <input id="capacity" name="capacity" type="number" min="1" max="4294967295" required
                value="{{ old('capacity', $activity->capacity) }}" />
        </div>
        <div>
            <label for="category">หมวดหมู่</label>
            <select id="category" name="category" required>
                <option value="">เลือกหมวดหมู่</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->code }}"
                        @selected(old('category', $activity->category->code) === $category->code)>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="organizer">ผู้จัด</label>
            <select id="organizer" name="organizer" required>
                <option value="">เลือกผู้จัด</option>
                @foreach ($organizers as $organizer)
                    <option value="{{ $organizer->code }}"
                        @selected(old('organizer', $activity->organizer->code) === $organizer->code)>
                        {{ $organizer->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="description">รายละเอียด</label>
            <textarea id="description" name="description" rows="5" required>{{ old('description', $activity->description) }}</textarea>
        </div>
        <div class="app-cmp-form-actions">
            <button class="app-cl-button app-cl-filled app-cl-primary">บันทึก</button>
        </div>
    </form>
    <div class="app-cmp-form-secondary-actions">
        <a class="app-cl-button"
            href="{{ session('bookmarks.activities.view') ?? route('activities.index') }}">ยกเลิก</a>
    </div>
@endsection
```

เทียบกับ create-form: action ต้องส่ง code เดิมใน URL และค่าเริ่มต้นของ old เปลี่ยนจากว่างเป็นค่าของ model ช่อง date ใช้ Y-m-d เพราะ input type=date ต้องการรูปแบบนี้

**Checkpoint:** ADMIN เปิด `/activities/AC099/update` แล้วแก้สถานที่ได้ ลองแก้ AC001 เป็น capacity 2 ขณะที่มีผู้สมัคร 3 คน ต้องปฏิเสธพร้อม alert และ database ยังรับ 100 คนตามเดิม

ปุ่ม Cancel ในเล่มกลับ bookmark รายการ เพื่อรักษาคำค้น/หน้าเดิม ไม่จำเป็นต้องล้าง session ทุกครั้ง

# 11 · ลบกิจกรรมและเข้าใจ cascade

การลบเปลี่ยนข้อมูลจึงใช้ POST + CSRF ตามข้อตกลงทีม ไม่ทำเป็นลิงก์ GET การลบ activity ต้องทำให้แถวการสมัครที่เกี่ยวข้องถูกลบตามด้วย

## 11.1 เพิ่มเมธอด

```php
    public function delete(string $activity): RedirectResponse
    {
        $item = $this->find($activity);
        Gate::authorize('delete', $item);

        try {
            $item->delete();

            return redirect()->route('activities.list')
                ->with('status', "กิจกรรม {$activity} ถูกลบแล้ว");
        } catch (QueryException $e) {
            return redirect()->back()
                ->withErrors(['alert' => $e->errorInfo[2] ?? $e->getMessage()]);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['alert' => $e->getMessage()]);
        }
    }
```

## 11.2 เพิ่ม route ใน prefix /{activity} ภายใต้ auth

```php
Route::post('/delete', 'delete')->name('delete');
```

ปุ่มลบฉบับเต็มจะอยู่ในหน้ารายละเอียดบท 15 ถ้าต้องการทดสอบตอนนี้ เพิ่มฟอร์มชั่วคราวท้าย section content ของหน้ารายละเอียด:

```blade
@can('delete', $activity)
    <form id="app-form-delete" method="post"
        action="{{ route('activities.delete', ['activity' => $activity->code]) }}">
        @csrf
    </form>
    <button type="submit" form="app-form-delete"
        onclick="return confirm('ยืนยันการลบ?')">ลบกิจกรรม</button>
@endcan
```

form แยกจากปุ่มได้ด้วย attribute `form` ไม่ต้องซ้อน form ซึ่ง HTML ไม่อนุญาต

## cascade กับ restrict ต่างกันอย่างไร

- `activity_user` มี FK cascade: ลบกิจกรรมแล้วลบการสมัครของกิจกรรมนั้นตาม
- activities ชี้ category/organizer แบบ restrict: ลบหมวดหรือผู้จัดที่ยังมีกิจกรรมอยู่ไม่ได้
- การลบกิจกรรมไม่ควรลบ User, Category หรือ Organizer ตาม

**Checkpoint:** สร้างกิจกรรมทดสอบแล้วลบได้ มี status กลับรายการ ทดสอบ cascade กับ AC001 ตอนท้ายหลังทดสอบข้ออื่นแล้ว เพราะการลบทำให้ข้อมูลตัวอย่างเปลี่ยน

```bash
git add app/Http/Controllers/ActivityController.php routes/activities.php resources/views/activities
git commit -m "Add admin activity create update and delete"
```

# 12 · ทบทวน CRUD ก่อนทำการสมัคร

หยุดเพิ่มโค้ดหนึ่งรอบ แล้วตรวจเส้นทางตั้งแต่ต้นจนจบ:

| Request | สิ่งที่ controller ทำ | ผล |
|---|---|---|
| GET create | Gate → ดึง dropdown → view | ฟอร์มว่าง |
| POST create | Gate → อ่านข้อมูล → fill/associate → save | รายละเอียด + status |
| GET update | หา code → Gate → ดึง dropdown → view | ฟอร์มมีค่าเดิม |
| POST update | Gate → อ่านข้อมูล → lock/count → fill/save | รายละเอียดใหม่ + status |
| POST delete | หา code → Gate → delete | รายการ + status |

## อธิบายให้ตัวเองฟัง

1. ทำไมข้อมูล form ไม่ควรถูกส่งเข้า model ทั้งหมดโดยไม่เลือก field?
2. ทำไม Gate ต้องตรวจใน controller แม้ปุ่มถูกซ่อนแล้ว?
3. ทำไมเก็บ code หมวดจาก form แล้วต้อง firstOrFail หา Category อีกครั้ง?
4. ถ้า code ซ้ำใครเป็นคนตรวจขั้นสุดท้าย?
5. ทำไม update ต้องตรวจ capacity กับจำนวนสมัครปัจจุบัน?

แนวคำตอบ: เลือก field เพื่อควบคุมข้อมูลที่แก้ได้; request ส่งเองได้; ต้องอ้าง model จริงก่อน associate; unique constraint ใน database; จำนวนผู้สมัครเปลี่ยนจากตอนเปิดฟอร์มได้

## ความหมายของ error ที่คาดไว้

403 คือไม่มีสิทธิ์ ส่วน alert คือทำรายการไม่สำเร็จ เช่นข้อมูลซ้ำ/ไม่ครบ ในตัวอย่างการเรียนนี้ QueryException ใช้ข้อความจาก database ตามแบบในคาบ หากนำไปเผยแพร่จริงควรเปลี่ยนเป็นข้อความที่อ่านง่ายและเก็บรายละเอียดใน log ก่อน

**Checkpoint:** อย่าไปบทสมัครหาก USER ยังเปิดฟอร์ม admin ได้ หรือ create/update ยังไม่คืนค่า old input เมื่อผิดพลาด

# 13 · สมัครกิจกรรม: attach และป้องกันสมัครซ้ำ

การสมัครไม่สร้าง User หรือ Activity ใหม่ แต่เพิ่มหนึ่งแถวใน `activity_user` เช่น activity_id=3, user_id=2 ความสัมพันธ์ N–N จึงเหมาะกับงานนี้

## กฎก่อนสมัคร

ผู้ใช้ต้องเป็น USER, วันกิจกรรมยังไม่ผ่าน, จำนวนสมัครยังไม่เต็ม และต้องไม่สมัครซ้ำ เราใช้ policy สำหรับ role/วัน/จำนวน ส่วน controller ใช้ whereDoesntHave + firstOrFail ตามแบบ addShop ในคาบ และ PK คู่ใน database ป้องกันซ้ำอีกชั้น

## ลองเขียนก่อน

เริ่มจากอธิบายลำดับเป็นภาษาไทยห้าบรรทัด: หา activity → ตรวจสิทธิ์ → หา activity ที่คนนี้ยังไม่สมัคร → attach → แจ้งสำเร็จ จากนั้นค่อยอ่านเฉลย

เพิ่มเมธอด register:

```php
    public function register(string $activity): RedirectResponse
    {
        $item = $this->find($activity);
        Gate::authorize('register', $item);
        $user = Auth::user();
        assert($user instanceof User);

        try {
            DB::transaction(function () use ($activity, $user): void {
                $item = Activity::where('code', $activity)->lockForUpdate()->firstOrFail();
                Gate::authorize('register', $item);
                $available = Activity::whereDoesntHave('participants',
                    static fn (Builder $query) => $query->whereKey($user->id))
                    ->where('code', $activity)->firstOrFail();
                $available->participants()->attach($user->id);
            });

            return redirect()->route('activities.view', ['activity' => $activity])
                ->with('status', "สมัครกิจกรรม {$item->name} สำเร็จ");
        } catch (AuthorizationException $e) {
            throw $e;
        } catch (QueryException $e) {
            return redirect()->back()
                ->withErrors(['alert' => $e->errorInfo[2] ?? $e->getMessage()]);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['alert' => 'สมัครไม่สำเร็จ: คุณอาจสมัครกิจกรรมนี้แล้ว']);
        }
    }
```

อ่านส่วนยากแยกทีละชั้น:

- `Auth::user()` เลือกคนที่ login จริง ไม่รับ user_id จาก form
- `whereDoesntHave('participants', ...)` เลือกเฉพาะกิจกรรมที่ไม่มีผู้ใช้นี้ในความสัมพันธ์
- `whereKey($user->id)` ระบุ User ที่ต้องการค้น ไม่ใช่ activity id
- `firstOrFail()` โยน exception เมื่อสมัครอยู่แล้ว จึงเข้ากล่อง alert แทนเพิ่มซ้ำ
- `attach($user->id)` เพิ่มแถวใน pivot และ timestamps

## ทำไมตรวจ policy สองรอบและใช้ lock

รอบแรกให้กรณีไม่มีสิทธิ์ตอบ 403 ตั้งแต่ต้น รอบสองอยู่หลัง lock เพื่ออ่านวันที่/จำนวนล่าสุด หากสองคนแย่งที่นั่งสุดท้าย ทั้งคู่ต้อง lock แถว Activity เดียวกันบน MySQL คนถัดไปจึงตรวจจำนวนหลังคนแรก commit แล้ว

PK คู่ป้องกันคนเดิมสมัครซ้ำ แต่ไม่ป้องกันคนละคนสมัครเกิน capacity ด้วยตัวมันเอง จึงต้องมี lock + ตรวจ count ภายใน transaction และทุกเส้นทางที่เปลี่ยน capacity/การสมัครควรใช้กติกาเดียวกัน

catch AuthorizationException ต้อง throw ต่อ เพื่อคง 403 ไม่เปลี่ยนเป็นกล่อง alert ส่วน duplicate query เข้าข้อผิดพลาดแบบที่ทีมกำหนด

## เพิ่ม route ใน prefix /{activity} ภายใต้ auth

```php
Route::post('/register', 'register')->name('register');
```

**Checkpoint:** หลังเพิ่มปุ่มในบท 15 ให้ user@activity.test สมัคร AC003 จาก 2/3 เป็น 3/3 ได้ ส่วน AC004 เต็มอยู่แล้วต้อง 403 แม้ส่ง POST เอง

ถ้าส่งซ้ำไปกิจกรรมที่ยังไม่เต็ม เช่น AC001 ซึ่ง user นี้สมัครแล้ว จะได้ alert จาก duplicate lookup หากกิจกรรมเต็มแล้ว policy จะปฏิเสธ 403 ก่อน ทั้งสองกรณีห้ามเพิ่มแถวใหม่

# 14 · ยกเลิกและรายชื่อผู้สมัคร

## 14.1 ยกเลิกด้วย detach

`detach()` ลบเฉพาะความเชื่อมโยงใน pivot ไม่ลบ User หรือ Activity ตัว policy cancel ตรวจว่าเป็น USER, วันยังไม่ผ่าน และสมัครอยู่จริง

เพิ่มเมธอด:

```php
    public function cancel(string $activity): RedirectResponse
    {
        $item = $this->find($activity);
        Gate::authorize('cancel', $item);

        try {
            DB::transaction(function () use ($activity): void {
                $locked = Activity::where('code', $activity)->lockForUpdate()->firstOrFail();
                Gate::authorize('cancel', $locked);
                $locked->participants()->detach(Auth::id());
            });

            return redirect()->route('activities.view', ['activity' => $activity])
                ->with('status', "ยกเลิกการสมัคร {$item->name} แล้ว");
        } catch (AuthorizationException $e) {
            throw $e;
        } catch (QueryException $e) {
            return redirect()->back()
                ->withErrors(['alert' => $e->errorInfo[2] ?? $e->getMessage()]);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['alert' => $e->getMessage()]);
        }
    }
```

เพิ่ม route ใน prefix /{activity} ภายใต้ auth:

```php
Route::post('/cancel', 'cancel')->name('cancel');
```

**ลองคิด:** ทำไม AC008 ยกเลิกไม่ได้แม้ user สมัครอยู่? เพราะวันผ่านแล้ว จึงไม่ผ่าน isOpen

## 14.2 ADMIN ดูรายชื่อและเวลาสมัคร

เพิ่มเมธอด:

```php
    public function viewParticipants(
        ServerRequestInterface $request,
        string $activity,
    ): View {
        $item = $this->find($activity);
        Gate::authorize('viewParticipants', $item);
        $params = $request->getQueryParams();
        $term = is_string($params['term'] ?? null) ? trim($params['term']) : '';
        $query = $item->participants()->orderBy('name');
        if ($term !== '') {
            $query->where(static function (Builder $query) use ($term): void {
                $query->where('name', 'LIKE', "%{$term}%")
                    ->orWhere('email', 'LIKE', "%{$term}%");
            });
        }

        return view('activities.view-participants', [
            'activity' => $item,
            'participants' => $query->paginate(self::MAX_ITEMS),
            'term' => $term,
        ]);
    }
```

`where(function...)` จัดกลุ่มเงื่อนไขชื่อ OR อีเมล เพื่อไม่ให้ OR หลุดออกไปปนเงื่อนไขความสัมพันธ์ ส่วน `$participant->pivot->created_at` คือเวลาสมัครกิจกรรมนี้ ไม่ใช่เวลาสร้างบัญชี User

เพิ่ม route ใน prefix /{activity} ภายใต้ auth:

```php
Route::get('/participants', 'viewParticipants')->name('view-participants');
```

**ไฟล์เต็ม `resources/views/activities/view-participants.blade.php`:**

```blade
@extends('activities.main', ['title' => 'ผู้สมัคร: ' . $activity->name])

@section('header')
    @parent
    <search>
        <form method="get" class="app-cmp-search-form"
            action="{{ route('activities.view-participants', ['activity' => $activity->code]) }}">
            <fieldset>
                <legend>ค้นหาผู้สมัคร</legend>
                <label>ชื่อหรืออีเมล <input name="term" value="{{ $term }}" /></label>
                <button class="app-cl-button">ค้นหา</button>
            </fieldset>
        </form>
    </search>
@endsection

@section('content')
    <table class="app-cmp-data-list">
        <thead><tr><th>ชื่อ</th><th>อีเมล</th><th>เวลาสมัคร</th></tr></thead>
        <tbody>
            @forelse ($participants as $participant)
                <tr>
                    <td>{{ $participant->name }}</td>
                    <td>{{ $participant->email }}</td>
                    <td>{{ $participant->pivot->created_at->format('d/m/Y H:i') }}</td>
                </tr>
            @empty
                <tr><td colspan="3">ไม่พบผู้สมัคร</td></tr>
            @endforelse
        </tbody>
    </table>
    {{ $participants->withQueryString()->links() }}
    <a class="app-cl-button"
        href="{{ route('activities.view', ['activity' => $activity->code]) }}">กลับรายละเอียด</a>
@endsection
```

**Checkpoint:** ADMIN เปิด `/activities/AC001/participants` เห็นเวลาสมัคร ค้นชื่อ/อีเมลได้ USER เปิด URL เดียวกันต้อง 403

# 15 · ประกอบหน้ารายละเอียดและตรวจ routes ทั้งชุด

ตอนนี้เมธอดรองรับปุ่มต่าง ๆ มีครบแล้ว จึงแทนหน้ารายละเอียดฉบับเล็กด้วยฉบับเต็ม อย่าเก็บฟอร์มลบชั่วคราวจากบท 11 ซ้ำ

## 15.1 รายละเอียดฉบับเต็ม

**ไฟล์เต็ม `resources/views/activities/view.blade.php`:**

```blade
@extends('activities.main', ['title' => $activity->name])

@section('content')
    <dl class="app-cmp-data-detail">
        <dt>รหัส</dt><dd>{{ $activity->code }}</dd>
        <dt>หมวดหมู่</dt><dd>{{ $activity->category->name }}</dd>
        <dt>ผู้จัด</dt><dd>{{ $activity->organizer->name }}</dd>
        <dt>วันที่</dt><dd>{{ $activity->date->format('d/m/Y') }}</dd>
        <dt>สถานที่</dt><dd>{{ $activity->location }}</dd>
        <dt>สมัคร / รับ</dt><dd>{{ $activity->participants_count }} / {{ $activity->capacity }}</dd>
        <dt>รายละเอียด</dt><dd style="white-space: pre-line">{{ $activity->description }}</dd>
        <dt>สถานะ</dt>
        <dd>{{ !$activity->isOpen() ? 'จบแล้ว' : ($activity->isFull() ? 'เต็มแล้ว' : 'เปิดรับ') }}</dd>
    </dl>

    <div class="app-cmp-form-actions">
        @guest
            <a class="app-cl-button" href="{{ route('login') }}">เข้าสู่ระบบเพื่อสมัคร</a>
        @endguest

        @auth
            @if (\Auth::user()->isMember())
                @if ($registered)
                    <p>คุณสมัครกิจกรรมนี้แล้ว</p>
                    @can('cancel', $activity)
                        <form method="post" action="{{ route('activities.cancel', ['activity' => $activity->code]) }}">
                            @csrf
                            <button class="app-cl-button app-cl-warning">ยกเลิกการสมัคร</button>
                        </form>
                    @else
                        <p>จบแล้ว ยกเลิกการสมัครไม่ได้</p>
                    @endcan
                    @if (Route::has('activities.ticket'))
                        <a class="app-cl-button"
                            href="{{ route('activities.ticket', ['activity' => $activity->code]) }}">ดาวน์โหลดบัตร</a>
                    @endif
                @else
                    @can('register', $activity)
                        <form method="post" action="{{ route('activities.register', ['activity' => $activity->code]) }}">
                            @csrf
                            <button class="app-cl-button app-cl-filled app-cl-primary">สมัครกิจกรรม</button>
                        </form>
                    @else
                        <p>{{ !$activity->isOpen() ? 'จบแล้ว' : 'เต็มแล้ว' }}</p>
                    @endcan
                @endif
            @endif
        @endauth

        @can('update', $activity)
            <a class="app-cl-button"
                href="{{ route('activities.update-form', ['activity' => $activity->code]) }}">แก้ไข</a>
        @endcan
        @can('viewParticipants', $activity)
            <a class="app-cl-button"
                href="{{ route('activities.view-participants', ['activity' => $activity->code]) }}">รายชื่อผู้สมัคร</a>
        @endcan
        @can('delete', $activity)
            <form id="app-form-delete" method="post"
                action="{{ route('activities.delete', ['activity' => $activity->code]) }}">
                @csrf
            </form>
            <button type="submit" form="app-form-delete" class="app-cl-button app-cl-warning"
                onclick="return confirm('ยืนยันการลบกิจกรรม?')">ลบกิจกรรม</button>
        @endcan
    </div>
    <div class="app-cmp-form-secondary-actions">
        <a class="app-cl-button"
            href="{{ session('bookmarks.activities.view') ?? route('activities.index') }}">กลับรายการ</a>
    </div>
@endsection
```

ลำดับการแสดง USER สำคัญ: ถ้าสมัครแล้วให้แจ้งและแสดง cancel ตามสิทธิ์ ก่อนตรวจปุ่มสมัคร ไม่เช่นนั้นผู้ที่สมัครแล้วในกิจกรรมเต็มอาจเห็นเพียง “เต็มแล้ว” โดยไม่รู้สถานะตนเอง

`Route::has('activities.ticket')` ทำให้ทำงานแยกจากเพื่อนได้ ขณะ route ticket ยังไม่มีให้ซ่อนลิงก์ พอสมาชิกคนที่ 2 เพิ่มแล้วลิงก์จะแสดง ผู้ใช้ต้องสมัครอยู่แล้วจึงเห็น ส่วนสิทธิ์ดาวน์โหลดจริงเป็นหน้าที่ TicketController ของเพื่อน

`white-space: pre-line` รักษาการขึ้นบรรทัดของ description โดยยังใช้ `{{ }}` escape HTML

## 15.2 ตรวจ routes ฉบับสุดท้าย

เปรียบเทียบ `routes/activities.php` ของคุณกับไฟล์เต็มนี้ หากเคยเพิ่มตามบทก่อนแล้ว อย่าเพิ่มซ้ำ สามารถจัดรูปให้ตรงชุดนี้ได้:

```php
<?php

use App\Http\Controllers\ActivityController;
use Illuminate\Support\Facades\Route;

Route::controller(ActivityController::class)
    ->prefix('/activities')
    ->name('activities.')
    ->group(static function (): void {
        Route::get('/list', 'list')->name('list');
        Route::any('', static fn () => redirect()->route('activities.list'))
            ->name('index');

        Route::middleware('auth')->group(static function (): void {
            Route::post('', 'create')->name('create');
            Route::get('/create', 'showCreateForm')->name('create-form');
            Route::prefix('/{activity}')->group(static function (): void {
                Route::post('', 'update')->name('update');
                Route::get('/update', 'showUpdateForm')->name('update-form');
                Route::post('/delete', 'delete')->name('delete');
                Route::get('/participants', 'viewParticipants')->name('view-participants');
                Route::post('/register', 'register')->name('register');
                Route::post('/cancel', 'cancel')->name('cancel');
            });
        });

        Route::get('/{activity}', 'view')->name('view');
    });
```

ไฟล์ตัวอย่างเริ่มต้นของทีมใช้ Router/Arr เพื่อหาชื่อ group แบบอัตโนมัติ เล่มนี้ใช้ชื่อ `activities.list` ตรง ๆ เพื่อให้มือใหม่ตามง่าย ผลชื่อ route ตรงกัน

```bash
php artisan route:list --path=activities
```

ควรมี 11 routes: index, list, view, create-form, create, update-form, update, delete, register, cancel และ view-participants ส่วน ticket อยู่ไฟล์ของสมาชิกคนที่ 2

**Checkpoint:** Guest เห็นลิงก์ login USER เห็นสมัครหรือยกเลิกตามสถานะ ADMIN เห็นแก้ไข/ลบ/รายชื่อผู้สมัครและไม่มีปุ่มสมัคร

```bash
git add app/Http/Controllers/ActivityController.php app/Models/Activity.php app/Policies/ActivityPolicy.php routes/activities.php resources/views/activities
git commit -m "Add registration cancellation and participant list"
```

# 16 · ทดสอบด้วยตัวเองจนมั่นใจ

ใช้ฐานข้อมูลพัฒนาที่รู้ว่ามีข้อมูลอะไรอยู่ ทดสอบกรณีลบไว้ท้ายสุด วันที่ใน seeder คิดจากวันที่ seed ถ้าผ่านไปหลายวัน สถานะบางกิจกรรมอาจเปลี่ยนตามจริง

บัญชีทดสอบทุกบัญชีใช้รหัส `1234`: admin@activity.test เป็น ADMIN, user@activity.test เป็น USER ที่สมัคร AC001/AC006/AC008 อยู่แล้ว และ user2 ถึง user5 เป็น USER เพิ่มเติม

## 16.1 Public และการนำทาง

- [ ] Logout แล้วเปิด `/activities` ไปหน้ารายการได้
- [ ] เปิด AC001 เห็นหมวด ผู้จัด วัน สถานที่ จำนวนสมัคร และ description
- [ ] ค้น code/ชื่อ/สถานที่/ชื่อหมวด/ชื่อผู้จัดได้
- [ ] เลือกหมวด CT004 ไม่พบรายการ
- [ ] upcoming ไม่รวมวันก่อนวันนี้
- [ ] เปลี่ยนหน้าแล้วยังมีคำค้น
- [ ] จากรายการที่มี filter เข้า detail แล้ว Back กลับ filter/หน้าเดิม
- [ ] code ไม่มีจริงได้ 404

## 16.2 ADMIN CRUD

- [ ] Guest เปิด create ไป login USER เปิด create/update/participants ได้ 403
- [ ] ADMIN สร้าง AC099 ได้ มี status และข้อมูลสัมพันธ์ถูกหมวด/ผู้จัด
- [ ] สร้าง AC099 ซ้ำ มี alert และค่าเดิมยังอยู่
- [ ] แก้ AC099 ได้ dropdown/date/description มีค่าเดิมถูกต้อง
- [ ] AC001 รับ 100 สมัคร 3 ลองแก้ให้รับ 2 ต้องถูกปฏิเสธ
- [ ] ช่องว่าง/จำนวนติดลบ/วันที่ไม่มีจริงถูกปฏิเสธ
- [ ] ทุก POST มี CSRF

## 16.3 Registration และสิทธิ์

- [ ] user@activity.test สมัคร AC003 ได้ จาก 2 เป็น 3 และเห็น “คุณสมัครกิจกรรมนี้แล้ว”
- [ ] ยกเลิก AC003 ได้ จาก 3 กลับเป็น 2
- [ ] ส่ง POST ซ้ำ AC001 ได้ alert และจำนวนสมัครไม่เพิ่ม
- [ ] AC004 เต็ม ไม่มีปุ่มสมัคร และส่ง POST เองได้ 403
- [ ] AC008 จบแล้ว ไม่มีปุ่มยกเลิก และส่ง POST เองได้ 403
- [ ] ADMIN ส่ง POST register/cancel ได้ 403
- [ ] USER ที่ไม่สมัครกิจกรรมนั้นยกเลิกไม่ได้
- [ ] ADMIN เห็นชื่อ อีเมล และเวลาจาก pivot ใน participants

### ส่ง POST เองเพื่อทดสอบปุ่มที่ซ่อน

ขณะ login บนหน้าเว็บ เปิด browser DevTools → Console พิมพ์คำสั่งนี้ด้วยตนเองเฉพาะบนเว็บ local ของคุณ:

```javascript
const token = document.querySelector('meta[name="csrf-token"]')?.content
    ?? document.querySelector('input[name="_token"]')?.value;
const response = await fetch('/activities/AC004/register', {
    method: 'POST',
    headers: {'X-CSRF-TOKEN': token, 'Accept': 'application/json'}
});
console.log(response.status);
```

USER ควรได้ 403 เพราะ AC004 เต็ม หน้า login แล้วจะมี token จากฟอร์ม logout หาก token ไม่มี ให้เปิดหน้าฟอร์ม POST ที่มี @csrf ก่อน การไม่มี token จะได้ 419 ซึ่งไม่ได้พิสูจน์ policy

การส่งซ้ำ AC001 ซึ่งคาดหวัง alert อาจถูก fetch ตาม redirect ให้ดู Network หรือเปิดหน้ารายละเอียดอีกครั้ง ไม่ใช้ HTTP 200 หลัง redirect เป็นหลักฐานว่าบันทึกสำเร็จ ให้ตรวจ session message และข้อมูลประกอบ

## 16.4 ลบและตรวจ cascade ท้ายสุด

- [ ] จด id ของ AC001 ใน phpMyAdmin
- [ ] ADMIN ลบ AC001 แล้ว activity หาย
- [ ] ตาราง activity_user ไม่มี activity_id ที่จดไว้
- [ ] User/Category/Organizer ยังอยู่

อย่าใช้ `migrate:fresh --seed` เพื่อแก้ทุก error เพราะลบตารางทั้งหมด หากต้องการ reset จริง ให้ใช้เฉพาะ database พัฒนาที่ทิ้งข้อมูลได้และตรวจชื่อ database ให้แน่ใจก่อน

**ทดสอบที่นั่งสุดท้ายบน MySQL:** เปิดสอง browser session ด้วย USER สองบัญชี แล้วส่งสมัครกิจกรรมที่เหลือหนึ่งที่ใกล้กัน ตรวจว่าผู้สมัครเพิ่มเพียงหนึ่งคน การคลิกด้วยมือช่วยสำรวจ แต่ไม่พิสูจน์ทุกจังหวะการแข่งขัน ให้ตรวจโค้ด transaction/lock ร่วมด้วย

# 17 · ตรวจโค้ดอัตโนมัติและข้อจำกัดของตัวอย่าง

บทนี้ทำหลังประกอบไฟล์ครบ ไม่ต้องเริ่มด้วย tests ก่อนเข้าใจ request ของตัวเอง

## ตรวจรูปแบบและ compilation

```bash
php -l app/Http/Controllers/ActivityController.php
php -l app/Policies/ActivityPolicy.php
php -l app/Models/Activity.php
php artisan route:list --path=activities
php artisan view:cache
php artisan view:clear
vendor/bin/pint --dirty
```

Pint จัดรูปแบบ PHP แต่ไม่แก้ logic; route:list และ view:cache ไม่แทนการ login แล้วใช้งานจริง

## ถ้าจะเขียน feature tests ต่อ

สร้างด้วย `php artisan make:test ActivityFeatureTest --phpunit` ใช้ `RefreshDatabase` และ seed ข้อมูลในฐานทดสอบเฉพาะ เช่น `activity_db_test` ตรวจ phpunit.xml และ credentials ก่อนรัน อย่าชี้ test ไป database ที่มีงานของคุณ เพราะ RefreshDatabase สามารถล้างข้อมูลได้

เคสที่ควรเขียน: Guest ดู public ได้, USER จัดการกิจกรรมไม่ได้, ADMIN CRUD, duplicate พร้อม old input, capacity ต่ำกว่าผู้สมัคร, สมัคร/ยกเลิก, กิจกรรมเต็ม/ผ่านมาแล้ว, participant search และ cascade

## สิ่งที่ตรวจให้คู่มือนี้แล้ว

ตัวอย่างเฉลยถูกนำไปประกอบในสำเนาโปรเจกต์แยกจากงานจริง แล้วทดสอบกับ Laravel 13.38.0 ผ่าน feature tests **6 เคส รวม 73 assertions** ครอบคลุม public browse/search/filter/404, role restrictions, CRUD/duplicate/old input, capacity/invalid fields, registration/cancellation และ participant/cascade รวมทั้งตรวจ routes 11 รายการและ Blade compilation

การทดสอบอัตโนมัติใช้ SQLite ในหน่วยความจำ จึงยืนยัน flow และผลลัพธ์เหล่านี้ได้ แต่ไม่ได้ยืนยันพฤติกรรม lock หรือ error message ของ MySQL และไม่ได้ทดสอบ PDF ticket ซึ่งเป็นงานเพื่อน คุณยังต้องทำ checklist บท 16 บน MySQL ของคุณ

## ข้อตกลงที่ต้องรักษาเมื่อรวมงาน

- public Policy รับ ?User; protected actions ใช้ auth + Gate
- code ใน URL; POST delete; ทุก POST มี CSRF
- status/alert ตาม layout; ฟอร์มผิดพลาดคืน old input
- associate สำหรับหมวด/ผู้จัด; attach/detach สำหรับการสมัคร
- shared SearchableController และ CSS ของเพื่อนคงอยู่
- ไม่ใช้ @include แยกฟอร์มในเล่มนี้ตามแนวคาบ หน้า create/update จึงมีส่วนคล้ายกัน
- หากเพื่อนเรียก filter/getFilterOptions ผ่าน controller ให้รักษา signature ที่รับ Builder|Relation และ array criteria ไว้

# 18 · Git, Pull Request และส่งต่องานให้ทีม

ก่อน commit อ่าน diff ทุกครั้ง เลือกไฟล์งานตัวเอง อย่าเพิ่ม `.env`, vendor หรือฐานข้อมูลลง Git

```bash
git status --short
git diff
```

เมื่อทำ checkpoint สุดท้ายครบ:

```bash
git add app/Http/Controllers/ActivityController.php app/Policies/ActivityPolicy.php app/Models/Activity.php routes/activities.php resources/views/activities
git commit -m "Complete Member 1 activity registration flow"
git push -u origin member-1/activities
```

ถ้า Git แจ้งไม่มีอะไรให้ commit แปลว่าไฟล์อาจถูกบันทึกใน commit ก่อนแล้ว ตรวจ log ไม่จำเป็นต้องสร้าง commit ว่าง

เปิด GitHub เปรียบเทียบ `member-1/activities` เข้า `main` สร้าง Pull Request และให้เพื่อน review ตามกติกาทีม ไม่ต้อง merge เองทันที

## ตัวอย่างสิ่งที่ควรเขียนใน PR ด้วยคำของคุณเอง

อธิบายว่าผู้ใช้ดู/ค้นหากิจกรรมได้ ADMIN จัดการกิจกรรมและรายชื่อได้ USER สมัคร/ยกเลิกตามวันและจำนวนได้ ระบุไฟล์สำคัญและผลทดสอบที่คุณรันจริง ไม่ใส่เครื่องหมายผ่านในสิ่งที่ยังไม่ได้ทดสอบ

## ส่งให้เพื่อนรู้

- สมาชิก 2: route `activities.view` พร้อมใช้, relationship participants มี timestamps, detail รอลิงก์ activities.ticket เมื่อ route พร้อม, getFilterOptions/filter ใช้ค้นกิจกรรมในหมวดได้
- สมาชิก 3: helper isOpen/isFull พร้อมใช้, filter ใช้กับ relationship ของ organizer ได้, route กิจกรรมใช้ code
- แจ้งกติกาว่าวันกิจกรรมเท่ากับวันนี้ยังเปิด และขอให้หน้าที่เพื่อนทำใช้ความหมายเดียวกัน

PDF คู่มือนี้เป็นเอกสารการเรียน ไม่ใช่ผลทดสอบงานที่คุณเขียนแล้ว ทุกครั้งที่แก้เฉลยด้วยตัวเองต้องตรวจผลใหม่

# 19 · เตรียมรายงานและอธิบายให้อาจารย์ฟัง

รายงานของคุณมีสี่ส่วนตามเอกสารทีม: งานที่รับผิดชอบ / วิธีพัฒนา / ไฟล์สำคัญ / ผลการทดสอบ เขียนจากสิ่งที่คุณทำจริง พร้อมภาพหรือผลลัพธ์ที่ได้

## คำถามที่ควรตอบได้ก่อนถือว่าเสร็จ

| คำถาม | แนวตอบสั้น ๆ |
|---|---|
| namespace กับ use ต่างกันอย่างไร | namespace กำหนดที่อยู่คลาส; use นำชื่อคลาสอื่นมาอ้างด้วยชื่อสั้น |
| ทำไม getQuery คืน Builder | เพื่อเพิ่มเงื่อนไขก่อนดึงผลจริง |
| Category/Organizer เกี่ยวกับ Activity อย่างไร | Activity belongsTo ทั้งสอง model |
| ทำไมการสมัครเป็นตารางกลาง | ทั้งผู้ใช้และกิจกรรมมีความสัมพันธ์หลายต่อหลาย |
| attach/detach ทำอะไร | เพิ่ม/ลบความเชื่อมโยงใน pivot |
| กันสมัครซ้ำกี่ชั้น | duplicate lookup ใน controller และ primary key คู่ใน database |
| PK คู่กันสมัครเกิน capacity ได้ไหม | ไม่ได้ ต้องตรวจ count พร้อมจัดลำดับ request ด้วย transaction/lock |
| associate ใช้ทำไม | ตั้งความสัมพันธ์ผ่าน model แทนรับ FK จาก form ตรง ๆ |
| with/withCount ใช้ทำไม | โหลดความสัมพันธ์และจำนวนเป็นชุด ลด query เพิ่มต่อแถว |
| Gate ต่างจาก @can อย่างไร | Gate กัน server request; @can เลือกสิ่งที่แสดงใน view |
| ทำไม public policy รับ ?User | Guest ไม่มี User จึงต้องยอมรับ null |
| ทำไมต้อง withInput/old | คืนค่าที่กรอกเมื่อบันทึกผิดพลาด |
| pivot created_at หมายถึงอะไร | เวลาที่ผู้ใช้สมัครกิจกรรมนี้ |
| cascade/restrict ต่างกันอย่างไร | cascade ลบแถวลูกตาม; restrict กันลบแม่ที่ยังถูกใช้อยู่ |
| ทำไม Back ใช้ bookmark | กลับ URL รายการเดิมพร้อมคำค้นและเลขหน้า |

## ซ้อม demo ตามลำดับ

1. Guest ค้นกิจกรรมและดูรายละเอียด
2. USER สมัครกิจกรรมที่ยังว่าง แล้วเปิดรายละเอียดเห็นสถานะตนเอง
3. USER ยกเลิก และดูกรณีเต็ม/จบแล้ว
4. ADMIN สร้างและแก้กิจกรรม แสดงข้อผิดพลาด capacity ต่ำเกินไป
5. ADMIN ดูรายชื่อพร้อมเวลาสมัคร และลบข้อมูลทดสอบ
6. ชี้ controller/policy/model/view ที่รับผิดชอบแต่ละพฤติกรรม

**คุณทำส่วนของตัวเองเสร็จเมื่อ:** checklist Member 1 ผ่านบนเครื่องคุณ, เข้าใจโค้ดแต่ละส่วน, push branch แล้ว และเพื่อน review/integrate ได้ ส่วน Category/Organizer CRUD, หน้าแรก และ PDF ticket ต้องทดสอบรวมหลังเพื่อนส่งงาน

# ภาคผนวก A · ตรวจการประกอบ ActivityController

ถ้าหลงว่าไฟล์ควรมีอะไร ให้ตรวจรายการนี้แทนการเริ่มเขียนใหม่

## หัวไฟล์และ imports ฉบับสุดท้าย

หัวไฟล์นี้อยู่ก่อน class เพียงครั้งเดียว:

```php
<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Category;
use App\Models\Organizer;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Psr\Http\Message\ServerRequestInterface;
```

จากนั้นเป็น `class ActivityController extends SearchableController { ... }` ภายในควรมีเมธอดต่อไปนี้อย่างละหนึ่ง:

| เมธอด | บท | หน้าที่ |
|---|---|---|
| getQuery | 2 | query ตั้งต้นพร้อม relationships/count |
| prepareCriteria | 4 | ค่าเริ่มต้นและรูปแบบ filters |
| getFilterOptions | 6 | เงื่อนไขค้นข้อความ |
| filter | 6 | ใช้ term/category/upcoming |
| list | 4 | แบ่งหน้าและส่งรายการ |
| view | 5 | ส่งรายละเอียดและสถานะสมัคร |
| showCreateForm | 9 | ข้อมูลฟอร์มสร้าง |
| showUpdateForm | 10 | ข้อมูลฟอร์มแก้ |
| readActivityData | 8 | อ่านและตรวจ POST |
| fillActivity | 8 | fill และ associate |
| create | 9 | บันทึกกิจกรรมใหม่ |
| update | 10 | แก้ไขพร้อมตรวจ capacity |
| delete | 11 | ลบกิจกรรม |
| register | 13 | เพิ่มการสมัคร |
| cancel | 14 | ยกเลิกการสมัคร |
| viewParticipants | 14 | ค้นและแสดงผู้สมัคร |

ลำดับเมธอดใน class ไม่เปลี่ยนวิธีเรียกของ PHP แต่จัดตามรายการนี้จะเทียบได้ง่าย ไม่จำเป็นต้องมี constructor ในตัวอย่างนี้

## ไฟล์ Blade ที่ควรมี

`main.blade.php`, `list.blade.php`, `view.blade.php`, `create-form.blade.php`, `update-form.blade.php`, `view-participants.blade.php` ทั้งหมดอยู่ใน `resources/views/activities/`

Model Activity ต้องคง casts/relationships เดิมและเพิ่ม isOpen/isFull ส่วน policy และ route ใช้ไฟล์ของกิจกรรมเท่านั้น

# ภาคผนวก B · แหล่งอ้างอิงและบันทึกความคืบหน้า

## เอกสารของ repository ที่ใช้เป็นหลัก

- `README.md` — setup และฐานร่วม
- `docs/team/MEMBER_1.md` — หน้าที่ routes checklist และหัวข้ออธิบาย
- `docs/team/README.md` — ข้อตกลงทีมและรูปแบบจากคาบ
- `docs/PROJECT_PLAN.md` — ภาพรวมและรายละเอียดการทำงาน
- `app/Http/Controllers/SearchableController.php` — source จริงของตัวช่วยค้นหา
- `app/Models/Activity.php`, `User.php` และ migrations/seeders — schema/relationships/ข้อมูลตัวอย่าง

บางส่วนใน PROJECT_RULES เป็นแนวทางทั่วไปที่ใช้ชื่อ flash/delete ต่างจากแผนที่ปรับตามคาบ เล่มนี้ยึดข้อตกลงปัจจุบันใน team README/PROJECT_PLAN: status/alert, POST delete, PSR-7 และ Gate หากคำสั่งอาจารย์ล่าสุดต่างออกไป ให้ตกลงกับทีมก่อนเปลี่ยน

## เอกสารทางการสำหรับอ่านเพิ่ม

- [Laravel 13 Authorization](https://laravel.com/framework/docs/13.x/authorization) — คำศัพท์ Gate/Policy และ guest authorization
- [Laravel 13 Eloquent Relationships](https://laravel.com/framework/docs/13.x/eloquent-relationships) — ความสัมพันธ์ model และ pivot
- [Laravel 13 Query Builder](https://laravel.com/framework/docs/13.x/queries) — query และ pessimistic locking

เนื้อหาและตัวอย่างในเล่มอิง source ใน repository เป็นหลัก และตรวจ execution กับ dependencies ที่ติดตั้ง แหล่งทางการใช้ตรวจแนวคิด ไม่ได้หมายความว่า Laravel บังคับให้ทุกโปรเจกต์ใช้รูปแบบ controller ตามในคาบนี้

## บันทึกของคุณ

| วันที่ | ทำถึงบท | สิ่งที่เข้าใจเพิ่ม | สิ่งที่ต้องลองต่อ | commit |
|---|---|---|---|---|
| | | | | |
| | | | | |
| | | | | |
| | | | | |

ทุกครั้งที่พัก จดบท/ไฟล์/ผลทดสอบล่าสุดไว้ เมื่อกลับมาเริ่มจาก checkpoint สุดท้าย จะเรียนต่อได้โดยไม่ต้องเล่าใหม่ทั้งโปรเจกต์
