<?php

namespace App\modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;

class PromotionLogModel extends Model
{
    protected $connection = 'hrm';
    protected $table = "tbl_ansar_promotion_log";
    protected $guarded = [];

    function ansar()
    {
        return $this->belongsTo(PersonalInfo::class, 'ansar_id', 'ansar_id');
    }

//    function kpi()
//    {
//        return $this->belongsTo(KpiGeneralModel::class, 'kpi_id');
//    }
//
//    public function disembodimentReason()
//    {
//        return $this->hasOne(DisembodimentReason::class, "id","disembodiment_reason_id");
//    }
}
