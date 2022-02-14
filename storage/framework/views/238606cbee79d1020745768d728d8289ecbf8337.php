<?php $__env->startSection('title','Freeze Letter'); ?>
<?php $__env->startSection('breadcrumb'); ?>
    <?php echo Breadcrumbs::render('freeze_letter_view'); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <script>
        GlobalApp.controller('FreezeLetterController', function ($scope, $http, $sce,$rootScope,httpService) {
            $scope.letterView = $sce.trustAsHtml("&nbsp;")
            $scope.printType = "smartCardNo"
            $scope.unit = {
                selectedUnit: []
            };
            $scope.units = [];
            $scope.allLoading = false;
            $scope.q = '';
            $scope.isDc = $rootScope.user.type==22 ? true : false;
            if ($scope.isDc) {
                $scope.unit.selectedUnit = $rootScope.user.district_id
            }
            else {
                httpService.unit().then(function (response) {
                    $scope.units = response.data;
                }, function (response) {

                })
            }
            $scope.loadData = function (url,q) {
                $scope.allLoading = true;
                $http({
                    method: 'get',
                    params: {type: "FREEZE",q:q},
                    url: url==undefined?'<?php echo e(URL::route('letter_data')); ?>':url
                }).then(function (response) {
                    if (!$scope.isDc) $scope.unit.selectedUnit = [];
                    $scope.letterView = $sce.trustAsHtml(response.data);
                    $scope.allLoading = false;
                },function (res) {
                    $scope.allLoading = false;
                })
            }
        })
        GlobalApp.directive('compileHtml',function ($compile) {
            return {
                restrict:'A',
                link:function (scope,elem,attr) {
                    scope.$watch('letterView',function(n){

                        if(attr.ngBindHtml) {
                            $compile(elem[0].children)(scope)
                        }
                    })

                }
            }
        })
    </script>
    <div ng-controller="FreezeLetterController" ng-init="loadData()">
        <section class="content">

            <div class="box box-solid">
                <div class="overlay" ng-if="allLoading">
                    <span class="fa">
                        <i class="fa fa-refresh fa-spin"></i> <b>Loading...</b>
                    </span>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-3 col-xs-6">
                            <div class="form-group">
                                <input type="radio" ng-model="printType" value="smartCardNo">
                                <span class="text text-bold" style="vertical-align: top">Print by Smart card no.</span>
                            </div>
                        </div>
                        <div class="col-sm-3 col-xs-6">
                            <div class="form-group">
                                <input type="radio" ng-model="printType" value="memorandumNo">
                                <span class="text text-bold" style="vertical-align: top">Print by Memorandum no.</span>
                            </div>
                        </div>
                    </div>
                    <div ng-if="printType=='smartCardNo'">
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="">Smart Card no.</label>
                                    <input type="text" ng-model="smartCardNo" placeholder="Enter Smart Card no." class="form-control">
                                </div>
                                <div class="form-group">
                                    <filter-template
                                            show-item="['unit']"
                                            type="single"
                                            data="unit.param"
                                            start-load="unit"
                                            layout-vertical="1"
                                    >
                                    </filter-template>
                                </div>
                                <div class="form-group">
                                    <?php echo Form::open(['route'=>'print_letter','target'=>'_blank']); ?>

                                    <?php echo Form::hidden('option','smartCardNo'); ?>

                                    <?php echo Form::hidden('id','[[smartCardNo]]'); ?>

                                    <?php echo Form::hidden('type','FREEZE'); ?>

                                    <?php if(auth()->user()->type!=22): ?>
                                        <?php echo Form::hidden('unit','[[unit.param.unit ]]'); ?>

                                    <?php else: ?>
                                        <?php echo Form::hidden('unit',auth()->user()->district?auth()->user()->district->id:''); ?>

                                    <?php endif; ?>
                                    <button class="btn btn-primary">Generate Freeze Letter</button>
                                    <?php echo Form::close(); ?>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div ng-if="printType=='memorandumNo'">
                        <div id="letter_data_view" ng-bind-html="letterView" compile-html></div>
                    </div>
                </div>
            </div>
        </section>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('template.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>