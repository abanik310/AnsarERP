<?php

namespace App\modules\HRM\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\modules\HRM\Models\District;
use App\modules\HRM\Models\MemorandumModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

class LetterController extends Controller
{
    //
    function transferLetterView()
    {
        return View::make('HRM::Letter.transfer_letter');
    }

    function getMemorandumIds(Request $requests)
    {
//        return $requests->all();
        DB::enableQueryLog();
        $t = DB::table('tbl_memorandum_id')
            ->join('tbl_transfer_ansar', 'tbl_transfer_ansar.transfer_memorandum_id', '=', 'tbl_memorandum_id.memorandum_id')
            ->join('tbl_kpi_info', 'tbl_transfer_ansar.transfered_kpi_id', '=', 'tbl_kpi_info.id')
            ->select('tbl_memorandum_id.*')->groupBy('tbl_memorandum_id.memorandum_id');
        $e = DB::table('tbl_memorandum_id')
            ->join('tbl_embodiment', 'tbl_embodiment.memorandum_id', '=', 'tbl_memorandum_id.memorandum_id')
            ->join('tbl_kpi_info', 'tbl_embodiment.kpi_id', '=', 'tbl_kpi_info.id')
            ->select('tbl_memorandum_id.*')->groupBy('tbl_memorandum_id.memorandum_id');
        $d = DB::table('tbl_memorandum_id')
            ->join('tbl_rest_info', 'tbl_rest_info.memorandum_id', '=', 'tbl_memorandum_id.memorandum_id')
            ->join('tbl_embodiment_log', 'tbl_rest_info.ansar_id', '=', 'tbl_embodiment_log.ansar_id')
            ->join('tbl_kpi_info', 'tbl_embodiment_log.kpi_id', '=', 'tbl_kpi_info.id')
            ->whereRaw("`tbl_rest_info`.`rest_date` = `tbl_embodiment_log`.`release_date`")
            ->select('tbl_memorandum_id.*')->groupBy('tbl_memorandum_id.memorandum_id');
			
		$f = DB::table('tbl_memorandum_id')
            ->join('tbl_freezing_info', 'tbl_freezing_info.memorandum_id', '=', 'tbl_memorandum_id.memorandum_id')
            ->join('tbl_kpi_info', 'tbl_freezing_info.kpi_id', '=', 'tbl_kpi_info.id')
            ->select('tbl_memorandum_id.*')->groupBy('tbl_memorandum_id.memorandum_id');
			
        if($requests->unit){
            $e->where('tbl_kpi_info.unit_id',$requests->unit);
			$f->where('tbl_kpi_info.unit_id',$requests->unit);
            $t->where('tbl_kpi_info.unit_id',$requests->unit);
            $d->where('tbl_kpi_info.unit_id',$requests->unit)->orderBy('tbl_embodiment_log.id','desc');
        }
        if($requests->q){
            $e->where('tbl_memorandum_id.memorandum_id','LIKE','%'.$requests->q.'%');
            $f->where('tbl_memorandum_id.memorandum_id','LIKE','%'.$requests->q.'%');
            $t->where('tbl_memorandum_id.memorandum_id','LIKE','%'.$requests->q.'%');
            $d->where('tbl_memorandum_id.memorandum_id','LIKE','%'.$requests->q.'%');
        }
        //$d->distinct('tbl_rest_info.memorandum_id')->paginate(20);
        //return DB::getQueryLog();
        switch ($requests->type) {
            case 'TRANSFER':
                //return $t->distinct()->paginate(20);
                return view('HRM::Letter.partial_letter_view',['data'=>$t->distinct()->paginate(20),'units'=>District::all(),'type'=>'TRANSFER']);
            case 'EMBODIMENT':
                return view('HRM::Letter.partial_letter_view',['data'=>$e->distinct()->paginate(20),'units'=>District::all(),'type'=>'EMBODIMENT']);
            case 'DISEMBODIMENT':
                return view('HRM::Letter.partial_letter_view',['data'=>$d->distinct('tbl_rest_info.memorandum_id')->paginate(20),'units'=>District::all(),'type'=>'DISEMBODIMENT']);
            case 'FREEZE':
                return view('HRM::Letter.partial_letter_view',['data'=>$f->distinct()->paginate(20),'units'=>District::all(),'type'=>'FREEZE']);
            default:
                return [];
        }

    }

