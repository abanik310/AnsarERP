<?php

namespace App\modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;

class PersonalInfoForPromotion extends Model
{
    protected $connection = 'hrm';
    protected $table = 'tbl_ansar_parsonal_info';
    
    public function personalinfo(){
        return $this->hasOne(PersonalInfo::class,'ansar_id');
    }
}
