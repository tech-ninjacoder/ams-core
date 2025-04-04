<?php

namespace App\Exports;

use App\Models\Core\Auth\User;
use App\Models\Tenant\Attendance\Attendance;
use App\Models\Tenant\Project\ProjectUser;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class ExportAbsenceLog implements FromCollection, WithHeadings, WithTitle
{
    use Exportable;

    /**
     * Title of the export sheet.
     */
    public function title(): string
    {
        return 'Absence Log';
    }

    /**
     * Collection of data for export.
     */
    public function collection()
    {
        $input = request()->all();
        $date = $input['date'];

        $rawAttendance = Attendance::select('user_id')->where('in_date', $date)->pluck('user_id');
        $notEmp = User::select('id')->where('is_in_employee', '=', 1)
            ->whereNull('deleted_at')
            ->where('status_id', 1)
            ->pluck('id');

        $rawAssigned = ProjectUser::select('user_id')
            ->whereIn('user_id', $notEmp)
            ->where('start_date', '<=', $date)
            ->where(function ($query) use ($date) {
                $query->where('end_date', '>=', $date)
                    ->orWhereNull('end_date');
            })
            ->distinct()
            ->pluck('user_id');

        $rawAbsence = User::whereNotIn('id', $rawAttendance)
            ->whereIn('id', $rawAssigned)
            ->with(['profile', 'past_projects' => function ($query) use ($date) {
                $query->where('start_date', '<=', $date)
                    ->where(function ($query) use ($date) {
                        $query->where('end_date', '>=', $date)
                            ->orWhereNull('end_date');
                    });
            }])
            ->where('is_in_employee', '=', 1)
            ->get();

        $result = collect();

        foreach ($rawAbsence as $value) {
            $emp_id = $value->profile->employee_id ?? 0; // Null coalescing operator to simplify

            // Log if no projects are found for a user
            if ($value->past_projects->isEmpty()) {
                Log::info("No projects found for user: " . $value->full_name . " (ID: " . $value->id . ")");
                $projectNames = 'No projects assigned'; // Customize this message
            } else {
                // Fetch project names and join them with commas
                $projectNames = $value->past_projects->pluck('name')->implode(', ');
            }

            $result->push([
                'employee_id' => $emp_id,
                'full_name' => $value->full_name,
                'date' => Carbon::parse($date)->toDateString(),
                'projects' => $projectNames, // Always return project names or a message if none
            ]);
        }

        return $result;
    }


    /**
     * Headings for the exported data.
     */
    public function headings(): array
    {
        return ["Employee ID", "Full Name", "Absence Date", "Projects"];
    }
}