<?php

namespace App\Rules;

use App\Helpers\Traits\MakeArrayFromString;
use App\Models\Tenant\Employee\Skill;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Log;

class SkillExistRule implements Rule
{

    use MakeArrayFromString;

    public function passes($attribute, $value)
    {
        $values = $this->makeArray($value);
        $skills = Skill::query()
            ->whereIn('name', $values)
            ->pluck('name')
            ->toArray();

         return count($skills) == count($values);
    }


    public function message()
    {

        return trans('default.is_invalid_message', ['subject' => __t('skill')]);
    }
}
