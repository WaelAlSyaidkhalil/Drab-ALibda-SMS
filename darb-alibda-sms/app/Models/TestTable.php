<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * جدول اختباري للتحقق من عمل خط النشر
 *
 * @property int $id
 * @property string $name           اسم السجل الاختباري
 * @property string|null $description  وصف اختياري
 * @property bool $is_active        هل السجل مفعّل
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class TestTable extends Model
{
    protected $table = 'test_tables';

    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
