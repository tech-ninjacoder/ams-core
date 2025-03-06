<?php

namespace App\Notifications\Tenant;

use App\Http\Composer\Helper\AdministrationPermissions;
//use App\Mail\Tag\DepartmentTag;
use App\Mail\Tag\WorkersProvidersTag;
use App\Notifications\BaseNotification;
use App\Notifications\Tenant\Helper\CommonParser;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class WorkersProvidersNotification extends BaseNotification implements ShouldQueue
{
    use Queueable, CommonParser;

    public function __construct($templates, $via, $workerprovider)
    {
        $this->templates = $templates;
        $this->via = $via;
        $this->model = $workerprovider;
        $this->auth = auth()->user();
        $this->tag = WorkersProvidersTag::class;
        parent::__construct();
    }


    public function parseNotification()
    {
        $this->databaseNotificationUrl = AdministrationPermissions::new(true)
            ->workers_providersUrl();

        $this->common();
    }
}
