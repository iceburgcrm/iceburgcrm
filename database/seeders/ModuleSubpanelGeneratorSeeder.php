<?php
// add comments to the code

namespace Database\Seeders;

use App\Models\SubpanelField;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use App\Models\ModuleSubpanel;
use App\Models\Module;
use App\Models\Field;
use App\Models\Relationship;

class ModuleSubpanelGeneratorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $module_id=Module::where('name', 'modules')->value('id');
        $field_id=Module::where('name', 'fields')->value('id');
        $fields=Field::where('module_id', $module_id)->get();
        foreach($fields as $field)
        {
            Relationship::saveRecord(
                Relationship::where('name', 'like', 'modules_fields')->value('id'),
                [
                    $module_id => $field->module_id,
                    $field_id => $field->id,
                ]
            );
        }

        $mod_id=Module::where('name', 'module_subpanels')->value('id');
        $fields=Field::where('module_id', $module_id)->get();
        foreach($fields as $field)
        {
            Relationship::saveRecord(
                Relationship::where('name', 'like', 'modules_subpanels')->value('id'),
                [
                    $module_id => $field->module_id,
                    $mod_id => $field->id,
                ]
            );
        }

        $datalet_id=Module::where('name', 'datalets')->value('id');
        $fields=Field::where('module_id', $module_id)->get();
        foreach($fields as $field)
        {
            Relationship::saveRecord(
                Relationship::where('name', 'like', 'modules_datalets')->value('id'),
                [
                    $module_id => $field->module_id,
                    $datalet_id=> $field->id,
                ]
            );
        }
    }
}

