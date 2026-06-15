# درب الإبداع - نظام إدارة مدرسة Laravel

## نظرة عامة

مشروع "درب الإبداع" هو نظام إدارة مدرسة مبني على Laravel 13 و PHP 8.3، مصمم لدعم ثلاثة أدوار رئيسية:

- **المعلم**
- **ولي الأمر**
- **الأدمن**

يهدف المشروع إلى فصل المسؤوليات عبر هيكلية طبقية واضحة مع دعم API احترافي، إدارة جداول دراسية، حضور، تقييمات، رسائل، وشؤون الطلاب والأبوين.

---

## هيكلية المشروع الخلفية

### البنية الأساسية للمجلد `app/`

```
app/
├── Aspects/         # الجوانب المقطعية (AOP) مثل تسجيل الأداء والتدقيق
├── Contracts/       # واجهات (Interfaces) لفصل الطبقات وتسهيل التبديل
├── Events/          # تعريف الأحداث المنطقية
├── Listeners/       # التقاط الأحداث وتنفيذ المهام الفرعية
├── Http/
│   ├── Controllers/
│   │   ├── Admin/
│   │   ├── Parent/
│   │   └── Teacher/
│   ├── Middleware/
│   │   ├── Admin/
│   │   ├── Parent/
│   │   └── Teacher/
│   └── Requests/
│       ├── Admin/
│       ├── Parent/
│       └── Teacher/
├── Models/
├── Notifications/
├── Observers/
├── Policies/
├── Repositories/
├── Services/
├── Exceptions/
└── Traits/
```

### مبدأ الفصل بين الاهتمامات

- **Controllers**: استقبال الطلبات وتحويلها إلى خدمات.
- **Services**: تنفيذ قواعد العمل (Business Logic) وعمليات التنسيق.
- **Repositories**: الوصول إلى البيانات وتعاملات قاعدة البيانات.
- **Models**: تمثيل الكيانات والعلاقات باستخدام Eloquent.
- **Policies**: إدارة صلاحية الوصول لكل دور.
- **Observers / Events / Listeners**: فصل أحداث النظام، الإشعارات، ووظائف بعد الحفظ.
- **Traits**: وظائف مشتركة مثل `ApiResponse` لتوحيد ردود الـ API.

---

## المجلدات الخاصة بالأدوار

تم تنظيم الطبقات حسب الدور الأساسي لكل حساب لتسهيل الفصل بين الصلاحيات:

- `app/Http/Controllers/Teacher`
- `app/Http/Controllers/Parent`
- `app/Http/Controllers/Admin`

ونفس التقسيم موجود في:

- `app/Http/Middleware`
- `app/Http/Requests`
- `app/Services`
- `app/Repositories`
- `app/Policies`
- `app/Notifications`
- `app/Observers`
- `app/Events`
- `app/Listeners`

هذا التنظيم يساعد على:

- كتابة كود نظيف ومنظم.
- تحسين الصيانة والتطوير مع نمو المشروع.
- فصل واجهات المستخدم المختلفة عن منطق المنصة.

---

## قاعدة البيانات والجداول الأساسية

### 1. `roles`
- تحتوي على الأدوار: `admin`, `teacher`, `student`, `parent`.
- كل مستخدم يرتبط بدور واحد.

### 2. `users`
- الحسابات العامّة للنظام.
- الحقول الرئيسية: `name`, `email`, `phone`, `avatar`, `password`, `role_id`, `fcm_token`, `is_active`.
- `users.phone` هو رقم الجوال الرئيسي المستخدم للدخول والتواصل.
- `users.name` هو الاسم الكامل العام للحساب.
- علاقة رئيسية مع `roles` و `students` و `teachers`.

### 3. `students`
- يمثل بيانات الطالب الشخصية وبيانات ولي الأمر.
- يحتفظ المعلومات التعليمية والهوية الخاصة بالطالب، بينما يبقى `users` هو مصدر بيانات الاتصال وبيانات الحساب.
- الحقول: `user_id`, `parent_id`, `first_name`, `last_name`, `national_id`, `registry_number`, `birth_date`, `gender`.
- العلاقات:
  - `belongsTo(User)`
  - `belongsTo(User, 'parent_id')`
  - `hasMany(StudentEnrollment)`

