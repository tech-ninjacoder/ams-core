<?php

namespace App\Notifications\Tenant;

use App\Http\Composer\Helper\AdministrationPermissions;
use App\Mail\Tag\AlertTag;
use App\Mail\Tag\HelmetTag;
use App\Mail\Tag\ProjectTag;
use App\Mail\Tag\TransferRequestTag;
use App\Mail\Tag\WorkingShiftTag;
use App\Notifications\BaseNotification;
use App\Notifications\Tenant\Helper\CommonParser;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class TransferRequestNotification extends BaseNotification implements ShouldQueue
{
    use Queueable, CommonParser;

    public function __construct($templates, $via, $transfer_request)
    {
        $this->templates = $templates;
        $this->via = $via;
        $this->model = $transfer_request;
        $this->auth = auth()->user();
        $this->tag = TransferRequestTag::class;
        Log::info('notifications transfer-request');
        Log::info(json_encode($templates)."<=TEMP".json_encode($via)."<=VIA".json_encode($transfer_request)."<=transfer_request");


        parent::__construct();
    }

    public function parseNotification()
    {
        $this->databaseNotificationUrl = route('support.transfer_request.all', $this->model).'?tab=Alerts';


        $this->common();
    }
}
