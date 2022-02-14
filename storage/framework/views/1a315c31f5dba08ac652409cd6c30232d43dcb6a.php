<?php /*User: Shreya*/ ?>
<?php /*Date: 3/16/2016*/ ?>
<?php /*Time: 4:16 PM*/ ?>


<?php $__env->startSection('title','Embodiment Memorandum ID Correction'); ?>
<?php $__env->startSection('breadcrumb'); ?>
    <?php echo Breadcrumbs::render('embodiment_memorandum_id_correction_view'); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>

    <script>
        $(document).ready(function () {
            $('#new_disembodiment_date').datepicker({
                dateFormat:'dd-M-yy'
            });
        })
        GlobalApp.controller('MemorandumIDCorrectionController', function ($scope,$http,$sce) {
            $scope.ansarId = "";
            $scope.ansarDetail = {};
            $scope.ansar_ids = [];
            $scope.totalLength =  0;
            $scope.loadingAnsar = false;

            $scope.loadAnsarDetail = function (id) {
                $scope.loadingAnsar = true;
                $http({
                    method:'get',
                    url:'<?php echo e(URL::route('load_ansar_for_embodiment_memorandum_id_correction')); ?>',
                    params:{ansar_id:id}
                }).then(function (response) {
                    $scope.ansarDetail = response.data
                    $scope.loadingAnsar = false;
                    $scope.totalLength--;
                })
            }

            $scope.verifyMemorandumId = function () {
                var data = {
                    memorandum_id: $scope.memorandumId
                }
                $scope.isVerified = false;
                $scope.isVerifying = true;
                $http.post('<?php echo e(action('UserController@verifyMemorandumId')); ?>', data).then(function (response) {
//                    alert(response.data.status)
                    $scope.isVerified = response.data.status;
                    $scope.isVerifying = false;
                }, function (response) {

                })
            }

            $scope.makeQueue = function (id) {
                $scope.ansar_ids.push(id);
                $scope.totalLength +=  1;
            }
            $scope.$watch('totalLength', function (n,o) {
                if(!$scope.loadingAnsar&&n>0){
                    $scope.loadAnsarDetail($scope.ansar_ids.shift())
                }
                else{
                    if(!$scope.ansarId)$scope.ansarDetail={}
                }
            })
        })
    </script>

    <div ng-controller="MemorandumIDCorrectionController">
        <?php /*<div class="breadcrumbplace">*/ ?>
            <?php /*<?php echo Breadcrumbs::render('disembodiment_date_correction'); ?>*/ ?>
        <?php /*</div>*/ ?>
        <?php if(Session::has('success_message')): ?>
            <div style="padding: 10px 20px 0 20px;">
                <div class="alert alert-success">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <span class="glyphicon glyphicon-ok"></span> <?php echo e(Session::get('success_message')); ?>

                </div>
            </div>
        <?php endif; ?>
        <section class="content" style="position: relative;" >
            <notify></notify>
            <div class="box box-solid">
                <?php echo Form::open(array('route' => 'new_embodiment_memorandum_id_update', 'id' => 'new-embodiment_memorandum_id-entry')); ?>

                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="ansar_id" class="control-label">Ansar ID (Comes from Embodiment)</label>
                                <input type="text" name="ansar_id" id="ansar_id" class="form-control" placeholder="Enter Ansar ID" ng-model="ansarId" ng-change="makeQueue(ansarId)">
                            </div>
                            <div class="form-group">
                                <label class="control-label">Memorandum no.&nbsp;&nbsp;&nbsp;<span
                                            ng-show="isVerifying"><i
                                                class="fa fa-spinner fa-pulse"></i>&nbsp;Verifying</span><span
                                            class="text-danger"
                                            ng-if="isVerified&&!memorandumId">Memorandum ID is required.</span><span
                                            class="text-danger"
                                            ng-if="isVerified&&memorandumId">This id already taken.</span></label>
                                <input ng-blur="verifyMemorandumId()" ng-model="memorandumId"
                                       type="text" class="form-control" name="memorandum_id"
                                       placeholder="Enter Memorandum no." required>
                                <?php /*<label for="black_date" class="control-label">New Dis-Embodiment Date</label>*/ ?>
                                <?php /*<input type="text" name="new_disembodiment_date" id="new_disembodiment_date" class="form-control" ng-model="new_disembodiment_date">*/ ?>
                            </div>
                            <button id="confirm-new-memorandum-id" class="btn btn-primary" ng-disabled="!ansarDetail.name||!memorandumId||isVerified||isVerifying||!ansarId"><img ng-show="loadingSubmit" src="<?php echo e(asset('dist/img/facebook-white.gif')); ?>" width="16" style="margin-top: -2px">Correct Memorandum ID</button>
                        </div>
                        <div class="col-sm-6 col-sm-offset-2" style="min-height: 400px;border-left: 1px solid #CCCCCC">
                            <div id="loading-box" ng-if="loadingAnsar">
                            </div>
                            <div ng-if="ansarDetail.name==undefined">
                                <h3 style="text-align: center">No Ansar Found</h3>
                            </div>
                            <div ng-if="ansarDetail.name!=undefined">
                                <div class="form-group">
                                    <label class="control-label">Name</label>
                                    <p>
                                        [[ansarDetail.name]]
                                    </p>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Rank</label>
                                    <p>
                                        [[ansarDetail.rank]]
                                    </p>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Sex</label>
                                    <p>
                                        [[ansarDetail.sex]]
                                    </p>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">KPI Name</label>
                                    <p>
                                        [[ansarDetail.kpi]]
                                    </p>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">KPI Unit</label>
                                    <p>
                                        [[ansarDetail.unit]]
                                    </p>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">KPI Thana</label>
                                    <p>
                                        [[ansarDetail.thana]]
                                    </p>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Embodiment Memorandum ID</label>
                                    <p>
                                        [[ansarDetail.m_id]]
                                    </p>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Reporting Date</label>
                                    <p>
                                        [[ansarDetail.r_date]]
                                    </p>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Embodiment Date</label>
                                    <p>
                                        [[ansarDetail.j_date]]
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php echo Form::close(); ?>

            </div>
        </section>
    </div>
    <script>

        $("#confirm-new-memorandum-id").confirmDialog({
            message:'Are you sure to Correct the Memorandum ID',
            ok_button_text:'Confirm',
            cancel_button_text:'Cancel',
            ok_callback: function (element) {
                $("#new-embodiment_memorandum_id-entry").submit()
            },
            cancel_callback: function (element) {
            }
        })
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('template.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>