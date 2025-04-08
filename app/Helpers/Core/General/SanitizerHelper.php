<?php


namespace App\Helpers\Core\General;

class SanitizerHelper
{
    /**
     * @param $value - TO FIX FITER_SANITIZE_STRING deprecated in PHP 8.1
     * @return mixed
     */
    public function filterData($value)
    {
       // return filter_var($value, $this->generateSanitizer($value));
       return $value;
    }

    /**
     * @param $data
     * @return false|int
     */
    public function generateSanitizer($data)
    {
        $type = gettype($data) == 'integer' ? 'int' : gettype($data);
        return filter_id($type);
    }
}
