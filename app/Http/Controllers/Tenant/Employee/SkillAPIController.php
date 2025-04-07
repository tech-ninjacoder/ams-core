<?php


namespace App\Http\Controllers\Tenant\Employee;


use App\Http\Controllers\Controller;
use App\Models\Tenant\Employee\Provider;
use App\Models\Tenant\Employee\Skill;

class SkillAPIController extends Controller
{
    public function index()
    {
        return Skill::select('id','name')->get();
    }
}
