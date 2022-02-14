<?php $__env->startSection('title','Embodiment Date Correction'); ?>
<?php $__env->startSection('breadcrumb'); ?>
    <?php echo Breadcrumbs::render('embodiment_date_correction'); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <script>
        GlobalApp.controller('DisembodimentDateCorrectionController', function ($scope, $http, $sce) {
            $scope.ansarId = "";
            $scope.ansarDetail = {};
            $scope.ansar_ids = [];
            $scope.totalLength = 0;
            $scope.loadingAnsar = false;
            $scope.loadAnsarDetail = function (id) {
                $scope.loadingAnsar = true;
                $http({
                    method: 'get',
                    url: '<?php echo e(URL::route('load_ansar_for_embodiment_date_correction')); ?>',
                    params: {ansar_id: id}
                }).then(function (response) {
                    $scope.ansarDetail = response.data;
                    $scope.loadingAnsar = false;
                    $scope.totalLength--;
                })
            };
            $scope.makeQueue = function (id) {
                $scope.ansar_ids.push(id);
                $scope.totalLength += 1;
            };
            $scope.$watch('totalLength', function (n, o) {
                if (!$scope.loadingAnsar && n > 0) {
                    $scope.loadAnsarDetail($scope.ansar_ids.shift())
                } else {
                    if (!$scope.ansarId) $scope.ansarDetail = {}
                }
            })
        })
    </script>

    <div ng-controller="DisembodimentDateCorrectionController">
        <?php if(Session::has('success_message')): ?>
            <div style="padding: 10px 20px 0 20px;">
                <div class="alert alert-success">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <span class="glyphicon glyphicon-ok"></span> <?php echo e(Session::get('success_message')); ?>

                </div>
            </div>
        <?php endif; ?>
        <?php if(Session::has('error_message')): ?>
            <div style="padding: 10px 20px 0 20px;">
                <div class="alert alert-danger">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <span class="fa fa-remove"></span> <?php echo e(Session::get('error_message')); ?>

                </div>
            </div>
        <?php endif; ?>
        <section class="content" style="position: relative;">
            <notify></notify>
            <div class="box box-solid">
                <?php echo Form::open(array('route' => 'new-embodiment-date-entry', 'id' => 'new-embodiment-date-entry')); ?>

                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group required" ng-init="ansarId='<?php echo e(Request::old('ansar_id')); ?>'">
                                <label for="ansar_id" class="control-label">Ansar ID (Embodied Ansar)</label>
                                <input type="text" name="ansar_id" value="<?php echo e(Request::old('ansar_id')); ?>" id="ansar_id"
                                       class="form-control" placeholder="Enter Ansar ID" ng-model="ansarId"
                                       ng-change="makeQueue(ansarId)">
                                <?php if($errors->has('ansar_id')): ?>
                                    <p class="text-danger"><?php echo e($errors->first('ansar_id')); ?></p>
                                <?php endif; ?>
                            </div>
                            <div class="form-group required"
                                 ng-init="new_embodiment_date='<?php echo e(Request::old('new_embodiment_date')); ?>'">
                                <datepicker-separate-fields label="New Embodiment Date" notify="emboInvalidDate"
                                                            rdata="new_embodiment_date"></datepicker-separate-fields>
                                <input type="hidden" name="new_embodiment_date" ng-value="new_embodiment_date">
                                <?php if($errors->has('new_embodiment_date')): ?>
                                    <p class="text-danger"><?php echo e($errors->first('new_embodiment_date')); ?></p>
                                <?php endif; ?>
                            </div>
                            <button id="confirm-new-embodiment-date" class="btn btn-primary"
                                    ng-disabled="emboInvalidDate">
                                <img ng-show="loadingSubmit" src="<?php echo e(asset('dist/img/facebook-white.gif')); ?>"
                                     width="16" style="margin-top: -2px">Correct Date
                            </button>
                        </div>
                        <div class="col-sm-6 col-sm-offset-2" style="min-height: 400px;border-left: 1px solid #CCCCCC">
                            <div id="loading-box" ng-if="loadingAnsar"></div>
                            <div ng-if="ansarDetail.name==undefined">
                                <input type="hidden" name="ansarExist" value="0">
                                <h3 style="text-align: center">No Ansar Found</h3>
                            </div>
                            <div ng-if="ansarDetail.name!=undefined">
                                <input type="hidden" name="ansarExist" value="1">
                                <div class="form-group">
                                    <label class="control-label">Name</label>
                                    <p>[[ansarDetail.name]]</p>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Rank</label>
                                    <p>[[ansarDetail.rank]]</p>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Sex</label>
                                    <p>[[ansarDetail.sex]]</p>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Date of Birth</label>
                                    <p>[[ansarDetail.dob]]</p>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Own Unit</label>
                                    <p>[[ansarDetail.unit]]</p>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Own Thana</label>
                                    <p>[[ansarDetail.thana]]</p>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">KPI Name</label>
                                    <p>[[ansarDetail.kpi_name]]</p>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Embodiment Date</label>
                                    <p>[[ansarDetail.joining_date]]</p>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Dis Embodiment Date</label>
                                    <p>[[ansarDetail.service_ended_date]]</p>
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
        $("#confirm-new-disembodiment-date").confirmDialog({
            message: 'Are you sure to Correct the Dis-Embodiment Date',
            ok_button_text: 'Confirm',
            cancel_button_text: 'Cancel',
            ok_callback: function (element) {
                $("#new-disembodiment-date-entry").submit()
            },
            cancel_callback: function (element) {
            }
        })
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('template.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>