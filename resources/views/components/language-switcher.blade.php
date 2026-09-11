@props([
    'compact' => false,
    'alternates' => [],
])

@php
    $activeLocale = $currentLocale ?? app()->getLocale();
    $nextLocale = $activeLocale === 'ar' ? 'en' : 'ar';

    // Icon-only button. Keep a readable title + aria-label for accessibility.
    $nextLabel = $nextLocale === 'ar' ? 'العربية' : 'English';
    $ariaLabel = $activeLocale === 'ar' ? 'Switch to English' : 'التبديل إلى العربية';
    $targetUrl = \App\Support\LocalizedUrl::switchTo($nextLocale, $alternates);
@endphp

<a
    class="btn btn-sm language-switcher {{ $compact ? 'language-switcher-compact' : '' }}"
    href="{{ $targetUrl }}"
    aria-label="{{ $ariaLabel }}"
    title="{{ $nextLabel }}"
>
    <i class="fas fa-globe" aria-hidden="true"></i>
    <span class="visually-hidden">{{ $nextLabel }}</span>
</a>
