<?php

namespace App\Imports;


use App\Helpers\Traits\MakeArrayFromString;
use App\Helpers\Traits\NameSplitTrait;
use App\Models\Core\Auth\Role;
use App\Models\Core\Auth\User;
use App\Models\Tenant\Employee\Department;
use App\Models\Tenant\Employee\Designation;
use App\Models\Tenant\Employee\EmploymentStatus;
use App\Models\Tenant\Employee\GatePass;
use App\Models\Tenant\Employee\Helmet;
use App\Models\Tenant\Employee\Skill;
use App\Rules\GatePassExistRule;
use App\Rules\RoleExistRule;
use App\Rules\SkillExistRule;
use App\Services\Tenant\Import\EmployeeImportService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class UsersImport implements ToModel, WithHeadingRow, WithValidation, WithBatchInserts, WithChunkReading, SkipsOnFailure
{
    use Importable, SkipsFailures, MakeArrayFromString, NameSplitTrait;

    public function model(array $row)
    {
        $department = Department::query()->where('name', $row['department'])->first()->id;
        $designation = Designation::query()->where('name', $row['designation'])->first()->id;
        if($row['pme_barcode']) {
            $helmet = Helmet::query()->where('pme_barcode',$row['pme_barcode'])->first()->id;
        } else{
            $helmet =null;
        }
        if($row['gate_passes']) {
            $gate_passes = GatePass::query()
                ->whereIn('name',$this->makeArray($row['gate_passes']))
                ->pluck('id')
                ->toArray();
        } else {
            $gate_passes=null;
        }

        if($row['skills']) {
            $skills = Skill::query()
                ->whereIn('name',$this->makeArray($row['skills']))
                ->pluck('id')
                ->toArray();
        } else {
            $skills=null;
        }

        $employment_status = EmploymentStatus::query()->where('name', $row['employment_status'])->first()->id;
        $roles = Role::query()
            ->whereIn('name', $this->makeArray($row['roles']))
            ->pluck('id')
            ->toArray();
        [$first_name, $last_name] = array_values($this->getFirstnameLastnameFromName($row['name']));

        DB::transaction(fn() => resolve(EmployeeImportService::class)
            ->setAttrs(array_merge($row, [
                'department_id' => $department,
                'designation_id' => $designation,
                'employment_status_id' => $employment_status,
                'roles' => $roles,
                'first_name' => $first_name,
                'last_name' => $last_name,
                'helmet_id' =>$helmet,
                'gate_passes' => $gate_passes,
                'skills' => $skills

            ]))->saveEmployee()
            ->sendPasswordResetMail()
        );
    }

    public array $requiredHeading = [
        "name",
        "email",
        "gender",
        "employee_id",
        "department",
        "designation",
        "employment_status",
        "roles",
        "salary",
        "joining_date",
        "pme_barcode",
        "gate_passes",
        "skills"
    ];

    public function rules(): array
    {
        return [
            '*.name' => ['required', 'string'],
            '*.email' => [
                'required',
                'email',
                'distinct',
                Rule::unique('users', 'email')
                    ->whereNull('deleted_at')
            ],
            '*.gender' => ['required', 'string', Rule::in(['male', 'female', 'other', 'Male', 'Female', 'Other'])],
            '*.employee_id' => ['required', 'distinct', 'unique:profiles'],
            '*.department' => ['required', 'string', 'exists:departments,name'],
            '*.designation' => ['required', 'string', 'exists:designations,name'],
            '*.employment_status' => ['required', 'string', 'exists:employment_statuses,name'],
            '*.roles' => ['required', 'string', new RoleExistRule],
            '*.salary' => ['nullable', 'numeric'],
            '*.joining_date' => ['nullable', 'date'],
            '*.pme_barcode' => ['nullable', 'numeric','exists:helmets,pme_barcode'],
            '*.gate_passes' => ['nullable','string',new GatePassExistRule],
            '*.skills' => ['nullable', 'string', new SkillExistRule]
        ];
    }

    public function batchSize(): int
    {
        return 100;
    }

    public function chunkSize(): int
    {
        return 100;
    }
}
