<?php

namespace App\modules\HRM\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Jobs\ExportData;
use App\modules\HRM\Models\CustomQuery;
use App\modules\HRM\Models\DataExportStatus;
use App\modules\HRM\Models\District;
use App\modules\HRM\Models\ExportDataJob;
use App\modules\HRM\Models\GlobalParameter;
use App\modules\HRM\Models\SystemSetting;
use App\modules\HRM\Models\PersonalnfoLogModel;
use App\modules\HRM\Models\OfferZone;
use App\modules\HRM\Models\UnitCompany;
use App\modules\HRM\Models\UnitCompanyLog;
use App\modules\recruitment\Models\JobAppliciant;
use App\modules\recruitment\Models\JobCircular;
use App\modules\HRM\Models\AnsarPromotion;
use App\modules\HRM\Models\AnsarPromotionStatusLog;
use App\modules\HRM\Models\PersonalInfo;
use App\modules\HRM\Models\AnsarStatusInfo;
use App\modules\HRM\Models\MemorandumModel;
use App\modules\HRM\Models\PanelModel;
use App\modules\HRM\Models\RestInfoModel;
use App\modules\HRM\Models\RestInfoLogModel;
use App\Jobs\RearrangePanelPositionGlobal;
use App\Jobs\RearrangePanelPositionLocal;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Maatwebsite\Excel\Facades\Excel;
use App\Helper\ExportDataToExcel;


class PromotionController extends Controller
{
    use ExportDataToExcel;

    function hrmDashboard()
    {
        $type = auth()->user()->type;
        if ($type == 22 || $type == 66) {
            return View::make('HRM::Dashboard.hrm-rc-dc');
        } else {
            return View::make('HRM::Dashboard.hrm');
        }
    }

    //Promotion

    public function promotionAnsarView()
    {
        return view('HRM::promotion.promotion_view');
    }

    public function SendToPanelBatchUploadView()
    {
        return view('HRM::promotion.send_to_panel_batch_upload_view');
    }

    public function promotionList()
    {
        return view('HRM::promotion.promotion_list');
    }
    public function promotionLog()
    {
        return view('HRM::promotion.promotion_ansar_log');
    }
    public function getPromotionLog(Request $request)
    {
        $limit = Input::get('limit');
        $offset = Input::get('offset');
        $unit = Input::get('unit');
        $division = Input::get('division');
        $rank = Input::get('rank');
        $sex = Input::get('gender');
        $q = Input::get('q');
        $rules = [
            'type' => 'regex:/[a-z]+/',
            'limit' => 'numeric',
            'offset' => 'numeric',
            'unit' => ['regex:/^(all)$|^[0-9]+$/'],
            'division' => ['regex:/^(all)$|^[0-9,]+$/'],
            'rank' => ['regex:/^(all)$|^[0-9]+$/'],
           // 'from_date' => ['regex:/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/'],
            //'to_date' => ['regex:/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/'],

        ];
        //print_r($offset);exit;
        $valid = Validator::make(Input::all(), $rules);

        if ($valid->fails()) {
            return response("Invalid Request(400)", 400);
        }
        
        $data = [];
        $user = Auth::user();
           
        //echo "Anik";exit;
        
        $data = CustomQuery::getPromotionAnsarLog($offset, $limit, $unit, $division, $sex, $rank, $q);
           //print_r($data);exit;
           
        //$data = CustomQuery::getTotalPaneledAnsarList($offset, $limit, $unit, $thana, $division, $sex, CustomQuery::ALL_TIME, $rank, $request->filter_mobile_no, $request->filter_age, $q);
        
                
        if ($request->exists('export')) {
            $data = collect($data['ansars'])->chunk(2000)->toArray();
            return $this->exportData($data, 'HRM::export.ansar_view_excel', $type);
        }
        return Response::json($data);
    }

    public function  getCirculars()
    {
        $status = Input::get('status');
        $category = Input::get('category');

        $data = JobCircular::with('category')->where('circular_status', $status)->where('job_category_id', $category)->get();
        
        return response()->json($data);

    }

