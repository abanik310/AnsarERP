@extends('template.master')
@section('title','Direct Offer')
{{--@section('small_title','DG')--}}
@section('breadcrumb')
    {!! Breadcrumbs::render('direct_offer') !!}
    @endsection
@section('content')
    <script>
        GlobalApp.controller('DirectOfferController', function ($scope,$http, $timeout, notificationService) {
            $scope.districts = [];
            $scope.ansarId = "";
            $scope.selectedDistrict = "";
            $scope.loadingDistrict = true;
            $scope.loadingAnsar = false;
            $scope.ansarDetail = {}
            $scope.submitResult = {};
            $scope.ansar_ids = [];
            $scope.totalLength =  0;
            $scope.exist = false;
			$scope.offer_error = ""
			$scope.errors = '';
           
            $scope.loadingSubmit = false;
			$scope.otpError = '';
			$scope.codeError = '';
			$scope.codeSuccess = '';
			$scope.otpValue = '';
			$scope.date = moment().format("DD-MMM-YYYY HH:mm:ss"); 

			var userId = '{{Auth::user()->id}}';
			var OTP_API_ENDPOINT = 'http://18.176.55.184/HRM/api/';
			
			$("#date").datepicker({
                dateFormat:'dd-M-yy',
                onSelect:function (dateText) {
                    var d = new Date(); // for now

                    var h = d.getHours();
                    h = (h < 10) ? ("0" + h) : h ;

                    var m = d.getMinutes();
                    m = (m < 10) ? ("0" + m) : m ;

                    var s = d.getSeconds();
                    s = (s < 10) ? ("0" + s) : s ;

                    dateText = dateText + " " + h + ":" + m + ":" + s;
                    // alert(dateText);
                    $('#date').val(dateText);
					$scope.date = dateText;
                }
            })
            
			
			$scope.$watch("date", function (n, o) {
                if (!n) $scope.date = '';
            });
			
            $http({
                method:'get',
                url:'{{URL::to('HRM/DistrictName')}}'
            }).then(function (response) {
                $scope.districts = response.data;
                $scope.loadingDistrict = false;
            })
            $scope.loadAnsarDetail = function (id) {
                $scope.loadingAnsar = true;
                $http({
                    method:'get',
                    url:'{{URL::route('ansar_detail_info')}}',
                    params:{ansar_id:id}
                }).then(function (response) {
                    $scope.ansarDetail = response.data;
                    $scope.loadingAnsar = false;
                    $scope.totalLength--;
                }, function(error){
                    $scope.ansarDetail = null;
                    $scope.loadingAnsar = false;
                });
            };
            $scope.makeQueue = function (id) {
                $scope.ansar_ids.push(id);
                $scope.totalLength +=  1;
            }
            $scope.checkFile = function(url){
                $http({
                    url:'{{URL::to('HRM/check_file')}}',
                    params:{path:url},
                    method:'get'
                }).then(function (response) {
                    $scope.exist = response.data.status;
                }, function () {
                    $scope.exist = false;
                })
            }
            $scope.$watch('totalLength', function (n,o) {
                if(!$scope.loadingAnsar&&n>0){
                    $scope.loadAnsarDetail($scope.ansar_ids.shift())
                }
                else{
                    if(!$scope.ansarId)$scope.ansarDetail={}
                }
            })
            $scope.sendOffer = function (s) {
				
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
				$scope.otpValue = '';
				  
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
							
							//alert('success');
							$scope.otpError = '';
							$("#withdraw-modal").modal('hide');
							
							$scope.error=false;
							$scope.loadingSubmit = true;
							$http({
								method:'post',
								url:'{{URL::to('HRM/direct_offer')}}',
								data:{
									ansar_id:$scope.ansarId,
									unit_id: $scope.selectedDistrict,
									//type:s,
									offer_date:$scope.date
								}
							}).then(function (response) {
								console.log(response.data)
								$scope.error=false;
								$scope.submitResult  = response.data;
								$scope.loadingSubmit = false;
								$scope.ansarId = "";
								$scope.selectedDistrict = "";
								$scope.ansarDetail = {}
								//alert(response.data.message);
								 if(response.data.status == 'error'){
									$scope.error = true;
								}else if(!response.data.status){
									$scope.error = true;
									$scope.offer_error = response.data.message;
								
									
								}else{
									$scope.error = false;
									$scope.offer_error = '';
								}
							},function (response) {
								console.log(response)
								console.log(response.status)
								$scope.loadingSubmit = false;
								//alert('test');
								if(response.status==500){
									$scope.error = true;
								}else if(response.data.status == 'error'){
									$scope.error = true;
								}else if(!response.status){
									$scope.error = true;
									$scope.offer_error = response.message
									alert(response.message);
								}else{
									$scope.error = false;
									$scope.offer_error = '';
								}
								
								$scope.submitResult  = response.data
							})  
						}else{
							//alert('failed!');
							$scope.otpError = response.data.message;
						}
						
                      
                    }) .catch(function (err) {
						
							 console.log(err);
							 throw err;
					});
			};	
			
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
						
						//console.log(response);                        
						
						if(response.data.status == 'success'){
							
							//$("#withdraw-modal").modal('show');	
                            $scope.codeSuccess = 'OTP has been sent to your mobile number.'; 							
							
						}else{
							$scope.codeError = response.data.message;
						}						
                      
				}).catch(function (err) {
					
						 console.log(err);
						 throw err;
				});
				
				
			}
			
			
            $scope.cancelOffer = function (e) {
                e.preventDefault()
                $scope.error=false;
                $scope.offerCanceling = true;
                $http({
                    method:'post',
                    url:'{{URL::to('HRM/direct_offer_cancel')}}',
                    data:{ansar_id:$scope.ansarId}
                }).then(function (response) {
                    $scope.error=false;
                    $scope.submitResult  = response.data;
                    if(response.data.status){
                        notificationService.notify('success',response.data.message)
                        $scope.ansarId = "";
                        $scope.ansarDetail = {}
                    }
                    else{
                        notificationService.notify('error',response.data.message)
                    }
                    $scope.offerCanceling = false;
                },function (response) {
                    $scope.offerCanceling = false;
                    if(response.status==500)$scope.error = true;
                    $scope.submitResult  = response.data
                })
            }
            $scope.ppp = function(){
//                alert(moment().format("DD-MMM-YYYY"))
                $("input[name='offer_date']").val(moment().format("DD-MMM-YYYY HH:mm:ss"));
            }
        })
    </script>
    <div ng-controller="DirectOfferController">
        <section class="content">
            <div class="box box-solid">
                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-4">
                            <form>
                                 <p class="text text-danger">
                                        [[offer_error ]]
                                    </p>
								<div class="form-group">
                                    <label for="ansar_id" class="control-label">Ansar ID to send Offer</label>
                                    <div class="input-group">
                                        <input type="text" name="ansar_id" class="form-control" placeholder="Enter Ansar ID" ng-model="ansarId">
                                        <span class="input-group-btn">
                                            <button class="btn btn-secondary" ng-click="loadAnsarDetail(ansarId)" type="button">
                                                <i class="fa fa-eye"></i>
                                            </button>
                                        </span>
                                    </div>
                                    <p class="text text-danger" ng-if="errors.ansar_id!=undefined">
                                        [[errors.ansar_id[0] ]]
                                    </p>
									
                                </div>
								
                                <div class="form-group">
                                    <label for="district" class="control-label">Select District to send Offer&nbsp;
                                        <img ng-show="loadingDistrict" src="{{asset('dist/img/facebook.gif')}}" width="16"></label>
                                    <select class="form-control" name="unit_id" ng-model="selectedDistrict" ng-disabled="loadingDistrict">
                                        <option value="">--@lang('title.unit')--</option>
                                        <option ng-repeat="d in districts"  value="[[d.id]]">[[d.unit_name_bng]]</option>
                                    </select>
                                    <p class="text text-danger" ng-if="errors.unit_id!=undefined">
                                        [[errors.unit_id[0] ]]
                                    </p>
                                </div>
								
                                <div class="form-group">
                                    <label for="date" class="control-label">Offer Date</label>
                                    <input type="text" name="offer_date" id="date" class="form-control" placeholder="Offer Date" ng-model="date">
                                    <p class="text text-danger" ng-if="errors.offer_date!=undefined">
                                        [[errors.offer_date[0] ]]
                                    </p>
                                </div>
                                <button class="btn btn-primary" confirm callback="sendOffer()" ng-disabled="loadingSubmit" type="submit">
                                    <i ng-show="loadingSubmit" class="fa fa-spinner fa-pulse"></i>
                                    <i ng-hide="loadingSubmit" class="fa fa-send"></i>
                                    Send Offer</button>
									
									
                                <a class="btn btn-danger" ng-disabled="offerCanceling" href="#" ng-click="cancelOffer($event)">
                                    <i ng-show="offerCanceling" class="fa fa-spinner fa-pulse"></i>
                                    <i ng-hide="offerCanceling" class="fa fa-times"></i>&nbsp;Cancel Offer
                                </a>

                            </form>
                            </div>
                        <div class="col-sm-8"
                             style="min-height: 400px;border-left: 1px solid #CCCCCC">
                            <div id="loading-box" ng-if="loadingAnsar">
                            </div>
                            <template-list data="ansarDetail" key="ansar_history"></template-list>
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
    <script>
        $(document).ready(function () {
            
			$("input[name='offer_date']").val(moment().format("DD-MMM-YYYY HH:mm:ss")); 
        })
    </script>
@stop