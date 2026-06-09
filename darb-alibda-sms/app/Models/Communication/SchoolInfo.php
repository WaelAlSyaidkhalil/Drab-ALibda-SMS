<?php

namespace App\Models\Communication;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\Filterable;
use App\Models\Traits\HasAttachments;
use App\Models\Communication\Attachment;
use Illuminate\Support\Carbon;

/**
 * نموذج معلومات المدرسة
 * معلومات عامة عن المدرسة
 *
 * @property int $id
 * @property string $school_name      اسم المدرسة
 * @property string|null $email       بريد المدرسة
 * @property string|null $phone       هاتف المدرسة
 * @property string|null $address     عنوان المدرسة
 * @property string|null $description وصف المدرسة
 * @property string|null $website     موقع المدرسة
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class SchoolInfo extends Model
{
    use Filterable, HasAttachments;

    protected $table = 'school_info';

    protected $fillable = [
        'name',
        'description',
        'address',
        'phone',
        'email',
        'website',
    ];

    public function getSchoolNameAttribute(): string
    {
        return $this->name;
    }

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ────── Methods ──────

    /**
     * الحصول على معلومات المدرسة الوحيدة
     *
     * @return static|null
     */
    public static function getInfo(): static|null
    {
        return self::first();
    }

    /**
     * تحديث أو إنشاء معلومات المدرسة
     *
     * @param array $attributes
     * @param array $values
     * @return static
     */
    public static function updateOrCreate(array $attributes, array $values = []): static
    {
        $info = self::first();
        $data = array_merge($attributes, $values);

        if ($info) {
            $info->update($data);
            return $info;
        }

        return self::create($data);
    }
}
