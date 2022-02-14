<?php

namespace App\Console;

use App\Console\Commands\NotificationServer;
use App\Console\Commands\RedistributeGlobalPositions;
use App\Console\Commands\RedistributeLocalPosition;
use App\Console\Commands\RemoveUnverified;
use App\Helper\Facades\GlobalParameterFacades;
use App\Helper\Helper;
use App\Helper\GlobalParameter;
use App\Helper\SMSTrait;
use App\Jobs\BlockForAge;
use App\Jobs\RearrangePanelPositionGlobal;
use App\Jobs\RearrangePanelPositionLocal;
use App\Jobs\UnblockRetireAnsar;
use App\modules\HRM\Models\AnsarFutureState;
use App\modules\HRM\Models\AnsarRetireHistory;
use App\modules\HRM\Models\AnsarStatusInfo;
use App\modules\HRM\Models\EmbodimentModel;
use App\modules\HRM\Models\EmbodimentDailyLog;
use App\modules\HRM\Models\FreezingInfoModel;
use \App\modules\HRM\Models\BlockListModel;
use App\modules\HRM\Models\KpiDetailsModel;
use App\modules\HRM\Models\KpiGeneralModel;
use App\modules\HRM\Models\OfferBlockedAnsar;
use App\modules\HRM\Models\OfferCancel;
use App\modules\HRM\Models\OfferSMS;
use App\modules\HRM\Models\OfferSMSStatus;
use App\modules\HRM\Models\PanelInfoLogModel;
use App\modules\HRM\Models\PanelModel;
use App\modules\HRM\Models\RestInfoModel;
use App\modules\HRM\Models\SmsReceiveInfoModel;
use App\modules\recruitment\Models\JobAcceptedApplicant;
use App\modules\recruitment\Models\JobCircular;
use App\modules\SD\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Nathanmac\Utilities\Parser\Facades\Parser;

class Kernel extends ConsoleKernel {

