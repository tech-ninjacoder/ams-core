<?php

namespace App\Exports;

use App\Models\Tenant\Project\ProjectUser;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use App\Models\Tenant\Project\Project;


class ExportProjectReport implements FromQuery, WithHeadings
{
    use Exportable;
    /**
     * @var int
     */

    public function __construct(int $id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return Builder
     */
    public function query()
    {


//        return ProjectUser::query()->select('user_id','project_id')->where('project_id', $this->id)->orderBy('project_id', 'DESC');
        return DB::table('projects')
            ->select('p.employee_id','u.first_name','u.last_name','projects.id','projects.name','a.start_date','a.end_date')
            ->join('project_user as a', 'a.project_id', '=', 'projects.id')
            ->join('users as u', 'u.id', '=', 'a.user_id')
            ->join('profiles as p', 'p.user_id', '=', 'a.user_id')
            ->where('projects.id', $this->id)

            ->orderBy('projects.id', 'DESC');
//        return Project::query();



        // TODO: Implement query() method.
    }
    public function headings(): array
    {
        return ["Employee ID", "EMP. First Name", "EMP. Last Name", "Project ID", "Project Name", "Start Date", "End Date"];
    }

}
