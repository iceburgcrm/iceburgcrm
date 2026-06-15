<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OpenAI\Laravel\Facades\OpenAI;

class AIAssist extends Model
{
    use HasFactory;

    public static function suggestFields($module_id, $data, $additional_text="")
    {
        $module=Module::where('id', $module_id)->first();
        $txt='Complete the following values for the fields below.  They are from a crm module called ' . $module->name;
        $txt.='.\n\n Return an Array with the field name (exactly as given) and the completed value.';
        $txt.=' Follow the rules for each array item and reset the rules for each item. Only return the data as json.  Only return the name and value field.  Do not include comments only the array.  TRY TO BE CREATIVE IN YOUR ANSWERS.  ';
        foreach($data['ai_fields'] as $key => $value)
        {
            if($value)
            {
                $field=Field::where('name', $key)->first();
                if($field){
                    $rules="Datatype is: " . $field->data_type .
                        ".  Input type is: " . $field->input_type .
                        ". Maximum Field Length is: " . $field->field_length .
                        "." . ($field->input_type == 'image' ? '  Data must be a base64 encoded image.' : '') .
                        "." . ($field->decimal_places ? '  Must have ' . $field->decimal_places . 'decimal places.' : '') .
                       "        ";
                    $txt.="   " . print_r([
                        'name' => $key,
                        'value' => (isset($data['field_data']['1__' . $key])) ? $data['field_data']['1__' . $key] : '',
                        'rules' => $rules
                    ], true);
                }

            }

        }
        $txt.=$additional_text;

        $response=self::getData($txt);

        return json_decode($response->choices[0]->message->content, true);
    }

    private static function getData($content)
    {
        return OpenAI::chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'user', 'content' => $content],
            ],
        ]);
    }
}
