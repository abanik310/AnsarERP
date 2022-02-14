@extends('template.master')
@section('content')

 <script>
        GlobalApp.controller('UserController', function ($scope, $timeout, $http, notificationService) {
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
			$scope.showNext = true;
			
			$scope.otpError = '';
			$scope.codeError = '';
			$scope.codeSuccess = '';
			$scope.otpValue = '';
			$scope.date = '';
			
			$("#date").datepicker({
                dateFormat:'dd-M-yy',
                onSelect:function (dateText) {
                    var d = new Date(); // for now

                    /* var h = d.getHours();
                    h = (h < 10) ? ("0" + h) : h ;

                    var m = d.getMinutes();
                    m = (m < 10) ? ("0" + m) : m ;

                    var s = d.getSeconds();
                    s = (s < 10) ? ("0" + s) : s ; */

                    //dateText = dateText + " " + h + ":" + m + ":" + s;
                    // alert(dateText);
                    $('#date').val(dateText);
					$scope.date = dateText;   
                }
            })
            
			
			var userId = '{{Auth::user()->id}}';
			
			$scope.processProjection = function (s) {
				$http({
                    url: '{{URL::to('ansar_projection_submit')}}',
                    //url: OTP_API_ENDPOINT+'send_offer_otp',
                    data: angular.toJson({
						userID : userId,
						"_token": "{{ csrf_token() }}",
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
         
			
			// SUBMIT BUTTON CLICK & CREATE OTP AND POPUP MODULE
			
			$scope.changeUserPassword = function (s) {
				
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
			
           
        })
    </script>
	
	 <script>
        $(document).ready(function () {
			$("#result_section").hide();
			
            $("#ansar-projection-form").ajaxForm({
                beforeSubmit: function (data) {
                    $("#ansar-projection-form .submit").slideUp(100)
                    $("#ansar-projection-form .submitting").slideDown(100)
                },
                success: function (response) {
                    $("#ansar-projection-form .submit").slideDown(100)
                    $("#ansar-projection-form .submitting").slideUp(100)
                    console.log(response)
                    if (response.validation) {
												$("#result_section").hide();

                        $("#ansar-projection-form p").css('display', 'none')
                        if (response.error.old_password != undefined) {
                            $("#ansar-projection-form p:eq(0) span").text(response.error.old_password)
                            $("#ansar-projection-form p:eq(0)").css('display', 'block')
                        }
                        if (response.error.password != undefined) {
                            $("#ansar-projection-form p:eq(1) span").text(response.error.password)
                            $("#ansar-projection-form p:eq(1)").css('display', 'block')
                        }
                        if (response.error.c_password != undefined) {
                            $("#ansar-projection-form p:eq(2) span").text(response.error.c_password)
                            $("#ansar-projection-form p:eq(2)").css('display', 'block')
                        }
                    }
                    else if (response.success) {
                        /* $('body').notifyDialog({
                            type: 'success',
                            message: 'User password changed successfully'
                        }).showDialog() */
						$("#result_section").show();
                        $("p#number_of_ansar").text(response.count)
                    }
                    else {
												$("#result_section").hide();

                        $('body').notifyDialog({
                            type: 'error',
                            message: 'An error occur. Please try again later'
                        }).showDialog()
                        $("#ansar-projection-form p").css('display', 'none');
						
                    }
                },
                error: function (response, statusText) {
						$("#result_section").hide();
                    $("#ansar-projection-form .submit").slideDown(100)
                    $("#ansar-projection-form .submitting").slideUp(100)
                    $('body').notifyDialog({
                        type: 'error',
                        message: 'An server error occur. ERROR CODE:' + statusText
                    }).showDialog()
                }

            })
            
            
        })
    </script>
  
			
           
    <div ng-controller="UserController">
        <section class="content">
            <div class="box box-solid">
                                
                            <div class="row">
                                <div class="col-sm-4 col-sm-offset-1" style="margin-top: 20px; margin-bottom: 20px">
                                    <h4 style="border-bottom: 1px solid #ababab">Ansar Joining Projection From Rest</h4>

                                    <form id="ansar-projection-form" 
                                          action="{{URL::to('/ansar_projection_submit')}}" method="post">

                                         <div class="form-group">
											<label for="date" class="control-label">Offer Date</label>
											<input type="text" name="offer_date" id="date" class="form-control" placeholder="Offer Date" ng-model="date">
										
                                        </div>
                                        <div class="form-group">
											<label for="day_number" class="control-label">Number of days</label>
											<div class="input-group">
												<input type="number" name="day_number" class="form-control" placeholder="Enter number of days" ng-model="dayNumber">
											
											</div>
											
									
                                        </div>
                                        
                                        <div>
                                            <button type="submit" ng-show="otpPass" class="btn btn-primary">
                                                <div class="submit">
                                                    Change
                                                </div>
                                                <div class="submitting">
                                                    <i class="fa fa-spinner fa-spin"></i><span class="blink-animation">Changing...</span>
                                                </div>
                                            </button>
                                            <div class="clearfix"></div>
                                        </div>
                                    </form>
									<!-- <div>
										   <div class="form-group text-right">
											  <button class="button btn-success" ng-show="showNext" ng-click="changeUserPassword()">Next</button>
										   </div>
							        </div> -->
                                </div>
								<div class="col-sm-4 col-sm-offset-1"
                                   style="min-height: 550px;border-left: 1px solid #CCCCCC; font-weight:bold;">
								   <div id="result_section" style="margin-top: 100px;">
									   <p class="text-center">Number of Anser will be eligible</p>
									   <p class="text-center text-success" id="number_of_ansar" style="font-weight:bold; font-size:40px; color: green;"></p>
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
            
			$("input[name='offer_date']").val(moment().format("DD-MMM-YYYY")); 
        })
    </script>

@stop