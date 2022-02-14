<?php

namespace App\modules\recruitment\Models;

use App\modules\HRM\Models\District;
use App\modules\HRM\Models\Division;
use App\modules\HRM\Models\EmbodimentLogModel;
use App\modules\HRM\Models\EmbodimentModel;
use App\modules\HRM\Models\PersonalInfo;
use App\modules\HRM\Models\Thana;
use App\modules\HRM\Models\BlackListModel;
use App\modules\HRM\Models\FreezingInfoModel;
use App\modules\HRM\Models\BlockListModel;
use App\modules\HRM\Models\Designation;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image;
class JobAppliciant extends Model
{
    //
    protected $table = 'job_applicant';
    protected $connection = 'recruitment';
    protected $guarded = ['id', 'job_circular_id'];
    protected $fillable = ['roll_no'];

    public function circular()
    {
        return $this->belongsTo(JobCircular::class, 'job_circular_id');
    }
    public function quotaType(){
        return $this->belongsTo(CircularApplicantQuota::class,'circular_applicant_quota_id');
    }
    public function ansar()
    {
        return $this->belongsTo(PersonalInfo::class, 'ansar_id','ansar_id');
    }

    public function division()
    {
        return $this->belongsTo(Division::class, 'division_id');
    }
    public function present_division()
    {
        return $this->belongsTo(Division::class, 'present_division_id');
    }
    public function govQuota()
    {
        return $this->hasOne(JobGovQuota::class, 'job_applicant_id');
    }

    public function district()
    {
        return $this->belongsTo(District::class, 'unit_id');
    }
	
	/* added by rintu for ansar new requirment */
	
	 public function freezlist()
    {
        return $this->belongsTo(FreezingInfoModel::class, 'ansar_id','ansar_id');
    }
    
      public function blocklist()
    {
        return $this->belongsTo(BlockListModel::class, 'ansar_id','ansar_id');
    }
    
       public function blacklist()
    {
        return $this->belongsTo(BlackListModel::class, 'ansar_id','ansar_id');
    }
	
	   public function designationdata()
    {
        return $this->belongsTo(Designation::class, 'designation');
    }
	
	 public function isVerified()
    {
       
	    if (!$this->verified) return 'NA';
	    // For Ansar Experience Calculation
		if($this->verified == 0){
			 
			 return 'not verified,';
		}   
		
       
    }
	
	 public function expCurrentDesignation()
    {
       //return 0;
	    if (!$this->ansar_id) return 0;
	    // For Ansar Experience Calculation
		if($this->designation == 1){
			 
			 $exp = $this->expCalculation();
			 return $exp; 
		}   
		
		// For APC Experience Calculation
		if($this->designation == 2){
			/* $apc_previous_data = DB::select("SELECT t2.created_at FROM job_applicant t1 
				 INNER JOIN job_selected_applicant t2 ON t1.applicant_id=t2.applicant_id
				 INNER JOIN db_amis.tbl_ansar_parsonal_info t3 ON t1.ansar_id=t3.ansar_id
				 INNER JOIN job_applicant t4 ON t1.ansar_id=t4.ansar_id AND t4.job_circular_id=122
				 WHERE t1.job_circular_id=21 AND t3.designation_id=2 AND t4.ansar_id=".$this->ansar_id); */
				 
			$query = DB::table('ansar_recruitment.job_applicant as dt1')
			 ->join('ansar_recruitment.job_selected_applicant as dt2', 'dt2.applicant_id', '=', 'dt1.applicant_id')  
			 ->join('db_amis.tbl_ansar_parsonal_info as dt3', 'dt1.ansar_id', '=', 'dt1.ansar_id')       
			 ->join('ansar_recruitment.job_applicant as dt4', 'dt1.ansar_id', '=', 'dt4.ansar_id') 
			 ->where('dt1.job_circular_id', 21)
			 ->where('dt1.ansar_id', $this->ansar_id)			 
			 ->where('dt3.designation_id', 2)
			 ->where('dt4.job_circular_id', 122);
			
			$apc_previous_data = $query->select(['dt2.created_at'])->get();
            //print_r($apc_previous_data);    
				
			    
			if ($apc_previous_data) {
                 $currentExp = $this->yearDiff(date('Y-m-d'), $apc_previous_data[0]->created_at);
				 return $currentExp;
				  			//	return 'NA';   

            }else{
				return 'NA';       
			}	       
			
		}    
		
		// For PC Experience Calculation
		if($this->designation == 3){
			return 'NA';
		}
		
       
    }

	
	  public function expCurrentCalculation()
    {
        if (!$this->ansar_id) return 0;
        $currentExp = $prevExp = 0;
        $currentEmbodiment = EmbodimentModel::where("ansar_id", $this->ansar_id)->first();
        if ($currentEmbodiment) {
            $currentExp = $this->yearDiff(date('Y-m-d'), $currentEmbodiment->joining_date);
        }
        /* $embodimentHistory = EmbodimentLogModel::where("ansar_id", $this->ansar_id)->get();
        if ($embodimentHistory->count() > 0) {
            foreach ($embodimentHistory as $history) {
                $prevExp += $this->yearDiff($history->joining_date, $history->release_date);
            }
        } */
        return $currentExp;
    }

	
	/* added by rintu for ansar new requirment */
	
