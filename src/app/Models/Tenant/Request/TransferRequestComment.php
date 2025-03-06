<?php

namespace App\Models\Tenant\Request;

use App\Models\Tenant\Request\TransferRequest;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransferRequestComment extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'transfer_request_comments';

    protected $fillable = [
        'request_id',
        'comment',
    ];

    public function request()
    {
        return $this->belongsTo(TransferRequest::class);
    }
}
