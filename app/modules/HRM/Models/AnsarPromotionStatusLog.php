<?php

namespace App\modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;

class AnsarPromotionStatusLog extends Model
{
    protected $connection = 'hrm';
    protected $table = 'tbl_ansar_promotion_status_log';
    
    public function personalinfo(){
        return $this->hasOne(PersonalInfo::class,'ansar_id');
    }
}