    public function present_district()
    {
        return $this->belongsTo(District::class, 'present_unit_id');
    }

    public function thana()
    {
        return $this->belongsTo(Thana::class, 'thana_id');
    }
    public function present_thana()
    {
        return $this->belongsTo(Thana::class, 'present_thana_id');
    }

    public function appliciantEducationInfo()
    {
        return $this->hasMany(JobAppliciantEducationInfo::class, 'job_applicant_id');
    }

    public function payment()
    {
        return $this->hasOne(JobAppliciantPaymentHistory::class, 'job_appliciant_id', 'applicant_id');
    }

    public function quota()
    {
        return $this->belongsTo(JobApplicantQuota::class, 'unit_id', 'district_id');
    }

    public function getDateOfBirthAttribute($value)
    {
        if ($value) {
            return Carbon::parse($value)->format('d-M-Y');
        }
        return null;
    }
	public function getProfilePicAttribute($value){
		try{
			$path = storage_path("receruitment");
			if(!file_exists($path)){
				if(mkdir($path)){
					$file_parts = explode("/",$value);
					$file_name = explode(".",$file_parts[count($file_parts)-1])[0].".jpg";
					Image::make($value)->resize(250,250)->encode("jpg")->save($path."/".$file_name);
					return $path."/".$file_name;
				}
				return $value;

			}else{
				$file_parts = explode("/",$value);
				$file_name = explode(".",$file_parts[count($file_parts)-1])[0].".jpg";
				if(file_exists($path."/".$file_name)) return $path."/".$file_name;
				Image::make($value)->resize(250,250)->encode("jpg")->save($path."/".$file_name);
				return $path."/".$file_name;
			}
		}catch(\Exception $e){
            return false;
        }
    }
	public function getSignaturePicAttribute($value){
		try{
			$path = storage_path("receruitment");
			if(!file_exists($path)){
				if(mkdir($path)){
					$file_parts = explode("/",$value);
					$file_name = explode(".",$file_parts[count($file_parts)-1])[0].".jpg";
					Image::make($value)->resize(250,250)->encode("jpg")->save($path."/".$file_name);
					return $path."/".$file_name;
				}
				return $value;

			}else{
				$file_parts = explode("/",$value);
				$file_name = explode(".",$file_parts[count($file_parts)-1])[0].".jpg";
				if(file_exists($path."/".$file_name)) return $path."/".$file_name;
				Image::make($value)->resize(250,250)->encode("jpg")->save($path."/".$file_name);
				return $path."/".$file_name;
			}
		}catch(\Exception $e){
            return false;
        }
    }
    public function getColumns()
    {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }

    public function editHistory()
    {
        return $this->hasMany(JobApplicantEditHistory::class, 'applicant_id');
    }

    public function selectedApplicant()
    {
        return $this->hasOne(JobSelectedApplicant::class, 'applicant_id', 'applicant_id');
    }

    public function accepted()
    {
        return $this->hasOne(JobAcceptedApplicant::class, 'applicant_id', 'applicant_id');
    }
    public function rejected()
    {
        return $this->hasOne(JobRejectedApplicant::class, 'applicant_id', 'applicant_id');
    }

    public function marks()
    {
        return $this->hasOne(JobApplicantMarks::class, 'applicant_id', 'applicant_id');
    }
    public function circularQuota()
    {
        return $this->belongsTo(CircularApplicantQuota::class, 'circular_applicant_quota_id');
    }

