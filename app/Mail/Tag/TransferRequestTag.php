<?php


namespace App\Mail\Tag;


use App\Http\Composer\Helper\AdministrationPermissions;

class TransferRequestTag extends Tag
{
    protected $transfer_request;

    public function __construct($transfer_request, $notifier, $receiver)
    {
        $this->transfer_request = $transfer_request;
        $this->notifier = $notifier;
        $this->receiver = $receiver;
        $this->resourceurl = AdministrationPermissions::new(true)->transfer_requestUrl();
    }

    function notification()
    {
        return array_merge([
            '{title}' => $this->transfer_request->title,
        ], $this->common());
    }
}
