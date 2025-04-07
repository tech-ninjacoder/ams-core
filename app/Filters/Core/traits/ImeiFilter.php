<?php


namespace App\Filters\Core\traits;


trait ImeiFilter
{
    public function imei($name = null)
    {
        $this->whereClause('imei', "%{$name}%", "LIKE");
    }
}