    use SMSTrait;

    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\Inspire::class,
        NotificationServer::class,
        RedistributeGlobalPositions::class,
        RedistributeLocalPosition::class,
        RemoveUnverified::class,
    ];

    /**
     * Kernel constructor.
     *
     *
     * /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule) {

        // Start send_offer_sms scheduler - send offer from tbl_sms_offer_info table  sms try 0          
        $schedule->call(function () {

            $offered_ansar = OfferSMS::with(['ansar', 'district'])->where('sms_try', 0)->where('sms_status', 'Queue')->take(10)->get();
            if (count($offered_ansar) > 0) {
                Log::info("Sending offer " . count($offered_ansar));
            }
            foreach ($offered_ansar as $offer) {
                DB::connection('hrm')->beginTransaction();
                
                 Log::info("test by rintu");
                        
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
                    // Log::info($response);
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
                    DB::connection('hrm')->commit();
                } catch (\Exception $e) {
                    //Log::info('OFFER SEND ERROR: ' . $e->getTraceAsString());
                    DB::connection('hrm')->rollback();
                }
            }
         })->everyMinute()->name("send_offer_sms")->withoutOverlapping();
         
        // Start send_failed_offer scheduler - send offer from tbl_sms_offer_info table  sms status failed     
        $schedule->call(function () {
            
            $offered_ansar = OfferSMS::with(['ansar', 'district'])->where('sms_status', 'Failed')->take(10)->get();
            foreach ($offered_ansar as $offer) {
                DB::connection('hrm')->beginTransaction();
                try {

                    $a = $offer->ansar;
                    $dis = $offer->district->unit_name_eng;
                    $dc = strtoupper($dis);
                    $sms_end_date = Carbon::parse($offer->sms_end_datetime)->format('d-m-Y h:i:s A');
                    $body = "Apni (ID:{$offer->ansar_id}, {$a->designation->name_eng}) aaj {$dis} theke offer peyesen. Please type (ans YES ) and send korun 6969 number e {$sms_end_date} tarikh er moddhey . Otherwise  offer ti cancel hoie jabe-DC {$dc}";
                    $phone = '88' . trim($a->mobile_no_self);
                    $response = $this->sendSMS($phone, $body);
                    $r = Parser::xml($response);
                    //Log::info("SERVER RESPONSE : " . json_encode($r));
                    $offer->sms_try += 1;
                    $offer->err_msg = 'Failed';
                    if (isset($r['PARAMETER']) && strcasecmp($r['PARAMETER'], 'OK') == 0 && isset($r['SMSINFO']['MSISDN']) && strcasecmp($r['SMSINFO']['MSISDN'], '88' . trim($a->mobile_no_self)) == 0) {
                        $offer->sms_status = 'Send';
                        $offer->save();
                    } else {
                        $offer->sms_status = 'Failed';
                        $offer->save();
                    }
                    $count = $offer->getOfferCount();
                    $offer_limit = +GlobalParameterFacades::getValue(GlobalParameter::MAXIMUM_OFFER_LIMIT);
                    if ($count == $offer_limit - 1) {
                        $BanglaText = "এটা আপনার $offer_limit নং অফার। এই অফার  YES না করলে আপনি আর অফার পাবেন না। শতর্ক হউন।";
                        $body = strtoupper(bin2hex(iconv('UTF-8', 'UCS-2BE', $BanglaText)));
                        //$this->sendSMS($phone, "Et apnaer $offer_limit no offer. Ei offer YES na korle apni ar offer paben na. Sotorko houn");
                        $this->sendSMS($phone, $body);
                    }
                    DB::connection('hrm')->commit();
                } catch (\Exception $e) {
                    //Log::info('OFFER SEND ERROR: ' . $e->getMessage());
                    DB::connection('hrm')->rollback();
                }
            }
        })->everyMinute()->name("send_failed_offer")->withoutOverlapping();
        
        // Start offer_cancel scheduler - send cancel offer sms from tbl_offer_cancel table  sms status 0
        $schedule->call(function () {
            // Log::info("called : offer_cancel");
            $offered_cancel = OfferCancel::where('sms_status', 0)->take(10)->get();
            foreach ($offered_cancel as $offer) {
                $a = $offer->ansar;
                $body = 'Your offer is cancelled';
                $phone = '88' . trim($a->mobile_no_self);
                $response = $this->sendSMS($phone, $body);
                $r = simplexml_load_string($response);
                //  Log::info(json_encode($r));
                $offer->sms_status = 1;
                $offer->save();
            }
        })->everyMinute()->name("offer_cancel")->withoutOverlapping();
        
        // Start revert_offer scheduler - process offer block count and rearrange all after offer time end     
        $schedule->call(function () {
            // Log::info("REVERT OFFER");
            $offeredAnsars = OfferSMS::where('sms_end_datetime', '<=', Carbon::now()->toDateTimeString())->get();
            $c = OfferSMS::where('sms_end_datetime', '<=', Carbon::now()->toDateTimeString())->count();
            foreach ($offeredAnsars as $ansar) {
                //  Log::info("CALLED START: OFFER NO REPLY" . $ansar->ansar_id);
                DB::beginTransaction();
                try {
                    $count = $ansar->getOfferCount();
                    $pi = $ansar->ansar;
                    $pa = $pi->panel;
                    $maximum_offer_limit = (int) GlobalParameterFacades::getValue(GlobalParameter::MAXIMUM_OFFER_LIMIT) - 1;
                    if ($count >= $maximum_offer_limit) {
                        $ansar->deleteCount();
                        $ansar->deleteOfferStatus();
                        $ansar->blockAnsarOffer();
                        if ($pa) {
                            $ansar->status()->update([
                                'offer_sms_status' => 0,
                                'offer_block_status' => 1,
                            ]);
                            $pa->panelLog()->save(new PanelInfoLogModel([
                                'ansar_id' => $pa->ansar_id,
                                'merit_list' => $pa->ansar_merit_list,
                                'panel_date' => $pa->panel_date,
                                're_panel_date' => $pa->re_panel_date,
                                'old_memorandum_id' => !$pa->memorandum_id ? "N\A" : $pa->memorandum_id,
                                'movement_date' => Carbon::today(),
                                'come_from' => $pa->come_from,
                                'move_to' => 'Offer',
                                'go_panel_position' => $pa->go_panel_position,
                                're_panel_position' => $pa->re_panel_position,
                                'comment' => 'Move to offer block. No reply within 48 hours, Offer_ds:' . $ansar->district_id
                            ]));
                            $pa->delete();
                        }
                    } else {
                        $ansar->saveCount();
                        $offer_status = OfferSMSStatus::where(['ansar_id' => $ansar->ansar_id])->first();
                        if ($pa) {
                            $t = explode(",", $offer_status->offer_type);
                            if (is_array($t)) {
                                $len = count($t);
                                if (strcasecmp($t[$len - 1], "RE") == 0) {
                                    $pa->re_panel_date = Carbon::now()->format('Y-m-d H:i:s');
                                } else if (strcasecmp($t[$len - 1], "GB") == 0 || strcasecmp($t[$len - 1], "DG") == 0 || strcasecmp($t[$len - 1], "CG") == 0) {
                                    $pa->panel_date = Carbon::now()->format('Y-m-d H:i:s');
                                }
                            }
                            $pa->locked = 0;
                            $pa->come_from = "Offer";
                            $pa->save();
                            $pi->status()->update([
                                'pannel_status' => 1,
                                'offer_sms_status' => 0,
                            ]);
                        } else {
                            $panel_log = PanelInfoLogModel::where('ansar_id', $ansar->ansar_id)->select('old_memorandum_id')->first();
                            $ansar->status()->update([
                                'offer_sms_status' => 0,
                                'pannel_status' => 1,
                            ]);
                            $ansar->panel()->save(new PanelModel([
                                'memorandum_id' => isset($panel_log->old_memorandum_id) ? $panel_log->old_memorandum_id : 'N\A',
                                'panel_date' => Carbon::now()->format('Y-m-d H:i:s'),
                                're_panel_date' => Carbon::now()->format('Y-m-d H:i:s'),
                                'come_from' => 'Offer',
                                'ansar_merit_list' => 1,
                                'go_panel_position' => null,
                                're_panel_position' => null
                            ]));
                        }
                    }
                    $ansar->saveLog('No Reply');
                    $ansar->delete();
                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollback();
                    // Log::info("ERROR: " . $e->getMessage());
                }
            }
            if ($c > 0) {
                dispatch(new RearrangePanelPositionLocal());
                dispatch(new RearrangePanelPositionGlobal());
            }
        })->everyMinute()->name("revert_offer")->withoutOverlapping();

        // Start revert_offer_accepted scheduler - process offer block count and rearrange all after response Yes but not attend 
        $schedule->call(function () {
            $offeredAnsars = SmsReceiveInfoModel::all();
            $now = Carbon::now();
            $c = 0;
            foreach ($offeredAnsars as $ansar) {
                if ($now->diffInDays(Carbon::parse($ansar->sms_received_datetime)) >= 7) {
                    $c++;
                    //Log::info("CALLED START: OFFER ACCEPTED" . $ansar->ansar_id);
                    DB::beginTransaction();
                    try {
                        $pa = $ansar->panel;
                        $count = $ansar->getOfferCount();
                        $maximum_offer_limit = (int) GlobalParameterFacades::getValue(GlobalParameter::MAXIMUM_OFFER_LIMIT) - 1;
                        if ($count >= $maximum_offer_limit) {
                            $ansar->deleteCount();
                            $ansar->deleteOfferStatus();
                            $ansar->blockAnsarOffer();
                            $ansar->status()->update([
                                'offer_sms_status' => 0,
                                'offer_block_status' => 1,
                            ]);
                            if ($pa) {
                                $pa->panelLog()->save(new PanelInfoLogModel([
                                    'ansar_id' => $pa->ansar_id,
                                    'merit_list' => $pa->ansar_merit_list,
                                    'panel_date' => $pa->panel_date,
                                    're_panel_date' => $pa->re_panel_date,
                                    'old_memorandum_id' => !$pa->memorandum_id ? "N\A" : $pa->memorandum_id,
                                    'movement_date' => Carbon::today(),
                                    'come_from' => $pa->come_from,
                                    'move_to' => 'Offer',
                                    'go_panel_position' => $pa->go_panel_position,
                                    're_panel_position' => $pa->re_panel_position,
                                    'comment' => 'Move to offer block. reply: yes, wait: 7 days. Offer_ds:' . $ansar->offered_district
                                ]));
                                $pa->delete();
                            }
                        } else {
                            $ansar->saveCount();
                            $offer_status = OfferSMSStatus::where(['ansar_id' => $ansar->ansar_id])->first();
                            if ($pa) {
                                if ($offer_status) {
                                    $t = explode(",", $offer_status->offer_type);
                                    if (is_array($t)) {
                                        $len = count($t);
                                        if ($len > 0 && strcasecmp($t[$len - 1], "RE") == 0) {
                                            $pa->re_panel_date = Carbon::now()->format('Y-m-d H:i:s');
                                        } else if ($len > 0 && (strcasecmp($t[$len - 1], "GB") == 0 || strcasecmp($t[$len - 1], "DG") == 0 || strcasecmp($t[$len - 1], "CG") == 0)) {
                                            $pa->panel_date = Carbon::now()->format('Y-m-d H:i:s');
                                        }
                                    }
                                } elseif (!in_array($ansar->offered_district, Config::get('app.offer'))) {
                                    $pa->re_panel_date = Carbon::now()->format('Y-m-d H:i:s');
                                } else {
                                    $pa->panel_date = Carbon::now()->format('Y-m-d H:i:s');
                                }
                                $pa->locked = 0;
                                $pa->come_from = "Offer";
                                $pa->save();
                                $ansar->status()->update([
                                    'pannel_status' => 1,
                                    'offer_sms_status' => 0,
                                ]);
                            } else {
                                $panel_log = PanelInfoLogModel::where('ansar_id', $ansar->ansar_id)->select('old_memorandum_id')->first();
                                $ansar->status()->update([
                                    'offer_sms_status' => 0,
                                    'pannel_status' => 1,
                                ]);
                                $ansar->panel()->save(new PanelModel([
                                    'memorandum_id' => isset($panel_log->old_memorandum_id) ? $panel_log->old_memorandum_id : 'N\A',
                                    'panel_date' => Carbon::now()->format('Y-m-d H:i:s'),
                                    'come_from' => 'Offer',
                                    'ansar_merit_list' => 1,
                                    're_panel_date' => Carbon::now()->format('Y-m-d H:i:s'),
                                    'go_panel_position' => null,
                                    're_panel_position' => null
                                ]));
                            }
                        }
                        $ansar->saveLog();
                        $ansar->delete();
                        DB::commit();
                    } catch (\Exception $e) {
                        DB::rollback();
                        //Log::info("ERROR: " . $e->getMessage());
                    }
                }
            }
            if ($c > 0) {
                dispatch(new RearrangePanelPositionLocal());
                dispatch(new RearrangePanelPositionGlobal());
            }
        })->dailyAt("23:55")->name("revert_offer_accepted")->withoutOverlapping();

        // Start withdraw_kpi scheduler - process KPI withdraw date is over
        $schedule->call(function () {
            $withdraw_kpi_ids = KpiDetailsModel::where('kpi_withdraw_date', '<=', Carbon::now())->whereNotNull('kpi_withdraw_date')->get();
            foreach ($withdraw_kpi_ids as $withdraw_kpi_id) {
                $kpi_info = KpiGeneralModel::find($withdraw_kpi_id->kpi_id);
                $kpi_info->status_of_kpi = 0;
                $kpi_info->withdraw_status = 1;
                $kpi_info->save();
                $withdraw_kpi_id->kpi_withdraw_date = NULL;
                $withdraw_kpi_id->save();
                $embodiment_infos = EmbodimentModel::where('kpi_id', $withdraw_kpi_id->kpi_id)->get();
                foreach ($embodiment_infos as $embodiment_info) {
                    $freeze_info_update = new FreezingInfoModel();
                    $freeze_info_update->ansar_id = $embodiment_info->ansar_id;
                    $freeze_info_update->freez_reason = "Guard Withdraw";
                    $freeze_info_update->freez_date = Carbon::now();
                    $freeze_info_update->kpi_id = $withdraw_kpi_id->kpi_id;
                    $freeze_info_update->ansar_embodiment_id = $embodiment_info->id;
                    $freeze_info_update->save();
                    $embodiment_info->emboded_status = "Freeze";
                    $embodiment_info->save();
                    AnsarStatusInfo::where('ansar_id', $embodiment_info->ansar_id)->update(['embodied_status' => 0, 'freezing_status' => 1]);
                }
            }
        })->dailyAt("00:00")->name('withdraw_kpi')->withoutOverlapping();

        // Start rest_to_panel scheduler - process Ansar after test Date is over
        $schedule->call(function () {
            $rest_ansars = RestInfoModel::whereDate('active_date', '<=', Carbon::today()->toDateString())->whereIn('disembodiment_reason_id', [1, 2, 8])->get();
            //Log::info("REST to PANEl : CALLED");

            foreach ($rest_ansars as $ansar) {

                if (!in_array(AnsarStatusInfo::REST_STATUS, $ansar->status->getStatus()) || in_array(AnsarStatusInfo::BLOCK_STATUS, $ansar->status->getStatus()) || in_array(AnsarStatusInfo::BLACK_STATUS, $ansar->status->getStatus()))
                    continue;
                DB::beginTransaction();
                try {
                    $panel_log = PanelInfoLogModel::where('ansar_id', $ansar->ansar_id)->orderBy('id', 'desc')->first();
                    PanelModel::create([
                        'ansar_id' => $ansar->ansar_id,
                        'come_from' => 'Rest',
                        'panel_date' => Carbon::today(),
                        're_panel_date' => Carbon::today(),
                        'memorandum_id' => isset($panel_log->old_memorandum_id) ? $panel_log->old_memorandum_id : 'N\A',
                        'ansar_merit_list' => isset($panel_log->merit_list) ? $panel_log->merit_list : 'N\A',
                        'action_user_id' => '0',
                    ]);
                    $ansar->status->update([
                        'pannel_status' => 1,
                        'rest_status' => 0,
                    ]);
                    $ansar->saveLog('Panel');
                    $ansar->delete();
                    DB::commit();
                    //Log::info("REST to PANEl :" . $ansar->ansar_id);
                } catch (\Exception $e) {
                    DB::rollBack();
                    //Log::info("REST to PANEl FAILED:" . $ansar->ansar_id);
                }
            }
        })->twiceDaily(0, 12)->name('rest_to_panel')->withoutOverlapping();

        // Start rest_to_panel scheduler - process Ansar after 1 year if disambodiment reason is DICIPLINARY
        $schedule->call(function () {
            $rest_ansars = RestInfoModel::whereRaw('FLOOR(DATEDIFF(rest_date,NOW())/365)>=1')->where('disembodiment_reason_id', 5)->get();
            //Log::info("REST to PANEl DICIPLINARY : CALLED");

            foreach ($rest_ansars as $ansar) {

                if (!in_array(AnsarStatusInfo::REST_STATUS, $ansar->status->getStatus()) || in_array(AnsarStatusInfo::BLOCK_STATUS, $ansar->status->getStatus()) || in_array(AnsarStatusInfo::BLACK_STATUS, $ansar->status->getStatus()))
                    continue;
                DB::beginTransaction();
                try {
                    $panel_log = PanelInfoLogModel::where('ansar_id', $ansar->ansar_id)->orderBy('id', 'desc')->first();
                    PanelModel::create([
                        'ansar_id' => $ansar->ansar_id,
                        'come_from' => 'Rest',
                        'panel_date' => Carbon::today(),
                        're_panel_date' => Carbon::today(),
                        'memorandum_id' => isset($panel_log->old_memorandum_id) ? $panel_log->old_memorandum_id : 'N\A',
                        'ansar_merit_list' => isset($panel_log->merit_list) ? $panel_log->merit_list : 'N\A',
                        'action_user_id' => '0',
                    ]);
                    $ansar->status->update([
                        'pannel_status' => 1,
                        'rest_status' => 0,
                    ]);
                    $ansar->saveLog('Panel');
                    $ansar->delete();
                    DB::commit();
                    //Log::info("REST to PANEl :" . $ansar->ansar_id);
                } catch (\Exception $e) {
                    DB::rollBack();
                    //Log::info("REST to PANEl FAILED:" . $ansar->ansar_id);
                }
            }
        })->twiceDaily(0, 12)->name('rest_to_panel_disciplaney_action')->withoutOverlapping();
        
        
        // Start rest_to_panel scheduler - process Ansar after 1 year if disambodiment reason is DICIPLINARY
        $schedule->call(function () {
            
        })->twiceDaily(0, 12)->name("ansar_retirement")->withoutOverlapping();
        
        
        // Start disable_circular scheduler - disable circullar after time end
        $schedule->call(function () {
            //Log::info("called : disable circular");
            DB::connection('recruitment')->beginTransaction();
            try {
                $circulars = JobCircular::where('status', 'active')->where('end_date', '<=', Carbon::now()->format('Y-m-d'))->get();
                foreach ($circulars as $circular) {
                    $circular->status = 'inactive';
                    $circular->payment_status = 'off';
                    $circular->save();
                    DB::connection('recruitment')->commit();
                }
            } catch (\Exception $e) {
                DB::connection('recruitment')->rollback();
            }
        })->dailyAt("23:50")->name("disable_circular")->withoutOverlapping();


        /*   $schedule->call(function () {
          Log::info("called : send_sms_to_accepted_applicant");
          $messID = uniqid('SB_');
          $messageID = $messID;
          $apiUser = 'join_ans_vdp';
          $apiPass = 'shurjoSM123';
          $applicants = JobAcceptedApplicant::with('applicant')->whereHas('applicant', function ($q) {
          $q->where('status', 'accepted');
          })->where('message_status', 'pending')->where('sms_status', 'on')->limit(10)->get();
          foreach ($applicants as $a) {
          if ($a->applicant) {
          $sms_data = http_build_query(
          array(
          'API_USER' => $apiUser,
          'API_PASSWORD' => $apiPass,
          'MOBILE' => $a->applicant->mobile_no_self,
          'MESSAGE' => $a->message,
          'MESSAGE_ID' => $messageID
          )
          );

          $ch = curl_init();
          $url = "https://shurjobarta.shurjorajjo.com.bd/barta_api/api.php";
          curl_setopt($ch, CURLOPT_URL, $url);
          curl_setopt($ch, CURLOPT_POST, 1);                //0 for a get request
          curl_setopt($ch, CURLOPT_POSTFIELDS, $sms_data);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
          curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
          curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
          $response = curl_exec($ch);
          curl_close($ch);
          var_dump($response);
          $a->message_status = 'send';
          $a->save();
          }
          }

          })->everyMinute()->name("send_sms_to_selected_applicant")->withoutOverlapping(); */


        $schedule->call(function () {
            //Log::info("called : generate attendance");
            //            DB::enableQueryLog();
            $kpis = KpiGeneralModel::with(['embodiment' => function ($q) {
                            $q->select('ansar_id', 'kpi_id', 'emboded_status');
                            $q->whereHas('ansar.status', function ($q) {
                                        $q->where('embodied_status', 1);
                                        $q->where('freezing_status', 0);
                                        $q->where('block_list_status', 0);
                                    });
                        }])->where('status_of_kpi', 1)
                    ->select('id', 'kpi_name');
//            return DB::getQueryLog();
            $now = Carbon::now();
            $day = $now->format('d');
            $month = $now->format('m');
            $year = $now->format('Y');
            $kpis->chunk(1000, function ($datas) use ($day, $month, $year) {

                $inserts = [];
                $bindings = [];
                
                foreach ($datas as $data) {
                    //Log::info('KPI_ID : ' . $data->id);
                    foreach ($data->embodiment as $em) {
                        $qs = [
                            '?',
                            '?',
                            '?',
                            '?',
                            '?',
                        ];
                        $bindings[] = $data->id;
                        $bindings[] = $em->ansar_id;
                        $bindings[] = $day;
                        $bindings[] = $month;
                        $bindings[] = $year;
                        $inserts[] = '(' . implode(",", $qs) . ')';
                        $p[] = $em->emboded_status;
                    }
                }
                $query = "INSERT IGNORE INTO tbl_attendance(kpi_id,ansar_id,day,month,year) VALUES " . implode(",", $inserts);
                DB::connection('sd')->beginTransaction();
                try {
                    DB::connection('sd')->insert($query, $bindings);
                    DB::connection('sd')->commit();
                } catch (\Exception $e) {

                    DB::connection('sd')->rollback();
                    return $e->getMessage();
                }
            });
        })->dailyAt("00:05")->name("generate_attendance")->withoutOverlapping();
