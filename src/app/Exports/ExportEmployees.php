<?php

namespace App\Exports;

use App\Models\Core\Auth\User;
use App\Models\Tenant\Attendance\Attendance;
use App\Models\Tenant\Project\ProjectUser;
use Carbon\Carbon;
//use http\Env\Request;
use Illuminate\Http\Request;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use App\Models\Tenant\Project\Project;
use Maatwebsite\Excel\Concerns\WithTitle;

class ExportEmployees implements FromArray, WithHeadings, WithTitle
{
    use Exportable;

    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function title(): string
    {
        return 'Employees '.Carbon::today()->toDateString();
    }

    public function array(): array
    {
        if (empty($this->data)) {
            return [];
        }

        $dataArray = array_map(function ($item, $index) {
            return [
                $index + 1,
                $item['profile_employee_id'] ?? '',
                $item['full_name'] ?? '',
                $item['projects_pme_id'] ?? '',
                $item['skills_name'] ?? '',
                $item['employment_status_name'] ?? '',

                $item['projects_location'] ?? '',
                $item['provider_name'] ?? '',
                $item['department_name'] ?? '',
                $item['projects_id'] ?? '',
                $item['coordinatorName'] ?? '',
                $item['workShiftName'] ?? ''
            ];
        }, $this->data, array_keys($this->data));

        return $dataArray;
    }

    public function headings(): array
    {
        return [
            "#",
            "EMP ID",
            "Full Name",
            "PME ID",
            "Skill",
            "Status",
            "Location",
            "Provider",
            "Camp",
            "Project ID",
            "Coordinator",
            "WorkingShift",
        ];
    }
}

