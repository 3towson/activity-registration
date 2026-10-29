# เรียนต่อบน PC — Member 1

เอกสารส่งต่อบริบทการเรียนจาก Mac ไป PC วันที่ 22 ตุลาคม 2026

## คำสั่งสำหรับผู้ช่วยที่อ่านไฟล์นี้

ผู้ใช้ต้องการ **เขียนโค้ดเองและเข้าใจสิ่งที่เขียน** ให้สอนเป็นภาษาไทยทีละขั้นเล็ก ๆ อธิบายก่อน ให้ผู้ใช้ลองทำ แล้วรอผลก่อนขั้นถัดไป อย่าเขียนฟีเจอร์ทั้งหมดหรือแก้ไฟล์แทน เว้นแต่ผู้ใช้สั่งโดยตรง การขอสร้างเอกสารนี้และ push เป็นคำสั่งเฉพาะสำหรับส่งต่อบริบท ไม่ใช่การอนุญาตให้ทำโค้ดงานแทน

ผู้ใช้เป็นมือใหม่ หากตอบไม่ได้ ให้อธิบายด้วยตัวอย่างง่าย ๆ ก่อน ไม่เร่งถามคำถามที่ต้องใช้ความรู้ซึ่งยังไม่ได้สอน อย่าส่งโค้ดยาวทั้งไฟล์ทันที ให้แบบฝึกหัดขนาดเล็กและอธิบายแต่ละบรรทัด

## โปรเจกต์และหน้าที่

- Repository: https://github.com/3towson/activity-registration
- Branch: `member-1/activities`
- ผู้ใช้เป็นสมาชิกคนที่ 1 รับผิดชอบ Activity CRUD, สมัคร/ยกเลิกกิจกรรม และรายชื่อผู้สมัครสำหรับ admin
- อ่าน [งาน Member 1](MEMBER_1.md), [ข้อตกลงทีม](README.md) และ [README หลัก](../../README.md) ก่อนสอนรายละเอียดฟีเจอร์
- ใช้ Laravel + Blade + MySQL ตามรูปแบบที่เรียนในคาบและข้อตกลงใน repository
- ฐานร่วมมี model, migrations, seeders, login, layout, CSS และหน้าแรกตรวจฐานร่วมแล้ว ไม่ต้องเริ่มใหม่
- ไฟล์หลักของผู้ใช้คือ `ActivityController`, `ActivityPolicy`, `resources/views/activities/*`, `routes/activities.php` และส่วน Activity model ตามเอกสารแบ่งงาน
- เคารพไฟล์ของสมาชิกคนอื่น โดยเฉพาะ `SearchableController`, `HomeController`, layout และไฟล์ route รวม

## สิ่งที่เกิดขึ้นแล้วในการเรียน

1. Clone บน Mac ที่ `~/Developer/activity-registration` และสร้าง branch `member-1/activities`
2. ผู้ใช้แจ้งว่าติดตั้ง PHP/Composer, ติดตั้ง dependencies, สร้าง `.env`/app key และทำขั้นตอน MySQL แล้ว แต่ควรตรวจสภาพแวดล้อม PC แยกต่างหาก
3. อธิบายว่า UI เดิมเป็นฐานร่วมของทีม ส่วนหน้ากิจกรรมยังเป็นงานของผู้ใช้
4. ทดลองเปลี่ยน `orderBy('code')` เป็น `orderBy('date')` ใน HomeController เพื่อดูผลการเรียงข้อมูล ผู้ใช้ทำแล้ว และได้รับคำแนะนำให้คืนเป็น `code`
5. สร้าง `ActivityController` และเปลี่ยน parent เป็น `SearchableController`
6. เริ่มอธิบาย `abstract function getQuery(): Builder;` ใน SearchableController แต่ยังไม่ได้ทำเมธอดสำเร็จ
7. คำถามเนื้อหาล่าสุดคือเหตุใดต้องใช้ `use App\Models\Activity;`

## สิ่งที่อธิบายล่าสุด

