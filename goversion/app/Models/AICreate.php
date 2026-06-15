<?php

namespace App\Models;

use Faker\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB as DB;
use Illuminate\Support\Facades\Log;
use OpenAI\Laravel\Facades\OpenAI;

class AICreate extends Model
{
    use HasFactory;

    private static mixed $model = 'gpt-3.5-turbo';
    protected static mixed $retry = 0;


    public static function process($prompt, $model = "gpt-3.5-turbo", $logo = false, $seedAmount = 0, $seedType = "")
    {
        self::$model=$model;
        if ($logo) {
            Log::info('Creating logo');
            self::createLogo($prompt);
        }
        Log::info('Getting Updating Settings');
        self::updateSettings(self::getSettings($prompt));
        Log::info('Create Modules');

        self::createModules(self::getModules($prompt));
        if (Module::where('primary', 0)->count() < 1) {
            self::createModules(self::getModules($prompt));
        }
;
        Log::info("Create Module Groups");
        self::createModuleGroups(self::getModuleGroups());
        if (ModuleGroup::count() < 1) {
            self::createModuleGroups(self::getModuleGroups());
        }

        Log::info('create Fields\n');
        foreach (Module::where('primary', 0)->get() as $module) {
            self::createFields(self::getFields($module), $module);
        }

        foreach (Module::where('primary', 0)
                     ->doesntHave('fields')->get()
                 as $module) {
            Log::info("Retry fields for " . $module->name);
            self::createFields(self::getFields($module), $module);
        }

        Log::info('Create relationships');
        self::createRelationships(self::getRelationships());
        if (Relationship::count() < 1) {
            self::createRelationships(self::getRelationships());
        }

        Log::info('Create subpanels');
        self::createSubPanels();

        Log::info('Generate');
        self::generate($seedAmount, $seedType);
        Log::info('done creating');

    }

    public static function generate($seedAmount=0)
    {
        Log::info('Generating module');
        $module = new Module;
        $module->generate($seedAmount);
    }

    public static function generateAIRecords($seedAmount=1, $moduleId=0, $startAt=0)
    {
        for ($x = 0; $x <= $seedAmount; $x++) {
            if ($moduleId > 0) {
               // self::createNextRecord(null, $moduleId);
               // exit;
                self::createNextRecord(self::getNextRecord($moduleId), $moduleId);
            }
        }
    }

    public static function createNextRecord($data=[], $moduleId)
    {

        if(isset($data) && isset($data['data'])){
            $module=Module::where('id', $moduleId)->first();
            $arr=[];
            foreach($data['data'] as $item)
            {
                $arr[]=$item['value'];
            }
            print "inserting the following array";
            var_dump($arr);
            Module::insertImport($module, [$arr]);
        }
        else {
            print "Data null";
            var_dump($data);
        }

    }
    public static function getNextRecord($moduleId)
    {
        $fields=Field::where('module_id', $moduleId)->get();
        $txt="Fill in the missing value field for the array below.
        Use the field name, type to help determine the values for the missing value fieldse. ";
        foreach($fields as $field) {
                $rules = "Datatype is: " . $field->data_type .
                    ".  Input type is: " . $field->input_type .
                    ". Maximum Field Length is: " . $field->field_length .
                    "." . ($field->input_type == 'image' ? '  Data must be a base64 encoded image.' : '') .
                    "." . ($field->decimal_places ? '  Must have ' . $field->decimal_places . 'decimal places.' : '') .
                    "        ";
                $txt .= "   " . print_r([
                        'name' => $field->name,
                        'value' => '',
                        'rules' => $rules
                    ], true);

        }

        $module = Module::find($moduleId);
        $record = DB::table($module->name)
            ->selectRaw($module->name.'.'.'*, '.$module->name.'.' . $module->primary_field . ' as '.$module->name.'_row_id, '.$module->name.'.'. $module->primary_field . ' as row_id')
            ->first();
        $excludeFields = ['updated_at', 'created_at', 'ice_slug'];
        $processedRecord = [];

        foreach ((array) $record as $key => $value) {
            if (!in_array($key, $excludeFields)) {
                $processedRecord[$key] = strlen($value) > 200 ? substr($value, 0, 200) . '...' : $value;
            }
        }


        //$txt.="Last Record: " . print_r($processedRecord, true);
        $txt.="

        return the array with the values field filled in, the name and remove the rules.
        return all data as a php encoded array under the key data. Output in json.  No text explaining.

        ";
        print $txt;
        //exit;

        $response=self::getData($txt);


        return json_decode($response->choices[0]->message->content, true);

    }



