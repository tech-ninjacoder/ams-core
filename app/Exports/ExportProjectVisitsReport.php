<?php

namespace App\Exports;

use App\Models\Tenant\Attendance\AttendanceDetails;
use App\Models\Tenant\Project\ProjectUser;
use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Events\BeforeSheet;
use \Maatwebsite\Excel\Sheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

use App\Models\Tenant\Project\Project;
use Maatwebsite\Excel\Events\AfterSheet;


class ExportProjectVisitsReport implements FromQuery, WithHeadings, WithEvents, ShouldAutoSize, WithTitle
{
    use Exportable;
    /**
     * @var int
     */
    private int $id;

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
        return AttendanceDetails::select('pr.employee_id','us.first_name','us.last_name','at.in_date', 'at.behavior', 'in_time','out_time',
            DB::raw('TIMEDIFF(out_time,in_time) as Total'))
            ->join('attendances as at', 'at.id', '=', 'attendance_details.attendance_id')
            ->join('users as us', 'us.id', '=', 'at.user_id')
            ->join('profiles as pr', 'pr.id','=', 'at.user_id')
            ->where('project_id', '=',$this->id);


        // TODO: Implement query() method.
    }
    public function headings(): array
    {
        return ["Employee ID", "EMP. First Name", "EMP. Last Name", "IN Date", "Behavior", "IN Time", "OUT Time", "Visit Total Time"];
    }
    public function registerEvents() :array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $cellRange = 'A2:I2'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(12);
            }
        ];
    }
    public function title(): string
    {
        return "Project Visits-".$this->id;
    }


}
