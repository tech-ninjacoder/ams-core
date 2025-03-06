<?php

namespace App\Notifications\Tenant;

use App\Http\Composer\Helper\AdministrationPermissions;
use App\Mail\Tag\AlertTag;
use App\Mail\Tag\HelmetTag;
use App\Mail\Tag\ProjectTag;
use App\Mail\Tag\WorkingShiftTag;
use App\Notifications\BaseNotification;
use App\Notifications\Tenant\Helper\CommonParser;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class AlertNotification extends BaseNotification implements ShouldQueue
{
    use Queueable, CommonParser;

    public function __construct($templates, $via, $project)
    {
        $this->templates = $templates;
        $this->via = $via;
        $this->model = $project;
        $this->auth = auth()->user();
        $this->tag = AlertTag::class;
//        Log::info($templates."<=TEMP".$via."<=VIA".$project."<=PROJECT");
//        Log::info('helmet');

        parent::__construct();
    }

    public function parseNotification()
    {
        $this->databaseNotificationUrl = route('support.employee.details', $this->model).'?tab=Alerts';


        $this->common();
    }
}
