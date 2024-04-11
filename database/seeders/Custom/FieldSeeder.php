<?php

namespace Database\Seeders\Custom;

use App\Models\Field;
use App\Models\Module;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class FieldSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        Module::get()->each(function ($module) {
            if (method_exists($this, $module->name)) {
                Log::info('Pre'.$module->name.'Generated');
                $this->{$module->name}($module->id);
                Log::info($module->name.'Generated');
            } else {
                Log::info($module->name.'Not Generated');
            }

        });


    }

    public function Currency()
    {
        $order = 0;
        $moduleId = Module::getId(__FUNCTION__);
        Field::insert(Field::getField([
            'name' => 'name',
            'label' => 'Name',
            'module_id' => $moduleId,
            'field_length' => 32,
        ], $order++));

        Field::insert(Field::getField([
            'name' => 'code',
            'label' => 'Code',
            'module_id' => $moduleId,
            'field_length' => 3,
        ], $order++));

        Field::insert(Field::getField([
            'name' => 'symbol',
            'label' => 'Symbol',
            'module_id' => $moduleId,
            'field_length' => 3,
        ], $order++));
    }

    public function Countries()
    {
        $order = 0;
        $moduleId = Module::getId(__FUNCTION__);

        Field::insert(Field::getField([
            'name' => 'code',
            'label' => 'Code',
            'module_id' => $moduleId,
            'field_length' => 16,
        ], $order++));

        Field::insert(Field::getField([
            'name' => 'name',
            'label' => 'name',
            'module_id' => $moduleId,
            'field_length' => 100,
        ], $order++));

        Field::insert(Field::getField([
            'name' => 'flag',
            'label' => 'Flag',
            'module_id' => $moduleId,
            'input_type' => 'image',
            'data_type' => 'MEDIUMTEXT',
        ], $order++));
    }

    public function States()
    {
        $order = 0;
        $moduleId = Module::getId(__FUNCTION__);

        Field::insert(Field::getField([
            'name' => 'code',
            'label' => 'Code',
            'module_id' => $moduleId,
            'field_length' => 4,
        ], $order++));

        Field::insert(Field::getField([
            'name' => 'abbreviation',
            'label' => 'Abbreviation',
            'module_id' => $moduleId,
            'field_length' => 4,
        ], $order++));

        Field::insert(Field::getField([
            'name' => 'name',
            'label' => 'Name',
            'module_id' => $moduleId,
            'field_length' => 120,
        ], $order++));
    }

}
