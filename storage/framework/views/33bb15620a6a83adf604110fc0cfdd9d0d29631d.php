<?php $__env->startSection('title','Verify Entry (Chunk)'); ?>
<?php /*<?php $__env->startSection('small_title','Chunk verification'); ?>*/ ?>
<?php $__env->startSection('breadcrumb'); ?>
    <?php echo Breadcrumbs::render('chunk_verification'); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <script>
        GlobalApp.controller('ChunkVerificationController', function ($scope, $http, $interval) {
            $scope.showAnsar = '10';
            $scope.ansars = []
            $scope.params = {}
            $scope.selectAll = false
            $scope.messages = [];
            $scope.selected = [];
            $scope.loadAnsar = function () {
                $scope.loading = true;
                $scope.savingPanel = false;
                $scope.error = undefined;
                $http({
                    method: 'get',
                    url: '<?php echo e(URL::to('HRM/getnotverifiedansar')); ?>',
                    params: {
                        chunk: 'chunk',
                        limit: $scope.showAnsar,
                        offset: 0,
                        division: $scope.params.range,
                        unit: $scope.params.unit,
                        thana: $scope.params.thana,
                        from_ansar:$scope.params.from_ansar,
                        to_ansar:$scope.params.to_ansar,
                    }
                }).then(function (response) {
                    $scope.loading = false;
                    $scope.ansars = response.data
                    $scope.selected = Array.apply(null, new Array($scope.ansars.length)).map(Boolean.prototype.valueOf, false)
                    var d = response.data;
                    var c = Math.ceil(d.length / 100);
                    var i = 0;

                    $scope.selected = Array.apply(null, new Array(d.length)).map(Boolean.prototype.valueOf, false)
                    $scope.selectAll = false
                }, function (response) {
                    $scope.error = response.data;
                    $scope.loading = false;
                })
            }
            $scope.$watch('selected', function (n, o) {
                if (n.length == 0) return;
                var t = 0, f = 0;
                $scope.selectAll = n.every(function (value, index) {
                    return value;
                })
            }, true)
            $scope.changeSelectedAll = function () {
                $scope.selected = Array.apply(null, new Array($scope.ansars.length)).map(Boolean.prototype.valueOf, $scope.selectAll)
            }
            $scope.addToPanel = function () {
                $("#panel-modal").modal('show')
            }
            $scope.saveToPanel = function () {

            }
        })
        GlobalApp.directive('formSubmit', function () {
            return {
                restrict: 'AC',
                link: function (scope, elem, attr) {

                    $(elem).on('click', function (e) {
                        e.preventDefault();
                        scope.loading = true;
                        scope.errorVerify = undefined;
                        $("#not-verified-form").ajaxSubmit({
                            success: function (response) {
                                console.log(response)
                                if (response.status) {
                                    scope.loadAnsar();
                                    scope.messages = response.messege;
                                }
                                else {
                                    $('body').notifyDialog({type: 'error', message: response.message}).showDialog()
                                }
                            },
                            error: function (response) {
                                scope.errorVerify = response;
                                scope.loading = false;
                            }
                        })
                    })
                }
            }
        })
        <?php /*GlobalApp.directive('savePanel', function (notificationService,$timeout) {*/ ?>
            <?php /*return {*/ ?>
                <?php /*restrict: 'AC',*/ ?>
                <?php /*link: function (scope, elem, attr) {*/ ?>

                    <?php /*$(elem).on('click', function (e) {*/ ?>
                        <?php /*e.preventDefault();*/ ?>
                        <?php /*scope.savingPanel = true;*/ ?>
                        <?php /*$timeout(function () {*/ ?>
                            <?php /*scope.$apply();*/ ?>
                        <?php /*})*/ ?>
                        <?php /*var data = {};*/ ?>
                        <?php /*data['ansar_id'] = $("input[name='not_verified[]']:checked").map(function () {*/ ?>
                            <?php /*return $(this).val()*/ ?>
                        <?php /*}).get();*/ ?>
                        <?php /*data['merit'] = Array.apply(null, new Array(data['ansar_id'].length)).map(function () {*/ ?>
                            <?php /*return 1;*/ ?>
                        <?php /*})*/ ?>
                        <?php /*data['memorandumId'] = $("input[name='memorandumId']").val();*/ ?>
                        <?php /*data['panel_date'] = $("input[name='panel_date']").val();*/ ?>
