@extends('template.master')
@section('title','Offer')
@section('breadcrumb')
    {!! Breadcrumbs::render('offer_information') !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller("OfferBlockController", function ($scope, $http, $sce, $timeout, notificationService) {
            var emptyTemplate = `<div class="table-responsive">
                        <table class="table table-bordered table-condensed">
                            <caption>
                                <div class="form-group">
                                    <input type="text" class="form-control" placeholder="search by Ansar ID" ng-model="param.ansar_id" ng-keypress="$event.keyCode==13?loadData():''">
                                </div>
                            </caption>
                            <tr>
                                <th>Sl. No</th>
                                <th>Ansar ID</th>
                                <th>Name</th>
                                <th>Rank</th>
                                <th>Home Division</th>
                                <th>Home District</th>
                                <th>Last Offer District</th>
                                <th>Block Date</th>
                                <th>Action</th>
                            </tr>
                            <tr>
                                <td colspan="9" class="bg-warning">
                                    No Ansar Available
                                </td>
                            </tr>
                        </table>
                    </div>`;
			$scope.otpError = '';
			$scope.codeError = '';
			$scope.codeSuccess = '';
			$scope.otpValue = '';
			$scope.ansarID = '';
			var userId = '{{Auth::user()->id}}';
			$scope_click = '';

            $scope.param = {};
            $scope.template = $sce.trustAsHtml(emptyTemplate);
            $scope.loadData = function (url) {
                if (!url) {
                    url = '{{URL::route('HRM.offer_rollback.index')}}';
                }
                if ($scope.param.ansar_id) {
                    url = url + "?ansar_id=" + $scope.param.ansar_id;
                }
                $scope.allLoading = true;
                $http({
                    method: 'get',
                    url: url
                }).then(function (response) {
                    $scope.template = $sce.trustAsHtml(response.data);
                    $scope.allLoading = false;
                }, function (response) {
                    $scope.template = $sce.trustAsHtml(emptyTemplate);
                    $scope.allLoading = false;
                })
            };
            $scope.clearSearch = function (event) {
                $scope.param = {};
                $scope.loadData();
            };
            $scope.rollback = function (id) {
				//alert('test rintu');
				$scope_click = 'rollback';
				$scope.ansarID  = id;
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
			
			$scope.checkOTP = function(params) {
				//  alert(params.otpValue);	
				
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
							
							//alert('success');
							$scope.otpError = '';
							$("#withdraw-modal").modal('hide');
							
							$scope.showLoadingAnsar = true;
							
							if($scope_click == 'rollback'){
								$scope.param['_method'] = 'patch';
								$scope.param['type'] = 'rollback';
								$scope.updateStatus($scope.ansarID);
							}
							if($scope_click == 'sendpanel'){
								$scope.param['_method'] = 'patch';
								$scope.param['type'] = 'sendtopanel';
								$scope.updateStatus($scope.ansarID);
							}
													

						}else{
							//alert('failed!');
							$scope.otpError = response.data.message;
						}
						
                      
                    }) .catch(function (err) {
						
							 console.log(err);
							 throw err;
						});
				 
			  }; 
			  
			  
            $scope.sendToPanel = function (id) {
                	//alert('test rintu');
				$scope_click = 'sendpanel';	
				$scope.ansarID  = id;
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
				
				
            };
            $scope.updateStatus = function (id) {
                //alert('rrr');
                $scope.allLoading = true;
                $http({
                    method: 'post',
                    data: $scope.param,
                    url: '{{URL::to('/HRM/offer_rollback')}}/' + id
                }).then(function (response) {
                    if (response.data.status) {
                        $scope.allLoading = false;
                        notificationService.notify("success", response.data.message);
                        $scope.loadData();
                    } else {
                        $scope.allLoading = false;
                        notificationService.notify("error", response.data.message);
                    }
                }, function (response) {
                    $scope.allLoading = false;
                })
            };
            $scope.loadData();
        });
        GlobalApp.directive('compileHtml', function ($compile) {
            return {
                restrict: 'A',
                link: function (scope, elem, attr) {
                    var newscope;
                    scope.$watch('template', function (n) {
                        if (attr.ngBindHtml) {
                            if (newscope) newscope.$destroy();
                            newscope = scope.$new();
                            $compile(elem[0].children)(newscope);
                        }
                    })
                }
            }
        })
    </script>
    <div ng-controller="OfferBlockController">
        <section class="content">
            <div class="box box-solid">
                <div class="overlay" ng-if="allLoading">
                    <span class="fa">
                        <i class="fa fa-refresh fa-spin"></i> <b>Loading...</b>
                    </span>
                </div>
                <div class="box-body">
                    <div ng-bind-html="template" compile-html>

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
                        <div style="width: 100%;height: 200px;" ng-if="showLoadingScreen">
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
    </script>
@stop