### 4. `teachers`
- يمثل بيانات المعلم الخاصة بالدور الوظيفي.
- يحفظ تفاصيل المعلم المنفصلة عن بيانات الدخول العامة في `users`.
- الحقول: `user_id`, `first_name`, `last_name`, `national_id`, `registry_number`, `employee_number`, `specialization`, `experience_years`, `phone_alt`, وغيرها.
- `users.phone` هو رقم الهاتف الرئيسي، بينما `teachers.phone_alt` هو الرقم الثانوي الإضافي.
- العلاقات:
  - `belongsTo(User)`
  - `hasMany(Schedule)`

### 5. `classes`
- يمثّل الصفوف العامة (مثل الأول، الثاني، الثالث).
- العلاقة مع `sections`.

### 6. `sections`
- تمثّل الشعب داخل الصف.
- الحقول: `class_id`, `name`, `capacity`.
- العلاقات:
  - `belongsTo(SchoolClass)`
  - `hasMany(StudentEnrollment)`
  - `hasMany(Schedule)`

### 7. `subjects`
- تمثّل المواد الدراسية.
- الحقول: `name`, `code`, `type`, `full_mark`, `pass_mark`.
- العلاقات:
  - `hasMany(SubjectComponent)`
  - `belongsToMany(SchoolClass)` من خلال `class_subject`
  - `belongsToMany(Term)` من خلال `term_subject`

### 8. `terms`
- تمثّل الفصول الدراسية السنوية.
- الحقول: `name`, `academic_year`, `start_date`, `end_date`, `is_active`.
- علاقات مع `Schedule` و `term_subject`.

### 9. `time_slots`
- تمثّل أوقات الحصص الثابتة في المدرسة.
- الحقول: `period_number`, `name`, `start_time`, `end_time`.
- علاقة `hasMany(Schedule)`.

### 10. `schedules`
- تمثّل الجدول الدراسي الفعلي.
- العلاقات الرئيسية:
  - `section_id` → `sections`
  - `subject_id` → `subjects`
  - `teacher_id` → `teachers`
  - `term_id` → `terms`
  - `time_slot_id` → `time_slots`
- تقييدات مهمة:
  - يمنع أن يكون المعلم في حصتين بنفس الوقت.
  - يمنع أن تكون الشعبة في حصتين بنفس الوقت.

### 11. `attendance`
- يسجّل حضور وغياب الطلاب لكل حصة.
- الحقول: `student_id`, `schedule_id`, `date`, `status`.
- يضمن قيد فريد لكل طالب في نفس الجدول والتاريخ.

### 12. `student_enrollments`
- يسجل انضمام الطالب لشعبة وسنة دراسية.
- الحقول: `student_id`, `section_id`, `academic_year`, `status`, `final_result`, `final_average`.
- يسمح بتتبع الطالب عبر السنوات الدراسية.

### 13. `student_marks`
- علامات الطالب لكل مكوّن من مكونات المادة.
- الحقول: `enrollment_id`, `subject_id`, `component_id`, `term_id`, `mark`.
- يضمن قيد فريد لكل تسجيل/مكوّن/فصل.

### 14. `student_subject_results`
- نتائج الطالب النهائية لكل مادة خلال السنة.
- الحقول: `enrollment_id`, `subject_id`, `term1_mark`, `term2_mark`, `yearly_mark`, `result`.
- يعتمد على `student_marks` لحساب العلامة السنوية.

### 15. `class_subject`
- جدول وصل يربط الصفوف بالمواد.
- يحدد المواد المتاحة لكل صف.

### 16. `term_subject`
- جدول وصل يربط الفصول بالمواد.
- يحدد المواد النشطة في كل فصل.

### 17. الجداول الاتصالية
- `conversations` و `messages` للرسائل الداخلية.
- `news` لنشر الإعلانات.
- `complaints` و `suggestions` لطلبات التواصل والتغذية الراجعة.
- `attachments` لتخزين المرفقات العامة مثل الصور والفيديوهات والمستندات المرتبطة بأي سجل يدعم المرفقات.

