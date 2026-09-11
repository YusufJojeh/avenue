<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Orchid\Screen\AsSource;

class Setting extends Model
{
    use AsSource;

    public $timestamps = true;
    protected $fillable = ['group','key','value'];

    // helper get('key', default) إن لم تكن موجودة
    public static function get($key, $default = null, $locale = null)
    {
        $row = static::where('key', $key)->first();
        
        return $row ? $row->value : $default;
    }

    // helper set('key', value) لحفظ أو تحديث القيمة
    public static function set($key, $value, $locale = null)
    {
        $row = static::where('key', $key)->first();

        if ($row) {
            $row->update(['value' => $value]);
        } else {
            static::create([
                'key' => $key,
                'value' => $value,
                'group' => 'general'
            ]);
        }

        return $value;
    }

}