    public function hrmDetail()
    {
        return $this->hasOne(JobApplicantHRMDetails::class, 'applicant_id', 'applicant_id');
    }
    public function hrmDetailTrashed()
    {
        return $this->hrmDetail()->onlyTrashed();
    }

    public function education()
    {
        return $this->belongsToMany(JobEducationInfo::class, 'job_appliciant_education_info', 'job_applicant_id', 'job_education_id')
            ->withPivot('subject','institute_name','passing_year','board_university','gade_divission');
    }

    public function physicalPoint()
    {
        if ($this->status != 'selected') {
            return -1;
        }
        if (!$this->circular->point()->exists() || !$this->circular->point()->where('point_for', 'physical')->exists()) {
            return "--";
        }
        $rules = $this->circular->point()->where('point_for', 'physical')->where('rule_name', 'height')->first()->rules;
        return $this->heightPoint($rules);
    }

    public function educationTrainingPoint()
    {
        if ($this->status != 'selected') {
            return -1;
        }
        if (!$this->circular->point()->exists() || !$this->circular->point()->where('point_for', 'edu_training')->exists()) {
            return "--";
        }
        $rules = $this->circular->point()->where('point_for', 'edu_training')->get();
        $edu_point = 0;
        $q = $this->circular_applicant_quota_id;
        foreach ($rules as $rule) {
            if ($rule->rule_name === 'education') {
                $edu_point += $this->eduPoint(json_decode(json_encode($rule->rules->{$q}), true));
            } else if ($rule->rule_name === 'training'&&($this->training_info=="VDP training"||$this->training_info=="TDP training")) {
                $edu_point += floatval($rule->rules->{$q}->training_point);
            }
        }
        /*$point_table = [
          4=>10,
          7=>8,
          8=>6,
          9=>4,
          10=>2
        ];
        $order = 'desc';
        $education_priority = $this->education()->orderBy('priority',$order)->first()['priority'];
//        $point_table[$education_priority] = 0;
        $training_point = $this->training_info=='VDP training'||$this->training_info=='TDP training'?5:0;*/
        return $edu_point;
    }

    public function educationExperiencePoint()
    {
        if ($this->status != 'selected') {
            return -1;
        }
        if (!$this->circular->point()->exists() || !$this->circular->point()->where('point_for', 'edu_experience')->exists()) {
            return "--";
        }
        $rules = $this->circular->point()->where('point_for', 'edu_experience')->get();
        $expRule = collect($rules)->where('rule_name', 'experience')->first();
        $eduRule = collect($rules)->where('rule_name', 'education')->first();
        $eduPoint = $point = 0;
        $q = $this->circular_applicant_quota_id;
        if ($eduRule) {
            $eduPoint = $this->eduPoint(json_decode(json_encode($eduRule->rules->{$q}), true));
        }
        if ($expRule) {
            $exp = $this->expCalculation();
            $min_exp_years = floatval($expRule->rules->{$q}->min_experience_years);
            $max_exp_years = floatval($expRule->rules->{$q}->max_experience_years);
            $min_point = floatval($expRule->rules->{$q}->min_exp_point);
            $max_point = floatval($expRule->rules->{$q}->max_exp_point);
            $delta_height = $max_exp_years - $min_exp_years;
            $delta_point = $max_point - $min_point;
            if ($exp >= $max_exp_years) $point = $max_point;
            else $point = number_format(($delta_point / $delta_height) * (($exp - $min_exp_years)) + $min_point, 2);
        }
        return $point + $eduPoint < 0 ? 0 : $point + $eduPoint;
    }
    public function physicalAgePoint()
    {
        if ($this->status != 'selected') {
            return -1;
        }
        if (!$this->circular->point()->exists() || !$this->circular->point()->where('point_for', 'physical_age')->exists()) {
            return "--";
        }
        $rules = $this->circular->point()->where('point_for', 'physical_age')->get();
        $ageRule = collect($rules)->where('rule_name', 'age')->first();
        $physicalRule = collect($rules)->where('rule_name', 'height')->first();
        $physical = $point = 0;
        $q = $this->circular_applicant_quota_id;
        if ($physicalRule) {
            $physical = $this->heightPoint($physicalRule->rules);
        }
        if ($ageRule) {
            $age = $this->yearDiff($this->date_of_birth,Carbon::now()->format('Y-m-d'));
            $min_age_years = floatval($ageRule->rules->{$q}->min_age_years);
            $max_age_years = floatval($ageRule->rules->{$q}->max_age_years);
            $min_point = floatval($ageRule->rules->{$q}->min_age_point);
            $max_point = floatval($ageRule->rules->{$q}->max_age_point);
            $delta_age = $max_age_years - $min_age_years;
            $delta_point = $max_point - $min_point;
            if ($age >= $max_age_years) $point = $ageRule->rules->{$q}->priority?$max_point:$min_point;
            else if($ageRule->rules->{$q}->priority) $point = number_format(($delta_point / $delta_age) * (($age - $min_age_years)) + $min_point, 2);
            else  $point = number_format(($delta_point / $delta_age) * (($max_age_years - $age)) + $min_point, 2);
        }
        return $point + $physical < 0 ? 0 : $point + $physical;
    }

