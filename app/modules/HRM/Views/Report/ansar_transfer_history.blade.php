@extends('template.master')
@section('title','Ansar Transfer History')
@section('breadcrumb')
    {!! Breadcrumbs::render('transfer_ansar_history') !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller('TransferController', function ($scope,$http) {
            $scope.ansars = [];
            $scope.ansar = "";
            $scope.pi = false;
            $scope.allLoading = false;
            $scope.errorFound=0;
            $scope.loadTransferHistory = function (ansar_id) {
                $scope.allLoading = true;
                $http({
                    url:'{{URL::route('get_transfer_ansar_history')}}',
                    method:'get',
                    params:{ansar_id:ansar_id}
                }).then(function (response) {
                    $scope.errorFound=0;
                    $scope.ansars = response.data.transfer_data;
					console.log($scope.ansars);
                    $scope.ansar = response.data.ansar;
                    $scope.pi = response.data.pi;
                    $scope.allLoading = false;
                },function (response) {
                    $scope.errorFound=1;
                    $scope.allLoading = false;
                    $scope.ansars = '';
                    $scope.ansar = "";
                    $scope.pi = false;
                    $scope.errorMessage = "Please enter a valid Ansar ID";
                })
            }
            $scope.loadTransferHistoryOnKeyPress = function (ansar_id,$event) {
                if($event.keyCode==13) {
                    $scope.allLoading = true;
                    $http({
                        url: '{{URL::route('get_transfer_ansar_history')}}',
                        method: 'get',
                        params: {ansar_id: ansar_id}
                    }).then(function (response) {
                        $scope.errorFound=0;
                        $scope.ansars = response.data;
                        $scope.allLoading = false;
                    }, function (response) {
                        $scope.errorFound=1;
                        $scope.allLoading = false;
                        $scope.ansars = '';
                        $scope.errorMessage = "Please enter a valid Ansar ID";
                    })
                }
            }    
          
            
              $scope.loadReportData = function (reportName, type) {
                $scope.allLoading = true;
                $http({
                    method: 'get',
                    url: '{{URL::route('localize_report')}}',
                    params: {name: reportName, type: type}
                }).then(function (response) {
                    $scope.report = response.data;
                    $scope.allLoading = false;
                })
            }
            $scope.dateConvert = function (date) {
                return (moment(date).format('DD-MMM-Y'));
            }
            $scope.loadReportData("ansar_service_report", "eng")
            $scope.convertDate = function (d) {
                return moment(d).format("DD-MMM-YYYY")
            }
        })
        $(function () {
            function beforePrint(){
//                console.log($("body").find("#print-body").html())
                $("#print-area").remove();
                $('body').append('<div id="print-area" class="letter">'+$("#ansar_transfer_history").html()+'</div>')
            }
            function afterPrint(){
                $("#print-area").remove()
            }
            if(window.matchMedia){
                var mediaQueryList = window.matchMedia('print');
                mediaQueryList.addListener(function(mql) {
                    if (mql.matches) {
                        beforePrint();
                    } else {
                        afterPrint();
                    }
                });
            }
            window.onbeforeprint = beforePrint;
            window.onafterprint = afterPrint;
            $('body').on('click','#print-report', function (e) {
               // alert("pppp")
                e.preventDefault();

                window.print();

            })
        })

    </script>
    <style>
        input::-webkit-input-placeholder {
            color: #7b7b7b !important;
        }

        input:-moz-placeholder { /* Firefox 18- */
            color: #7b7b7b !important;
        }

        input::-moz-placeholder {  /* Firefox 19+ */
            color: #7b7b7b !important;
        }

        input:-ms-input-placeholder {
            color: #7b7b7b !important;
        }
    </style>
    
    <style>
        @page {
            size: 9in 13in;
            margin: 27mm 16mm 27mm 16mm;
        }
    </style>
    
    <div ng-controller="TransferController">
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
                                <div class="form-group">
                                    {{--<label class="control-label">Enter a ansar id</label>--}}
                                    <input type="text" ng-model="ansar_id" class="form-control" placeholder="Enter Ansar ID" ng-keypress="loadTransferHistoryOnKeyPress(ansar_id,$event)">
                                    <span class="text-danger" ng-if="errorFound==1"><p>[[errorMessage]]</p></span>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12 col-xs-12">
                                <button class="btn btn-primary" ng-click="loadTransferHistory(ansar_id)">Generate Transfer Report</button>

                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12" id="ansar_transfer_history">
                            <h3 style="text-align: center">Ansar Transfer History&nbsp;<a href="#" id="print-report"><span class="glyphicon glyphicon-print"></span></a></h3>
                             <div class="table-responsive" align="center">
                            <table class="table " style="width: auto">
                                <tr>
                                    <td style="background: #ffffff">[[report.ansar_detail.name]]</td>
                                    <td style="background: #ffffff" ng-if="!ansar">--</td>
                                    <td style="background: #ffffff" ng-if="ansar">[[ansar.ansar_name_bng]]</td>
                                    <td style="background: #ffffff" rowspan="4" align="center" valign="middle">
                                        <img src="{{URL::to('image').'?file='}}[[ansar.profile_pic]]" class="img-thumbnail"
                                             style="width:120px;height: auto">
                                    </td>
                                </tr>
                                <tr>
                                    <td style="background: #FFFFFF">[[report.ansar_detail.rank]]</td>
                                    <td style="background: #ffffff" ng-if="!ansar">--</td>
                                    <td style="background: #ffffff" ng-if="ansar">[[ansar.name_bng]]</td>
                                </tr>
                                
                                <tr>
                                    <td style="background: #FFFFFF">Mobile</td>
                                    <td style="background: #ffffff" ng-if="!ansar">--</td>
                                    <td style="background: #ffffff" ng-if="ansar">[[ansar.mobile_no_self]]</td>
                                </tr>
                                
                                <tr>
                                    <td style="background: #ffffff">[[report.ansar_detail.bg]]</td>
                                    <td style="background: #ffffff" ng-if="!ansar">--</td>
                                    <td style="background: #ffffff" ng-if="ansar">[[ansar.blood_group_name_bng]]</td>
                                </tr>
                                <tr>
                                    <td style="background: #ffffff">[[report.ansar_detail.district]]</td>
                                    <td style="background: #ffffff" ng-if="!ansar">--</td>
                                    <td style="background: #ffffff" ng-if="ansar">[[ansar.unit_name_bng]]</td>
                                </tr>
                            </table>
                        </div>
                            <div>
                                <p>NB: Date - Embodiment Date(For Embodiment), Joining Date(Transfer), Freeze Date (Freeze)</p>
                            </div>
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
                                    <tr ng-show="ansars.length==0">
                                        <td colspan="7" class="warning">
                                            No Data is available to show
                                        </td>
                                    </tr>
                                    <tbody ng-if="errorFound==1" ng-bind-html="ansars"></tbody>
                                    <tr ng-repeat="a in ansars" ng-show="ansars.length>0">
                                        
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
        </section>       
    </div>
@stop