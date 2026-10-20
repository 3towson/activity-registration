# 954348 — Week 07–09: Database (Lecture + Lab) — ฉบับรวม

> รวมเนื้อหาจาก 3 ไฟล์ (คัดลอกเนื้อหาเดิมครบทุกบรรทัด ไม่ตัดทอน เรียงตามสัปดาห์):
> 1. `lec07-lab07-basic-database.md` — Week 07: Basic Database
> 2. `week08-database-manipulation.md` — Week 08: Database Manipulation
> 3. `week09-relationships-lec-lab.md` — Week 09: Relationships
>
> หมายเหตุ: ลิงก์สารบัญภายในแต่ละสัปดาห์ยังคงเป็นของไฟล์เดิม

## สารบัญรวม

- [Week 07 — Basic Database (Lecture + Lab)](#954348--week-07-basic-database-lecture--lab)
- [Week 08 — Database Manipulation (Lecture + Lab + Seeders)](#week-08--database-manipulation-lecture--lab--seeders)
- [Week 09 — Relationships (Lecture + Lab)](#week-09--relationships-lecture--lab)

---

# 954348 — Week 07: Basic Database (Lecture + Lab)

> รวมเนื้อหาจาก `lec07-basic_database.pdf`, `lab07-basic_database.pdf` และไฟล์ใน `lab07-database.zip`
> Framework: Laravel 13.x (Eloquent ORM) · Database: MariaDB/MySQL · ภาคเรียน 01/2569

---

## สารบัญ

**Part 1 — Lecture 07**
1. Intro to Object-Relational Mapping (ORM)
2. MariaDB/MySQL
3. Eloquent — Model
4. Eloquent — Retrieving Models
5. Example (my-db project)
6. References

**Part 2 — Lab 07**
7. โจทย์ Lab 07
8. ไฟล์จาก lab07-database.zip (Migration + Seeder)
9. แนวทางทำ Lab (Shop model / controller / views / routes)

---

# Part 1 — Lecture 07

## 1. Intro to Object-Relational Mapping (ORM)

### What is ORM?
- Object-Relational Mapping คือวิธีแทนข้อมูลใน relational database ด้วย object ใน OOP
  - ส่วนใหญ่ **1 object = 1 row** ใน database
  - Object-Document Mapping (ODM) ใช้กับ document database เช่น MongoDB
- ถ้าค่าใดอ้างอิงข้อมูลจากอีกตาราง ORM จะ query ข้อมูลนั้นให้อัตโนมัติเมื่อผู้ใช้เรียกใช้
  - ORM สร้าง SQL จาก relation ที่เรากำหนดไว้ใน class
  - class นี้เรียกว่า **Entity** หรือ **Model**

### Patterns of ORM (มี 2 รูปแบบ)

**1) Object mapper pattern**
- แยก data access layer ออกจาก data representation object
- data representation object (model) เป็นแค่ plain object (POPO: Plain Old PHP Object)
- Data Access Object (DAO) คือ object ที่มี method จัดการข้อมูล
- framework ส่วนใหญ่ที่ใช้ pattern นี้สามารถ **generate database schema จาก model** ได้
  - เป็นแบบ **Code-First**

**2) Active record pattern**
- รวม data access layer และ data representation object ไว้ด้วยกัน
- โดยทั่วไปเน้นดึงข้อมูลจาก database schema ที่มีอยู่แล้ว
  - ทำง่ายกว่า object mapper เพราะไม่ต้อง config map attribute ของ class ไปยัง field ของ database
- แต่ถ้าต้องการ generate schema จากแอปเอง ต้องทำงานเพิ่ม
  - framework แบบ active record ส่วนใหญ่ไม่สามารถ generate schema จาก model ได้โดยตรง

---

## 2. MariaDB/MySQL

### MariaDB
- MariaDB สร้างโดยผู้พัฒนา MySQL ดั้งเดิม และรับประกันว่าจะเป็น open source ตลอดไป
- ถ้าเครื่องมี MySQL อยู่แล้ว ใช้ MySQL ได้เลย
- ถ้าไม่มี database ตระกูล MySQL แนะนำให้ติดตั้ง MariaDB

### MariaDB installation
- Download: <https://mariadb.org/download/>
- ติดตั้ง MariaDB แล้วเพิ่ม `C:\Program Files\MariaDB 12.3.2\bin` ใน PATH variable
- (Optional) ติดตั้ง MySQL client — ในวิชานี้ใช้ CLI เป็นหลัก
  - MySQL Workbench: <https://dev.mysql.com/downloads/workbench/>
  - HeidiSQL: <https://www.heidisql.com/download.php>
  - phpMyAdmin (สำหรับผู้ใช้ขั้นสูง): <https://www.phpmyadmin.net/>

---

## 3. Eloquent — Model

### What is Eloquent?
- Eloquent คือ ORM framework ค่าเริ่มต้นของ Laravel
  - เป็น **active record pattern** ORM
- แต่ละ table ใน database มี "Model" ที่ใช้โต้ตอบกับ table นั้น
- นอกจากดึงข้อมูลแล้ว Eloquent model ยังใช้ insert, update, delete ได้ด้วย

### Database configuration in Laravel

สร้าง database ใน MariaDB ด้วยรหัสนักศึกษา

เปิด MySQL client:

```bash
mysql -h 127.0.0.1 -u root --password="your_password"
```

คำสั่ง SQL:

```sql
CREATE DATABASE u672110xxx_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
SHOW DATABASES; # Show all databases
QUIT;           # Exit mysql CLI
```

เปิดไฟล์ `.env` ที่ root ของโปรเจกต์แล้วกรอกค่า:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=u672110xxx_db
DB_USERNAME=root
DB_PASSWORD=your_password
```

### artisan command for database

เปิด MySQL CLI โดยใช้ config จาก Laravel:

```bash
php artisan db
```

สร้าง model:

```bash
php artisan make:model ModelClassName
```

Model ที่สร้างด้วยคำสั่งนี้จะ:
- ถูกเก็บใน `app/Models/`
- มี namespace `App\Models`
- extend class `Illuminate\Database\Eloquent\Model`

### The base model in Eloquent

ทุก model ที่ extend `Illuminate\Database\Eloquent\Model` จะมี field เหล่านี้เสมอ:

| Field | ชนิด | คำอธิบาย |
|---|---|---|
| `id` | primary, auto-increment, unsigned bigint | primary key |
| `created_at` | timestamp, default NULL | เวลาที่สร้าง |
| `updated_at` | timestamp, default NULL | เวลาที่แก้ไขล่าสุด |

- ดังนั้นทุก table ต้องสร้างด้วย field ข้างต้น
  - โดยปกติ intermediate table (ตารางกลาง) ไม่มี model ที่เกี่ยวข้อง จึงไม่จำเป็นต้องมี

### Naming convention for class and table
- ตามหลัก convention ชื่อ table = ชื่อ class แบบ **snake case พหูพจน์** เว้นแต่ระบุชื่ออื่นชัดเจน
  - model `Book` → table `books`
  - model `BookCategory` → table `book_categories`
- หรือใช้ `Table` attribute ตอนประกาศ class เพื่อระบุชื่อ table:

```php
use Illuminate\Database\Eloquent\Attributes\Table;

#[Table('my_books')]
class Book extends Model { ... }
```

### Accessing database value with model
- เข้าถึงค่าใน database ด้วย object attribute ที่ชื่อเดียวกับ field

```php
$product->code
```

- หมายถึง field `code` ใน database ของ model นั้น

---

## 4. Eloquent — Retrieving Models

### Retrieving Models
- เมื่อสร้าง model และ table ที่เกี่ยวข้องแล้ว ก็เริ่มดึงข้อมูลได้
- มอง Eloquent model แต่ละตัวเป็น **query builder** (ตัว generate SQL) ที่ query table ของมันได้อย่างลื่นไหล (fluent)

### ทำไมไม่เขียน SQL ตรง ๆ
- ใน ORM เราไม่ใช้ SQL ตรง ๆ
  - SQL แตกต่างกันตาม database vendor
  - ORM map model ไปยัง database ได้หลายชนิด เช่น MySQL, MS SQL, PostgreSQL จึงมี method ที่ generate SQL ให้แทน
- method ส่วนใหญ่ของ active record (model) ตั้งชื่อตาม SQL statement
  - เริ่มด้วยการเรียก **static method** ด้วย `::` แล้วต่อด้วย **instance method** ด้วย `->`

```php
$products = Product::where('code', 'PD001')->get();
```

### Active Record Methods — immediately retrieve
method กลุ่มนี้ดึงข้อมูลทันทีด้วย method เดียว

**`all()`** — ดึงทุก record

```php
$products = Product::all();
```

**`find($id)`** — ดึง record เดียวด้วย id

```php
$product = Product::find(31);
```

**`findOrFail($id)`** — เหมือน `find($id)` แต่ถ้าไม่เจอจะ throw `ModelNotFoundException` ซึ่ง Laravel แปลงเป็น HTTP 404 (not found)

```php
$product = Product::findOrFail(35);
```

### Active Record Methods — query
method กลุ่มนี้ใส่เงื่อนไข query ให้ object แต่ **ยังไม่ดึงข้อมูล** จนกว่าจะเรียก finishing method

**`where()`** — เพิ่ม where clause แบบ `AND`

```php
where($fieldname, $value)
where($fieldname, $operator, $value)
where([[$filename, $operator, $value], ...])
```

```php
$query1 = Product::where('code', 'BK001');
$query2 = Product::where('price', '>=', 100)->where('price', '<', 500);
// the following query is same as $query2
$query3 = Product::where([['price', '>=', 100], ['price', '<', 500]]);
```

**`orWhere()`** — เพิ่ม where clause แบบ `OR`

```php
orWhere($fieldname, $value)
orWhere($fieldname, $operator, $value)
```

```php
$query1 = Product
              ::where('price', '<', 100)
              ->orWhere('price', '>=', 500);
```

**`orderBy($fieldname, $direction = 'asc')`** — เพิ่ม order by (ถ้าไม่ระบุ direction จะเป็น `'asc'`)

```php
$query = Product
             ::where('price', '>=', 100)
             ->where('price', '<', 500)
             ->orderBy('code');
```

### Active Record Methods — Logical Grouping
- `where()` และ `orWhere()` สร้าง `and`/`or` ใน level เดียวกัน
- ถ้าต้องการจัดกลุ่มเงื่อนไข (วงเล็บ) ต้องใช้ closure function

```php
// import Illuminate\Database\Eloquent\Builder
$query1 = Product::where('price', '>', 100)
              ->where(static function (Builder $query): void {
                  $query->where('cat_code', 'CT001')
                      ->orWhere('cat_code', 'CT003');
              });
// the above code will generate the following SQL
/*
   SELECT * FROM `products` WHERE `price` > 100 AND
   (`cat_code` = 'CT001' OR `cat_code` = 'CT003')
*/
```

### Active Record Methods — finishing query
หลังได้ query แล้ว ใช้ finishing method เพื่อดึงข้อมูลจริง

**`get()`** — ดึงทุก record จาก query

```php
$query = Product::where('price', '>', 100);
$products = $query->get();

// หรือใช้ chain
$products = Product::where('price', '>', 100)->get();
```

**`first()` / `firstOrFail()`** — ดึง record แรก; `firstOrFail()` จะ throw `ModelNotFoundException` เมื่อไม่พบ

```php
$product = Product::where('code', 'BK003')->firstOrFail();
```

**Aggregate methods** — `count`, `max`, `min`, `avg`, `sum`

```php
$productCount = Product::where('code', 'BK003')->count();
$sumPrice = Product::sum('price');
$minPrice = Product::where('cat_code', 'CT003')->min('price');
```

- อ่านเพิ่มเกี่ยวกับ query: <https://laravel.com/docs/13.x/queries>

### สรุป method (cheat sheet)

| กลุ่ม | Method | หน้าที่ |
|---|---|---|
| Immediate | `all()` | ดึงทุก record |
| Immediate | `find($id)` | ดึง 1 record ด้วย id (ไม่เจอ = null) |
| Immediate | `findOrFail($id)` | เหมือน find แต่ไม่เจอ = 404 |
| Query | `where(...)` | เงื่อนไข AND |
| Query | `orWhere(...)` | เงื่อนไข OR |
| Query | `orderBy($f, $dir)` | เรียงลำดับ (default asc) |
| Query | `where(closure)` | จัดกลุ่มเงื่อนไข (วงเล็บ) |
| Finishing | `get()` | ดึงทุก record ของ query |
| Finishing | `first()` / `firstOrFail()` | ดึง record แรก (หรือ 404) |
| Finishing | `count/max/min/avg/sum` | aggregate |

---

## 5. Example (my-db project)

### Prepare for example
- สร้างโปรเจกต์ **`my-db`** (ใช้ต่อเนื่องอีกหลายสัปดาห์)
- เพิ่ม composer packages:

```bash
composer require symfony/psr-http-message-bridge
composer require nyholm/psr7
```

- สร้าง master page ที่ `resources/views/layouts/main.blade.php`
  - กำหนด `section` ใน master page เองตามต้องการ

### Database configuration
- เปิด `.env` แล้วกรอกค่า (ต้องแน่ใจว่าสร้าง database `u672110xxx_db` แล้ว)

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=u672110xxx_db
DB_USERNAME=root
DB_PASSWORD=your_password
```

- แตกไฟล์ `lec07-database.zip` ลงใน root ของโปรเจกต์ แล้วรัน migration และ seeding:

```bash
php artisan migrate
php artisan db:seed --class=ProductSeeder
```

### Create Product Model
1. เปิด MySQL client (ถ้าต้องการ)

```bash
php artisan db
```

หรือถ้าคำสั่งข้างบนมี error ให้ใช้:

```bash
mysql -h 127.0.0.1 -u root --password="your_password" u672110xxx_db
```

2. สร้าง Product model

```bash
php artisan make:model Product
```

### Create controller
3. สร้าง `ProductController`
   - ตั้งชื่อ controller ลงท้าย `Controller` เพื่อไม่ให้ชนกับชื่อ model

```bash
php artisan make:controller ProductController
```

### Grouping Templates
- เมื่อมี template จำนวนมาก ให้จัดกลุ่ม template ของ model เดียวกันไว้ใน subdirectory เดียวกัน
  - เช่น template ของ Product อยู่ในโฟลเดอร์ `products`

```
/resources
  /views
    /layouts
      main.blade.php
    /products
      main.blade.php
      list.blade.php
      view.blade.php
    /shops
      main.blade.php
      list.blade.php
      view.blade.php
```

- ใช้ `main.blade.php` ในแต่ละโฟลเดอร์เป็น **sub-main template** ของ products และ shops

`products/main.blade.php`

```blade
@extends('layouts.main', [
    'title' => "Product: {$title}",
])
```

`shops/main.blade.php`

```blade
@extends('layouts.main', [
    'title' => "Shop: {$title}",
])
```

> หมายเหตุ: สไลด์ต้นฉบับสะกดชื่อไฟล์ผิดเป็น `main.balde.php` / `list.balde.php` / `view.balde.php` — ชื่อที่ถูกต้องคือ `*.blade.php`

### Layout ใน `layouts.main` (ตามมาตรฐาน W3C)

```blade
<header id="app-main-header">
  <nav> <!-- ... site-wide navigation --> </nav>
</header>
<main id="app-main-content">
  <header class="app-cmp-content-header">
    <h1>
      @section('title'){{ $title }}@show
    </h1>
    {{-- for other header parts: filtering, content navigation --}}
    @yield('header')
  </header>
  @yield('content')
</main>
<footer id="app-main-footer">
  &#xA9; Copyright Your_name's Database.
</footer>
```

### Add controller methods
4. เพิ่ม method ใน `ProductController`

```php
// At the top of file
use Psr\Http\Message\ServerRequestInterface;
use App\Models\Product;
use Illuminate\View\View;

function list(): View {
    $products = Product
        ::orderBy('code')
        ->get();

    return view('products.list', [
        'products' => $products,
    ]);
}

function view(string $productCode): View {
    $product = Product
        ::where('code', $productCode)
        ->firstOrFail();

    return view('products.view', [
        'product' => $product,
    ]);
}
```

### `products/list.blade.php`
5. แสดงรายการสินค้า (ส่วนใน `<tbody>`): คอลัมน์แรกเป็น Navigation link ไปยัง route `products.view`

```blade
@foreach($products as $product)
    <tr>
        <th>
            <a href="{{ route('products.view', [
                'product' => $product->code,
            ]) }}">
                {{ $product->code }}
            </a>
        </th>
        <td>{{ $product->name }}</td>
    </tr>
@endforeach
```

### `products/view.blade.php`
6. แสดงรายละเอียดสินค้า

```blade
<dl>
    <dt>Code</dt>
    <dd>{{ $product->code }}</dd>

    <dt>Name</dt>
    <dd>{{ $product->name }}</dd>

    <dt>Price</dt>
    <dd>{{ number_format((float)$product->price, 2) }}</dd>
</dl>

<pre>{{ $product->description }}</pre>
```

- ส่วน `<pre>` ใช้แสดง Description data (ข้อความหลายบรรทัดคงรูปแบบเดิม)

### `routes/web.php`
7. จัดกลุ่ม route ของ product ด้วย `prefix()`, `name()` และ index route

```php
// At the top of file, auto-importing
use App\Http\Controllers\ProductController;

// ... other routes config

Route::controller(ProductController::class)
    ->prefix('/products')
    ->name('products.')
    ->group(static function(Router $router): void {
        $prefixName = \array_last($router->getGroupStack())['as'];

        Route::any('', static fn() => redirect()->route("{$prefixName}list"))
            ->name('index');
        Route::get('', 'list')->name('list');
        Route::get('/{product}', 'view')->name('view');
    });
```

> หมายเหตุ: โค้ดใช้ type `Router` ใน closure จึงต้อง `use Illuminate\Routing\Router;` ที่ด้านบนไฟล์ด้วย (สไลด์ไม่ได้แสดงไว้)

---

## 6. References
- <https://mariadb.org/>
- <https://laravel.com/docs/13.x/migrations>
- <https://laravel.com/docs/13.x/seeding>
- <https://laravel.com/docs/13.x/eloquent>
- <https://laravel.com/docs/13.x/queries>

---

# Part 2 — Lab 07

## 7. โจทย์ Lab 07: Basic Database

**NOTE:** ใช้โปรเจกต์จากตัวอย่างสัปดาห์ที่ 07 (`my-db`)

### ข้อ 1 — สร้างตาราง `shops` และข้อมูลจากไฟล์ `lab07-database.zip`
- ในไฟล์ `database/seeders/ShopSeeder.php` ให้แทนที่ `$yourName` และ `$friendName` ด้วยชื่อที่ต้องการ

```bash
php artisan migrate
php artisan db:seed --class=ShopSeeder
```

### ข้อ 2 — สร้าง `Shop` model และ `ShopController`

| Method | Path | หน้าที่ |
|---|---|---|
| GET | `/shops` | แสดงรายการร้านค้า (list of shops) |
| GET | `/shops/{shop}` | แสดงข้อมูลร้านค้าตาม code ที่ระบุใน `{shop}` |

- แก้ navigation menu ให้มีลิงก์ไป `/shops` แล้วตกแต่งด้วย CSS

### ผลลัพธ์ที่ต้องได้ (จากภาพในเอกสาร)

**หน้า `/shops` — "Shop: List"**
- navigation menu ด้านบนมีปุ่ม **Product** และ **Shop** (ปุ่มที่ active จะเป็นสีเทา)
- หัวข้อ `Shop: List`
- ตารางหัวข้อ "List of Shops" คอลัมน์: **Code | Name | Owner**
  - Code เป็นลิงก์ไป `/shops/{shop}` (ตัวหนา สีน้ำเงิน แบบ monospace)

| Code | Name | Owner |
|---|---|---|
| SH001 | Pachara Shop | Pachara |
| SH002 | Phasit Shop | Phasit |
| SH003 | Other Shop | Pachara |

- footer: `© Copyright Pachara's Database.`

**หน้า `/shops/SH001` — "Shop: Pachara Shop"**
- หัวข้อ `Shop: {ชื่อร้าน}`
- แสดงข้อมูลแบบ definition list (label :: value)

```
Code     :: SH001
Name     :: Pachara Shop
Owner    :: Pachara
Location :: 18.80045380,98.94889980        ← latitude,longitude
Address  :: College of Arts, Media and Technology     ← หลายบรรทัด (Multi-line)
            239 Huaykaew Rd., Suthep,
            Mueang Chiang Mai District, Chiang Mai 50200
```

- **Location** = `latitude,longitude` (คั่นด้วยจุลภาค)
- **Address** = แสดงหลายบรรทัด (multi-line) ตามที่เก็บใน database (ใช้ `<pre>` หรือ CSS `white-space: pre-line`)
- footer เหมือนหน้า list

---

## 8. ไฟล์จาก `lab07-database.zip`

โครงสร้างไฟล์:

```
database/
├── migrations/
│   └── 2026_08_13_172833_create_shops_table.php
└── seeders/
    └── ShopSeeder.php
```

### `database/migrations/2026_08_13_172833_create_shops_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('shops', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->index('created_at');
            $table->index('updated_at');
            $table->string('code', 64)->unique();
            $table->string('name', 255);
            $table->string('owner', 255);
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->text('address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shops');
    }
};
```

**โครงสร้างตาราง `shops`**

| Column | Type | หมายเหตุ |
|---|---|---|
| `id` | bigint unsigned, PK, auto-increment | |
| `created_at`, `updated_at` | timestamp (nullable) | มี index ทั้งคู่ |
| `code` | varchar(64) | **unique** |
| `name` | varchar(255) | |
| `owner` | varchar(255) | |
| `latitude` | decimal(10, 8) | |
| `longitude` | decimal(11, 8) | |
| `address` | text | เก็บหลายบรรทัด (`\r\n`) |

### `database/seeders/ShopSeeder.php`

> ต้องแก้ `$yourName` และ `$friendName` เป็นชื่อของตัวเองตามโจทย์ (ในไฟล์ที่ได้รับตั้งไว้เป็น `Pachara` / `Phasit`)

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShopSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $yourName = 'Pachara';
        $friendName = 'Phasit';
        $currentTstmp = now();

        DB::table('shops')->insert([
            [
                'created_at' => $currentTstmp,
                'updated_at' => $currentTstmp,
                'code' => 'SH001',
                'name' => "{$yourName} Shop",
                'owner' => "{$yourName}",
                'latitude' => '18.8004538',
                'longitude' => '98.9488998',
                'address' => "College of Arts, Media and Technology\r\n239 Huaykaew Rd., Suthep,\r\nMueang Chiang Mai District, Chiang Mai 50200",
            ],
            [
                'created_at' => $currentTstmp,
                'updated_at' => $currentTstmp,
                'code' => 'SH002',
                'name' => "{$friendName} Shop",
                'owner' => "{$friendName}",
                'latitude' => '18.7921726',
                'longitude' => '98.9575002',
                'address' => "Chiang Mai University Cooperative Store\r\n239 Huaykaew Rd., Suthep,\r\nMueang Chiang Mai District, Chiang Mai 50200",
            ],
            [
                'created_at' => $currentTstmp,
                'updated_at' => $currentTstmp,
                'code' => 'SH003',
                'name' => "Other Shop",
                'owner' => "{$yourName}",
                'latitude' => '18.7989343',
                'longitude' => '98.9518081',
                'address' => "Innovation Learning Center, ILC\r\n239 Huaykaew Rd., Suthep,\r\nMueang Chiang Mai District, Chiang Mai 50200",
            ],
        ]);
    }
}
```