---

## العلاقات الرئيسية في النظام

### علاقات المستخدم والكيانات الأكاديمية

- `User` → `Role` (belongsTo)
- `User` → `Student` (hasOne)
- `User` → `Teacher` (hasOne)
- `User` → `Student` (hasMany children عبر `parent_id`)
- `Student` → `StudentEnrollment` (hasMany)
- `StudentEnrollment` → `Section` (belongsTo)

### علاقات الجدول الدراسي

- `Schedule` → `Section`, `Subject`, `Teacher`, `Term`, `TimeSlot`
- `Schedule` → `Attendance` (hasMany)
- `Attendance` → `Student`, `Schedule`

### علاقات التقييم

- `StudentEnrollment` → `StudentMark`, `StudentSubjectResult`
- `StudentMark` → `SubjectComponent`, `Term`, `Subject`
- `StudentSubjectResult` → `Subject`, `StudentEnrollment`

### علاقات المقررات والفصول

- `Subject` ↔ `SchoolClass` عبر `class_subject`
- `Subject` ↔ `Term` عبر `term_subject`

---

## الطبقات التكنولوجية والملفات الأساسية

### `app/Traits/ApiResponse.php`
- توحيد ردود JSON لجميع الـ API.
- يدعم رسائل نجاح، خطأ، إنشاء، تعديل، حذف، صفحات.

### `app/Http/Controllers/Controller.php`
- يُفترض أن يستخدم `ApiResponse` trait.
- يُعد نقطة البداية لجميع الكنترولرات.

### `app/Repositories/`
- طبقة للتعامل مع قواعد البيانات.
- تتيح فصل Eloquent عن منطق الخدمة.

### `app/Services/`
- مكان تنفيذ قواعد العمل الأساسية.
- تنسيق نتائج الـ repositories وإطلاق الأحداث.

### `app/Policies/`
- إدارة صلاحيات المستخدمين.
- يضمن أن المعلم/الأهل/الأدمن لديهم صلاحيات واضحة.

### `app/Events/` و `app/Listeners/`
- لفصل المعالجة بعد وقوع الأحداث.
- مثل إرسال إشعار عند تسجيل غياب أو إضافة خبر.

---

## إعداد المشروع وتشغيله

### تثبيت الحزم

```bash
composer install
npm install
```

### نسخ ملف البيئة

```bash
cp .env.example .env
php artisan key:generate
```

### تشغيل المهاجرات والبيانات

```bash
php artisan migrate
php artisan db:seed
```

### تشغيل السيرفر المحلي

```bash
php artisan serve
```

---

## الـ Seeders الحالية

- `RoleSeeder`
- `AdminSeeder`
- `TeacherSeeder`
- `ParentSeeder`
- `StudentSeeder`

هذه الـ seeders تنشئ:

- الأدوار الأساسية.
- حساب مسؤول.
- حسابات معلمين ببيانات شخصية ومهنية مفصّلة (`national_id`, `registry_number`, `specialization`, `phone_alt`, إلخ).
- حسابات أولياء أمور.
- حسابات طلاب وربطهم بآبائهم.

> ملاحظة: يُستخدم جدول `users` كمصدر رئيسي لبيانات حساب الدخول العامة مثل `name` و`phone`، بينما تخزن جداول `teachers` و`students` البيانات المتخصّصة لكل دور.

---

## التوسع والتحسينات المستقبلية

### مميزات يمكن إضافتها بسهولة

- `Laravel Fortify` + `Sanctum` للمصادقة والـ API.
- `Laravel Scout` مع `Meilisearch` أو `Typesense` لتحسين البحث.
- `sentry/sentry-laravel` لمراقبة الأخطاء.
- `laravel-notification-channels/telegram` لإرسال تنبيهات فورية.
- `Laravel Telescope` و `Debugbar` لتطوير ومراقبة الأداء.
- `Husky` + `lint-staged` لحماية جودة الكود قبل الالتزام.

### لماذا هذه الهندسة قوية؟

