<?php


namespace App\Filters\Core\traits;


trait ImeiSearchFilter
{
    public function search($search = null)
    {
        $this->imei($search);
    }
}
