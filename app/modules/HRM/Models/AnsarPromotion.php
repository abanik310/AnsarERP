<?php

namespace App\modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;

class AnsarPromotion extends Model
{
    protected $connection = 'hrm';
    protected $table = 'tbl_ansar_promotion';
    
    public function personalinfo(){
        return $this->hasOne(PersonalInfo::class,'ansar_id');
    }
}