- تفصل بين منطق العمل والوصول إلى البيانات والتحكم.
- تمنع تداخل الصلاحيات عبر تقسيم المجلدات حسب الدور.
- تعطي مرونة للتوسع المستقبلي دون كسر الكود الموجود.
- تسمح بإضافة أتمتة مثل الأحداث والإشعارات بسهولة.

---

## توصيات تطبيقية

- ابدأ بتركيب `Fortify` + `Sanctum` إذا كنت تريد API و SPA.
- استخدم `ApiResponse` لتوحيد ردود الـ JSON.
- نفّذ `Policies` لكل وظيفة حرجة مثل إدخال الدرجات أو إضافة الحضور.
- اعتمد على `Repositories` عند الحاجة لتغيير مصدر البيانات لاحقاً.
- افصل البيانات المجمعة (النتائج، الحضور، الجدول) في `Services`.

---

## ملاحظات إضافية

- يحتوي المشروع على مجلدات جديدة تم إنشاؤها لدعم التقسيم الطبقي والوظائف الخاصة بالأدوار.
- لا يُنصح بإضافة `app/Http/Interface`، بل استخدم `app/Contracts` و `app/Http/Resources` عند الحاجة.

---

## ترخيص

هذا المشروع يعتمد على Laravel ومفتوح المصدر بموجب ترخيص MIT.





أراجع بنية السيدرز الحالية والجداول الأساسية للمراحل والصفوف والمواد حتى أضع خطة دقيقة متوافقة مع مشروعك.

