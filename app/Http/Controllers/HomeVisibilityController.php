<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class HomeVisibilityController extends Controller
{
    /**
     * Get visibility settings for all sections
     */
    public function getVisibilitySettings(): array
    {
        return Cache::remember('home.visibility.settings', 3600, function () {
            return [
                'hero'       => (bool) Setting::get('home.show.hero', true),
                'slider'     => (bool) Setting::get('home.show.slider', true),
                'offers'     => (bool) Setting::get('home.show.offers', true),
                'categories' => (bool) Setting::get('home.show.categories', true),
                'special'    => (bool) Setting::get('home.show.special', true),
                'latest'     => (bool) Setting::get('home.show.latest', true),
                'external'   => (bool) Setting::get('home.show.external', true),
            ];
        });
    }

    /**
     * Check if a specific section should be visible
     */
    public function isSectionVisible(string $section): bool
    {
        $settings = $this->getVisibilitySettings();
        return $settings[$section] ?? false;
    }

    /**
     * Get section limits
     */
    public function getSectionLimits(): array
    {
        return Cache::remember('home.section.limits', 3600, function () {
            $limitsRaw = Setting::get('home.limits');
            $limitsArr = is_array($limitsRaw) ? $limitsRaw : (json_decode($limitsRaw ?? '', true) ?: []);
            
            return array_merge([
                'slider'     => 6,
                'categories' => 8,
                'special'    => 8,
                'latest'     => 12,
                'external'   => 8,
            ], $limitsArr);
        });
    }

    /**
     * Get limit for a specific section
     */
    public function getSectionLimit(string $section): int
    {
        $limits = $this->getSectionLimits();
        return $limits[$section] ?? 8;
    }

    /**
     * Clear visibility cache
     */
    public function clearCache(): void
    {
        Cache::forget('home.visibility.settings');
        Cache::forget('home.section.limits');
    }

    /**
     * Update visibility settings
     */
    public function updateVisibility(Request $request): array
    {
        $this->authorizeSettings($request);

        $data = $request->validate([
            'section' => 'required|string|in:hero,slider,offers,categories,special,latest,external',
            'visible' => 'required|boolean',
        ]);

        $section = $data['section'];
        $visible = $data['visible'];

        Setting::set("home.show.$section", $visible);
        
        // Clear cache to reflect changes immediately
        $this->clearCache();

        return [
            'success' => true,
            'message' => "Section '$section' visibility updated to " . ($visible ? 'visible' : 'hidden'),
            'section' => $section,
            'visible' => $visible,
        ];
    }

    /**
     * Update section limits
     */
    public function updateLimits(Request $request): array
    {
        $this->authorizeSettings($request);

        $data = $request->validate([
            'section' => 'required|string|in:slider,categories,special,latest,external',
            'limit'   => 'required|integer|min:1|max:50',
        ]);

        $section = $data['section'];
        $limit = $data['limit'];

        // Get current limits
        $limits = $this->getSectionLimits();
        $limits[$section] = $limit;

        // Save updated limits
        Setting::set('home.limits', $limits);
        
        // Clear cache to reflect changes immediately
        $this->clearCache();

        return [
            'success' => true,
            'message' => "Section '$section' limit updated to $limit",
            'section' => $section,
            'limit' => $limit,
        ];
    }

    /**
     * Get all settings for API response
     */
    public function getAllSettings(): array
    {
        return [
            'visibility' => $this->getVisibilitySettings(),
            'limits' => $this->getSectionLimits(),
            'site_name' => Setting::get('site.name', 'MyStore'),
            'theme_mode' => Setting::get('theme.mode', 'auto'),
        ];
    }

    /**
     * Bulk update visibility settings
     */
    public function bulkUpdateVisibility(Request $request): array
    {
        $this->authorizeSettings($request);

        $data = $request->validate([
            'sections' => 'required|array',
            'sections.*' => 'string|in:hero,slider,offers,categories,special,latest,external',
            'visible' => 'required|boolean',
        ]);

        $sections = $data['sections'];
        $visible = $data['visible'];

        foreach ($sections as $section) {
            Setting::set("home.show.$section", $visible);
        }

        // Clear cache to reflect changes immediately
        $this->clearCache();

        return [
            'success' => true,
            'message' => count($sections) . " sections updated to " . ($visible ? 'visible' : 'hidden'),
            'sections' => $sections,
            'visible' => $visible,
        ];
    }

    /**
     * Toggle section visibility
     */
    public function toggleVisibility(Request $request, string $section): array
    {
        $this->authorizeSettings($request);

        if (!in_array($section, ['hero', 'slider', 'offers', 'categories', 'special', 'latest', 'external'])) {
            return [
                'success' => false,
                'message' => 'Invalid section',
            ];
        }

        $currentVisibility = $this->isSectionVisible($section);
        $newVisibility = !$currentVisibility;

        Setting::set("home.show.$section", $newVisibility);
        
        // Clear cache to reflect changes immediately
        $this->clearCache();

        return [
            'success' => true,
            'message' => "Section '$section' toggled to " . ($newVisibility ? 'visible' : 'hidden'),
            'section' => $section,
            'visible' => $newVisibility,
        ];
    }

    private function authorizeSettings(Request $request): void
    {
        abort_unless($request->user()?->hasAccess('manage.settings'), 403);
    }
}
