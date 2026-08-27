<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB as DB;

class Setting extends Model
{
    use HasFactory;

    protected $table = 'ice_settings';

    private static ?array $settingsCache = null;

    public static function getSetting($key)
    {
            $setting = self::settingsCache()[$key] ?? null;
            if (! $setting && $key == 'theme') {
            return 'iceburgsaas';
            }

            return $setting;
    }

    public static function getThemes()
    {
        return DB::table('ice_themes')->get();

    }

    public static function getSettings()
    {
        $data = [];
        Setting::all()->each(function ($item) use (&$data) {
            return $data[$item->name] = $item->value;
        });
        if (!auth()->check()) {
            /* Only set for guest so large imaging isn't passing each request */
            $logo=Setting::where('name', 'logo')->value('additional_data');
            if(strlen($logo)){
                $data['logo']=$logo;
            }
        }

        return $data;
    }

    public static function getBreadCrumbs($level1 = null, $level2 = null, $level3 = null)
    {
        $data[] = ['name' => 'Home', 'url' => '/dashboard', 'svg' => 'home'];
        if ($level1 !== null) {
        $data[] = $level1;
        }
        if ($level2 !== null) {
        $data[] = $level2;
        }
        if ($level3 !== null) {
        $data[] = $level3;
        }

        return $data;
    }

    public static function saveSettings($setting)
    {
        foreach ($setting as $key => $value) {
            Setting::where('name', $key)->update(['value' => $value]);
        }

        self::$settingsCache = null;

        return 1;
    }

    private static function settingsCache(): array
    {
        if (self::$settingsCache === null) {
            self::$settingsCache = Setting::query()->pluck('value', 'name')->all();
        }

        return self::$settingsCache;
    }
}
