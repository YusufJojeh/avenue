<?php

namespace App\Helpers;

use App\Models\Setting;

class SiteHelper
{
    /**
     * Get the site name from settings
     */
    public static function getSiteName(): string
    {
        try {
            return Setting::get('site.name', 'MyStore');
        } catch (\Exception $e) {
            return 'MyStore';
        }
    }

    /**
     * Get all site settings
     */
    public static function getSiteSettings(): array
    {
        try {
            $settings = [];
            $allSettings = Setting::all();
            
            foreach ($allSettings as $setting) {
                $settings[$setting->key] = $setting->value;
            }
            
            // Parse JSON settings
            if (isset($settings['site.social_media']) && $settings['site.social_media']) {
                $settings['social_media'] = json_decode($settings['site.social_media'], true) ?: [];
            } else {
                $settings['social_media'] = [];
            }
            
            if (isset($settings['home.limits']) && $settings['home.limits']) {
                $settings['limits'] = json_decode($settings['home.limits'], true) ?: [];
            } else {
                $settings['limits'] = [];
            }
            
            return $settings;
        } catch (\Exception $e) {
            return [
                'social_media' => [],
                'limits' => [],
            ];
        }
    }

    /**
     * Get app name for configuration
     */
    public static function getAppName(): string
    {
        return self::getSiteName();
    }
}
