<?php

namespace App\modules\HRM\Controllers;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Jobs\OfferQueue;
use App\Jobs\RearrangePanelPositionGlobal;
use App\Jobs\RearrangePanelPositionLocal;
use App\modules\HRM\Models\ActionUserLog;
use App\modules\HRM\Models\AnsarStatusInfo;
use App\modules\HRM\Models\CustomQuery;
use App\modules\HRM\Models\OfferCancel;
use App\modules\HRM\Models\OfferQuota;
use App\modules\HRM\Models\OfferSmsLog;
use App\modules\HRM\Models\OfferSMSStatus;
use App\modules\HRM\Models\OfferZone;
use App\modules\HRM\Models\PanelInfoLogModel;
use App\modules\HRM\Models\PanelModel;
use App\modules\HRM\Models\PersonalInfo;
use App\modules\HRM\Models\RequestDumper;
use App\modules\HRM\Models\UserOfferQueue;
use App\modules\HRM\Models\UserOtp;
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
use App\Helper\SMSTrait;

class OtpApiController extends Controller
{
	
	 use SMSTrait;

	
 
	
	
	
	// for https to http 
	
		 function sendOfferOTPRequest(Request $request){
			  
			$user_id = intval($request->userID);
			  
			//$url = "http://18.176.55.184/HRM/api/send_offer_otp";
			//$url = "http://localhost/ansar_otp_api/api/send_otp.php";
                        $url = "http://159.65.157.49/api/send_otp.php";
			$param = "userID=$user_id";
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
			//Log::info($response); 
			$response = json_decode($response, true);
			//echo $response['status']; exit;
			
			if($response['status'] == 'success'){
				
				return response(collect(['status' => 'success', 'message' => $response['message']])->toJson(), 200, ['Content-Type' => 'application/json']);

			}elseif($response['status'] == 'error'){
				
				return response(collect(['status' => 'error', 'message' => $response['message']])->toJson(), 200, ['Content-Type' => 'application/json']);

			}else{
					return response(collect(['status' => 'error', 'message' => $response['message']])->toJson(), 400, ['Content-Type' => 'application/json']);

			}
			 
		 }
		 
		 
		 function checkOfferOTPRequest(Request $request){
			  
			$user_id = intval($request->userID);
			$otp = $request->get('otp');

			  
			//$url = "http://18.176.55.184/HRM/api/check_offer_otp";
                        //$url = "http://localhost/ansar_otp_api/api/check_otp.php";
			$url = "http://159.65.157.49/api/check_otp.php";
			$param = "userID=$user_id&otp=$otp";
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
			//Log::info($response); 
			$response = json_decode($response, true);
			//echo $response['status']; exit;
			
			if($response['status'] == 'success'){
				
				return response(collect(['status' => 'success', 'message' => $response['message']])->toJson(), 200, ['Content-Type' => 'application/json']);

			}elseif($response['status'] == 'error'){
				
				return response(collect(['status' => 'error', 'message' => $response['message']])->toJson(), 200, ['Content-Type' => 'application/json']);

			}elseif($response['status'] == 'error1'){
				
				return response(collect(['status' => 'error1', 'message' => $response['message']])->toJson(), 200, ['Content-Type' => 'application/json']);

			}else{
					return response(collect(['status' => 'error', 'message' => $response['message']])->toJson(), 400, ['Content-Type' => 'application/json']);

			}
			 
		 }
		 
		 
		  function resendOfferOTPRequest(Request $request){
			  
			$user_id = intval($request->userID);
			  
			//$url = "http://18.176.55.184/HRM/api/resend_offer_otp";
                        //$url = "http://localhost/ansar_otp_api/api/resend_otp.php";
                        $url = "http://159.65.157.49/api/resend_otp.php";
			
			$param = "userID=$user_id";
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
			//Log::info($response); 
			$response = json_decode($response, true);
			//echo $response['status']; exit;
			
			if($response['status'] == 'success'){
				
				return response(collect(['status' => 'success', 'message' => $response['message']])->toJson(), 200, ['Content-Type' => 'application/json']);

			}elseif($response['status'] == 'error'){
				
				return response(collect(['status' => 'error', 'message' => $response['message']])->toJson(), 200, ['Content-Type' => 'application/json']);

			}else{
					return response(collect(['status' => 'error', 'message' => $response['message']])->toJson(), 400, ['Content-Type' => 'application/json']);

			}
		 
		  }
	
	
	
	
}
