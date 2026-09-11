<?php

return [
    'titles' => [
        'create' => 'إنشاء فئة',
        'edit' => 'تعديل فئة',
        'current_image' => 'الصورة الحالية',
    ],
    'fields' => [
        'parent_id' => 'الفئة الأم',
        'name' => 'اسم الفئة',
        'slug' => 'الرابط المختصر',
        'description' => 'الوصف',
        'image' => 'الصورة',
        'is_active' => 'نشط',
        'sort_order' => 'ترتيب العرض',
    ],
    'help' => [
        'parent_id' => 'اتركه فارغاً إذا لم يكن هناك فئة أم',
        'slug' => 'معرف URL فريد',
        'image' => 'قم بتحميل صورة الفئة (JPG/PNG/WebP). الحد الأقصى 3 ميجابايت',
        'image_current_prefix' => 'الصورة الحالية: ',
    ],
    'actions' => [
        'save' => 'حفظ',
        'remove' => 'إزالة',
        'confirm_remove' => 'هل أنت متأكد من حذف هذه الفئة؟',
    ],
    'toast' => [
        'saved' => 'تم حفظ الفئة بنجاح',
        'deleted' => 'تم حذف الفئة بنجاح',
        'delete_failed' => 'فشل حذف الفئة: :error',
        'upload_failed' => 'فشل تحميل الصورة: :error',
    ],
    'validation' => [
        'parent_self' => 'لا يمكن أن تكون الفئة والدة لنفسها.',
    ],
];
