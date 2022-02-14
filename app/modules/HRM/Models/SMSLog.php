<?php

namespace App\modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;

class SMSLog extends Model
{
    //
    protected $table = 'tbl_sms_log';
    protected $guarded = [];
    
    
    public function unit()
    {
        return $this->belongsTo(District::class, 'last_offer_unit');
    }
}
