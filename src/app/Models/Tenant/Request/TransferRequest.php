<?php

namespace App\Models\Tenant\Request;

use App\Models\Core\Auth\User;
use App\Models\Tenant\Employee\Department;
use App\Models\Tenant\Request\Rules\TransferRequestRules;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransferRequest extends Model
{
    use HasFactory;
    use SoftDeletes;
    use TransferRequestRules;

    protected $table = 'transfer_requests';


    protected $fillable = [
        'title',
        'description',
        'status',
        'sender_id',
        'department_id',
    ];
    protected $casts = ['title' => 'string'];


    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function comments()
    {
        return $this->hasMany(TransferRequestComment::class);
    }
}