---

## 9. แนวทางทำ Lab (ตัวอย่างที่เขียนเพิ่ม — ไม่ได้อยู่ในเอกสารต้นฉบับ)

> ส่วนนี้เขียนตามรูปแบบตัวอย่าง Product ในบทเรียน ใช้เป็นโครงเริ่มต้นสำหรับ Project ได้ ปรับ style/ชื่อ section ให้ตรงกับ `layouts/main.blade.php` ของตัวเอง

### 9.1 คำสั่งทั้งหมด

```bash
php artisan migrate
php artisan db:seed --class=ShopSeeder
php artisan make:model Shop
php artisan make:controller ShopController
```

### 9.2 `app/Http/Controllers/ShopController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use Illuminate\View\View;

class ShopController extends Controller
{
    function list(): View {
        $shops = Shop::orderBy('code')->get();

        return view('shops.list', [
            'shops' => $shops,
        ]);
    }

    function view(string $shopCode): View {
        $shop = Shop::where('code', $shopCode)->firstOrFail();

        return view('shops.view', [
            'shop' => $shop,
        ]);
    }
}
```

### 9.3 `routes/web.php`

```php
use App\Http\Controllers\ShopController;
use Illuminate\Routing\Router;

Route::controller(ShopController::class)
    ->prefix('/shops')
    ->name('shops.')
    ->group(static function(Router $router): void {
        $prefixName = \array_last($router->getGroupStack())['as'];

        Route::any('', static fn() => redirect()->route("{$prefixName}list"))
            ->name('index');
        Route::get('', 'list')->name('list');
        Route::get('/{shop}', 'view')->name('view');
    });
```

### 9.4 Views

`resources/views/shops/main.blade.php`

```blade
@extends('layouts.main', [
    'title' => "Shop: {$title}",
])
```

`resources/views/shops/list.blade.php` (ปรับ `@extends`/`@section` ให้ตรงกับ layout ที่ตัวเองออกแบบ)

```blade
@extends('shops.main', ['title' => 'List'])

@section('content')
<table>
    <caption>List of Shops</caption>
    <thead>
        <tr><th>Code</th><th>Name</th><th>Owner</th></tr>
    </thead>
    <tbody>
        @foreach($shops as $shop)
            <tr>
                <th>
                    <a href="{{ route('shops.view', ['shop' => $shop->code]) }}">
                        {{ $shop->code }}
                    </a>
                </th>
                <td>{{ $shop->name }}</td>
                <td>{{ $shop->owner }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
@endsection
```

`resources/views/shops/view.blade.php`

```blade
@extends('shops.main', ['title' => $shop->name])

@section('content')
<dl>
    <dt>Code</dt>
    <dd>{{ $shop->code }}</dd>

    <dt>Name</dt>
    <dd>{{ $shop->name }}</dd>

    <dt>Owner</dt>
    <dd>{{ $shop->owner }}</dd>

    <dt>Location</dt>
    <dd>{{ $shop->latitude }},{{ $shop->longitude }}</dd>

    <dt>Address</dt>
    <dd><pre>{{ $shop->address }}</pre></dd>
</dl>
@endsection
```

### 9.5 Navigation menu (ใน `layouts/main.blade.php`)

```blade
<nav>
    <a href="{{ route('products.index') }}"
       class="{{ request()->routeIs('products.*') ? 'active' : '' }}">Product</a>
    <a href="{{ route('shops.index') }}"
       class="{{ request()->routeIs('shops.*') ? 'active' : '' }}">Shop</a>
</nav>
```

### 9.6 CSS ตัวอย่าง

```css
#app-main-header nav { text-align: center; padding: .5rem; }
#app-main-header nav a {
    display: inline-block; padding: .2rem .6rem; margin: 0 .2rem;
    border: 1px solid #333; border-radius: 4px; text-decoration: none; color: #000;
}
#app-main-header nav a.active { background: #aaa; }