//        $schedule->call(function () {
//            Log::info("called : unblock panel locked");
//            PanelModel::where('locked', 1)->update(['locked' => 0]);
//
//
//        })->everyThirtyMinutes()->name("panel_unlock")->withoutOverlapping();
//        $schedule->call(function () {
//            Log::info("called : offer block to panel");
//            DB::connection('hrm')->beginTransaction();
//            try {
//                $currentDate = Carbon::now()->format('Y-m-d');
//                $unit = GlobalParameterFacades::getUnit(GlobalParameter::OFFER_BLOCK_PERIOD);
//                $value = GlobalParameterFacades::getValue(GlobalParameter::OFFER_BLOCK_PERIOD);
//                switch (strtolower($unit)) {
//                    case 'year':
//                        $blocked_ansars = OfferBlockedAnsar::whereRaw("TIMESTAMPDIFF(YEAR,blocked_date,'$currentDate')>=$value")->take(1000)->get();
//                        break;
//                    case 'month':
//                        $blocked_ansars = OfferBlockedAnsar::whereRaw("TIMESTAMPDIFF(MONTH,blocked_date,'$currentDate')>=$value")->take(1000)->get();
//                        break;
//                    case 'day':
//                        $blocked_ansars = OfferBlockedAnsar::whereRaw("TIMESTAMPDIFF(DAY,blocked_date,'$currentDate')>=$value")->take(1000)->get();
//                        break;
//                    default:
//                        dd('Invalid Parameter');
//                }
//
//                foreach ($blocked_ansars as $blocked_ansar) {
//                    $now = Carbon::now();
//                    $panel_log = PanelInfoLogModel::where('ansar_id', $blocked_ansar->ansar_id)->orderBy('panel_date', 'desc')->first();
//                    PanelModel::create([
//                        'memorandum_id' => $panel_log && isset($panel_log->old_memorandum_id) ? $panel_log->old_memorandum_id : 'N\A',
//                        'panel_date' => $now,
//                        'come_from' => 'Offer Cancel',
//                        'ansar_merit_list' => 1,
//                        'ansar_id' => $blocked_ansar->ansar_id,
//                    ]);
//                    AnsarStatusInfo::where('ansar_id', $blocked_ansar->ansar_id)->update(['offer_block_status' => 0, 'pannel_status' => 1]);
//                    $blocked_ansar->status = "unblocked";
//                    $blocked_ansar->unblocked_date = Carbon::now()->format('Y-m-d');
//                    $blocked_ansar->save();
//                    $blocked_ansar->delete();
//                }
//                DB::commit();
//            } catch (\Exception $exception) {
//                DB::rollback();
//                return ['status' => false, 'message' => $exception->getMessage()];
//            }
//            return ['status' => true, 'message' => 'Sending to panel complete'];
//
//
//        })->everyThirtyMinutes()->name("offer_block_to_panel_6_month")->withoutOverlapping();
        
        
        // Start ansar_block_for_age3 scheduler - send ansar 500 for block for age job       
        $schedule->call(function () {

            $count = PanelModel::whereHas('ansarInfo.status', function ($q) {
                        $q->where('block_list_status', 0);
                        $q->where('pannel_status', 1);
                        $q->where('black_list_status', 0);
                    })->count();
            //Log::info("called : Ansar Block For Age".$count);
            for ($i = 0; $i < $count; $i += 500) {
                dispatch(new BlockForAge($i));
            }
        })->daily()->name("ansar_block_for_age3")->withoutOverlapping();
        
        
        // Start UnblockRetireAnsar scheduler - ansar_unblock_for_age   
        $schedule->call(function () {
            //Log::info("ansar_unblock_for_age:");
            $ansars = AnsarRetireHistory::all();
            DB::connection('hrm')->beginTransaction();
            try {
                $now = \Carbon\Carbon::now();
                foreach ($ansars as $ansar) {

                    $info = $ansar->ansar;
                    $dob = $info->data_of_birth;

                    $age = \Carbon\Carbon::parse($dob)->diff($now, true);
                    $ansarRe = GlobalParameterFacades::getValue('retirement_age_ansar') - 3;
                    $pcApcRe = GlobalParameterFacades::getValue('retirement_age_pc_apc') - 3;
                    //echo("called : Ansar Block For Age-".$ansar->ansar_id."Age:".$age->y."year ".$age->m."month ".$age->d." days");
                    if ($info->designation->code == "ANSAR" && $age->y < $ansarRe) {
                        if ($ansar->retire_from == 'panel') {
                            $pl = PanelInfoLogModel::where('ansar_id', $info->ansar_id)->orderBy('panel_date', 'desc')->first();
                            $info->panel()->create([
                                'ansar_merit_list' => $pl->merit_list,
                                'panel_date' => $pl->panel_date,
                                're_panel_date' => $pl->re_panel_date,
                                'memorandum_id' => $pl->old_memorandum_id,
                                'come_from' => 'After Retier'
                            ]);
                            $info->status->update([
                                'pannel_status' => 1,
                                'retierment_status' => 0
                            ]);
                        }
                        $ansar->delete();
                    } else if (($info->designation->code == "PC" || $info->designation->code == "APC") && $age->y < $pcApcRe) {
                        $pl = PanelInfoLogModel::where('ansar_id', $info->ansar_id)->orderBy('panel_date', 'desc')->first();
                        $info->panel()->create([
                            'ansar_merit_list' => $pl->merit_list,
                            'panel_date' => $pl->panel_date,
                            're_panel_date' => $pl->re_panel_date,
                            'memorandum_id' => $pl->old_memorandum_id,
                            'come_from' => 'After Retier'
                        ]);
                        $info->status->update([
                            'pannel_status' => 1,
                            'retierment_status' => 0
                        ]);
                        $ansar->delete();
                    }
                }

                DB::connection('hrm')->commit();
            } catch (\Exception $e) {
                //Log::info("ansar_unblock_for_age:".$e->getMessage());
                DB::connection('hrm')->rollback();
            }
            dispatch(new RearrangePanelPositionGlobal());
            dispatch(new RearrangePanelPositionLocal());
        })->everyTenMinutes()->name("UnblockRetireAnsar")->withoutOverlapping();

        // Start ansar_future_state_execute scheduler - move ansar one status to another with condition
        $schedule->call(function() {
            $ansars = AnsarFutureState::where('activation_date', "<=", Carbon::now()->toDateTimeString())->get();
            $panel_count = 0;
            DB::beginTransaction();
            try {
                foreach ($ansars as $ansar) {
                    switch ($ansar->to_status) {
                        case "Panel":
                            $panel_count++;
                            $ansar->moveToPanel();
                            $ansar->delete();
                            break;
                        case "Embodiment":
                            $ansar->moveToEmbodiment();
                            $ansar->delete();
                            break;
                        case "Rest":
                            $ansar->moveToRest();
                            $ansar->delete();
                            break;
                        case "Unverified":
                            $ansar->moveToUnverified();
                            $ansar->delete();
                            break;
                        case "Free":
                            $ansar->moveToFree();
                            $ansar->delete();
                            break;
                        case "Block":
                            $ansar->moveToBlock();
                            $ansar->delete();
                            break;
                    }
                }
                if ($panel_count > 0) {
                    dispatch(new RearrangePanelPositionLocal());
                    dispatch(new RearrangePanelPositionGlobal());
                }
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                echo $e->getTraceAsString();
            }
        })->dailyAt("00:45")->name("ansar_future_state_execute")->withoutOverlapping();
	//   })->everyMinute()->name("ansar_future_state_execute")->withoutOverlapping();
        
        // save daily embodiment total count (add by rintu kumar chowdhury)
        $schedule->call(function () {
            $today = Carbon::now()->format('Y-m-d');
            $dailyLog = EmbodimentDailyLog::where(['date' => $today])->first();
            
            if(!$dailyLog){
                $totalEmbodied = DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->join('tbl_embodiment', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_embodiment.ansar_id')->join('tbl_kpi_info', 'tbl_embodiment.kpi_id', '=', 'tbl_kpi_info.id')->where('embodied_status', 1)->where('block_list_status', 0)->distinct()->count('tbl_ansar_parsonal_info.ansar_id');
                $totalAnsarEmbodied = DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->join('tbl_embodiment', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_embodiment.ansar_id')->join('tbl_kpi_info', 'tbl_embodiment.kpi_id', '=', 'tbl_kpi_info.id')->where('embodied_status', 1)->where('block_list_status', 0)->where('tbl_ansar_parsonal_info.designation_id', 1)->distinct()->count('tbl_ansar_parsonal_info.ansar_id');
                $totalAPCEmbodied = DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->join('tbl_embodiment', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_embodiment.ansar_id')->join('tbl_kpi_info', 'tbl_embodiment.kpi_id', '=', 'tbl_kpi_info.id')->where('embodied_status', 1)->where('block_list_status', 0)->where('tbl_ansar_parsonal_info.designation_id', 2)->distinct()->count('tbl_ansar_parsonal_info.ansar_id');
                $totalPCEmbodied = DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->join('tbl_embodiment', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_embodiment.ansar_id')->join('tbl_kpi_info', 'tbl_embodiment.kpi_id', '=', 'tbl_kpi_info.id')->where('embodied_status', 1)->where('block_list_status', 0)->where('tbl_ansar_parsonal_info.designation_id', 3)->distinct()->count('tbl_ansar_parsonal_info.ansar_id');

                EmbodimentDailyLog::create([
                        'date' => $today,
                        'total' => $totalEmbodied,
                        'ansar' => $totalAnsarEmbodied,
                        'apc' => $totalAPCEmbodied,
                        'pc' => $totalPCEmbodied,
                    ]);
            }
            
        })->twiceDaily(17, 23)->name("save_daily_emobodiment")->withoutOverlapping();
        //})->everyMinute()->name("save_daily_emobodiment")->withoutOverlapping();
        
        
        //// unblocked all fixed time based blocked ansar after time over (add by rintu kumar chowdhury)
        $schedule->call(function () {
            
            $blocked_periodic_ansars = BlockListModel::where(['is_periodic'=> 1, 'date_for_unblock'=> NULL])->whereDate('assigned_unblock_date', '<', Carbon::now())->get();     
   
            foreach ($blocked_periodic_ansars as $blocked_periodic_ansar) {
                DB::connection('hrm')->beginTransaction();
                try {

                $ansar_id     = $blocked_periodic_ansar->ansar_id;
                $unblock_date = Carbon::now()->format("Y-m-d");
                $moveStatus   = $blocked_periodic_ansar->assigned_unblock_stutus;
                $memorandumId = 'N/A';

                $ansar = AnsarStatusInfo::where('ansar_id', $ansar_id)->first();

                if (empty($ansar) || !in_array(AnsarStatusInfo::BLOCK_STATUS, $ansar->getStatus())) {
                   // throw new \Exception("This ansar is not in block list");
                      break;
                }
                
                
                $ansar_unblock_details = [
                    'date_for_unblock' => $unblock_date,
                    'comment_for_unblock' => 'Unblock by System'
                ];

               // $this->removeOtherStatusExceptBlock($ansar);
                
                if (!empty($ansar) && in_array(AnsarStatusInfo::BLOCK_STATUS, $ansar->getStatus())) {
            
                    if (in_array(AnsarStatusInfo::PANEL_STATUS, $ansar->getStatus())) {
                        //$ansar->panel->saveLog("Blocklist", Carbon::now()->format('Y-m-d'), '44.03.0000.048.50.007.18-577 Date:Oct-27-2019');
                        //$ansar->panel->delete();
                    } elseif (in_array(AnsarStatusInfo::EMBODIMENT_STATUS, $ansar->getStatus())) {
                        $ansar->embodiment->saveLog('Blocklist', Carbon::now()->format('Y-m-d'), '', '');
                        $ansar->embodiment->delete();
                    } elseif (in_array(AnsarStatusInfo::REST_STATUS, $ansar->getStatus())) {
                        $ansar->rest->saveLog('Blocklist', Carbon::now()->format('Y-m-d'), '');
                        $ansar->rest->delete();
                    }
                }
               
                $blocklist_entry = BlockListModel::where('ansar_id', $ansar_id)->orderBy('id', 'desc')->first();
                $blocklist_entry->update($ansar_unblock_details);
                $blocklist_entry->save();
                switch (strtolower($moveStatus)) {
                    case "free":
                        $ansar->updateToFreeState()->save();
                        break;
                    case "rest":
                        RestInfoModel::create([
                            'ansar_id' => $ansar_id,
                            'old_embodiment_id' => 0,
                            'memorandum_id' => $memorandumId,
                            'rest_date' => Carbon::now()->format("Y-m-d"),
                            'active_date' => Carbon::now()->addMonths(6)->format('Y-m-d'),
                            'disembodiment_reason_id' => 8,
                            'total_service_days' => 0,
                            'rest_form' => 'Block',
                            'comment' => 'After unblock move to rest status',
                            'action_user_id' => 0
                        ]);
                        $ansar->updateToRestState()->save();
                        break;
                    case "panel":
                    $myansar=DB::table('tbl_ansar_parsonal_info')->where('ansar_id',$ansar_id)->first();
                        $gender=$myansar->sex;
                        $designation=$myansar->designation_id;

                        if($designation==1)
                        {
                            $max_go_panel_position= DB::table('tbl_panel_info')
                            ->join('tbl_ansar_parsonal_info', 'tbl_panel_info.ansar_id', '=','tbl_ansar_parsonal_info.ansar_id')
                            ->where('tbl_ansar_parsonal_info.designation_id', $designation)
                            ->where('tbl_ansar_parsonal_info.sex', $gender)
                            ->max('go_panel_position')+1;

                            $max_re_panel_position= DB::table('tbl_panel_info')
                            ->join('tbl_ansar_parsonal_info', 'tbl_panel_info.ansar_id', '=','tbl_ansar_parsonal_info.ansar_id')
                            ->where('tbl_ansar_parsonal_info.designation_id', $designation)
                            ->where('tbl_ansar_parsonal_info.sex', $gender)
                            ->max('re_panel_position')+1;
                        }
                        else
                        {
                            $max_go_panel_position= DB::table('tbl_panel_info')
                            ->join('tbl_ansar_parsonal_info', 'tbl_panel_info.ansar_id', '=','tbl_ansar_parsonal_info.ansar_id')
                            ->where('tbl_ansar_parsonal_info.designation_id', $designation)
                            ->max('go_panel_position')+1;

                            $max_re_panel_position= DB::table('tbl_panel_info')
                            ->join('tbl_ansar_parsonal_info', 'tbl_panel_info.ansar_id', '=','tbl_ansar_parsonal_info.ansar_id')
                            ->where('tbl_ansar_parsonal_info.designation_id', $designation)
                            ->max('re_panel_position')+1;
                        }

                        PanelModel::create([
                            'ansar_id' => $ansar_id,
                            'come_from' => 'Block',
                            'panel_date' => Carbon::now(),
                            're_panel_date' => Carbon::now(),
                            'memorandum_id' => $memorandumId,
                            'ansar_merit_list' => 'N\A',
                            'action_user_id' => 0,
                            'go_panel_position' => $max_go_panel_position,
                            're_panel_position' => $max_re_panel_position,
                        ]);
                        $ansar->updateToPanelState()->save();

                        break;
                    case "not_verified":
                        $ansar->ansar->update(['verified' => 0]);
                        break;
                }

            
                    DB::connection('hrm')->commit();
                } catch (\Exception $e) {
                    //Log::info('OFFER SEND ERROR: ' . $e->getTraceAsString());
                    DB::connection('hrm')->rollback();
                } 
            }
        })->twiceDaily(17, 23)->name("unblock_fixed_period_block_ansars")->withoutOverlapping();


    }
    
    
    
}
