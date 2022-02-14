<?php

namespace App\modules\HRM\Controllers;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Jobs\OfferQueue;
use App\Helper\Facades\GlobalParameterFacades;
use App\Helper\GlobalParameter;
use App\Helper\SMSTrait;
use App\Jobs\RearrangePanelPositionGlobal;
use App\Jobs\RearrangePanelPositionLocal;
use App\modules\HRM\Models\ActionUserLog;
use App\modules\HRM\Models\AnsarStatusInfo;
use App\modules\HRM\Models\CustomQuery;
use App\modules\HRM\Models\OfferCancel;
use App\modules\HRM\Models\OfferQuota;
use App\modules\HRM\Models\OfferSMS;
use App\modules\HRM\Models\OfferSmsLog;
use App\modules\HRM\Models\OfferSMSStatus;
use App\modules\HRM\Models\OfferZone;
use App\modules\HRM\Models\PanelInfoLogModel;
use App\modules\HRM\Models\PanelModel;
use App\modules\HRM\Models\PersonalInfo;
use App\modules\HRM\Models\RequestDumper;
use App\modules\HRM\Models\UserOfferQueue;
use App\modules\HRM\Models\OfferBlockedAnsar;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Nathanmac\Utilities\Parser\Facades\Parser;


class OfferController extends Controller
{
      use SMSTrait;

    //
    function makeOffer()
    {
        $dis = Auth::user()->district_id;
        //if ($dis) return View::make('HRM::Offer.offer_view')->with(['isFreeze' => CustomQuery::isAnsarFreezeInDistrict($dis)]);
//        else return View::make('HRM::Offer.offer_view')->with(['isFreeze' => false]);
        return View::make('HRM::Offer.offer_view')->with(['isFreeze' => false]);
    }

    function getQuotaCount()
    {
        return Response::json(['total_offer' => Helper::getOfferQuota(Auth::user())]);
    }

    function calculateQuota($id)
    {

        return Helper::getOfferQuota(Auth::user());
    }

    function getKpi(Request $request)
    {   //echo 'rintu'; exit;
        /*We failed to determine its functionality and where this function is used.[22/03/2020-Sabbir & Ayman]*/
        //return response(collect(['type' => 'error', 'message' => "Internal Server Error!!"])->toJson(), 400, ['Content-Type' => 'application/json']);
       
        //$global_offer = array( 2,7,8,9,11,12,16,18,26,31,42,48,55,65,66,67,68,69,70,71,72,74,75);
        $global_offer = array(11,18,42,65,66,67,68,69,70,71,72,74,75);
        $send_offer_enabled = env('SEND_OFFER_ENABLED', true);
        
        if (!$send_offer_enabled){
            return response(collect(['type' => 'error', 'message' => "Sending offer is disabled! Please contact with admin."])->toJson(), 200, ['Content-Type' => 'application/json']);
        }
        
        
        $rules = [];
        $rules['pc_male'] = 'required|numeric|regex:/^[0-9]+$/|min:0';
        $rules['pc_female'] = 'required|numeric|regex:/^[0-9]+$/|min:0';
        $rules['apc_male'] = 'required|numeric|regex:/^[0-9]+$/|min:0';
        $rules['apc_female'] = 'required|numeric|regex:/^[0-9]+$/|min:0';
        $rules['ansar_male'] = 'required|numeric|regex:/^[0-9]+$/|min:0';
        $rules['ansar_female'] = 'required|numeric|regex:/^[0-9]+$/|min:0';
        if (Auth::user()->type == 11) {
            $rules['district'] = 'required';
        } else if (Auth::user()->type == 22) {
            $rules['exclude_district'] = 'required|numeric|regex:/^[0-9]+$/';
        }
        $valid = Validator::make($request->all(), $rules);
        if ($valid->fails()) {
            
            return response(collect(['type' => 'error', 'message' => 'Invalid request'])->toJson(), 400, ['Content-Type' => 'application/json']);
        }
        DB::beginTransaction();
        try {
            
            
             if (UserOfferQueue::where('user_id', Auth::user()->id)->exists()) {
              //  throw new \Exception("Your have one pending offer.Please wait until your offer is complete");
			  return response(collect(['type' => 'error', 'message' => "Your have one pending offer.Please wait until your offer is complete."])->toJson(), 200, ['Content-Type' => 'application/json']);
            }
            $userOffer = UserOfferQueue::create([
                'user_id' => Auth::user()->id
            ]);
            $user = Auth::user();
            if ($user->type == 22) {
                $district_id = $user->district_id;
                 //if (in_array($district_id, Config::get('app.offer'))) {
                if (in_array($district_id, $global_offer)) {    
                    $offer_type = 'GB';
                    //Log::info('global offer');
                    //Log::info($global_offer);
                } else {
                    $offer_type = 'RE';
                    //Log::info('re offer');
                    //Log::info($global_offer);
                }
            } else {
                $district_id = $request->get('district_id');
                //if (in_array($district_id, Config::get('app.offer'))) {
                if (in_array($district_id, $global_offer)) {    
                    $offer_type = 'GB';
                } else {
                    $offer_type = 'RE';
                }
            }
            // $offerZone = OfferZone::where('unit_id', $user->district_id)->pluck('offer_zone_unit_id')->toArray();
            
            
            //check offer zone 
            
           
            $com_ctg_range = array(2,7,8,9,11,16,26,31,48,55,72);
            if(in_array($user->district_id, $com_ctg_range)){
                $offerZone= array();
            }else{
                $offerZone = OfferZone::where('unit_id', $user->district_id)->pluck('offer_zone_unit_id')->toArray();  
            }
            
            
            $data = CustomQuery::getAnsarInfo(
                ['male' => $request->get('pc_male'), 'female' => $request->get('pc_female')],
                ['male' => $request->get('apc_male'), 'female' => $request->get('apc_female')],
                ['male' => $request->get('ansar_male'), 'female' => $request->get('ansar_female')],
                $request->get('district'),
                $request->get('exclude_district'), Auth::user(), $offerZone, $offer_type, $district_id);
            
            echo count($data); exit;
            //PanelModel::whereIn('ansar_id', $data)->update(['locked' => 1]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response(collect(['type' => 'error', 'message' => $e->getMessage()])->toJson(), 400, ['Content-Type' => 'application/json']);
        }
        return Response::json($data);
    }