- `namespace` ระบุ namespace ของคลาสที่กำลังประกาศ
- `use App\Models\Activity;` ระบุว่าชื่อสั้น `Activity` หมายถึง model คลาสใด ไม่ได้ดึงข้อมูลจากฐานข้อมูล
- หากไม่มี import ภายใต้ `namespace App\Http\Controllers;` ชื่อ `Activity` จะถูกตีความเป็น `App\Http\Controllers\Activity`
- ใช้ชื่อเต็ม `\App\Models\Activity` แทน import ได้ แต่ import ช่วยให้อ่านง่าย
- `orderBy('code')` เตรียมการเรียงข้อมูลตามคอลัมน์ code; `get()` จึงเรียกดึงข้อมูล
- เมธอด `getQuery()` ต้องคืน Builder เพื่อให้คลาสแม่เพิ่มเงื่อนไขก่อนเรียกดึงข้อมูล

## สภาพโค้ดจริงตอนสร้างเอกสารนี้

ตรวจจาก commit `83df82d` (`Start ActivityController query`) พบว่า ActivityController ยังเป็นงานระหว่างเรียน:

```php
<?php

namespace App\Http\Controllers;
namespace App\Models\Activity;

use Illuminate\Http\Request;

class ActivityController extends SearchableController
{
    //
}
```

**อย่าสรุปจากชื่อ commit ว่า getQuery เสร็จแล้ว** ตอนนี้มี `namespace` บรรทัดที่สองแทน `use` และยังไม่มีเมธอดที่คลาสแม่กำหนด ให้สอนผู้ใช้แก้ด้วยตนเอง อย่าแก้ให้โดยอัตโนมัติ

## จุดเริ่มต้นสำหรับแชตใหม่

1. ตรวจ branch, `git status` และอ่าน ActivityController กับ SearchableController ปัจจุบันก่อน เพราะผู้ใช้อาจทำต่อแล้ว
2. หากโค้ดยังเหมือนข้างบน ให้เริ่มด้วยการอธิบายความแตกต่างระหว่าง `namespace` กับ `use` แล้วให้ผู้ใช้แก้เฉพาะบรรทัดที่สองด้วยตนเอง
3. เมื่อผู้ใช้ทำเสร็จ ค่อยสอน import ของ `Illuminate\Database\Eloquent\Builder` และสร้าง `getQuery(): Builder` ทีละส่วน เริ่มจากการคืน query ที่เรียงตาม code
4. ค่อยเพิ่ม eager loading ของ category/organizer และจำนวน participants พร้อมอธิบายเหตุผล
5. จากนั้นจึงสอน list action, public routes และ Blade list ทีละส่วน แล้วค่อย search/filter/pagination, รายละเอียด, policies, CRUD, registration และ participant list
6. อย่าเปิดใช้ทุก route ในตัวอย่างพร้อมกันก่อนมีเมธอดรองรับ และให้ยึดชื่อ route ตามเอกสารทีม

## เปิดต่อบน PC

เปิด terminal ใน repository บน PC หากมีงานที่ยังไม่ commit ให้จัดการงานนั้นก่อนสลับ branch หรือ pull จากนั้นใช้:

```bash
git fetch origin
git switch member-1/activities
git pull --ff-only
```

เปิดโฟลเดอร์โปรเจกต์ใน VS Code แล้วพิมพ์ในแชต Codex ว่า:

> อ่าน docs/team/MEMBER_1_TUTORIAL_HANDOFF.md แล้วสอนต่อทีละขั้นเป็นภาษาไทย ให้ฉันเขียนเอง เริ่มจากตรวจไฟล์ปัจจุบัน

PHP, Composer, MySQL, `.env`, app key และฐานข้อมูลบน PC ต้องตั้งค่าแยกจาก Mac; Git ไม่ย้าย `.env`, vendor หรือข้อมูล MySQL ให้ อย่าใช้ `migrate:fresh` เพียงเพื่อย้ายเครื่องเพราะลบตารางและข้อมูลเดิม
