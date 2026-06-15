# وثائق واجهات برمجة التطبيقات (API) لمشروع `darb-alibda-sms`

ملف التوثيق هذا مخصص لمطور Flutter لفهم كل مسار API متاح في المشروع، مع شرح المدخلات والمخرجات والملاحظات المهمة.

---

## الشكل العام للاستجابة

جميع الاستجابات تعود بصيغة JSON عامة:

- `status`: `success` أو `error`
- `message`: رسالة نصية قصيرة
- `data`: محتوى الاستجابة عند النجاح

مثال نجاح:

```json
{
  "status": "success",
  "message": "تم جلب البيانات بنجاح",
  "data": { ... }
}
```

مثال خطأ:

```json
{
  "status": "error",
  "message": "بيانات خاطئة أو حدث خطأ"
}
```

> جميع المسارات التي تتطلب مصادقة تحتاج إلى ترويسة HTTP:
>
> `Authorization: Bearer {token}`
>
> يتم الحصول على `token` من استجابة تسجيل الدخول.

---

## 1. تسجيل دخول المعلم

- المسار: `POST /api/teacher/login`
- النوع: `public`

### المدخلات

```json
{
  "phone": "+9665XXXXXXXX",
  "password": "password123",
  "fcm_token": "optional-device-token",
  "remember": true
}
```

### المخرجات

- `user`: بيانات المستخدم
- `token`: رمز المصادقة
- `token_type`: `Bearer`

مثال:

```json
{
  "status": "success",
  "message": "تم تسجيل الدخول بنجاح.",
  "data": {
    "user": {
      "id": 2,
      "name": "أحمد علي",
      "email": "teacher@example.com",
      "phone": "+9665...",
      "role": "teacher",
      "is_active": true,
      "fcm_token": null
    },
    "token": "32|...",
    "token_type": "Bearer"
  }
}
```

### ملاحظات

- `phone` يجب أن يكون على شكل رقم صحيح مع أو بدون `+`.
- `password` لا يقبل مسافات.

---

## 2. رسالة الدعم

- المسار: `GET /api/teacher/support`
- النوع: `public`

### المخرجات

بيانات الدعم العامة مثل اسم المدرسة ورقم الهاتف والإيميل.

---

## 3. بيانات المستخدم الحالي

- المسار: `GET /api/teacher/me`
- النوع: يحتاج مصادقة

### المخرجات

- `user`: الملف الشخصي للمستخدم مع بيانات المعلم، المواد، الشعب، والجدول.

### ملاحظات

- تستخدم هذه النهاية لعرض صفحة الملف الشخصي في التطبيق.

---

## 4. تحديث الملف الشخصي

- المسار: `POST /api/teacher/profile`
- النوع: يحتاج مصادقة

### المدخلات

- `email` (اختياري)
- `avatar` (اختياري، صورة)
- `address` (اختياري)
- `phone_alt` (اختياري)
- `experience_years` (اختياري)

> يفضل إرسال الطلب بنسق `multipart/form-data` عندما يحتوي على `avatar`.

### المخرجات

- `user`: الملف المحدث.

---

## 5. تسجيل الخروج

- المسار: `POST /api/teacher/logout`
- النوع: يحتاج مصادقة

### المخرجات

- لا يوجد بيانات إضافية، فقط رسالة نجاح.

---

## 6. لوحة المعلم

- المسار: `GET /api/teacher/dashboard`
- النوع: يحتاج مصادقة

### المخرجات

تعرض ملخص نقاط رئيسية للمعلم مثل:
- عدد الحضور اليومي
- عدد الطلاب الناشطين
- عدد طلبات تبرير الغياب
- عدد الملاحظات أو الأخبار اليوم

> يعتمد على الخدمة في المشروع لإعادة بيانات مخصّصة.

---

## 7. طلبات تبرير الغياب

### 7.1 جلب الطلبات

- المسار: `GET /api/teacher/absence-justifications`
- النوع: يحتاج مصادقة

### المخرجات

قائمة طلبات الغياب الخاصة بطلاب المعلم، وكل عنصر يحتوي على:
- `id`
- بيانات الطالب
- بيانات ولي الأمر
- `absence_date`
- `reason`
- `status`
- `review_note`
- `reviewed_by`
- `reviewed_at`
- `attachments`
- `created_at`

### ملاحظات

- يعرض فقط الطلبات الخاصة بطلاب الشُعب التي يدرسها المعلم.

### 7.2 تحديث طلب الغياب

- المسار: `POST /api/teacher/absence-justifications/update/{justificationId}`
- النوع: يحتاج مصادقة

### المدخلات

```json
{
  "status": "pending|approved|rejected",
  "review_note": "optional note"
}
```

### المخرجات

- رسالة نجاح فقط.

### 7.3 حذف طلب الغياب

- المسار: `POST /api/teacher/absence-justifications/destroy/{justificationId}`
- النوع: يحتاج مصادقة

### المخرجات

- رسالة نجاح فقط.

---

## 8. البرنامج الدراسي

### 8.1 برنامج اليوم

- المسار: `GET /api/teacher/schedule/today`
- النوع: يحتاج مصادقة

### المخرجات

