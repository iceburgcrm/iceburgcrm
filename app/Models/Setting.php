<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB as DB;

class Setting extends Model
{
    use HasFactory;

    public static function getSetting($key)
    {
            $setting=Setting::where('name', $key)->pluck('value')->first();
            if(!$setting && $key == 'theme') return 'light';
            return $setting;
    }

    public static function getThemes()
    {
        return DB::table('themes')->get();

    }

    public static function getSettings()
    {
        $data=[];
        Setting::all()->each(function($item) use (&$data) {
            return $data[$item->name]=$item->value;
        });
        return $data;
    }

    public static function getBreadCrumbs($level1=null, $level2=null, $level3=null)
    {
        $data[] = ['name' => 'Home', 'url' => '/dashboard', 'svg' => 'home'];
        if($level1 !== null) $data[] = $level1;
        if($level2 !== null) $data[] = $level2;
        if($level3 !== null) $data[] = $level3;
        return $data;
    }

    public static function saveSettings($setting)
    {
        foreach($setting as $key => $value)
        {
            Setting::where("name", $key)->update([ "value" => $value]);
        }
        return 1;
    }
}
