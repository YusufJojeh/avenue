<?php

return [
    'titles' => [
        'create' => 'إنشاء عرض',
        'edit' => 'تعديل عرض',
        'current_banner' => 'البانر الحالي',
    ],
    'fields' => [
        'title' => 'العنوان',
        'description' => 'الوصف',
        'type' => 'النوع',
        'value' => 'القيمة',
        'starts_at' => 'يبدأ في',
        'ends_at' => 'ينتهي في',
        'is_active' => 'نشط',
        'banner' => 'البانر',
    ],
    'help' => [
        'type' => 'إذا كان شحن مجاني، سيتم تجاهل القيمة.',
        'value' => 'للنسبة المئوية: 1-100. للمبلغ الثابت: >= 0',
        'banner' => 'قم بتحميل البانر (JPG/PNG/WebP). الحد الأقصى 5 ميجابايت',
        'banner_current_prefix' => 'الحالي: ',
    ],
    'types' => [
        'percent' => 'نسبة مئوية (%)',
        'fixed' => 'مبلغ ثابت',
        'free_shipping' => 'شحن مجاني',
    ],
    'actions' => [
        'save' => 'حفظ',
        'remove' => 'إزالة',
        'confirm_remove' => 'حذف هذا العرض؟',
    ],
    'toast' => [
        'saved' => 'تم حفظ العرض بنجاح',
        'deleted' => 'تم حذف العرض بنجاح',
        'delete_failed' => 'فشل حذف العرض: :error',
        'upload_failed' => 'فشل تحميل البانر: :error',
    ],
];
