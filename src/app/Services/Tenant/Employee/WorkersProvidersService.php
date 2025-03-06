<?php


namespace App\Services\Tenant\Employee;


use App\Exceptions\GeneralException;
use App\Helpers\Core\Traits\HasWhen;
use App\Models\Core\Auth\Role;
use App\Models\Core\Auth\User;
use App\Models\Tenant\Employee\Department;
use App\Models\Tenant\Employee\DepartmentUser;
use App\Models\Tenant\Employee\WorkersProviders;
use App\Models\Tenant\Employee\WorkersProvidersUser;
use App\Models\Tenant\WorkingShift\DepartmentWorkingShift;
use App\Models\Tenant\WorkingShift\UpcomingDepartmentWorkingShift;
use App\Models\Tenant\WorkingShift\UpcomingUserWorkingShift;
use App\Models\Tenant\WorkingShift\WorkingShift;
use App\Models\Tenant\WorkingShift\WorkingShiftUser;
use App\Notifications\Tenant\DepartmentNotification;
use App\Notifications\Tenant\WorkersProvidersNotification;
use App\Repositories\Tenant\Employee\DepartmentRepository;
use App\Services\Tenant\TenantService;
use Illuminate\Validation\ValidationException;

class WorkersProvidersService extends TenantService
{
    use HasWhen;

    protected Role $role;
    protected User $current_manager;

    public bool $isUpdate = false;
    private $worker_provider_id;

    public function __construct(WorkersProviders $workerprovider, Role $role)
    {
        $this->model = $workerprovider;
        $this->role = $role;
    }
    public function moveEmployee()
    {
        $this->endEmployeesPreviousDepartment()
            ->moveToDepartment();

        return $this;
    }

    public function endEmployeesPreviousDepartment()
    {
        WorkersProvidersUser::whereIn('user_id', $this->getAttribute('users'))
            ->whereNull('end_date')
            ->where('worker_provider_id', '!=', $this->getWorkerProviderId())
            ->update([
                'end_date' => nowFromApp()->format('Y-m-d')
            ]);

        return $this;
    }
    public function getWorkerProviderId()
    {
        return $this->worker_provider_id ?: $this->model->id;
    }
    public function moveToDepartment()
    {
        $workersproviders_users = collect(WorkersProvidersUser::getNoneExistedUsers(
            $this->getWorkerProviderId(),
            $this->getAttribute('users')
        ))->map(fn ($user) => [
            'worker_provider_id' => $this->getWorkerProviderId(),
            'user_id' => $user,
            'start_date' => nowFromApp()->format('Y-m-d')
        ])->toArray();

        WorkersProvidersUser::insert($workersproviders_users);


        return $this;
    }

    public function notify($event, $workerprovider = null)
    {
        $model = $workerprovider ?: $this->model;

        notify()
            ->on($event)
            ->with($model)
            ->send(WorkersProvidersNotification::class);

        return $this;
    }


    public function departmentManagerRole()
    {
        $role = $this->role
            ->where('is_default', 1)
            ->where('is_admin', 0)
            ->where('alias', 'department_manager')
            ->where('tenant_id', tenant()->id)
            ->first();

        if ($role) {
            return $role;
        }

        throw new GeneralException(__t('manager_role_not_found'));
    }


    public function update()
    {
        $this->model->fill($this->getAttributes());

        $this->when($this->model->isDirty(), function (WorkersProvidersService $service) {
            $service->notify('department_updated');
        });

        $this->model->save();

        return $this;
    }


    public function setIsUpdate(bool $value): self
    {
        $this->isUpdate = $value;

        return $this;
    }

}
