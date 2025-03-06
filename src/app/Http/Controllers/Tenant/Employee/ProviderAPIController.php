<?php


namespace App\Http\Controllers\Tenant\Employee;


use App\Http\Controllers\Controller;
use App\Models\Tenant\Employee\Provider;

class ProviderAPIController extends Controller
{
    public function index()
    {
        return Provider::select('id','name','contract_type')->get();
    }
}