    public static function createSubPanels()
    {

        $relationships=Relationship::where('status', 1)->get();
        foreach($relationships as $relationship)
        {
            $modules=explode(",",$relationship->modules);
            foreach($modules as $moduleId)
            {
                $primaryModule=Module::where('id', $moduleId)->first();
                $subModules=$modules;
                $subModules=array_diff($subModules, [$moduleId]);
                foreach($subModules as $subModuleId){
                    $subModule=Module::where('id', $subModuleId)->first();
                    $moduleSubpanelId=ModuleSubpanel::insertGetId([
                        'name' => $primaryModule->name . '_' . $subModule->name,
                        'label' => $subModule->label,
                        'relationship_id' => $relationship->id,
                        'module_id' => $primaryModule->id,
                    ]);
                    $fields=Field::where('module_id', $subModuleId)->get();

                    $cnt=0;
                    foreach($fields as $field)
                    {
                        if($cnt==3) break;
                        SubpanelField::insert([
                            'subpanel_id' => $moduleSubpanelId,
                            'field_id' => $field->id,
                        ]);
                        $cnt++;
                    }

                }

            }
        }
    }
    public static function createLogo($data)
    {
        $txt="give me a dalle-3 prompt for a crm using the prompt for inspiration and the crm topic.  The artwork should be clean, with no text, allowing the imagery to speak for itself";
        $txt.="Prompt: " . $data;
        $response=self::getData($txt);
        $image_prompt=$response->choices[0]->message->content;
        $image_prompt.=" Imagine a sleek, modern dashboard that represents the heart of a CRM system, designed to streamline and enhance customer relationships for businesses. This image should capture the essence of technology at the service of human connections, blending elements of advanced digital interfaces with symbols of personal interaction. Picture a background with a gradient of calming blues and vibrant greens, symbolizing growth and trust. Foreground elements include a network of interconnected dots and lines, representing a digital network, subtly shaped like a handshake or a heart, to symbolize personal connections and the warmth of human relationships. Include icons that represent communication, such as speech bubbles, email envelopes, and phone symbols, seamlessly integrated into the network. The overall feel should be futuristic yet accessible, inviting viewers to see the CRM as a tool that bridges the gap between technology and personal touch in business relationships. The artwork should be clean, with no text, allowing the imagery to speak for itself.";
        $response=self::getImageData($image_prompt);
        if(isset($response)){
            $imageData=file_get_contents($response->data[0]['url']);
            Setting::where('name', 'logo')->update(['value'=>'1', 'additional_data' => base64_encode($imageData)]);
        }
        else {
            $response=self::getImageData($image_prompt);
            $imageData=file_get_contents($response->data[0]['url']);
            Setting::where('name', 'logo')->update(['value'=>'1', 'additional_data' => base64_encode($imageData)]);
        }
    }

    public static function getSettings($data)
    {
        $themes=Theme::pluck('name')->toArray();
        $txt="Text prompt: " . $data;
        $txt.="\n\n
        We're creating a new crm.  Based on the prompt text create a title, description and select a theme name.



        list of themes:
        " . print_r($themes, true) . "

        put the theme name as an array key value in a settings array.

        put the name as an array key value pair in a settings array

        put the description in the same array using description as the key.  the description should be less than 200 characters.

        json encode the array.  Provide no text.
        ";
        $response=self::getData($txt);
        return json_decode($response->choices[0]->message->content, true);

    }

