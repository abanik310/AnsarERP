<?php

namespace App\modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;

class AnsarStatusfoForPromotion extends Model
{
    protected $connection = 'hrm';
    protected $table = 'tbl_ansar_status_info';
    
    public function personalinfo(){
        return $this->hasOne(PersonalInfo::class,'ansar_id');
    }
}