table { border-collapse: collapse; }
th, td { border: 1px solid #333; padding: 2px 6px; }
thead th { background: #888; }
tbody th a { font-family: monospace; font-weight: bold; color: #0645ad; }

dt { font-weight: bold; float: left; clear: left; width: 6rem; }
dt::after { content: " ::"; }
dd { margin-left: 7rem; font-family: monospace; }
dd pre { margin: 0; }

#app-main-footer { text-align: center; font-size: .8rem; margin-top: 3rem; }
```

### 9.7 Checklist ก่อนส่ง
- [ ] `.env` ตั้งค่า database ถูกต้อง และสร้าง database แล้ว
- [ ] แก้ `$yourName` / `$friendName` ใน `ShopSeeder` แล้ว
- [ ] `php artisan migrate` และ `db:seed --class=ShopSeeder` สำเร็จ
- [ ] `/shops` แสดง 3 ร้าน และ code เป็นลิงก์ไป `/shops/{shop}`
- [ ] `/shops/SH001` แสดง Code, Name, Owner, Location (lat,long), Address หลายบรรทัด
- [ ] code ที่ไม่มีอยู่ (เช่น `/shops/XXX`) ขึ้น 404
- [ ] navigation มีปุ่ม Product และ Shop
- [ ] footer แสดง Copyright ชื่อตัวเอง


---

<!-- ===================== END OF WEEK 07 ===================== -->

# Week 08 — Database Manipulation (Lecture + Lab + Seeders)

> รวมเนื้อหาจาก 3 ไฟล์:
> - `lec08-database_manipulation.pdf` (Lecture, 62 สไลด์)
> - `lab08-database_manipulation.pdf` (Lab 08, Practice 954348 — 01/2569)
> - `lec08-seeders.zip` (`Product02Seeder.php`, `Shop02Seeder.php`)
>
> ส่วนที่มีเครื่องหมาย 📝 คือหมายเหตุ/ส่วนเสริมที่เพิ่มเข้ามา ไม่ได้อยู่ในเอกสารต้นฉบับ

## สารบัญ

- [Part 1 — Lecture](#part-1--lecture)
  - [1. Searching & Pagination](#1-searching--pagination)
  - [2. Eloquent — Insert, Update and Delete](#2-eloquent--insert-update-and-delete)
  - [3. Example (Step-by-step)](#3-example-step-by-step)
  - [4. References](#4-references)
- [Part 2 — Seeders](#part-2--seeders)
- [Part 3 — Lab 08](#part-3--lab-08)
- [Part 4 — 📝 แนวทางทำ Lab (Shop)](#part-4---แนวทางทำ-lab-shop)
- [Quick Reference](#quick-reference)

---

# Part 1 — Lecture

**Contents**
- Searching & Pagination
- Eloquent — Insert, Update and Delete
- Example
- References

## 1. Searching & Pagination

### Intro to searching

- When we deal with many records in database, we cannot handle all of records in one view, so we have to filter the records that we want.
  - Searching is one of often used for filtering.
- There are many searching approach for example:
  - Search by using specific fields.
  - Search by using range.
  - Search by using search term.
- In this section we will learn about **search term**.

### Search term

- User specifies search by giving the group of words, called **search term**, then the application decides which fields will be used to search.
  - By usually, each word in search term would be operated with **AND** operator.
- In most cases of web application, we send the search term to web server by using **query string**.
  - The common name would be `q`, `s`, `search` or `term`.
- And the simplest way to get the search result we use SQL `LIKE` operator.

### Search term — example : form

First, we create form with GET method then send the form-data to the route of listing data, usually it is the same route as form appear, e.g.

```blade
@section('header')
<search>
    <!-- form with single control will submit when user press enter -->
    <form action="{{ route('products.list') }}" method="get">
        <label>
            <b>search</b>
            <input type="text" name="term" value="{{ $term }}" />
        </label>
    </form>
</search>
@endsection

<!-- ... content section -->
```

### Search term — example : controller

Then in controller method of route `products.list`.

```php
function list(ServerRequestInterface $request): View {
    $data = $request->getQueryParams();
    $query = Product::orderBy('code');
    $term = (key_exists('term', $data))? $data['term'] : '';
    // import Illuminate\Database\Eloquent\Builder
    foreach(preg_split('/\s+/', trim($term)) as $word) {
        $query->where(function(Builder $innerQuery) use ($word) {
            $innerQuery
                ->where('code', 'LIKE', "%{$word}%")
                ->orWhere('name', 'LIKE', "%{$word}%");
        });
    }

    return view('products.list', [
        'term' => $term,
        'products' => $query->get(),
    ]);
}
```

### Pagination

- When we use searching but the results are still too many then we use pagination to reduce the results.
- Pagination is separating the results to many pages.
  - The page number is specified by using query string with name `page`, e.g. `?page=1`.
- Eloquent make this task very simple by just using `paginate()` method, e.g.

```php
$products = Product::where('price', '>', 500)->paginate(5);
```

- The argument of `paginate()` is the number of items per page.

### Pagination — Eloquent

- The collection returned from `paginate()` method is same as one returned from `get()` method.
  - So, we can use the same approach to walk through data, e.g., `@foreach`.
- Moreover, the result from `paginate()` provides more methods that work with pagination, e.g., `links()`, to generate HTML tag for pagination links.
- Eloquent pagination automatically use request object to get information that it want so we do not need to pass them to `paginate()` method.

### Pagination — Example : controller

Same as *Searching — Example : controller* just change `get()` to `paginate(5)`.

```php
function list(ServerRequestInterface $request): View {
    // ... other business logic from Searching — Example : controller

    return view('products.list', [
        'term' => $term,
        'products' => $query->paginate(5),
    ]);
}
```

### Pagination — Example : template

Eloquent pagination can generate HTML for pagination links but by default, it uses Tailwind CSS framework that we would not use it in this course, so we have to change default pagination views to non-third-party views.

Do the following steps.

1. Extract pagination templates from framework.

   ```bash
   php artisan vendor:publish --tag=laravel-pagination
   ```

   - Now we get pagination templates in `resources/views/vendor/pagination/`

2. Change default pagination template in file `app/Providers/AppServiceProvider.php` in `boot()` method add the following code, `pagination::` is a namespace for pagination views.

   ```php
   \Illuminate\Pagination\Paginator
       ::defaultView('pagination::semantic-ui');
   \Illuminate\Pagination\Paginator
       ::defaultSimpleView('pagination::semantic-ui');
   ```

Then add the following code to template of route `products.list`.

- The method `withQueryString()` tells Eloquent pagination to preserve the existing query string, in this case they are search term.

```blade
{{ $products->withQueryString()->links() }}
```

- Now we have to create CSS to decorate the above links.
- Or you can customize the following template by yourselves: `resources/views/pagination/semantic-ui.blade.php`

### Notable — Full-text search

- Using `LIKE` operator is not a good approach for very large number of records.
  - It is very low performance.
  - Database indexing does not help when query with leading wildcard, e.g., `%xxx`.
  - For very large number of records, the full-text search approach is recommended.
- But the build-in full-text search of database has many of limitations.
  - It supports a few number of languages.
  - It has the difference SQL syntax of each database vendor.
- So, in many cases, we use external libraries/services that are based on full-text search approach, e.g.
  - Apache Lucene
  - Apache Solr (Based on Apache Lucene)
  - Algolia
  - Elasticsearch (Based on Apache Lucene)
  - TNTSearch
  - Moreover, many of them provide the additional search feature, e.g., fuzzy search, graph search or geo search.
- Then Laravel supports those libraries/services by using package `laravel/scout`.
- Since Laravel 12, for native full-text search, there is the built-in `whereFullText()` for native full-text search querying.
  - And vector search is also supported.
  - See more https://laravel.com/docs/13.x/search

---

## 2. Eloquent — Insert, Update and Delete

### Simple inserting

When we want to create a new record and insert into the database just create the instance of Model then call `save()` method, e.g.

```php
// create instance/model
$product = new Product();

// add the values to model
$product->code = 'PD005';
$product->name = 'Laravel framework';

// insert the new model to database
$product->save();
```

- Eloquent automatically assign value to fields `created_at` and `updated_at` then retrieve the inserted id to property `id`, in the other word, `$product->id` will be updated to the new id from database.

### Simple updating

When we want to update the record in database just retrieve the record, by using query or `find()` method, then call `save()` method, e.g.

```php
// retrieve model
$product = Product::where('code', 'PD004')->firstOrFail();

// update the values in model
$product->name = 'Laravel framework with Eloquent';

// update the changed values of model to database
$product->save();
```

- Eloquent automatically update field `updated_at`.

### Mass Updates

We can update multiple records in database with one command by using query methods then call `update()` method. e.g.

```php
Product::where('price', '>', 500)
    ->update(['discount' => 10]);
```

- The argument of `update()` is associative array paired with fields name and their value.
- Eloquent automatically update field `updated_at` for all records.

### Mass Assignment

In many cases, we want to assign the values to model by using user input data, or form-data, but assigning each field per statement is not a convenient way, e.g.:

```php
use Psr\Http\Message\ServerRequestInterface;

// method in controller
function create(ServerRequestInterface $request): View {
    /* User input the following data that match the fields name [
           'code' => 'PD005',
           'name' => 'Laravel framework',
           ... other field values
       ]
     */
    $data = $request->getParsedBody();
    $product = new Product();
    $product->code = $data['code'];
    $product->name = $data['name'];
    // ... other fields assigning
    $product->save();
}
```

Eloquent provides the mass assignment methods `create()` and `fill()` to make this task easier.

```php
// method in controller
function create(
    ServerRequestInterface $request,
): View {
    /* User input the following data [
           'code' => 'PD005',
           'name' => 'Laravel framework',
           ... other fields
       ]
     */
    $data = $request->getParsedBody();
    $product = Product::create($data);
}
```

```php
// method in controller
function update(
    ServerRequestInterface $request,
    string $productCode,
): View {
    /* User input the following data [
           'name' => 'Laravel framework new',
           ... other fields
       ]
     */
    $data = $request->getParsedBody();
    $product = Product::where('code', $productCode)
                    ->firstOrFail();
    $product->fill($data);
    $product->save();
}
```

> 📝 ในสไลด์พิมพ์ว่า `firstOfFail()` ซึ่งเป็น typo — ที่ถูกต้องคือ `firstOrFail()`

### Mass Assignment — Fillable Attribute

- But mass assignment opens the security holds that user can assign the values to some critical fields, e.g., `role`, so we need additional setting to allow the fields that can be assigned by using mass assignment.
- We have to set array of fillable field names to `Fillable` attribute, e.g.:

```php
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['code', 'name', 'price', 'description'])]
class Product extends Model
{
    // ... other declaration
}
```

### Simple deleting

When we want to delete the record in database just retrieve the record, by using query or `find()` method, then call `delete()` method, e.g.:

```php
// retrieve model
$product = Product::where('code', 'PD004')->firstOrFail();

// delete the model from database
$product->delete();
```

### Mass deletes

We can delete multiple records in database with one command by using query methods then call `delete()` method. e.g.:

```php
Product::where('price', '>', 500)->delete();
```

---

## 3. Example (Step-by-step)

### Example — Prepare

- Reuse your previous project from last week, week-07.
  - Search criteria follows week-06.
- Copy the given seeders to `database/seeders`:
  - `Product02Seeder.php`
  - `Shop02Seeder.php`
  - (โค้ดเต็มอยู่ใน [Part 2 — Seeders](#part-2--seeders))
- Seed DB with specific class names:

```bash
php artisan db:seed --class=Product02Seeder
php artisan db:seed --class=Shop02Seeder
```

### Step 1 — Export default pagination templates

```bash
php artisan vendor:publish --tag=laravel-pagination
```

- Change default pagination template:
  - pagination template: `vendor.pagination.semantic-ui`
  - simple pagination template: `vendor.pagination.semantic-ui`
  - Or by using namespace `pagination::semantic-ui`
  - `app/Providers/AppServiceProvider.php` in method `boot()` method add the following code:

```php
\Illuminate\Pagination\Paginator
    ::defaultView('pagination::semantic-ui');
\Illuminate\Pagination\Paginator
    ::defaultSimpleView('pagination::semantic-ui');
```

### Step 2 — Update `app/Models/Product.php` to allow mass assignment

Allow mass assignment for: `code`, `name`, `price` and `description`.

```php
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['code', 'name', 'price', 'description'])]
class Product extends Model
{
    // ... other declaration
}
```

### Step 3 — SearchableController

We want to create search term for `ProductController` and `ShopController` on `code` and `name`.

- So, we have to create `SearchableController` with base code for searching.

  ```bash
  php artisan make:controller SearchableController
  ```

- Then update `ProductController` and `ShopController` to extend `SearchableController`.
- We make `SearchableController` be **abstract class**, for extension only.

#### `app/Http/Controllers/SearchableController.php` (โค้ดเต็ม รวมจาก 5 สไลด์)

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Psr\Http\Message\ServerRequestInterface;

abstract class SearchableController extends Controller
{
    const int MAX_ITEMS = 5;

    // Keyword abstract means MUST be overridden later.
    abstract function getQuery() : Builder;

    function prepareCriteria(array $criteria) : array {
        return [
            'term' => null,
            ...$criteria,
        ];
    }

    function getFilterOptions(): array {
        return [
            'term' => [
                'code' =>
                    static fn(Builder $query, string $word)
                        => $query->where('code', 'LIKE', "%{$word}%"),
                'name' =>
                    static fn(Builder $query, string $word)
                        => $query->where('name', 'LIKE', "%{$word}%"),
            ],
        ];
    }

    final function filterByTerm(
        Builder $query,
        string $term,
        array $termOptions,
    ): void {
        foreach(\preg_split(
            '/\s+/', \trim($term), flags: \PREG_SPLIT_NO_EMPTY,
        ) as $word) {
            $query->where(
                static function (Builder $wordQuery) use ($word, $termOptions): void {
                    foreach ($termOptions as $fieldFn) {
                        $wordQuery->orWhere(
                            static fn(Builder $fieldQuery) => $fieldFn($fieldQuery, $word),
                        );
                    }
                },
            );
        }
    }

    function filter(
        Builder $query, array $criteria,
        array $filterOptions,
    ): void {
        if ($criteria['term'] !== null) {
            $this->filterByTerm(
                $query,
                $criteria['term'],
                $filterOptions['term'],
            );
        }
    }

    final function search(array $criteria, ?array $filterOptions = null): Builder
    {
        $filterOptions ??= $this->getFilterOptions();

        $query = $this->getQuery();
        $this->filter($query, $criteria, $filterOptions);

        return $query;
    }

    // For easily searching by code.
    function find(string $code): Model {
        return $this->getQuery()->where('code', $code)->firstOrFail();
    }
}
```

