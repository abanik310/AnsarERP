<?php

namespace App\modules\HRM\Models;
use App\models\User;

use Illuminate\Database\Eloquent\Model;

class RestInfoLogModel extends Model
{
    protected $connection = 'hrm';
    protected $table="tbl_rest_info_log";
    protected $guarded = [];

    public function reason()
    {
        return $this->hasOne(DisembodimentReason::class,'id','disembodiment_reason_id');
    }
    
       public function user_details()
    {
        return $this->belongsTo(User::class, 'action_user_id');
    }
    
}
