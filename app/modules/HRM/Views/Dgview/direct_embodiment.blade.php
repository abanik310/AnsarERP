@extends('template.master')
@section('title','Direct Embodiment')
@section('breadcrumb')
    {!! Breadcrumbs::render('direct_embodiment') !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller('DirectEmbodimentController', function ($scope, $timeout, $http, notificationService) {
            $scope.ansarId = "";
            $scope.r_date = "";
            $scope.j_date = "";
            $scope.ansarDetail = {};
            $scope.reset = {thana: false, kpi: false};
            $scope.loadingAnsar = false;
            $scope.loadingSubmit = false;
            $scope.submitResult = {};
            $scope.ansar_ids = [];
            $scope.totalLength = $scope.ansar_ids.length;
            $scope.memorandumId = '';
            $scope.isVerified = false;
            $scope.isVerifying = false;
            $scope.exist = false;
			$scope.errors = '';
           
            $scope.otpPass = true;
			$scope.showNext = false;
			
			$scope.otpError = '';
			$scope.codeError = '';
			$scope.codeSuccess = '';
			$scope.otpValue = '';
			
			var userId = '{{Auth::user()->id}}';
			
			
            $scope.loadAnsarDetail = function (id) {
                $scope.loadingAnsar = true;
                $http({
                    method: 'get',
                    url: '{{URL::route('direct_embodiment_ansar_details')}}',
                    params: {ansar_id: id}
                }).then(function (response) {
                    $scope.ansarDetail = response.data;
                    $scope.loadingAnsar = false;
                    $scope.totalLength--;
                })
            };
			
			// SUBMIT BUTTON CLICK & CREATE OTP AND POPUP MODULE
			
			$scope.directEmbodiment = function (s) {
				
				//alert($scope.date);
				$scope.showLoadingScreen = true;
                $scope.otpError = '';
				$scope.codeError = '';
				$scope.codeSuccess = '';
				
				$http({
                    url: '{{URL::to('HRM/send_offer_otp_request')}}',
                    //url: OTP_API_ENDPOINT+'send_offer_otp',
                    data: angular.toJson({
						userID : userId
                    }),
                    method: 'post'
                }).then(
                    function (response) {
						
						console.log(response);                        
						
						if(response.data.status == 'success'){
							
							$("#withdraw-modal").modal('show');			
							
						}else{
							if(response.data.status == 'error1'){
								alert(response.data.message);
							}else{
								alert('OTP not sent!');

							}
							return;
						}						
                      
				}).catch(function (err) {
					
						 console.log(err);
						 throw err;
				});	
            }
			
			$scope.checkOTP = function(params) {
				$scope.errors = ''
				$offer_error = '';
				$scope.codeError = '';
				$scope.codeSuccess = '';
				$scope.otpError = '';
				  
				$http({
					url: '{{URL::to('HRM/check_offer_otp_request')}}',
                    //url: OTP_API_ENDPOINT+'check_offer_otp',
                    data: angular.toJson({
						userID : userId,
						otp: params.otpValue
                    }),
                    method: 'post' 
                }).then(
                    function (response) {
						
						//console.log(response);                        
						
						if(response.data.status == 'success'){
							 $scope.otpPass = true;
			                 $scope.showNext = false;
							 $("#withdraw-modal").modal('hide');		
							
						}else{
							//alert('failed!');
							$scope.otpError = response.data.message;
							$scope.otpPass = false;
			                $scope.showNext = true;
							$("#withdraw-modal").modal('show');		
						}
						
                      
                    }) .catch(function (err) {
						
							 console.log(err);
							 throw err;
					});
			};
			
			
			
			// RESEND OTP REQUEST PROCESS
			
			$scope.resendCode = function () {
				
				//$scope.codeSuccess = 'OTP has been sent to your mobile number.';
				$scope.buttonDisabled = true;

				$timeout(function() {
				   $scope.buttonDisabled = false;
				}, 30000);
				
				$scope.codeError = '';
				$scope.codeSuccess = '';
				$scope.otpError = '';
				$http({
					url: '{{URL::to('HRM/resend_offer_otp_request')}}',
                    //url: OTP_API_ENDPOINT+'resend_offer_otp',
                    data: angular.toJson({	
                         userID : userId					
                    }),
                    method: 'post'
                }).then(
                    function (response) {
						
						if(response.data.status == 'success'){							
                            $scope.codeSuccess = 'OTP has been sent to your mobile number.';					
						}else{
							$scope.codeError = response.data.message;
						}						
                      
				}).catch(function (err) {
					
						 console.log(err);
						 throw err;
				});
				
				
			}
			
			
			
			
            $scope.makeQueue = function (id) {
                $scope.ansar_ids.push(id);
                $scope.totalLength += 1;
            };
			
            $scope.$watch('totalLength', function (n, o) {
                if (!$scope.loadingAnsar && n > 0) {
                    $scope.loadAnsarDetail($scope.ansar_ids.shift())
                } else {
                    if (!$scope.ansarId) $scope.ansarDetail = {}
                }
            });
			
            $scope.reset = function () {
                $scope.reset = {};
                $scope.reset = {thana: true, kpi: true};
                $scope.ansarDetail = '';
            }
        })
    </script>
    <div ng-controller="DirectEmbodimentController">
        <section class="content">
            <notify></notify>
            <div class="box box-solid">
                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-4">
                           
						   <form action="{{URL::to('HRM/direct_embodiment_submit')}}" method="post" form-submit
                                  loading="loadingSubmit" on-reset="reset()" errors="errors">
								  
                                <div class="form-group">
                                    <label for="ansar_id" class="control-label">Ansar ID</label>
                                    <input type="text" name="ansar_id" id="ansar_id" class="form-control"
                                           placeholder="Enter Ansar ID" ng-model="ansarId"
                                           ng-change="makeQueue(ansarId)">
                                    <p class="text text-danger" ng-if="errors.ansar_id!=undefined&&errors.ansar_id[0]">
                                        [[errors.ansar_id[0] ]]</p>
                                </div>
                                <div class="form-group">
                                    <label for="mem_id" class="control-label">Memorandum no.&nbsp;<i
                                                class="fa fa-spinner fa-pulse" ng-show="isVerifying"></i>
                                        <span class="text-danger" ng-if="isVerified">This id already taken</span>
                                    </label>
                                    <input type="text" name="mem_id" id="mem_id" class="form-control"
                                           placeholder="Enter Memorandum no." ng-model="memorandumId">
                                    <p class="text text-danger" ng-if="errors.mem_id!=undefined&&errors.mem_id[0]">
                                        [[errors.mem_id[0] ]]</p>
                                </div>
                                <div class="form-group">
                                    <datepicker-separate-fields label="Reporting Date:" notify="reportingInvalidDate"
                                                                rdata="r_date"></datepicker-separate-fields>
                                    <input type="hidden" name="reporting_date" ng-value="r_date">
                                    <p class="text text-danger"
                                       ng-if="errors.reporting_date!=undefined&&errors.reporting_date[0]">
                                        [[errors.reporting_date[0] ]]</p>
                                </div>
                                <div class="form-group">
                                    <datepicker-separate-fields label="Embodiment Date:" notify="embodimentInvalidDate"
                                                                rdata="j_date"></datepicker-separate-fields>
                                    <input type="hidden" name="joining_date" ng-value="j_date">
                                    <p class="text text-danger"
                                       ng-if="errors.joining_date!=undefined&&errors.joining_date[0]">
                                        [[errors.joining_date[0] ]]</p>
                                </div>
                                <filter-template
                                        show-item="['unit','thana','kpi']"
                                        type="single"
                                        data="param"
                                        start-load="unit"
                                        layout-vertical="1"
                                        reset="reset"
                                        field-name="{unit:'unit',thana:'thana',kpi:'kpi_id'}"
                                        error-key="{unit:'unit',thana:'thana',kpi:'kpi_id'}"
                                        error-message="{unit:errors.unit[0],thana:errors.thana[0],kpi_id:errors.kpi_id[0]}"
                                >

                                </filter-template>
                                <button class="btn btn-primary"  ng-show="otpPass" ng-disabled="loadingSubmit || embodimentInvalidDate || reportingInvalidDate"><i
                                            class="fa fa-spinner fa-pulse" ng-show="loadingSubmit"></i>Embodied Ansar
                                </button>
                            </form>
							<!--<div>
							   <div class="form-group text-right">
									  <button class="button btn-success" ng-show="showNext" ng-click="directEmbodiment()">Next</button>
			                       </div>
							</div> -->
							
                        </div>
                        <div class="col-sm-6 col-sm-offset-2"
                             style="min-height: 550px;border-left: 1px solid #CCCCCC">
                            <div id="loading-box" ng-if="loadingAnsar">
                            </div>
                            <div ng-if="ansarDetail.ansar_details.ansar_name_eng==undefined">
                                <h3 style="text-align: center">No Ansar Found</h3>
                            </div>
                            <div ng-if="ansarDetail.ansar_details.ansar_name_eng!=undefined">
                                <div class="form-group">
                                    <label class="control-label">Name</label>
                                    <p>[[ansarDetail.ansar_details.ansar_name_eng]]</p>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Rank</label>
                                    <p>[[ansarDetail.ansar_details.name_eng]]</p>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Home District</label>
                                    <p>[[ansarDetail.ansar_details.unit_name_eng]]</p>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Gender</label>
                                    <p>[[ansarDetail.ansar_details.sex]]</p>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Date of Birth</label>
                                    <p>[[ansarDetail.ansar_details.data_of_birth|dateformat:'DD-MMM-YYYY']]</p>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Current Status</label>
                                    <p>[[ansarDetail.status]]</p>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Last Disembodied Date</label>
                                    <p>
                                        [[ansarDetail.ansar_details.release_date?(ansarDetail.ansar_details.release_date|dateformat:'DD-MMM-YYYY'):'--']]</p>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Disembodied Reason</label>
                                    <p>
                                        [[ansarDetail.ansar_details.reason_in_bng?ansarDetail.ansar_details.reason_in_bng:'--']]</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
		
		 <div class="modal modal-default fade" role="dialog" id="withdraw-modal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title">OTP</h4>

                    </div>
                    <div class="modal-body">
                        <div style="width: 100%;height: 200px;" >
                            <div style="margin: auto;text-align:center;position: relative;top:50%;transform: translateY(-50%)">
                                  <form ng-submit="checkOTP(params)">
 
								   <div class="form-group">
										<label for="mem_id" class="control-label"></label>
										<input type="text" name="otp" id="otp" class="form-control"
											   placeholder="Enter OTP" ng-change="myFunc()" ng-model="params.otpValue">
										<p class="text text-danger">[[otpError]]</p>
                                   </div>
								   <div class="form-group text-right">
									  <button class="button btn-success" >Submit</button>
			                       </div>
								   <div class="form-group text-left"> 
										  <button ng-click="resendCode()" type="button" ng-disabled="buttonDisabled">Resend Code</button>
										  <p class="text text-danger">[[codeError]]</p>
										  <p class="text text-success">[[codeSuccess]]</p>
								   </div>
								 </form>   
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop