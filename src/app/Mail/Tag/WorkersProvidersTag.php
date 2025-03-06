<?php


namespace App\Mail\Tag;


use App\Http\Composer\Helper\AdministrationPermissions;

class WorkersProvidersTag extends Tag
{
    protected $workerprovider;

    public function __construct($workerprovider, $notifier, $receiver)
    {
        $this->workerprovider = $workerprovider;
        $this->notifier = $notifier;
        $this->receiver = $receiver;
        $this->resourceurl = AdministrationPermissions::new(true)->workers_providersUrl();
    }

    function notification()
    {
        return array_merge([
            '{name}' => $this->workerprovider->name,
        ], $this->common());
    }
}
