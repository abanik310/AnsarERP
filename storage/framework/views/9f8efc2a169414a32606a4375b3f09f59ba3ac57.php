<?php $__env->startSection('title','Offer'); ?>
<?php $__env->startSection('breadcrumb'); ?>
<?php echo Breadcrumbs::render('offer_information'); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<style>
    #offer-view * {
        font-size: 20px !important;
    }

    ul.nav-tabs li.active a {
        background: #01655d !important;
        color: white !important;
    }
</style>
<script>
    GlobalApp.controller('OfferController', function ($scope, $http, $interval, $timeout, notificationService) {

    $scope.kpiPCMale = '';
    $scope.kpiPCFemale = '';
    $scope.kpiAPCMale = '';
    $scope.kpiAPCFemale = '';
    $scope.kpiAnsarMale = '';
    $scope.kpiAnsarFemale = '';
    $scope.alerts = [];
    $scope.noAnsar = true;
    $scope.offerAnsarId = [];
    $scope.showLoadScreen = true;
    $scope.showLoadScreen1 = true;
    $scope.showLoadingAnsar = false;
    $scope.modalStyle = {};
    $scope.selectedDistrict = [];
    $scope.updatedDistrict = [];
    $scope.removedDistrict = [];
    $scope.allDistrict = [];
    $scope.quotaLoading = true;
    $scope.data = {offeredDistrict: ""};
    $scope.selectedAnsar = [];
    $scope.result = {};
    $scope.countDown = 10;
    $scope.AvailableCount = '';
    //$scope.buttonDisabled = true;


    $scope.buttonText = "Send Offer";
    $scope.offerQuota = 0;
    $scope.negateDistrictId = null;
    $scope.otpError = '';
    $scope.codeError = '';
    $scope.codeSuccess = '';
    $scope.otpValue = '';
    var promis;
    $scope.districtId = '<?php echo e(Auth::user()->district_id); ?>';
    var userType = '<?php echo e(Auth::user()->type); ?>';
    var userId = '<?php echo e(Auth::user()->id); ?>';
    var OTP_API_ENDPOINT = 'http://18.176.55.184/HRM/api/';
    if (parseInt(userType) == 11 || parseInt(userType) == 33 || parseInt(userType) == 77) {
    $scope.isAdmin = true;
    $http({
    url: '<?php echo e(URL::to('HRM/DistrictName')); ?>',
            type: 'get'
    }).then(function (response) {
    $scope.allDistrict = response.data;
    })
    } else {
    $scope.isAdmin = false;
    $scope.negateDistrictId = $scope.districtId;
    }
    $scope.removeDistrict = function () {
    for (var i = 0; i < $scope.removedDistrict.length; i++) {
    $scope.allDistrict.push($scope.updatedDistrict[$scope.removedDistrict[i] - i]);
    $scope.updatedDistrict.splice($scope.removedDistrict[i] - i, 1)
    }
    $scope.removedDistrict = [];
    };
    
    
    $scope.loadAnsar = function () {
        
    var total = parseInt($scope.kpiPCMale) + parseInt($scope.kpiPCFemale) + parseInt($scope.kpiAPCMale) + parseInt($scope.kpiAPCFemale) + parseInt($scope.kpiAnsarMale) + parseInt($scope.kpiAnsarFemale);
    console.log($scope.kpiAnsarMale); 
        
    if (total > $scope.offerQuota) {
        alert("Your offer limit exit total number of offer you want to send");
        return;
    }
   // $scope.buttonText = "Loading Ansar";
   // $scope.showLoadScreen = false;
   $scope.showLoadScreen1 = false;
    var data = {
    pc_male: $scope.kpiPCMale || 0,
            pc_female: $scope.kpiPCFemale || 0,
            apc_male: $scope.kpiAPCMale || 0,
            apc_female: $scope.kpiAPCFemale || 0,
            ansar_male: $scope.kpiAnsarMale || 0,
            ansar_female: $scope.kpiAnsarFemale || 0,
            district: $scope.selectedDistrict.filter(function (v) {
            return v != undefined;
            }),
            exclude_district: (parseInt(userType) == 11 ? null : $scope.districtId)
    };
    //$scope.showLoadingAnsar = true;
    //$scope.modalStyle = {'display': 'block'};
    $http({
    url: '<?php echo e(URL::to('HRM/kpi_list')); ?>',
            method: 'post',
            data: angular.toJson(data)
    }).then(function (response) {
        console.log(response);
        $scope.AvailableCount = response.data;
        //$scope.showLoadScreen = true;
        $scope.showLoadScreen1 = true;

   
    })
    };
    $scope.sendOffer = function () {
    //$("#withdraw-modal").modal('show');
    $scope.showLoadingScreen = true;
    $scope.otpError = '';
    $scope.codeError = '';
    $scope.codeSuccess = '';
    $http({
    url: '<?php echo e(URL::to('HRM/send_offer_otp_request')); ?>',
            //url: OTP_API_ENDPOINT+'send_offer_otp',
            data: angular.toJson({
            userID : userId
            }),
            method: 'post'
    }).then(
            function (response) {

            console.log(response);
            if (response.data.status == 'success'){

            $("#withdraw-modal").modal('show');
            } else{
            if (response.data.status == 'error1'){
            alert(response.data.message);
            } else{
            alert('OTP not sent!');
            }
            return;
            }

            }).catch(function (err) {

    console.log(err);
    throw err;
    });
    };
    /* $scope.submitOTP = function () {
     alert('rintu test');
     
     alert($scope.otpValue);
     
     $http({
     url: '<?php echo e(URL::to('HRM/check_offer_otp')); ?>',
     data: angular.toJson({
     
     }),
     method: 'post'
     }).then(
     function (response) {
     
     console.log(response);                        
     
     if(response.data.status == 'success'){
     
     alert('success');
     }else{
     alert('OTP not sent!');
     return;
     }
     
     
     });
     $scope.otpError = 'OTP has been sent to your mobile number.';
     }			 */
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
    url: '<?php echo e(URL::to('HRM/resend_offer_otp_request')); ?>',
            //url: OTP_API_ENDPOINT+'resend_offer_otp',
            data: angular.toJson({
            userID : userId
            }),
            method: 'post'
    }).then(
            function (response) {

            //console.log(response);                        

            if (response.data.status == 'success'){

            //$("#withdraw-modal").modal('show');	
            $scope.codeSuccess = 'OTP has been sent to your mobile number.';
            } else{
            $scope.codeError = response.data.message;
            }

            }).catch(function (err) {

    console.log(err);
    throw err;
    });
    }
    
