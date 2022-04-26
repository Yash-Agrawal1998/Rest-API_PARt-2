<?php

namespace App\Filter;

use Phalcon\Escaper;

/**
 * Class for filtering our data
 */
class FilterData
{
    function sanitizeData($data)
    {
        $escape= new Escaper();
        foreach ($data as $key =>$value) {
            $data[$key]=$escape->escapeHtml($value);
        }
        return $data;
    }
}