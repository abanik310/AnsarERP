<?php

namespace App\modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    protected $connection = 'hrm';
    protected $table = 'db_amis.tbl_organization_type';
    protected $guarded = [];
}