        public function confirmPromotion(Request $request)
    {

        $rules = [
            'range' => 'regex:/^[0-9]+$/',
            'unit' => 'regex:/^[0-9]+$/',
            'circular' => 'required|regex:/^[0-9]+$/',
        ];
        $this->validate($request, $rules);
        try {
            $written_pass_mark = 0;
            $viva_pass_mark = 0;
            $mark_distribution = JobCircularMarkDistribution::where('job_circular_id', $request->circular)->first();
            if ($mark_distribution) {
                $written_pass_mark = (floatval($mark_distribution->convert_written_mark) * floatval($mark_distribution->written_pass_mark)) / 100;
                $viva_pass_mark = (floatval($mark_distribution->viva) * floatval($mark_distribution->viva_pass_mark)) / 100;
            }
//        return $written_pass_mark." ".$viva_pass_mark;
            $job_quota = JobCircularQuota::where('job_circular_id', $request->circular)->first();
            if ($job_quota->type == "unit") {
                $quota = $job_quota->quota()->where('district_id', $request->unit)->first();
                $accepted = JobAppliciant::whereHas('accepted', function ($q) {
                })->where('status', 'accepted')->where('job_circular_id', $request->circular)->where('unit_id', $request->unit)->count();
            } else {
                $quota = $job_quota->quota()->where('range_id', $request->range)->first();
                $accepted = JobAppliciant::whereHas('accepted', function ($q) {
                })->where('status', 'accepted')->where('job_circular_id', $request->circular)->where('division_id', $request->range)->count();
            }
            if ($job_quota->type == "unit") {
                $applicant_male = JobApplicantMarks::with(['applicant' => function ($q) {
                    $q->with(['district', 'division', 'thana']);
                }])->whereHas('applicant', function ($q) use ($request) {
                    $q->whereHas('selectedApplicant', function () {
                    })->where('status', 'selected')->where('job_circular_id', $request->circular)->where('unit_id', $request->unit);
                })->select(DB::raw('DISTINCT *,(IFNULL(written,0)+IFNULL(viva,0)+IFNULL(physical,0)+IFNULL(edu_training,0)+IFNULL(edu_experience,0)+IFNULL(physical_age,0)) as total_mark'))->havingRaw('total_mark>0')->orderBy('total_mark', 'desc');
                $applicant_male->where('written', '>=', $written_pass_mark)->where('viva', '>=', $viva_pass_mark);
            } else {
                $applicant_male = JobApplicantMarks::with(['applicant' => function ($q) {
                    $q->with(['district', 'division', 'thana']);
                }])->whereHas('applicant', function ($q) use ($request) {
                    $q->where(DB::raw('height_feet*12+height_inch'), ">=", 65);
                    $q->whereHas('education', function ($q) {
                        $q->where('priority', '>=', 7);
                    });
                    $q->whereHas('selectedApplicant', function () {
                    })->where('status', 'selected')->where('job_circular_id', $request->circular)->where('division_id', $request->range);
                })->select(DB::raw('DISTINCT *,(IFNULL(written,0)+IFNULL(viva,0)+IFNULL(physical,0)+IFNULL(edu_training,0)+IFNULL(edu_experience,0)+IFNULL(physical_age,0)) as total_mark'))->havingRaw('total_mark>0')->orderBy('total_mark', 'desc');
                $applicant_male->where('written', '>=', $written_pass_mark)->where('viva', '>=', $viva_pass_mark);
            }
            if ($quota) {

                if (intval($quota->male) - $accepted > 0) $applicants = $applicant_male->limit(intval($quota->male) - $accepted)->get();
                else $applicants = [];
            } else $applicants = [];
            if (count($applicants)) {
                foreach ($applicants as $applicant) {
                    $applicant->applicant->update(['status' => 'accepted']);
                    if (!$applicant->applicant->accepted) {
                        $applicant->applicant->accepted()->create([
                            'action_user_id' => auth()->user()->id
                        ]);
                    }
                }
            } else {
                throw new \Exception('No applicants within quota available');
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
        return response()->json(['status' => 'success', 'message' => 'Applicant accepted successfully']);
    }

    public function acceptPromotionByFile(Request $request){
        $file = $request->file("applicant_id_list");
        $applicant_ids = "";
        
        Excel::load($file,function ($reader) use(&$applicant_ids){
            $applicant_ids = array_flatten($reader->limitColumns(1)->first());
        });
        //print_r($applicant_ids);exit;
        $applicants = JobAppliciant::where('job_circular_id',$request->circular)->whereIn('applicant_id',$applicant_ids)->get();
        //return $applicants;
        //echo $applicants;exit;
        
        DB::beginTransaction();
        
        try{
            //ob_implicit_flush(true);
            //ob_end_flush();
            //echo "Start Processing....";
            $i=1;
            foreach ($applicants as $applicant){

				$this->promotionEntry($applicant);
                $this->promotionEntryLog($applicant);
                $this->statusMakesNotVerified($applicant);
                $this->personalInfoMakesNotVerified($applicant);
                
                //return $this->promotionEntryLog($applicant);

				// if($applicant->accepted) continue;
                // $applicant->status = 'accepted';
                // $applicant->marks()->create([
                //     'specialized' => 1
                // ]);
                // $applicant->accepted()->create([
                //     'action_user_id' => $request->action_user_id,
                //     'comment'=>$request->comment
                // ]);
                // $applicant->save();
                // echo "Processed $i of ".count($applicants);
                // $i++;
            }
            DB::commit();
        }catch(\Exception $e){
            DB::rollback();
//            redirect()->back()->with("error_message",$e->getMessage());
            return $e;
        }
        return redirect()->back()->with("success_message","Applicant Updated to accepted successfully");
    }

    public function SendToPanelBatchUploadByFile(Request $request){
        DB::enableQueryLog();
        $file = $request->file("applicant_id_list");
        $date = Carbon::yesterday()->format('d-M-Y H:i:s');
        $applicant_ids = "";
        $selected_ansars = [];
        //echo $request->circular;exit;
        
        Excel::load($file,function ($reader) use(&$applicant_ids){
            $applicant_ids = array_flatten($reader->limitColumns(1)->first());
        });
        //print_r($applicant_ids);exit;
        $applicants = AnsarPromotion::where('circular_id',$request->circular)->whereIn('ansar_id',$applicant_ids)->get();
        $selected_ansars = $request->input('ansar_id');
        foreach($applicants as $applicant)
        {
            $selected_ansars[]= $applicant->ansar_id;
        }
        //echo $applicants;exit;
        //return $applicants;
        //dd(DB::getQueryLog());
        $rules = [
            'memorandum_id' => 'required',
            'panel_date' => ["required", "after:{$date}"],
        ];
        $valid = Validator::make($request->all(), $rules);
        if ($valid->fails()) {
            $messages = $valid->messages();echo $messages; exit;
            return Response::json(['status' => false, 'message' => 'Invalid request']);
            
        }
        //echo $selected_ansars; exit;
        DB::beginTransaction();
        $user = [];
        try{
            $n = Carbon::now();
            $mi = $request->input('memorandum_id');
            $pd = $request->input('panel_date');
            $modified_panel_date = Carbon::parse($pd)->format('Y-m-d H:i:s');
            //$ansar_merit = $request->input('merit');
            $memorandum_entry = new MemorandumModel();
            $memorandum_entry->memorandum_id = $mi;
            $memorandum_entry->save();

            
            if (!is_null($selected_ansars)) {
                for ($i = 0; $i < count($selected_ansars); $i++) {
                    $ansar = PersonalInfo::where('ansar_id', $selected_ansars[$i])->first();
                    if ($ansar && ($ansar->verified == 0 || $ansar->verified == 1)) {
                        $ansar->verified = 2;
                        $ansar->save();
                    }
                     
                        $ansar->deleteCount();
                        $ansar->deleteOfferStatus();
                        $panel_entry = new PanelModel;
                        $panel_entry->ansar_id = $selected_ansars[$i];
                        $panel_entry->come_from = "Entry";
                        $panel_entry->panel_date = $modified_panel_date;
                        $panel_entry->re_panel_date = $modified_panel_date;
                        $panel_entry->memorandum_id = $mi;
                        
                        //$panel_entry->ansar_merit_list = $ansar_merit[$i];
                        $panel_entry->action_user_id = Auth::user()->id;
                        $panel_entry->save();

                        AnsarStatusInfo::where('ansar_id', $selected_ansars[$i])->update(['free_status' => 0, 'offer_sms_status' => 0, 'offered_status' => 0, 'block_list_status' => 0, 'black_list_status' => 0, 'rest_status' => 0, 'embodied_status' => 0, 'pannel_status' => 1, 'freezing_status' => 0]);

                        array_push($user, ['ansar_id' => $selected_ansars[$i], 'action_type' => 'PANELED', 'from_state' => 'FREE', 'to_state' => 'PANELED', 'action_by' => auth()->user()->id]);
                    
                }
            }
            DB::commit();
            CustomQuery::addActionlog($user, true);
            $this->dispatch(new RearrangePanelPositionGlobal());
            $this->dispatch(new RearrangePanelPositionLocal());
        }catch(\Exception $e){
            DB::rollback();
        //redirect()->back()->with("error_message",$e->getMessage());
            return $e;
        }
        return redirect()->back()->with("success_message","Applicants successfully sent to panel...");
    }

    public function SendToPanelFromAnsarList(Request $request){
        //echo ("Anik");exit;
        DB::enableQueryLog();
        $date = Carbon::yesterday()->format('d-M-Y H:i:s');
        $row_id = $request->request_id;
        $requestAnsarPromotionData = AnsarPromotion::findOrFail($row_id);
        $selected_ansars[] = $requestAnsarPromotionData->ansar_id;

        $rules = [
            'memorandum_id' => 'required',
             'panel_date' => ["required", "after:{$date}"]
        ];
        $valid = Validator::make($request->all(), $rules);
        if ($valid->fails()) {
            $messages = $valid->messages();echo $messages; exit;
            return Response::json(['status' => false, 'message' => 'Invalid request']);
            
        }
        //echo "Anik"; exit;
        DB::beginTransaction();
        $user = [];
        try{
            $n = Carbon::now();
            $mi = $request->input('memorandum_id');
            $pd = $request->input('panel_date');
            $modified_panel_date = Carbon::parse($pd)->format('Y-m-d H:i:s');
            //$ansar_merit = $request->input('merit');
            $memorandum_entry = new MemorandumModel();
            $memorandum_entry->memorandum_id = $mi;
            $memorandum_entry->save();
            
            
            if (!is_null($selected_ansars)) {
                for ($i = 0; $i < count($selected_ansars); $i++) {
                    $ansar = PersonalInfo::where('ansar_id', $selected_ansars[$i])->first();
                    if ($ansar && ($ansar->verified == 0 || $ansar->verified == 1)) {
                        $ansar->verified = 2;
                        $ansar->save();
                    }
                     
                        $ansar->deleteCount();
                        $ansar->deleteOfferStatus();
                        $panel_entry = new PanelModel;
                        $panel_entry->ansar_id = $selected_ansars[$i];
                        $panel_entry->come_from = "Entry";
                        $panel_entry->panel_date = $modified_panel_date;
                        $panel_entry->re_panel_date = $modified_panel_date;
                        $panel_entry->memorandum_id = $mi;
                        
                        //$panel_entry->ansar_merit_list = $ansar_merit[$i];
                        $panel_entry->action_user_id = Auth::user()->id;
                        $panel_entry->save();

                        AnsarStatusInfo::where('ansar_id', $selected_ansars[$i])->update(['free_status' => 0, 'offer_sms_status' => 0, 'offered_status' => 0, 'block_list_status' => 0, 'black_list_status' => 0, 'rest_status' => 0, 'embodied_status' => 0, 'pannel_status' => 1, 'freezing_status' => 0]);
                        AnsarPromotion::where('ansar_id', $selected_ansars[$i])->update(['status' => 'Completed']);
                        array_push($user, ['ansar_id' => $selected_ansars[$i], 'action_type' => 'PANELED', 'from_state' => 'FREE', 'to_state' => 'PANELED', 'action_by' => auth()->user()->id]);
                    
                }
            }
            DB::commit();
            CustomQuery::addActionlog($user, true);
            $this->dispatch(new RearrangePanelPositionGlobal());
            $this->dispatch(new RearrangePanelPositionLocal());
        }catch(\Exception $e){
            DB::rollback();
        //redirect()->back()->with("error_message",$e->getMessage());
            return $e;
        }
        return redirect()->back()->with("success_message","Applicant successfully sent to panel...");
    }
    

    public function promotionEntry($applicant)
    {
        
        //echo "promotionEntry from foreach";
        $results = [];
        $ansar_id = $applicant->ansar_id;
        $circular_id = $applicant->job_circular_id;
        $status = $applicant->status;
        
        $data = CustomQuery::checkPromotionAnsarExistStatus($ansar_id);
            // echo "<pre>"; exit;
            if(count($data)>0 ){
                $message = "This Ansar is already in the queue";
                $results = ['status' => false, 'message' => $message];
                return Response::json($results);
            }else{
                $inserted_data = [
                    "ansar_id"=> $ansar_id,
                    "circular_id" => $circular_id,
                    "status" => "On Process"
                ];
                //print_r($inserted_data);exit;
                //DB::enableQueryLog();
                AnsarPromotion::insert($inserted_data);
            }
                
    }

    public function promotionEntryLog($applicant)
    {
        
        
        $results = [];
        $ansar_id = $applicant->ansar_id;
        $circular_id = $applicant->job_circular_id;
        $ansarStatusData = AnsarStatusInfo::where('ansar_id',$ansar_id)->first();
        $free_status = $ansarStatusData->free_status;
        $pannel_status = $ansarStatusData->pannel_status;
        $offer_sms_status = $ansarStatusData->offer_sms_status;
        $offered_status = $ansarStatusData->offered_status;
        $embodied_status = $ansarStatusData->embodied_status;
        $offer_block_status = $ansarStatusData->offer_block_status;
        $freezing_status = $ansarStatusData->freezing_status;
        $early_retierment_status = $ansarStatusData->early_retierment_status;
        $block_list_status = $ansarStatusData->block_list_status;
        $black_list_status = $ansarStatusData->black_list_status;
        $rest_status = $ansarStatusData->rest_status;
        $retierment_status = $ansarStatusData->retierment_status;
        
        $data = CustomQuery::checkPromotionLogAnsarExistStatus($ansar_id);
            // echo "<pre>"; exit;
            if(count($data)>0 ){
                $message = "This Ansar is already in the queue";
                $results = ['status' => false, 'message' => $message];
                return Response::json($results);
            }else{
                $inserted_data = [
                    "ansar_id"=> $ansar_id,
                    "circular_id" => $circular_id,
                    "action_user_id"=> Auth::user()->id,
                    "free_status" => $free_status,
                    "pannel_status"=> $pannel_status,
                    "offer_sms_status" => $offer_sms_status,
                    "offered_status" => $offered_status,
                    "embodied_status" => $embodied_status,
                    "offer_block_status"=> $offer_block_status,
                    "freezing_status" => $freezing_status,
                    "early_retierment_status"=> $early_retierment_status,
                    "block_list_status" => $block_list_status,
                    "black_list_status" => $black_list_status,
                    "rest_status"=> $rest_status,
                    "retierment_status" => $retierment_status
                ];
                //print_r($inserted_data);exit;
                //DB::enableQueryLog();
                AnsarPromotionStatusLog::insert($inserted_data);
            }
                
    }

    public function statusMakesNotVerified($applicant)
    {
        
        $ansar_id = $applicant->ansar_id;
               
                $makesNotVerified = AnsarStatusInfo::findOrFail($ansar_id);
                //$makesNotVerified->promotional_not_verified = 1;
                $makesNotVerified->free_status = 0;
                $makesNotVerified->pannel_status = 0;
                $makesNotVerified->offer_sms_status = 0;
                $makesNotVerified->offered_status = 0;
                $makesNotVerified->embodied_status = 0;
                $makesNotVerified->offer_block_status = 0;
                $makesNotVerified->freezing_status = 0;
                $makesNotVerified->early_retierment_status = 0;
                $makesNotVerified->block_list_status = 0;
                $makesNotVerified->black_list_status = 0;
                $makesNotVerified->rest_status = 0;
                $makesNotVerified->retierment_status = 0;
                $makesNotVerified->save();
                
    }

    public function personalInfoMakesNotVerified($applicant)
    {
         
        $ansar_id = $applicant->ansar_id;
               
                $makesNotVerified = PersonalInfo::findOrFail($ansar_id);
                $makesNotVerified->verified = 0;
                $makesNotVerified->save();
  
    }

    //promotion List

    // public function verifyAnsar($applicant)
    // {
         
    //     $row_id = $applicant->row_id;
               
    //             $makesNotVerified = AnsarPromotion::findOrFail($row_id);
    //             $makesNotVerified->not_verified_status = 1;
    //             $makesNotVerified->save();
    //             // $makesNotVerified = PersonalInfo::findOrFail($row_id);
    //             // $makesNotVerified->not_verified_status = 1;
    //             // $makesNotVerified->save();

    // }

    public function getPromotionList(Request $request)
    {
        //DB::enableQueryLog();
        $limit = Input::get('limit');
        $offset = Input::get('offset');
        $unit = Input::get('unit');
        $division = Input::get('division');
        $rank = Input::get('rank');
        $sex = Input::get('gender');
        $q = Input::get('q');
        $rules = [
            'type' => 'regex:/[a-z]+/',
            'limit' => 'numeric',
            'offset' => 'numeric',
            'unit' => ['regex:/^(all)$|^[0-9]+$/'],
            'division' => ['regex:/^(all)$|^[0-9,]+$/'],
            'rank' => ['regex:/^(all)$|^[0-9]+$/'],
           // 'from_date' => ['regex:/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/'],
            //'to_date' => ['regex:/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/'],

        ];
        //print_r($limit);exit;
        $valid = Validator::make(Input::all(), $rules);

        if ($valid->fails()) {
            return response("Invalid Request(400)", 400);
        }
        //echo "anik";exit;
        $data = CustomQuery::getPromotionListWithRankGender($offset, $limit, $unit, $division, $sex, $rank, $q);
        //dd(DB::getQueryLog());exit;
        if ($request->exists('export')) {
            $data = collect($data['allPromotionAnsar'])->chunk(2000)->toArray();
            return $this->exportData($data, 'HRM::export.ansar_view_excel', $type);
        }
        return Response::json($data);
    }

    public function getPromotionAnsarList(Request $request)
    {
        $limit = Input::get('limit');
        $offset = Input::get('offset');
        $unit = Input::get('unit');
        $division = Input::get('division');
        $rank = Input::get('rank');
        $sex = Input::get('gender');
        $q = Input::get('q');
        $rules = [
            'type' => 'regex:/[a-z]+/',
            'limit' => 'numeric',
            'offset' => 'numeric',
            'unit' => ['regex:/^(all)$|^[0-9]+$/'],
            'division' => ['regex:/^(all)$|^[0-9,]+$/'],
            'rank' => ['regex:/^(all)$|^[0-9]+$/'],
           // 'from_date' => ['regex:/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/'],
            //'to_date' => ['regex:/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/'],

        ];
        $valid = Validator::make(Input::all(), $rules);

        if ($valid->fails()) {
            return response("Invalid Request(400)", 400);
        }
        
        $data = [];
        $user = Auth::user();
        
            
        if($unit != 'all'){
            
            $data = CustomQuery::getPromotionAnsarInfo($offset, $limit, $unit, $division, $sex, $rank, $q);
           // echo "<pre>"; print_r($data);exit;
        }else{
            $data = [];
        }
        //$data = CustomQuery::getTotalPaneledAnsarList($offset, $limit, $unit, $thana, $division, $sex, CustomQuery::ALL_TIME, $rank, $request->filter_mobile_no, $request->filter_age, $q);
        
                
        if ($request->exists('export')) {
            $data = collect($data['allPromotionAnsar'])->chunk(2000)->toArray();
            return $this->exportData($data, 'HRM::export.ansar_view_excel', $type);
        }
        return Response::json($data);
    }

   

    function verifyRankPromotion(Request $request)
    {  
        $row_id = $request->request_id;
        $comment = $request->comment;
        $results = [];   

        $requestAnsar = AnsarPromotion::findOrFail($row_id);
        $requestAnsar->not_verified_status = 1;
        $requestAnsar->promoted_status = 1;
        $requestAnsar->save();
        $requestAnsar = PersonalInfo::findOrFail($row_id);
        $requestAnsar->verified = 2;
        $requestAnsar->save();
        // $requestAnsar->request_comment = $comment;   
        // $requestAnsar->requested_type = "remove";  
        // $requestAnsar->status = 0;   
        // $requestAnsar->action_id = Auth::user()->id;
        // $requestAnsar->save();
        //$message = "Ansar ".$ansar_id." Delete in the pending list. Please wait for approval";
     
       $results = ['status' => true, 'message' => 'Ansar ('.$requestAnsar->ansar_id . ') has been verified!'];
        return Response::json($results);
    }

    function rankPromotion(Request $request)
    {  
        echo "AnikZz";exit;
        $row_id = $request->request_id;
        $rank = $request->rank;
        $results = [];   

        if($request->rank=='Ansar')
        {
            $requestAnsar = AnsarPromotion::findOrFail($row_id);
            $requestAnsar->not_verified_status = 1;
            $requestAnsar->promoted_status = 1;
            $requestAnsar->save();
            $requestAnsar = PersonalInfo::findOrFail($row_id);
            $requestAnsar->verified = 2;
            $requestAnsar->save();
        }
        if($request->rank=='APC')
        {
            $requestAnsar = AnsarPromotion::findOrFail($row_id);
            $requestAnsar->not_verified_status = 1;
            $requestAnsar->promoted_status = 1;
            $requestAnsar->save();
            $requestAnsar = PersonalInfo::findOrFail($row_id);
            $requestAnsar->verified = 2;
            $requestAnsar->designation_id = 2;
            $requestAnsar->save();
        }
        if($request->rank=='PC')
        {
            $requestAnsar = AnsarPromotion::findOrFail($row_id);
            $requestAnsar->not_verified_status = 1;
            $requestAnsar->promoted_status = 1;
            $requestAnsar->save();
            $requestAnsar = PersonalInfo::findOrFail($row_id);
            $requestAnsar->verified = 2;
            $requestAnsar->designation_id = 3;
            $requestAnsar->save();
        }
        
        // $requestAnsar->request_comment = $comment;   
        // $requestAnsar->requested_type = "remove";  
        // $requestAnsar->status = 0;   
        // $requestAnsar->action_id = Auth::user()->id;
        // $requestAnsar->save();
        //$message = "Ansar ".$ansar_id." Delete in the pending list. Please wait for approval";
     
       $results = ['status' => true, 'message' => 'Ansar ('.$requestAnsar->ansar_id . ') has been verified!'];
        return Response::json($results);
    }

    function rankUpdate(Request $request)
    {  
        //echo "AnikZz";exit;
        $row_id = $request->request_id;
        $results = [];   

        $requestAnsarPromotionData = AnsarPromotion::findOrFail($row_id);
        $requestedAnsar = $requestAnsarPromotionData->ansar_id;
        $ansarUpdatedData = PersonalInfo::where('ansar_id', $requestedAnsar)->firstOrFail();
       
        if($ansarUpdatedData->designation_id==2)
        {
            
            $results = ['status' => false, 'message' => 'Ansar ('.$requestAnsarPromotionData->ansar_id . ') can not be promoted APC to APC!'];
            return Response::json($results);
        }
        else{
            $ansarUpdatedData->designation_id = 2;
            $ansarUpdatedData->save();  
            
            $results = ['status' => true, 'message' => 'Ansar ('.$requestAnsarPromotionData->ansar_id . ') has been successfully promoted to APC!'];
            return Response::json($results);
        }
        
    }

    function promotedToAPC(Request $request)
    {  
        //echo "AnikZz";exit;
        $row_id = $request->request_id;
        $results = [];   
        $requestAnsarPromotionData = AnsarPromotion::findOrFail($row_id);
        $requestedAnsar = $requestAnsarPromotionData->ansar_id;
        $ansarUpdatedData = PersonalInfo::where('ansar_id', $requestedAnsar)->firstOrFail();
       
        if($ansarUpdatedData->designation_id==2)
        {
            $results = ['status' => false, 'message' => 'Ansar ('.$requestAnsarPromotionData->ansar_id . ') can not be promoted APC to APC!'];
            return Response::json($results);
        }
        else{
            $ansarUpdatedData->designation_id = 2;
            $ansarUpdatedData->save();  
            
            $results = ['status' => true, 'message' => 'Ansar ('.$requestAnsarPromotionData->ansar_id . ') has been successfully promoted to APC!'];
            return Response::json($results);
        }
        
    }

    function promotedToPC(Request $request)
    {  
        //echo "AnikZz";exit;
        $row_id = $request->request_id;
        $results = [];   

        $requestAnsarPromotionData = AnsarPromotion::findOrFail($row_id);
        $requestedAnsar = $requestAnsarPromotionData->ansar_id;
        $ansarUpdatedData = PersonalInfo::where('ansar_id', $requestedAnsar)->firstOrFail();
        
        if($ansarUpdatedData->designation_id==3)
        {
            $results = ['status' => false, 'message' => 'Ansar ('.$requestAnsarPromotionData->ansar_id . ') can not be promoted PC to PC!'];
            return Response::json($results);
        }
        elseif($ansarUpdatedData->designation_id==1)
        {
            $results = ['status' => false, 'message' => 'Ansar ('.$requestAnsarPromotionData->ansar_id . ') can not be promoted Ansar to PC!'];
            return Response::json($results);
        }
        else{
            $ansarUpdatedData->designation_id = 3;
            $ansarUpdatedData->save();  
            
            $results = ['status' => true, 'message' => 'Ansar ('.$requestAnsarPromotionData->ansar_id . ') has been successfully promoted to PC!'];
            return Response::json($results);
        }
        
    }

    function backtoPrevious(Request $request)
    {  
        $row_id = $request->request_id;
        $results = [];   

        $requestAnsarPromotionData = AnsarPromotion::findOrFail($row_id);
        $requestedAnsar = $requestAnsarPromotionData->ansar_id;
        //$requestedAnsar->status = "Back To Previous";

        $ansarStatusLogData = AnsarPromotionStatusLog::where('ansar_id', $requestedAnsar)->firstOrFail();
        
        $ansarStatusData = AnsarStatusInfo::findOrFail($requestedAnsar);
        //echo '<pre>';print_r($ansarStatusData);exit;
        $ansarStatusData->free_status = $ansarStatusLogData->free_status;
        $ansarStatusData->pannel_status = $ansarStatusLogData->pannel_status;
        $ansarStatusData->offer_sms_status = $ansarStatusLogData->offer_sms_status;
        $ansarStatusData->offered_status = $ansarStatusLogData->offered_status;
        $ansarStatusData->embodied_status = $ansarStatusLogData->embodied_status;
        $ansarStatusData->offer_block_status = $ansarStatusLogData->offer_block_status;
        $ansarStatusData->freezing_status = $ansarStatusLogData->freezing_status;
        $ansarStatusData->early_retierment_status = $ansarStatusLogData->early_retierment_status;
        $ansarStatusData->block_list_status = $ansarStatusLogData->block_list_status;
        $ansarStatusData->black_list_status = $ansarStatusLogData->black_list_status;
        $ansarStatusData->rest_status = $ansarStatusLogData->rest_status;
        $ansarStatusData->retierment_status = $ansarStatusLogData->retierment_status;
        $ansarStatusData->save();  
        $requestAnsarPromotionData->delete(); 
        // $requestAnsar->requested_type = "remove";  
        // $requestAnsar->status = 0;   
        // $requestAnsar->action_id = Auth::user()->id;
        // $requestAnsar->save();
        //$message = "Ansar ".$ansar_id." Delete in the pending list. Please wait for approval";
     
       $results = ['status' => true, 'message' => 'Ansar ('.$ansarStatusLogData->ansar_id . ') has been successfully back to previous!'];
        return Response::json($results);
    }

    public function SendToPanelBatchUpload(Request $request)
    {
    //     $row_id = $request->request_id;
    //     $comment = $request->comment;
    //     $results = [];   

    //     $requestAnsar = AnsarPromotion::findOrFail($row_id);
        
    //    $results = ['status' => true, 'message' => 'Ansar ('.$requestAnsar->ansar_id . ') Successfully sent to panel...'];
    //     return Response::json($results);

        $date = Carbon::yesterday()->format('d-M-Y H:i:s');
        $rules = [
            'memorandumId' => 'required',
            'ansar_id' => 'required|is_array|array_type:int',
            'merit' => 'required|is_array|array_type:int|array_length_same:ansar_id',
            'panel_date' => ["required", "after:{$date}", "date_format:d-M-Y H:i:s"],
        ];
        $valid = Validator::make($request->all(), $rules);
        if ($valid->fails()) {
            return Response::json(['status' => false, 'message' => 'Invalid request']);
        }
        $selected_ansars = $request->input('ansar_id');
        DB::beginTransaction();
        $user = [];
        try {
            $n = Carbon::now();
            $mi = $request->input('memorandumId');
            $pd = $request->input('panel_date');
            $modified_panel_date = Carbon::parse($pd)->format('Y-m-d H:i:s');
            $come_from_where = $request->input('come_from_where');
            $ansar_merit = $request->input('merit');
            $memorandum_entry = new MemorandumModel();
            $memorandum_entry->memorandum_id = $mi;
            $memorandum_entry->save();
            if (!is_null($selected_ansars)) {
                for ($i = 0; $i < count($selected_ansars); $i++) {
                    $ansar = PersonalInfo::where('ansar_id', $selected_ansars[$i])->first();
                    if ($ansar && ($ansar->verified == 0 || $ansar->verified == 1)) {
                        $ansar->verified = 2;
                        $ansar->save();
                    }
                    if ($come_from_where == 1) {

                        $panel_entry = new PanelModel;
                        $panel_entry->ansar_id = $selected_ansars[$i];
                        $panel_entry->come_from = "Rest";
                        $panel_entry->panel_date = $modified_panel_date;
                        $panel_entry->re_panel_date = $modified_panel_date;
                        $panel_entry->memorandum_id = $mi;
                        $panel_entry->ansar_merit_list = $ansar_merit[$i];
                        $panel_entry->action_user_id = Auth::user()->id;
                        $panel_entry->save();

                        $rest_info = RestInfoModel::where('ansar_id', $selected_ansars[$i])->first();

                        $rest_log_entry = new RestInfoLogModel();
                        $rest_log_entry->old_rest_id = $rest_info->id;
                        $rest_log_entry->old_embodiment_id = $rest_info->old_embodiment_id;
                        $rest_log_entry->old_memorandum_id = $rest_info->memorandum_id;
                        $rest_log_entry->ansar_id = $selected_ansars[$i];
                        $rest_log_entry->rest_date = $rest_info->rest_date;
                        $rest_log_entry->total_service_days = $rest_info->total_service_days;
                        $rest_log_entry->rest_type = $rest_info->rest_form;
                        $rest_log_entry->disembodiment_reason_id = $rest_info->disembodiment_reason_id;
                        $rest_log_entry->comment = $rest_info->comment;
                        $rest_log_entry->move_to = "Panel";
                        $rest_log_entry->move_date = $modified_panel_date;
                        $rest_log_entry->action_user_id = Auth::user()->id;
                        $rest_log_entry->save();

                        $rest_info->delete();
                        AnsarStatusInfo::where('ansar_id', $selected_ansars[$i])->update(['free_status' => 0, 'offer_sms_status' => 0, 'offered_status' => 0, 'block_list_status' => 0, 'black_list_status' => 0, 'rest_status' => 0, 'embodied_status' => 0, 'pannel_status' => 1, 'freezing_status' => 0]);

                        array_push($user, ['ansar_id' => $selected_ansars[$i], 'action_type' => 'PANELED', 'from_state' => 'REST', 'to_state' => 'PANELED', 'action_by' => auth()->user()->id]);
                    } else {
                        
                        $ansar->deleteCount();
                        $ansar->deleteOfferStatus();
                        $panel_entry = new PanelModel;
                        $panel_entry->ansar_id = $selected_ansars[$i];
                        $panel_entry->come_from = "Entry";
                        $panel_entry->panel_date = $modified_panel_date;
                        $panel_entry->re_panel_date = $modified_panel_date;
                        $panel_entry->memorandum_id = $mi;
                        $panel_entry->ansar_merit_list = $ansar_merit[$i];
                        $panel_entry->action_user_id = Auth::user()->id;
                        $panel_entry->save();

                        AnsarStatusInfo::where('ansar_id', $selected_ansars[$i])->update(['free_status' => 0, 'offer_sms_status' => 0, 'offered_status' => 0, 'block_list_status' => 0, 'black_list_status' => 0, 'rest_status' => 0, 'embodied_status' => 0, 'pannel_status' => 1, 'freezing_status' => 0]);

                        array_push($user, ['ansar_id' => $selected_ansars[$i], 'action_type' => 'PANELED', 'from_state' => 'FREE', 'to_state' => 'PANELED', 'action_by' => auth()->user()->id]);
                    }

                }
            }
            DB::commit();
            CustomQuery::addActionlog($user, true);
            $this->dispatch(new RearrangePanelPositionGlobal());
            $this->dispatch(new RearrangePanelPositionLocal());
        } catch (\Exception $e) {
            DB::rollback();
            return Response::json(['status' => false, 'message' => "Ansar/s not added to panel"]);
        }
        return Response::json(['status' => true, 'message' => "Ansar/s added to panel successfully"]);
    }
}