> 📝 หลักการ: แต่ละคำใน term ถูกเชื่อมกันด้วย **AND** (`$query->where(...)` ต่อคำ) ส่วนภายในคำเดียวกัน แต่ละ field ถูกเชื่อมด้วย **OR** (`orWhere`) — เช่น term = `php web` → `(code LIKE %php% OR name LIKE %php%) AND (code LIKE %web% OR name LIKE %web%)`

#### Example — ProductController

For `ProductController`:
- Import `ServerRequestInterface`.
- Import `Product` model.
- Change to extend from `SearchableController`.

```php
namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Psr\Http\Message\ServerRequestInterface;
use Illuminate\View\View;
use App\Models\Product;

class ProductController extends SearchableController
{
    // ... class body
}
```

Override method `getQuery()` and update method `list()` then set pagination to `static::MAX_ITEMS` items/page.

```php
#[\Override()]
function getQuery() : Builder {
    return Product::orderBy('code');
}

function list(ServerRequestInterface $request): View {
    $criteria = $this->prepareCriteria($request->getQueryParams());
    $query = $this->search($criteria); // use default filterOptions
    return view('products.list', [
        'criteria' => $criteria,
        'products' => $query->paginate(static::MAX_ITEMS),
    ]);
}
```

Update method `view()` to use `$this->find()` from `SearchableController`.

```php
function view(string $productCode): View {
    $product = $this->find($productCode);

    return view('products.view', [
        'product' => $product,
    ]);
}
```

### Step 4 — Update `products/list.blade.php`

Update `products/list.blade.php` to have search form in `@section('header')` and add pagination links.
- We will add criteria of min price and max price later.

```blade
@section('header')
<search>
    <form action="{{ route('products.list') }}" method="get" class="app-cmp-search-form">
        <fieldset>
            <legend>Search</legend>
            <label>
                <b>Term</b>
                <input type="text" name="term" value="{{ $criteria['term'] }}" />
            </label>
            <br />
            <button type="submit">Filter</button>
            <a href="{{ route('products.list') }}">Clear</a>
        </fieldset>
    </form>
</search>

<div class="app-cmp-links-bar">
    {{ $products->withQueryString()->links() }}
</div>
@endsection
```

**🖼️ ภาพตัวอย่าง (Searching & pagination):** หน้า `localhost:8000/products` หัวข้อ "Product: List" มีแถบเมนู Product / Shop ด้านบน, กล่อง *Search* มีช่อง **Term**, ปุ่ม **Filter** (น้ำเงิน) และ **Clear** (แดง), ลิงก์หน้า `< 1 2 >` ชิดขวา และตาราง "List of Products" (Code | Name | Price) แสดง 5 รายการ: PD001 Programming PHP 345.00, PD002 JavaScript: The Definitive Guide 250.00, PD003 Learning PHP, MySQL & JavaScript 450.00, PD004 Python Crash Course, 2nd Edition 560.00, PD101 Modern PHP Web Development 758.55

### Step 5 — Create product

For creating product, we need to:
- Add route `products.create-form` with method **get** for URL `/products/create` that points to from for creating product.
  - This form will submit form-data with method post to `/products` to add form-data to database.
- Add route `products.create` with method **post** for URL `/products` that inserts the form-data to database.
  - After inserted redirect to route `products.view` with the following code
  - We have to put the above routes **before** route `products.view`.
- Then add link to route `products.create-form` in route `products.list`.

#### ProductController

Add the following methods to `ProductController`:

```php
// import Illuminate\Http\RedirectResponse in the top of file

function showCreateForm(): View {
    return view('products.create-form');
}

function create(ServerRequestInterface $request): RedirectResponse {
    $product = Product::create($request->getParsedBody());

    return redirect()->route('products.view', [
        'product' => $product->code,
    ]);
}
```

#### routes/web.php

Add the following routes before route `products.view`.

```php
// in ProductController group

Route::controller(ProductController::class)
    ->prefix('/products')
    ->name('products.')
    ->group(static function (): void {
        $prefixName = \array_last($router->getGroupStack())['as'];
        Route::any('', static fn() => redirect()->route("{$prefixName}list"))
            ->name('index');

        Route::get('', 'list')->name('list');
        Route::post('', 'create')->name('create');
        Route::get('/create', 'showCreateForm')->name('create-form');
        Route::get('/{product}', 'view')->name('view');
    });
```

> 📝 ในสไลด์ใช้ `$router` ภายใน closure โดยไม่ได้ประกาศ ถ้ารันแล้ว error ว่า `$router` undefined ให้รับเป็นพารามิเตอร์ของ closure แทน เช่น
> `->group(static function (\Illuminate\Routing\Router $router): void { ... })` (Laravel จะส่ง router เข้ามาให้) หรือใช้ `Route::getGroupStack()`

#### products/create-form.blade.php

```blade
{{-- ... Other template directives --}}

@section('content')
<form action="{{ route('products.create') }}" method="post">
    @csrf

    <label>Code <input type="text" name="code" required /></label><br />
    <label>Name <input type="text" name="name" required /></label><br />
    <label>
        Price <input type="number" step="any" name="price" required />
    </label><br />
    <label>
        Description
        <textarea name="description" cols="80" rows="10" required></textarea>
    </label><br />

    <button type="submit">Create</button>
</form>
@endsection
```

#### products/list.blade.php — ลิงก์ Create

Update `products/list.blade.php` to add link to route `products.create-form`.

```blade
{{-- inside .app-cmp-links-bar --}}

<nav>
    <ul class="app-cmp-links">
        <li>
            <a href="{{ route('products.create-form') }}">Create Product</a>
        </li>
    </ul>
</nav>
```

**🖼️ ภาพตัวอย่าง (Create product):** ในหน้า Product: List มีปุ่ม **Create Product** (น้ำเงิน) อยู่ใต้กล่อง Search ฝั่งซ้าย (ตรงข้ามกับลิงก์หน้า) — คลิกแล้วไปหน้า `localhost:8000/products/create` หัวข้อ "Product: Create" มีฟอร์ม Code \*, Name \*, Price \*, Description \* (textarea) และปุ่ม **Create**

### Step 6 — Update & Delete product

For updating product, we need to:
- Add route `products.update-form` with method **get** for URL `/products/{product}/update` that points to from for updating product.
  - This form will submit form-data with method post to `/products/{product}/update` to add form-data to database.
    > 📝 ตามโค้ด routes จริง ฟอร์มจะ submit ไปที่ `products.update` = **POST** `/products/{product}`
- Add route `products.update` with method **post** for URL `/products/{product}` that updates the form-data to database.
  - After inserted redirect to route `products.view` with the following code

    ```php
    return redirect()->route('products.view', ['product' => $product->code]);
    ```

- Add route `products.delete` with method **post** for URL `/products/{product}/delete` that deletes model form database.
  - After deleted redirect to route `products.index` with the following code

    ```php
    return redirect()->route('products.index');
    ```

- You have to put the above routes **after** route `products.view`.
- Then add link to route `products.update-form` and `products.delete` in route `products.view`.
- Update data through the HTTP GET method is **not recommended**.
  - This chapter just points you how to manipulate through routing system.
  - We have a better approach in the next chapter.

#### Example — Notable (HTTP methods)

HTTP protocol classifies HTTP methods to be 2 categories
1. **Safe methods:**
   - `GET`, `HEAD`, `OPTIONS` and `TRACE`
   - Methods do not change the state of data.
2. **Unsafe methods** (other methods out of safe methods):
   - e.g., `POST`, `PUT`, `DELETE` or `PATCH`
   - Methods change the state of data.

- Most of CSRF protection protect unsafe methods.

#### ProductController — update / delete

```php
function showUpdateForm(string $productCode): View {
    $product = $this->find($productCode);

    return view('products.update-form', [
        'product' => $product,
    ]);
}

function update(
    string $productCode,
    ServerRequestInterface $request,
): RedirectResponse {
    $product = $this->find($productCode);
    $product->fill($request->getParsedBody());
    $product->save();

    return redirect()->route('products.view', [
        'product' => $product->code,
    ]);
}

function delete(string $productCode): RedirectResponse {
    $product = $this->find($productCode);
    $product->delete();

    return redirect()->route('products.index');
}
```

#### routes/web.php (ฉบับเต็ม)

Update the following routes, we change `products.view` to use `prefix()` and `group()`.

```php
// in ProductController group

Route::controller(ProductController::class)
    ->prefix('/products')
    ->name('products.')
    ->group(static function (): void {
        $prefixName = \array_last($router->getGroupStack())['as'];
        Route::any('', static fn() => redirect()->route("{$prefixName}list"))
            ->name('index');

        Route::get('', 'list')->name('list');
        Route::post('', 'create')->name('create');
        Route::get('/create', 'showCreateForm')->name('create-form');
        Route::prefix('/{product}')
            ->group(static function (): void {
                Route::get('', 'view')->name('view');
                Route::post('', 'update')->name('update');
                Route::get('/update', 'showUpdateForm')->name('update-form');
                Route::post('/delete', 'delete')->name('delete');
            });
    });
```

📝 สรุปตาราง routes ของ products:

| Name | Method | URL | Controller method |
|---|---|---|---|
| `products.index` | ANY | `/products` (redirect → list) | — |
| `products.list` | GET | `/products` | `list` |
| `products.create` | POST | `/products` | `create` |
| `products.create-form` | GET | `/products/create` | `showCreateForm` |
| `products.view` | GET | `/products/{product}` | `view` |
| `products.update` | POST | `/products/{product}` | `update` |
| `products.update-form` | GET | `/products/{product}/update` | `showUpdateForm` |
| `products.delete` | POST | `/products/{product}/delete` | `delete` |

#### products/update-form.blade.php

```blade
{{-- ... Other templates directive --}}

@section('content')
<form action="{{ route('products.update', [
    'product' => $product->code,
]) }}" method="post">
    @csrf

    <label>Code
        <input type="text" name="code" value="{{ $product->code }}" required />
    </label><br />
    <label>Name
        <input type="text" name="name" value="{{ $product->name }}" required />
    </label><br />
    <label>Price
        <input type="number" step="any" name="price"
            value="{{ $product->price }}" required />
    </label><br />
    <label>
        Description
        {{-- No space between <textarea> and {{ $product->description }} --}}
        <textarea
            name="description" cols="80" rows="10" required
        >{{ $product->description }}</textarea>
    </label><br />

    <button type="submit">Update</button>
</form>
@endsection
```

#### products/view.blade.php

Update `products/view.blade.php` to add link to route `products.update-form` and `products.delete`.

```blade
@section('header')
<nav>
    <form action="{{ route('products.delete', [
        'product' => $product->code,
    ]) }}" method="post"
        id="app-form-delete">
        @csrf
    </form>
    <ul>
        <li>
            <a href="{{ route('products.update-form', [
                'product' => $product->code,
            ]) }}">Update</a>
        </li>
        <li>
            <button type="submit" form="app-form-delete" class="app-cl-link">Delete</button>
        </li>
    </ul>
</nav>
@endsection
```

> 📝 เทคนิค: `<form id="app-form-delete">` ว่าง ๆ + `<button form="app-form-delete">` ทำให้ปุ่ม Delete อยู่ใน `<ul>` ได้แต่ยังส่ง POST พร้อม `@csrf`

**🖼️ ภาพตัวอย่าง (Update Product view):** หน้า `localhost:8000/products/PD001` หัวข้อ "Product: Programming PHP" มีปุ่ม **Update** (น้ำเงิน) และ **Delete** (แดง) ใต้หัวข้อ แล้วแสดง Code :: PD001, Name :: Programming PHP, Price :: 345.00 และคำอธิบายสินค้าด้านล่าง

**🖼️ ภาพตัวอย่าง (Update product):** หน้า `localhost:8000/products/PD001/update` หัวข้อ "Products: PD001" ฟอร์มมีค่าเดิมเติมไว้แล้ว (Code PD001, Name Programming PHP, Price 345.00 ชิดขวา, Description ใน textarea) และปุ่ม **Update**

