<?php


namespace App\Mail\Tag;


use App\Http\Composer\Helper\AdministrationPermissions;

class ProjectTag extends Tag
{
    protected $project;

    public function __construct($project, $notifier, $receiver)
    {
        $this->project = $project;
        $this->notifier = $notifier;
        $this->receiver = $receiver;
        $this->resourceurl = AdministrationPermissions::new(true)->projectUrl();
    }

    function notification()
    {
        return array_merge([
            '{name}' => $this->project->name,
        ], $this->common());
    }
}
