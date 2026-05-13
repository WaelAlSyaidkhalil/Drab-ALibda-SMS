<?php

namespace App\Models\Communication;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\Filterable;
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
 * @property string|null $logo_url    رابط الشعار
 * @property string|null $about       نبذة عن المدرسة
 * @property string|null $vision      الرؤية
 * @property string|null $mission     الرسالة
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class SchoolInfo extends Model
{
    use Filterable;

    protected $fillable = [
        'school_name',
        'email',
        'phone',
        'address',
        'logo_url',
        'about',
        'vision',
        'mission',
    ];

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
     * @param array $data
     * @return static
     */
    public static function updateOrCreate(array $data): static
    {
        $info = self::first();

        if ($info) {
            $info->update($data);
            return $info;
        }

        return self::create($data);
    }
}
