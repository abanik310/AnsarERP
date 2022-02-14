<?php

namespace App\modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;

class UnitCompany extends Model
{
    protected $connection = 'hrm';
    protected $table = 'tbl_unit_company_ansar_list';
    
    public function personalinfo(){
        return $this->hasOne(PersonalInfo::class,'ansar_id');
    }
}
