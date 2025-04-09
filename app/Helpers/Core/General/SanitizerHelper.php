<?php


namespace App\Helpers\Core\General;

class SanitizerHelper
{
    /**
     * @param $value
     * @return mixed
     */
    public function filterData($value)
    {
       return $value ? filter_var($value, $this->generateSanitizer($value)) : $value;
       
    }
    /**
     * @param $data
     * @return false|int
     */
    public function generateSanitizer($data)
    {
        $type = gettype($data) === 'integer' ? 'int' : gettype($data);
        if($type === 'string')
            return filter_id('special_chars');
        else
            return filter_id($type);
    }
}
