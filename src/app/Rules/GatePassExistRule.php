<?php

namespace App\Rules;

use App\Helpers\Traits\MakeArrayFromString;
use App\Models\Core\Auth\Role;
use App\Models\Tenant\Employee\GatePass;
use Illuminate\Contracts\Validation\Rule;

class GatePassExistRule implements Rule
{

    use MakeArrayFromString;

    public function passes($attribute, $value)
    {
        $values = $this->makeArray($value);
        $gate_passes = GatePass::query()
            ->whereIn('name', $values)
            ->pluck('name')
            ->toArray();

         return count($gate_passes) == count($values);
    }


    public function message()
    {
        return trans('default.is_invalid_message', ['subject' => __t('gate_passes')]);
    }
}