    function sendOfferSMS(Request $request)
    {

        //var_dump($request);die;
       //Log::info($global_offer);
        //subrata change blobal offer array
        // $global_offer = array( 42,18,42,66,67,68,69,65,71,70,72,74,75,2,7,8,9,11,12,16,26,31,48,55,72);
        
        //$global_offer = array( 2,7,8,9,11,12,16,18,26,31,42,48,55,65,66,67,68,69,70,71,72,74,75);
        $global_offer = array(11,18,42,65,66,67,68,69,70,71,72,74,75);
         
        $send_offer_enabled = env('SEND_OFFER_ENABLED', true);
        
        if (!$send_offer_enabled){
            return response(collect(['type' => 'error', 'message' => "Sending offer is disabled! Please contact with admin."])->toJson(), 200, ['Content-Type' => 'application/json']);
        }

        $rules = [];
        $rules['pc_male'] = 'required|numeric|regex:/^[0-9]+$/|min:0';
        $rules['pc_female'] = 'required|numeric|regex:/^[0-9]+$/|min:0';
        $rules['apc_male'] = 'required|numeric|regex:/^[0-9]+$/|min:0';
        $rules['apc_female'] = 'required|numeric|regex:/^[0-9]+$/|min:0';
        $rules['ansar_male'] = 'required|numeric|regex:/^[0-9]+$/|min:0';
        $rules['ansar_female'] = 'required|numeric|regex:/^[0-9]+$/|min:0';
        $rules['district_id'] = 'required|numeric|regex:/^[0-9]+$/';
        $rules['memorandum_id'] = 'required|unique:hrm.tbl_memorandum_id,memorandum_id';

        if (Auth::user()->type == 11) {
            $rules['district'] = 'required';
        } else if (Auth::user()->type == 22) {
            $rules['exclude_district'] = 'required|numeric|regex:/^[0-9]+$/';
        }

        $valid = Validator::make($request->all(), $rules);
        if ($valid->fails()) {
          // Log::info($valid->messages()->toArray());
            return response(collect(['type' => 'error', 'message' => 'Invalid request'])->toJson(), 400, ['Content-Type' => 'application/json']);
        }
        DB::beginTransaction();
        try {
            if (UserOfferQueue::where('user_id', Auth::user()->id)->exists()) {
              //  throw new \Exception("Your have one pending offer.Please wait until your offer is complete");
			  return response(collect(['type' => 'error', 'message' => "Your have one pending offer.Please wait until your offer is complete."])->toJson(), 200, ['Content-Type' => 'application/json']);
            }
            $userOffer = UserOfferQueue::create([
                'user_id' => Auth::user()->id
            ]);
            $user = Auth::user();
            if ($user->type == 22) {
                $district_id = $user->district_id;
                 //if (in_array($district_id, Config::get('app.offer'))) {
                if (in_array($district_id, $global_offer)) {    
                    $offer_type = 'GB';
                    //Log::info('global offer');
                    //Log::info($global_offer);
                } else {
                    $offer_type = 'RE';
                    //Log::info('re offer');
                    //Log::info($global_offer);
                }
            } else {
                $district_id = $request->get('district_id');
                //if (in_array($district_id, Config::get('app.offer'))) {
                if (in_array($district_id, $global_offer)) {    
                    $offer_type = 'GB';
                } else {
                    $offer_type = 'RE';
                }
            }
           // $offerZone = OfferZone::where('unit_id', $user->district_id)->pluck('offer_zone_unit_id')->toArray();
            
            
            //check offer zone 
            
           
            $com_ctg_range = array(2,7,8,9,11,16,26,31,48,55,72);
            
            if(in_array($user->district_id, $com_ctg_range)){
                $offerZone= array();
            }else{
                $offerZone = OfferZone::where('unit_id', $user->district_id)->pluck('offer_zone_unit_id')->toArray();  
            }
            
            $data = CustomQuery::getAnsarInfo(
                ['male' => $request->get('pc_male'), 'female' => $request->get('pc_female')],
                ['male' => $request->get('apc_male'), 'female' => $request->get('apc_female')],
                ['male' => $request->get('ansar_male'), 'female' => $request->get('ansar_female')],
                $request->get('district'),
                $request->get('exclude_district'), $user, $offerZone, $offer_type, $district_id);
     
            //Log::info($request->all());
            RequestDumper::create([
                'user_id' => auth()->user()->id,
                'request_ip' => $request->ip(),
                'request_url' => $request->url(),
                'request_data' => serialize($request->all()),
                'header'=>serialize($request->header()),
                'response_data' => serialize($data)
            ]);
           $quota = Helper::getOfferQuota(Auth::user());
           
           

        if ($quota !== false && $quota < count($data)) throw new \Exception("Your offer quota limit exit");
            PanelModel::whereIn('ansar_id', $data)->update(['locked' => 1]);
            $this->dispatch(new OfferQueue($data, $district_id, Auth::user(), $userOffer, $offer_type, $request->get('memorandum_id')));           
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response(collect(['status' => 'error', 'message' => $e->getMessage()])->toJson(), 400, ['Content-Type' => 'application/json']);
        }
		/*$ip  = !empty($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
		//$url = "http://freegeoip.net/json/103.99.249.186";
		$url = "https://api.ipdata.co?api-key=c2f42d581138c3cd3a88010b2cc035db19fe8c04715714d4676f90e2";
		$ch  = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		$data = curl_exec($ch);
		curl_close($ch);

		if ($data) {
			$location = json_decode($data);

			//$lat = $location->latitude;
			//$lon = $location->longitude;

			//$sun_info = date_sun_info(time(), $lat, $lon);
			//print_r($location);
			Log::info($data);
		}
		*/
		
        return Response::json(['type' => 'success', 'message' => "Offer Send Successfully"]);
    }


