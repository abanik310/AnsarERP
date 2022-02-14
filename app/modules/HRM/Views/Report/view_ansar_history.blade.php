@extends('template.master')
@section('title','View Ansar History')
@section('breadcrumb')
    {!! Breadcrumbs::render('view_ansar_history') !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller("ViewAnsarHistoryController", function ($rootScope, $scope, $http, $sce) {
            $scope.ansarDetail = {};
            $scope.allLoading = false;
            $scope.loadAnsarDetail = function (id) {
                $scope.allLoading = true;
                $scope.errorFound = 0;
                $scope.errorMessage = "";
                $http({
                    method: 'get',
                    url: '{{URL::route('view_ansar_history_report')}}',
                    params: {ansar_id: id}
                }).then(function (response) {
                    $scope.ansarDetail = response.data;
                    $scope.allLoading = false;
                }, function (response) {
                    $scope.ansarDetail = {};
                    $scope.errorFound = 1;
                    $scope.errorMessage = "Please enter a valid Ansar ID";
                    $scope.allLoading = false;
                })
            };
            $scope.getKPIInfo = function (kpi) {
                if (kpi) {
                    return kpi.kpi_name + ", " + kpi.thana.thana_name_bng + ", " + kpi.unit.unit_name_bng + ", " + kpi.division.division_name_bng;
                }
                return "";
            };
            $scope.getUnitAddress = function (unit) {
                if (unit) {
                    return unit.unit_name_bng + ", " + unit.division.division_name_bng;
                }
                return "";
            };
            $scope.convertDateObj = function (dateStr) {
                if (dateStr) {
                    return new Date(dateStr);
                }
                return '';
            };
            $scope.convertDate = function (d) {
                return moment(d).format("DD-MMM-YYYY")
            }
                        var v = '<div class="text-center" style="margin-top: 20px"><i class="fa fa-spinner fa-pulse"></i></div>'
         
            $scope.showDetailLog = function (log_id) {
                $("#log-form").modal('show');
                $rootScope.detail = $sce.trustAsHtml(v);
                //alert(log_id);
                var data = {
                    log_id: log_id
                };
                
                $http({
                    url: '{{URL::to('HRM/log_details')}}',
                            method: 'post',
                            data: angular.toJson(data)
                    }).then(function (response) {
                   // console.log(response.data);
                      $rootScope.detail = $sce.trustAsHtml(response.data.view);

                    }, function (response) {
                          $rootScope.detail = $sce.trustAsHtml(response.data.view);
                    })
            }
            
//            $http.get(url).then(function (response) {
//                    $rootScope.detail = $sce.trustAsHtml(response.data.view);
//                    $rootScope.applicant_id = response.data.id;
//                })
        });
    </script>
    <style></style>
    <div ng-controller="ViewAnsarHistoryController">
        <section class="content">
            <div class="box box-solid">
                <div class="overlay" ng-if="allLoading">
                    <span class="fa">
                        <i class="fa fa-refresh fa-spin"></i> <b>Loading...</b>
                    </span>
                </div>
                <div class="box-body"><br>
                    <div class="row">
                        <div class="col-md-6 col-centered">
                            <div class="col-md-6 col-sm-12 col-xs-12">
                                <form ng-submit="loadAnsarDetail(ansar_id)" class="row">
                                    <div class="form-group">
                                        <input type="text" ng-model="ansar_id" class="form-control"
                                               placeholder="Enter Ansar ID">
                                        <span class="text-danger" ng-if="errorFound==1">[[errorMessage]]</span>
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-6 col-sm-12 col-xs-12">
                                <button class="btn btn-primary" ng-click="loadAnsarDetail(ansar_id)">Generate Ansar
                                    Service Record
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box box-solid" ng-if="ansarDetail && ansarDetail['ansar']">
                <div class="box-title"><h3 style="margin: 1%;">Personal Information</h3></div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Designation</th>
                                    <th>Current Status</th>
                                    <th>Mobile Number</th>
                                    <th>Gender</th>
                                    <th>Picture</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td><a href="/HRM/entryreport/[[ansarDetail['ansar'].ansar_id]]">[[ansarDetail['ansar'].ansar_name_bng]]</a>
                                    </td>
                                    <td>[[ansarDetail['ansar'].designation.name_bng]]</td>
                                    <td>
                                        [[ansarDetail['status'].join()]]&nbsp;
                                        <span ng-if="ansarDetail && ansarDetail['cPanel'] && ansarDetail['cPanel'].go_panel_position==null">(Global Blocked)</span>
                                        <span ng-if="ansarDetail && ansarDetail['cPanel'] && ansarDetail['cPanel'].re_panel_position==null">(Regional Blocked)</span>
                                    </td>
                                    <td>[[ansarDetail['ansar'].mobile_no_self]]</td>
                                    <td>[[ansarDetail['ansar'].sex]]</td>
                                    <td><img src="/image?file=[[ansarDetail['ansar'].profile_pic]]"
                                             style="width: 80px;height: 80px"
                                             alt="[[ansarDetail['ansar'].ansar_name_bng]]"/></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-12" ng-if="ansarDetail['future']">
                            <h4>Schedule Job&nbsp;<span class="small">(N.B.: Action take in future)</span></h4>
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th>Form Status</th>
                                    <th>To Status</th>
                                    <th>Active Date</th>
                                    <th>Action User Id</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>[[ansarDetail['future'].from_status]]</td>
                                    <td>[[ansarDetail['future'].to_status]]</td>
                                    <td>[[convertDateObj(ansarDetail['future'].activation_date) | date:'mediumDate']]
                                    </td>
                                    <td>[[ansarDetail['future'].action_by]]</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box box-solid" ng-if="ansarDetail && (ansarDetail['cOffer'] || ansarDetail['lOffer'])">
                <div class="box-title"><h3 style="margin: 1%;">Offer Information</h3></div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12">
                            <h4>Current</h4>
                            <p ng-if="!ansarDetail['cOffer']">No data</p>
                            <table class="table table-bordered table-striped" ng-if="ansarDetail['cOffer']">
                                <thead>
                                <tr>
                                    <th>Offer Date & Time</th>
                                    <th>Offer District</th>
                                    <th>Offer Type</th>
                                    <th>Come From</th>
                                    <th>SMS Status</th>
                                    <th>Memorandum</th>
                                    
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>[[convertDateObj(ansarDetail['cOffer'].sms_send_datetime) |
                                        date:'medium']]
                                    </td>
                                    <td>[[getUnitAddress(ansarDetail['cOffer'].district)]]</td>
                                    <td>[[ansarDetail['cOffer'].offerType]]</td>
                                    <td>[[ansarDetail['cOffer'].come_from]]</td>
                                    <td>[[ansarDetail['cOffer'].sms_status]]</td>
                                    <td>[[ansarDetail['cOffer'].memo_id]]</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-12">
                            <h4>History</h4>
                            <p ng-if="!Array.isArray(ansarDetail['lOffer']) && ansarDetail['lOffer'].length<=0">No
                                data</p>
                            <table class="table table-bordered table-striped"
                                   ng-if="ansarDetail['lOffer'] && ansarDetail['lOffer'].length>0">
                                <thead>
                                <tr>
                                    <th>Offer Date & Time</th>
                                    <th>Offer district</th>
                                    <th>Offer type</th>
                                    <th>Reply type</th>
                                    <th>Memorandum</th>
                                    <th>Action User Id/Name</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr ng-repeat="loffer in ansarDetail['lOffer']">
                                    <td ng-style="loffer.offerBlocked && loffer.offerBlocked==true?{'background': 'orange','color':'white'}:''">
                                        [[convertDateObj(loffer.offered_date) | date:'medium']]
                                    </td>
                                    <td ng-style="loffer.offerBlocked && loffer.offerBlocked==true?{'background': 'orange','color':'white'}:''">
                                        [[getUnitAddress(loffer.district)]]
                                    </td>
                                    <td ng-style="loffer.offerBlocked && loffer.offerBlocked==true?{'background': 'orange','color':'white'}:''">
                                        [[loffer.offerType]]
                                    </td>
                                    <td ng-style="loffer.offerBlocked && loffer.offerBlocked==true?{'background': 'orange','color':'white'}:''">
                                        [[loffer.reply_type]]
                                    </td>
                                    <td ng-style="loffer.offerBlocked && loffer.offerBlocked==true?{'background': 'orange','color':'white'}:''">
                                        [[loffer.memo_id]]
                                    </td>
                                    <td ng-style="loffer.offerBlocked && loffer.offerBlocked==true?{'background': 'orange','color':'white'}:''">
                                        [[loffer.action_user_id]] / [[loffer.user_details.user_name]]
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-12">
                            <h4>Offer Block</h4>
                            <p ng-if="!Array.isArray(ansarDetail['bOffer']) && ansarDetail['bOffer'].length<=0">No
                                data</p>
                            <table class="table table-bordered table-striped"
                                   ng-if="ansarDetail['bOffer'] && ansarDetail['bOffer'].length>0">
                                <thead>
                                <tr>
                                    <th>Status</th>
                                    <th>Block Date</th>
                                    <th>Unblock Date</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr ng-repeat="boffer in ansarDetail['bOffer']">
                                    <td>[[boffer.status]]</td>
                                    <td>[[convertDateObj(boffer.blocked_date) | date:'mediumDate']]</td>
                                    <td>[[convertDateObj(boffer.unblocked_date) | date:'mediumDate']]</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box box-solid" ng-if="ansarDetail && (ansarDetail['cPanel'] || ansarDetail['lPanel'])">
                <div class="box-title"><h3 style="margin: 1%;">Panel Information</h3></div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12">
                            <h4>Current</h4>
                            <p ng-if="!ansarDetail['cPanel']">No Data</p>
                            <table class="table table-bordered table-striped" ng-if="ansarDetail['cPanel']">
                                <thead>
                                <tr>
                                    <th>Global Panel<br>Date & Time</th>
                                    <th>Global Panel<br>Position</th>
                                    <th>Regional Panel<br>Date & Time</th>
                                    <th>Regional Panel<br>Position</th>
                                    <th>Come From</th>
                                    <th>Memorandum</th>
                                    <th>Action User<br>Id</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>
                                        [[convertDateObj(ansarDetail['cPanel'].panel_date) | date:'medium']]
                                    </td>
                                    <td ng-style="ansarDetail['cPanel'].go_panel_position==null? {'background': 'orange','color':'white'}:''">
                                        [[ansarDetail['cPanel'].go_panel_position==null ? "Offer Blocked" :(ansarDetail['rcvsms'].sms_status=="ACCEPTED" && ansarDetail['cPanel'].go_panel_position==0) ? "Offer Accepted": (ansarDetail['cOffer'].id>0 && ansarDetail['cPanel'].go_panel_position==0)? "Offered" : ansarDetail['cPanel'].go_panel_position]]
                                    </td>
                                    <td>
                                        [[convertDateObj(ansarDetail['cPanel'].re_panel_date) | date:'medium']]
                                    </td>
                                    <td ng-style="ansarDetail['cPanel'].re_panel_position==null? {'background': 'orange','color':'white'}:''">
                                        [[ansarDetail['cPanel'].re_panel_position==null ? "Offer Blocked" : (ansarDetail['rcvsms'].sms_status=="ACCEPTED" && ansarDetail['cPanel'].re_panel_position==0) ? "Offer Accepted": (ansarDetail['cOffer'].id>0 && ansarDetail['cPanel'].re_panel_position==0) ? "Offered" :ansarDetail['cPanel'].re_panel_position]]
                                    </td>
                                    <td>
                                        [[ansarDetail['cPanel'].come_from]]
                                    </td>
                                    <td>
                                        [[ansarDetail['cPanel'].memorandum_id]]
                                    </td>
                                    <td>
                                        [[ansarDetail['cPanel'].action_user_id]]
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-12">
                            <h4>History</h4>
                            <p ng-if="!Array.isArray(ansarDetail['lPanel']) && ansarDetail['lPanel'].length<=0">No
                                Data</p>
                            <table class="table table-bordered table-striped"
                                   ng-if="ansarDetail['lPanel'] && ansarDetail['lPanel'].length>0">
                                <thead>
                                <tr>
                                    <th>Global Panel<br>Date & Time</th>
                                    <th>Global Panel<br>Position</th>
                                    <th>Regional Panel<br>Date & Time</th>
                                    <th>Regional Panel<br>Position</th>
                                    <th>Come From</th>
                                    <th>Memorandum</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr ng-repeat="lpanel in ansarDetail['lPanel']">
                                    <td>[[convertDateObj(lpanel.panel_date) | date:'medium']]</td>
                                    <td>[[lpanel.go_panel_position]]</td>
                                    <td>[[convertDateObj(lpanel.re_panel_date) | date:'medium']]</td>
                                    <td>[[lpanel.re_panel_position]]</td>
                                    <td>[[lpanel.come_from]]</td>
                                    <td>[[lpanel.old_memorandum_id]]</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box box-solid" ng-if="ansarDetail && (ansarDetail['cRest'] || ansarDetail['lRest'])">
                <div class="box-title"><h3 style="margin: 1%;">Rest Information</h3></div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12">
                            <h4>Current</h4>
                            <p ng-if="!ansarDetail['cRest']">No Data</p>
                            <table class="table table-bordered table-striped" ng-if="ansarDetail['cRest']">
                                <thead>
                                <tr>
                                    <th>Rest Date</th>
                                    <th>Come From</th>
                                    <th>Total Service(In Days)</th>
                                    <th>Reason</th>
                                    <th>Memorandum</th>
                                    <th>Comment</th>
                                    <th>Action User Id</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>[[convertDateObj(ansarDetail['cRest'].rest_date) | date:'mediumDate']]</td>
                                    <td>[[ansarDetail['cRest'].rest_form]]</td>
                                    <td>[[ansarDetail['cRest'].total_service_days]]</td>
                                    <td>[[ansarDetail['cRest'].reason.reason_in_bng]]</td>
                                    <td>[[ansarDetail['cRest'].memorandum_id]]</td>
                                    <td>[[ansarDetail['cRest'].comment]]</td>
                                    <td>[[ansarDetail['cRest'].action_user_id]]</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-12">
                            <h4>History</h4>
                            <p ng-if="!Array.isArray(ansarDetail['lRest']) && ansarDetail['lRest'].length<=0">No
                                Data</p>
                            <table class="table table-bordered table-striped"
                                   ng-if="ansarDetail['lRest'] && ansarDetail['lRest'].length>0">
                                <thead>
                                <tr>
                                    <th>Rest Date</th>
                                    <th>Come From</th>
                                    <th>Total Service(In Days)</th>
                                    <th>Reason</th>
                                    <th>Move Date</th>
                                    <th>Move To</th>
                                    <th>Memorandum</th>
                                    <th>Comment</th>
                                    <th>Action User Id/ User Name</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr ng-repeat="lrest in ansarDetail['lRest']">
                                    <td>[[convertDateObj(lrest.rest_date) | date:'mediumDate']]</td>
                                    <td>[[lrest.rest_type]]</td>
                                    <td>[[lrest.total_service_days]]</td>
                                    <td>[[lrest.reason.reason_in_bng]]</td>
                                    <td>[[convertDateObj(lrest.move_date) | date:'mediumDate']]</td>
                                    <td>[[lrest.move_to]]</td>
                                    <td>[[lrest.old_memorandum_id]]</td>
                                    <td>[[lrest.comment]]</td>
                                    <td>[[lrest.action_user_id]] / [[lrest.user_details.user_name]]</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box box-solid"
                 ng-if="ansarDetail && (ansarDetail['cEmbodiment'] || ansarDetail['lEmbodiment'])">
                <div class="box-title"><h3 style="margin: 1%;">Embodiment Information</h3></div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12">
                            <h4>Current</h4>
                            <p ng-if="!ansarDetail['cEmbodiment']">No Data</p>
                            <table class="table table-bordered table-striped" ng-if="ansarDetail['cEmbodiment']">
                                <thead>
                                <tr>
                                    <th>Reporting Date</th>
                                    <th>Embodiment Date</th>
                                    <th>Service End Date</th>
                                    <th>KPI</th>
                                    <th>Memorandum</th>
                                    <th>Action User Id</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>
                                        [[convertDateObj(ansarDetail['cEmbodiment'].reporting_date) |
                                        date:'mediumDate']]
                                    </td>
                                    <td>[[convertDateObj(ansarDetail['cEmbodiment'].joining_date) | date:'mediumDate']]
                                    </td>
                                    <td>[[convertDateObj(ansarDetail['cEmbodiment'].service_ended_date) |
                                        date:'mediumDate']]
                                    </td>
                                    <td>[[getKPIInfo(ansarDetail['cEmbodiment'].kpi)]]</td>
                                    <td>[[ansarDetail['cEmbodiment'].memorandum_id]]</td>
                                    <td>[[ansarDetail['cEmbodiment'].action_user_id]]</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-12">
                            <h4>History</h4>
                            <p ng-if="!Array.isArray(ansarDetail['lEmbodiment']) && ansarDetail['lEmbodiment'].length<=0">
                                No Data</p>
                            <table class="table table-bordered table-striped"
                                   ng-if="ansarDetail['lEmbodiment'] && ansarDetail['lEmbodiment'].length>0">
                                <thead>
                                <tr>
                                    <th>Reporting<br>Date</th>
                                    <th>Embodiment<br>Date</th>
                                    <th>Disembodiment<br>Date</th>
                                    <th>Disembodiment<br>Reason</th>
                                    <th>Comment</th>
                                    <th>KPI</th>
                                    <th>Memorandum</th>
                                    <th>Action User Id</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr ng-repeat="lembodiment in ansarDetail['lEmbodiment']">
                                    <td>[[convertDateObj(lembodiment.reporting_date) | date:'mediumDate']]</td>
                                    <td>[[convertDateObj(lembodiment.joining_date) | date:'mediumDate']]</td>
                                    <td>[[convertDateObj(lembodiment.release_date) | date:'mediumDate']]</td>
                                    <td>[[lembodiment.disembodiment_reason.reason_in_bng]]</td>
                                    <td>[[lembodiment.comment]]</td>
                                    <td>[[getKPIInfo(lembodiment.kpi)]]</td>
                                    <td>[[lembodiment.old_memorandum_id]]</td>
                                    <td>[[lembodiment.action_user_id]]</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box box-solid" ng-if="ansarDetail && (ansarDetail['cFreeze'] || ansarDetail['lFreeze'])">
                <div class="box-title"><h3 style="margin: 1%;">Freeze Status Information</h3></div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12">
                            <h4>Current</h4>
                            <p ng-if="!ansarDetail['cFreeze']">No Data</p>
                            <table class="table table-bordered table-striped" ng-if="ansarDetail['cFreeze']">
                                <thead>
                                <tr>
                                    <th>Freeze Date</th>
                                    <th>Freeze Reason</th>
                                    <th>Comment On Freeze</th>
                                    <th>KPI</th>
                                    <th>Memorandum</th>
                                    <th>Action User Id</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>[[convertDateObj(ansarDetail['cFreeze'].freez_date) | date:'mediumDate']]</td>
                                    <td>[[ansarDetail['cFreeze'].freez_reason]]</td>
                                    <td>[[ansarDetail['cFreeze'].comment_on_freez]]</td>
                                    <td>[[getKPIInfo(ansarDetail['cFreeze'].kpi)]]</td>
                                    <td>[[ansarDetail['cFreeze'].memorandum_id]]</td>
                                    <td>[[ansarDetail['cFreeze'].action_user_id]]</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-12">
                            <h4>History</h4>
                            <p ng-if="!Array.isArray(ansarDetail['lFreeze']) && ansarDetail['lFreeze'].length<=0">
                                No Data</p>
                            <table class="table table-bordered table-striped"
                                   ng-if="ansarDetail['lFreeze'] && ansarDetail['lFreeze'].length>0">
                                <thead>
                                <tr>
                                    <th>Unfreeze Date</th>
                                    <th>Unfreeze Comment</th>
                                    <th>Unfreeze To</th>
                                    <th>Freeze Date</th>
                                    <th>Freeze Reason</th>
                                    <th>Freeze Comment</th>
                                    <th>Action User Id</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr ng-repeat="lfreeze in ansarDetail['lFreeze']">
                                    <td>[[convertDateObj(lfreeze.move_frm_freez_date) | date:'mediumDate']]</td>
                                    <td>[[lfreeze.comment_on_move]]</td>
                                    <td>[[lfreeze.move_to]]</td>
                                    <td>[[convertDateObj(lfreeze.freez_date) | date:'mediumDate']]</td>
                                    <td>[[lfreeze.freez_reason]]</td>
                                    <td>[[lfreeze.comment_on_freez]]</td>
                                    <td>[[lfreeze.action_user_id]]</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!--
            <div class="box box-solid" ng-if="ansarDetail && ansarDetail['transfer']">
                <div class="box-title"><h3 style="margin: 1%;">Transfer Log</h3></div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12">
                            <p ng-if="!Array.isArray(ansarDetail['transfer']) && ansarDetail['transfer'].length<=0">
                                No Data</p>
                            <table class="table table-bordered table-striped"
                                   ng-if="ansarDetail['transfer'] && ansarDetail['transfer'].length>0">
                                <thead>
                                <tr>
                                    <th>Present KPI</th>
                                    <th>Present KPI Embodiment Date</th>
                                    <th>Transfer KPI</th>
                                    <th>Transfer KPI Embodiment Date</th>
                                    <th>Memorandum</th>
                                    <th>Action User Id</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr ng-repeat="transfer in ansarDetail['transfer']">
                                    <td>[[getKPIInfo(transfer.present_kpi)]]</td>
                                    <td>[[convertDateObj(transfer.present_kpi_join_date) | date:'mediumDate']]</td>
                                    <td>[[getKPIInfo(transfer.transfer_kpi)]]</td>
                                    <td>[[convertDateObj(transfer.transfered_kpi_join_date) | date:'mediumDate']]</td>
                                    <td>[[transfer.transfer_memorandum_id]]</td>
                                    <td>[[transfer.action_by]]</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>-->
            <div class="box box-solid" ng-if="ansarDetail && ansarDetail['transfer']">
                <div class="box-title"><h3 style="margin: 1%;">Transfer Log</h3></div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12">
                           
              <div class="table-responsive">
                                <table class="table table-bordered">
                                    <tr>
                                        <th>SL. No</th>
                                        <th>Type</th>
                                        <th>KPI Name</th>
                                        <!--<th>To KPI</th>-->
                                        <th>District</th>
                                        <th>Thana</th>
                                       <!-- <th>Embodiment Date</th>
                                        <th>Transfer Date</th>-->
                                        <th>Memo ID</th>
                                        <th>Action User</th>
<!--                                        <th>KPI Joining Date</th>
                                        <th>KPI End Date</th>-->
                                        <th style="width: 10%;">Date</th>
                                        <th>Service Days</th>
                                    </tr>
                                    <tr ng-show="ansarDetail['transfer_data'].length==0">
                                        <td colspan="7" class="warning">
                                            No Data is available to show
                                        </td>
                                    </tr>
                                    <tbody ng-if="errorFound==1" ng-bind-html="ansarDetail['transfer_data']"></tbody>
                                    <tr ng-repeat="a in ansarDetail['transfer_data']" ng-show="ansarDetail['transfer_data'].length>0">
                                        
                                        <td>[[$index+1]]</td>
                                        <td>[[a.type]]</td>
                                        <!--<td>[[a.FromkpiName]], [[a.thana_bng]], [[a.unit_bng]]</td>-->
                                        <td>
                                          <span data-ng-if="a.type == 'embodiment' && a.kpi.status == ''">[[a.data.kpi.kpi_name]]</span>
										  <span data-ng-if="a.type == 'embodiment' && a.kpi.status != ''">[[a.kpi.kpidetails.kpi_name]]</span>
										  <span data-ng-if="a.type == 'transfer'">[[a.data.transfer_kpi.kpi_name]]</span>
                                        </td>
                                        <td>
                                          <span data-ng-if="a.type == 'embodiment' && a.kpi.status == ''">[[a.data.kpi.unit.unit_name_bng]]</span>
										  <span data-ng-if="a.type == 'embodiment' && a.kpi.status != ''">[[a.kpi.kpidetails.unit.unit_name_bng]]</span>
                                          <span data-ng-if="a.type == 'transfer'">[[a.data.transfer_kpi.unit.unit_name_bng]]</span>

                                        </td>
                                       <td>
                                           <span span data-ng-if="a.type == 'embodiment' && a.kpi.status == ''">[[a.data.kpi.thana.thana_name_bng]]</span>
										   <span data-ng-if="a.type == 'embodiment' && a.kpi.status != ''">
										   [[a.kpi.kpidetails.thana.thana_name_bng]]</span>
                                           <span data-ng-if="a.type == 'transfer'">[[a.data.transfer_kpi.thana.thana_name_bng]]</span>
                                          
                                        </td>
                                        <td>
                                           <span data-ng-if="a.type == 'embodiment' && a.data.old_memorandum_id && a.data.old_memorandum_id.length">[[a.data.old_memorandum_id]]</span>                                           
                                           <span data-ng-if="a.type == 'embodiment' && a.data.memorandum_id && a.data.memorandum_id.length">[[a.data.memorandum_id]]</span>                                           
                                           <span data-ng-if="a.type == 'transfer'">[[a.data.transfer_memorandum_id]]</span>
                                           
                                        </td>
                                        <td>
                                            <span data-ng-if="a.type == 'transfer'">[[a.data.action_by]]</span>
                                            <span data-ng-if="a.type == 'freez'">[[a.data.action_user_id]]</span>
                                            <span data-ng-if="a.type == 'unfreez'">[[a.data.action_user_id]]</span>
                                            <span data-ng-if="a.type == 'embodiment'">[[a.data.action_user_id]]</span>
<!--                                            <span data-ng-if="a.type == 'disembodiment'">[[a.data.action_user_id]]</span>-->


                                        </td>
                                        <td>
                                            <span data-ng-if="a.type == 'embodiment'">[[convertDate(a.data.joining_date)]]</span>
                                            <span data-ng-if="a.type == 'transfer'">[[convertDate(a.data.transfered_kpi_join_date)]]</span>
                                            <span data-ng-if="a.type == 'disembodiment'">[[convertDate(a.time)]]</span>
                                            <span data-ng-if="a.type == 'freez'">[[convertDate(a.data.freez_date)]]</span>
                                            <span data-ng-if="a.type == 'unfreez'">[[convertDate(a.data.move_frm_freez_date)]]</span>

                                        </td>
<!--                                        <td>[[convertDate(a.transferDate)]]</td>                                        -->
                                        <td>[[a.time_difference]]</td>
                                        
                                    </tr>
<!--                                    <tr ng-repeat="a in ansars" ng-show="$last">
                                        
                                        <td>[[$index+2]]</td>
                                        <td>[[a.TokpiName]], [[a.tk_thana_bng]], [[a.tk_unit_bng]]</td>
                                        <td>[[a.TokpiName]]</td>
                                        <td>[[a.tk_unit]]</td>
                                        <td>[[a.tk_thana]]</td>
                                        <td></td>
                                        <td></td>
                                        <td>[[convertDate(a.transferDate)]]</td>
                                        <td></td>
                                        <td></td>
                                        
                                    </tr>-->
                                </table>
                            </div> 
                        </div> 
                    </div> 
                </div> 
            </div>
            <div class="box box-solid" ng-if="ansarDetail && ansarDetail['block']">
                <div class="box-title"><h3 style="margin: 1%;">Block Status Information</h3></div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12">
                            <p ng-if="!Array.isArray(ansarDetail['block']) && ansarDetail['block'].length<=0">
                                No Data</p>
                            <table class="table table-bordered table-striped"
                                   ng-if="ansarDetail['block'] && ansarDetail['block'].length>0">
                                <thead>
                                <tr>
                                    <th>Block Status Date</th>
                                    <th>Come From</th>
                                    <th>Block Reason</th>
                                    <th>Unblock Date</th>
                                    <th>Unblock Reason</th>
                                    <th>Action User Id</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr ng-repeat="block in ansarDetail['block']">
                                    <td>[[convertDateObj(block.date_for_block) | date:'mediumDate']]</td>
                                    <td>[[block.block_list_from]]</td>
                                    <td>[[block.comment_for_block]]</td>
                                    <td>[[convertDateObj(block.date_for_unblock) | date:'mediumDate']]</td>
                                    <td>[[block.comment_for_unblock]]</td>
                                    <td>[[block.action_user_id]]</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            
            <div class="box box-solid" ng-if="ansarDetail && (ansarDetail['cBlack'] || ansarDetail['lBlack'])">
                <div class="box-title"><h3 style="margin: 1%;">Black Status Information</h3></div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12">
                            <h4>Current</h4>
                            <p ng-if="!ansarDetail['cBlack']">No Data</p>
                            <table class="table table-bordered table-striped" ng-if="ansarDetail['cBlack']">
                                <thead>
                                <tr>
                                    <th>Black Status Date</th>
                                    <th>Come From</th>
                                    <th>Comment</th>
                                    <th>Action User Id</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>[[convertDateObj(ansarDetail['cBlack'].black_listed_date) | date:'mediumDate']]
                                    </td>
                                    <td>[[ansarDetail['cBlack'].black_list_from]]</td>
                                    <td>[[ansarDetail['cBlack'].black_list_comment]]</td>
                                    <td>[[ansarDetail['cBlack'].black_list_comment]]</td>
                                    <td>[[ansarDetail['cBlack'].action_user_id]]</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-12">
                            <h4>History</h4>
                            <p ng-if="!Array.isArray(ansarDetail['lBlack']) && ansarDetail['lBlack'].length<=0">
                                No Data</p>
                            <table class="table table-bordered table-striped"
                                   ng-if="ansarDetail['lBlack'] && ansarDetail['lBlack'].length>0">
                                <thead>
                                <tr>
                                    <th>Black Status Date</th>
                                    <th>Come From</th>
                                    <th>Reason</th>
                                    <th>Unblack Status Date</th>
                                    <th>Unblack Reason</th>
                                    <th>Move Date</th>
                                    <th>Move To</th>
                                    <th>Action User Id</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr ng-repeat="lblack in ansarDetail['lBlack']">
                                    <td>[[convertDateObj(lblack.black_listed_date) | date:'mediumDate']]</td>
                                    <td>[[lblack.black_list_from]]</td>
                                    <td>[[lblack.black_list_comment]]</td>
                                    <td>[[convertDateObj(lblack.unblacklist_date) | date:'mediumDate']]</td>
                                    <td>[[lblack.unblacklist_comment]]</td>
                                    <td>[[convertDateObj(lblack.move_date) | date:'mediumDate']]</td>
                                    <td>[[lblack.move_to]]</td>
                                    <td>[[lblack.action_user_id]]</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            
            <!-- Ansar Personal Info Change Log Section -->
            
            <div class="box box-solid">
                <div class="box-title"><h3 style="margin: 1%;">Ansar Change Log</h3></div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12">
                            <h4>Personal Information</h4>
                            <p ng-if="!Array.isArray(ansarDetail['cInfoHistoryLog']) && ansarDetail['cInfoHistoryLog'].length<=0">No Data</p>
                            <table class="table table-bordered table-striped" ng-if="ansarDetail['cInfoHistoryLog'] && ansarDetail['cInfoHistoryLog'].length>0">
                                <thead>
                                <tr>
                                    <th>Log Date</th>
                                    <th>Name</th>
                                    <th>Name(Eng)</th>
                                    <th>Mobile No</th>
                                    <th>NID</th>
                                    <th>Designation</th>
                                    <th>Date of Birth</th>
                                    <th>Action User Id</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr ng-repeat="cInfoHistoryLog in ansarDetail['cInfoHistoryLog']">
                                    <td>[[convertDateObj(cInfoHistoryLog.action_time) | date:'medium']]</td>
                                    <td>[[cInfoHistoryLog.ansar_name_bng]]</td>
                                    <td>[[cInfoHistoryLog.ansar_name_eng]]</td>
                                    <td>[[cInfoHistoryLog.mobile_no_self]]</td>
                                    <td>[[cInfoHistoryLog.national_id_no]]</td>
                                    <td>[[cInfoHistoryLog.designation_id]]</td>
                                    <td>[[convertDateObj(cInfoHistoryLog.data_of_birth) | date:'mediumDate']]</td>
                                    <td>[[cInfoHistoryLog.user_id]]</td> 
                                    <td ng-click=showDetailLog([[cInfoHistoryLog.log_id]]) data-id="[[cInfoHistoryLog.log_id]]"><i class="fa fa-eye"></i></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>                       
                    </div>
                </div>
            </div>
            
            <div class="modal fade" id="log-form">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">Detail Log</h4>
                        </div>
                        <div class="modal-body" ng-bind-html="detail" compile-htmll>
                            
                       

                        </div>
                    </div>
                </div>
            </div>
            
            
        </section>
    </div>
@endsection