قائمة الحصص لهذا المعلم في اليوم الحالي، كل عنصر يحتوي على:
- `id`
- `subject`
- `section`
- `class`
- `day`
- `time_slot` (مع `id`, `period_number`, `name`, `start_time`, `end_time`)
- `term`

### 8.2 برنامج الأسبوع

- المسار: `GET /api/teacher/schedule/week`
- النوع: يحتاج مصادقة

### المخرجات

جدول الأسبوع مجمّع حسب اليوم، على سبيل المثال:

```json
{
  "الأحد": [ ... ],
  "الاثنين": [ ... ]
}
```

كل عنصر يحتوي على نفس الحقول المذكورة في برنامج اليوم.

---

## 9. حضور الشعبة والطلاب

### 9.1 جلب الشعب والطلاب

- المسار: `GET /api/teacher/sections-with-students`
- النوع: يحتاج مصادقة

### Query Parameters

- `class_id` (اختياري)
- `section_id` (اختياري)
- `date` (اختياري، صيغة `YYYY-MM-DD`)

### المخرجات

كل عنصر شعبة يحتوي على:
- `section_id`
- `section_name`
- `section_full_name`
- `class_id`
- `class_name`
- `total_students`
- `attendance`:
  - `date`
  - `present`
  - `absent`
  - `late`
  - `excused`
  - `percentage`
- `schedules`: قائمة الحصص للأستاذ في هذه الشعبة
- `students`: قائمة الطلاب مع:
  - `student_id`
  - `enrollment_id`
  - `registry_number`
  - `full_name`
  - `first_name`
  - `last_name`
  - `email`
  - `phone`
  - `gender`
  - `birth_date`
  - `parent`
  - `attendance_status`

### ملاحظات

- النتائج مهيأة لتسهيل الفلاتر في تطبيق Flutter.
- إذا لم يرسل `date`، يستخدم التاريخ الحالي.

### 9.2 تحديث الحضور والغياب دفعة واحدة

- المسار: `POST /api/teacher/attendance/sections/{sectionId}/batch-update`
- النوع: يحتاج مصادقة

### المدخلات

```json
{
  "date": "2026-06-15",
  "schedule_id": 1,          // اختياري لتحديد الحصة إذا كانت الشعبة لها أكثر من حصة
  "students": [
    {
      "student_id": 10,
      "status": "present|absent|late|excused"
    },
    {
      "student_id": 12,
      "status": "absent",
      "reason": "سبب الغياب"
    }
  ]
}
```

### المخرجات

- `section_id`
- `date`
- `counts`:
  - `present`
  - `absent`
  - `late`
  - `excused`
  - `total`

### ملاحظات

- كل الطلاب غير المرسلة لهم حالة يتم افتراضهم `present`.
- `reason` متاح للحالات التي تحتاج وصفاً.
- إذا كانت الشعبة تحتوي على أكثر من جدول في نفس اليوم، استخدم `schedule_id` لتحديد الحصة المطلوبة.

---

## 10. الأخبار

### 10.1 جلب كل الأخبار

- المسار: `GET /api/teacher/news`
- النوع: يحتاج مصادقة

### المخرجات

قائمة الأخبار مع حقل `is_read` لكل خبر.

### 10.2 عدد الأخبار غير المقروءة

- المسار: `GET /api/teacher/news/unread-count`
- النوع: يحتاج مصادقة

### المخرجات

```json
{
  "status": "success",
  "message": "تم جلب عدد الأخبار غير المقروءة",
  "data": {
    "unread_count": 5
  }
}
```

### 10.3 تعليم خبر كمقروء

- المسار: `POST /api/teacher/news/{newsId}/mark-as-read`
- النوع: يحتاج مصادقة

### المخرجات

- رسالة نجاح فقط.

### 10.4 تعليم كل الأخبار كمقروءة

- المسار: `POST /api/teacher/news/mark-all-as-read`
- النوع: يحتاج مصادقة

### المخرجات

- `marked_count`: عدد الأخبار التي تم تعليمها كمقروءة.

---

## ملاحظة عامة

- إذا أردت تنفيذ أي طلب يحتاج تحميل ملفات (`avatar`)، أرسل الطلب كـ `multipart/form-data`.
- إذا كان هناك خطأ في المدخلات، عادة ستعود الاستجابة بحالة HTTP `422` مع تفاصيل الحقول.
- جميع المسارات تحت `/api/teacher/*` تستخدم مصادقة `sanctum` ما عدا `login` و `support`.

---

## كيف يستخدم Flutter هذه الوثائق؟

1. نفذ طلب `POST /api/teacher/login` لأخذ `token`.
2. ضَع `Authorization: Bearer {token}` في كل طلب محمي.
3. اعتمد على الحقول `status`, `message`, `data` للتعامل مع النجاح أو الخطأ.
4. استخدم `sections-with-students` لعرض الطلاب حسب الشعبة وحساب الحضور من `attendance`.
5. نفّذ `batch-update` مع مصفوفة الطلاب فقط لتحديث الحضور دفعة واحدة.

---

## نهاية الوثيقة

هذه الوثيقة تغطي جميع الـ API المعرفة في ملف `routes/api.php` الحالي.
إذا احتجت توسيعها لاحقاً لأي مسارات جديدة، يمكن تحديثها بنفس النمط.