    function removeFromPanel($ansar)
    {
        $pa = $ansar->panel;
        $ansar->status()->update([
            'pannel_status' => 0,
            'offer_sms_status' => 1,
        ]);
        $ansar->panel->panelLog()->save(new PanelInfoLogModel([
            'ansar_id' => $pa->ansar_id,
            'merit_list' => $pa->ansar_merit_list,
            'panel_date' => $pa->panel_date,
            'old_memorandum_id' => !$pa->memorandum_id ? "N\A" : $pa->memorandum_id,
            'movement_date' => Carbon::today(),
            'come_from' => $pa->come_from,
            'move_to' => 'Offer',
        ]));
        $ansar->panel()->delete();
    }

    function removeFromRest($ansar_ids)
    {
        $as = AnsarStatusInfo::where('ansar_id', $ansar_ids)->first();
        $as->offer_sms_status = 1;
        return $as->save();
    }

    function offerQuota()
    {
        $quota = CustomQuery::offerQuota();
        return View::make('HRM::Offer.offer_quota', ['quota' => $quota]);
    }

    function getOfferQuota(Request $request)
    {
        return Response::json(CustomQuery::offerQuota($request->range ? $request->range : 'all'));
    }

    function updateOfferQuota(Request $request)
    {
        //return $request->get('quota_id');
        $rules = [
            'quota_id' => 'required|is_array|array_type:int',
            'quota_value' => 'required|is_array|array_length_same:quota_id'
        ];
        $valid = Validator::make($request->all(), $rules);
        if ($valid->fails()) {
            return Redirect::back()->with('error', "Invalid request");
        }
        $id = $request->get('quota_id');
        $quota = $request->get('quota_value');
        DB::beginTransaction();
        try {
            for ($i = 0; $i < count($id); $i++) {

                try {
                    $offer_quota = OfferQuota::where('unit_id', $id[$i])->firstOrFail();
                    $offer_quota->update(['quota' => $quota[$i]]);
                } catch (ModelNotFoundException $e) {
                    //return $e->getMessage();
                    $offer_quota = new OfferQuota;
                    $offer_quota->unit_id = $id[$i];
                    $offer_quota->quota = $quota[$i];
                    $offer_quota->saveOrFail();
                }
                DB::commit();
            }
        } catch (\Exception $e) {
            DB::rollback();
            return Redirect::back()->with('error', $e->getMessage());
        }
        return Redirect::back()->with('success', 'Offer quota updated successfully');
    }

