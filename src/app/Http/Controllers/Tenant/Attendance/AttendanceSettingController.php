<?php

namespace App\Http\Controllers\Tenant\Attendance;

use App\Helpers\Traits\TenantAble;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\Attendance\AttendanceSettingsRequest as Request;
use App\Repositories\Core\BaseRepository;
use App\Repositories\Core\Setting\SettingRepository;
use App\Services\Core\Setting\SettingService;
use Illuminate\Support\Facades\Log;

class AttendanceSettingController extends Controller
{
    use TenantAble;

    protected BaseRepository $repository;

    public function __construct(SettingService $service, SettingRepository $repository)
    {
        $this->service = $service;
        $this->repository = $repository;
    }

    public function index()
    {
        [$setting_able_id, $setting_able_type] = $this->tenantAble();

        return $this->repository->getFormattedSettings(
            'attendance', $setting_able_type, $setting_able_id
        );
    }

    public function update(Request $request)
    {
        [$setting_able_id, $setting_able_type] = $this->tenantAble();

        $attributes = array_merge($request->only('punch_in_time_tolerance', 'work_availability_definition'), [
            'auto_approval' => (int)$request->get('auto_approval', 0),
            'users' => is_array($request->get('users')) ? json_encode($request->get('users', [])) : $request->get('users'),
            'roles' => is_array($request->get('roles')) ? json_encode($request->get('roles', [])) : $request->get('roles')
        ]);

        $this->service->saveSettings(
            $attributes,
            'attendance',
            $setting_able_type,
            $setting_able_id
        );

        return updated_responses('attendance_settings');
    }

    public function recurring(Request $request)
    {
        Log::info('request '.json_encode($request->all()));
        [$setting_able_id, $setting_able_type] = $this->tenantAble();

        $attributes = ['recurring_attendance' => (int)$request->get('recurring_attendance', 0)];

        $this->service->saveSettings(
            $attributes,
            'attendance',
            $setting_able_type,
            $setting_able_id
        );

        return updated_responses('attendance_settings');
    }
}
