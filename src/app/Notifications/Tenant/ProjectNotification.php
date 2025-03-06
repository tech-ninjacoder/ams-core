<?php

namespace App\Notifications\Tenant;

use App\Http\Composer\Helper\AdministrationPermissions;
use App\Mail\Tag\ProjectTag;
use App\Mail\Tag\WorkingShiftTag;
use App\Notifications\BaseNotification;
use App\Notifications\Tenant\Helper\CommonParser;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProjectNotification extends BaseNotification implements ShouldQueue
{
    use Queueable, CommonParser;

    public function __construct($templates, $via, $project)
    {
        $this->templates = $templates;
        $this->via = $via;
        $this->model = $project;
        $this->auth = auth()->user();
        $this->tag = ProjectTag::class;
        parent::__construct();
    }

    public function parseNotification()
    {
        $this->databaseNotificationUrl = AdministrationPermissions::new(true)
            ->projectUrl();

        $this->common();
    }
}
