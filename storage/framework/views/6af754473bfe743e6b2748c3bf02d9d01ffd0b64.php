<?php $__env->startSection('title','Three Years Over List'); ?>
<?php $__env->startSection('breadcrumb'); ?>
    <?php echo Breadcrumbs::render('three_year_over_report_view'); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <script>
        GlobalApp.controller('ReportThreeYearsOverList', function ($scope, $http, $sce) {
            $scope.total = 0;
            $scope.numOfPage = 0;
            $scope.itemPerPage = parseInt("<?php echo e(config('app.item_per_page')); ?>");
            $scope.currentPage = 0;
            $scope.ansars = $sce.trustAsHtml("");
            $scope.pages = [];
            $scope.allLoading = false;
            $scope.loadingPage = [];
            $scope.reportType = 'eng';
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
                $scope.currentPage = page == undefined ? 0 : page.pageNum;
                $scope.loadingPage[$scope.currentPage] = true;
                $scope.allLoading = true;
                $http({
                    url: '<?php echo e(URL::route('three_years_over_ansar_info')); ?>',
                    method: 'get',
                    params: {
                        offset: page == undefined ? 0 : page.offset,
                        limit: page == undefined ? $scope.itemPerPage : page.limit,
                        unit: $scope.params.unit,
                        division: $scope.params.range,
                        ansar_rank: $scope.params.rank,
                        ansar_sex: $scope.params.gender,
                        view: 'view'
                    }
                }).then(function (response) {
                    $scope.ansars = response.data;
                    $scope.gCount = response.data.total;
                    $scope.total = sum(response.data.total);
                    $scope.numOfPage = Math.ceil($scope.total / $scope.itemPerPage);
                    $scope.loadPagination();
                    $scope.loadingPage[$scope.currentPage] = false;
                    $scope.allLoading = false;
                })
            };
            $scope.exportData = function (type) {
                var page = $scope.exportPage;
                if (type == 'page') $scope.export_page = true;
                else $scope.export_all = true;
                $http({
                    url: '<?php echo e(URL::route('three_years_over_ansar_info')); ?>',
                    method: 'get',
                    params: {
                        offset: type == 'all' ? -1 : (page == undefined ? 0 : page.offset),
                        limit: type == 'all' ? -1 : (page == undefined ? $scope.itemPerPage : page.limit),
                        unit: $scope.params.unit,
                        division: $scope.params.range,
                        ansar_rank: $scope.params.rank,
                        ansar_sex: $scope.params.gender,
                        export: type
                    }
                }).then(function (res) {
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
                    url: '<?php echo e(URL::to('HRM/generate/file')); ?>/' + $scope.export_data.id,
                    method: 'post',
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

            $scope.filterMiddlePage = function (value, index, array) {
                var minPage = $scope.currentPage - 3 < 0 ? 0 : ($scope.currentPage > array.length - 4 ? array.length - 8 : $scope.currentPage - 3);
                var maxPage = minPage + 7;
                if (value.pageNum >= minPage && value.pageNum <= maxPage) {
                    return true;
                }
            };
            $scope.loadReportData = function (reportName, type) {
                $scope.allLoading = true;
                $http({
                    method: 'get',
                    url: '<?php echo e(URL::route('localize_report')); ?>',
                    params: {name: reportName, type: type}
                }).then(function (response) {
                    $scope.report = response.data;
                    $scope.allLoading = false;
                })
            };
            $scope.loadReportData("three_years_over_ansar_report", "eng");

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
                e.preventDefault();
                $("#print-area").remove();
                $('body').append('<div id="print-area">' + $("#print-three_years_over_ansar_report").html() + '</div>')
                window.print();
                $("#print-area").remove()
            })
        })
    </script>
    <div ng-controller="ReportThreeYearsOverList">
        <section class="content">
            <div class="box box-solid">
                <div class="overlay" ng-if="allLoading">
                    <span class="fa">
                        <i class="fa fa-refresh fa-spin"></i> <b>Loading...</b>
                    </span>
                </div>
                <div class="box-body">
                    <div class="pull-right">
                            <span class="control-label" style="padding: 5px 8px">
                                View report in&nbsp;&nbsp;&nbsp;<input type="radio" class="radio-inline"
                                                                       style="margin: 0 !important;" value="eng"
                                                                       ng-change="loadReportData('three_years_over_ansar_report',reportType)"
                                                                       ng-model="reportType">&nbsp;<b>English</b>
                                &nbsp;<input type="radio" class="radio-inline" style="margin: 0 !important;" value="bng"
                                             ng-model="reportType"
                                             ng-change="loadReportData('three_years_over_ansar_report',reportType)">&nbsp;<b>???????????????</b>
                            </span>
                    </div>
                    <br>
                    <filter-template
                            show-item="['range','unit','thana','rank','gender']"
                            type="all"
                            range-change="loadPage()"
                            unit-change="loadPage()"
                            rank-change="loadPage()"
                            gender-change="loadPage()"
                            start-load="range"
                            field-width="{range:'col-sm-3',unit:'col-sm-3',thana:'col-sm-2',rank:'col-sm-2',gender:'col-sm-2'}"
                            data="params"
                            on-load="loadPage()"
                    ></filter-template>
                    <div id="print-three_years_over_ansar_report">
                        <h3 id="report-header">[[report.ansar.ansar_title]]&nbsp;&nbsp;
                            <div class="btn-group btn-group-sm pull-right print-hide">
                                <button id="print-report" class="btn btn-default"><i
                                            class="fa fa-print"></i>&nbsp;Print
                                </button>
                                <button id="export-report" ng-disabled="export_page||export_all"
                                        ng-click="exportData('page')" class="btn btn-default ">
                                    <i ng-show="!export_page" class="fa fa-file-excel-o"></i>
                                    <i ng-show="export_page" class="fa fa-spinner fa-pulse"></i>&nbsp;Export this page
                                </button>
                                <button ng-disabled="export_page||export_all" ng-click="exportData('all')"
                                        id="export-report-all" class="btn btn-default">
                                    <i ng-show="!export_all" class="fa fa-file-excel-o"></i>
                                    <i ng-show="export_all" class="fa fa-spinner fa-pulse"></i>&nbsp;Export all
                                </button>
                            </div>
                        </h3>
                        <h4 class="text text-bold">Total Ansar([[total]]): PC([[gCount.PC==unefined?0:gCount.PC]]),
                            APC([[gCount.APC==unefined?0:gCount.APC]]),
                            Ansar([[gCount.ANSAR==unefined?0:gCount.ANSAR]])</h4>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tr>
                                    <th>[[report.ansar.sl_no]]</th>
                                    <th>[[report.ansar.id]]</th>
                                    <th>[[report.ansar.rank]]</th>
                                    <th>[[report.ansar.name]]</th>
                                    <th>[[report.ansar.district]]</th>
                                    <th>[[report.ansar.kpi_name]]</th>
                                    <th>[[report.ansar.kpi_unit]]</th>
                                    <th>[[report.ansar.joining_date]]</th>
                                </tr>
                                <tr ng-repeat="a in ansars.ansars">
                                    <td>[[ansars.index+$index]]</td>
                                    <td>[[a.id]]</td>
                                    <td>[[a.name]]</td>
                                    <td>[[a.rank]]</td>
                                    <td>[[a.unit]]</td>
                                    <td>[[a.kpi]]</td>
                                    <td>[[a.k_unit]]</td>
                                    <td>[[a.j_date|dateformat:'DD-MMM-YYYY']]</td>
                                </tr>
                                <tr>
                                    <td colspan="7" ng-if="ansars.ansars==undefined||ansars.ansars.length<=0"
                                        class="warning">No Ansar available
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="row print-hide">
                            <div class="col-sm-4">
                                <label for="item_par_page">Show :</label>
                                <select name="item_per_page" ng-change="loadPage()" id="item_par_page"
                                        ng-model="itemPerPage">
                                    <option value="20" ng-selected="true">20</option>
                                    <option value="40">40</option>
                                    <option value="60">60</option>
                                    <option value="80">80</option>
                                    <option value="100">100</option>
                                    <option value="150">150</option>
                                    <option value="200">200</option>
                                </select>
                            </div>
                            <div class="col-sm-8">
                                <div class="table_pagination" ng-if="pages.length>1">
                                    <ul class="pagination" style="margin: 0">
                                        <li ng-class="{disabled:currentPage == 0}">
                                            <a href="#" ng-click="loadPage(pages[0],$event)">&laquo;&laquo;</a>
                                        </li>
                                        <li ng-class="{disabled:currentPage == 0}">
                                            <a href="#" ng-click="loadPage(pages[currentPage-1],$event)">&laquo;</a>
                                        </li>
                                        <li ng-repeat="page in pages|filter:filterMiddlePage"
                                            ng-class="{active:page.pageNum==currentPage&&!loadingPage[page.pageNum],disabled:!loadingPage[page.pageNum]&&loadingPage[currentPage]}">
                                            <span ng-show="currentPage == page.pageNum&&!loadingPage[page.pageNum]">[[page.pageNum+1]]</span>
                                            <a href="#" ng-click="loadPage(page,$event)"
                                               ng-hide="currentPage == page.pageNum||loadingPage[page.pageNum]">[[page.pageNum+1]]</a>
                                            <span ng-show="loadingPage[page.pageNum]" style="position: relative">
                                                <i class="fa fa-spinner fa-pulse"
                                                   style="position: absolute;top:10px;left: 50%;margin-left: -9px"></i>[[page.pageNum+1]]</span>
                                        </li>
                                        <li ng-class="{disabled:currentPage==pages.length-1}">
                                            <a href="#" ng-click="loadPage(pages[currentPage+1],$event)">&raquo;</a>
                                        </li>
                                        <li ng-class="{disabled:currentPage==pages.length-1}">
                                            <a href="#"
                                               ng-click="loadPage(pages[pages.length-1],$event)">&raquo;&raquo;</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('template.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>