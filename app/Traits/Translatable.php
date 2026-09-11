<?php

namespace App\Traits;

trait Translatable
{
    /**
     * Get localized attribute value
     *
     * @param string $attribute
     * @return mixed
     */
    public function getLocalizedAttribute(string $attribute)
    {
        $locale = app()->getLocale();
        $localizedField = "{$attribute}_{$locale}";
        
        // Return localized field if it exists and has a value
        if (isset($this->attributes[$localizedField]) && !empty($this->attributes[$localizedField])) {
            return $this->attributes[$localizedField];
        }
        
        // Fallback to English
        $englishField = "{$attribute}_en";
        if (isset($this->attributes[$englishField])) {
            return $this->attributes[$englishField];
        }
        
        // Fallback to the attribute itself (if it exists without suffix)
        return $this->attributes[$attribute] ?? null;
    }
    
    /**
     * Get localized name
     *
     * @return string|null
     */
    public function getLocalizedName(): ?string
    {
        return $this->getLocalizedAttribute('name');
    }
    
    /**
     * Get localized description
     *
     * @return string|null
     */
    public function getLocalizedDescription(): ?string
    {
        return $this->getLocalizedAttribute('description');
    }
    
    /**
     * Get localized slug
     *
     * @return string|null
     */
    public function getLocalizedSlug(): ?string
    {
        return $this->getLocalizedAttribute('slug');
    }
}