    function handleCancelOffer()
    {
        $rules = [
            'ansar_ids' => 'required|is_array|array_type:int|array_length_min:1'
        ];
        $vaild = Validator::make(Input::all(), $rules);
        if ($vaild->fails()) {
            return response(collect(['type' => 'error', 'message' => "Invalid request(400)"]), 400, ['Content-Type' => 'application/json']);
        }
        $result = ['success' => 0, 'fail' => 0];
        $ansar_ids = Input::get('ansar_ids');
        for ($i = 0; $i < count($ansar_ids); $i++) {
            DB::beginTransaction();
            try {
                $ansar = PersonalInfo::where('ansar_id', $ansar_ids[$i])->first();
                $panel_date = Carbon::now()->format("Y-m-d H:i:s");
                $offered_ansar = $ansar->offer_sms_info;
                $os = OfferSMSStatus::where('ansar_id', $ansar_ids[$i])->first();
                if (!$offered_ansar) $received_ansar = $ansar->receiveSMS;
                if ($offered_ansar && $offered_ansar->come_from == 'rest') {
                    $ansar->status()->update([
                        'offer_sms_status' => 0,
                        'rest_status' => 1,
                    ]);
                } else {
                    $pa = $ansar->panel;
                    if (!$pa) {
                        $panel_log = $ansar->panelLog()->first();
                        $ansar->panel()->save(new PanelModel([
                            'memorandum_id' => $panel_log->old_memorandum_id,
                            'panel_date' => $os && $os->isGlobalOfferRegion() ? $panel_date : $panel_log->panel_date,
                            're_panel_date' => $os && $os->isRegionalOfferRegion() ? $panel_date : $panel_log->re_panel_date,
                            'come_from' => 'OfferCancel',
                            'ansar_merit_list' => 1,
                            'action_user_id' => auth()->user()->id,
                        ]));

                    } else {
                        $pa->locked = 0;
                        $pa->come_from = 'OfferCancel';
                        if ($os && $os->isGlobalOfferRegion()) {
                            $pa->panel_date = $panel_date;
                        } elseif ($os && $os->isRegionalOfferRegion()) {
                            $pa->re_panel_date = $panel_date;
                        }
                        $pa->save();
                    }
                    $ansar->status()->update([
                        'offer_sms_status' => 0,
                        'pannel_status' => 1,
                    ]);
                }
                $ansar->offerCancel()->save(new OfferCancel([
                    'offer_cancel_date' => Carbon::now()
                ]));

                if ($os) {
                    $ot = explode(",", $os->offer_type);
                    $ou = explode(",", $os->last_offer_units);
                    $ot = array_slice($ot, 0, count($ot) - 1);
                    $ou = array_slice($ou, 0, count($ou) - 1);
                    $os->offer_type = implode(",", $ot);
                    $os->last_offer_units = implode(",", $ou);
                    $os->last_offer_unit = !count($ou) ? "" : $ou[count($ou) - 1];
                    $os->save();
                }
                if ($offered_ansar) {
                    $ansar->offerLog()->save(new OfferSmsLog([
                        'offered_date' => $offered_ansar->sms_send_datetime,
                        'action_date' => Carbon::now(),
                        'offered_district' => $offered_ansar->district_id,
                        'action_user_id' => auth()->user()->id,
                        'reply_type' => 'No Reply',
                        'comment' => 'Offer Cancel'
                    ]));
                    $offered_ansar->delete();
                } else {
                    $ansar->offerLog()->save(new OfferSmsLog([
                        'offered_date' => $received_ansar->sms_send_datetime,
                        'offered_district' => $received_ansar->offered_district,
                        'action_user_id' => auth()->user()->id,
                        'action_date' => Carbon::now(),
                        'reply_type' => 'Yes',
                        'comment' => 'Offer Cancel'
                    ]));
                    $received_ansar->delete();
                }
                DB::commit();
                auth()->user()->actionLog()->save(new ActionUserLog([
                    'ansar_id' => $ansar_ids[$i],
                    'action_type' => 'CANCEL OFFER',
                    'from_state' => 'OFFER',
                    'to_state' => 'PANEL'
                ]));
                $result['success']++;
            } catch (\Exception $e) {
                DB::rollback();
                return response(collect(['type' => 'error', 'message' => $e->getMessage()]), 400, ['Content-Type' => 'application\json']);
            }
        }
        if (count($ansar_ids)) {
            $this->dispatch(new RearrangePanelPositionGlobal());
            $this->dispatch(new RearrangePanelPositionLocal());
        }
        return Response::json(['type' => 'success', 'message' => 'Offer cancel successfully']);
    }

