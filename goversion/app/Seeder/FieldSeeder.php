<?php

namespace App\Seeder;

use App\Models\Field;
use App\Models\WorkFlowData;
use Carbon\Carbon;
use Faker\Factory;
use Illuminate\Support\Facades\DB as DB;

class FieldSeeder
{
    private $module;

    public function __construct($module)
    {
        $this->module = $module;
    }

    public function seed($seed): void
    {
        $faker = Factory::create();

        for ($x = 0; $x < $seed; $x++) {

            $data = Field::factory()->make()->toArray();
            $data = ['ice_slug' => $faker->regexify('[A-Za-z0-9]{20}')];
            $this->module->fields()->get()->each(function ($field) use (&$data, $faker) {

                if (! empty($field->list)) {
                    $data[$field->name] = 1;
                } else {

                    switch ($field->input_type) {
                        case 'color':
                            $data[$field->name] = $faker->hexColor();
                            break;
                        case 'tel':
                            $data[$field->name] = $faker->phoneNumber;
                            break;
                        case 'email':
                            $data[$field->name] = $faker->email;
                            break;
                        case 'city':
                            $data[$field->name] = $faker->city;
                            break;
                        case 'zip':
                            $data[$field->name] = $faker->postcode;
                            break;
                        case 'address':
                            $data[$field->name] = $faker->streetAddress();
                            break;
                        case 'checkbox':
                            $data[$field->name] = (bool) rand(0, 1);
                            break;
                        case 'file':
                            $data[$field->name] = '';
                            $file = file_get_contents('http://demo.iceburg.ca/seed/pdf/sample.pdf');
                            if ($file) {
                                $data[$field->name] = 'data:application/pdf;base64,'.base64_encode($file);
                            }
                            break;
                        case 'video':
                            $data[$field->name] = '';
                            break;
                        case 'audio':
                            $data[$field->name] = '';
                            break;
                        case 'image':
                            $data[$field->name] = '';
                            if ($field->name == 'flag' && isset($data['code'])) {
                                $image = file_get_contents('http://demo.iceburg.ca/seed/flags/'.$data['code'].'.png');
                                if ($image) {
                                    $data[$field->name] = 'data:image/png;base64,'.base64_encode($image);
                                }
                            } elseif ($field->name == 'profile_pic') {
                                $image = file_get_contents('http://demo.iceburg.ca/seed/people/0000'.rand(10, 99).'.jpg');
                                if ($image) {
                                    $data[$field->name] = 'data:image/jpg;base64,'.base64_encode($image);
                                }
                            } elseif ($field->name == 'company_logo') {
                                $image = file_get_contents('http://demo.iceburg.ca/seed/company_logos/'.rand(1, 23).'.png');
                                if ($image) {
                                    $data[$field->name] = 'data:image/jpg;base64,'.base64_encode($image);
                                }
                            }
                            break;
                        case 'password':
                            $data[$field->name] = $faker->password();
                            break;
                        case 'number':
                            $data[$field->name] = rand(1, 2000);
                            break;
                        case 'url':
                            $data[$field->name] = $faker->url();
                            break;
                        case 'date':
                            $data[$field->name] = rand(strtotime('-7 day'), strtotime('now'));
                            break;
                        case 'currency':
                            $data[$field->name] = $faker->randomFloat(2, 1, 100);
                            break;
                        case 'related':
                            $data[$field->name] = rand(1, 5);
                            break;
                        case 'textarea':
                            $data[$field->name] = $faker->realTextBetween(50, 200);
                            break;
                        default:
                            if ($field->name == 'name') {
                                $data[$field->name] = $faker->company;
                            } elseif ($field->name == 'first_name') {
                                $data[$field->name] = $faker->firstName;
                            } elseif ($field->name == 'last_name') {
                                $data[$field->name] = $faker->lastName;
                            } elseif ($field->data_type == 'string') {
                                $data[$field->name] = $faker->realTextBetween(10, 50);
                            } elseif ($field->data_type == 'Integer') {
                                $data[$field->name] = $faker->numberBetween(1, 100);
                            } else {
                                $data[$field->name] = 1;
                            }
                        break;
                    }
                }
            });
            if (isset($data['start_date']) && isset($data['end_date'])) {
                $data['start_date'] = rand(strtotime('now'), strtotime('-7 day'));
                if ($this->module->name == 'meetings') {
                    $data['start_date'] = rand(strtotime('now'), strtotime('+7 day'));
                    $data['end_date'] = $data['start_date'] + (60 * 30 * (rand(1, 6)));
                } elseif ($this->module->name == 'contracts') {
                    $data['start_date'] = rand(strtotime('-60 day'), strtotime('+60 day'));
                    $data['end_date'] = $data['start_date'] + (86400 * 30 * (rand(1, 6)));
                } elseif ($this->module->name == 'projects') {
                    $data['start_date'] = rand(strtotime('-7 day'), strtotime('+30 day'));
                    $data['end_date'] = $data['start_date'] + (86400 * (rand(1, 30)));
                }
            }

            $data['created_at'] = date('Y-m-d H:i:s', strtotime('-'.rand(1, 31).' DAY'));
            $data['updated_at'] = $data['created_at'];
            $id = DB::table($this->module->name)->insertGetId($data);
            WorkFlowData::insert([
                'from_id' => 0,
                'from_module_id' => 0,
                'to_id' => $id,
                'to_module_id' => $this->module->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
