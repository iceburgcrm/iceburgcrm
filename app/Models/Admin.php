<?php

namespace App\Models;

use app\models\Module;

class Admin
{
    public function getData($request)
    {
        $data = [];
        switch($request->type)
        {
            case 'module':
                $data=Module::where('id', $request->id)
                    ->with('fields')
                    ->with('groups')
                    ->with('convertedmodules')
                    ->with('subpanels')
                    ->first();
                break;
            case 'subpanel':
                $data=Subpanel::where('id', $request->id)
                    ->with('relationship.relationshipmodule.module.fields.module')
                    ->with('module')
                    ->first();
                break;
            default:
                break;
        }
        return $data;
    }
}