Read [](file:///c%3A/Users/PC/Documents/Drab-ALibda-SMS/darb-alibda-sms/database/seeders)

Searched for regex `class .*Seeder|public function run\(|use App\\Models` (`**/database/seeders/*.php`), 26 results

Searched for regex `class SchoolClass|class Section|class Subject|class Schedule|class TimeSlot|class Teacher` (`**/app/Models/**/*.php`), 7 results

أراجع الحقول الحالية ومحتوى سيدرز المعلمين والطلاب لأضع خطة واضحة تناسب الهيكل الحالي.

Read [](file:///c%3A/Users/PC/Documents/Drab-ALibda-SMS/darb-alibda-sms/database/seeders/TeacherSeeder.php)

Read [](file:///c%3A/Users/PC/Documents/Drab-ALibda-SMS/darb-alibda-sms/database/seeders/StudentSeeder.php)

Searched for regex `create_.*classes|create_.*sections|create_.*subjects|create_.*schedules|create_.*time_slots|create_.*school_info` (`**/database/migrations/*.php`), no results

Read [](file:///c%3A/Users/PC/Documents/Drab-ALibda-SMS/darb-alibda-sms/database/migrations)

Read [](file:///c%3A/Users/PC/Documents/Drab-ALibda-SMS/darb-alibda-sms/database/migrations/2026_05_06_112830_create_classes_table.php)

Read [](file:///c%3A/Users/PC/Documents/Drab-ALibda-SMS/darb-alibda-sms/database/migrations/2026_05_06_112835_create_sections_table.php)

Read [](file:///c%3A/Users/PC/Documents/Drab-ALibda-SMS/darb-alibda-sms/database/migrations/2026_05_06_113008_create_subjects_table.php)

Read [](file:///c%3A/Users/PC/Documents/Drab-ALibda-SMS/darb-alibda-sms/database/migrations/2026_05_06_113038_create_schedules_table.php)

Read [](file:///c%3A/Users/PC/Documents/Drab-ALibda-SMS/darb-alibda-sms/database/migrations/2026_05_10_105927_create_class_subject_table.php)

Read [](file:///c%3A/Users/PC/Documents/Drab-ALibda-SMS/darb-alibda-sms/database/migrations/2026_05_10_110736_create_term_subject_table.php)

Read [](file:///c%3A/Users/PC/Documents/Drab-ALibda-SMS/darb-alibda-sms/database/migrations/2026_05_06_112946_create_terms_table.php)

Read [](file:///c%3A/Users/PC/Documents/Drab-ALibda-SMS/darb-alibda-sms/database/seeders/RoleSeeder.php)

## ما فهمته

أنت تريد مشروع Laravel متكامل يعتمد على:
- PHP حديثة و Eloquent ORM
- هيكل MVC الصحيح
- بيانات تمهيدية كاملة لكل الجداول
- منهج دراسي بنظام سوريا
- توزيع الصفوف والمراحل كما يلي:
  - المرحلة الابتدائية: الصفوف 1-6
  - المرحلة الإعدادية: الصفوف 7-9
  - المرحلة الثانوية: الصفوف 10-12
- لكل صف 3 شعب
- لكل شعبة 10 طلاب
- للمراحل الابتدائية: كل شعبة لها أستاذ واحد يُدرّس كل المواد
- للمراحل الإعدادية والثانوية: لكل مادة معلم متخصص
- إضافة الجداول الدراسية لكل شعبة مع منع التضارب

---

## الملخص

حالياً المشروع فيه:
- جداول `classes`, `sections`, `subjects`, `schedules`, `teachers`, `students`, `terms`
- يوجد بعض السيدرز الأساسية مثل `TeacherSeeder`, `StudentSeeder`, `RoleSeeder`
- لكن لم يتم تهيئة قاعدة البيانات بالكامل وفق نظام الصفوف والشعب والمواد السوري

---

## الخطة التي سأقوم بها

1. **فحص النموذج والهيكل الحالي**
   - أراجع نماذج `Class`, `Section`, `Subject`, `Schedule`, `Term`
   - أراجع جداول الربط مثل `class_subject`, `term_subject`

2. **إنشاء أو تعديل السيدرز التالية**
   - `ClassSeeder`
     - ينشئ الصفوف 1-12
     - يعيّن المراحل: ابتدائي / إعدادي / ثانوي
   - `SectionSeeder`
     - ينشئ 3 شعب لكل صف: أ، ب، ج
     - يضع السعة لكل شعبة على الأقل 10 طلاب
   - `SubjectSeeder`
     - يضيف جميع المواد وفق المنهج السوري لكل مرحلة:
       - ابتدائي (لغة عربية، رياضيات، دين، علوم، تربية وطنية، إنجليزي، إلخ)
       - إعدادي (عربي، رياضيات، علوم، إنجليزي، تاريخ، جغرافيا، دين، علوم الشعبة..)
       - ثانوي (مواد فرع العلوم، الأدب، الشرعي... حسب التخصص)
   - `TermSeeder`
     - ينشئ فصول دراسية مثل الفصل الأول والفصل الثاني لسنة أكاديمية
   - `TeacherSeeder`
     - يضيف معلمين
     - يربط فصل ابتدائي كامل لكل شعبة بمعلم واحد
     - يربط في الإعدادي والثانوي معلمين لكل مادة
   - `StudentSeeder`
     - ينشئ 10 طلاب لكل شعبة
     - يربطهم بالشعبة وولي الأمر إذا كان موجوداً
   - `ScheduleSeeder`
     - يبني جداول لكل شعبة
     - يتجنب تضارب المعلم أو الشعبة عبر فهارس `unique`
     - يستخدم `time_slots` و `terms`

3. **تعبئة جداول الربط**
   - `class_subject`
   - `term_subject`
   - احتمالاً `schedules` لتوزيع المواد حسب الشعبة والفترة

4. **اتباع أفضل الممارسات**
   - استخدام `updateOrCreate` و Eloquent relationships
   - تجنب الكتابة المباشرة في قواعد البيانات داخل الكود غير المخصص للسيدرز
   - فصل منطق التهيئة في Seeder مستقل لكل كيان
   - احترام اسماء الحقول والموديلات الموجودة

---

## النتيجة المتوقعة

بعد التنفيذ:
- قاعدة بيانات مملوءة ببيانات واقعية للمدرسة
- كل الصفوف والمراحل معرفة
- شعب كاملة لكل صف
- طلاب لكل شعبة
- معلمين مرتبطين بشكل صحيح
- جدول دراسي منظم لمنع التضارب

---

## ما أحتاجه منك الآن

إذا كنت موافقاً على هذه الخطة:
- سأبدأ بإنشاء السيدرز الجديدة وتنظيم البيانات
- وأجعلها تعمل مع هيكل المشروع الحالي