$scope.verifyMemorandumId = function () {
                var data = {
                    memorandum_id: $scope.memorandumId
                };
                
                console.log($scope.memorandumId);
                
               
                //$scope.isVerified = false;
                $scope.isVerifying = true;
                $http.post('<?php echo e(action('UserController@verifyMemorandumId')); ?>', data).then(function (response) {
                    $scope.isVerified = response.data.status;
                    console.log(response.data.status);
                    $scope.isVerifying = false;
                }, function (response) {
                    $scope.isVerified = response.data.status;
                })
            };

    $scope.checkOTP = function(params) {
    //  alert(params.otpValue);	

    $scope.codeError = '';
    $scope.codeSuccess = '';
    $scope.otpError = '';
    $http({
    url: '<?php echo e(URL::to('HRM/check_offer_otp_request')); ?>',
            //url: OTP_API_ENDPOINT+'check_offer_otp',
            data: angular.toJson({
            userID : userId,
                    otp: params.otpValue
            }),
            method: 'post'
    }).then(
            function (response) {

            //console.log(response);                        

            if (response.data.status == 'success'){

            //alert('success');
            $scope.otpError = '';
            $("#withdraw-modal").modal('hide');
            $scope.showLoadingAnsar = true;
            $scope.buttonText = "Sending Offer...";
            $http({
            url: '<?php echo e(URL::to('HRM/send_offer')); ?>',
                    data: angular.toJson({
                    pc_male: $scope.kpiPCMale || 0,
                            pc_female: $scope.kpiPCFemale || 0,
                            apc_male: $scope.kpiAPCMale || 0,
                            apc_female: $scope.kpiAPCFemale || 0,
                            ansar_male: $scope.kpiAnsarMale || 0,
                            ansar_female: $scope.kpiAnsarFemale || 0,
                            district: $scope.selectedDistrict.filter(function (v) {
                            return v != undefined;
                            }),
                            exclude_district: (parseInt(userType) == 11 ? null : $scope.districtId),
                            district_id: $scope.isAdmin ? $scope.data.offeredDistrict : $scope.districtId,
                            type: 'panel',
                            offer_limit: $scope.offerQuota,
                            memorandum_id: $scope.memorandumId
                    }),
                    method: 'post'
            }).then(
                    function (response) {
                    $scope.showLoadScreen = true;
                    $scope.alerts = [];
                    $scope.alerts.push(response.data);
                    $scope.buttonText = "Send Offer";
                    notificationService.notify(response.data.type, response.data.message);
                    $scope.kpiPCMaleStatus = false;
                    $scope.kpiPCFemaleStatus = false;
                    $scope.kpiAPCMaleStatus = false;
                    $scope.kpiAPCFemaleStatus = false;
                    $scope.kpiAnsarMaleStatus = false;
                    $scope.kpiAnsarFemaleStatus = false;
                    $scope.getOfferCount();
                    },
                    function (response) {
                    $scope.alerts = [];
                    notificationService.notify(response.data.type, response.data.message);
                    $scope.showLoadScreen = true;
                    $scope.buttonText = "Send Offer"
                    }
            )

            } else{
            //alert('failed!');
            $scope.otpError = response.data.message;
            }


            }).catch(function (err) {

    console.log(err);
    throw err;
    });
    };
    $scope.getOfferCount = function () {
    $scope.quotaLoading = true;
    $http({
    url: "<?php echo e(URL::to('HRM/get_offer_count')); ?>",
            method: 'get'
    }).then(function (response) {
    $scope.offerQuota = response.data.total_offer;
    $scope.quotaLoading = false;
    }, function (response) {

    })
    };
    $scope.checkDistrict = function (a, b) {
    var s = false;
    a.forEach(function (a) {
    if (a.id == b.id) {
    s = true;
    }
    });
    return s;
    };
    $scope.getOfferCount();
    $scope.getInt = function (a) {
    if (isNaN(a)) {
    return '';
    } else {
    return a;
    }
    };
    $scope.startCountDown = function () {
    promis = $interval(function () {
    $scope.countDown = $scope.countDown - 1
    }, 1000)
    };
    $scope.$watch('countDown', function (n, o) {
    if (n <= 0) {
    $interval.cancel(promis);
    window.location.assign('<?php echo e(URL::previous()); ?>')
    }
    });
    $scope.closeAlert = function () {
    $scope.alerts = [];
    };
    $scope.getMessageString1 = function () {
    var message = "Please confirm your selection<br/><span style='color: red;font-size: 20px;display: block;'>";
    if ($scope.kpiPCMale) message += "PC(Male):" + $scope.kpiPCMale + "<br>";
    if ($scope.kpiPCFemale) message += "PC(Female):" + $scope.kpiPCFemale + "<br>";
    if ($scope.kpiAPCMale) message += "APC(Male):" + $scope.kpiAPCMale + "<br>";
    if ($scope.kpiAPCFemale) message += "APC(Female):" + $scope.kpiAPCFemale + "<br>";
    if ($scope.kpiAnsarMale) message += "ANSAR(Male):" + $scope.kpiAnsarMale + "<br>";
    if ($scope.kpiAnsarFemale) message += "ANSAR(Female):" + $scope.kpiAnsarFemale + "<br>";
    return message += "</span>";
    };
    
    $scope.getMessageString = function () {
    var message = "Are you sure to send offer?<br/><span style='color: red;font-size: 20px;display: block;'>";
    if ($scope.kpiPCMale) message += "PC(Male):" + $scope.kpiPCMale + "<br>";
    if ($scope.kpiPCFemale) message += "PC(Female):" + $scope.kpiPCFemale + "<br>";
    if ($scope.kpiAPCMale) message += "APC(Male):" + $scope.kpiAPCMale + "<br>";
    if ($scope.kpiAPCFemale) message += "APC(Female):" + $scope.kpiAPCFemale + "<br>";
    if ($scope.kpiAnsarMale) message += "ANSAR(Male):" + $scope.kpiAnsarMale + "<br>";
    if ($scope.kpiAnsarFemale) message += "ANSAR(Female):" + $scope.kpiAnsarFemale + "<br>";
    return message += "</span>";
    };
    
    $scope.message1 = $scope.getMessageString1();
    $scope.message = $scope.getMessageString();
    $scope.$watch("[kpiPCMale,kpiPCFemale,kpiAPCMale,kpiAPCFemale,kpiAnsarMale,kpiAnsarFemale]", function (n, o) {
    $scope.message = $scope.getMessageString();
    $scope.message1 = $scope.getMessageString1();
    }, true)

            //checkbox watch
            $scope.$watch("kpiPCMaleStatus", function (n, o) {
            if (!n) $scope.kpiPCMale = '';
            });
    $scope.$watch("kpiPCFemaleStatus", function (n, o) {
    if (!n) $scope.kpiPCFemale = '';
    });
    $scope.$watch("kpiAPCMaleStatus", function (n, o) {
    if (!n) $scope.kpiAPCMale = '';
    });
    $scope.$watch("kpiAPCFemaleStatus", function (n, o) {
    if (!n) $scope.kpiAPCFemale = '';
    });
    $scope.$watch("kpiAnsarMaleStatus", function (n, o) {
    if (!n) $scope.kpiAnsarMale = '';
    });
    $scope.$watch("kpiAnsarFemaleStatus", function (n, o) {
    if (!n) $scope.kpiAnsarFemale = '';
    });
    })