    function cancelOfferView()
    {
        return View::make('HRM::Offer.offer_cancel_view');
    }

    function getOfferedAnsar()
    {
        $rules = [
            'district_id' => 'required|numeric|regex:/^[0-9]+$/'
        ];
        $valid = Validator::make(Input::all(), $rules);
        if ($valid->fails()) {
            return response(collect(['type' => 'error', 'message' => 'Invalid request']), 400, ['Content-Type' => 'application\json']);
        }
        $district_id = Input::get('district_id');
        $gender = Input::get('gender');
        $rank = Input::get('rank');
        $offer_noreply_ansar = DB::table('tbl_sms_offer_info')
            ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_sms_offer_info.ansar_id')
            ->join('tbl_designations', 'tbl_ansar_parsonal_info.designation_id', '=', 'tbl_designations.id')
            ->join('tbl_units', 'tbl_ansar_parsonal_info.unit_id', '=', 'tbl_units.id')
            ->where('tbl_sms_offer_info.district_id', '=', $district_id);
        $offer_accepted_ansar = DB::table('tbl_sms_receive_info')
            ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_sms_receive_info.ansar_id')
            ->join('tbl_designations', 'tbl_ansar_parsonal_info.designation_id', '=', 'tbl_designations.id')
            ->join('tbl_units', 'tbl_ansar_parsonal_info.unit_id', '=', 'tbl_units.id')
            ->where('tbl_sms_receive_info.offered_district', '=', $district_id);

        if (isset($gender) && ($gender == 'Male' || $gender == 'Female' || $gender == 'Other')) {
            $offer_noreply_ansar = $offer_noreply_ansar->where('tbl_ansar_parsonal_info.sex', '=', $gender);
            $offer_accepted_ansar = $offer_accepted_ansar->where('tbl_ansar_parsonal_info.sex', '=', $gender);
        }
        if (isset($rank) && !empty($rank) && is_numeric($rank)) {
            $offer_noreply_ansar = $offer_noreply_ansar->where('tbl_designations.id', '=', $rank);
            $offer_accepted_ansar = $offer_accepted_ansar->where('tbl_designations.id', '=', $rank);
        }

        $clone_offer_noreply_ansar = clone $offer_noreply_ansar;
        $clone_offer_accepted_ansar = clone $offer_accepted_ansar;

        $count_offer_noreply_ansar = $clone_offer_noreply_ansar->groupBy('tbl_designations.id')->select(DB::raw("count('tbl_ansar_parsonal_info.ansar_id') as t"), 'tbl_designations.code');
        $count_offer_accepted_ansar = $clone_offer_accepted_ansar->groupBy('tbl_designations.id')->select(DB::raw("count('tbl_ansar_parsonal_info.ansar_id') as t"), 'tbl_designations.code');
        $a1 = collect($count_offer_noreply_ansar->get())->pluck('t', 'code')->toArray();
        $a2 = collect($count_offer_accepted_ansar->get())->pluck('t', 'code')->toArray();

        $sums = [];
        foreach (array_keys($a1 + $a2) as $key) {
            $sums[$key] = (isset($a1[$key]) ? $a1[$key] : 0) + (isset($a2[$key]) ? $a2[$key] : 0);
        }

        $offer_noreply_ansar = $offer_noreply_ansar->select('tbl_sms_offer_info.ansar_id', 'tbl_sms_offer_info.sms_send_datetime', 'tbl_sms_offer_info.sms_end_datetime', 'tbl_sms_offer_info.district_id', 'tbl_ansar_parsonal_info.ansar_name_bng', 'tbl_ansar_parsonal_info.sex', 'tbl_designations.name_bng', 'tbl_units.unit_name_bng');
        $offer_accepted_ansar = $offer_accepted_ansar->select('tbl_sms_receive_info.ansar_id', 'tbl_sms_receive_info.sms_send_datetime', 'tbl_sms_receive_info.sms_end_datetime', 'tbl_sms_receive_info.offered_district', 'tbl_ansar_parsonal_info.ansar_name_bng', 'tbl_ansar_parsonal_info.sex', 'tbl_designations.name_bng', 'tbl_units.unit_name_bng');
        $list = $offer_noreply_ansar->unionAll($offer_accepted_ansar);

        return Response::json(['list' => $list->get(), 'tCount' => $sums]);
    }