    function printLetter(Request $request)
    {
//        return $request->all();
        $id = Input::get('id');
        $type = Input::get('type');
        $unit = Input::get('unit');
        $view = Input::get('view');
        $option = Input::get('option');
        $rules = [
            'type' => 'regex:/^[A-Z]+$/',
            'unit' => 'numeric|regex:/^[0-9]+$/',
        ];
        $valid = Validator::make(Input::all(), $rules);

        if ($valid->fails()) {
            //return print_r($valid->messages());
            return response("Invalid Request(400)", 400);
        }
        switch ($type) {
            case 'TRANSFER':
                return $this->transferLetterPrint($id, $unit, $view,$option);
            case 'EMBODIMENT':
                return $this->embodimentLetterPrint($id, $unit, $view,$option);
            case 'DISEMBODIMENT':
                return $this->disembodimentLetterPrint($id, $unit, $view,$option);
            case 'FREEZE':
                return $this->freezLetterPrint($id, $unit, $view,$option);
        }
    }

    function transferLetterPrint($id, $unit, $v,$option)
    {
        //DB::enableQueryLog();
        $mem = DB::table('tbl_memorandum_id')
            ->join('tbl_transfer_ansar','tbl_transfer_ansar.transfer_memorandum_id','=','tbl_memorandum_id.memorandum_id')
            ->distinct('tbl_memorandum_id.memorandum_id')->orderBy('tbl_memorandum_id.created_at','desc')->select('tbl_memorandum_id.memorandum_id as memorandum_id', 'mem_date as created_at');
        $user = DB::table('tbl_user')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_user.id')
            ->join('tbl_units', 'tbl_units.id', '=', 'tbl_user.district_id')
            ->join('tbl_division', 'tbl_units.division_id', '=', 'tbl_division.id')
            ->where('tbl_user.district_id', $unit)->select('tbl_units.unit_name_eng as unit_eng','tbl_user_details.first_name', 'tbl_user_details.last_name', 'tbl_user_details.mobile_no', 'tbl_user_details.email', 'tbl_units.unit_name_bng as unit','tbl_division.division_name_eng as division','tbl_division.division_name_bng as division_bng')->first();
        $result = DB::table('tbl_transfer_ansar')
            ->join('tbl_kpi_info as pk', 'tbl_transfer_ansar.present_kpi_id', '=', 'pk.id')
            ->join('tbl_kpi_info as tk', 'tbl_transfer_ansar.transfered_kpi_id', '=', 'tk.id')
            ->join('tbl_thana as tk_thana', 'tk_thana.id', '=', 'tk.thana_id')
            ->join('tbl_thana as pk_thana', 'pk_thana.id', '=', 'pk.thana_id')
            ->join('tbl_ansar_parsonal_info', 'tbl_transfer_ansar.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
            ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
            ->where('pk.unit_id',$unit)
            ->orderBy('tbl_transfer_ansar.created_at','desc')			
            ->select('pk_thana.thana_name_bng as pk_thana','tk_thana.thana_name_bng as tk_thana','pk.unit_id','tbl_ansar_parsonal_info.ansar_id as ansar_id', 'tbl_ansar_parsonal_info.ansar_name_bng as name', 'tbl_ansar_parsonal_info.father_name_bng as father_name', 'tbl_designations.name_bng as rank', 'pk.kpi_name as p_kpi_name', 'tk.kpi_name as t_kpi_name');
        if($option=='smartCardNo'){
            $l  = strlen($id.'');
            if($l>6) $id = substr($id.'',6);
			$result->limit(1);
            $result->where('tbl_ansar_parsonal_info.ansar_id',$id);
            $mem->where('tbl_transfer_ansar.ansar_id',$id);
        }
        else{
            $result->where('tbl_transfer_ansar.transfer_memorandum_id', $id);
            $mem->where('tbl_transfer_ansar.transfer_memorandum_id', $id);
        }
        $result = DB::table(DB::raw('('.$result->toSql().') x'))->mergeBindings($result)->get();
		//print_r(DB::getQueryLog());
		//echo '<pre>'; print_r($result); exit;
        $mem = $mem->first();
        if($user->unit_eng=="CHITTAGONGNORTH" || $user->unit_eng=="CHITTAGONGSOUTH" || $user->unit_eng=="CHITTAGONGADMIN")
            $user->unit_short="চট্টগ্রাম";
        elseif ($user->unit_eng=="DHAKAADMIN"||$user->unit_eng=="DHAKAEAST"||$user->unit_eng=="DHAKAWEST"||$user->unit_eng=="DHAKASOUTH"||$user->unit_eng=="DHAKANORTH")
            $user->unit_short = "ঢাকা";
        else $user->unit_short = $user->unit;
        if ($mem && $result) {
            return View::make('HRM::Letter.master')->with(['mem' => $mem, 'user' => $user, 'result' => $result, 'view' => 'print_transfer_letter']);
        } else {
            return View::make('HRM::Letter.no_mem_found')->with(['id' => $id]);
        }
    }

    function embodimentLetterPrint($id, $unit, $v,$option)
    {
		
        $mem = DB::table('tbl_embodiment')
            ->leftJoin('tbl_memorandum_id','tbl_memorandum_id.memorandum_id','=','tbl_embodiment.memorandum_id')
            ->select('tbl_memorandum_id.memorandum_id', 'tbl_memorandum_id.mem_date as created_at');
        $user = DB::table('tbl_user')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_user.id')
            ->join('tbl_units', 'tbl_units.id', '=', 'tbl_user.district_id')
            ->join('tbl_division', 'tbl_units.division_id', '=', 'tbl_division.id')
            ->where('tbl_user.district_id', $unit)->select('tbl_units.unit_name_eng as unit_eng','tbl_user_details.first_name', 'tbl_user_details.last_name', 'tbl_user_details.mobile_no', 'tbl_user_details.email', 'tbl_units.unit_name_bng as unit','tbl_division.division_name_eng as division','tbl_division.division_name_bng as division_bng')->first();
        $result = DB::table('tbl_embodiment')
            ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment.kpi_id')
            ->join('tbl_ansar_parsonal_info', 'tbl_embodiment.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
            ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
            ->join('tbl_thana', 'tbl_thana.id', '=', 'tbl_ansar_parsonal_info.thana_id')
            ->join('tbl_thana as kt', 'kt.id', '=', 'tbl_kpi_info.thana_id')
            ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')		
            ->select('tbl_ansar_parsonal_info.ansar_id as ansar_id', 'tbl_ansar_parsonal_info.ansar_name_bng as name', 'tbl_kpi_info.unit_id','tbl_ansar_parsonal_info.father_name_bng as father_name', 'tbl_designations.name_bng as rank', 'tbl_kpi_info.kpi_name as kpi_name', 'tbl_ansar_parsonal_info.village_name as village_name', 'tbl_ansar_parsonal_info.post_office_name as pon', 'tbl_units.unit_name_bng as unit', 'tbl_thana.thana_name_bng as thana', 'tbl_embodiment.joining_date','kt.thana_name_bng as kpi_thana');
		
		if($unit != 0){
			$result->where('tbl_kpi_info.unit_id',$unit);
		}	
			
        if($option=='smartCardNo'){
            $l  = strlen($id.'');
            if($l>6) $id = substr($id.'',6);
            $result->where('tbl_ansar_parsonal_info.ansar_id',$id);
            $mem->where('tbl_embodiment.ansar_id',$id);
        }
        else{
            $result->where('tbl_embodiment.memorandum_id', $id);
            $mem->where('tbl_embodiment.memorandum_id', $id);
        }
        $result = $result->get();

		
        $mem = $mem->first();
       
        if ($mem && $result) {
			$user = DB::table('tbl_user')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_user.id')
            ->join('tbl_units', 'tbl_units.id', '=', 'tbl_user.district_id')
            ->join('tbl_division', 'tbl_units.division_id', '=', 'tbl_division.id')
            ->where('tbl_user.district_id', $result[0]->unit_id)->select('tbl_units.unit_name_eng as unit_eng','tbl_user_details.first_name', 'tbl_user_details.last_name', 'tbl_user_details.mobile_no', 'tbl_user_details.email', 'tbl_units.unit_name_bng as unit','tbl_division.division_name_eng as division','tbl_division.division_name_bng as division_bng')->first();
			
			 if($user->unit_eng=="CHITTAGONGNORTH" || $user->unit_eng=="CHITTAGONGSOUTH" || $user->unit_eng=="CHITTAGONGADMIN")
            $user->unit_short="চট্টগ্রাম";
        elseif ($user->unit_eng=="DHAKAADMIN"||$user->unit_eng=="DHAKAEAST"||$user->unit_eng=="DHAKAWEST"||$user->unit_eng=="DHAKASOUTH"||$user->unit_eng=="DHAKANORTH")
            $user->unit_short = "ঢাকা";
        else $user->unit_short = $user->unit;
            return View::make('HRM::Letter.master')->with(['mem' => $mem, 'user' => $user, 'result' => $result, 'view' => 'print_embodiment_letter']);
        } else {
            return View::make('HRM::Letter.no_mem_found')->with('id', $id);
        }
    }

    function disembodimentLetterPrint($id, $unit, $v,$option)
    {
        DB::enableQueryLog();
        $mem = DB::table('tbl_rest_info')
            ->join('tbl_memorandum_id', 'tbl_memorandum_id.memorandum_id', '=', 'tbl_rest_info.memorandum_id')
            ->join('tbl_disembodiment_reason', 'tbl_disembodiment_reason.id', '=', 'tbl_rest_info.disembodiment_reason_id')
            ->select('tbl_disembodiment_reason.reason_in_bng as reason', 'tbl_memorandum_id.memorandum_id', 'tbl_memorandum_id.mem_date as created_at');
        $user = DB::table('tbl_user')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_user.id')
            ->join('tbl_units', 'tbl_units.id', '=', 'tbl_user.district_id')
            ->join('tbl_division', 'tbl_units.division_id', '=', 'tbl_division.id')
            ->where('tbl_user.district_id', $unit)->select('tbl_units.unit_name_eng as unit_eng','tbl_user_details.first_name', 'tbl_user_details.last_name', 'tbl_user_details.mobile_no', 'tbl_user_details.email', 'tbl_units.unit_name_bng as unit','tbl_division.division_name_eng as division','tbl_division.division_name_bng as division_bng')->first();
        $result = DB::table('tbl_embodiment_log')
            ->join('tbl_rest_info', 'tbl_rest_info.ansar_id', '=', 'tbl_embodiment_log.ansar_id')
            ->join('tbl_memorandum_id', 'tbl_memorandum_id.memorandum_id', '=', 'tbl_rest_info.memorandum_id')
            ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment_log.kpi_id')
            ->join('tbl_ansar_parsonal_info', 'tbl_embodiment_log.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
            ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
            ->join('tbl_thana', 'tbl_thana.id', '=', 'tbl_ansar_parsonal_info.thana_id')
            ->join('tbl_thana as kpi_thana', 'kpi_thana.id', '=', 'tbl_kpi_info.thana_id')
            ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
            ->whereRaw('tbl_embodiment_log.release_date=tbl_rest_info.rest_date')
            ->where('tbl_kpi_info.unit_id',$unit)
            ->select('kpi_thana.thana_name_bng as kpi_thana','tbl_ansar_parsonal_info.ansar_id as ansar_id', 'tbl_ansar_parsonal_info.ansar_name_bng as name', 'tbl_ansar_parsonal_info.father_name_bng as father_name', 'tbl_designations.name_bng as rank', 'tbl_kpi_info.kpi_name as kpi_name', 'tbl_ansar_parsonal_info.village_name_bng as village_name', 'tbl_ansar_parsonal_info.post_office_name_bng as pon', 'tbl_units.unit_name_bng as unit', 'tbl_thana.thana_name_bng as thana', 'tbl_embodiment_log.joining_date', 'tbl_embodiment_log.release_date')->orderBy('tbl_embodiment_log.id','DESC');
        if($option=='smartCardNo'){
            $l  = strlen($id.'');
            if($l>6) $id = substr($id.'',6);
            $result->where('tbl_ansar_parsonal_info.ansar_id',$id);
            $mem->where('tbl_rest_info.ansar_id',$id);
        }
        else{
            $result->where('tbl_memorandum_id.memorandum_id', $id);
            $mem->where('tbl_memorandum_id.memorandum_id', $id);
        }
        
       
        
        
        if (!$result->exists()) {
            

            $ansar_id_list = [250, 369, 393, 429, 530, 608, 689, 709, 968, 1082, 1175, 1212, 1294, 1712, 1922, 2145, 2237, 2308, 2381, 2410, 2473, 2611, 2688, 2709, 2994, 2995, 3033, 3096, 3235, 3277, 3288, 3383, 3409, 3458, 3638, 3774, 3779, 3791, 3825, 3987, 4012, 4153, 4193, 4292, 4322, 4337, 4466, 4579, 4660, 4671, 4789, 4804, 4839, 4925, 4947, 5110, 5145, 5230, 5322, 5405, 5548, 5554, 5566, 5762, 5797, 5800, 6068, 6105, 6425, 6456, 6517, 6603, 6837, 6883, 6889, 6980, 7133, 7514, 7761, 7819, 7895, 7949, 8255, 8369, 8401, 8511, 8750, 8761, 8781, 8847, 9029, 9387, 9574, 9747, 10590, 10866, 11203, 11282, 11402, 11471, 11510, 11514, 11686, 12464, 12716, 12753, 12954, 12955, 12984, 12989, 13045, 13159, 13314, 13696, 13768, 13868, 13894, 13930, 14133, 14170, 14247, 14372, 14378, 14399, 14466, 15318, 15700, 15828, 15992, 16490, 16614, 16878, 16945, 17031, 17036, 17281, 17307, 17555, 17699, 17994, 18531, 18589, 18635, 18900, 19098, 19291, 19373, 19882, 20235, 20372, 20620, 20949, 21138, 21145, 21364, 21384, 21420, 21651, 21751, 21836, 21968, 22407,  22554, 22717, 22762, 22886, 23165, 23334, 23628, 23634, 23744, 23860, 23979, 24136, 24176, 24795, 25248, 25487, 25964, 26067, 26459, 26587, 26721, 26775, 27299, 27308, 27461, 27754, 27870, 27977, 28190, 28502, 28512, 28535, 28651, 28742, 28808, 28903, 29046, 29110, 29277, 29340, 29367, 29514, 29526, 29575, 29885, 29978, 30157, 30191, 30670, 30731, 30767, 30844, 32630, 32661, 32741, 33365, 34322, 34791, 34808, 34817, 35339, 35430, 35519, 35600, 35729, 35970, 36102, 36698, 36779, 36797, 36873, 36874, 36892, 36913, 36986, 37054, 37140, 37151, 37178, 37187, 37372, 37479, 38004, 38135, 38141, 38161, 38256, 38298, 38879, 38915, 39279, 39342, 39360, 39399, 40453, 40495, 40765, 40852, 41093, 41125, 41135, 41140, 41178, 41201, 41317, 41470, 41530, 42179, 42181, 42203, 42212, 42275, 42483, 42535, 42571, 42852, 42873, 43056, 43658, 43717, 44171, 44231, 44270, 44655, 44761, 44894, 45021, 45058, 45080, 45105, 45259, 45743, 46027, 46596, 46934, 47084, 47211, 47315, 47360, 47518, 47579, 47631, 47662, 47906, 47994, 47995, 48239, 48784, 48940, 49088, 49137, 49337, 49919, 49977, 50023, 50063, 50460, 50558, 50770, 51614, 51841, 51880, 51922, 51933, 51954, 52686, 52946, 52982, 53235, 53484, 53692, 54067, 54077, 54119, 54144, 54155, 54167, 54181, 54357, 54418, 54422, 54552, 54889, 55116, 55180, 55356, 56018, 56167, 56219, 56257, 57250, 57495, 57564, 58053, 58305, 58316, 59100, 59197, 59275, 59311, 59315, 59397, 59468, 59486, 59491, 59787, 59828, 59908, 59945, 59947, 60336, 60490, 61214, 61271, 61427, 61772, 62133, 62556, 62811, 63633, 63721, 63817, 64287, 64732, 64785, 64899, 64919, 64969, 65293, 65683, 66078, 66510, 66520, 66601, 66735, 67559, 68976, 69195, 69392, 69465, 71714, 71861, 71948, 73096, 73581, 18633, 20914, 59546, 18353];

            if (in_array($id, $ansar_id_list)) {
                
                $otherStarttime = '2021-09-19';
                $otherEndtime = '2021-09-20';
            
                $result = DB::table('tbl_embodiment_log')
                                #->join('tbl_memorandum_id', 'tbl_memorandum_id.memorandum_id', '=', 'tbl_embodiment_log.comment')
                                ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment_log.kpi_id')
                                ->join('tbl_ansar_parsonal_info', 'tbl_embodiment_log.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                                ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                                ->join('tbl_thana', 'tbl_thana.id', '=', 'tbl_ansar_parsonal_info.thana_id')
                                ->join('tbl_thana as kpi_thana', 'kpi_thana.id', '=', 'tbl_kpi_info.thana_id')
                                ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                                ->where(function($query) use ($otherStarttime,$otherEndtime){
                                     $query->where('tbl_embodiment_log.release_date', '=', $otherStarttime);
                                     $query->orWhere('tbl_embodiment_log.release_date', '=', $otherEndtime);
                                 })
                                ->where('tbl_kpi_info.unit_id', $unit)
                                ->select('kpi_thana.thana_name_bng as kpi_thana', 'tbl_ansar_parsonal_info.ansar_id as ansar_id', 'tbl_ansar_parsonal_info.ansar_name_bng as name', 'tbl_ansar_parsonal_info.father_name_bng as father_name', 'tbl_designations.name_bng as rank', 'tbl_kpi_info.kpi_name as kpi_name', 'tbl_ansar_parsonal_info.village_name as village_name', 'tbl_ansar_parsonal_info.post_office_name as pon', 'tbl_units.unit_name_bng as unit', 'tbl_thana.thana_name_bng as thana', 'tbl_embodiment_log.joining_date', 'tbl_embodiment_log.release_date')->orderBy('tbl_embodiment_log.id', 'DESC');

                if ($option == 'smartCardNo') {
                    $l = strlen($id . '');
                    if ($l > 6)
                        $id = substr($id . '', 6);
                    $result->where('tbl_ansar_parsonal_info.ansar_id', $id);
                }
            } else {
                $result = DB::table('tbl_embodiment_log')
                                ->join('tbl_rest_info_log as tbl_rest_info', 'tbl_rest_info.ansar_id', '=', 'tbl_embodiment_log.ansar_id')
                                ->join('tbl_memorandum_id', 'tbl_memorandum_id.memorandum_id', '=', 'tbl_rest_info.old_memorandum_id')
                                ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment_log.kpi_id')
                                ->join('tbl_ansar_parsonal_info', 'tbl_embodiment_log.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                                ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                                ->join('tbl_thana', 'tbl_thana.id', '=', 'tbl_ansar_parsonal_info.thana_id')
                                ->join('tbl_thana as kpi_thana', 'kpi_thana.id', '=', 'tbl_kpi_info.thana_id')
                                ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                                ->whereRaw('tbl_embodiment_log.release_date=tbl_rest_info.rest_date')
                                ->where('tbl_kpi_info.unit_id', $unit)
                                ->select('kpi_thana.thana_name_bng as kpi_thana', 'tbl_ansar_parsonal_info.ansar_id as ansar_id', 'tbl_ansar_parsonal_info.ansar_name_bng as name', 'tbl_ansar_parsonal_info.father_name_bng as father_name', 'tbl_designations.name_bng as rank', 'tbl_kpi_info.kpi_name as kpi_name', 'tbl_ansar_parsonal_info.village_name as village_name', 'tbl_ansar_parsonal_info.post_office_name as pon', 'tbl_units.unit_name_bng as unit', 'tbl_thana.thana_name_bng as thana', 'tbl_embodiment_log.joining_date', 'tbl_embodiment_log.release_date')->orderBy('tbl_embodiment_log.id', 'DESC');
                if ($option == 'smartCardNo') {
                    $l = strlen($id . '');
                    if ($l > 6)
                        $id = substr($id . '', 6);
                    $result->where('tbl_ansar_parsonal_info.ansar_id', $id);
                }
                else {
                    $result->where('tbl_memorandum_id.memorandum_id', $id);
                }
            }
        }
        if(!$mem->exists()){
            
            if (in_array($id, $ansar_id_list)) {
                
               
                $mem = DB::table('tbl_embodiment_log')
                        ->join('tbl_memorandum_id', 'tbl_memorandum_id.memorandum_id', '=', 'tbl_embodiment_log.comment')
                        ->join('tbl_disembodiment_reason', 'tbl_disembodiment_reason.id', '=', 'tbl_embodiment_log.disembodiment_reason_id')
                        ->select('tbl_disembodiment_reason.reason_in_bng as reason', 'tbl_memorandum_id.memorandum_id', 'tbl_memorandum_id.mem_date as created_at');
                if ($option == 'smartCardNo') {
                    $l = strlen($id . '');
                    if ($l > 6)
                        $id = substr($id . '', 6);
                    $mem->where('tbl_embodiment_log.ansar_id', $id);
                }
                $mem->where('tbl_memorandum_id.memorandum_id', '44.03.0000.048.50.007.21-285');
                

            } else {
                $mem = DB::table('tbl_rest_info_log as tbl_rest_info')
                        ->join('tbl_memorandum_id', 'tbl_memorandum_id.memorandum_id', '=', 'tbl_rest_info.old_memorandum_id')
                        ->join('tbl_disembodiment_reason', 'tbl_disembodiment_reason.id', '=', 'tbl_rest_info.disembodiment_reason_id')
                        ->select('tbl_disembodiment_reason.reason_in_bng as reason', 'tbl_memorandum_id.memorandum_id', 'tbl_memorandum_id.mem_date as created_at');
                
                if ($option == 'smartCardNo') {
                    $l = strlen($id . '');
                    if ($l > 6)
                        $id = substr($id . '', 6);
                    $mem->where('tbl_rest_info.ansar_id', $id);
                }
                else {
                    $mem->where('tbl_memorandum_id.memorandum_id', $id);
                }
            }
        }
        $mem = $mem->first();
        //dd(DB::getQueryLog()); 
       // print_r( $result); exit;
        $result = DB::table(DB::raw("({$result->toSql()}) x"))->mergeBindings($result)->groupBy('ansar_id')->get();
         //dd(DB::getQueryLog()); exit;
        if($user->unit_eng=="CHITTAGONGNORTH" || $user->unit_eng=="CHITTAGONGSOUTH" || $user->unit_eng=="CHITTAGONGADMIN")
            $user->unit_short="চট্টগ্রাম";
        elseif ($user->unit_eng=="DHAKAADMIN"||$user->unit_eng=="DHAKAEAST"||$user->unit_eng=="DHAKAWEST"||$user->unit_eng=="DHAKASOUTH"||$user->unit_eng=="DHAKANORTH")
            $user->unit_short = "ঢাকা";
        else $user->unit_short = $user->unit;

        if ($mem && $result) {
            return View::make('HRM::Letter.master')->with(['mem' => $mem, 'user' => $user, 'result' => $result, 'view' => 'print_disembodiment_letter']);
        } else {
            return View::make('HRM::Letter.no_mem_found')->with('id', $id);
        }
    }
    
    
     function freezLetterPrint($id, $unit, $v,$option)
    {  
        DB::enableQueryLog();
        $mem = DB::table('tbl_freezing_info')
            ->join('tbl_memorandum_id', 'tbl_memorandum_id.memorandum_id', '=', 'tbl_freezing_info.memorandum_id')
            ->select('tbl_freezing_info.freez_reason as reason', 'tbl_freezing_info.comment_on_freez as comment', 'tbl_memorandum_id.memorandum_id', 'tbl_memorandum_id.mem_date as created_at');
        $user = DB::table('tbl_user')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_user.id')
            ->join('tbl_units', 'tbl_units.id', '=', 'tbl_user.district_id')
            ->join('tbl_division', 'tbl_units.division_id', '=', 'tbl_division.id')
            ->where('tbl_user.district_id', $unit)->select('tbl_units.unit_name_eng as unit_eng','tbl_user_details.first_name', 'tbl_user_details.last_name', 'tbl_user_details.mobile_no', 'tbl_user_details.email', 'tbl_units.unit_name_bng as unit','tbl_division.division_name_eng as division','tbl_division.division_name_bng as division_bng')->first();
        
        $result = DB::table('tbl_freezing_info')
            ->join('tbl_memorandum_id', 'tbl_memorandum_id.memorandum_id', '=', 'tbl_freezing_info.memorandum_id')
            ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_freezing_info.kpi_id')
            ->join('tbl_ansar_parsonal_info', 'tbl_freezing_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
            ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
            ->join('tbl_thana', 'tbl_thana.id', '=', 'tbl_ansar_parsonal_info.thana_id')
            ->join('tbl_thana as kpi_thana', 'kpi_thana.id', '=', 'tbl_kpi_info.thana_id')
            ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
            ->where('tbl_kpi_info.unit_id',$unit)
            ->select('kpi_thana.thana_name_bng as kpi_thana','tbl_ansar_parsonal_info.ansar_id as ansar_id', 'tbl_ansar_parsonal_info.ansar_name_bng as name', 'tbl_ansar_parsonal_info.father_name_bng as father_name', 'tbl_designations.name_bng as rank', 'tbl_kpi_info.kpi_name as kpi_name', 'tbl_ansar_parsonal_info.village_name_bng as village_name', 'tbl_ansar_parsonal_info.post_office_name_bng as pon', 'tbl_units.unit_name_bng as unit', 'tbl_thana.thana_name_bng as thana', 'tbl_freezing_info.freez_date', 'tbl_freezing_info.embodiment_date', 'tbl_freezing_info.comment_on_freez');
        if($option=='smartCardNo'){
            $l  = strlen($id.'');
            if($l>6) $id = substr($id.'',6);
            $result->where('tbl_ansar_parsonal_info.ansar_id',$id);
            $mem->where('tbl_freezing_info.ansar_id',$id);
        }
        else{
            $result->where('tbl_memorandum_id.memorandum_id', $id);
            $mem->where('tbl_memorandum_id.memorandum_id', $id);
        }
        
   
      
        $mem = $mem->first();
        
               
        $result = DB::table(DB::raw("({$result->toSql()}) x"))->mergeBindings($result)->groupBy('ansar_id')->get();
//		dd(DB::getQueryLog()); 
        
        if($user->unit_eng=="CHITTAGONGNORTH" || $user->unit_eng=="CHITTAGONGSOUTH" || $user->unit_eng=="CHITTAGONGADMIN")
            $user->unit_short="চট্টগ্রাম";
        elseif ($user->unit_eng=="DHAKAADMIN"||$user->unit_eng=="DHAKAEAST"||$user->unit_eng=="DHAKAWEST"||$user->unit_eng=="DHAKASOUTH"||$user->unit_eng=="DHAKANORTH")
            $user->unit_short = "ঢাকা";
        else $user->unit_short = $user->unit;

        if ($mem && $result) {
            return View::make('HRM::Letter.master')->with(['mem' => $mem, 'user' => $user, 'result' => $result, 'view' => 'print_freez_letter']);
        } else {
            return View::make('HRM::Letter.no_mem_found')->with('id', $id);
        }
    }


    function embodimentLetterView()
    {
        return View::make('HRM::Letter.embodiment_letter');
    }

    function disembodimentLetterView()
    {
        return View::make('HRM::Letter.disembodiment_letter');
    }
    
    function freezeLetterView()
    {
        return View::make('HRM::Letter.freeze_letter');
    }
}
