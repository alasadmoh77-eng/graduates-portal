<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToArray;

class RawImport implements ToArray
{
    /**
     * @param array $array
     */
    public function array(array $array)
    {
        return $array;
    }
}