    private function eduPoint($rules)
    {
        $point_table = array_values($rules['edu_point']);
        $epp = intval($rules['edu_p_count']);
        Log::info($this->education);
        if ($epp === 1) {
            $education_priority = $this->education()->orderBy('priority', 'asc')->first()['priority'];
            Log::info("education_priority".$education_priority);
            $key = array_search($education_priority, array_column($point_table, 'priority'));
            $point = intval($point_table[$key]['point']);
        } else if ($epp === 2) {

            $education_priority = $this->education()->orderBy('priority', 'desc')->first()['priority'];
            Log::info("education_priority".$education_priority);
            $key = array_search($education_priority, array_column($point_table, 'priority'));
            $point = intval($point_table[$key]['point']);
        } else {
            $p = collect($point_table)->pluck('priority');
            $education_priority = $this->education()->whereIn('priority', $p)->pluck('priority');
            $point = collect($point_table)->whereIn('priority', $education_priority)->sum('point');
        }
        Log::info("edu_point".$point);
        Log::info($point_table);
        return $point;

    }
    private function heightPoint($rules){
        $gender = strtolower($this->gender);
        $quota = $this->circular_applicant_quota_id;
        $min_height = floatval($rules->{$quota}->{$gender}->min_height_feet) * 12 + floatval($rules->{$quota}->{$gender}->min_height_inch);
        $max_height = floatval($rules->{$quota}->{$gender}->max_height_feet) * 12 + floatval($rules->{$quota}->{$gender}->max_height_inch);
        $min_point = floatval($rules->{$quota}->{$gender}->min_point);
        $max_point = floatval($rules->{$quota}->{$gender}->max_point);
        $total_height = floatval($this->height_feet) * 12 + floatval($this->height_inch);
        $delta_height = $max_height - $min_height;
        $delta_point = $max_point - $min_point;
        if ($total_height >= $max_height) return $max_point;
//        return number_format(($delta_point / $delta_height) * (($total_height - $min_height)) + $min_point,2);
        return ($delta_point / $delta_height) * (($total_height - $min_height)) + $min_point;

    }
    public function expCalculation()
    {
        if (!$this->ansar_id) return 0;
        $currentExp = $prevExp = 0;
        $currentEmbodiment = EmbodimentModel::where("ansar_id", $this->ansar_id)->first();
        if ($currentEmbodiment) {
            $currentExp = $this->yearDiff(date('Y-m-d'), $currentEmbodiment->joining_date);
        }
        $embodimentHistory = EmbodimentLogModel::where("ansar_id", $this->ansar_id)->get();
        if ($embodimentHistory->count() > 0) {
            foreach ($embodimentHistory as $history) {
                $prevExp += $this->yearDiff($history->joining_date, $history->release_date);
            }
        }
        return $currentExp + $prevExp;
    }
	
	
    private function yearDiff($d1, $d2)
    {
        $d1 = new \DateTime($d1);
        $d2 = new \DateTime($d2);
        $diff = $d1->diff($d2);
        return $diff->y+($diff->m/12)+($diff->d/365);
    }

    public function getQuotaDetailsAttribute($value){
        if(!isset($value)||empty($value)) return "";
        $quotaObj = json_decode($value);
        if(isset($quotaObj->quota) && $quotaObj->quota=='0') return "কোটা নাই";
        else{
            $quota = DB::table('job_circular_applicant_quota')
                ->where('job_circular_applicant_quota.id', $quotaObj->quota)->first();
            if($quota){
                return $quota->quota_name_bng;
            }else{
                return "";
            }
        }
    }
}