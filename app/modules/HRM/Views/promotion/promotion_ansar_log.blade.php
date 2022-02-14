@extends('template.master')
@section('title','Promotion Ansar Log')
@section('breadcrumb')
    
@endsection
@section('content')
    <script>
        GlobalApp.controller('PromotionController', function ($scope, $http, $sce, $parse, notificationService) {
            
            $scope.rank = 'all';
            $scope.queue = [];
            $scope.addAnsarBtn = false;
            $scope.exportPage = '';
            
            $scope.defaultPage = {pageNum: 0, offset: 0, limit: $scope.itemPerPage, view: 'view'};
            $scope.total = 0;
            $scope.param = {};
            $scope.numOfPage = 0;
            $scope.itemPerPage = parseInt("{{config('app.item_per_page')}}");
            $scope.currentPage = 0;
            $scope.ansarLog = [];
            $scope.ansar_id = [];
            $scope.pages = [];
            $scope.loadingPage = [];
            $scope.allLoading = true;
            $scope.showLoadScreen1 = true;
            $scope.orderBy = "";
            //$scope.from_date = moment().subtract(1, 'years').format("D-MMM-YYYY");
            //$scope.to_date = moment().format("D-MMM-YYYY");
            $scope.from_date = '';
            $scope.to_date = '';
            $scope.isDisabled = false;
            
            $scope.loadPagination = function () {
                
                $scope.pages = [];
                for (var i = 0; i < $scope.numOfPage; i++) {
                    $scope.pages.push({
                        pageNum: i,
                        offset: i * $scope.itemPerPage,
                        limit: $scope.itemPerPage
                    });
                    $scope.loadingPage[i] = false;
                }
            };
            $scope.loadPage = function (page, $event) {

                if ($event != undefined) $event.preventDefault();
                $scope.exportPage = page;
                $scope.currentPage = page == undefined ? 0 : page.pageNum;
                $scope.loadingPage[$scope.currentPage] = true;
                $scope.allLoading = true;
                $http({
                    url: '{{URL::to('HRM/get_promotion_log')}}',
                    method: 'get',
                    params: {
                        offset: page == undefined ? 0 : page.offset,
                        limit: page == undefined ? $scope.itemPerPage : page.limit,
                        unit: $scope.param.unit == undefined ? 'all' : $scope.param.unit,
                        division: $scope.param.range == undefined ? 'all' : $scope.param.range,
                        gender: $scope.param.gender == undefined ? 'all' : $scope.param.gender,
                        q: $scope.q,
                        rank: $scope.rank,
                        sortBy: $scope.orderBy,
                    }
                }).then(function (response) {					
                    $scope.queue.shift();
                    if ($scope.queue.length > 1) $scope.loadPage();
                    $scope.ansarLog = response.data.ansarLog;
                    console.log($scope.ansarLog);
                   
                    $scope.loadingPage[$scope.currentPage] = false;
                    $scope.allLoading = false;
                    $scope.total = sum(response.data.total);
                    $scope.gCount = response.data.total;
                    $scope.numOfPage = Math.ceil($scope.total / $scope.itemPerPage);
                    $scope.loadPagination();
                })
            };
            $scope.exportData = function (type) {
                var page = $scope.exportPage;
                if (type == 'page') $scope.export_page = true;
                else $scope.export_all = true;
                $http({
                    url: '{{URL::to('HRM/get_available_ansar_list')}}',
                    method: 'get',
                    params: {
                        type: $scope.ansarType,
                        offset: type == 'all' ? -1 : (page == undefined ? 0 : page.offset),
                        limit: type == 'all' ? -1 : (page == undefined ? $scope.itemPerPage : page.limit),
                        unit: $scope.param.unit == undefined ? 'all' : $scope.param.unit,
                        thana: $scope.param.thana == undefined ? 'all' : $scope.param.thana,
                        division: $scope.param.range == undefined ? 'all' : $scope.param.range,
                        gender: $scope.param.gender == undefined ? 'all' : $scope.param.gender,
                        filter_mobile_no: $scope.param.filter_mobile_no == undefined ? 0 : $scope.param.filter_mobile_no,
                        filter_age: $scope.param.filter_age == undefined ? 0 : $scope.param.filter_age,
                        q: $scope.q,
                        rank: $scope.rank,
                        export: type,
                        from_date: $scope.from_date,
                        to_date: $scope.to_date
                    }
                }).then(function (res) {
					//console.log(res);
                    $scope.export_data = res.data;
                    $scope.generating = true;
                    generateReport();
                    $scope.export_page = $scope.export_all = false;
                }, function (res) {
                    $scope.export_page = $scope.export_all = false;
                })
            };
            $scope.file_count = 1;

            function generateReport() {
                $http({
                    url: '{{URL::to('HRM/generate/file')}}/' + $scope.export_data.id,
                    method: 'post'
                }).then(function (res) {
                    if ($scope.export_data.total_file > $scope.file_count) {
                        setTimeout(generateReport, 1000);
                        if (res.data.status) $scope.file_count++;
                    } else {
                        $scope.generating = false;
                        $scope.file_count = 1;
                        window.open($scope.export_data.download_url, '_blank')
                    }
                }, function (res) {
                    if ($scope.export_data.file_count > $scope.file_count) {
                        setTimeout(generateReport, 1000)
                    }
                })
            }

            $scope.search = function () {
            };
            $scope.filterMiddlePage = function (value, index, array) {
                var minPage = $scope.currentPage - 3 < 0 ? 0 : ($scope.currentPage > array.length - 4 ? array.length - 8 : $scope.currentPage - 3);
                var maxPage = minPage + 7;
                if (value.pageNum >= minPage && value.pageNum <= maxPage) {
                    return true;
                }
            };
            $scope.changeRank = function (i) {
                $scope.rank = i;
                $scope.loadPage()
            };

            $scope.modal = function (data) {
                 console.log(data);
                //  alert($scope.comment );
                $scope.printLetter = false;
                $scope.getSingleRow = data;
                
            }

            function capitalizeLetter(s) {
                return s.charAt(0).toUpperCase() + s.slice(1);
            }

            function sum(t) {
                var s = 0;
                for (var i in t) {
                    s += parseInt(t[i])
                }
                return s;
            }
        });
        $(function () {
            $("#print-report").on('click', function (e) {
                $("#print-area").remove();
                $("#print_table table").removeClass('table table-bordered');
                $('body').append('<div id="print-area">' + $("#print_table").html() + '</div>');
                window.print();
                $("#print_table table").addClass('table table-bordered');
                $("#print-area").remove()
            })
        })
    </script>
    <div ng-controller="PromotionController">
        <section class="content">
            <div>
                <div class="box box-solid">
                    <div class="overlay" ng-if="allLoading">
                    <span class="fa">
                        <i class="fa fa-refresh fa-spin"></i> <b>Loading...</b>
                    </span>
                    </div>
                    <div class="box-body">
                        <div class="box-body" id="change-body">
                            <filter-template
                                    show-item="['range','unit','thana','rank','gender']"
                                    type="all"
                                    range-change="loadPage()"
                                    unit-change="loadPage()"
                                    thana-change="loadPage()"
                                    kpi-change="loadPage()"
                                    rank-change="loadPage()"
                                    gender-change="loadPage()"
                                    on-load="loadPage()"
                                    start-load="range"
                                    field-width="{range:'col-sm-2',unit:'col-sm-2',thana:'col-sm-2',kpi:'col-sm-2',rank:'col-sm-2',gender:'col-sm-2'}"
                                    data="params"
                            ></filter-template>
                            <div class="loading-data"><i class="fa fa-4x fa-refresh fa-spin loading-icon"></i>
                            </div>
                            <div class="table-responsive">
                                <table class="table  table-bordered table-striped" id="ansar-table">
                                    <caption>
                                        <div class="row">
                                            <div class="col-sm-8">
                                                <span class="text text-bold" style="color:#000000;font-size: 1.1em">Total : [[response?response.total:0]]</span>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="input-group">
                                                    <input type="text" class="form-control" ng-model="params.q"
                                                           placeholder="Search here by ansar id">
                                                    <span class="input-group-btn">
                                                        <button class="btn btn-primary" type="button"
                                                                ng-click="loadPage()">
                                                            <i class="fa fa-search"></i>
                                                        </button>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- <a class="btn btn-primary btn-xs"
                                           href="{{URL::route('loadPage',['export'=>1])}}">
                                            <i class="fa fa-file-excel-o"></i> Export
                                        </a> --}}
                                    </caption>
                                    <tr>
                                        
                                        <th class="text-center">SL</th>
                                        <th class="text-center">Ansar ID</th>
                                        <th class="text-center">Ansar Name</th>
                                        <th class="text-center">Rank</th>
                                        
                                    </tr>
                                    <tr ng-show="ansarLog.length>0"
                                        ng-repeat="promotionAnsarLog in ansarLog">
                                        <td>
                                            <input type="checkbox" ng-true-value="[[$index]]" ng-false-value="false"
                                                   ng-model="checked[$index]">
                                        </td>
                                        <td>[[(response.current_page-1)*response.per_page+$index+1]]</td>
                                        <td>[[promotionAnsarLog.id]]</td>
                                        <td>[[promotionAnsarLog.name]]</td>
                                        <td>[[promotionAnsarLog.rank]]</td>
                                        {{-- <td>[[promotionAnsar.reporting_date|dateformat:'DD-MMM-YYYY']]</td>
                                        <td>[[promotionAnsar.freez_date|dateformat:'DD-MMM-YYYY']]</td> --}}
                                        
                                       
                                    </tr>
                                    <tr ng-show="ansarLog.length==0">
                                        <td class="warning" colspan="11">No information found</td>
                                    </tr>
                                </table>
                            </div>
                            
                            <div class="row" ng-if="response.total>response.per_page">
                                <div class="col-sm-3">
                                    <div class="form-group" ng-init="params.limit = '50'">
                                        <label for="" class="control-label">Load limit</label>
                                        <select class="form-control" ng-model="params.limit"
                                                ng-change="loadPage()">
                                            <option value="50">50</option>
                                            <option value="100">100</option>
                                            <option value="150">150</option>
                                            <option value="200">200</option>
                                            <option value="300">300</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-9">
                                    <div ng-bind-html="view" compile-g-html></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        
    </div>
@stop