    public static function getModuleGroups()
    {
        $modules=Module::where('status', 1)
            ->where('primary', 0)
            ->select('id', 'name')
            ->get()
            ->toArray();
        $txt="
        Group the following modules into 4 groups for a crm with roughly the same amount in each group.    Group them by theme.
 Save the module_group_id and name in an array with an array key of 'module_groups'.  We also want to make another array with the module_name, module_group_id under the key 'modules' that lists all of the existing modules with the new module_group_id.

        Add name and id of each group to an array under the array key 'module_groups'
use this format:
[
    'id' => '',  // id of the group
    'name' => '' // name of group lower case no spaces,
    'label' => '',  //label for the group
]

Update the existing module list with the new module_group_id
'id', 'module_group_id'
[
    1, 1
]




        list of existing modules:
        " . print_r($modules, true) . "


         json encode the array output.  Provide no text or explaining.";

        $response=self::getData($txt);
        return json_decode($response->choices[0]->message->content, true);

    }

    public static function getModules($data)
    {
        //self::$model='gpt-4';
        $modules=Module::where('status', 1)->get()->toArray();
        $txt="Text prompt: " . $data;
        $txt.="\n\nUser will text in a text prompt.  GPT will try to determine a CRM type.  Based on the CRM type come up with a list of modules.  For example a typical crm may have:
Accounts
Contacts
Contracts
Leads
Opportunities
Lineitems
Products
Campaigns
Cases
Documents
Notes
Projects
Groups
Quotes

Include unique Modules that relate to the topic.  A book collecting crm might have a books and authors module.  A Laravel tutorial crm would have a tutorials module.  Find unique modules for the topic.



For each module create an array like the following format and put all of them in an array under the key modules:
[
    'name' => 'accounts',
    'label' => 'Accounts',
    'description' => 'Account module',
    'icon' => 'BuildingOffice2Icon',
]


only use these values for the icon
'BuildingOffice2Icon',
        'BuildingOffice2Icon',
        'BuildingOfficeIcon',
        'BuildingLibraryIcon',
        'BuildingStorefrontIcon',
        'BriefcaseIcon',
        'HomeIcon',
        'HomeModernIcon',
        'UserPlusIcon',
        'UserMinusIcon',
        'UserCircleIcon',
        'UserIcon',
        'ChatBubbleLeftIcon',
        'CalculatorIcon',
        'CircleStackIcon',
        'BookOpenIcon',
        'Bars4Icon',
        'UsersIcon',
        'LightBulbIcon',
        'MegaphoneIcon',
        'InboxStackIcon',
        'CurrencyDollarIcon',
        'ArrowRightOnRectangleIcon',
        'QueueListIcon',
        'PencilSquareIcon',
        'DocumentIcon',
        'PencilIcon',
        'UserGroupIcon',
        'GlobeAmericasIcon',
        'RectangleGroupIcon',
        'GlobeAltIcon',
        'CurrencyPoundIcon',
        'SparklesIcon',
        'PhoneIcon',
        'Cog6ToothIcon',


Output as a json object.

Do not include any additional text explaining.  Try to add at least 15 modules or more.   Don't skip any module definition.  Don't write 'Follow the pattern above for other modules and fields.', finish the patterns";

        $response=self::getData($txt);

        return json_decode($response->choices[0]->message->content, true);
    }


    public static function getFields($module){

        $txt="Take the module name '" . $module->name . "' and create a list of crm fields.  Try to create at least 15 fields.
The format of the array is below.  Fill in the missing pieces or remove values depending on the field type
[
            'name' => '',
            'label' => '',
            'input_type' => '',
        ]

rules:
input_type can be one of:
tel
email
city
custom
checkbox
color
date
image
number
password
radio
text
url
textarea
video
zip
address

output as a json encoded object.  No text.  No explaining.";

        $response=self::getData($txt);

        return json_decode($response->choices[0]->message->content, true);
    }