### Step 7 — minPrice and maxPrice

Now we want to add criteria min price and max price to `ProductController`.
- Override method `prepareCriteria()` to prepare `minPrice` and `maxPrice` from query string.
- Add method `filterByMinPrice()` and `filterByMaxPrice()` to `ProductController`.
- Override method `filter()` from `SearchableController` to filter by `minPrice` and `maxPrice`.
- Add `minPrice` and `maxPrice` to search form of `products.list.blade.php`.

#### Override `prepareCriteria()`

```php
#[\Override()]
function prepareCriteria(array $criteria) : array {
    return [
        ...parent::prepareCriteria($criteria),
        'minPrice' => (($criteria['minPrice'] ?? null) === null)
            ? null
            : (float) $criteria['minPrice'],
        'maxPrice' => (($criteria['maxPrice'] ?? null) === null)
            ? null
            : (float) $criteria['maxPrice'],
    ];
}
```

#### `filterByMinPrice()` / `filterByMaxPrice()`

```php
function filterByMinPrice(Builder $query, float $minPrice): void {
    $query->where('price', '>=', $minPrice);
}

function filterByMaxPrice(Builder $query, float $maxPrice): void {
    $query->where('price', '<=', $maxPrice);
}
```

#### Override `filter()`

```php
#[\Override()]
function filter(
    Builder $query,
    array $criteria,
    array $filterOptions,
): void {
    parent::filter($query, $criteria, $filterOptions);

    if ($criteria['minPrice'] !== null) {
        $this->filterByMinPrice($query, $criteria['minPrice']);
    }

    if ($criteria['maxPrice'] !== null) {
        $this->filterByMaxPrice($query, $criteria['maxPrice']);
    }
}
```

#### products/list.blade.php — เพิ่ม minPrice / maxPrice

```blade
{{-- In search form --}}

<label>
    Min Price
    <input type="number" name="minPrice" value="{{ $criteria['minPrice'] }}"
        step="0.01" />
</label><br />
<label>
    Max Price
    <input type="number" name="maxPrice" value="{{ $criteria['maxPrice'] }}"
        step="0.01" />
</label><br />
```

> 📝 Laravel มี middleware `ConvertEmptyStringsToNull` อยู่แล้ว แต่เพราะเราอ่านค่าผ่าน PSR `getQueryParams()` ถ้าช่องว่างแล้วส่งมาเป็น `""` อาจถูกแปลงเป็น `(float) "" = 0` ได้ ถ้าเจอปัญหานี้ให้เช็ก `=== ''` เพิ่มด้วย

**🖼️ ภาพตัวอย่าง (minPrice and maxPrice):** หน้า Product: List ในกล่อง Search มี 3 ช่อง: **Term**, **Min Price**, **Max Price** + ปุ่ม Filter / Clear, ปุ่ม Create Product, ลิงก์หน้า `< 1 2 >` และตารางสินค้า

## 4. References

- https://laravel.com/docs/13.x/eloquent
- https://laravel.com/docs/13.x/pagination
- https://laravel.com/docs/13.x/search

---

# Part 2 — Seeders

ไฟล์จาก `lec08-seeders.zip` — วางใน `database/seeders/` แล้วรัน:

```bash
php artisan db:seed --class=Product02Seeder
php artisan db:seed --class=Shop02Seeder
```

### `database/seeders/Product02Seeder.php`

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Product02Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currentTstmp = now();

        DB::table('products')->insert([
            [
                'created_at' => $currentTstmp,
                'updated_at' => $currentTstmp,
                'code' => 'PD101',
                'name' => 'Modern PHP Web Development',
                'price' => '758.55',
                'description' => <<<EOD
                    This comprehensive guide teaches you how to build real-world
                    web applications using modern PHP. You will learn core language
                    features, database integration with MySQL, secure user
                    authentication, and best practices for deploying robust web
                    services.
                    \t- Master PHP syntax, object-oriented programming,
                    \t  and MVC architecture.
                    \t- Database & Security: Connect safely with PDO,
                    \t  prevent SQL injection, and hash passwords securely.
                    \t- Project Workflow: Build a complete dynamic web
                    \t  application step-by-step.
                    EOD,
            ],
            [
                'created_at' => $currentTstmp,
                'updated_at' => $currentTstmp,
                'code' => 'PD102',
                'name' => 'Web Security for Developers',
                'price' => '1369.18',
                'description' => <<<EOD
                    You will learn how to:
                    \t- Protect against SQL injection attacks, malicious
                    \t  JavaScript, and cross-site request forgery.
                    \t- Add authentication and shape access control
                    \t  to protect accounts.
                    \t- Lock down user accounts to prevent attacks that
                    \t  rely on guessing passwords, stealing sessions,
                    \t  or escalating privileges.
                    \t- Implement encryption.
                    \t- Manage vulnerabilities in legacy code.
                    \t- Prevent information leaks that disclose
                    \t  vulnerabilities.
                    \t- Mitigate advanced attacks like malvertising and
                    \t  denial-of-service
                    EOD,
            ],
            [
                'created_at' => $currentTstmp,
                'updated_at' => $currentTstmp,
                'code' => 'PD103',
                'name' => 'How To Be A Web Developer In 90 Days',
                'price' => '892.72',
                'description' => <<<EOD
                    You are going to enjoy this book because I have made coding fun
                    by doing something that has never been done before. I’ve
                    included animations that explain daily lessons. You will also
                    receive a free 15 minute live chat with a Certified Web
                    Developer. Plus, you can learn at your own pace. If you need
                    additional help, there’s an option to attend live online
                    classes. At the end of this book, for your final project, you
                    will build your own website
                    EOD,
            ],
            [
                'created_at' => $currentTstmp,
                'updated_at' => $currentTstmp,
                'code' => 'PD104',
                'name' => 'Designing Web APIs',
                'price' => '756.49',
                'description' => <<<EOD
                    Using a web API to provide services to application developers
                    is one of the more satisfying endeavors that software engineers
                    undertake. But building a popular API with a thriving developer
                    ecosystem is also one of the most challenging. With this
                    practical guide, developers, architects, and tech leads will
                    learn how to navigate complex decisions for designing, scaling,
                    marketing, and evolving interoperable APIs.
                    EOD,
            ],
            [
                'created_at' => $currentTstmp,
                'updated_at' => $currentTstmp,
                'code' => 'PD105',
                'name' => 'Visual Studio Code: End-to-End Editing and Debugging Tools',
                'price' => '978.38',
                'description' => <<<EOD
                    Visual Studio Code, a free, open source, cross-compatible source
                    code editor, is one of the most popular choices for web
                    developers. It is fast, lightweight, customizable, and contains
                    built-in support for JavaScript, Typescript, and Node.js
                    extensions for other languages, including C++, Python, and PHP.
                    Features such as debugging capability,embedded Git control,
                    syntax highlighting, code snippets, and IntelliSense intelligent
                    code completion support―several of which set it apart from the
                    competition―help make Visual Studio Code an impressive,
                    out-of-the-box solution.
                    EOD,
            ],
        ]);
    }
}
```

### `database/seeders/Shop02Seeder.php`

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Shop02Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currentTstmp = now();

        DB::table('shops')->insert([
            [
                'created_at' => $currentTstmp,
                'updated_at' => $currentTstmp,
                'code' => 'SH101',
                'name' => 'Adam Shop',
                'owner' => 'Adam',
                'latitude' => '19.93633',
                'longitude' => '25.54209',
                'address' => "Address 101_1\r\nAddress 101_2",
            ],
            [
                'created_at' => $currentTstmp,
                'updated_at' => $currentTstmp,
                'code' => 'SH102',
                'name' => 'MadA Shop',
                'owner' => 'Adam',
                'latitude' => '22.41123',
                'longitude' => '-90.57680',
                'address' => "Address 102_1\r\nAddress 102_2",
            ],
            [
                'created_at' => $currentTstmp,
                'updated_at' => $currentTstmp,
                'code' => 'SH103',
                'name' => 'Page Turners',
                'owner' => 'Bob',
                'latitude' => '-24.99135',
                'longitude' => '80.35458',
                'address' => "Address 103_1\r\nAddress 103_2",
            ],
            [
                'created_at' => $currentTstmp,
                'updated_at' => $currentTstmp,
                'code' => 'SH104',
                'name' => 'Literary Lounge',
                'owner' => 'Cindy',
                'latitude' => '54.11798',
                'longitude' => '40.51694',
                'address' => "Address 104_1\r\nAddress 104_2",
            ],
            [
                'created_at' => $currentTstmp,
                'updated_at' => $currentTstmp,
                'code' => 'SH105',
                'name' => 'Novel Nook',
                'owner' => 'Dian',
                'latitude' => '29.64557',
                'longitude' => '114.18390',
                'address' => "Address 105_1\r\nAddress 105_2",
            ],
            [
                'created_at' => $currentTstmp,
                'updated_at' => $currentTstmp,
                'code' => 'SH106',
                'name' => 'Readery Place',
                'owner' => 'Evan',
                'latitude' => '38.74781',
                'longitude' => '-125.22089',
                'address' => "Address 106_1\r\nAddress 106_2",
            ],
            [
                'created_at' => $currentTstmp,
                'updated_at' => $currentTstmp,
                'code' => 'SH107',
                'name' => 'Tome Treasures',
                'owner' => 'Frank',
                'latitude' => '3.58527',
                'longitude' => '24.96360',
                'address' => "Address 107_1\r\nAddress 107_2",
            ],
            [
                'created_at' => $currentTstmp,
                'updated_at' => $currentTstmp,
                'code' => 'SH108',
                'name' => 'Paperback Palace',
                'owner' => 'Gorge',
                'latitude' => '-15.14895',
                'longitude' => '-66.80432',
                'address' => "Address 108_1\r\nAddress 108_2",
            ],
            [
                'created_at' => $currentTstmp,
                'updated_at' => $currentTstmp,
                'code' => 'SH109',
                'name' => 'Ink & Paper',
                'owner' => 'Henry',
                'latitude' => '-28.30137',
                'longitude' => '120.25675',
                'address' => "Address 109_1\r\nAddress 109_2",
            ],
            [
                'created_at' => $currentTstmp,
                'updated_at' => $currentTstmp,
                'code' => 'SH110',
                'name' => 'Bookish Corner',
                'owner' => 'Ivy',
                'latitude' => '18.8029089',
                'longitude' => '98.9516926',
                'address' => "Address 110_1\r\nAddress 110_2",
            ],
        ]);
    }
}
```

📝 สรุปข้อมูลที่ seed:

| Code | Name | Price |
|---|---|---|
| PD101 | Modern PHP Web Development | 758.55 |
| PD102 | Web Security for Developers | 1369.18 |
| PD103 | How To Be A Web Developer In 90 Days | 892.72 |
| PD104 | Designing Web APIs | 756.49 |
| PD105 | Visual Studio Code: End-to-End Editing and Debugging Tools | 978.38 |

| Code | Name | Owner | Latitude | Longitude |
|---|---|---|---|---|
| SH101 | Adam Shop | Adam | 19.93633 | 25.54209 |
| SH102 | MadA Shop | Adam | 22.41123 | -90.57680 |
| SH103 | Page Turners | Bob | -24.99135 | 80.35458 |
| SH104 | Literary Lounge | Cindy | 54.11798 | 40.51694 |
| SH105 | Novel Nook | Dian | 29.64557 | 114.18390 |
| SH106 | Readery Place | Evan | 38.74781 | -125.22089 |
| SH107 | Tome Treasures | Frank | 3.58527 | 24.96360 |
| SH108 | Paperback Palace | Gorge | -15.14895 | -66.80432 |
| SH109 | Ink & Paper | Henry | -28.30137 | 120.25675 |
| SH110 | Bookish Corner | Ivy | 18.8029089 | 98.9516926 |

(address ของทุกร้านเป็น 2 บรรทัด เช่น `Address 101_1\r\nAddress 101_2`)

---

# Part 3 — Lab 08

**Practice 954348 — 01/2569 — Lab 08 Database Manipulation**

> **NOTE:** Use the project from example of week 08.

1. Update `Shop` model and `ShopController` that can **search, paginate, create, update and delete** same as `Product`.
   - Search term on fields **`code`, `name` and `owner`**.
   - Fillable on fields **`code`, `name`, `owner`, `latitude`, `longitude` and `address`**.

### หน้าจอที่ต้องได้ (จากภาพใน Lab)

**🖼️ หน้า 2 — Shop: Create** (`localhost:8000/shops/create`)
- แถบเมนูบน: **Product** | **Shop** (Shop ถูกเลือก)
- หัวข้อ: **Shop: Create**
- ฟอร์ม: `Code *`, `Name *`, `Owner *`, `Latitude *`, `Longitude *` (input บรรทัดเดียว), `Address *` (textarea ใหญ่)
- ปุ่ม **Create** (น้ำเงิน)
- footer: © Copyright Pachara's Database.

**🖼️ หน้า 2 — Shop view** (`localhost:8000/shops/SH001`)
- หัวข้อ: **Shop: Pachara Shop**
- ปุ่ม **Update** (น้ำเงิน) และ **Delete** (แดง)
- รายละเอียด:
  - Code :: SH001
  - Name :: Pachara Shop
  - Owner :: Pachara
  - Location :: `18.80045380, 98.94889980`
  - Address ::
    ```
    College of Arts, Media and Technology
    239 Huaykaew Rd., Suthep,
    Mueang Chiang Mai District, Chiang Mai 50200
    ```

