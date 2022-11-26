<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToArray;

class GenericImport implements ToArray
{
    public array $data=[];
    /**
     * @param array $row
     * @return array
     */
    public function array(array $row)
    {
        $this->data[]=$row;
        /*
        return new User([
            'name'     => $row[0],
            'email'    => $row[1],
            'password' => Hash::make($row[2]),
        ]);
        */
    }
}
