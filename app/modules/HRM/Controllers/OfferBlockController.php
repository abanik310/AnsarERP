<?php

namespace App\modules\HRM\Controllers;

use App\modules\HRM\Models\AnsarStatusInfo;
use App\modules\HRM\Models\OfferBlockedAnsar;
use App\modules\HRM\Models\OfferSMS;
use App\modules\HRM\Models\OfferSmsLog;
use App\modules\HRM\Models\PanelInfoLogModel;
use App\modules\HRM\Models\PanelModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class OfferBlockController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        if ($request->ajax()) {
            
           
            $offer_blocked = AnsarStatusInfo::with(['offer_block', 'panel', 'ansar'])
                    ->where(function($query) {
                $query->where('pannel_status', 1)
                ->orWhere('offer_block_status', 1);
            });
            if ($request->ansar_id) {
                $offer_blocked->where('ansar_id', $request->ansar_id);
            }


            $ansars = $offer_blocked->paginate(30);

            foreach ($ansars as $key => $ansar) {

                if ($ansar->offer_block_status) {
                    $data = $ansar->get_unit($ansar->ansar_id);
                } elseif ($ansar->pannel_status) {
                    $data = $ansar->get_last_offer($ansar->ansar_id);
                } else {
                    //will not process
                }

                if (!count($ansar->offer_block) && !count($ansar->panel)) {
                    unset($ansars[$key]);
                }
                
            }
            
            //echo '<pre>';  print_r($ansars);exit;
            return view('HRM::offer_rollback.data', compact('ansars'));
        }
        return view('HRM::offer_rollback.offer_rollback');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        if ($request->type == 'rollback') {
            return response()->json($this->rollBackOffer($id));
        } else if ($request->type == 'sendtopanel') {
            return response()->json($this->sendToPanel($id));
        } else {
            return response()->json(['status' => false, 'message' => "Invalid request"]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        //
    }

    private function rollBackOffer($id) {
        DB::beginTransaction();
        try {
            
            $ansar_status_data =  AnsarStatusInfo::where('ansar_id', $id)->first();
            
            if($ansar_status_data->pannel_status == 1){
                $message = "Panel Ansar(".$id.") Offer ";
                $paneled_ansar = PanelModel::where('ansar_id', $id)->latest('id')->first();
                $last_offer_data =  OfferSmsLog::with('unit')->where('ansar_id', $id)->latest('id')->first();

                $now = Carbon::now();
                $endDate = Carbon::now()->addHours(24);
                OfferSMS::create([
                    'sms_send_datetime' => $now->format('Y-m-d h:i:s'),
                    'ansar_id' => $paneled_ansar->ansar_id,
                    'sms_end_datetime' => $endDate->format('Y-m-d h:i:s'),
                    'district_id' => $last_offer_data->offered_district,
                    'come_from' => 'Panel',
                    'action_user_id' => auth()->user()->id
                ]);
                AnsarStatusInfo::where('ansar_id', $paneled_ansar->ansar_id)->update(['pannel_status' => 0, 'offer_sms_status' => 1]);
//                $blocked_ansar->status = "unblocked";
//                $blocked_ansar->unblocked_date = Carbon::now()->format('Y-m-d');
//                $blocked_ansar->save();
//                $blocked_ansar->delete();
                
            }elseif ($ansar_status_data->offer_block_status == 1) {
                $message = "Offer Block Ansar(".$id.") Offer ";

                $blocked_ansar = OfferBlockedAnsar::findOrFail($id);
                $now = Carbon::now();
                $endDate = Carbon::now()->addHours(24);
                OfferSMS::create([
                    'sms_send_datetime' => $now->format('Y-m-d h:i:s'),
                    'ansar_id' => $blocked_ansar->ansar_id,
                    'sms_end_datetime' => $endDate->format('Y-m-d h:i:s'),
                    'district_id' => $blocked_ansar->last_offer_unit,
                    'come_from' => 'Offer Block',
                    'action_user_id' => auth()->user()->id
                ]);
                AnsarStatusInfo::where('ansar_id', $blocked_ansar->ansar_id)->update(['offer_block_status' => 0, 'offer_sms_status' => 1]);
                $blocked_ansar->status = "unblocked";
                $blocked_ansar->unblocked_date = Carbon::now()->format('Y-m-d');
                $blocked_ansar->save();
                $blocked_ansar->delete();
            }
            
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollback();
            return ['status' => false, 'message' => $exception->getMessage()];
        }
        return ['status' => true, 'message' => $message.'Rollback complete'];
    }

    private function sendToPanel($id) {
        DB::beginTransaction();
        try {
            $blocked_ansar = OfferBlockedAnsar::findOrFail($id);
            $now = Carbon::now();
            $panel_log = PanelInfoLogModel::where('ansar_id', $blocked_ansar->ansar_id)->orderBy('panel_date', 'desc')->first();
            PanelModel::create([
                'memorandum_id' => $panel_log && isset($panel_log->old_memorandum_id) ? $panel_log->old_memorandum_id : 'N\A',
                'panel_date' => $now,
                're_panel_date' => $now,
                'come_from' => 'Offer Cancel',
                'ansar_merit_list' => 1,
                'ansar_id' => $blocked_ansar->ansar_id,
            ]);
            AnsarStatusInfo::where('ansar_id', $blocked_ansar->ansar_id)->update(['offer_block_status' => 0, 'pannel_status' => 1]);
            $blocked_ansar->status = "unblocked";
            $blocked_ansar->unblocked_date = Carbon::now()->format('Y-m-d');
            $blocked_ansar->save();
            $blocked_ansar->delete();
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollback();
            return ['status' => false, 'message' => $exception->getMessage()];
        }
        return ['status' => true, 'message' => 'Sending to panel complete'];
    }

}