</script>
<div ng-controller="OfferController" id="offer-view">
    <section class="content">
        <?php if($isFreeze): ?>
        <h3 style="text-align: center">You have <span class="text-warning"><?php echo e($isFreeze); ?></span> freezed ansar in
            your district.Unfreeze them then you are eligible to send offer
            <br>Redirect in ...<span class="text-danger" ng-init="startCountDown()">[[countDown]]</span> Second
        </h3>
        <?php else: ?>
        <div class="row">
            <div ng-class="{'col-md-10 col-centered':isAdmin,'col-md-8 col-centered':!isAdmin}">
                <div class="box box-solid">
                    <div class="box-body">
                        <h4 ng-if="!isAdmin" style="text-align: right;">You have total
                            <span ng-hide="quotaLoading" style="text-decoration: underline"
                                  ng-class="{'text-green':offerQuota>50,'text-danger':offerQuota<=10}">[[offerQuota]]</span>
                            <i ng-show="quotaLoading" class="fa fa-pulse fa-spinner"></i>
                            offer left
                        </h4>

                        <div class="row">
                            <div class="col-md-4" ng-if="isAdmin">
                                <h4><?php echo app('translator')->get('title.unit'); ?></h4>
                                <ul class="offer-district" style="padding-left:0;">
                                    <li ng-repeat="unit in allDistrict">
                                        <input ng-change="addDistrict()" type="checkbox" class="check-boxx"
                                               ng-model="selectedDistrict[$index]" ng-true-value="[[unit.id]]"
                                               ng-false-value="" id="id-[[unit.id]]" value="[[unit.id]]"
                                               name="units[]">
                                        <label for="id-[[unit.id]]" class="check-label"><i class="fa"
                                                                                           ng-class="{'fa-check':selectedDistrict[$index]}"></i>[[unit.unit_name_eng]]</label>
                                    </li>
                                </ul>
                            </div>
                            <div ng-class="{'col-md-8':isAdmin,'col-md-12':!isAdmin}">
                                <ul class="nav mb-3 nav-tabs" id="pills-tab" role="tablist">
                                    <li class="nav-item active">
                                        <a class="nav-link" id="pills-ansar-tab" data-toggle="pill"
                                           href="#pills-ansar" role="tab" aria-controls="pills-ansar"
                                           aria-selected="false">Ansar</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="pills-pc-tab" data-toggle="pill"
                                           href="#pills-pc" role="tab" aria-controls="pills-pc"
                                           aria-selected="true">PC</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="pills-apc-tab" data-toggle="pill"
                                           href="#pills-apc" role="tab" aria-controls="pills-apc"
                                           aria-selected="false">APC</a>
                                    </li>
                                </ul>
                                <div class="tab-content" id="pills-tabContent">
                                    <div class="tab-pane fade" id="pills-pc" role="tabpanel"
                                         aria-labelledby="pills-pc-tab" style="padding: 3% 0 0 0">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="kpiPCMaleStatus">
                                                        <input id="kpiPCMaleStatus" type="checkbox" value="true"
                                                               ng-model="kpiPCMaleStatus">&nbsp;<i
                                                               class="fa fa-male male"
                                                               style="color: black;"></i>&nbsp;Male/???????????????
                                                    </label>
                                                    <input type="number" ng-model="kpiPCMale" min="0"
                                                           placeholder="Put your number here"
                                                           max="[[offerQuota]]" class="form-control"
                                                           ng-disabled="!kpiPCMaleStatus" pattern="[0-9]+"
                                                           onkeydown="return event.keyCode !== 69 && event.keyCode !== 101"
                                                           ng-change="kpiPCMale=kpiPCMale==''?'':getInt(kpiPCMale)">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="kpiPCFemaleStatus">
                                                        <input id="kpiPCFemaleStatus" type="checkbox"
                                                               value="true"
                                                               ng-model="kpiPCFemaleStatus">&nbsp;<i
                                                               class="fa fa-female female"
                                                               style="color: black;"></i>&nbsp;Female/???????????????
                                                    </label>
                                                    <input type="number" ng-model="kpiPCFemale" min="0"
                                                           placeholder="Put your number here"
                                                           max="[[offerQuota]]" class="form-control"
                                                           ng-disabled="!kpiPCFemaleStatus"
                                                           onkeydown="return event.keyCode !== 69 && event.keyCode !== 101"
                                                           ng-change="kpiPCFemale=kpiPCFemale==''?'':getInt(kpiPCFemale)">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="pills-apc" role="tabpanel"
                                         aria-labelledby="pills-apc-tab" style="padding: 3% 0 0 0">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="kpiAPCMaleStatus">
                                                        <input id="kpiAPCMaleStatus" type="checkbox"
                                                               value="true"
                                                               ng-model="kpiAPCMaleStatus">&nbsp;<i
                                                               class="fa fa-male male"
                                                               style="color: black;"></i>&nbsp;Male/???????????????
                                                    </label>
                                                    <input type="number" ng-model="kpiAPCMale" min="0"
                                                           placeholder="Put your number here"
                                                           max="[[offerQuota]]" class="form-control"
                                                           ng-disabled="!kpiAPCMaleStatus"
                                                           onkeydown="return event.keyCode !== 69 && event.keyCode !== 101"
                                                           ng-change="kpiAPCMale=kpiAPCMale==''?0:getInt(kpiAPCMale)">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="kpiAPCFemaleStatus">
                                                        <input id="kpiAPCFemaleStatus" type="checkbox"
                                                               value="true" ng-model="kpiAPCFemaleStatus">&nbsp;<i
                                                               class="fa fa-female female"
                                                               style="color: black;"></i>&nbsp;Female/???????????????
                                                    </label>
                                                    <input type="number" ng-model="kpiAPCFemale" min="0"
                                                           placeholder="Put your number here"
                                                           max="[[offerQuota]]" class="form-control"
                                                           onkeydown="return event.keyCode !== 69 && event.keyCode !== 101"
                                                           ng-change="kpiAPCFemale=kpiAPCFemale==''?0:getInt(kpiAPCFemale)"
                                                           ng-disabled="!kpiAPCFemaleStatus">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade active in" id="pills-ansar" role="tabpanel"
                                         aria-labelledby="pills-ansar-tab" style="padding: 3% 0 0 0">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="kpiAnsarMaleStatus">
                                                        <input id="kpiAnsarMaleStatus" type="checkbox"
                                                               value="true"
                                                               ng-model="kpiAnsarMaleStatus">&nbsp;<i
                                                               class="fa fa-male male"
                                                               style="color: black;"></i>&nbsp;Male/???????????????
                                                    </label>
                                                    <input type="number" ng-model="kpiAnsarMale" min="0"
                                                           placeholder="Put your number here"
                                                           max="[[offerQuota]]" class="form-control"
                                                           onkeydown="return event.keyCode !== 69 && event.keyCode !== 101"
                                                           ng-change="kpiAnsarMale=kpiAnsarMale==''?0:getInt(kpiAnsarMale)"
                                                           ng-disabled="!kpiAnsarMaleStatus">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="kpiAnsarFemaleStatus">
                                                        <input id="kpiAnsarFemaleStatus" type="checkbox"
                                                               value="true" ng-model="kpiAnsarFemaleStatus">&nbsp;<i
                                                               class="fa fa-female female"
                                                               style="color: black;"></i>&nbsp;Female/???????????????
                                                    </label>
                                                    <input type="number" ng-model="kpiAnsarFemale" min="0"
                                                           placeholder="Put your number here"
                                                           max="[[offerQuota]]" class="form-control"
                                                           onkeydown="return event.keyCode !== 69 && event.keyCode !== 101"
                                                           ng-change="kpiAnsarFemale=kpiAnsarFemale==''?0:getInt(kpiAnsarFemale)"
                                                           ng-disabled="!kpiAnsarFemaleStatus">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group" ng-if="isAdmin" style="margin-top: 5%">
                                    <div class="row">
                                        <div class="col-sm-5">
                                            <label class="control-label">
                                                District to send offer
                                            </label>
                                        </div>
                                        <div class="col-sm-7">
                                            <select class="form-control" ng-change="checkChange()"
                                                    style="font-size: 16px!important;"
                                                    ng-model="data.offeredDistrict">
                                                <option value="">--<?php echo app('translator')->get('title.unit'); ?> to send offer--</option>
                                                <option ng-repeat="district in allDistrict"
                                                        ng-disabled="selectedDistrict.indexOf(district.id)>=0"
                                                        value="[[district.id]]">[[district.unit_name_eng]]
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group" style="margin-top: 5%">
                                    <div class="row">
                                         <div class="col-sm-6">
                                         <div class="form-group">
                                                    <label class="control-label">Memorandum <span
                                            ng-show="isVerifying"><i
                                                class="fa fa-spinner fa-pulse"></i>&nbsp;Verifying</span><span
                                            class="text-danger"
                                            ng-if="isVerified&&!memorandumId">Memorandum ID is required.</span>
											<span
                                            class="text-danger"
                                            ng-if="isVerified&&memorandumId==''">Memorandum ID is required.</span>
											<span
                                            class="text-danger"
                                            ng-if="isVerified&&memorandumId">This id already taken.</span></label>
                                <input ng-paste="verifyMemorandumId()"  ng-blur="verifyMemorandumId()"  ng-keydown="verifyMemorandumId()" ng-keyup="verifyMemorandumId()" ng-model="memorandumId"
                                       type="text" class="form-control" name="memorandum_id"
                                       placeholder="Enter Memorandum" required>
                                            </div>
                                             </div>
                                        <div class="col-sm-6">
                                            <label class="control-label" ng-if="AvailableCount != ''">
                                                
                                                <p>Selected Ansar <span  class="text-success" style="font-size:50px !important;">[[AvailableCount]]</span></p>
                                            </label>
                                        </div>
                                        <div class="col-sm-6">
                                           
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <button class="btn btn-primary pull-right" confirm callback="sendOffer()" sp-message="message"
                        ng-disabled="(isAdmin&&!data.offeredDistrict)||quotaLoading ||!memorandumId||isVerified||isVerifying"><i ng-show="showLoadScreen"
                                                                                class="fa fa-send"></i><i
                                                                                ng-hide="showLoadScreen" class="fa fa-spinner fa-pulse"></i>[[buttonText]]
                </button>
                
                <!-- Added for local test by rintu-->
       
                <button style="margin-right: 40px; "class="btn btn-primary pull-right" confirm callback="loadAnsar()" sp-message="message1"
                        ng-disabled="(isAdmin&&!data.offeredDistrict)||quotaLoading"><i
                                                                                ng-hide="showLoadScreen1" class="fa fa-spinner fa-pulse"></i>Check Selected Ansar
                </button>
        
                <!-- Added for local test by rintu-->

                <div class="clearfix"></div>
            </div>
        </div>
        <?php endif; ?>
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
    $(function () {
    $("#pc-table").sortTable()
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('template.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>