    function testSmsPurpose()
    {
        return null;
        $user = "ansarapi";
        $pass = "75@5S01j";
        $sid = "ANSARVDPBANGLA";
        $url = "http://sms.sslwireless.com/pushapi/dynamic/server.php";
        $param = "user=$user&pass=$pass&sms[0][0]=8801712363785&sms[0][1]=" . urlencode("Test
        SMS 1") . "&sms[0][2]=123456789&sid=$sid";
        $crl = curl_init();
        curl_setopt($crl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($crl, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($crl, CURLOPT_URL, $url);
        curl_setopt($crl, CURLOPT_HEADER, 0);
        curl_setopt($crl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($crl, CURLOPT_POST, 1);
        curl_setopt($crl, CURLOPT_POSTFIELDS, $param);
        $response = curl_exec($crl);
        curl_close($crl);
        $xmlvalue = simplexml_load_string($response);
        return Response::json($xmlvalue);
    }
    
    function SendSMSFromHRM() {
		
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', '0');
		
        $offered_ansar = OfferSMS::with(['ansar', 'district'])->where('sms_try', 0)->where('sms_status', 'Queue')->orderBy('id', 'ASC')->take(10)->get();
		
		//print_r($offered_ansar); exit;
		
        if (count($offered_ansar) > 0) {
            Log::info("Sending offer " . count($offered_ansar));
        }
        foreach ($offered_ansar as $offer) {
            //DB::connection('hrm')->beginTransaction();
            try {

                $a = $offer->ansar;
                $maximum_offer_limit = (int) GlobalParameterFacades::getValue(GlobalParameter::MAXIMUM_OFFER_LIMIT);
                $count = $offer->getOfferCount();
                $dis = $offer->district->unit_name_eng;
                $dc = $dis == "DMA HSIA" ? strtoupper("DHAKA AIRPORT") : strtoupper($dis);
                $sms_end_date = Carbon::parse($offer->sms_end_datetime)->format('d-m-Y h:i:s A');

                //$body = "Apni (ID:{$offer->ansar_id}, {$a->designation->name_eng}) aaj {$dis} theke offer peyesen. Please type (ans YES ) and send korun 26969 number e {$sms_end_date} tarikh er moddhey . Otherwise  offer ti cancel hoie jabe-DC {$dc}";

                $BanglaText = "আপনি";
                $body = strtoupper(bin2hex(iconv('UTF-8', 'UCS-2BE', $BanglaText)));
                //Log::info("Coverted to SSL Bangla Text");
                //Log::info($body);
                //Log::info("Dumping Phone Number");
                $phone = '88' . trim($a->mobile_no_self);
                // Log::info($phone);
                //$response = $this->sendSMS($phone, $body);
                //Log::info("Starting SMS Sending Processs");
                /* SMS Sending Process Starts */

                $BanglaText = "আপনি (ID:{$offer->ansar_id}, {$a->designation->name_eng}) আজ  {$dis} থেকে অফার পেয়েছেন । অনুগ্রহ করে (ans YES ) টাইপ করুন এবং পাঠিয়ে দিন ২৬৯৬৯ নাম্বার এ  {$sms_end_date} তারিখ এর মধ্যে  অন্যথায় অফারটি বাতিল হয়ে যাবে - {$dc}";

                $str = strtoupper(bin2hex(iconv('UTF-8', 'UCS-2BE', $BanglaText)));

                $user = 'ansarapi';
                $pass = '75@5S01j';
                $sid = 'ANSARVDPBANGLA';
                $url = "http://sms.sslwireless.com/pushapi/dynamic/server.php";

                $param = "user=$user&pass=$pass&sms[0][0]=$phone&sms[0][1]=" . urlencode($str) . "&sid=$sid";
                $crl = curl_init();
                curl_setopt($crl, CURLOPT_SSL_VERIFYPEER, FALSE);
                curl_setopt($crl, CURLOPT_SSL_VERIFYHOST, 2);
                curl_setopt($crl, CURLOPT_URL, $url);
                curl_setopt($crl, CURLOPT_HEADER, 0);
                curl_setopt($crl, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($crl, CURLOPT_POST, 1);
                curl_setopt($crl, CURLOPT_POSTFIELDS, $param);
                $response = curl_exec($crl);
                curl_close($crl);

                //Log::info('Dumping Response from within Kernel SMS call');
                //Log::info($response);
                /* SMS Sending Complete */
                $r = Parser::xml($response);
                // Log::info("Dumping Server Response");
                // Log::info("SERVER RESPONSE : " . json_encode($r));
                $offer->sms_try += 1;
                $offer->err_msg = json_encode($r);

                if (isset($r['PARAMETER']) && strcasecmp($r['PARAMETER'], 'OK') == 0 && isset($r['SMSINFO']['MSISDN']) && strcasecmp($r['SMSINFO']['MSISDN'], '88' . trim($a->mobile_no_self)) == 0) {
                    $offer->sms_status = 'Send';
                    $offer->save();
                } else {
                    $offer->sms_status = 'Failed';

                    $offer->save();
                }
                if ($count == $maximum_offer_limit - 1) {
                    $BanglaText = "এটা আপনার $maximum_offer_limit নং অফার। এই অফার  YES না করলে আপনি আর অফার পাবেন না। শতর্ক হউন।";
                    $body = strtoupper(bin2hex(iconv('UTF-8', 'UCS-2BE', $BanglaText)));
                    //$this->sendSMS($phone, "Et apnaer $maximum_offer_limit no offer. Ei offer YES na korle apni er offer paben na. Sotorko houn");
                    $this->sendSMS($phone, $body);
                }
				
				echo  $response; 
				
				
               // DB::connection('hrm')->commit();
            } catch (\Exception $e) {
                //Log::info('OFFER SEND ERROR: ' . $e->getTraceAsString());
              //  DB::connection('hrm')->rollback();
            }
        }
    }
    
    
    function monitorTools(){
        
        $data['total_queue_offer'] = OfferSMS::where('sms_status','Queue')->count();
        $data['total_queue_today'] = OfferSMS::where('sms_status','Queue')->where('sms_send_datetime',Carbon::today())->count();
        $data['total_unwanted_locked'] =   DB::select("
SELECT COUNT(t1.ansar_id) as total_ansar  FROM `tbl_panel_info` t1
LEFT JOIN `tbl_ansar_status_info` t2 ON t2.ansar_id = t1.ansar_id
WHERE t1.locked = 1 
AND t2.pannel_status = 1 
AND t1.ansar_id NOT IN (	
	SELECT t4.ansar_id FROM `tbl_sms_offer_info` t4 
	WHERE  t4.sms_end_datetime >= NOW()
) 
AND t1.ansar_id NOT IN (
	SELECT t3.ansar_id FROM `tbl_sms_receive_info` t3
	WHERE DATE(t3.`sms_received_datetime` ) + INTERVAL 7 DAY >= DATE(NOW())
)
AND t1.ansar_id NOT IN (SELECT t6.ansar_id FROM `tbl_blocklist_info` t6  WHERE t6.date_for_unblock IS NULL)
AND t1.ansar_id NOT IN (SELECT t7.ansar_id FROM `tbl_blacklist_info` t7 )
AND t1.ansar_id NOT IN (SELECT t8.ansar_id FROM `tbl_freezing_info` t8)");

                
/* SELECT COUNT(t5.ansar_id) AS total_ansar FROM tbl_panel_info t5
INNER JOIN `tbl_ansar_parsonal_info` t6 ON t6.ansar_id = t5.ansar_id
WHERE t5.locked = 1 
AND 
t5.ansar_id NOT IN (
SELECT t1.ansar_id FROM `tbl_panel_info` t1
LEFT JOIN `tbl_sms_offer_info` t2 ON t2.ansar_id = t1.ansar_id
WHERE t1.locked = 1 AND DATE(t2.`sms_end_datetime`) > NOW()

UNION ALL (
	SELECT c.ansar_id
FROM tbl_panel_info AS c
LEFT JOIN tbl_sms_send_log AS cch ON cch.ansar_id = c.ansar_id
WHERE
   cch.id = (
      SELECT MAX(id)
      FROM tbl_sms_send_log
      WHERE ansar_id = c.ansar_id
   )AND c.locked = 1 AND cch.reply_type = 'Yes' AND DATE_ADD(DATE(cch.`action_date`), INTERVAL 7 DAY) < NOW()
)
)*/

    //$data['total_unwanted_blocked'] =  DB::select('SELECT COUNT(t1.ansar_id) AS total_ansar FROM `tbl_offer_blocked_ansar` t1
//WHERE t1.status = "blocked" AND t1.ansar_id NOT IN (
//SELECT t2.ansar_id FROM `tbl_offer_status` t2
//WHERE (ROUND((CHAR_LENGTH(REPLACE(t2.offer_type,",",""))-CHAR_LENGTH(REPLACE(REPLACE(t2.offer_type,",",""),"RE","")))/CHAR_LENGTH("RE"))> 3 ) OR (ROUND((CHAR_LENGTH(REPLACE(t2.offer_type,",",""))-CHAR_LENGTH(REPLACE(REPLACE(t2.offer_type,",",""),"GB","")))/CHAR_LENGTH("GB"))> 3 ))');    
        
$data['total_unwanted_blocked'] =  DB::select('SELECT COUNT(t1.ansar_id) AS total_ansar  FROM `tbl_offer_blocked_ansar` t1
WHERE t1.status = "blocked" 
AND t1.ansar_id NOT IN (SELECT t2.ansar_id FROM `tbl_offer_status` t2 WHERE (ROUND((CHAR_LENGTH(REPLACE(t2.offer_type,",",""))-CHAR_LENGTH(REPLACE(REPLACE(t2.offer_type,",",""),"RE","")))/CHAR_LENGTH("RE"))> 3 ) OR (ROUND((CHAR_LENGTH(REPLACE(t2.offer_type,",",""))-CHAR_LENGTH(REPLACE(REPLACE(t2.offer_type,",",""),"GB","")))/CHAR_LENGTH("GB"))> 3 ))
AND t1.ansar_id IN (SELECT t3.ansar_id AS ansar
FROM `tbl_offer_blocked_ansar` t3
INNER JOIN `tbl_offer_status` t4 ON t4.ansar_id =  t3.ansar_id)
AND t1.ansar_id NOT IN (SELECT t6.ansar_id FROM `tbl_blocklist_info` t6  WHERE t6.date_for_unblock IS NULL)
AND t1.ansar_id NOT IN (SELECT t7.ansar_id FROM `tbl_blacklist_info` t7 )
AND t1.ansar_id NOT IN (SELECT t8.ansar_id FROM `tbl_freezing_info` t8)');   

        //print_r( $data['total_unwanted_blocked']); exit;
        return View::make('HRM::Offer.queue_report', ['data' => $data]);
        
    }
    
    
    function unblocked_ansar_test(){
        
        $currentDate = Carbon::now()->format('Y-m-d');
        
        $unit = GlobalParameterFacades::getUnit(GlobalParameter::OFFER_BLOCK_PERIOD);
        
        $value = GlobalParameterFacades::getValue(GlobalParameter::OFFER_BLOCK_PERIOD);
        
        switch (strtolower($unit)) {
            case 'year':
                $blocked_ansars = OfferBlockedAnsar::whereRaw("TIMESTAMPDIFF(YEAR,blocked_date,'$currentDate')>=$value")->take(1000)->get();
                break;
            case 'month':
                $blocked_ansars = OfferBlockedAnsar::whereRaw("TIMESTAMPDIFF(MONTH,blocked_date,'$currentDate')>=$value")->take(1000)->get();
                break;
            case 'day':
                $blocked_ansars = OfferBlockedAnsar::whereRaw("TIMESTAMPDIFF(DAY,blocked_date,'$currentDate')>=$value")->take(1000)->get();
                break;
            default:
                dd('Invalid Parameter');
        }
        
        
        
        print_r($blocked_ansars); exit;
                
        

    }
}
