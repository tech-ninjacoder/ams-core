<?php


namespace App\Mail\Tag;


use App\Http\Composer\Helper\AdministrationPermissions;

class HelmetTag extends Tag
{
    protected $helmet;

    public function __construct($helmet, $notifier, $receiver)
    {
        $this->helmet = $helmet;
        $this->notifier = $notifier;
        $this->receiver = $receiver;
        $this->resourceurl = AdministrationPermissions::new(true)->HelmetUrl();
    }

    function notification()
    {
        return array_merge([
            '{name}' => $this->helmet->pme_barcode,
        ], $this->common());
    }
}
