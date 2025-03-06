<?php

namespace App\Http\Controllers\Tenant\Request;

use App\Exceptions\GeneralException;
use App\Filters\Tenant\DesignationsFilter;
use App\Filters\Tenant\TransferRequestFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\Employee\DesignationRequest;
use App\Http\Requests\Tenant\Request\TransferHttpRequest;
use App\Models\Core\Auth\User;
use App\Models\Tenant\Employee\Department;
use App\Models\Tenant\Employee\Designation;
use App\Models\Tenant\Request\TransferRequest;
use App\Models\Tenant\Request\TransferRequestComment;
use App\Notifications\Tenant\HelmetNotification;
use App\Notifications\Tenant\TransferRequestNotification;
use App\Repositories\Tenant\Employee\DepartmentRepository;
use App\Services\Tenant\Employee\DesignationService;
use App\Services\Tenant\Request\TransferRequestService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TransferRequestController extends Controller
{
    public function __construct(TransferRequestService $service, TransferRequestFilter $filter)
    {
        $this->service = $service;
        $this->filter = $filter;
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $access_behavior = $request->access_behavior;
        $Department = resolve(DepartmentRepository::class)->getDepartments(auth()->id(),'children','user',true);
        $DepartmentManager = resolve(DepartmentRepository::class)->getDepartmentsManagers(auth()->id(),'children','user',true);
        Log::info('user '.$request->access_behavior);
        Log::info('getDepartments '.json_encode($Department));
        Log::info('$DepartmentManager '.json_encode($DepartmentManager));

        $query = $this->service->withCount('comments');

        if ($access_behavior === 'own_departments') {
            $query->orWhere('sender_id', $DepartmentManager)
                ->orWhere('department_id', $Department);
        }

        return $query->latest()->paginate(request()->get('per_page', 10));
    }

    public function store(TransferHttpRequest $request)
    {
        DB::transaction(function () use ($request) {

            $user = Auth::user();

            $request['sender_id'] = $user->id;
            Log::info('$request => ' . json_encode($request->only('title', 'description', 'status', 'sender_id', 'department')));
            $id = $this->service->save($request->only('title', 'description', 'status', 'sender_id', 'department_id'))->id;
            $saved_request = TransferRequest::find($id);
            $department = Department::find($request->department_id);
            Log::info('$department  ' . json_encode($department));

            Log::info('manager  ' . json_encode($department->manager_id));
            $manager = User::find($department->manager_id);
            notify()
                ->on('transfer_request')
                ->with($manager)
                ->send(TransferRequestNotification::class);

            log_to_database('transfer_request', [
                'old' => [],
                'attributes' => $saved_request
            ], 'default', $manager, $saved_request);
        });

        return created_responses('transfer_request');
    }

    public function show(TransferHttpRequest $transferRequest, $id)
    {
        $transferRequest_object = TransferRequest::find($id);
        Log::info('show '.$transferRequest_object);
        return $transferRequest_object;
    }

    public function update(TransferRequest $request , TransferHttpRequest $transferRequest)
    {
        DB::transaction(function () use ($request, $transferRequest) {
            $request->update(
                $transferRequest->only('title', 'status')
            );
            $DepartmentManager = resolve(DepartmentRepository::class)->getDepartmentsManagers($transferRequest->department_id,'children','user',true);
            Log::info('receiver dep '.json_encode($DepartmentManager));

            if (isset($transferRequest->comment)) {
                Log::info('comment ' . $transferRequest->comment);
                $comment = new TransferRequestComment();
                $comment->transfer_request_id = $transferRequest->id;
                $comment->comment = $transferRequest->comment;
                $comment->save();
            }
            notify()
                ->on('transfer_request')
                ->with($transferRequest->receiver_id)
                ->send(TransferRequestNotification::class);
//            notify()
//                ->on('vts_alert')
//                ->with($alert)
//                ->send(AlertNotification::class);
//        notify(new HelmetNotification($employee,'database',1));
            log_to_database('transfer_request', [
                'old' => [],
                'attributes' => $request
            ], 'default', $transferRequest->receiver_id, $request);
//        Log::info($helmet);
        });


        return updated_responses('transfer_request');
    }

    public function destroy(TransferRequest $transferRequest)
    {

        // Delete the transfer request
        $transferRequest->delete();

        return deleted_responses('transfer_request');
    }




}
