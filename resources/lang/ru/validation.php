<?php /* Translated by Paul (https://paul.bid) www.paul.bid@gmail.com */

return [

    /*
    |--------------------------------------------------------------------------
    | Языковые строки для проверок и правил
    |--------------------------------------------------------------------------
    |
    | Следующие языковые строки содержат сообщения об ошибках по умолчанию, ис-
    | пользуемые классом валидатора. Некоторые строки имеют несколько версий,
    | например, правила и ограничения. Настройте каждое из этих сообщений тут.
    |
    */

    'accepted' => 'Атрибут :attribute должен быть принят.',
    'accepted_if' => 'Атрибут :attribute должен быть принят, когда :other равен :value.',
    'active_url' => ':attribute не является допустимым URL-адресом.',
    'after' => 'Атрибут :attribute должен быть датой после :date.',
    'after_or_equal' => 'Атрибут :attribute должен быть датой позже или равной дате: :date.',
    'alpha' => 'Атрибут :attribute должен содержать только буквы.',
    'alpha_dash' => 'Атрибут :attribute должен содержать только буквы, цифры, тире и символы подчёркивания.',
    'alpha_num' => 'Атрибут :attribute должен содержать только буквы и цифры.',
    'array' => 'Атрибут :attribute должен быть массивом.',
    'before' => 'Атрибут :attribute должен быть датой, предшествующей дате: :date.',
    'before_or_equal' => 'Атрибут :attribute должен быть датой предшествующей или равной дате: :date.',
    'between' => [
        'array' => 'Атрибут :attribute должен содержать от :min до :max элементов.',
        'file' => 'Значение :attribute должно быть в диапазоне от :min до :max килобайт.',
        'numeric' => 'Значение :attribute должно быть числом в диапазоне между :min и :max.',
        'string' => 'Значение :attribute должно содержать в диапазоне от :min до :max символов.',
    ],
    'boolean' => 'Поле :attribute должно иметь значение true или false.',
    'confirmed' => 'Атрибут подтверждения :attribute не совпадает.',
    'current_password' => 'Пароль неверный.',
    'date' => 'Атрибут :attribute не является допустимой датой.',
    'date_equals' => 'Атрибут :attribute должен быть датой, равной :date.',
    'date_format' => 'Атрибут :attribute не соответствует формату :format.',
    'declined' => 'Атрибут :attribute должен быть отклонён.',
    'declined_if' => 'Атрибут :attribute необходимо отклонить, если :other равно :value.',
    'different' => 'Атрибуты :attribute и :other должны быть разными.',
    'digits' => 'Атрибут :attribute должен состоять из :digits цифр.',
    'digits_between' => 'Значение атрибута :attribute должно находиться в диапазоне от :min до :max цифр.',
    'dimensions' => 'Атрибут :attribute имеет недопустимые размеры изображения.',
    'distinct' => 'Поле :attribute имеет повторяющееся значение.',
    'email' => 'Атрибут :attribute должен быть действительным адресом электронной почты.',
    'ends_with' => 'Атрибут :attribute должен заканчиваться одним из следующих значений: :values.',
    'enum' => 'Выбранный атрибут :attribute недействителен.',
    'exists' => 'Выбранный атрибут :attribute недействителен.',
    'file' => 'Атрибут :attribute должен быть файлом.',
    'filled' => 'Поле :attribute должно иметь какое-либо значение (не быть пустым).',
    'gt' => [
        'array' => 'Атрибут :attribute должен содержать больше элементов, чем :value.',
        'file' => 'Значение :attribute должно быть больше, чем :value килобайт.',
        'numeric' => 'Значение :attribute должно быть числом больше :value.',
        'string' => 'Значение :attribute должно содержать больше :value символов.',
    ],
    'gte' => [
        'array' => 'Атрибут :attribute должен содержать :value элемента(ов) или более.',
        'file' => 'Значение :attribute должно быть больше или равно значению :value килобайт.',
        'numeric' => 'Значение :attribute должно быть числом большим или равным :value.',
        'string' => 'Значение :attribute должно содержать больше или ровно :value символов.',
    ],
    'image' => 'Атрибут :attribute должен быть изображением.',
    'in' => 'Выбранный атрибут :attribute недействителен.',
    'in_array' => 'Поле :attribute не существует в :other.',
    'integer' => 'Значение :attribute должно быть целым числом.',
    'ip' => 'Атрибут :attribute должен быть действительным IP-адресом.',
    'ipv4' => 'Атрибут :attribute должен быть действительным адресом IPv4.',
    'ipv6' => 'Атрибут :attribute должен быть действительным адресом IPv6.',
    'json' => 'Атрибут :attribute должен быть допустимой строкой JSON.',
    'lt' => [
        'array' => 'Атрибут :attribute должен содержать меньше элементов, чем :value.',
        'file' => 'Значение :attribute должно быть меньше, чем :value килобайт.',
        'numeric' => 'Значение :attribute должно быть числом меньше :value.',
        'string' => 'Значение :attribute должно содержать меньше :value символов.',
    ],
    'lte' => [
        'array' => 'Атрибут :attribute должен содержать :value элемента(ов) или менее.',
        'file' => 'Значение :attribute должно быть меньше или равно значению :value килобайт.',
        'numeric' => 'Значение :attribute должно быть числом меньшим или равным :value.',
        'string' => 'Значение :attribute должно содержать меньше или ровно :value символов.',
    ],
    'mac_address' => 'Атрибут :attribute должен быть действительным MAC-адресом.',
    'max' => [
        'array' => 'Атрибут :attribute должен содержать не более :max элементов.',
        'file' => 'Значение :attribute должно быть не более :max килобайт.',
        'numeric' => 'Значение :attribute должно быть числом не большим чем :max.',
        'string' => 'Значение :attribute должно содержать не более :max символов.',
    ],
    'mimes' => ':attribute должен быть файлом типа: :values.',
    'mimetypes' => ':attribute должен быть файлом типа: :values.',
    'min' => [
        'array' => 'Атрибут :attribute должен содержать не менее :min элементов.',
        'file' => 'Значение :attribute должно быть не менее :min килобайт.',
        'numeric' => 'Значение :attribute должно быть числом не меньшим чем :min.',
        'string' => 'Значение :attribute должно содержать не менее :min символов.',
    ],
    'multiple_of' => ':attribute должен быть кратен :value.',
    'not_in' => 'Выбранный атрибут :attribute недействителен.',
    'not_regex' => 'Формат :attribute недействителен.',
    'numeric' => 'Атрибут :attribute должен быть числом.',
    'password' => [
        'letters' => ':attribute должен содержать хотя бы одну букву.',
        'mixed' => ':attribute должен содержать как минимум одну заглавную и одну строчную букву.',
        'numbers' => ':attribute должен содержать хотя бы одну цифру.',
        'symbols' => ':attribute должен содержать хотя бы один символ.',
        'uncompromised' => 'Данный :attribute появился в утечке данных. Пожалуйста, выберите другой :attribute.',
    ],
    'present' => 'Поле :attribute должно присутствовать.',
    'prohibited' => 'Поле :attribute запрещено.',
    'prohibited_if' => 'Поле :attribute запрещено, если :other равно :value.',
    'prohibited_unless' => 'Поле :attribute запрещено, если только :other не находится в :values.',
    'prohibits' => 'Поле :attribute запрещает присутствие/наличие :other.',
    'regex' => 'Формат :attribute недействителен.',
    'required' => 'Поле :attribute является обязательным.',
    'required_array_keys' => 'Поле :attribute должно содержать записи для: :values.',
    'required_if' => 'Поле :attribute является обязательным, когда :other равно :value.',
    'required_unless' => 'Поле :attribute является обязательным, если только :other не находится в :values.',
    'required_with' => 'Поле :attribute является обязательным, если присутствует(ют) :values.',
    'required_with_all' => 'Поле :attribute является обязательным, если присутствует(ют) все :values.',
    'required_without' => 'Поле :attribute является обязательным, если :values отсутствует(ют).',
    'required_without_all' => 'Поле :attribute является обязательным, если ни одно из :values не присутствует.',
    'same' => ':attribute и :other должны полностью совпадать.',
    'size' => [
        'array' => 'Атрибут :attribute должен содержать :size элементов.',
        'file' => 'Значение :attribute должно быть :size килобайт.',
        'numeric' => 'Значение :attribute должно быть числом :size.',
        'string' => 'Значение :attribute должно содержать :size символов.',
    ],
    'starts_with' => ':attribute должен начинаться с одного из следующих: :values.',
    'doesnt_start_with' => ':attribute не может начинаться с одного из следующих: :values.',
    'string' => ':attribute должен быть строкой.',
    'timezone' => ':attribute должен быть действительным часовым поясом.',
    'unique' => 'Атрибут :attribute уже занят.',
    'uploaded' => 'Не удалось загрузить :attribute.',
    'url' => ':attribute должен быть действительным URL-адресом.',
    'uuid' => ':attribute должен быть действительным UUID.',

    /*
    |--------------------------------------------------------------------------
    | Языковые строки для настаиваемого индивидуального перевода
    |--------------------------------------------------------------------------
    |
    | Здесь можно указать пользовательские сообщения проверки для атрибутов,
    | используя правила "attribute.rule" для именования строк. Это позволяет
    | быстро указать конкретную языковую строку для данного правила атрибута.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'ваше-сообщение/текст строки',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | Эти языковые строки используются для замены атрибута по умолчанию на что-
    | либо более удобное для читателя, например, «E-Mail адрес» вместо «email».
    | Это поможет вам сделать ваше сообщение более выразительным и точным.
    |
    */

    'attributes' => [],

];