<?php /*//                        console.log(data);*/ ?>
                        <?php /*$.ajax({*/ ?>
                            <?php /*url: '<?php echo e(URL::route('save-panel-entry')); ?>',*/ ?>
                            <?php /*data: data,*/ ?>
                            <?php /*method: 'post',*/ ?>
                            <?php /*success: function (response) {*/ ?>

                                <?php /*scope.savingPanel = false;*/ ?>
                                <?php /*if (response.status) {*/ ?>
                                    <?php /*scope.loadAnsar();*/ ?>
                                    <?php /*notificationService.notify('success', response.message);*/ ?>
                                    <?php /*$("input[name='memorandumId']").val('');*/ ?>
                                    <?php /*$("input[name='panel_date']").val('');*/ ?>
                                    <?php /*$("#panel-modal").modal('hide');*/ ?>

                                <?php /*}*/ ?>
                                <?php /*else {*/ ?>
                                    <?php /*notificationService.notify('error', response.message);*/ ?>
                                <?php /*}*/ ?>
                            <?php /*},*/ ?>
                            <?php /*error: function (response) {*/ ?>
                                <?php /*console.log(response)*/ ?>
                                <?php /*scope.savingPanel = false;*/ ?>
                            <?php /*}*/ ?>
                        <?php /*})*/ ?>

                    <?php /*})*/ ?>
                <?php /*}*/ ?>
            <?php /*}*/ ?>
        <?php /*})*/ ?>
        $(document).ready(function (e) {
            $("#button-top").on('click', function (e) {
                $('html,body').animate({scrollTop: 0}, 'slow')
            })
            var t = $('#ppp').offset().top
            var l = $('#ppp').offset().left
            console.log({top: l})
            $(document).scroll(function (e) {
                if (t - $(document).scrollTop() <= 0) {
                    $("#button-top").css('display', 'block')
                }
                else {
                    $("#button-top").css('display', 'none')
                }
            })
        })
    </script>
    <div ng-controller="ChunkVerificationController">
        <?php /*<div class="breadcrumbplace">*/ ?>
        <?php /*<?php echo Breadcrumbs::render('chunk_verification'); ?>*/ ?>
        <?php /*</div>*/ ?>
        <button id="button-top" class="btn btn-primary"
                style="position: fixed;bottom: 10px;right: 20px;z-index: 1000000000000000;display: none">
            <i class="fa fa-arrow-up fa-2x"></i>
        </button>

        <section class="content">
            <div class="box box-solid">
                <div class="overlay" ng-if="loading">
                    <span class="fa">
                        <i class="fa fa-refresh fa-spin"></i> <b>Loading...</b>
                    </span>
                </div>
                <div style="padding: 5px 10px">
                    <p ng-repeat="message in messages" class="text"
                       ng-class="{'text-success':message.status,'text-danger':!message.status}"><i class="fa"
                                                                                                   ng-class="{'fa-check':message.status,'fa-warning':!message.status}"></i>&nbsp;[[message.message]]
                    </p>
                </div>
                <div class="box-body">
                    <div id="ppp" style="margin-right: 0" class="row margin-bottom">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Show Ansar :</label>

                                <div class="col-sm-9">
                                    <select class="form-control" ng-model="showAnsar" ng-change="loadAnsar()">
                                        <option value="10">10</option>
                                        <option value="20">20</option>
                                        <option value="30">30</option>
                                        <option value="40">40</option>
                                        <option value="50">50</option>
                                        <option value="60">60</option>
                                        <option value="70">70</option>
                                        <option value="80">90</option>
                                        <option value="90">90</option>
                                        <option value="100">100</option>
                                        <option value="300">300</option>
                                        <option value="500">500</option>
                                        <option value="1000">1000</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <button class="btn btn-primary pull-right" id="verify-ansar" form-submit>
                            <i class="fa fa-check"></i>&nbsp;Verify Ansar
                        </button>
                    </div>
                    <filter-template
                            show-item="['range','unit','thana']"
                            type="all"
                            range-change="loadAnsar()"
                            unit-change="loadAnsar()"
                            thana-change="loadAnsar()"
                            range-load="loadAnsar()"
                            start-load="range"
                            field-width="{range:'col-sm-4',unit:'col-sm-4',thana:'col-sm-4'}"
                            data="params"
                            on-load="loadAnsar()"
                    >

                    </filter-template>
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="">From Ansar ID</label>
                                <input type="text" ng-model="params.from_ansar" class="form-control" placeholder="Form Ansar ID">
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="">To Ansar ID</label>
                                <input type="text" ng-model="params.to_ansar" class="form-control" placeholder="To Ansar ID">
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label style="display: block">&nbsp;</label>
                                <button class="btn btn-primary" id="verify-ansar" ng-click="loadAnsar()">
                                    <i class="fa fa-check"></i>&nbsp;load Ansar
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <form id="not-verified-form" method="post" action="<?php echo e(URL::to('HRM/entryChunkVerify')); ?>">
                            <input type="hidden" name="chunk_verification" value="chunk_verification">
                            <table class="table table-bordered">
                                <caption>
                                    <table-search q="q" results="results"></table-search>
                                </caption>
                                <tr>
                                    <th>SL. No</th>
                                    <th>Ansar ID</th>
                                    <th>Ansar Name</th>
                                    <th>Ansar District</th>
                                    <th>Ansar Thana</th>
                                    <th>Rank</th>
                                    <th>Sex</th>
                                    <th><input type="checkbox" ng-model="selectAll"
                                               ng-change="changeSelectedAll()" value="all" name="select_all">
                                    </th>
                                    <th>
                                        Remark
                                    </th>
                                </tr>
                                <tr ng-if="ansars.length==0||error!=undefined">
                                    <td class="warning" colspan="8">No unverified ansar found</td>
                                </tr>
                                <tr ng-repeat="a in ansars|filter:q as results"
                                    ng-if="ansars.length>0&&error==undefined">
                                    <td>[[$index+1]]</td>
                                    <td><a href="<?php echo e(URL::to('HRM/entryreport')); ?>/[[a.ansar_id]]">[[a.ansar_id]]</a></td>
                                    <td>[[a.ansar_name_bng]]</td>
                                    <td>[[a.unit_name_bng]]</td>
                                    <td>[[a.thana_name_bng]]</td>
                                    <td>[[a.name_bng]]</td>
                                    <td>[[a.sex]]</td>
                                    <td><input type="checkbox" ng-model="selected[$index]" value="[[a.ansar_id]]"
                                               name="not_verified[]"></td>
                                    <td>
                                        <input type="hidden" value="[[a.ansar_id]]" name="[['unverified['+$index+'][ansar_id]']]" ng-if="!selected[$index]">
                                        <input type="text" placeholder="remark" name="[['unverified['+$index+'][remark]']]" ng-if="!selected[$index]">
                                    </td>
                                </tr>
                            </table>
                        </form>
                        <?php /*<?php if(UserPermission::userPermissionExists('save-panel-entry')): ?>*/ ?>
                            <?php /*<button class="btn btn-primary pull-right" ng-click="addToPanel()">Add To Panel</button>*/ ?>
                        <?php /*<?php endif; ?>*/ ?>
                    </div>
                </div>
            </div>
        </section>
        <?php /*<div id="panel-modal" class="modal fade" role="dialog">*/ ?>
            <?php /*<div class="modal-dialog modal-sm">*/ ?>
                <?php /*<div class="modal-content">*/ ?>
                    <?php /*<div class="modal-header">*/ ?>
                        <?php /*<button type="button" class="close" data-dismiss="modal" aria-label="Close">*/ ?>
                            <?php /*<span aria-hidden="true">&times;</span>*/ ?>
                        <?php /*</button>*/ ?>
                        <?php /*<h5 class="modal-title">Add To Panel</h5>*/ ?>


                    <?php /*</div>*/ ?>
                    <?php /*<div class="modal-body">*/ ?>
                        <?php /*<div class="form-group">*/ ?>
                            <?php /*<label>Memorandum ID</label>*/ ?>
                            <?php /*<input name="memorandumId" type="text" class="form-control" placeholder="Enter memorandum ID">*/ ?>
                        <?php /*</div>*/ ?>
                        <?php /*<div class="form-group">*/ ?>
                            <?php /*<label>Panel Date</label>*/ ?>
                            <?php /*<input type="text" name="panel_date" class="form-control" date-picker placeholder="Panel Date">*/ ?>
                        <?php /*</div>*/ ?>
                    <?php /*</div>*/ ?>
                    <?php /*<div class="modal-footer">*/ ?>
                        <?php /*<button ng-disabled="savingPanel" save-panel class="btn btn-primary pull-right"><i ng-if="savingPanel" class="fa fa-spinner fa-pulse"></i>Submit</button>*/ ?>
                    <?php /*</div>*/ ?>
                <?php /*</div>*/ ?>
            <?php /*</div>*/ ?>
        <?php /*</div>*/ ?>
    </div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('template.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>