    public static function getRelationships(){

        $modules=Module::where('primary', 0)->select('id', 'name')->get()->toArray();
        $txt="Below are a list of modules for a crm.  I'm trying to determine if a natural relationship exists between two or more modules.  If it does I want to make a relationship array item.



        A relationship record looks like for two modules
        [
            'name' => 'module1name_module2name',
            'modules' => [
                id of module 1,
                id of module 2,
            ]
        ]
        The name is made up of the first module name underscore and the second module name
        The ids are made up of the module 1s

        This is an example of a three module relationship
        [
        'name' => 'module1name_module2name_module3name',
            'modules' => [
                id of module 1,
                id of module 2,
                id of module 3,
            ]


         List of modules
        " . print_r($modules, true) . "


Go through each module name and ask yourself if this module name is usually related to another module name when thinking about crms.    Be a little bit creative

Try to make 2 or 3 relationships for each module

        Output each relationship array as a list of arrays json encoded.  Include no text.";

        $response=self::getData($txt);

        return json_decode($response->choices[0]->message->content, true);
        }


    private static function getData($content)
    {
        return OpenAI::chat()->create([
            'model' => self::$model,
            'messages' => [
                ['role' => 'user', 'content' => $content],
            ],
        ]);
    }

    private static function getImageData($content)
    {

        return OpenAI::images()->create([
            'model' => "dall-e-3",
            'prompt' => $content,
            'size' => "1024x1024",
            'quality' => "standard",
            'n' => 1,
        ]);
    }



    public static function createModuleGroups($data){
        foreach($data['module_groups'] as $moduleGroup)
        {
            ModuleGroup::Insert($moduleGroup);
        }

        foreach($data['modules'] as $module)
        {
            Module::where('id', $module['id'])->update(['module_group_id' => $module['module_group_id']]);
        }
    }

    public static function createModules($data)
    {
        $order = 0;
        foreach($data['modules'] as $module)
        {
            $module['view_order']=$order++;
            $module['module_group_id']=0;
            if(!Module::where('name', $module['name'])->first())
            {
                Module::insert($module);
            }

        }
    }

    public static function createFields($data, $module)
    {

        $data=self::checkFields($data, $module->id);
        $cnt=0;
        if(isset($data)) {
            foreach ($data as $field) {

                $field=Field::getField($field);
                if(strtolower($field['name']) == "created_at"
                    || strtolower($field['name']) == "updated_at")
                {
                    continue;
                }
                if(!Field::where('name', $field['name'])->where('module_id', $module->id)->first())
                {
                    Field::insert($field);
                }

            }
        }
        self::$retry=0;
    }

    public static function checkFields($data=[], $moduleId)
    {
        $output=[];

        if(isset($data)){
            foreach($data as $item)
            {
                $item['module_id']=$moduleId;
                if(!isset($item['input_type'])){
                   continue;
                }
                switch($item['input_type']){
                    case 'tel':
                        $item['field_length']=32;
                        break;
                    case 'email':
                        $item['field_length']=64;
                        break;
                    case 'city':
                        $item['field_length']=50;
                        break;
                    case 'url':
                        $item['field_length']=75;
                        break;
                    case 'number':
                        unset($item['field_length']);
                        $item['data_type']='integer';
                        break;
                    case 'currency':
                        $item['field_length']=8;
                        $item['decimal_places']=2;
                        break;
                    case 'date':
                        $item['data_type']='integer';
                        break;
                    case 'address':
                        $item['field_length']=128;
                        break;
                    case 'textarea':
                        $item['field_length']=190;
                        break;
                    case 'checkbox':
                        $item['data_type']='boolean';
                        $item['field_length']=0;
                        break;
                    case 'password':
                        $item['field_length']=100;
                        break;
                    case 'text':
                        $item['field_length']=64;
                        break;
                    case 'video':
                        $item['data_type']='mediumtext';
                        break;
                    case 'audio':
                        $item['data_type']='mediumtext';
                        break;
                    case 'zip':
                        $item['field_length']=20;
                        break;
                    case 'file':
                        $item['data_type']='text';
                        break;
                    case 'number':
                        $item['field_length']=0;
                        break;
                    default:
                        $item['field_length']=64;
                        break;
                }
                array_push($output, $item);
            }
        }


        return $output;
    }

    public static function createRelationships($data)
    {
        if(isset($data)) {
            foreach($data as $relationship)
            {
                $relationship['modules']=implode(",", $relationship['modules']);
                Relationship::insert($relationship);
            }
        }

    }

    public static function updateSettings($data)
    {
        if(isset($data)) {
            foreach ($data as $key => $value) {
                Setting::where('name', $key)->update(['value' => $value]);
            }
        }

    }
}