**🖼️ หน้า 3 — Shop update form** (`localhost:8000/shops/SH001/update`)
- หัวข้อ: **Shop: SH001**
- ฟอร์มเติมค่าเดิม: Code `SH001`, Name `Pachara Shop`, Owner `Pachara`, Latitude `18.80045380` (ชิดขวา), Longitude `98.94889980` (ชิดขวา), Address (textarea 3 บรรทัดตามด้านบน)
- ปุ่ม **Update**

---

# Part 4 — 📝 แนวทางทำ Lab (Shop)

> ส่วนนี้ไม่ได้อยู่ในเอกสารต้นฉบับ — เป็นแนวทางที่ทำตาม pattern เดียวกับ Product ใน Lecture เพื่อใช้อ้างอิงตอนทำ Lab/Project ควรปรับชื่อ view / layout / class CSS ให้ตรงกับโปรเจกต์ week-07 ของตัวเอง

### `app/Models/Shop.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['code', 'name', 'owner', 'latitude', 'longitude', 'address'])]
class Shop extends Model
{
    // ... other declaration (เช่น relationship จากสัปดาห์ก่อน)
}
```

### `app/Http/Controllers/ShopController.php`

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Psr\Http\Message\ServerRequestInterface;
use App\Models\Shop;

class ShopController extends SearchableController
{
    #[\Override()]
    function getQuery(): Builder {
        return Shop::orderBy('code');
    }

    // เพิ่ม owner เข้าไปใน search term (code, name มาจาก parent)
    #[\Override()]
    function getFilterOptions(): array {
        $options = parent::getFilterOptions();
        $options['term']['owner'] =
            static fn(Builder $query, string $word)
                => $query->where('owner', 'LIKE', "%{$word}%");

        return $options;
    }

    function list(ServerRequestInterface $request): View {
        $criteria = $this->prepareCriteria($request->getQueryParams());
        $query = $this->search($criteria);

        return view('shops.list', [
            'criteria' => $criteria,
            'shops' => $query->paginate(static::MAX_ITEMS),
        ]);
    }

    function view(string $shopCode): View {
        $shop = $this->find($shopCode);

        return view('shops.view', [
            'shop' => $shop,
        ]);
    }

    function showCreateForm(): View {
        return view('shops.create-form');
    }

    function create(ServerRequestInterface $request): RedirectResponse {
        $shop = Shop::create($request->getParsedBody());

        return redirect()->route('shops.view', [
            'shop' => $shop->code,
        ]);
    }

    function showUpdateForm(string $shopCode): View {
        $shop = $this->find($shopCode);

        return view('shops.update-form', [
            'shop' => $shop,
        ]);
    }

    function update(
        string $shopCode,
        ServerRequestInterface $request,
    ): RedirectResponse {
        $shop = $this->find($shopCode);
        $shop->fill($request->getParsedBody());
        $shop->save();

        return redirect()->route('shops.view', [
            'shop' => $shop->code,
        ]);
    }

    function delete(string $shopCode): RedirectResponse {
        $shop = $this->find($shopCode);
        $shop->delete();

        return redirect()->route('shops.index');
    }
}
```

### `routes/web.php` (กลุ่ม Shop)

```php
use App\Http\Controllers\ShopController;

Route::controller(ShopController::class)
    ->prefix('/shops')
    ->name('shops.')
    ->group(static function (\Illuminate\Routing\Router $router): void {
        $prefixName = \array_last($router->getGroupStack())['as'];
        Route::any('', static fn() => redirect()->route("{$prefixName}list"))
            ->name('index');

        Route::get('', 'list')->name('list');
        Route::post('', 'create')->name('create');
        Route::get('/create', 'showCreateForm')->name('create-form');
        Route::prefix('/{shop}')
            ->group(static function (): void {
                Route::get('', 'view')->name('view');
                Route::post('', 'update')->name('update');
                Route::get('/update', 'showUpdateForm')->name('update-form');
                Route::post('/delete', 'delete')->name('delete');
            });
    });
```

### `resources/views/shops/list.blade.php` (ส่วน header)

```blade
@section('header')
<search>
    <form action="{{ route('shops.list') }}" method="get" class="app-cmp-search-form">
        <fieldset>
            <legend>Search</legend>
            <label>
                <b>Term</b>
                <input type="text" name="term" value="{{ $criteria['term'] }}" />
            </label>
            <br />
            <button type="submit">Filter</button>
            <a href="{{ route('shops.list') }}">Clear</a>
        </fieldset>
    </form>
</search>

<div class="app-cmp-links-bar">
    <nav>
        <ul class="app-cmp-links">
            <li>
                <a href="{{ route('shops.create-form') }}">Create Shop</a>
            </li>
        </ul>
    </nav>

    {{ $shops->withQueryString()->links() }}
</div>
@endsection
```

### `resources/views/shops/create-form.blade.php`

```blade
@section('content')
<form action="{{ route('shops.create') }}" method="post">
    @csrf

    <label>Code <input type="text" name="code" required /></label><br />
    <label>Name <input type="text" name="name" required /></label><br />
    <label>Owner <input type="text" name="owner" required /></label><br />
    <label>Latitude <input type="number" step="any" name="latitude" required /></label><br />
    <label>Longitude <input type="number" step="any" name="longitude" required /></label><br />
    <label>
        Address
        <textarea name="address" cols="80" rows="10" required></textarea>
    </label><br />

    <button type="submit">Create</button>
</form>
@endsection
```

### `resources/views/shops/update-form.blade.php`

```blade
@section('content')
<form action="{{ route('shops.update', [
    'shop' => $shop->code,
]) }}" method="post">
    @csrf

    <label>Code
        <input type="text" name="code" value="{{ $shop->code }}" required />
    </label><br />
    <label>Name
        <input type="text" name="name" value="{{ $shop->name }}" required />
    </label><br />
    <label>Owner
        <input type="text" name="owner" value="{{ $shop->owner }}" required />
    </label><br />
    <label>Latitude
        <input type="number" step="any" name="latitude"
            value="{{ $shop->latitude }}" required />
    </label><br />
    <label>Longitude
        <input type="number" step="any" name="longitude"
            value="{{ $shop->longitude }}" required />
    </label><br />
    <label>
        Address
        {{-- No space between <textarea> and {{ $shop->address }} --}}
        <textarea
            name="address" cols="80" rows="10" required
        >{{ $shop->address }}</textarea>
    </label><br />

    <button type="submit">Update</button>
</form>
@endsection
```

### `resources/views/shops/view.blade.php` (ส่วน header)

```blade
@section('header')
<nav>
    <form action="{{ route('shops.delete', [
        'shop' => $shop->code,
    ]) }}" method="post"
        id="app-form-delete">
        @csrf
    </form>
    <ul>
        <li>
            <a href="{{ route('shops.update-form', [
                'shop' => $shop->code,
            ]) }}">Update</a>
        </li>
        <li>
            <button type="submit" form="app-form-delete" class="app-cl-link">Delete</button>
        </li>
    </ul>
</nav>
@endsection
```

### Checklist Lab 08

- [ ] `Shop` model มี `#[Fillable(['code', 'name', 'owner', 'latitude', 'longitude', 'address'])]`
- [ ] `ShopController extends SearchableController` + override `getQuery()`
- [ ] Search term ค้นได้ทั้ง `code`, `name`, `owner`
- [ ] Pagination 5 รายการ/หน้า + `withQueryString()->links()`
- [ ] Routes: index, list, create, create-form, view, update, update-form, delete (create-form อยู่**ก่อน** `/{shop}`)
- [ ] หน้า list มีลิงก์ Create Shop
- [ ] หน้า view มีปุ่ม Update / Delete
- [ ] ทุกฟอร์ม POST มี `@csrf`
- [ ] seed ข้อมูลด้วย `php artisan db:seed --class=Shop02Seeder` แล้วลองค้นหา เช่น `Adam` ต้องเจอ SH101, SH102

---

# Quick Reference

| ต้องการ | โค้ด |
|---|---|
| Insert | `$m = new Model(); $m->x = ...; $m->save();` |
| Insert (mass) | `Model::create($data)` — ต้องมี `#[Fillable]` |
| Update | `$m = ...->firstOrFail(); $m->x = ...; $m->save();` |
| Update (mass assign) | `$m->fill($data); $m->save();` |
| Mass update | `Model::where(...)->update(['f' => v])` |
| Delete | `$m->delete()` |
| Mass delete | `Model::where(...)->delete()` |
| Pagination | `$query->paginate(5)` |
| Pagination links | `{{ $items->withQueryString()->links() }}` |
| Publish pagination views | `php artisan vendor:publish --tag=laravel-pagination` |
| Default pagination view | `Paginator::defaultView('pagination::semantic-ui')` ใน `AppServiceProvider::boot()` |
| Seed เฉพาะ class | `php artisan db:seed --class=Product02Seeder` |
| Query string | `$request->getQueryParams()` |
| Form body | `$request->getParsedBody()` |
| Full-text (native) | `whereFullText()` (Laravel 12+) / `laravel/scout` |


---

<!-- ===================== END OF WEEK 08 ===================== -->

# Week 09 — Relationships (Lecture + Lab)

> Course: Practice 954348 · Semester 01/2569
> Sources combined in this file:
> - `lec09-relationships.pdf` (Lecture slides)
> - `lec09-database.zip` (Lecture example files: migration + seeder)
> - `lab09-relationships.pdf` (Lab sheet)
> - `lab09-migrations.zip` (Lab migration files)

---

## Table of Contents

