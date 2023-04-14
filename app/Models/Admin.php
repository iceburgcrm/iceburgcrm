<?php

namespace App\Models;

use app\models\Module;
use app\models\ModuleSubpanel;
use Exception;
use Illuminate\Support\Facades\DB as DB;

class Admin
{
    public function getData($request)
    {
        $data = [];
        switch ($request->type) {
            case 'module':
                $data = Module::where('id', $request->id)
                    ->with('fields')
                    ->with('groups')
                    ->with('convertedmodules')
                    ->with('subpanels')
                    ->first();
                break;
            case 'subpanel':
                $data = ModuleSubpanel::where('id', $request->id)
                    ->with('relationship.relationshipmodule.module.fields.module')
                    ->with('module')
                    ->first();
                break;
            default:
                break;
        }

        return $data;
    }

    public static function resetCRM()
    {
        try {
            Module::where('faker_seed', 1)
                ->where('admin', '!=', 1)
                ->get()->each(function ($module) {
                    DB::table($module->name)->truncate();
                 }
            );
        } catch (Exception $e) {
          return false;
        }

        return true;
    }
}
