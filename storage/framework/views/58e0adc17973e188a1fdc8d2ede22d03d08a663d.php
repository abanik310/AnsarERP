<?php $__env->startSection('title','User Action Log'); ?>
<?php /*<?php $__env->startSection('small_title','DG'); ?>*/ ?>
<?php $__env->startSection('breadcrumb'); ?>
    <?php echo Breadcrumbs::render('user_action_log'); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <script>
        GlobalApp.controller('UserActionLog', function ($scope, $http, $sce) {
            $scope.data = $sce.trustAsHtml("")
            $scope.formData = {};
            $scope.loading = false;
            $scope.loadLog = function () {
                $scope.loading = true;
                $http({
                    url:'<?php echo e(URL::route('user_action_log')); ?>',
                    data:angular.toJson($scope.formData),
                    method:'post'
                }).then(function (response) {
                    console.log(response.data)
                    $scope.data = $sce.trustAsHtml(response.data)
                    $scope.loading = false;
                }, function (response) {
                    $scope.loading = false;
                })
            }
        })
    </script>
    <section class="content">
        <div class="box box-solid" ng-controller="UserActionLog">
            <div class="box-body">
                <form>
                    <?php echo csrf_field(); ?>

                    <div class="row">
                        <div class="col-sm-4 col-centered">
                            <div class="form-group">
                                <label for="user_name">User Name</label>
                                <input type="text" ng-model="formData.user_name" class="form-control" name="user_name" placeholder="Enter User Name">
                            </div>
                        </div>
                        <div class="col-sm-2 col-centered">
                            <button class="btn btn-primary btn-block" ng-click="loadLog()" ng-disabled="loading"><i ng-if="loading" class="fa fa-spinner fa-pulse"></i>&nbsp;View Action Log</button>
                            <?php /*<div class="clearfix"></div>*/ ?>
                        </div>
                        <?php /*<div class="col-sm-4">*/ ?>
                            <?php /*<div class="form-group">*/ ?>
                                <?php /*<label for="user_name">To Date</label>*/ ?>
                                <?php /*<input type="text" ng-model="formData.to_date"   date-picker class="form-control" name="to_date"*/ ?>
                                       <?php /*placeholder="To Date">*/ ?>
                            <?php /*</div>*/ ?>
                        <?php /*</div>*/ ?>
                    </div>
                    <div>


                    </div>
                </form>
                <div style="padding-top: 20px">
                    <div ng-bind-html="data">

                    </div>
                </div>
            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('template.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>