- [Part A — Lecture 09: Relationships](#part-a--lecture-09-relationships)
  - [A1. Eloquent Model Relationship Defining](#a1-eloquent-model-relationship-defining)
  - [A2. one-to-many](#a2-one-to-many-relationship)
  - [A3. Naming conventions](#a3-naming-conventions)
  - [A4. one-to-one](#a4-one-to-one-relationship)
  - [A5. many-to-many](#a5-many-to-many-relationship)
  - [A6. Querying Relations](#a6-querying-relations)
  - [A7. Aggregating Related Models](#a7-aggregating-related-models)
  - [A8. N+1 query problem](#a8-n1-query-problem)
  - [A9. Note of the next chapter](#a9-note-of-the-next-chapter)
  - [A10. Example — relation between Product and Shop (step 1–10)](#a10-example--relation-between-product-and-shop)
  - [A11. Lecture example files (lec09-database.zip)](#a11-lecture-example-files-lec09-databasezip)
  - [A12. References](#a12-references)
- [Part B — Lab 09: Relationships](#part-b--lab-09-relationships)
  - [B1. Task 1 — ShopController same behavior as ProductController](#b1-task-1--update-shopcontroller)
  - [B2. Task 2 — Category model (one-to-many with Product)](#b2-task-2--category-model-one-to-many-with-product)
  - [B3. Task 3 — Show category data in product views + handle N+1](#b3-task-3--display-category-data--handle-n1)
  - [B4. Lab migration files (lab09-migrations.zip)](#b4-lab-migration-files-lab09-migrationszip)
- [Part C — Quick Reference / Checklist](#part-c--quick-reference--checklist)

---

# Part A — Lecture 09: Relationships

**Contents**
- Eloquent Relationships
  - one-to-one
  - one-to-many
  - many-to-many
- Querying Relations
- Example
- References

---

## A1. Eloquent Model Relationship Defining

- We can define model relationships by creating **methods within model class** that return the type of relationships by using a relationship method, and **the name of the method will be the relationship name**, e.g.:

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    function category(): BelongsTo {
        return $this->belongsTo(Category::class);
    }
}
```

- We will learn about relationship methods in the next several slides.
- The relationship methods, e.g., `belongsTo()`, are available to our model via the model's `Illuminate\Database\Eloquent\Model` base class.

### Accessing related model (cont.)

- When we have defined relationships, we can get the related model by calling them like **object properties accessing**, e.g.

```php
$product = Product::where('code', 'PD001')->firstOrFail();

/*
 * With the following code Eloquent will generate SQL
 * related to method name that declares the relationship
 * then send SQL query to get category data.
 */
$category = $product->category; // property accessing
```

- Keep in your mind that we **declare them by using methods** but **retrieve their data by using properties accessing**.

### Model relationships vs database relationships (cont.)

- The relationships defined in model class **do not force** our database to have the same relationships as model.
  - They are just used to generate SQL.
- But for many reasons, e.g.:
  - performance
  - data integrity

  we also define our database to have the same relationships as defined in model.

---

## A2. one-to-many relationship

`Category (1) ──< Product (many)`

- When we want to define one-to-many relationship, we put **`hasMany()`** relationship on **Category** model side and put **`belongsTo()`** relationship on **Product** model side.
  - Remember that we put `belongsTo()` relationship on **the side that contains foreign key**.
- In the case we do not want to get the list of products on Category model side, called **unidirectional**, we just do not add relation method on it.

**class Category**

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    function products(): HasMany {
        return $this->hasMany(Product::class);
    }
}
```

**class Product**

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    function category(): BelongsTo {
        return $this->belongsTo(Category::class);
    }
}
```

---

## A3. Naming conventions

### Naming convention — `belongsTo()`

- Because the `belongsTo()` relationship requires a foreign key, the name of the foreign key in the table uses the following convention:
  - Eloquent determines the foreign key name by examining **the name of the relationship** and suffixing the method name with **`_id`**.
  - So, in this case, Eloquent assumes that the `Product` model has a **`category_id`** column.
    - `category` comes from the name of method, or `category()`.

### Naming convention — `hasMany()`

- Because the `hasMany()` relationship requires a foreign key on the other side of the relationship, the referred foreign key in the other table uses the following convention:
  - Eloquent will take the **"snake_case" name of the parent model** and suffix it with **`_id`**.
  - So, in this example, Eloquent will assume the foreign key column on the `Product` model for `Category` model side is **`category_id`**.
    - `category` comes from the class name of `Category` model.

### Naming convention — specific foreign key

- From the naming convention of `belongsTo()` and `hasMany()`, they may produce different foreign key names.
  - So, in this case, it forces us to name the relationship on the `belongsTo()` side to `category()`.
- In some cases, we cannot use the previous naming convention, so Eloquent allows us to **specify the name of foreign key by passing the second argument** to the relationship method, e.g.

```php
return $this->hasMany(Product::class, 'catetory_id');
```

---

## A4. one-to-one relationship

`Product (1) ── (1) Description`

- When we want to define one-to-one relationship, we have to add a foreign key on **either side** of models.
  - The side that has foreign key uses **`belongsTo()`** relationship method.
  - And the other side uses **`hasOne()`** relationship method.
- And same as one-to-many relationship, for unidirectional, we can ignore the relationship on `hasOne()` side.
- The naming convention of `hasOne()` is the same as `hasMany()`.

### Case 1: foreign key on `descriptions`

**class Product**

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends Model
{
    function description(): HasOne {
        return $this->hasOne(Description::class);
    }
}
```

**class Description**

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Description extends Model
{
    function product(): BelongsTo {
        return $this->belongsTo(Product::class);
    }
}
```

### Case 2: foreign key on `products`

**class Product**

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    function description(): BelongsTo {
        return $this->belongsTo(Description::class);
    }
}
```

**class Description**

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Description extends Model
{
    function product(): HasOne {
        return $this->hasOne(Product::class);
    }
}
```

---

## A5. many-to-many relationship

`Product >──< Shop` (via intermediate table `product_shop`)

- For many-to-many relationship, we require **the intermediate table** to link two models together.
  - On both sides of models use **`belongsToMany()`** relationship method.
- By default, intermediate table:
  - uses `product_id` and `shop_id` to be **composite primary key**.
  - can have `created_at` and `updated_at` timestamp by using **`withTimestamps()`** when we define relationship.

**class Product**

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    function shops(): BelongsToMany {
        return $this
            ->belongsToMany(Shop::class)
            ->withTimestamps();
    }
}
```

**class Shop**

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Shop extends Model
{
    function products(): BelongsToMany {
        return $this
            ->belongsToMany(Product::class)
            ->withTimestamps();
    }
}
```

### Naming Convention — `belongsToMany()`

- For the **intermediate table name**:
  - Eloquent will join the two related model names in **alphabetical order** separated by underscore `_`. (e.g. `product_shop`)
- For the **foreign keys name** in intermediate table:
  - It is the same as `hasMany()` relationship naming convention.
- But we can specify the intermediate table name and foreign keys name by passing additional arguments to `belongsToMany()`, e.g., for `Product` model:

```php
return $this->belongsToMany(Shop::class,
    'product_shop',          'product_id',         'shop_id');
// intermediate_table_name, this side foreign key, other side foreign key
```

---

## A6. Querying Relations

### Getting query object for relation

- When we want to get the **query object** of relation, just call the relationship with **method accessing**, e.g. from the previous relationship of `Product` model and `Category` model:

```php
$category = Category::where('code', 'CT001')->firstOrFail();
$query = $category->products();
$products = $query->where('price', '>', 100)->get();
```

- This will generate SQL like (assume category `CT001` has `id = 1`):

```sql
SELECT * FROM `products` WHERE `category_id` = 1 AND `price` > 100;
```

- ⚠️ Keep in your mind that **logical grouping is required** when you query with `orWhere()` on relation query.

> Summary: `$category->products` (property) = data (Collection) · `$category->products()` (method) = query object (Relation) that can be further chained.

---

## A7. Aggregating Related Models

### Counting Related Models — `withCount()`

- Sometimes we may want to count the number of related models for a given relationship without actually loading the models.
- To accomplish this, we may use the `withCount()` method.
- The `withCount()` method will place a **`{relation_name}_count`** attribute on the resulting models.

```php
$categories = Category::withCount('products')->get();

foreach($categories as $category) {
    echo $category->name.' has '.$category->products_count;
}
```

### Deferred Count Loading — `loadCount()`

- Using the `loadCount()` method, we may load a relationship count **after** the parent model has already been retrieved:

```php
$category = Category::where('code', 'CT001')->firstOrFail();
$category->loadCount('products');

echo $category->name.' has '.$category->products_count;
```

### Other Aggregate Functions

- In addition to the `withCount()` method, Eloquent provides **`withMin()`, `withMax()`, `withAvg()`, and `withSum()`** methods.
- These methods will place a **`{relation_name}_{function}_{column}`** attribute on our resulting models:

```php
$categories = Category::withSum('products', 'price')->get();

foreach($categories as $category) {
    echo $category->name.' has total value '.$category->products_sum_price;
}
```

- Like the `loadCount()` method, deferred versions of these methods are also available.

> Note: the slides have a typo `$categoy` in the echo lines; it is written as `$category` here.

---

## A8. N+1 query problem

- N+1 query problem is a common problem in ORM.
  - First, ORM queries to retrieve a collection of parent objects, e.g., list. **(1 query)**
  - Then, for each of these N parent objects, ORM executes a separate additional query to fetch its associated child (or related) data. **(N queries)**
  - This can lead to significant performance degradation, especially when dealing with a large number of records.
- The primary solution involves using **eager loading** techniques provided by many ORMs to fetch related data in a single query.

### Eager loading with `with()`

- Eloquent provides eager loading by using method **`with()`** and specifying the relationship name to be loaded.

```php
// assume Product has a relation belongsTo to Category.
$query = Product::orderBy('code')->with('category');
```

- When we call a finishing query method, Eloquent also adds additional SQL for specified relationships to retrieve them in the same query as collection retrieval.

---

## A9. Note of the next chapter

- In this chapter, we just **define the relationship and query with relationship constraints**.
- In the next chapter, we will **manipulate the relationship data**.
- So, in the example and assignments, you have to **add relationships information directly to database**.

---

## A10. Example — relation between Product and Shop

- Reuse your previous project from last week, **week-08**.
- `Product` model and `Shop` model have **many-to-many** relationship.
  - From the given file (`lec09-database.zip`), copy:
    - `2026_08_18_082420_create_product_shop_table.php` to `database/migrations`
    - `ProductShopSeeder.php` to `database/seeders`
- Migrate and seed by using the following command:

```bash
php artisan migrate
php artisan db:seed --class=ProductShopSeeder
```

### Wanted result — `products.list`

URL: `localhost:8000/products` — page title **"Product: List"**

- Nav: `Product` | `Shop`
- Search form: **Term**, **Min Price**, **Max Price**, buttons **Filter** / **Clear**
- Button **Create Product** + pagination `< 1 2 >`
- Table "List of Products" with columns: **Code | Name | Price | No. of Shops**

| Code  | Name                                 | Price  | No. of Shops |
|-------|--------------------------------------|-------:|-------------:|
| PD001 | Programming PHP                      | 345.00 | 2 |
| PD002 | JavaScript: The Definitive Guide     | 250.00 | 3 |
| PD003 | Learning PHP, MySQL & JavaScript     | 450.00 | 2 |
| PD004 | Python Crash Course, 2nd Edition     | 560.00 | 1 |
| PD101 | Modern PHP Web Development           | 758.55 | 4 |

### Step 1 — Add relationship to Product model

```php
// import Illuminate\Database\Eloquent\Relations\BelongsToMany
function shops(): BelongsToMany {
    return $this->belongsToMany(Shop::class)->withTimestamps();
}
```

### Step 2 — Add relationship to Shop model

```php
// import Illuminate\Database\Eloquent\Relations\BelongsToMany
function products(): BelongsToMany {
    return $this->belongsToMany(Product::class)->withTimestamps();
}
```

### Step 3 — Add aggregate `withCount()`

Now for route `products.list`, we want to show the number of shops that have the respective product, so in `ProductController` method `list()` change:

```php
$query = $this->search($criteria);
```

to

```php
$query = $this->search($criteria)->withCount('shops');
```

### Step 4 — Update `products/list.blade.php`

Add the column that shows the number of shops:

```blade
{{-- ... Other table elements --}}

@foreach($products as $product)
    <tr>
        {{-- ... Other table elements --}}
        {{-- Add the following <td> --}}
        <td>{{ $product->shops_count }}</td>
    </tr>
@endforeach
```

### Wanted result — `products.view` and `products.view-shops`

**`products.view`** — URL `localhost:8000/products/PD101`, title **"Product: PD101"**
- Buttons: **View Shops** (→ `products.view-shops`), **Update**, **Delete**
- Shows Code, Name, Price, and description text.

**`products.view-shops`** — URL `localhost:8000/products/PD101/shops`, title **"Product: PD101: Shops"**
- Search form: **Term** only, **Filter** / **Clear**
- **< Back** link (→ `products.view`)
- Caption: "List of Shops for Modern PHP Web Development"
- Table columns: **Code | Name | Owner | No. of Products**

| Code  | Name         | Owner   | No. of Products |
|-------|--------------|---------|----------------:|
| SH001 | Pachara Shop | Pachara | 2 |
| SH002 | Phasit Shop  | Phasit  | 3 |
| SH003 | Other Shop   | Pachara | 3 |
| SH102 | MadA Shop    | Adam    | 1 |

### Step 5 — Update `SearchableController`

We add `Relation` to the union type of `$query` to allow passing relation from model.

Change:

```php
function filter(Builder $query, array $criteria, array $filterOptions) : void {
    // method body
}
```

To:

```php
// import Illuminate\Database\Eloquent\Relations\Relation
function filter(
    Builder|Relation $query,
    array $criteria,
    array $filterOptions,
) : void {
    // method body
}
```

Update `filterByTerm()` method to match the new signature. Change:

```php
function filterByTerm(
    Builder $query,
    string $term,
    array $termOptions,
) : void {
    // method body
}
```

To:

```php
function filterByTerm(
    Builder|Relation $query,
    string $term,
    array $termOptions,
) : void {
    // method body
}
```

### Step 6 — Update `ProductController`

Change:

```php
function filter(Builder $query, array $search , array $filterOptions) : void {
    // method body
}
```

To:

```php
// import Illuminate\Database\Eloquent\Relations\Relation
function filter(
    Builder|Relation $query,
    array $search,
    array $filterOptions,
) : void {
    // method body
}
```

Update `filterByMinPrice()` and `filterByMaxPrice()` to match the new signature.

Change:

```php
function filterByMinPrice(
    Builder $query,
    float $minPrice,
) : void {
    // method body
}

function filterByMaxPrice(
    Builder $query,
    float $maxPrice,
) : void {
    // method body
}
```

To:

```php
function filterByMinPrice(
    Builder|Relation $query,
    float $minPrice,
) : void {
    // method body
}

function filterByMaxPrice(
    Builder|Relation $query,
    float $maxPrice,
) : void {
    // method body
}
```

### Step 7 — Method for getting shops of product (in `ProductController`)

```php
function viewShops(string $productCode, ServerRequestInterface $request): View
{
    $product = $this->find($productCode);
    $shopController = \resolve(ShopController::class);
    $criteria = $shopController->prepareCriteria($request->getQueryParams());
    $filterOptions = $shopController->getFilterOptions();
    $shopQuery = $product->shops()->withCount('products');
    $shopController->filter($shopQuery, $criteria, $filterOptions);

    return view('products.view-shops', [
        'criteria' => $criteria,
        'product' => $product,
        'shops' => $shopQuery->paginate($shopController::MAX_ITEMS),
    ]);
}
```

> Key idea: reuse another controller's search/filter logic via `\resolve(ShopController::class)` and pass a **relation query** (`$product->shops()`) into its `filter()` — this is why Step 5–6 add `Builder|Relation`.

### Step 8 — Create `products/view-shops.blade.php`

This template is very similar to `shops/list.blade.php`.

- Change template in `@extends` to `'products.main'`.
  - Title is `"{$product->code}: Shops"`
- Change search and clear URL to route `products.view-shops` in section header.

```blade
<search><form action="{{ route('products.view-shops', [
    'product' => $product->code,
]) }}" method="get" class="app-cmp-search-form">
    {{-- ... Other form controls --}}
    <a href="{{ route('products.view-shops', [
        'product' => $product->code,
    ]) }}">Clear</a>
</form></search>
```

- Add back link to `products.view`.

```blade
<nav class="app-cmp-links-bar">
    <ul>
        {{-- Change show create-form to back link to product-view --}}
        <li>
            <a href="{{ route('products.view', [
                'product' => $product->code,
            ]) }}">&lt; Back</a>
        </li>
    </ul>

    {{ $shops->withQueryString()->links() }}
</nav>
```

- Update table caption to use `$product->name`.

```blade
<caption>List of Shops for {{ $product->name }}</caption>
```

- Add **No. of Products** column.

```blade
{{-- ... Other table elements --}}

@foreach($shops as $shop)
    <tr>
        {{-- ... Other table elements --}}
        {{-- Add the following <td> --}}
        <td>{{ $shop->products_count }}</td>
    </tr>
@endforeach
```

### Step 9 — Add route to `routes/web.php`

```php
// In Product controller group.
// After route products.delete.
Route::get(
    '/shops',
    'viewShops',
)->name('view-shops');
```

### Step 10 — Update `products/view.blade.php`

Add link to route `products.view-shops`:

```blade
{{-- Add Show Shop to navigation --}}
<nav>
    <ul>
        <li>
            <a href="{{ route('products.view-shops', [
                'product' => $product->code,
            ]) }}">View Shops</a>
        </li>
        {{-- ... link to Update --}}
        {{-- ... link to Delete --}}
    </ul>
</nav>
```

---

## A11. Lecture example files (`lec09-database.zip`)

### `database/migrations/2026_08_18_082420_create_product_shop_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_shop', function (Blueprint $table) {
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('shop_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            // Declaring the composite primary key
            $table->primary(['product_id', 'shop_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_shop');
    }
};
```

### `database/seeders/ProductShopSeeder.php`

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductShopSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currentTstmp = now();

        $products = DB::table('products')
            ->select('id')
            ->get();

        $shops = DB::table('shops')
            ->select('id')
            ->get();

        $shopMax = \min(5, \count($shops));
        $relatedShops = [
            \min(2, $shopMax),
            \min(3, $shopMax),
            \min(2, $shopMax),
            \min(1, $shopMax),
            \min(4, $shopMax),
        ];
        $productMax = \min(\count($relatedShops), \count($products));

        $data = [];
        for ($i = 0; $i < $productMax; $i++) {
            $relatedShop = $relatedShops[$i];
            for ($j = 0; $j < $relatedShop; $j++) {
                $data[] = [
                    'created_at' => $currentTstmp,
                    'updated_at' => $currentTstmp,
                    'product_id' => $products[$i]->id,
                    'shop_id' => $shops[($i + $j) % $shopMax]->id,
                ];
            }
        }

        DB::table('product_shop')->insert($data);
    }
}
```

---

## A12. References

- https://laravel.com/docs/13.x/eloquent-relationships

---

# Part B — Lab 09: Relationships

> **NOTE:** Use the project from example of week 09.

---

## B1. Task 1 — Update `ShopController`

**1. Update `ShopController` to be the same behavior as `ProductController`.**

### Expected: `shops.list`

URL: `localhost:8000/shops` — title **"Shop: List"**

- Nav: `Product` | `Shop`
- Search: **Term**, **Filter** / **Clear**
- Button **Create Shop** + pagination `< 1 2 3 >`
- Table "List of Shops" columns: **Code | Name | Owner | No. of Products**

| Code  | Name         | Owner   | No. of Products |
|-------|--------------|---------|----------------:|
| SH001 | Pachara Shop | Pachara | 2 |
| SH002 | Phasit Shop  | Phasit  | 3 |
| SH003 | Other Shop   | Pachara | 3 |
| SH101 | Adam Shop    | Adam    | 3 |
| SH102 | MadA Shop    | Adam    | 1 |

### Expected: `shops.view`

URL: `localhost:8000/shops/SH001` — title **"Shop: SH001"**

- Buttons: **View Products** (→ `shops.view-products`), **Update**, **Delete**
- Fields: Code `SH001`, Name `Pachara Shop`, Owner `Pachara`, Location `18.80045380,98.94889980`, Address `College of Arts, Media and Technology / 239 Huaykaew Rd., Suthep, / Mueang Chiang Mai District, Chiang Mai 50200`

### Expected: `shops.view-products`

URL: `localhost:8000/shops/SH001/products` — title **"Shop: SH001: Products"**

- Search: **Term**, **Min Price**, **Max Price**, **Filter** / **Clear**
- **< Back** link (→ `shops.view`)
- Caption: "List of Products for Pachara Shop"
- Table columns: **Code | Name | Price | No. of Shops**

| Code  | Name                       | Price  | No. of Shops |
|-------|----------------------------|-------:|-------------:|
| PD001 | Programming PHP            | 345.00 | 2 |
| PD101 | Modern PHP Web Development | 758.55 | 4 |

> The search criteria of this route are the same as route **`products.list`** that can search by **search term, min price and max price**. ⚠️ **Don't create a new one, just reuse from `ProductController`.**
> (Same pattern as `ProductController::viewShops()` in Lecture Step 7, but reversed: resolve `ProductController` inside `ShopController`.)

---

## B2. Task 2 — Category model (one-to-many with Product)

**2. Create `Category` model that has relation *one-to-many* with `Product` model.**

Extract the given file **`lab09-migrations.zip`** to **`database/migrations`**. Then run:

```bash
php artisan migrate
```

Create **`Category`** model and **`CategoryController`**. Then create **category routes that are the same as shop routes**.

### Expected: `categories.list`

URL: `localhost:8000/categories` — title **"Category: List"**

- Nav: `Product` | `Shop` | `Category`
- Search: **Term**, **Filter** / **Clear**
- Button **Create Category**
- Table "List of Categories" columns: **Code | Name | No. of Products**

| Code  | Name       | No. of Products |
|-------|------------|----------------:|
| CT001 | PHP        | 4 |
| CT002 | Python     | 1 |
| CT003 | JavaScript | 4 |

> This route can search by using **search term on `code` and `name`**.

### Expected: `categories.create-form`

URL: `localhost:8000/categories/create` — title **"Category: Create"**

- Fields: **Code \***, **Name \***, **Description \*** (textarea), button **Create**

### Expected: `categories.view`

URL: `localhost:8000/categories/CT001` — title **"Category: CT001"**

- Buttons: **View Products** (→ `categories.view-products`), **Update**, **Delete**
- Shows Code `CT001`, Name `PHP`, then description (`PHP` / `Description`)

### Expected: `categories.update-form`

URL: `localhost:8000/categories/CT001/update` — title **"Category: CT001"**

- Fields pre-filled: **Code \*** `CT001`, **Name \*** `PHP`, **Description \*** `PHP\nDescription`, button **Update**

### Expected: `categories.view-products`

URL: `localhost:8000/categories/CT001/products` — title **"Category: CT001: Products"**

- Search: **Term**, **Min Price**, **Max Price**, **Filter** / **Clear**
- **< Back** link (→ `categories.view`)
- Caption: "List of Products for PHP"
- Table columns: **Code | Name | Price | No. of Shops**

| Code  | Name                                 | Price  | No. of Shops |
|-------|--------------------------------------|-------:|-------------:|
| PD001 | Programming PHP                      | 345.00 | 2 |
| PD101 | Modern PHP Web Development           | 758.55 | 4 |
| PD103 | How To Be A Web Developer In 90 Days | 892.72 | 0 |
| PD104 | Designing Web APIs                   | 756.49 | 0 |

> The search criteria are the same as route **`products.list`** that can search by **search term, min price and max price**.

### Category routes summary (same as shop routes)

| Route name                    | URL                               |
|-------------------------------|-----------------------------------|
| `categories.list`             | `/categories`                     |
| `categories.create-form`      | `/categories/create`              |
| `categories.view`             | `/categories/{category}`          |
| `categories.update-form`      | `/categories/{category}/update`   |
| `categories.view-products`    | `/categories/{category}/products` |
| (+ create / update / delete actions, same as shop routes) | |

---

## B3. Task 3 — Display category data + handle N+1

**3. Update all views that display product data except `categories.view-products` to display category data.**

Update route **`products.list`**, **`products.view`** and **`shops.view-products`** to show category and **handle N+1 query problem in collection retrievals** (use `with('category')`).

### Expected: `products.list`

URL: `localhost:8000/products` — nav `Product` | `Shop` | `Category`

- New **Category** column — values are **links to `categories.view`**
- Columns: **Code | Name | Category | Price | No. of Shops**

| Code  | Name                             | Category (link) | Price  | No. of Shops |
|-------|----------------------------------|-----------------|-------:|-------------:|
| PD001 | Programming PHP                  | PHP             | 345.00 | 2 |
| PD002 | JavaScript: The Definitive Guide | JavaScript      | 250.00 | 3 |
| PD003 | Learning PHP, MySQL & JavaScript | JavaScript      | 450.00 | 2 |
| PD004 | Python Crash Course, 2nd Edition | Python          | 560.00 | 1 |
| PD101 | Modern PHP Web Development       | PHP             | 758.55 | 4 |

### Expected: `products.view`

URL: `localhost:8000/products/PD001` — title **"Product: PD001"**

- Buttons: **View Shops**, **Update**, **Delete**
- New row: **Category :: [CT001] PHP** (between Name and Price)

### Expected: `shops.view-products`

URL: `localhost:8000/shops/SH002/products` — title **"Shop: SH002: Products"**

- New **Category** column — ⚠️ **Don't create links** (plain text)
- Caption: "List of Products for Phasit Shop"

| Code  | Name                             | Category   | Price  | No. of Shops |
|-------|----------------------------------|------------|-------:|-------------:|
| PD001 | Programming PHP                  | PHP        | 345.00 | 2 |
| PD002 | JavaScript: The Definitive Guide | JavaScript | 250.00 | 3 |
| PD101 | Modern PHP Web Development       | PHP        | 758.55 | 4 |

> **Note:** For `products.create-form` and `products.update-form`, we **don't touch them for a while**, because we have to learn about **relationships manipulation** in the next chapter.

---

## B4. Lab migration files (`lab09-migrations.zip`)

Extract all to `database/migrations`, then `php artisan migrate`. Run order (by timestamp):

1. `2026_09_09_083136_create_categories_table.php` — create `categories` + insert CT001–CT003
2. `2026_09_09_083721_add_column_category_id_to_products_table.php` — add nullable `category_id` to `products`
3. `2026_09_09_140830_add_foreign_category_id_to_product_table.php.php` — fill `category_id` from name/description, then make it NOT NULL + foreign key (restrict on delete)

> ⚠️ File #3 is named with a double extension `.php.php` in the zip. Laravel may still load it, but renaming to `.php` is safer.

### 1) `2026_09_09_083136_create_categories_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->index('created_at');
            $table->index('updated_at');
            $table->string('code', 64)->unique();
            $table->string('name', 255);
            $table->text('description');
        });

        $currentTstmp = now();

        DB::table('categories')->insert([
            [
                'created_at' => $currentTstmp,
                'updated_at' => $currentTstmp,
                'code' => 'CT001',
                'name' => 'PHP',
                'description' => "PHP\r\nDescription",
            ],
            [
                'created_at' => $currentTstmp,
                'updated_at' => $currentTstmp,
                'code' => 'CT002',
                'name' => 'Python',
                'description' => "Python\r\nDescription",
            ],
            [
                'created_at' => $currentTstmp,
                'updated_at' => $currentTstmp,
                'code' => 'CT003',
                'name' => 'JavaScript',
                'description' => "JavaScript\r\nDescription",
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
```

### 2) `2026_09_09_083721_add_column_category_id_to_products_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedBigInteger('category_id')->nullable()->after('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('category_id');
        });
    }
};
```

### 3) `2026_09_09_140830_add_foreign_category_id_to_product_table.php.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $categories = DB::table('categories')->get();
        DB::table('products')->update(['category_id' => null]);
        foreach ($categories as $category) {
            DB::table('products')
                ->whereAny(['name', 'description'], 'LIKE', "%{$category->name}%")
                ->update(['category_id' => $category->id]);
        }
        if (\count($categories) > 0) {
            DB::table('products')
                ->whereNull('category_id')
                ->update(['category_id' => $categories[0]->id]);
        }

        Schema::table('products', function (Blueprint $table) {
            $table->unsignedBigInteger('category_id')->nullable(false)->change();
            $table->foreign('category_id')
                ->references('id')
                ->on('categories')
                ->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->unsignedBigInteger('category_id')->nullable()->change();
        });
    }
};
```

---

# Part C — Quick Reference / Checklist

### Relationship methods cheat sheet

| Relationship | Side with FK | Side without FK | Return type import |
|---|---|---|---|
| one-to-one   | `belongsTo()` | `hasOne()`  | `Relations\BelongsTo` / `Relations\HasOne` |
| one-to-many  | `belongsTo()` (many side) | `hasMany()` (one side) | `Relations\BelongsTo` / `Relations\HasMany` |
| many-to-many | both sides: `belongsToMany()` (+ `->withTimestamps()`) — FK in pivot table | | `Relations\BelongsToMany` |

### Access patterns

| Code | Result |
|---|---|
| `$product->category` | related model (property → data) |
| `$category->products` | Collection of products (property → data) |
| `$category->products()` | Relation query object (method → chain `where`, `paginate`, …) |
| `Model::with('rel')` | eager load (fix N+1) |
| `Model::withCount('rel')` | adds `rel_count` |
| `$model->loadCount('rel')` | deferred count |
| `withSum('rel','col')` / `withMin` / `withMax` / `withAvg` | adds `rel_sum_col` etc. |

### Lab 09 checklist

- [ ] **Task 1** `ShopController` behaves like `ProductController`
  - [ ] `shops.list` has **No. of Products** (`withCount('products')`)
  - [ ] `shops.view` has **View Products** button
  - [ ] `shops.view-products` (`/shops/{shop}/products`) — reuse `ProductController` search (term, min/max price), back link, `No. of Shops` column
  - [ ] Route added in shop route group
- [ ] **Task 2** Category
  - [ ] Extract `lab09-migrations.zip` → `database/migrations`, `php artisan migrate`
  - [ ] `Category` model with `products(): HasMany`; `Product` model with `category(): BelongsTo`
  - [ ] `CategoryController` + routes same as shop (list, create-form, create, view, update-form, update, delete, view-products)
  - [ ] `categories.list` search term on `code`, `name`; **No. of Products** column
  - [ ] `categories.view-products` search term/min/max price (reuse `ProductController`)
  - [ ] Views: list, create-form, view, update-form, view-products; nav bar adds **Category**
- [ ] **Task 3** Category data in product views
  - [ ] `products.list` — Category column, **link** to `categories.view`, `with('category')`
  - [ ] `products.view` — `Category :: [CODE] Name`
  - [ ] `shops.view-products` — Category column, **no link**, `with('category')`
  - [ ] Don't touch `products.create-form` / `products.update-form` yet
