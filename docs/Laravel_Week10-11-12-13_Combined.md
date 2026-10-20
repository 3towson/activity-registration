# Laravel — Week 10–13 รวมเนื้อหาทั้งหมด (Lecture + Lab)

> Practice 954348 — 01/2569 (Laravel 13.x)
> รวมจาก 4 ไฟล์: `week10-updating-with-relationship.md`, `Week11_Session_Lecture_Lab.md`, `lec12-lab12-authentication-authorization.md`, `Week13_Errors_Handling_Lecture_Lab.md`
> เนื้อหาแต่ละสัปดาห์คัดลอกจากไฟล์ต้นฉบับครบทุกบรรทัด ไม่มีการตัดหรือแก้ไข

## สารบัญรวม

1. [Week 10 — Updating with Relationship (Lecture + Lab 10)](#week-10--updating-with-relationship-lecture--lab-10)
2. [Week 11 — Session (Lecture + Lab)](#week-11--session-lecture--lab)
3. [Week 12 — Authentication & Authorization (Lecture + Lab + Migrations)](#week-12--authentication--authorization-lecture--lab--migrations)
4. [Week 13 — Errors Handling (Lecture + Lab)](#week-13--errors-handling-lecture--lab)

---

# Week 10 — Updating with Relationship (Lecture + Lab 10)

> Practice 954348 — 01/2569
> Reference: https://laravel.com/framework/docs/13.x/eloquent-relationships

---

## สารบัญ

- [Part A — Lecture](#part-a--lecture)
  - [1. More relationship querying](#1-more-relationship-querying)
  - [2. Updating with relationship](#2-updating-with-relationship)
  - [3. Directive @selected and @checked](#3-directive-selected-and-checked)
  - [4. Example — Add/Remove Shop in Product](#4-example--addremove-shop-in-product)
- [Part B — Lab 10](#part-b--lab-10-updating-with-relationship)
- [Appendix — Quick Reference](#appendix--quick-reference)

---

# Part A — Lecture

## Contents

- More relationship querying
  - `whereHas()` / `orWhereHas()`
  - `whereDoesntHave()` / `orWhereDoesntHave()`
- Updating with relationship
- Directive `@selected` and `@checked`
- Example
- References

---

## 1. More relationship querying

### Querying is based on other model value

- In some cases, we want to query the models that are based on other related models' values.
- For example:
  - We want to list all `Shop` models that contain `Product` models that have price > 100.
  - Or we want to list all `Product` models that have category code starting with `CT1`.
- Eloquent provides the following methods to do that:
  - `whereHas()` / `orWhereHas()`
  - `whereDoesntHave()` / `orWhereDoesntHave()`

### Example — Product model

```php
// Product
function category(): BelongsTo {
    return $this->belongsTo(Category::class);
}

function shops(): BelongsToMany {
    return $this->belongsToMany(Shop::class);
}
```

### Example — Category and Shop models

```php
// Category
function products(): HasMany {
    return $this->hasMany(Product::class);
}

// Shop
function products(): BelongsToMany {
    return $this->belongsToMany(Product::class);
}
```

### `whereHas()` / `orWhereHas()`

- Usage:
  - `whereHas('relation_name', $closure_to_query_related_model)`
  - `$closure_to_query_related_model` provides the query object of the related model as a function parameter.
  - Returns the query object.
- Adds `where exists` with the given query in `$closure_to_query_related_model`.
- `orWhereHas()` is the same as `whereHas()` but joins with the `OR` operator.

#### Example

List all products that have category code starting with `CT1`.

```php
// import Illuminate\Database\Eloquent\Builder
$products = Product::whereHas('category', function (Builder $query) {
    // $query is for Category model
    $query->where('code', 'LIKE', 'CT1%');
})->get();
```

Generated SQL is like:

```sql
select * from `products` where exists
  (select * from `categories` where
     `products`.`category_id` = `categories`.`id` and `code` LIKE 'CT1%')
```

### `whereDoesntHave()` / `orWhereDoesntHave()`

- Usage:
  - `whereDoesntHave('relation_name', $closure_to_query_related_model)`
  - `$closure_to_query_related_model` provides the query object of the related model as a function parameter.
  - Returns the query object.
- Adds `where not exists` with the given query in `$closure_to_query_related_model`.
- `orWhereDoesntHave()` is the same as `whereDoesntHave()` but joins with the `OR` operator.

#### Example

List all shops that do not contain the product with code `PD001`.

```php
// import Illuminate\Database\Eloquent\Builder
$shops = Shop::whereDoesntHave('products', function (Builder $query) {
    // $query is for Product model
    $query->where('code', 'PD001');
})->get();
```

Generated SQL is like:

```sql
select * from `shops` where not exists
  (select * from `products` inner join `product_shop`
     on `products`.`id` = `product_shop`.`product_id` where
     `shops`.`id` = `product_shop`.`shop_id` and `code` = 'PD001')
```

---

## 2. Updating with relationship

### Create related model — `save()`

We can use `save()` from the relationship query to create related models.

```php
$product = new Product();
$product->code = 'PD001';
$product->name = 'Book';

$category = Category::where('code', 'CT001')->firstOrFail();
$category->products()->save($product);
```

- Eloquent automatically assigns `category_id` to the `Product` model.

### Create related model — `saveMany()`

Create multiple related models with `saveMany()`.

```php
$product1 = new Product();
$product1->code = 'PD001';
$product1->name = 'Book';

$product2 = Product::where('code', 'PD002')->firstOrFail();

$category = Category::where('code', 'CT001')->firstOrFail();
$category->products()->saveMany([$product1, $product2]);
```

### Create related model — `create()` and `createMany()`

For Mass Assignment, relationship queries also support `create()` and `createMany()`.

```php
$category = Category::where('code', 'CT001')->firstOrFail();
$category->products()->createMany([
    ['code' => 'PD001', 'name' => 'Book'],
    ['code' => 'PD002', 'name' => 'Pencil'],
]);
```

- The related model has to be set up for Mass Assignment, `#[Fillable(...)]`.

### Recursively Saving Models — `push()`

To save a model and all its associated relationships, use `push()`.

```php
$category = Category::where('code', 'CT001')->firstOrFail();
$category->products[0]->name = 'The new Book';
$category->products[1]->name = 'The new pencil';

/* The following statement not only saves category data
   but also saves the first and the second product. */
$category->push();
```

### BelongsTo Relationship — `associate()`

- To assign a child model (BelongsTo relationship) to a new parent model, use `associate()`.
- **Don't set the `table_name_id` field directly — it breaks the internal process of Eloquent.**

```php
$product  = Product::where('code', 'PD001')->firstOrFail();
$category = Category::where('code', 'CT003')->firstOrFail();

// The following code will change $product to category CT003.
$product->category()->associate($category);
$product->save(); // save() is required
```

We can also just use the id of the category (assume category `CT003` has id = 3):

```php
$product->category()->associate(3);
$product->save(); // save() is required
```

### BelongsTo Relationship — `dissociate()`

- To remove a parent model from a child model, use `dissociate()`.
- The foreign key has to be nullable.

```php
$product = Product::where('code', 'PD001')->firstOrFail();

// The following code will remove category from $product.
$product->category()->dissociate();
$product->save(); // save() is required
```

### BelongsToMany — `attach()`

Eloquent provides methods to make many-to-many relationships more convenient. E.g., add `Product` to `Shop` using `attach()`.

```php
$shop     = Shop::where('code', 'SH001')->firstOrFail();
$product1 = Product::where('code', 'PD001')->firstOrFail();
$product2 = Product::where('code', 'PD002')->firstOrFail();
$product3 = Product::where('code', 'PD003')->firstOrFail();

$shop->products()->attach($product1);              // attach single model
$shop->products()->attach([$product2, $product3]); // attach multiple models
```

> หมายเหตุ: ในสไลด์ต้นฉบับพิมพ์ผิดเป็น `$prodcut2` — แก้เป็น `$product2` แล้ว

### BelongsToMany — `detach()`

Remove `Product` from `Shop` using `detach()`.

```php
$shop     = Shop::where('code', 'SH001')->firstOrFail();
$product1 = $shop->products[0];
$product2 = $shop->products[1];
$product3 = $shop->products[2];

$shop->products()->detach($product1);              // detach single model
$shop->products()->detach([$product2, $product3]); // detach multiple models
```

> หมายเหตุ: ในสไลด์ต้นฉบับพิมพ์ผิดเป็น `dettach()` และ `$prodcut2` — ชื่อเมธอดที่ถูกต้องคือ `detach()`

### BelongsToMany — `sync()`

- To `attach()` and `detach()` at the same time, use `sync()`.
- Any models not in the given array will be removed from the intermediate table.

```php
$shop     = Shop::where('code', 'SH001')->firstOrFail();
$product1 = Product::where('code', 'PD001')->firstOrFail();
$product2 = Product::where('code', 'PD002')->firstOrFail();

$shop->products()->sync([$product1, $product2]);
// Now $shop has only 2 products: PD001 and PD002.
```

### Refresh model with data from database — `refresh()`

- `attach()`, `detach()` and `sync()` only update the data in the database.
- Our in-memory models do **not** get updated.
- To use related models after calling those methods, refresh the model with `refresh()`.

```php
$product = Product::where('code', 'PD001')->firstOrFail();
$shop    = Shop::where('code', 'SH001')->firstOrFail();

$product->shops()->attach($shop);

// Only calling
//   $product->shops[count($product->shops) - 1];
// would not get the attached shop,
// we have to call $product->refresh() first.
$product->refresh();
$attached_shop = $product->shops[count($product->shops) - 1];
```

### Using id instead of entire model

Like `associate()`, many relationship-updating methods accept an id instead of a model. So `attach()`, `detach()` and `sync()` work with ids.

```php
$shop->products()->attach(1);      // add product id = 1 to $shop
$shop->products()->attach([2, 3]); // add product id = 2 and 3 to $shop
```

---

## 3. Directive `@selected` and `@checked`

### Assign old value to controls

On an updating page we assign the old value to controls, e.g.:

```html
<input type="text" name="code" value="{{ $product->code }}" />
```

For drop-down lists, checkboxes, or radio buttons we must assign the `selected` or `checked` attribute to the old option. Generated HTML would be like:

```html
<!-- Assume the old value of category is 'CT002' -->
<select name="category">
    <option value="CT001">[CT001] PHP</option>
    <option value="CT002" selected>[CT002] JavaScript</option>
    <option value="CT003">[CT003] Python</option>
</select>
```

### Assign `selected` or `checked` in Blade templates

Compare the drop-down value with the old value to add `selected` to the related `<option>`.

With `@if` directive:

```blade
<select name="category">
    @foreach($categories as $category)
        <option value="{{ $category->code }}"
            @if($product->category->code === $category->code) selected @endif
        >[{{ $category->code }}] {{ $category->name }}</option>
    @endforeach
</select>
```

Or with the ternary operator:

```blade
<select name="category">
    @foreach($categories as $category)
        <option value="{{ $category->code }}"
            {{ ($product->category->code === $category->code) ? 'selected' : '' }}
        >[{{ $category->code }}] {{ $category->name }}</option>
    @endforeach
</select>
```

### Laravel 9+, `@selected` and `@checked`

- From Laravel 9 there is shorter syntax: `@selected` and `@checked`.
- Usage:
  - `@selected(expression)`
  - `@checked(expression)`
- If `expression` is true, it generates the word `selected` or `checked` respectively.

#### `@selected` Example

```blade
<select name="category">
    @foreach($categories as $category)
        <option value="{{ $category->code }}"
            @selected($product->category->code === $category->code)
        >[{{ $category->code }}] {{ $category->name }}</option>
    @endforeach
</select>
```

---

## 4. Example — Add/Remove Shop in Product

- Reuse project from week-09.
- Add shop to product.
- Remove shop from product.

### Routes overview

- **Adding Shop to Product:**
  - `GET /products/{product}/shops/add` — show adding form.
  - `POST /products/{product}/shops` — add selected shop to product.
- **Removing Shop from Product:**
  - `POST /products/{product}/shops/remove` — remove selected shop from product.

### Flow (จากภาพหน้าจอในสไลด์)

**Add Shop to Product**

- Update route `products.view-shops` (URL `/products/PD001/shops`, หัวข้อ "Product: PD001: Shops")
  - Add link **"Add Shops"** to route `products.add-shops-form`.
  - ตาราง: Code | Name | Owner | No. of Products | ปุ่ม Remove (เช่น SH001 Pachara Shop, SH002 Phasit Shop)
- In route `products.add-shops-form` (URL `/products/PD001/shops/add`, หัวข้อ "Product: PD001: Add Shops")
  - List all shops that **do not contain** the given product (e.g., SH001 and SH002 do not show in this list).
  - Has pagination and a search form (Term, Filter, Clear).
  - Add back link to `products.view-shops`.
  - ตาราง: Code | Name | Owner | No. of Products | ปุ่ม Add
- User clicks **Add** to submit form-data to route `products.add-shop`.
  - `products.add-shop` adds the submitted shop to the product, then **redirects back** to `products.add-shops-form`.
  - Shop SH101 no longer appears in the list because it has been added.
- Click **< Back** to `products.view-shops` → SH101 is now in the shop list of PD001.

**Remove Shop from Product**

- In `products.view-shops`, user clicks **Remove** to submit form-data to route `products.remove-shop`.
- `products.remove-shop` removes the submitted shop from the product, then **redirects back** to `products.view-shops`.
- SH101 is removed from the list.

### Step 1 — Add filter out Shop by Product (ProductController)

When adding shops to a product, make sure the shops don't already have this product.

```php
function filterOutShopByProduct(
    Builder|Relation $shopQuery,
    Product $product,
): void {
    /* We use whereDoesntHave() for getting shops that do not
     * contain this product, in this case, we use code for checking.
     */
    $shopQuery->whereDoesntHave(
        'products',
        static function (Builder $productQuery) use ($product): void {
            $productQuery->where('code', $product->code);
        },
    );
}
```

### Step 2 — `products.add-shops-form` (ProductController)

```php
class ProductController extends SearchableController
{
    // Now we add showAddShopsForm() method
    function showAddShopsForm(
        string $productCode,
        ServerRequestInterface $request,
    ): View {
        $product = $this->find($productCode);
        $shopController = \resolve(ShopController::class);
        $criteria = $shopController->prepareCriteria($request->getQueryParams());

        $shopQuery = $shopController->getQuery()->withCount('products');
        $this->filterOutShopByProduct($shopQuery, $product);

        return view('products.add-shops-form', [
            'criteria' => $criteria,
            'product'  => $product,
            'shops'    => $shopQuery->paginate($shopController::MAX_ITEMS),
        ]);
    }
}
```

### Step 3 — `products.add-shop` (ProductController)

- Use `attach()` to add the shop to the product.
- Use `redirect()->back()` instead of `redirect()->route()` to keep the query string for searching.
  - `redirect()->back()` uses session to keep the previous URL, then redirects to it.
  - (Session will be covered in the next chapter.)

```php
class ProductController extends SearchableController
{
    // Now we add addShop() method
    function addShop(
        string $productCode,
        ServerRequestInterface $request,
    ): RedirectResponse {
        $product = $this->find($productCode);
        $shopController = \resolve(ShopController::class);
        $data = $request->getParsedBody();

        // To make sure that no duplicate shop.
        $shopQuery = $shopController->getQuery();
        $this->filterOutShopByProduct($shopQuery, $product);

        $shop = $shopQuery
            ->where('code', $data['shop'])
            ->firstOrFail();

        // Add $shop to $product
        $product->shops()->attach($shop);

        return redirect()->back();
    }
}
```

### Step 4 — Update `routes/web.php`

Change route `products.view-shops` to grouped routes.

```php
// ... In ProductController routes group
// Change Route::get('/shops', 'viewShops')->name('view-shops');
// TO:
Route::prefix('/shops')->group(static function (): void {
    Route::get('', 'viewShops')->name('view-shops');
    Route::post('', 'addShop')->name('add-shop');
    Route::get('/add', 'showAddShopsForm')->name('add-shops-form');
});
```

### Step 5 — Template `products/add-shops-form.blade.php`

- Same as `products.view-shops`.
- In section `header`:

```blade
<!-- For search form, change action/clear to route products.add-shops-form -->
<!-- add form#app-form-add-shop to <nav> -->
<nav>
    <form action="{{ route('products.add-shop', [
        'product' => $product->code,
    ]) }}" id="app-form-add-shop" method="post">
        @csrf
    </form>

    <!-- Change back link to products.view-shops -->
</nav>
```

### Step 6 — Submission buttons for adding a specific shop

In section `content`. Use `$shop->code` to refer to the added shop.

```blade
<table>
    <!-- table header -->
    <tbody>
        @foreach($shops as $shop)
            <tr>
                <!-- Other columns -->
                <td>
                    <button type="submit"
                        form="app-form-add-shop"
                        name="shop" value="{{ $shop->code }}">
                        Add
                    </button>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
```

### Step 7 — Update `products.view-shops` (link to add form)

```blade
<!-- Link to route products.add-shops-form -->
<a href="{{ route('products.add-shops-form', [
    'product' => $product->code,
]) }}">Add Shops</a>
```

### Step 8 — `products.remove-shop` (ProductController)

Use `detach()` to remove the shop from the product.

```php
class ProductController extends SearchableController
{
    // Now we add removeShop() method
    function removeShop(
        string $productCode,
        ServerRequestInterface $request,
    ): RedirectResponse {
        $product = $this->find($productCode);
        $data = $request->getParsedBody();

        // To get existing shop
        $shop = $product->shops()->where('code', $data['shop'])->firstOrFail();

        // Remove $shop from $product
        $product->shops()->detach($shop);

        return redirect()->back();
    }
}
```

### Step 9 — Update `routes/web.php`

```php
// ... In prefix /products/{product}/shops
// ... After route products.add-shops-form
Route::post('/remove', 'removeShop')->name('remove-shop');
```

### Step 10 — Update `products.view-shops` (remove form)

```blade
<!-- In <nav> of section header, add form#app-form-remove-shop -->
<nav>
    <form action="{{ route('products.remove-shop', [
        'product' => $product->code,
    ]) }}" id="app-form-remove-shop" method="post">
        @csrf
    </form>

    <!-- other links -->
</nav>
```

### Step 11 — Submission buttons for removing a specific shop

Use `$shop->code` to refer to the removed shop.

```blade
<!-- In <table> of section content -->
<table>
    <!-- table header -->
    <tbody>
        @foreach($shops as $shop)
            <tr>
                <!-- Other columns -->
                <td>
                    <button type="submit"
                        form="app-form-remove-shop"
                        name="shop" value="{{ $shop->code }}">
                        Remove
                    </button>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
```

### Final routes (Product → Shops)

| Method | URI | Controller method | Route name |
|---|---|---|---|
| GET | `/products/{product}/shops` | `viewShops` | `products.view-shops` |
| POST | `/products/{product}/shops` | `addShop` | `products.add-shop` |
| GET | `/products/{product}/shops/add` | `showAddShopsForm` | `products.add-shops-form` |
| POST | `/products/{product}/shops/remove` | `removeShop` | `products.remove-shop` |

---

# Part B — Lab 10 Updating with Relationship

> **NOTE:** Use the project from the example of week 10.

## Lab 1 — Update `ShopController` (same behavior as `ProductController`)

**Requirement:** Update `ShopController` to have the same behavior as `ProductController` (add/remove products in a shop).
**Don't forget to handle the N+1 query problem.**

### Route `shops.view-products`
- URL: `localhost:8000/shops/SH001/products`
- Title: **Shop: SH001: Products**
- Search form: Term, Min Price, Max Price, [Filter], [Clear]
- Buttons: **< Back**, **Add Products**
- Heading: "List of Products for Pachara Shop"
- Table columns: **Code | Name | Category | Price | No. of Shops | [Remove]**

  | Code | Name | Category | Price | No. of Shops | |
  |---|---|---|---|---|---|
  | PD001 | Programming PHP | PHP | 345.00 | 2 | Remove |
  | PD003 | Learning PHP, MySQL & JavaScript | JavaScript | 450.00 | 3 | Remove |

### Route `shops.add-products-form`
- URL: `localhost:8000/shops/SH001/products/add`
- Title: **Shop: SH001: Add Products**
- Search form: Term, Min Price, Max Price, [Filter], [Clear]
- Button: **< Back**, pagination (`< 1 2 >`)
- Heading: "List of Products for Adding to Pachara Shop"
- Table columns: **Code | Name | Category | Price | No. of Shops | [Add]**

  | Code | Name | Category | Price | No. of Shops | |
  |---|---|---|---|---|---|
  | PD002 | JavaScript: The Definitive Guide | JavaScript | 250.00 | 3 | Add |
  | PD004 | Python Crash Course, 2nd Edition | Python | 560.00 | 1 | Add |
  | PD101 | Modern PHP Web Development | PHP | 758.55 | 3 | Add |
  | PD102 | Web Security for Developers | JavaScript | 1,369.18 | 0 | Add |
  | PD103 | How To Be A Web Developer In 90 Days | PHP | 892.72 | 0 | Add |

---

## Lab 2 — Update `CategoryController` (same behavior as `ProductController`)

**Requirement:** Update `CategoryController` to have the same behavior as `ProductController`.

- In route `categories.view-products`: **no removing product link** — we **cannot remove** a product from a category.
- But there **is adding product** to a category, which **changes the category of the product**.
- **Don't forget to handle the N+1 query problem.**

### Route `categories.view-products`
- URL: `localhost:8000/categories/CT001/products`
- Title: **Category: CT001: Products**
- Search form: Term, Min Price, Max Price, [Filter], [Clear]
- Buttons: **< Back**, **Add Products**
- Heading: "List of Products for PHP"
- Table columns: **Code | Name | Price | No. of Shops** (no Category column, no Remove button)

  | Code | Name | Price | No. of Shops |
  |---|---|---|---|
  | PD001 | Programming PHP | 345.00 | 3 |
  | PD101 | Modern PHP Web Development | 758.55 | 3 |
  | PD103 | How To Be A Web Developer In 90 Days | 892.72 | 0 |
  | PD104 | Designing Web APIs | 756.49 | 0 |

### Route `categories.add-products-form`
- URL: `localhost:8000/categories/CT001/products/add`
- Title: **Category: CT001: Add Products**
- Must show category **without link** (plain text in the Category column).
- Search form: Term, Min Price, Max Price, [Filter], [Clear]
- Button: **< Back**
- Heading: "List of Products for Adding to PHP"
- Table columns: **Code | Name | Category | Price | No. of Shops | [Add]**

  | Code | Name | Category | Price | No. of Shops | |
  |---|---|---|---|---|---|
  | PD002 | JavaScript: The Definitive Guide | JavaScript | 250.00 | 3 | Add |
  | PD003 | Learning PHP, MySQL & JavaScript | JavaScript | 450.00 | 3 | Add |
  | PD004 | Python Crash Course, 2nd Edition | Python | 560.00 | 1 | Add |
  | PD102 | Web Security for Developers | JavaScript | 1,369.18 | 0 | Add |
  | PD105 | Visual Studio Code: End-to-End Editing and Debugging Tools | JavaScript | 978.38 | 0 | Add |

---

## Lab 3 — Product create/update can specify category

**Requirement:** Update routes `products.create-form`, `products.create`, `products.update-form` and `products.update` so they can specify a category.

- Route `products.create-form` has a **drop-down list of categories with no default value**.
- **\*\* Don't use `category_id` directly. \*\*** (ใช้ `associate()` แทน)

### Route `products.create-form`
- URL: `localhost:8000/products/create`
- Title: **Product: Create**
- Fields: Code \*, Name \*, **Category \***, Price \*, Description \*, [Create]
- Category drop-down options:
  - `--- Please Select Category ---` (default, no value)
  - `[CT001] PHP`
  - `[CT002] Python`
  - `[CT003] JavaScript`

### Route `products.update-form`
- URL: `localhost:8000/products/PD004/update`
- Title: **Product: PD004**
- Be careful when updating: the drop-down list **has to select the correct category by default** (e.g., PD004 → `[CT002] Python`).
- Fields: Code (PD004), Name (Python Crash Course, 2nd Edition), Category (`[CT002] Python` selected), Price (560.00), Description, [Update]

---

## Lab 4 — Searchable by category name

**Requirement:** Update routes `products.list`, `shops.view-products`, `shops.add-products-form` and `categories.add-products-form` to be **searchable by category name**.

**\*\* Only `categories.view-products` cannot be searched by category name. \*\***

### Route `products.list` — search `term=php`
- URL: `localhost:8000/products?term=php&minPrice=&maxPrice=`
- Title: **Product: List**, button **Create Product**
- Table: Code | Name | Category (link) | Price | No. of Shops

  | Code | Name | Category | Price | No. of Shops |
  |---|---|---|---|---|
  | PD001 | Programming PHP | PHP | 345.00 | 3 |
  | PD003 | Learning PHP, MySQL & JavaScript | JavaScript | 450.00 | 3 |
  | PD101 | Modern PHP Web Development | PHP | 758.55 | 3 |
  | PD103 | How To Be A Web Developer In 90 Days | **PHP** ← matched by category | 892.72 | 0 |
  | PD104 | Designing Web APIs | **PHP** ← matched by category | 756.49 | 0 |

### Route `shops.view-products` — search `term=php`
- URL: `localhost:8000/shops/SH001/products?term=php&minPrice=...`

  | Code | Name | Category | Price | No. of Shops | |
  |---|---|---|---|---|---|
  | PD001 | Programming PHP | PHP | 345.00 | 3 | Remove |
  | PD003 | Learning PHP, MySQL & JavaScript | JavaScript | 450.00 | 3 | Remove |
  | PD103 | How To Be A Web Developer In 90 Days | **PHP** ← matched by category | 892.72 | 1 | Remove |

### Route `shops.add-products-form` — search `term=php`
- URL: `localhost:8000/shops/SH001/products/add?term=php&minPr...`

  | Code | Name | Category | Price | No. of Shops | |
  |---|---|---|---|---|---|
  | PD101 | Modern PHP Web Development | PHP | 758.55 | 3 | Add |
  | PD104 | Designing Web APIs | **PHP** ← matched by category | 756.49 | 0 | Add |

### Route `categories.view-products` — search `term=php` (NOT searched by category name)
- URL: `localhost:8000/categories/CT001/products?term=php&minPri...`
- Only products whose code/name match "php" appear (category name is not searched):

  | Code | Name | Price | No. of Shops |
  |---|---|---|---|
  | PD001 | Programming PHP | 345.00 | 3 |
  | PD101 | Modern PHP Web Development | 758.55 | 3 |

### Route `categories.add-products-form` — search `term=script`
- URL: `localhost:8000/categories/CT001/products/add?term=script&...`

  | Code | Name | Category | Price | No. of Shops | |
  |---|---|---|---|---|---|
  | PD002 | JavaScript: The Definitive Guide | JavaScript | 250.00 | 3 | Add |
  | PD003 | Learning PHP, MySQL & JavaScript | JavaScript | 450.00 | 3 | Add |
  | PD102 | Web Security for Developers | **JavaScript** ← matched by category | 1,369.18 | 0 | Add |
  | PD105 | Visual Studio Code: End-to-End Editing and Debugging Tools | **JavaScript** ← matched by category | 978.38 | 0 | Add |

---

# Appendix — Quick Reference

## Eloquent relationship methods

| Purpose | Method | Relationship | Note |
|---|---|---|---|
| Filter by related model (exists) | `whereHas()` / `orWhereHas()` | any | `where exists (...)` |
| Filter by related model (not exists) | `whereDoesntHave()` / `orWhereDoesntHave()` | any | `where not exists (...)` |
| Create child via parent | `save()` / `saveMany()` | HasMany | sets FK automatically |
| Mass-assign create | `create()` / `createMany()` | HasMany | needs `#[Fillable(...)]` |
| Save model + relations | `push()` | any | recursive save |
| Set parent | `associate($model or $id)` | BelongsTo | then `save()` — don't set `*_id` directly |
| Remove parent | `dissociate()` | BelongsTo | FK must be nullable, then `save()` |
| Add to pivot | `attach($model / $id / [...])` | BelongsToMany | DB only |
| Remove from pivot | `detach($model / $id / [...])` | BelongsToMany | DB only |
| Replace pivot set | `sync([...])` | BelongsToMany | removes items not in array |
| Reload from DB | `refresh()` | — | after attach/detach/sync |
| Count related (avoid N+1) | `withCount('relation')` | any | gives `relation_count` |
| Eager load (avoid N+1) | `with('relation')` | any | loads relations in 1 extra query |

## Blade directives

| Directive | Output when true |
|---|---|
| `@selected(expr)` | `selected` |
| `@checked(expr)` | `checked` |
| `@csrf` | hidden CSRF token input |

## Patterns to remember

- Form outside table + buttons with `form="form-id" name="x" value="..."` to submit one row.
- `redirect()->back()` keeps the search query string after add/remove.
- Use `\resolve(OtherController::class)` to reuse another controller's `prepareCriteria()`, `getQuery()`, and `MAX_ITEMS`.
- Filter-out pattern (`whereDoesntHave`) both for listing the "add" form **and** for validating the submitted item (prevent duplicates).


---

<!-- ============================================================ -->

# Week 11 — Session (Lecture + Lab)

> รวมเนื้อหาจาก Lecture "Session" และ "Practice 954348 01/2569 Lab 11 Session"
> ใช้เป็น reference สำหรับทำ Project (Laravel)

---

## สารบัญ

- [Part 1: Lecture — Session](#part-1-lecture--session)
  - [1. Introduction to session](#1-introduction-to-session)
  - [2. Laravel session](#2-laravel-session)
  - [3. Example — Status message](#3-example--status-message)
  - [4. Example — Back link (Bookmarks)](#4-example--back-link-bookmarks)
  - [References](#references)
- [Part 2: Lab 11 — Session](#part-2-lab-11--session)
  - [Lab 1: ProductController](#lab-1-productcontroller)
  - [Lab 2: ShopController](#lab-2-shopcontroller)
  - [Lab 3: CategoryController](#lab-3-categorycontroller)
- [Appendix: สรุป Pattern ที่ใช้ซ้ำ](#appendix-สรุป-pattern-ที่ใช้ซ้ำ)

---

# Part 1: Lecture — Session

## 1. Introduction to session

### HTTP request/response round trip

*(รูป: Adam `192.168.10.21` และ Bob `192.168.10.22` ต่อผ่าน Router ที่ทำ NAT เป็น IP `182.232.115.62` ไปยัง Web server `202.21.10.15`)*

- ในการตั้งค่าเครือข่ายส่วนใหญ่ client จะเชื่อมต่อกับ router ที่แปลง private IP เป็น public IP ผ่าน **NAT**
  - ใช้ทั้งเรื่อง security และการจัดการ IP address
- จากรูป web server **ไม่สามารถ** แยกได้ว่า client คนไหนเป็นผู้ส่ง request
  - request จาก Adam และ Bob ดูเหมือนกัน
  - IP address **ช่วยไม่ได้** ในกรณีนี้

### Cookie

- HTTP protocol มีข้อมูลสำหรับติดตาม client เรียกว่า **cookie**
- เมื่อ client ส่ง request ไปยัง web server, server จะใส่ cookie ใน response header เช่น

  ```
  Cookie: user=Adam
  ```

- ใน request ครั้งถัดไป client จะ **ส่ง cookie กลับ** ไปยัง server ทุกครั้งผ่าน request header
  - web browser ทำให้ **อัตโนมัติ**
- ตอนนี้ web server รู้แล้วว่า client คนไหนส่ง request

```
Adam ── 1. Request ──────────────────────────▶ Server
Adam ◀─ 2. Response with cookie user=Adam ──── Server
Adam ── 3. Request with cookie user=Adam ────▶ Server
```

### Session

- แต่ cookie ถูกเก็บที่ **client-side** หมายความว่า client แก้ค่า cookie ได้
  - เช่น Adam เปลี่ยน cookie จาก `user=Adam` เป็น `user=Bob`
  - ดังนั้น cookie **ไม่ใช่** ข้อมูลที่ server เชื่อถือได้
- เพื่อแก้ปัญหา security, web server จะให้ **randomized session ID** แก่ client แทนข้อมูลจริง
- ข้อมูลสำคัญจะถูกเก็บที่ **server-side** และอ้างอิงผ่าน session ID

```
Adam ── 1. Request ───────────────────────────────▶ Server ◀──look for session──▶ Session storage
Adam ◀─ 2. Response with cookie SESSION_ID=BX12C ─── Server                        ┌──────────────────────┐
Adam ── 3. Request with cookie SESSION_ID=BX12C ──▶ Server                        │ BX12C → user=Adam    │
                                                                                  │ H32EP → user=Bob     │
                                                                                  │ ...   → user=...     │
                                                                                  └──────────────────────┘
```

---

## 2. Laravel session

### Session in Laravel

- Laravel จัดการ session ให้อัตโนมัติ
- ทุก route ใน `routes/web.php` ใช้ session โดย default
  - เรียกว่า **stateful routes**
  - ส่วน route ใน `routes/api.php` เป็น **stateless** (ไม่มี session)
- ตั้งค่าประเภท session storage ได้:
  - `file` — เก็บใน `storage/framework/sessions`
  - `database`
  - in memory — เช่น ใช้ Redis
  - ฯลฯ
- **Laravel 11+ ใช้ `database` เป็น default**

### Global function `session()`

- ใช้ function `session()` เพื่อเข้าถึงข้อมูลใน session
- ข้อมูลใน session เก็บแบบ associative array
- การใช้งาน:
  - `session($key)` — get ค่าจาก `$key`
  - `session($associative_array)` — set ค่าด้วย associative array
  - `session()` — get session object
- ในบทนี้ใช้ **session object** ซึ่งมี method ที่มีประโยชน์มากกว่า เช่น

```php
$session = session();
$user = $session->get('user');
```

### Retrieve data

```php
// simply get data
$user = session()->get('user');

// if session does not contain the given key
// we can specify the default value by using second arguments.
$user = session()->get('user', 'annonymous');

// or use closure function
$user = session()->get('user', function() {
    return 'annonymous';
});
```

Get all data:

```php
$session_data = session()->all(); // return associative array
```

### Storing data

```php
// By using key.
session()->put('user', 'Adam');

// By using associative array.
session()->put([
    'user' => 'Adam',
    'type' => 'guest',
]);
```

### Deleting data

`forget()` ลบข้อมูลบางส่วนออกจาก session:

```php
// Forget a single key...
session()->forget('user');

// Forget multiple keys...
session()->forget(['user', 'type']);
```

`flush()` ลบข้อมูลทั้งหมด:

```php
session()->flush();
// now all session data was removed.
```

`pull()` ดึงค่าแล้วลบในคำสั่งเดียว:

```php
$user = session()->pull('user');
// now key 'user' was removed from session.
```

### Determining if an item exists in the session

`has()` — คืน `true` ถ้ามี key และค่า **ไม่เป็น null**

```php
if (session()->has('user')) {
    // do some tasks.
}
```

`exists()` — คืน `true` ถ้ามี key **ไม่ว่าค่าจะเป็นอะไร** (รวม null)

```php
if (session()->exists('user')) {
    // do some tasks.
}
```

### Flash data

- บางครั้งต้องการเก็บข้อมูลไว้ใช้แค่ **request ถัดไป**
- ใช้ method `flash()` — คล้าย `put()` แต่ข้อมูลจะใช้ได้ทันทีและใน HTTP request ถัดไป
- หลังจาก request ถัดไป ข้อมูล flash จะถูกลบ

```php
session()->flash('status', 'The data has been deleted.');
```

ใน request ถัดไป:

```php
$message = session()->get('status');
// In the end of this request the key 'status' is automatically removed.
```

### Redirecting with flashed session data

ใช้ method `with()` บน redirect object:

```php
return redirect()->route('products.list')
    ->with('status', 'Product is created.');
```

ใช้กับ `redirect()->back()` ได้ด้วย:

```php
return redirect()->back()->with('status', 'Product is deleted.');
```

### Regenerating the session ID

- มีปัญหา security เช่น **session fixation** ที่บังคับให้ต้อง regenerate session id
- **ต้อง regenerate session id ทุกครั้งตอน login**

`regenerate()` — สร้าง session id ใหม่ แต่ **เก็บข้อมูลเดิมไว้**

```php
session()->regenerate();
// It responds with the new session id via cookie.
```

`invalidate()` — สร้าง session id ใหม่ และ **ลบข้อมูลทั้งหมด**

```php
session()->invalidate();
// now all session data are removed and responds with new session id.
```

### Dot notation on key

Laravel รองรับ dot notation สำหรับเข้าถึง nested associative array:

```php
session()->put('data', [
    'a' => 1,
    'b' => 2,
]);

// get nested associative array
$b = session()->get('data.b'); // $b is 2

// set nested associative array
session()->put('data.a', 5); // now the key 'a' in 'data' will be 5
```

> ⚠️ ดังนั้น **ห้ามตั้งชื่อ key ใน session ที่มีจุด (.)** เพราะจะถูกตีความเป็น nested

### `@session` directive

ตั้งแต่ Laravel 10 มี `@session` directive = `@if` + `session()->has(...)`

```blade
{{-- Old --}}
@if(session()->has('message'))
    <span>{{ session()->get('message') }}</span>
@endif

{{-- New --}}
@session('message')
    <span>{{ $value }}</span>
@endsession
```

- `$value` เป็นตัวแปรภายในของ `@session`

---

## 3. Example — Status message

- ใช้ project จากสัปดาห์ก่อน
- ต้องการแสดงข้อความหลังจากสร้าง product ใหม่
  - หลัง route `products.create` → route ที่ redirect ไปแสดง `"Product xxxxx was created."`
- และสำหรับการลบ
  - หลัง route `products.delete` → route ที่ redirect ไปแสดง `"Product xxxxx was deleted."`

### ภาพตัวอย่าง (จาก slide)

| Action | ก่อน | หลัง |
|---|---|---|
| **Create** | `/products/create` กรอก PD901 แล้วกด Create | `/products/PD901` แสดงกล่อง ⓘ **Product PD901 was created.** |
| **Update** | `/products/PD901/update` แก้ code เป็น PD901x แล้วกด Update | `/products/PD901x` แสดงกล่อง ⓘ **Product PD901x was updated.** |
| **Delete** | `/products/PD901x` กด Delete | `/products` (Product: List) แสดงกล่อง ⓘ **Product PD901x was deleted.** |

### Step 1 — Update `layouts/main.blade.php`

เพิ่มโค้ดนี้ **ก่อน** `@yield('header')`:

```blade
<div class="app-cmp-notifications">
    @session('status')
        <div role="status">{{ $value }}</div>
    @endsession
</div>

@yield('header')
```

- ตกแต่งด้วย CSS

### Step 2 — `ProductController::create()`

เปลี่ยนจาก:

```php
return redirect()->route('products.view', [
    'product' => $product->code,
]);
```

เป็น:

```php
return redirect()
    ->route('products.view', [
        'product' => $product->code,
    ])
    ->with('status', "Product {$product->code} was created.");
```

### Step 3 — `ProductController::update()`

เปลี่ยนจาก:

```php
return redirect()->route('products.view', [
    'product' => $product->code,
]);
```

เป็น:

```php
return redirect()
    ->route('products.view', [
        'product' => $product->code,
    ])
    ->with('status', "Product {$product->code} was updated.");
```

### Step 4 — `ProductController::delete()`

เปลี่ยนจาก:

```php
return redirect()->route('products.index');
```

เป็น:

```php
return redirect()
    ->route('products.index')
    ->with('status', "Product {$product->code} was deleted.");
```

---

## 4. Example — Back link (Bookmarks)

### แนวคิด: back link ใน `products/view.blade.php`

- route `products.view` ถูก link มาได้จากหลาย route เช่น `products.list` หรือ `shops.view-products`
- `products.view` เองไม่สามารถรู้ได้ว่า URL ก่อนหน้าคืออะไร
- ดังนั้น **ทุก route ที่ link ไปยัง `products.view` ต้องบันทึก URL ของตัวเองลง session** เพื่อเป็น bookmark ของ back link
- เก็บ bookmark ไว้ที่ key `bookmarks.products.view` เพื่อแยกจาก back link อื่น เช่น `bookmarks.shops.view` ในกรณีที่ user เปิดหลายหน้าต่างใน site เดียวกัน

### ภาพตัวอย่าง (จาก slide)

- **Back link ไปยัง `products.list`:** อยู่ที่ `/products?page=2&term=p` → คลิก PD102 → `/products/PD102` → กด `< Back` → กลับไปที่ `/products?page=2&term=p` (**รักษา query string ไว้**)
- **Back link ไปยัง `shops.view-products`:** อยู่ที่ `/shops/SH001/products` → คลิก PD001 → `/products/PD001` → กด `< Back` → กลับไปที่ `/shops/SH001/products`

### ⚠️ Notable: session for navigation state

- การใช้ session จัดการ navigation state **ไม่ใช่วิธีที่แนะนำ**
  - ไม่สามารถจัดการบางกรณีได้ถูกต้อง เช่น **looped navigation**
- วิธีที่แนะนำคือจัดการ navigation state ที่ **client-side** (ทำได้ด้วย JavaScript)
- แต่ในวิชานี้ใช้เฉพาะ server-side เพื่อแสดงการใช้ session

### Step 5 — เก็บ back link URL สำหรับ route `products.list`

ใน `products/list.blade.php`:

```blade
{{-- Add the following code before @foreach($products as $product) --}}
@php
    session()->put('bookmarks.products.view', url()->full());
@endphp
```

- `url()->full()` คืน full URL **รวม query string** ของ route ปัจจุบัน
- `@php` directive ให้รันโค้ด PHP ใน blade template ได้
- กำหนด bookmark ใน template เพราะ template คือที่ดูแล logic ของ link

### Step 6 — เก็บ back link URL สำหรับ route อื่น

เพิ่มโค้ดเดียวกันใน template:

- `shops/view-products.blade.php`
- `shops/add-products-form.blade.php`
- `categories/view-products.blade.php`
- `categories/add-products-form.blade.php`

```blade
{{-- Add the following code before @foreach($products as $product) --}}
@php
    session()->put('bookmarks.products.view', url()->full());
@endphp
```

### Step 7 — เพิ่ม back link ใน `products/view.blade.php`

- ถ้าไม่มี bookmark ให้ back link ไปที่ `products.index`
- ใช้ **nullish coalescing operator (`??`)** จัดการค่า null

```blade
<nav>
    <ul>
        <li>
            <a href="{{
                session()->get('bookmarks.products.view') ??
                route('products.index')
            }}">&lt; Back</a>
        </li>
        {{-- ... other links --}}
    </ul>
</nav>
```

### Step 8 — Forward back link ไปยัง route ที่ redirect

สำหรับ route ที่มีการ redirect เช่น `products.delete` ต้อง forward back link ไปด้วย

ใน `products/view.blade.php` เพิ่ม:

```blade
@php
    session()->put(
        'bookmarks.products.delete',
        session()->get('bookmarks.products.view'),
    );
@endphp
```

### Step 9 — Update `ProductController::delete()`

ต้อง update การ redirect ของ `products.delete` ให้ไปยังตำแหน่งที่ถูกต้อง

เปลี่ยนจาก:

```php
return redirect()
    ->route('products.index')
    ->with('status', "Product {$product->code} was deleted.");
```

เป็น:

```php
return redirect(
    session()->get('bookmarks.products.delete') ?? route('products.index')
)->with('status', "Product {$product->code} was deleted.");
```

### Back link สำหรับ `products.create-form` (ปุ่ม Cancel)

- เพิ่ม bookmark ให้ `products.create-form` เพื่อกลับไปหน้าก่อนหน้าเมื่อกด **Cancel**
- *(ภาพ: `/products?page=2&term=p` → กด Create Product → `/products/create` → กด Cancel → กลับไป `/products?page=2&term=p`)*

### Forward back link ไปยัง `products.view` หลัง redirect จาก `products.create`

- *(ภาพ: `/products/create` กด Create → `/products/PD901` (แสดง "Product PD901 was created.") → กด `< Back` → กลับไป `/products?page=2&term=p`)*

### Step 10 — Bookmark สำหรับ `products.create-form` และ `products.create`

ใน `products/list.blade.php` **ก่อน** link ไปยัง `products.create-form`:

```blade
<!-- Add the following code before link to products.create-form -->
@php
    session()->put('bookmarks.products.create-form', url()->full());
    session()->put(
        'bookmarks.products.create',
        session()->get('bookmarks.products.create-form'),
    );
@endphp

{{-- link to products.create-form --}}
```

### Step 11 — เพิ่ม back link ให้ปุ่ม Cancel

ใน `products/create-form.blade.php` (ถัดจากปุ่ม create):

```blade
{{-- create button --}}
<a href="{{
    session()->get('bookmarks.products.create-form') ??
    route('products.index')
}}">Cancel</a>
```

> หมายเหตุ: slide ต้นฉบับเขียนว่า "In products/list.blade.php" แต่ปุ่ม Cancel อยู่ในหน้า create form

### Step 12 — Update `ProductController::create()`

- forward back link ของ `products.create` ไปยัง `products.view`
- ลบ forwarding back link หลังใช้แล้ว
- เพิ่มโค้ดก่อน return statement:

```php
if(session()->has('bookmarks.products.create')) {
    session()->put(
        'bookmarks.products.view',
        session()->get('bookmarks.products.create'),
    );
}

session()->forget('bookmarks.products.create');

// return statement
```

### Step 13 — Back link สำหรับ `products.update-form` และ `products.update`

- หลังจาก update product แล้ว redirect ไป `products.view` ซึ่งเป็น route เดียวกับ back link ของ `products.update-form`
- เราไม่ต้องการเปลี่ยน back link ของ `products.view` จึงใช้ location ต่างกันระหว่าง `products.update-form` และ `products.update`
- เพิ่มโค้ดนี้ **หลัง** bookmark ของ `products.delete` (ใน `products/view.blade.php`):

```php
// products.delete bookmark
session()->put('bookmarks.products.update-form', url()->full());
session()->put(
    'bookmarks.products.update',
    session()->get('bookmarks.products.view'),
);
```

### Step 14 — เพิ่ม back link ใน `products/update-form.blade.php`

ถัดจากปุ่ม update:

```blade
{{-- update button --}}
<a href="{{
    session()->get('bookmarks.products.update-form') ??
    route('products.view', [
        'product' => $product->code,
    ])
}}">Cancel</a>
```

### Step 15 — Update `ProductController::update()`

forward back link ของ `products.update` ไปยัง `products.view` — เพิ่มก่อน return statement:

```php
if(session()->has('bookmarks.products.update')) {
    session()->put(
        'bookmarks.products.view',
        session()->get('bookmarks.products.update'),
    );
}

session()->forget('bookmarks.products.update');

// return statement
```

## References

- https://laravel.com/framework/docs/13.x/session

---

# Part 2: Lab 11 — Session

> **NOTE:** ใช้ project จาก example ของ week 11
>
> **HINT:** เพื่อให้ debug ง่าย เพิ่มโค้ดนี้ใน `layouts/main.blade.php` ก่อน `<footer>`:
>
> ```blade
> @dump(session()->get('bookmarks'))
> ```

## Lab 1: ProductController

Update `ProductController` และ templates ด้วย status messages ต่อไปนี้

### Status messages

| Route | Message |
|---|---|
| `products.add-shop` | `Shop yyyyy was added to Product xxxxx.` |
| `products.remove-shop` | `Shop yyyyy was removed from Product xxxxx.` |

### Back links

**Update back link ของ route `products.view-shops`:**
- เก็บใน session key `bookmarks.products.view-shops`, default คือ route `products.view`
- route `products.view` ต้อง bookmark URL ของตัวเอง

**Update back link ของ route `products.add-shops-form`:**
- เก็บใน session key `bookmarks.products.add-shops-form`, default คือ route `products.view-shops`
- route `products.view-shops` ต้อง bookmark URL ของตัวเอง

---

## Lab 2: ShopController

Update `ShopController` และ templates ด้วย status messages ต่อไปนี้

### Status messages

| Route | Message |
|---|---|
| `shops.create` | `Shop yyyyy was created.` |
| `shops.update` | `Shop yyyyy was updated.` |
| `shops.delete` | `Shop yyyyy was deleted.` |
| `shops.add-product` | `Product xxxxx was added to Shop yyyyy.` |
| `shops.remove-product` | `Product xxxxx was removed from Shop yyyyy.` |

### Back links

**เพิ่ม back link ของ route `shops.create-form` (ปุ่ม Cancel):**
- เก็บใน session key `bookmarks.shops.create-form`, default คือ route `shops.index`
- route `shops.list` ต้อง bookmark URL ของตัวเอง

**Forward back link ของ route `shops.create` ไปยัง `shops.view`:**
- เก็บใน session key `bookmarks.shops.create` และ forward ไปยัง session key `bookmarks.shops.view` ใน `ShopController::create()`
- route `shops.list` ต้อง bookmark location เดียวกับ `bookmarks.shops.create-form`

**เพิ่ม back link ของ route `shops.update-form` (ปุ่ม Cancel):**
- เก็บใน session key `bookmarks.shops.update-form`, default คือ route `shops.view`
- route `shops.view` ต้อง bookmark URL ของตัวเอง

**Forward back link ของ route `shops.update` ไปยัง `shops.view`:**
- เก็บใน session key `bookmarks.shops.update` และ forward ไปยัง session key `bookmarks.shops.view` ใน `ShopController::update()`
- route `shops.view` ต้อง bookmark location เดียวกับ back link ของตัวเอง

**เพิ่ม back link ของ route `shops.view`:**
- เก็บใน session key `bookmarks.shops.view`, default คือ route `shops.index`
- route ต่อไปนี้ต้อง bookmark URL ของตัวเอง:
  - `shops.list`
  - `products.view-shops`
  - `products.add-shops-form`

**Redirect ไปยัง back link ใน route `shops.delete`:**
- เก็บใน session key `bookmarks.shops.delete`
- ใน `ShopController::delete()` update การ redirect ไปยัง bookmark นี้, default คือ route `shops.view`
- route `shops.view` ต้อง bookmark location เดียวกับ back link ของตัวเอง

> หมายเหตุ: เอกสาร lab เขียน default เป็น `shops.view` แต่หลังลบ shop แล้ว shop นั้นไม่มีอยู่ — ถ้า bookmark ว่าง default ที่ใช้งานได้จริงน่าจะเป็น `shops.index` (ควรถามผู้สอนเพื่อยืนยัน)

**Update back link ของ route `shops.view-products`:**
- เก็บใน session key `bookmarks.shops.view-products`, default คือ route `shops.view`
- route `shops.view` ต้อง bookmark URL ของตัวเอง

**Update back link ของ route `shops.add-products-form`:**
- เก็บใน session key `bookmarks.shops.add-products-form`, default คือ route `shops.view-products`
- route `shops.view-products` ต้อง bookmark URL ของตัวเอง

---

## Lab 3: CategoryController

Update `CategoryController` และ templates ด้วย status messages ต่อไปนี้

### Status messages

| Route | Message |
|---|---|
| `categories.create` | `Category ccccc was created.` |
| `categories.update` | `Category ccccc was updated.` |
| `categories.delete` | `Category ccccc was deleted.` |
| `categories.add-product` | `Product xxxxx was added to Category ccccc.` |

> (เอกสารต้นฉบับพิมพ์ `cagegories.update` และ `Category cccccc` — น่าจะเป็น typo ของ `categories.update` / `ccccc`)

### Back links

**เพิ่ม back link ของ route `categories.create-form` (ปุ่ม Cancel):**
- เก็บใน session key `bookmarks.categories.create-form`, default คือ route `categories.index`
- route `categories.list` ต้อง bookmark URL ของตัวเอง

**Forward back link ของ route `categories.create` ไปยัง `categories.view`:**
- เก็บใน session key `bookmarks.categories.create` และ forward ไปยัง session key `bookmarks.categories.view` ใน `CategoryController::create()`
  > (ต้นฉบับเขียน `categories.shops.view` — น่าจะเป็น typo ของ `bookmarks.categories.view`)
- route `categories.list` ต้อง bookmark location เดียวกับ `bookmarks.categories.create-form`

**เพิ่ม back link ของ route `categories.update-form` (ปุ่ม Cancel):**
- เก็บใน session key `bookmarks.categories.update-form`, default คือ route `categories.view`
- route `categories.view` ต้อง bookmark URL ของตัวเอง

**Forward back link ของ route `categories.update` ไปยัง `categories.view`:**
- เก็บใน session key `bookmarks.categories.update` และ forward ไปยัง session key `bookmarks.categories.view` ใน `CategoryController::update()`
- route `categories.view` ต้อง bookmark location เดียวกับ back link ของตัวเอง

**เพิ่ม back link ของ route `categories.view`:**
- เก็บใน session key `bookmarks.categories.view`, default คือ route `categories.index`
- route ต่อไปนี้ต้อง bookmark URL ของตัวเอง:
  - `categories.list`
  - `products.list`

**Redirect ไปยัง back link ใน route `categories.delete`:**
- เก็บใน session key `bookmarks.categories.delete`
- ใน `CategoryController::delete()` update การ redirect ไปยัง bookmark นี้, default คือ route `categories.view`
- route `categories.view` ต้อง bookmark location เดียวกับ back link ของตัวเอง

**Update back link ของ route `categories.view-products`:**
- เก็บใน session key `bookmarks.categories.view-products`, default คือ route `categories.view`
- route `categories.view` ต้อง bookmark URL ของตัวเอง

**Update back link ของ route `categories.add-products-form`:**
- เก็บใน session key `bookmarks.categories.add-products-form`, default คือ route `categories.view-products`
- route `categories.view-products` ต้อง bookmark URL ของตัวเอง

---

# Appendix: สรุป Pattern ที่ใช้ซ้ำ

> ส่วนนี้เป็น cheat sheet ที่สรุปจาก lecture เพื่อใช้ทำ lab/project ได้เร็วขึ้น (`{resource}` = `products` / `shops` / `categories`, `{target}` = route ปลายทาง)

### A. Status message (Controller)

```php
return redirect()
    ->route('{resource}.view', ['{param}' => $model->code])
    ->with('status', "Shop {$shop->code} was created.");
```

### B. Bookmark URL ปัจจุบัน (ใน template ของหน้าที่ "link ออกไป")

```blade
@php
    session()->put('bookmarks.{target}', url()->full());
@endphp
```

### C. Back link / Cancel (ใน template ของหน้าปลายทาง)

```blade
<a href="{{
    session()->get('bookmarks.{target}') ?? route('{default.route}', [...])
}}">&lt; Back</a>
```

### D. Forward bookmark สำหรับ action ที่ redirect (create / update / delete)

ใน template (ก่อน link/form):

```blade
@php
    session()->put('bookmarks.{resource}.create-form', url()->full());
    session()->put('bookmarks.{resource}.create', session()->get('bookmarks.{resource}.create-form'));
@endphp
```

ใน controller (ก่อน return):

```php
if (session()->has('bookmarks.{resource}.create')) {
    session()->put(
        'bookmarks.{resource}.view',
        session()->get('bookmarks.{resource}.create'),
    );
}
session()->forget('bookmarks.{resource}.create');
```

### E. Redirect ไป bookmark (delete)

```php
return redirect(
    session()->get('bookmarks.{resource}.delete') ?? route('{resource}.index')
)->with('status', "... was deleted.");
```

### F. ตาราง Bookmark key ทั้งหมด

| Session key | ใครเป็นคน set (bookmark) | Default |
|---|---|---|
| `bookmarks.products.view` | products.list, shops.view-products, shops.add-products-form, categories.view-products, categories.add-products-form, products.create (forward), products.update (forward) | `products.index` |
| `bookmarks.products.delete` | products.view (= back link ของ products.view) | `products.index` |
| `bookmarks.products.create-form` | products.list | `products.index` |
| `bookmarks.products.create` | products.list (= create-form) | — |
| `bookmarks.products.update-form` | products.view (URL ตัวเอง) | `products.view` |
| `bookmarks.products.update` | products.view (= back link ของ products.view) | — |
| `bookmarks.products.view-shops` | products.view | `products.view` |
| `bookmarks.products.add-shops-form` | products.view-shops | `products.view-shops` |
| `bookmarks.shops.view` | shops.list, products.view-shops, products.add-shops-form, shops.create (forward), shops.update (forward) | `shops.index` |
| `bookmarks.shops.create-form` | shops.list | `shops.index` |
| `bookmarks.shops.create` | shops.list (= create-form) | — |
| `bookmarks.shops.update-form` | shops.view (URL ตัวเอง) | `shops.view` |
| `bookmarks.shops.update` | shops.view (= back link ของ shops.view) | — |
| `bookmarks.shops.delete` | shops.view (= back link ของ shops.view) | `shops.view` (ตามเอกสาร) |
| `bookmarks.shops.view-products` | shops.view | `shops.view` |
| `bookmarks.shops.add-products-form` | shops.view-products | `shops.view-products` |
| `bookmarks.categories.view` | categories.list, products.list, categories.create (forward), categories.update (forward) | `categories.index` |
| `bookmarks.categories.create-form` | categories.list | `categories.index` |
| `bookmarks.categories.create` | categories.list (= create-form) | — |
| `bookmarks.categories.update-form` | categories.view (URL ตัวเอง) | `categories.view` |
| `bookmarks.categories.update` | categories.view (= back link ของ categories.view) | — |
| `bookmarks.categories.delete` | categories.view (= back link ของ categories.view) | `categories.view` (ตามเอกสาร) |
| `bookmarks.categories.view-products` | categories.view | `categories.view` |
| `bookmarks.categories.add-products-form` | categories.view-products | `categories.view-products` |


---

<!-- ============================================================ -->

# Week 12 — Authentication & Authorization (Lecture + Lab + Migrations)

> รวมเนื้อหาจาก 3 ไฟล์: `lec12-authentication_and_authorization.pdf`, `lab12-authentication_and_authorization.pdf`, `lec12-migrations.zip`
> Course: Practice 954348 — 01/2569 (Laravel 13)

## สารบัญ

- [Part 1 — Lecture](#part-1--lecture)
  - [1. Authentication vs. Authorization](#1-authentication-vs-authorization)
  - [2. Laravel Authentication](#2-laravel-authentication)
  - [3. Laravel Authorization](#3-laravel-authorization)
  - [4. HTTP Cache Control](#4-http-cache-control)
  - [5. Example (step-by-step)](#5-example-step-by-step)
  - [6. References](#6-references)
- [Part 2 — Migrations (lec12-migrations.zip)](#part-2--migrations-lec12-migrationszip)
- [Part 3 — Lab 12](#part-3--lab-12)
- [Appendix — Quick Cheat Sheet](#appendix--quick-cheat-sheet)

---

# Part 1 — Lecture

**Contents**
- Authentication vs. Authorization
- Laravel Authentication
- Laravel Authorization
- HTTP cache control
- Example
- References

---

## 1. Authentication vs. Authorization

### What is Authentication?

- Authentication is a process to **verify that users are who they claim to be**.
- There are many methods to verify authentication:
  - **Passwords** — something they know.
  - **Smart cards** — something they have.
  - **Biometrics** — something they are.
- In this chapter, we will learn about the simplest one that is **password**.

### What is Authorization?

- Authorization is a process to **verify that current users have the right permission** to the requested tasks.
- It **requires authentication process before** this process.
- For web applications, they require authentication for one time then use sharable resources, e.g., **session**, to provide authenticated user to authorization process.

---

## 2. Laravel Authentication

### Authentication Process (flowchart)

```
START
  │
  ▼
[Login] ──(username, password)──▶ <is correct username and password?>
  ▲                                     │True                │False
  │                                     ▼                    │
  │                             [store user in session]      │
  │                                     │                    │
  │                                     ▼                    │
  │                             [redirect to given page]     │
  │                                     │                    │
  │                                     ▼                    │
  │                                   STOP                   │
  └──────────────────────────────────────────────────────────┘
```

- Laravel provides authentication process through class `Illuminate\Support\Facades\Auth`.
- By default, `Auth` will use the existing `User` model to manage users data.
- If users **pass authentication process**, then the user data will **be stored in session**.
- By default, Laravel uses **e-mail** as **username**.

### User model

- When we create Laravel project, the `User` model is already in our project.
- User model properties:
  - Same as other models, `id`, `created_at` and `updated_at` are required by default.
  - `name` — name of user.
  - `email` — user's e-mail, **unique**.
  - `email_verified_at` — the time that e-mail was verified.
    - If we don't have verify process, just set this value to `CURRENT_TIMESTAMP()`.
  - `password` — encrypted password with self-defined encryption method.
    - By using helper class `Illuminate\Support\Facades\Hash`.
      - https://www.php.net/manual/en/function.password-hash.php
      - https://www.php.net/manual/en/function.crypt.php
  - `remember_token` — Remember Me token.
    - If we don't have Remember Me process, just set this value to empty string.

### `users` schema

```sql
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### Update User model to support role

- By default, `User` model does not apply the role concept, so we have to update it.
  - We **don't allow `role` field setting in mass assignment** so don't add `role` to `Fillable` attribute.
- Add method `isAdministrator()` for conveniently using.

```php
function isAdministrator() : bool {
    return $this->role === 'ADMIN';
}
```

### Create LoginController

- Now we have to create authentication process in `LoginController`.

```bash
php artisan make:controller LoginController
```

- Import required classes to `LoginController`.

```php
use Psr\Http\Message\ServerRequestInterface;
use Illuminate\Support\Facades\Auth;
```

### Add `authenticate()` to LoginController

- This method will check if email and password match our record.

```php
// in LoginController class
function authenticate(ServerRequestInterface $request): RedirectResponse {
    // authentication process
}
```

#### Authentication process: Get credentials information

- Laravel uses e-mail as username so we have to get email and password from login form.

```php
// get credentials from user.
$data = $request->getParsedBody();
$credentials = [
    'email' => $data['email'],
    'password' => $data['password'],
];
```

#### Authentication process: Authenticate credentials

- Laravel provides method `attempt()` for authentication process.
  - If successfully authenticated, Laravel automatically adds authenticated user to session.
  - When we request protected routes without being authenticated, Laravel redirects us to login page and keeps our requested URL, so we can redirect to our requested URL by using `redirect()->intended()`.

```php
// authenticate by using method attempt()
if (Auth::attempt($credentials)) {
    // regenerate a new session ID
    session()->regenerate();

    // redirect to the requested URL or
    // to route products.list if does not specify
    return redirect()->intended(route('products.list'));
}
```

#### Authentication process: Return to login form if fail

- If user cannot pass authentication process, then redirect back to login form.
  - We use method `withErrors()` to add error message to flashed session data. (We will learn about this later in the next chapter.)

```php
// if cannot authenticate redirect back to loginForm
// with error message.
return redirect()->back()->withErrors([
    'credentials' => 'The provided credentials do not match our records.',
]);
```

### Add `showLoginForm()` to LoginController

- The method to display login form.

```php
function showLoginForm(): View {
    return view('logins.form');
}
```

- By default, with authorization process, if there is no authenticated user in session then Laravel will redirect to route **`login`**.
  - That means **the named route of this method must be `login`**.

### `logins.form` template

- For login-form template we **do not extend main page**.
  - This template uses `@error` directive that we will learn about later.

```blade
{{-- HTML <head> & <body> --}}
<form action="{{ route('authenticate') }}" method="post">
    @csrf
    <label>
        E-mail <input type="text" name="email" required />
    </label><br />
    <label>
        Password <input type="password" name="password" required />
    </label><br />
    <button type="submit">Login</button>
    @error('credentials')
        <div class="warn">{{ $message }}</div>
    @enderror
</form>
```

### Add `logout()` method

- To logout, just call `Auth::logout()` then make session invalidate.

```php
function logout(): RedirectResponse {
    Auth::logout();

    session()->invalidate();

    // regenerate CSRF token
    session()->regenerateToken();

    return redirect()->route('login');
}
```

### Add routes to `routes/web.php`

```php
// add LoginController alias
use App\Http\Controllers\LoginController;
// ... other routes
Route::controller(LoginController::class)
    ->prefix('/auth')
    ->group(static function(): void {
        // name this route to login by default setting.
        Route::get('/login', 'showLoginForm')->name('login');
        Route::post('/login', 'authenticate')->name('authenticate');
        Route::post('/logout', 'logout')->name('logout');
    });
```

---

## 3. Laravel Authorization

### Authorization Process (flowchart)

```
START
  │
  ▼
/request for route/
  │
  ▼
<is route required authentication?> ──False──────────────────────────────┐
  │True                                                                  │
  ▼                                                                      ▼
<is user stored in session?> ──False──▶ [redirect to login page] ──▶ STOP
  │True                                                                  
  ▼                                                                      
<is route protected by authorization?> ──False──▶ [process route method] ──▶ STOP
  │True  [user from session]
  ▼
<is user authorized?> ──True──▶ [process route method] ──▶ STOP
  │False
  ▼
[access is denied] ──▶ STOP
```

- By default, Laravel uses authenticated user from *session*.
  - If it is present then get authenticated user and send it to authorization process.
  - If it is not present then redirect to route `login`.

### Define policy for each model

- Laravel uses **policy** to do authorization process.
- First, we have to create policy for each model.
  - The naming convention uses model name followed by `Policy` suffix, e.g., `ProductPolicy`.
  - We can use artisan command to create policy:

```bash
php artisan make:policy ProductPolicy
```

  - All policies are stored in `app/Policies` directory.
  - Has alias of `App\Models\User`.

### Define policy methods

- Now we can define policy by using class methods, e.g.:
  - **The method name is a policy name (policy action).**

```php
// in body of class ProductPolicy
function create(User $user): bool {
    return $user->isAdministrator();
}
```

  - We use method `isAdministrator()` from `User` model to check if authorized to create; only role `ADMIN` can create.
- In most cases, it requires at least one parameter that is `$user`.
- The return value:
  - `true` — is authorized
  - `false` — is not authorized
  - Since Laravel 11+, the methods can return `Response` object.

### Define policy methods (cont.) — using model value

- We can use the model value to check authorization, e.g.

```php
// alias App\Models\Product
function delete(User $user, Product $product): bool {
    return $this->create($user) && ($product->price < 500);
}
```

  - Only `$user` with updatable permission and product price < 500 can delete.

### Add required authentication process to controller methods

- For example, in `ProductController`, we require authenticated user for all methods in this controller, so we add middleware `auth` in routes definition.
  - Add to middleware group in file `routes/web.php`.

```php
// add middleware to controllers
Route::middleware(['auth'])
    ->group(static function(): void {
        Route::controller(ProductController::class)
            ->group(static function(): void {
                // ... routes definition for ProductController
            });
    });
```

- Now before all methods of this controller are called, Laravel will check authenticated user first.
  - If there is no authenticated user, it will redirect to route `login`.

### Add authorization process to controller methods

- For example, in method `showCreateForm()` and `create()` in `ProductController`.
  - We have to authorize on **both** form and real create process.
  - `Gate::authorize()` checks the given policy action; if it cannot pass, `AuthorizationException` is thrown and process is terminated.
  - If we want to check without throwing exception, use **`Gate::check()`** instead.

```php
function showCreateForm(): View {
    Gate::authorize('create', Product::class);
    // ... other statements
}

function create(ServerRequestInterface $request): View {
    Gate::authorize('create', Product::class);
    // ... other statements
}
```

  - The `Product::class` is required to determine the Policy class.
  - `'create'` is a method name, aka policy action, in Policy class.

- For example, in method `delete()` in `ProductController`:

```php
function delete($productCode): View {
    $product = Product::where('code', $productCode)->firstOrFail();
    Gate::authorize('delete', $product);
    // ... other statements
}
```

  - Laravel can determine Policy class by using type of `$product`.

### How does Laravel determine the Policy class?

- Consider `ProductPolicy` method `create()`:

```php
function create(User $user): bool { ... }
```

  and calling `authorize()` in `ProductController`:

```php
Gate::authorize('create', Product::class);
```

  - Without `Product::class`, Laravel cannot determine which Policy class would be used.
  - In this case, Laravel can determine that `ProductPolicy` is the Policy class for this authorization.

- Consider `ProductPolicy` method `delete()`:

```php
function delete(User $user, Product $product): bool { ... }
```

  and calling:

```php
Gate::authorize('delete', $product);
```

  - Laravel uses type of `$product` to determine the Policy class → `ProductPolicy`.

- **In conclusion:**
  - If policy method needs model object, e.g., `delete()`, we **have to** use model object, e.g., `$product`.
  - If we have model object, e.g., `$product`, we **can** use model object.
  - If we don't have model object, we can use class name, e.g., `Product::class`.

### Authorize in template

- For example, we don't want the link to delete product to appear in route `products.view` when product cannot be deleted.
  - In this case we use **`@can`** directive in template.
    - It can combine with `@elsecan` and `@else`, e.g., `@can ... @elsecan ... @else ... @endcan`.
  - The parameters of `@can` directive are the same as `Gate::check()`.
  - In contrast, we can use **`@cannot`** for the opposite case.

```blade
{{-- other links --}}

@can('delete', $product)
<li class="app-cl-warn">
    <button type="submit" form="app-form-delete" class="app-cl-link">
        Delete
    </button>
</li>
@endcan
```

### Getting authenticated user in template

- We can use `\Auth::user()`, alias of `Illuminate\Support\Facades\Auth::user()`, to get authenticated user in template.
- In main page we add the panel of authenticated user by showing name of user and link to logout.
  - File `resources/views/layouts/main.blade.php`
  - We use **`@auth`** directive to check whether there is an authenticated user.

```blade
{{-- In header element after h1 --}}
<nav class="app-cmp-user-panel">
    @auth
        <form action="{{ route('logout') }}" method="post">
            @csrf
            <span>{{ \Auth::user()->name }}</span>
            <button type="submit">Logout</button>
        </form>
    @endauth
</nav>
```

### Notable

- There is another approach to assign authorization process by using middleware on routes definition, in file `routes/web.php`.
  - But it requires implicit binding, so we leave this approach for self-study.

---

## 4. HTTP Cache Control

### Browser cache page problem

Scenario (from slide screenshots): Admin is on `/users` (Users: List) → clicks **Logout** → lands on `/auth/login` → user clicks browser **navigation back** → the browser shows the cached `Users: List` page again (with user data!) even though the user is logged out.

- This happens because web browser caches HTML for later fast loading.
- For authorization, we **have to prevent this behavior**.

### HTTP cache control

- From standard HTTP, we have to add HTTP header `Cache-Control`, e.g.

```
Cache-Control: no-cache, no-store, must-revalidate, max-age=0
```

- This HTTP header tells web browser how to control cache.
  - More: https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Cache-Control

### Laravel cache control

- Laravel provides cache control through middleware:
  - `\Illuminate\Http\Middleware\SetCacheHeaders`
  - Its alias is **`cache.headers`**
- We can add this middleware in route group:

```php
// in file routes/web.php
Route::controller(ProductController::class)
    ->middleware('cache.headers:no_store;no_cache;must_revalidate;max_age=0')
    ->group(/* closure function */);

// or if there are multiple middleware
Route::controller(ProductController::class)
    ->middleware([
        'auth',
        'cache.headers:no_store;no_cache;must_revalidate;max_age=0',
    ])
    ->group(/* closure function */);
```

- Or we can assign this middleware for multiple routes by using middleware group.
  - In file `routes/web.php`, wrap the required routes in middleware group:

```php
Route::middleware([
    'cache.headers:no_store;no_cache;must_revalidate;max_age=0',
])->group(function () {
    // ... routes definition
});
```

- ⚠️ For Laravel, we have to change **`-` to `_`** and **`,` to `;`** (e.g. `no-cache, no-store` → `no_cache;no_store`).

---

## 5. Example (step-by-step)

- Reuse the project from the previous week.
- Update and add data to `users` schema by using the given migration: `lec12-migrations.zip` (see [Part 2](#part-2--migrations-lec12-migrationszip)).
  - We want `users` schema to support role-based approach.
  - All users have password = **`1234`**
  - Run the following command:

```bash
php artisan migrate
```

### Step 1 — Update User model

1) We don't want user to change email after it has been created → **remove `email` from Fillable**.

```php
// remove email from Fillable
#[Fillable(['name', 'password'])]
```

2) Add the following method to `User` class for convenient usage.

```php
function isAdministrator(): bool {
    return $this->role === 'ADMIN';
}
```

### Login form (screen)

Login page (`/auth/login`): title **Login**, fields **Email**, **Password**, button **Login**. Does not use the main layout.

### Step 2 — Create LoginController

```bash
php artisan make:controller LoginController
```

- Add required aliases.

```php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
// (also: Psr\Http\Message\ServerRequestInterface, Illuminate\View\View,
//  Illuminate\Http\RedirectResponse)
```

### Step 3 — Add method `showForm()`

```php
function showForm(): View {
    return view('logins.form');
}
```

### Step 4 — Add method `logout()`

```php
function logout(): RedirectResponse {
    Auth::logout();
    session()->invalidate();

    // regenerate CSRF token
    session()->regenerateToken();

    return redirect()->route('login');
}
```

### Step 5 — Add method `authenticate()`

Skeleton:

```php
function authenticate(ServerRequestInterface $request): RedirectResponse {

    // get credentials from user.

    // authenticate by using method attempt()

    // if cannot authenticate redirect back to loginForm with error message.

}
```

1) Get credentials from user. This example uses `Illuminate\Support\Facades\Validator`.

```php
// get credentials from user.
// import Illuminate\Support\Facades\Validator
$validator = Validator::make(
    $request->getParsedBody(),
    [
        'email' => 'required',
        'password' => 'required',
    ],
);
```

2) Authenticate by using `$validator->passes()` and `Auth::attempt()`.

```php
// authenticate by using $validator->passes() and Auth::attempt()
if ($validator->passes()) {
    if (Auth::attempt($validator->safe()->only(['email', 'password']))) {
        // regenerate the new session ID
        session()->regenerate();

        // redirect to the requested URL or to route products.list if does not specify
        return redirect()->intended(route('products.index'));
    }

    $validator
        ->errors()
        ->add(
            'credentials',
            'The provided credentials do not match our records.',
        );
}
```

> หมายเหตุ: ในสไลด์ต้นฉบับบรรทัด `Auth::attempt(...)` ขาดวงเล็บปิด 1 ตัว — โค้ดข้างบนแก้ให้ถูก syntax แล้ว
> และ redirect ใช้ `route('products.index')` / `products.list` ให้ตรงกับชื่อ route ในโปรเจกต์ตัวเอง

3) In the case of authentication fail.

```php
// if cannot authenticate redirect back to loginForm with error message.
return redirect()
    ->back()
    ->withErrors($validator);
```

#### Full LoginController (รวมจาก step 2–5)

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Psr\Http\Message\ServerRequestInterface;

class LoginController extends Controller
{
    function showForm(): View
    {
        return view('logins.form');
    }

    function authenticate(ServerRequestInterface $request): RedirectResponse
    {
        $validator = Validator::make(
            $request->getParsedBody(),
            [
                'email' => 'required',
                'password' => 'required',
            ],
        );

        if ($validator->passes()) {
            if (Auth::attempt($validator->safe()->only(['email', 'password']))) {
                session()->regenerate();

                return redirect()->intended(route('products.list'));
            }

            $validator->errors()->add(
                'credentials',
                'The provided credentials do not match our records.',
            );
        }

        return redirect()->back()->withErrors($validator);
    }

    function logout(): RedirectResponse
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        return redirect()->route('login');
    }
}
```

### Step 6 — Add routes

Create authentication routes in file `routes/web.php`:
- `login` — `GET /auth/login`
- `authentication` — `POST /auth/login`
- `logout` — `POST /auth/logout`

```php
use App\Http\Controllers\LoginController;
// ... other routes
Route::controller(LoginController::class)
    ->prefix('/auth')
    ->group(static function(): void {
        // name this route to login following default setting.
        Route::get('/login', 'showForm')->name('login');
        Route::post('/login', 'authenticate')->name('authentication');
        Route::post('/logout', 'logout')->name('logout');
    });
```

> หมายเหตุ: ส่วน Lecture ใช้ชื่อ route `authenticate` แต่ส่วน Example ใช้ `authentication` — เลือกใช้ชื่อเดียวให้ตรงกันทั้ง route และ form

### Step 7 — `logins/form.blade.php`

- Don't extend `layouts.main`.

```blade
{{-- HTML <head> & <body> --}}
<form action="{{ route('authentication') }}" method="post">
    @csrf
    <label>
        E-mail <input type="email" name="email" required />
    </label><br />
    <label>
        Password <input type="password" name="password" required />
    </label><br />
    <button type="submit">Login</button>
    <div class="app-cmp-notifications">
        @error('credentials')
            <div role="alert">
                {{ $message }}
            </div>
        @enderror
    </div>
</form>
```

- **Incorrect credentials** → route `authentication` with incorrect email or password → redirect back to login form showing message *"The provided credentials do not match our records."*
- **Correct credentials** → route `authentication` with correct email and password → redirect to intended page (product list).

### Policy of Product model (requirement)

- **Administrators** can perform all tasks.
- **Users** can view only; allowed pages:
  - List products
  - View product
  - View shops of product

### Step 8 — Create ProductPolicy

```bash
php artisan make:policy ProductPolicy
```

```php
// in class body
function list(): bool {
    // All authenticated user can list
    return true;
}

function view(): bool {
    // Same as list action
    return $this->list();
}

function create(User $user): bool {
    return $user->isAdministrator();
}

function update(User $user): bool {
    // Same as create action.
    return $this->create($user);
}

function delete(User $user): bool {
    // Same as update action,
    // we consider delete is a special case of update.
    return $this->update($user);
}
```

#### Full ProductPolicy

```php
<?php

namespace App\Policies;

use App\Models\User;

class ProductPolicy
{
    function list(): bool
    {
        return true;
    }

    function view(): bool
    {
        return $this->list();
    }

    function create(User $user): bool
    {
        return $user->isAdministrator();
    }

    function update(User $user): bool
    {
        return $this->create($user);
    }

    function delete(User $user): bool
    {
        return $this->update($user);
    }
}
```

### Step 9 — Assign Authentication Middleware

Update `routes/web.php`: wrap routes `products.*`, `shops.*` and `categories.*` with `auth` middleware group.

```php
// assign middleware group
Route::middleware(['auth'])->group(static function(): void {
    // ... routes definition for products.*
    Route::controller(ProductController::class) // ...
    // ... routes definition for shops.*
    Route::controller(ShopController::class) // ...
    // ... routes definition for categories.*
    Route::controller(CategoryController::class) // ...
});
```

### Step 10 — Update ProductController

1) Add `Gate::authorize('list', Product::class)` to the top of method `list()`.

```php
// import Illuminate\Support\Facades\Gate
function list(/* ... parameters list */): View {
    Gate::authorize('list', Product::class);
    // ... other statements
}
```

2) Add `Gate::authorize('view', $product)` to the top of methods:
   - `view()`
   - `viewShops()`

```php
// Just an example
function view(/* ... parameters list */): View {
    $product = $this->find($productCode);

    Gate::authorize('view', $product);
    // ... other statements
}
```

3) Add `Gate::authorize('create', Product::class)` to the top of methods:
   - `showCreateForm()`
   - `create()`
   - The database processing method, method `create()`, is **more important**.

```php
function showCreateForm(/* ... parameters list */): View {
    Gate::authorize('create', Product::class);
    // ... other statements
}

function create(/* ... parameters list */): RedirectResponse {
    Gate::authorize('create', Product::class);
    // ... other statements
}
```

4) Add `Gate::authorize('update', $product)` to the top of methods:
   - `showUpdateForm()`
   - `update()`
   - `showAddShopsForm()`
   - `addShop()`
   - `removeShop()`

```php
// The top of the methods
$product = $this->find($productCode);

Gate::authorize('update', $product);
// ... other statements
```

5) Add `Gate::authorize('delete', $product)` to the top of method `delete()`.

```php
function delete(/* ... parameters list */): RedirectResponse {
    $product = $this->find($productCode);

    Gate::authorize('delete', $product);
    // ... other statements
}
```

#### สรุป mapping ProductController → policy action

| Controller method | Policy action | Argument |
|---|---|---|
| `list()` | `list` | `Product::class` |
| `view()`, `viewShops()` | `view` | `$product` |
| `showCreateForm()`, `create()` | `create` | `Product::class` |
| `showUpdateForm()`, `update()`, `showAddShopsForm()`, `addShop()`, `removeShop()` | `update` | `$product` |
| `delete()` | `delete` | `$product` |

### Step 11 — Add user panel in `layouts.main` template

Update `layouts/main.blade.php`:

```blade
{{-- in site-wide navigation --}}

<nav>
    {{-- main navigation --}}

    @auth
        <form action="{{ route('logout') }}" method="post"
            id="app-form-logout">
            @csrf
        </form>
        <ul class="app-cmp-user-panel">
            <li>{{ \Auth::user()->name }}</li>
            <li>
                <button type="submit" form="app-form-logout">Logout</button>
            </li>
        </ul>
    @endauth
</nav>
```

- **User role** screen: header shows user's name (e.g. "User") + Logout button.
- **Administrator role** screen: header shows "Administrator" + Logout button.

### Remove links for unauthorized user

- From the previous example, links always show although the current user cannot perform those actions.
- Now we have to remove the links if they are not allowed for the current user.
- All of this can be done in templates.

### Step 12 — `products/list.blade.php`

Remove link to create product when authenticated user is not administrator.

```blade
{{-- navigation --}}
<nav>
    <ul>
        @can('create', \App\Models\Product::class)
        <li>
            <a href="{{ route('product-create-form') }}">New Product</a>
        </li>
        @endcan
    </ul>
</nav>
```

### Step 13 — `products/view.blade.php`

Remove link to update and delete product when authenticated user is not administrator.

```blade
{{-- navigation --}}
<nav>
    <ul>
        {{-- ... other links --}}

        @can('view', $product)
            <li><a href="{{-- ... route --}}">View Shops</a></li>
        @endcan
        @can('update', $product)
            <li><a href="{{-- ... route --}}">Update</a></li>
        @endcan
        @can('delete', $product)
            <li><button {{-- ... attributes --}}>Delete</button></li>
        @endcan

        {{-- ... other links --}}
    </ul>
</nav>
```

### Step 14 — `products/view-shops.blade.php`

Remove link to add and remove shop when authenticated user is not administrator.

```blade
{{-- navigation --}}
<nav>
    <ul>
        @can('update', $product)
            <li><a href="{{-- ... route --}}">Add Shops</a></li>
        @endcan
    </ul>
</nav>

{{-- In table, other parts of the same column must be removed too. --}}
@can('update', $product)
    <td><button {{-- ... attributes --}}>Remove</button></td>
@endcan
```

> ⚠️ อย่าลืมซ่อน `<th>` ของคอลัมน์ Remove ด้วย `@can` เดียวกัน ไม่งั้นตารางจะเพี้ยน

Screens shown in slides: `products.list`, `products.view`, `products.view-shops` — for USER role the New Product / Update / Delete / Add Shops / Remove links are hidden; for ADMIN they appear.

### Delete policy of Category

- For more examples, we **cannot delete category that has at least one product**.
  - We can put this logic in `delete` action of `CategoryPolicy`.

### Step 15 — Create CategoryPolicy

```bash
php artisan make:policy CategoryPolicy
```

- Add some policy methods (not complete yet).

```php
use App\Models\Category;

// in class body
function create(User $user): bool {
    return $user->isAdministrator();
}

function update(User $user): bool {
    return $this->create($user);
}

function delete(User $user, Category $category): bool {
    // to make sure there is products_count.
    $category->loadCount('products');
    return $this->update($user) && ($category->products_count === 0);
}
```

### Step 16 — Update CategoryController (not complete yet)

1) Add `Gate::authorize('delete', $category)` to method `delete()` after we have gotten `$category` instance.

```php
// import Illuminate\Support\Facades\Gate
function delete(/* ... parameters list */): RedirectResponse {
    $category = $this->find($categoryCode);

    Gate::authorize('delete', $category);
    // ... other statements
}
```

### Step 17 — `categories/view.blade.php`

Remove link to delete category when authenticated user is not administrator **or** category cannot be deleted.

```blade
{{-- navigation --}}
<nav>
    <ul>
        {{-- ... other links --}}

        @can('delete', $category)
            <li><a href="{{-- ... route --}}">Delete</a></li>
        @endcan

        {{-- ... other links --}}
    </ul>
</nav>
```

Screens shown: `categories.list`, `categories.view`.

### Step 18 — Make the specific routes no-cache

In file `routes/web.php`, wrap authentication routes and controller routes:

```php
Route::middleware([
    'cache.headers:no_store;no_cache;must_revalidate;max_age=0',
])->group(static function (): void {
    // ... routes definition for authentication
    Route::controller(LoginController::class) // ...

    // ... routes for auth middleware group
    Route::middleware(['auth'])->group(function () {
        // ... routes definition for controllers
    });
});
```

#### ตัวอย่างโครง `routes/web.php` แบบเต็ม (รวมทุก step)

```php
<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ShopController;
use Illuminate\Support\Facades\Route;

Route::middleware([
    'cache.headers:no_store;no_cache;must_revalidate;max_age=0',
])->group(static function (): void {

    Route::controller(LoginController::class)
        ->prefix('/auth')
        ->group(static function (): void {
            Route::get('/login', 'showForm')->name('login');
            Route::post('/login', 'authenticate')->name('authentication');
            Route::post('/logout', 'logout')->name('logout');
        });

    Route::middleware(['auth'])->group(static function (): void {
        Route::controller(ProductController::class)
            ->prefix('/products')
            ->name('products.')
            ->group(static function (): void {
                // ... product routes
            });

        Route::controller(ShopController::class)
            ->prefix('/shops')
            ->name('shops.')
            ->group(static function (): void {
                // ... shop routes
            });

        Route::controller(CategoryController::class)
            ->prefix('/categories')
            ->name('categories.')
            ->group(static function (): void {
                // ... category routes
            });

        // Lab 12: UserController routes (see Part 3)
    });
});
```

---

## 6. References

- https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Headers/Cache-Control
- https://laravel.com/framework/docs/13.x/authentication
- https://laravel.com/framework/docs/13.x/authorization
- https://laravel.com/framework/docs/13.x/hashing

---

# Part 2 — Migrations (`lec12-migrations.zip`)

ไฟล์ใน zip มี 2 ไฟล์ — วางไว้ใน `database/migrations/` แล้วรัน `php artisan migrate`

### `2026_09_22_103402_add_column_role_to_users_table.php`

เพิ่มคอลัมน์ `role` (nullable ก่อน) ต่อจาก `remember_token`

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
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 32)->nullable()->after('remember_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};
```

### `2026_09_29_094412_add_nonnullable_role_to_users_table.php`

1. ตั้ง `role = 'USER'` ให้ user เดิมที่ยังเป็น NULL
2. สร้าง/อัปเดต user ตัวอย่าง 2 คน (`admin@my-db.com` = ADMIN, `user@my-db.com` = USER, password = `1234`)
3. เปลี่ยน `role` เป็น NOT NULL

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('users')->whereNull('role')->update(['role' => 'USER']);

        $currentTstmp = now();

        DB::table('users')->updateOrInsert(
            ['email' => 'admin@my-db.com'],
            [
                'name' => 'Administrator',
                'email' => 'admin@my-db.com',
                'email_verified_at' => $currentTstmp,
                'password' => Hash::make('1234'),
                'role' => 'ADMIN',
                'created_at' => $currentTstmp,
                'updated_at' => $currentTstmp,
            ],
        );

        DB::table('users')->updateOrInsert(
            ['email' => 'user@my-db.com'],
            [
                'name' => 'User',
                'email' => 'user@my-db.com',
                'email_verified_at' => $currentTstmp,
                'password' => Hash::make('1234'),
                'role' => 'USER',
                'created_at' => $currentTstmp,
                'updated_at' => $currentTstmp,
            ],
        );

        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->nullable(true)->change();
        });
    }
};
```

**บัญชีทดสอบ**

| Email | Password | Role |
|---|---|---|
| `admin@my-db.com` | `1234` | ADMIN |
| `user@my-db.com` | `1234` | USER |

---

# Part 3 — Lab 12

**Practice 954348 — 01/2569 — Lab 12 Authentication & Authorization**

> NOTE: Use the project from example of week 12.

## Lab 1 — Create Policy for Shop model

| Action | Who | Routes |
|---|---|---|
| `list` | authenticated user | `shops.list` |
| `create` | only administrator | `shops.create-form`, `shops.create` |
| `update` | same as create | `shops.update-form`, `shops.update`, `shops.add-products-form`, `shops.add-product`, `shops.remove-product` |
| `delete` | same as update | `shops.delete` |
| `view` | authenticated user | **Other routes** (e.g. `shops.view`, `shops.view-products`) |

- Update **all templates** to remove unauthorized links.

## Lab 2 — Update Policy for Category model

| Action | Who | Routes |
|---|---|---|
| `list` | authenticated user | `categories.list` |
| `create` | only administrator | `categories.create-form`, `categories.create` |
| `update` | same as create | `categories.update-form`, `categories.update`, `categories.add-products-form`, `categories.add-product` |
| `view` | authenticated user | **Other routes** |
| `delete` | (from lecture) admin AND category has 0 products | `categories.delete` |

- Update **all templates** to remove unauthorized links.

## Lab 3 — Create CRUD for User model

- **Only administrators** can perform tasks for this model, so prevent normal users accessing all routes in `UserController`.
- The users **cannot delete** and **cannot change roles** for their own data.
- For self-user data, he/she can **view and update** his/her own data but **cannot change role**. The users can change their own data by using the link from **name before logout link**.
- In route `users.list` the search term can be used on fields **email, name and role**.
- Different from other controllers, it uses **email as code**.
- There are 2 roles for users: **`ADMIN`** and **`USER`**.

> **Important:** User cannot delete self-record and cannot update own role.
>
> **Note:** From Laravel 10+, password field in User model is **hashed by default** (cast `'password' => 'hashed'`). So, we just set password as plain text. Study more: https://laravel.com/framework/docs/13.x/hashing

### Routes

#### `users.list`
- **URL:** `[GET] /users`
- **Controller Method:** `list`
- **Template:** `users.list`
- **Bookmarks:**
  - `bookmarks.users.create-form` → current URL
  - `bookmarks.users.create` → same as `bookmarks.users.create-form`
  - `bookmarks.users.view` → current URL

#### `users.create-form`
- **URL:** `[GET] /users/create`
- **Controller Method:** `showCreateForm`
- **Template:** `users.create-form`
- **Back Link:** [bookmark] `bookmarks.users.create-form`, default route `users.index`.

#### `users.create`
- **URL:** `[POST] /users`
- **Controller Method:** `create`
- **Forward:** `bookmarks.users.create` to `bookmarks.users.view`.
- **Redirect:** to route `users.view`.
- **Status Message:** `User {$user->email} was created.`

> Because path `/users/{user}` contains route parameter, we have to put all paths **`/users/self/*` before it**.

#### `users.selves.view`
- **URL:** `[GET] /users/self`
- **Controller Method:** `viewSelf`
- **Template:** `users.selves.view`
- **Back Link:** [bookmark] `bookmarks.users.selves.view`, default root path (only slash symbol, `/`).
- **Bookmarks:**
  - `bookmarks.users.selves.update-form` → current URL
  - `bookmarks.users.selves.update` → same as `bookmarks.users.selves.view`

#### `users.selves.update-form`
- **URL:** `[GET] /users/self/update`
- **Controller Method:** `showUpdateSelfForm`
- **Template:** `users.selves.update-form`
- **Back Link:** [bookmark] `bookmarks.users.selves.update-form`, default route `users.selves.view`.

#### `users.selves.update`
- **URL:** `[POST] /users/self`
- **Controller Method:** `updateSelf`
- **Forward:** `bookmarks.users.selves.update` to `bookmarks.users.selves.view`.
- **Redirect:** to route `users.selves.view`.
- **Status Message:** `Your information was updated.`

#### `users.view`
- **URL:** `[GET] /users/{user}`
- **Controller Method:** `view`
- **Template:** `users.view`
- **Back Link:** [bookmark] `bookmarks.users.view`, default route `users.index`.
- **Bookmarks:**
  - `bookmarks.users.delete` → same as `bookmarks.users.view`
  - `bookmarks.users.update-form` → current URL
  - `bookmarks.users.update` → same as `bookmarks.users.view`

#### `users.update-form`
- **URL:** `[GET] /users/{user}/update`
- **Controller Method:** `showUpdateForm`
- **Template:** `users.update-form`
- **Back Link:** [bookmark] `bookmarks.users.update-form`, default route `users.view`.

#### `users.update`
- **URL:** `[POST] /users/{user}`
- **Controller Method:** `update`
- **Forward:** `bookmarks.users.update` to `bookmarks.users.view`.
- **Redirect:** to route `users.view`.
- **Status Message:** `User {$user->email} was updated.`

#### `users.delete`
- **URL:** `[POST] /users/{user}/delete`
- **Controller Method:** `delete`
- **Redirect:** [bookmark] `bookmarks.users.delete`, default route `users.index`.
- **Status Message:** `User {$user->email} was deleted.`

#### Route summary table

| Route name | Method | URL | Controller method | Template |
|---|---|---|---|---|
| `users.list` | GET | `/users` | `list` | `users.list` |
| `users.create-form` | GET | `/users/create` | `showCreateForm` | `users.create-form` |
| `users.create` | POST | `/users` | `create` | — |
| `users.selves.view` | GET | `/users/self` | `viewSelf` | `users.selves.view` |
| `users.selves.update-form` | GET | `/users/self/update` | `showUpdateSelfForm` | `users.selves.update-form` |
| `users.selves.update` | POST | `/users/self` | `updateSelf` | — |
| `users.view` | GET | `/users/{user}` | `view` | `users.view` |
| `users.update-form` | GET | `/users/{user}/update` | `showUpdateForm` | `users.update-form` |
| `users.update` | POST | `/users/{user}` | `update` | — |
| `users.delete` | POST | `/users/{user}/delete` | `delete` | — |

### Bookmark for self view (in `layouts.main`)

All routes **except routes under `users.selves.*`** have to bookmark for `bookmarks.users.selves.view`. This can be done in template `layouts.main`.

We use `Route::is($pattern)` for checking that the current route matches the given `$pattern`. So, the bookmarking code should be:

```blade
@php
    if (!Route::is('users.selves.*')) {
        session()->put('bookmarks.users.selves.view', url()->full());
    }
@endphp
```

**Hint:** In controllers under route `users.selves.*`, we can get current authenticated user by using `Auth::user()` but the returned class is `\Illuminate\Contracts\Auth\Authenticatable`. So, we have to cast it to `User` by using the following code:

```php
$user = Auth::user();
assert($user instanceof User);
```

### Expected screens (from Lab screenshots)

1. **Product: List as USER role** — top nav shows `Product`, `Shop`, `Category` (**no `User` link** for normal users). Right side shows the user name (`User`) as a link + `Logout` button.
2. **User: List** (`/users?term=user`, logged in as Administrator) — nav has `Product`, `Shop`, `Category`, `User`; right side `Administrator` + `Logout`. Search box with `Term`, `Filter`, `Clear`; button `Create User`; table "List of Users" with columns **Email | Name | Role**. Searching `user` matches on email, name and role (e.g. `adam@my-db.com / Adam Smith / USER` and `user@my-db.com / User / USER`).
   - *The search term can be used on field email, name and role.*
3. **User: Create** (`/users/create`) — fields: `Email *`, `Password *`, `Name *`, `Role *` (select: `--- Please Select Role ---`, options ADMIN/USER); buttons `Create` and `Cancel`.
4. **User: admin@my-db.com** (view own record as admin, `/users/admin@my-db.com`) — shows Email, Name, Role; buttons `< Back` and `Update` only — **no Delete** (*Cannot delete self-data*).
5. **Update own record as admin** (`/users/admin@my-db.com/update`):
   - Email shown as text — *Cannot change email after it has been created.*
   - Name `*` editable.
   - Role shown as text `ADMIN` — *Cannot change role of self-data.*
   - Password input with placeholder **"Leave blank if you don't want to update"** — *Because password was encrypted and we don't know the real value. If user leaves this control empty, that means use the old value. You can use HTML attribute `placeholder` to show this message.*
   - Buttons `Update`, `Cancel`.
6. **Update other user as admin** (`/users/user@my-db.com/update`) — Email text, Name `*`, **Role `*` as a select (USER/ADMIN)** — *Can change role of other users*; Password with placeholder.
7. **Product: List as USER** — the name link (`User`) at top right links to route **`users.selves.view`**.
8. **User: Self** (`/users/self`) — buttons `< Back` (back link uses bookmark `bookmarks.users.selves.view`) and `Update Self`; shows Email and Name (no role editing).
9. **User: Self update** (`/users/self/update`) — Email as text, `Name *`, `Password` (placeholder "Leave blank if you don't want to update"); buttons `Update`, `Cancel`. No Role field.

### Lab 3 — Implementation guide (แนวทางที่ใช้ทำ Project ได้)

> ส่วนนี้เป็นแนวทางเสริมจากโจทย์ (ไม่ได้อยู่ใน PDF โดยตรง) — ปรับให้ตรงกับโครงสร้างโปรเจกต์เดิม (เช่น ชื่อ method `find()`, pagination, search pattern) ของตัวเอง

#### UserPolicy

```php
<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    function list(User $user): bool
    {
        return $user->isAdministrator();
    }

    function view(User $user): bool
    {
        return $this->list($user);
    }

    function create(User $user): bool
    {
        return $user->isAdministrator();
    }

    function update(User $user): bool
    {
        return $this->create($user);
    }

    // admin เปลี่ยน role ได้ แต่ไม่ใช่ของตัวเอง
    function updateRole(User $user, User $target): bool
    {
        return $this->update($user) && ($user->id !== $target->id);
    }

    // admin ลบได้ แต่ห้ามลบตัวเอง
    function delete(User $user, User $target): bool
    {
        return $this->update($user) && ($user->id !== $target->id);
    }
}
```

#### Routes (ต้องวาง `/users/self*` ก่อน `/users/{user}`)

```php
Route::controller(UserController::class)
    ->prefix('/users')
    ->name('users.')
    ->group(static function (): void {
        Route::get('', 'list')->name('list');
        Route::get('/create', 'showCreateForm')->name('create-form');
        Route::post('', 'create')->name('create');

        // self routes — must be before /{user}
        Route::get('/self', 'viewSelf')->name('selves.view');
        Route::get('/self/update', 'showUpdateSelfForm')->name('selves.update-form');
        Route::post('/self', 'updateSelf')->name('selves.update');

        Route::get('/{user}', 'view')->name('view');
        Route::get('/{user}/update', 'showUpdateForm')->name('update-form');
        Route::post('/{user}', 'update')->name('update');
        Route::post('/{user}/delete', 'delete')->name('delete');
    });
```

#### UserController (โครง)

```php
<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Psr\Http\Message\ServerRequestInterface;

class UserController extends Controller
{
    private function find(string $email): User
    {
        return User::where('email', $email)->firstOrFail();
    }

    private function self(): User
    {
        $user = Auth::user();
        assert($user instanceof User);
        return $user;
    }

    function list(ServerRequestInterface $request): View
    {
        Gate::authorize('list', User::class);

        $term = $request->getQueryParams()['term'] ?? null;
        $query = User::orderBy('email');

        if ($term !== null) {
            $query->where(function ($q) use ($term) {
                $q->where('email', 'LIKE', "%{$term}%")
                  ->orWhere('name', 'LIKE', "%{$term}%")
                  ->orWhere('role', 'LIKE', "%{$term}%");
            });
        }

        session()->put('bookmarks.users.create-form', url()->full());
        session()->put('bookmarks.users.view', url()->full());

        return view('users.list', [
            'term' => $term,
            'users' => $query->paginate(5),
        ]);
    }

    function showCreateForm(): View
    {
        Gate::authorize('create', User::class);
        session()->put('bookmarks.users.create', session('bookmarks.users.create-form'));
        return view('users.create-form');
    }

    function create(ServerRequestInterface $request): RedirectResponse
    {
        Gate::authorize('create', User::class);
        $data = $request->getParsedBody();

        $user = new User();
        $user->fill($data);              // name, password (hashed automatically)
        $user->email = $data['email'];   // email not in Fillable
        $user->role = $data['role'];     // role not in Fillable
        $user->save();

        session()->put('bookmarks.users.view', session('bookmarks.users.create'));

        return redirect()->route('users.view', ['user' => $user->email])
            ->with('status', "User {$user->email} was created.");
    }

    function view(string $email): View
    {
        $user = $this->find($email);
        Gate::authorize('view', $user);

        session()->put('bookmarks.users.update-form', url()->full());
        session()->put('bookmarks.users.update', session('bookmarks.users.view'));
        session()->put('bookmarks.users.delete', session('bookmarks.users.view'));

        return view('users.view', ['user' => $user]);
    }

    function showUpdateForm(string $email): View
    {
        $user = $this->find($email);
        Gate::authorize('update', $user);
        return view('users.update-form', ['user' => $user]);
    }

    function update(ServerRequestInterface $request, string $email): RedirectResponse
    {
        $user = $this->find($email);
        Gate::authorize('update', $user);
        $data = $request->getParsedBody();

        $user->name = $data['name'];
        if (!empty($data['password'])) {   // blank = keep old password
            $user->password = $data['password'];
        }
        if (Gate::check('updateRole', $user) && isset($data['role'])) {
            $user->role = $data['role'];   // cannot change own role
        }
        $user->save();

        session()->put('bookmarks.users.view', session('bookmarks.users.update'));

        return redirect()->route('users.view', ['user' => $user->email])
            ->with('status', "User {$user->email} was updated.");
    }

    function delete(string $email): RedirectResponse
    {
        $user = $this->find($email);
        Gate::authorize('delete', $user);   // cannot delete self
        $user->delete();

        return redirect(session('bookmarks.users.delete', route('users.list')))
            ->with('status', "User {$user->email} was deleted.");
    }

    // ---------- self ----------

    function viewSelf(): View
    {
        session()->put('bookmarks.users.selves.update-form', url()->full());
        session()->put('bookmarks.users.selves.update', url()->full());
        return view('users.selves.view', ['user' => $this->self()]);
    }

    function showUpdateSelfForm(): View
    {
        return view('users.selves.update-form', ['user' => $this->self()]);
    }

    function updateSelf(ServerRequestInterface $request): RedirectResponse
    {
        $user = $this->self();
        $data = $request->getParsedBody();

        $user->name = $data['name'];
        if (!empty($data['password'])) {
            $user->password = $data['password'];
        }
        $user->save();   // role never changes here

        return redirect()->route('users.selves.view')
            ->with('status', 'Your information was updated.');
    }
}
```

> ⚠️ Self routes (`users.selves.*`) ไม่ต้อง `Gate::authorize` แบบ admin-only เพราะ user ปกติก็ต้องเข้าได้ — แต่ยังต้องอยู่ใน `auth` middleware
> ⚠️ โจทย์ให้ `default route users.index` — ถ้าโปรเจกต์ไม่มี route `users.index` ให้ใช้ `users.list` แทน

#### `layouts.main` — nav & user panel

```blade
@php
    if (!Route::is('users.selves.*')) {
        session()->put('bookmarks.users.selves.view', url()->full());
    }
@endphp

<nav>
    <ul>
        <li><a href="{{ route('products.list') }}">Product</a></li>
        <li><a href="{{ route('shops.list') }}">Shop</a></li>
        <li><a href="{{ route('categories.list') }}">Category</a></li>
        @can('list', \App\Models\User::class)
            <li><a href="{{ route('users.list') }}">User</a></li>
        @endcan
    </ul>

    @auth
        <form action="{{ route('logout') }}" method="post" id="app-form-logout">
            @csrf
        </form>
        <ul class="app-cmp-user-panel">
            <li><a href="{{ route('users.selves.view') }}">{{ \Auth::user()->name }}</a></li>
            <li><button type="submit" form="app-form-logout">Logout</button></li>
        </ul>
    @endauth
</nav>
```

#### `users/update-form.blade.php` — role & password fields

```blade
<label>Email</label> <span>{{ $user->email }}</span>   {{-- cannot change email --}}

<label>Name *</label>
<input type="text" name="name" value="{{ $user->name }}" required />

@can('updateRole', $user)
    <label>Role *</label>
    <select name="role" required>
        @foreach (['ADMIN', 'USER'] as $role)
            <option value="{{ $role }}" @selected($user->role === $role)>{{ $role }}</option>
        @endforeach
    </select>
@else
    <label>Role</label> <span>{{ $user->role }}</span>
@endcan

<label>Password</label>
<input type="password" name="password"
       placeholder="Leave blank if you don't want to update" />
```

#### `users/view.blade.php` — hide Delete for self

```blade
@can('update', $user)
    <li><a href="{{ route('users.update-form', ['user' => $user->email]) }}">Update</a></li>
@endcan
@can('delete', $user)
    <li><button type="submit" form="app-form-delete">Delete</button></li>
@endcan
```

---

# Appendix — Quick Cheat Sheet

| เรื่อง | ใช้อะไร |
|---|---|
| Login | `Auth::attempt(['email' => ..., 'password' => ...])` → `session()->regenerate()` → `redirect()->intended(...)` |
| Logout | `Auth::logout()` → `session()->invalidate()` → `session()->regenerateToken()` |
| ผู้ใช้ปัจจุบัน (PHP) | `$user = Auth::user(); assert($user instanceof User);` |
| ผู้ใช้ปัจจุบัน (Blade) | `\Auth::user()->name`, `@auth ... @endauth` |
| ชื่อ route login | ต้องชื่อ **`login`** (Laravel redirect มาที่นี่อัตโนมัติ) |
| บังคับ login | `Route::middleware(['auth'])->group(...)` |
| สร้าง policy | `php artisan make:policy XxxPolicy` (ใน `app/Policies`) |
| ตรวจสิทธิ์ (throw 403) | `Gate::authorize('action', Model::class)` / `Gate::authorize('action', $model)` |
| ตรวจสิทธิ์ (bool) | `Gate::check('action', ...)` |
| ซ่อนลิงก์ใน Blade | `@can('action', $model) ... @elsecan ... @else ... @endcan`, `@cannot` |
| เช็ก route ปัจจุบัน | `Route::is('users.selves.*')` |
| กัน back-button cache | `'cache.headers:no_store;no_cache;must_revalidate;max_age=0'` |
| Password | Laravel 10+ hash อัตโนมัติ (cast `hashed`) → set plain text ได้เลย; ช่องว่าง = ไม่เปลี่ยน |
| Mass assignment | ห้ามใส่ `role` (และ `email` หลัง example) ใน `#[Fillable]` → set ทีละ field |
| Count relation ใน policy | `$category->loadCount('products'); $category->products_count === 0` |


---

<!-- ============================================================ -->

# Week 13 — Errors Handling (Lecture + Lab)

> Practice 954348 01/2569 — Laravel (Laravel 13.x, PHP 8.5)

## สารบัญ

- [Part A — Lecture: Errors Handling](#part-a--lecture-errors-handling)
  - [1. try ... catch statement](#1-try--catch-statement)
  - [2. Flashed session for errors](#2-flashed-session-for-errors)
  - [3. Catching Illuminate\Database\QueryException](#3-catching-illuminatedatabasequeryexception)
  - [4. Example — ProductController (step by step)](#4-example--productcontroller-step-by-step)
  - [5. ตัวอย่างผลลัพธ์ (Screenshots)](#5-ตัวอย่างผลลัพธ์-screenshots)
  - [6. References](#6-references)
- [Part B — Lab 13: Errors Handling](#part-b--lab-13-errors-handling)

---

# Part A — Lecture: Errors Handling

**Contents**

- try ... catch statement
- Flashed session for errors
- References

---

## 1. try ... catch statement

- ภาษาโปรแกรมหลายภาษามีคำสั่งที่ใช้จัดการ error ที่เกิดขึ้นในกลุ่มของคำสั่ง โดยใช้ `try ... catch`
- ข้อมูลของ error เรียกว่า **exception**
- exception เป็น **object**
- มี exception หลายชนิดที่อาจเกิดขึ้นในกลุ่มคำสั่ง เช่น
  - `\DivisionByZeroError`
  - `\InvalidArgumentException`
  - ฯลฯ
- exception class ทั้งหมด implement interface `\Throwable`
- อ่านเพิ่มเติม: <https://www.php.net/manual/en/language.exceptions.php>

### 1.1 Exception หลัก 2 ประเภทใน PHP

PHP แบ่ง exception เป็น 2 ประเภทหลัก:

1. **error**
   - error ที่ตัวภาษาสร้างขึ้นเอง เช่น `\DivisionByZeroError`
   - ส่วนใหญ่ลงท้ายด้วย `Error`
   - extend class `\Error`
2. **exception**
   - error เชิงโปรแกรมที่โปรแกรมเมอร์สร้างขึ้น เช่น `\InvalidArgumentException`
   - ส่วนใหญ่ลงท้ายด้วย `Exception`
   - extend class `\Exception`

> ถ้าต้องการจัดการทั้ง error และ exception ต้อง catch `\Throwable`

### 1.2 โครงสร้างของ try ... catch

```php
try {
    // If there are some errors occur in block of try statement,
    // it will be thrown to blocks of catch statement.
    // Or you can use throw command to create an instance of exception, e.g.
    // throw new \Exception('The error message.');
}
catch(TypeOfException $excp) {
    // If you want to get error message use $excp->getMessage()
}
/* ... other catch of specific exception types if needed. */
catch(OtherTypeOfException $excp) {
    // If you want to catch all exceptions this type could be \Throwable.
}
/* finally if needed. */
finally {
    // Code within the block of finally will always be executed
    // regardless of whether an exception has been thrown.
    // It is also executed when there are return in try ... catch.
}
```

---

## 2. Flashed session for errors

### 2.1 จัดการ error ด้วย flashed session

- ตัวอย่าง: ถ้าสร้าง Product model ด้วยโค้ดเดิม จะค้างอยู่ที่ route `products.create` พร้อมข้อความ error
- ในกรณีนี้ เราต้องการ **กลับไปที่ route `products.create-form`** พร้อมข้อความ error
- และต้องการให้ **form-data ถูกเติมกลับเข้าไปในฟอร์ม**
- Laravel มี method สำหรับเพิ่มข้อมูลลง flashed session:
  - `withInput()` — ใส่ form-data ลงใน flashed session
  - `withErrors($errors)` — ใส่ `$errors` ลงใน flashed session
    - `$errors` เป็น associative array คู่ของ *ชื่อ error* กับ *ข้อความ* หรือเป็น validator object
- ใช้ `try ... catch` เพื่อดักจับข้อความ error

### 2.2 การใช้งานใน template

- `@error('error_name')`
  - ตรวจว่ามี error ชื่อ `'error_name'` ใน flashed session หรือไม่
  - error ถูกเพิ่มลง flashed session ด้วย method `withErrors()`
  - ใช้ตัวแปรภายใน `$message` เพื่อดึงข้อความ error ของชื่อนั้น
- `old('control_name', $default_value = null)`
  - ดึง form-data ก่อนหน้าของ `'control_name'`
  - ถ้าไม่มี form-data ก่อนหน้า:
    - ถ้ากำหนด `$default_value` → คืนค่า `$default_value`
    - ถ้าไม่กำหนด → คืนค่า `null`
  - ค่า `null` จะถูกแปลงเป็น empty string ใน template

### 2.3 Example — method `create()` ของ ProductController (catch `\Throwable`)

```php
function create(ServerRequestInterface $request): RedirectResponse {
    $this->authorize('create', Product::class);

    try {
        // ... Normal process

        return redirect(
            session()->get(
                'bookmarks.products.create-form',
                route('products.list'),
            ),
        )
            ->with('status', "Product {$product->code} was created.")
        ;
    } catch(\Throwable $excp) {
        return redirect()->back()->withInput()->withErrors([
            'alert' => $excp->getMessage(),
        ]);
    }
}
```

### 2.4 Example — main template `resources/views/layouts/main.blade.php`

- ใช้ directive `@error()` ตรวจว่ามี error ชื่อนั้นหรือไม่
- ใช้ตัวแปรภายใน `$message` ดึงข้อความ error

```blade
<div class="app-cmp-notifications">
    {{-- status message --}}
    @error('alert')
        <div role="alert">
            {{ $message }}
        </div>
    @enderror
</div>
```

### 2.5 Example — template `resources/views/products/create.blade.php`

- ใช้ฟังก์ชัน `old()` ดึง form-data ก่อนหน้า เช่น control `code`
- ถ้าไม่มี form-data ก่อนหน้า ฟังก์ชันนี้คืนค่า `null`

```blade
<label>
    <b>Code</b>
    <input name="code" value="{{ old('code') }}" />
</label>
```

### 2.6 เปรียบเทียบผลลัพธ์

**Without error handling**

- ฟอร์ม Product: Create กรอก Code `PD001` (ซ้ำกับที่มีอยู่), Name `New Product`, Category `[CT003] JavaScript`, Price `1234.56`, Description `The new product.`
- กด Create → ได้หน้า **Internal Server Error (500)** ของ Laravel ที่ `POST http://localhost:8000/products`
- Exception: `Illuminate\Database\UniqueConstraintViolationException`
- ข้อความ: `SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry 'PD001' for key 'products_code_unique' (Connection: mysql, ..., SQL: insert into products (...) values (...))`

**With error handling (catch `\Throwable`)**

- กลับมาที่หน้า `products/create` พร้อมกล่องแจ้งเตือนสีแดงแสดงข้อความ `SQLSTATE[23000]: ...` แบบเต็ม (ยาวมาก)
- ค่าในฟอร์มทุกช่อง (Code, Name, Category, Price, Description) **เหมือนกับ form-data ก่อนเกิด error**

---

## 3. Catching `Illuminate\Database\QueryException`

- จากตัวอย่างก่อนหน้า ข้อความ error จาก `$excp->getMessage()` ยาวมากและ **ไม่เป็นมิตรกับผู้ใช้**
  - เหมาะกับโปรแกรมเมอร์ แต่ไม่เหมาะกับผู้ใช้
- ชนิดของ exception นี้คือ `Illuminate\Database\QueryException` ซึ่งให้ข้อความ error ที่มีประโยชน์กว่าเล็กน้อย
  - ผ่าน property `$errorInfo` (numeric array) ที่ **index 2**
  - `$excp->errorInfo[2]`
  - ⚠️ **ห้ามใช้ property `$errorInfo` กับ exception อื่น** — มีเฉพาะใน `QueryException` เท่านั้น
  - อ่านเพิ่ม: <https://www.php.net/manual/en/pdo.errorinfo.php>
- ในกรณีนี้ เราจัดการ `QueryException` แล้วตามด้วย `\Exception` **สำหรับการจัดการฐานข้อมูลเท่านั้น**
  - ปล่อยให้ Laravel จัดการ exception อื่น ๆ

### 3.1 Handle errors สำหรับ `Illuminate\Database\QueryException`

อัปเดต method `create()` ใน `ProductController`:

```php
use Illuminate\Database\QueryException;

// in class body
function create(Request $request): RedirectResponse {
    // Actually, now we can move authorize() into try block,
    // because we only catch QueryException.
    $this->authorize('create', Product::class);

    try {
        // ... Database manipulation process

        return redirect()->route('products.list')
            ->with('status', "Product {$product->code} was created.")
        ;
    } catch(QueryException $excp) {
        return redirect()->back()->withInput()->withErrors([ 'alert' => $excp->errorInfo[2] ]);
    } catch(\Exception $excp) {
        return redirect()->back()->withInput()->withErrors([ 'alert' => $excp->getMessage() ]);
    }
}
```

**ผลลัพธ์ With `QueryException` handling:** กลับมาหน้า `products/create` พร้อมข้อความสั้นที่อ่านง่าย:

> ⚠ Duplicate entry 'PD001' for key 'products_code_unique'

และค่าในฟอร์มยังคงเหมือนเดิมทุกช่อง

---

## 4. Example — ProductController (step by step)

- ใช้โปรเจกต์จากสัปดาห์ก่อนหน้า
- สร้าง `QueryException` handling ให้ทุก method ที่จัดการฐานข้อมูลใน `ProductController`:
  - `create()`
  - `addShop()`
  - `removeShop()`
  - `update()`
  - `delete()`

### Step 1 — Update main template `layouts/main.blade.php`

```blade
<div class="app-cmp-notifications">
    {{-- status message --}}
    @error('alert')
        <div role="alert">
            {{ $message }}
        </div>
    @enderror
</div>
```

### Step 2 — Update `ProductController`

#### 2.1) Import `Illuminate\Database\QueryException`

```php
use Illuminate\Database\QueryException;
// ... other aliases
```

#### 2.2) `ProductController::create()`

```php
function create(Request $request): RedirectResponse {
    $this->authorize('create', Product::class);

    try {
        // ... Database manipulation process
    } catch(QueryException $excp) {
        return redirect()->back()->withInput()->withErrors([
            'alert' => $excp->errorInfo[2],
        ]);
    } catch(\Exception $excp) {
        return redirect()->back()->withInput()->withErrors([
            'alert' => $excp->getMessage(),
        ]);
    }
}
```

#### 2.3) `ProductController::addShop()`

```php
function addShop(/* ... parameters list */): RedirectResponse {
    $product = $this->find($productCode);
    $this->authorize('update', $product);

    try {
        // ... Database manipulation process
    } catch(QueryException $excp) {
        // We don't want withInput() here.
        return redirect()->back()->withErrors([
            'alert' => $excp->errorInfo[2],
        ]);
    } catch(\Exception $excp) {
        return redirect()->back()->withErrors([
            'alert' => $excp->getMessage(),
        ]);
    }
}
```

#### 2.4) `ProductController::removeShop()`

```php
function removeShop(/* ... parameters list */): RedirectResponse {
    $product = $this->find($productCode);
    $this->authorize('update', $product);

    try {
        // ... Database manipulation process
    } catch(QueryException $excp) {
        // We don't want withInput() here.
        return redirect()->back()->withErrors([
            'alert' => $excp->errorInfo[2],
        ]);
    } catch(\Exception $excp) {
        return redirect()->back()->withErrors([
            'alert' => $excp->getMessage(),
        ]);
    }
}
```

#### 2.5) `ProductController::update()`

```php
function update(/* ... parameters list */): RedirectResponse {
    $product = $this->find($productCode);
    $this->authorize('update', $product);

    try {
        // ... Database manipulation process
    } catch(QueryException $excp) {
        return redirect()->back()->withInput()->withErrors([
            'alert' => $excp->errorInfo[2],
        ]);
    } catch(\Exception $excp) {
        return redirect()->back()->withInput()->withErrors([
            'alert' => $excp->getMessage(),
        ]);
    }
}
```

#### 2.6) `ProductController::delete()`

```php
function delete(/* ... parameters list */): RedirectResponse {
    $product = $this->find($productCode);
    $this->authorize('delete', $product);

    try {
        // ... Database manipulation process
    } catch(QueryException $excp) {
        // We don't want withInput() here.
        return redirect()->back()->withErrors([
            'alert' => $excp->errorInfo[2],
        ]);
    } catch(\Exception $excp) {
        return redirect()->back()->withErrors([
            'alert' => $excp->getMessage(),
        ]);
    }
}
```

> **สรุปกฎ `withInput()`**
> - ใช้ `withInput()` กับ method ที่มาจาก **ฟอร์มกรอกข้อมูล**: `create()`, `update()`
> - **ไม่ใช้** `withInput()` กับ `addShop()`, `removeShop()`, `delete()`

### Step 3 — Update template `products/create-form.blade.php`

เพิ่ม `old()` เพื่อดึง form-data ก่อนหน้า

#### 3.1) tag `<input />`

```blade
{{-- ... other tags --}}
<input type="text" name="code" value="{{ old('code') }}" required />
{{-- ... other tags --}}
<input type="text" name="name" value="{{ old('name') }}" required />
{{-- ... other tags --}}
<input type="number" name="price" value="{{ old('price') }}"
    step="any" required />
{{-- ... other tags --}}
```

#### 3.2) tag `<select>` — ใช้ `@selected` ร่วมกับ `old()`

```blade
{{-- ... other tags --}}
<select name="category" required>
    <option value="">-- Please select category --</option>
    @foreach($categories as $category)
        <option value="{{ $category->code }}"
            @selected($category->code === old('category'))>
            [{{ $category->code }}] {{ $category->name }}
        </option>
    @endforeach
</select>
{{-- ... other tags --}}
```

#### 3.3) tag `<textarea>`

```blade
{{-- ... other tags --}}
<textarea name="description" cols="80" rows="10"
    required>{{ old('description') }}</textarea>
{{-- ... other tags --}}
```

### Step 4 — Update template `products/update-form.blade.php`

เพิ่ม `old()` **พร้อม default value** (ค่าเดิมจาก model)

#### 4.1) tag `<input />`

```blade
{{-- ... other tags --}}
<input type="text" name="code" value="{{ old('code', $product->code) }}"
    required />
{{-- ... other tags --}}
<input type="text" name="name" value="{{ old('name', $product->name) }}"
    required />
{{-- ... other tags --}}
<input type="number" name="price" value="{{ old('price', $product->price) }}"
    step="any" required />
{{-- ... other tags --}}
```

#### 4.2) tag `<select>` — ใช้ `@selected` ร่วมกับ `old()`

```blade
{{-- ... other tags --}}
<select name="category" required>
    @foreach($categories as $category)
        <option value="{{ $category->code }}"
            @selected($category->code === old('category', $product->category->code)) >
            [{{ $category->code }}] {{ $category->name }}
        </option>
    @endforeach
</select>
{{-- ... other tags --}}
```

#### 4.3) tag `<textarea>`

```blade
{{-- ... other tags --}}
<textarea name="description" cols="80" rows="10"
    required>{{ old('description', $product->description) }}</textarea>
{{-- ... other tags --}}
```

---

## 5. ตัวอย่างผลลัพธ์ (Screenshots)

### 5.1 Example — original data

หน้า `localhost:8000/products/PD001/update` (Product: PD001)

| Field | Value |
|---|---|
| Code | PD001 |
| Name | Programming PHP |
| Category | [CT001] PHP |
| Price | 345.00 |
| Description | Why is PHP the most widely used programming language on the web? ... (คำอธิบายหนังสือ PHP) |

### 5.2 Example — update with duplicate key

- แก้ไขฟอร์มเป็น Code `PD002` (ซ้ำกับสินค้าอื่น), Name `Programming PHP vol. 2`, Category `[CT003] JavaScript`, Price `1345.00`, Description `Updated to work with JavaScript.`
- กด **Update** → กลับมาหน้าเดิมพร้อมข้อความ:

  > ⚠ Duplicate entry 'PD002' for key 'products_code_unique'

- ค่าทุกช่องในฟอร์มยังเป็นค่าที่ผู้ใช้กรอกล่าสุด (มาจาก `old()`) ไม่ใช่ค่าเดิมจากฐานข้อมูล

### 5.3 Example — missing attribute `required`

- สมมติว่า **ลืม** ใส่ attribute `required` ใน control `description`
- ผู้ใช้ลบค่า Description ให้ว่าง แล้วกด **Update** → กลับมาหน้าเดิมพร้อมข้อความ:

  > ⚠ Column 'description' cannot be null

---

## 6. References

- <https://www.php.net/manual/en/language.exceptions.php>
- <https://laravel.com/framework/docs/13.x/validation>

---

# Part B — Lab 13: Errors Handling

> **NOTE:** Use the project from example of week 13.

### 1. สร้าง `QueryException` handling ให้ `ShopController`

- `create()`
- `update()`
- `addProduct()`
- `removeProduct()`
- `delete()`

และเติม form-data ก่อนหน้ากลับเข้าฟอร์มถ้าจำเป็น (fill back the previous form-data if needed)

### 2. สร้าง `QueryException` handling ให้ `CategoryController`

- `create()`
- `update()`
- `addProduct()`
- `delete()`

และเติม form-data ก่อนหน้ากลับเข้าฟอร์มถ้าจำเป็น

### 3. สร้าง `QueryException` handling ให้ `UserController`

- `create()`
- `update()`
- `delete()`
- `selfUpdate()`

และเติม form-data ก่อนหน้ากลับเข้าฟอร์มถ้าจำเป็น
