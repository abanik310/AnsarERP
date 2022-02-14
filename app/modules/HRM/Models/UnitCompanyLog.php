<?php

namespace App\modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;

class UnitCompanyLog extends Model
{
    protected $connection = 'hrm';
    protected $table = 'tbl_unit_company_log';
    
    public function personalinfo(){
        return $this->hasOne(PersonalInfo::class,'ansar_id');
    }
}
