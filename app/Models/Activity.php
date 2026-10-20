<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

// ไม่ใส่ category_id / organizer_id ใน Fillable → ต้องตั้งผ่าน associate() เท่านั้น
#[Fillable(['code', 'name', 'date', 'location', 'capacity', 'description'])]
class Activity extends Model
{
    protected function casts(): array
    {
        return [
            'date' => 'date',
            'capacity' => 'integer',
        ];
    }

    function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    function organizer(): BelongsTo
    {
        return $this->belongsTo(Organizer::class);
    }

    /**
     * ผู้สมัครกิจกรรม (ตารางกลาง activity_user)
     * ชื่อ relationship ต่างจากชื่อ model ได้ เพราะ Laravel ตั้งชื่อตาราง/FK จากชื่อ model
     */
    function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }
}
