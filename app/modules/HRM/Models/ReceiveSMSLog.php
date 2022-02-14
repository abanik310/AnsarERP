<?php

namespace App\modules\HRM\Models;
use Illuminate\Database\Eloquent\Model;

class ReceiveSMSLog extends Model
{
    //
    protected $connection = 'hrm';
    protected $table='tbl_receive_sms_log';

    protected $guarded = [];
}
