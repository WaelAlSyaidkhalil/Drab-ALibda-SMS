<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * يحوّل كلمات المرور المخزّنة كنص عادي إلى تشفير bcrypt.
 *
 * يحافظ على كلمة مرور كل مستخدم كما هي، فقط يخزّنها مشفّرة،
 * حتى لا يفقد أي مستخدم إمكانية تسجيل الدخول.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')
            ->orderBy('id')
            ->chunkById(200, function ($users) {
                foreach ($users as $user) {
                    if (blank($user->password) || Hash::isHashed($user->password)) {
                        continue;
                    }

                    DB::table('users')
                        ->where('id', $user->id)
                        ->update(['password' => Hash::make($user->password)]);
                }
            });
    }

    public function down(): void
    {
        // لا يمكن التراجع — لا يمكن استرجاع النص الأصلي من قيمة مشفّرة.
    }
};
