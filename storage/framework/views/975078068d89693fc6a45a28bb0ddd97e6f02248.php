<?php $__env->startSection('title','Offer Quota'); ?>
<?php $__env->startSection('breadcrumb'); ?>
    <?php echo Breadcrumbs::render('offer_quota'); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <script>
        GlobalApp.controller('QuotaController', function ($scope, $rootScope, $http) {
            $scope.quotas = [];
            $scope.param = {};
            $scope.loadQuota = function () {
                console.log($scope.param)
                $http({
                    url: '/HRM/get_offer_quota',
                    method: 'get',
                    params: {range: $scope.param.range}
                }).then(function (response) {

                    $scope.quotas  = response.data;
                },function (response) {

                })
            }
        })
    </script>
    <div ng-controller="QuotaController">
        <?php /*<div class="breadcrumbplace">*/ ?>
        <?php /*<?php echo Breadcrumbs::render('offer_quota'); ?>*/ ?>
        <?php /*</div>*/ ?>
        <section class="content">

            <div class="box box-solid">
                <div class="box-body">
                    <filter-template
                            show-item="['range']"
                            type="all"
                            range-change="loadQuota()"
                            on-load="loadQuota()"
                            data="param"
                            start-load="range"
                            field-width="{range:'col-sm-5'}"
                    ></filter-template>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">

                            <tr>
                                <th>SL. NO</th>
                                <th>Unit name</th>
                                <th>Total Offer quota</th>
                                <th>Used Offer quota</th>
                                <th>Offer quota Left</th>
                            </tr>
                            <tr ng-repeat="q in quotas">
                                <td>[[$index+1]]</td>
                                <td>[[q.unit_name_bng]]</td>
                                <td>[[q.total_quota]]</td>
                                <td>[[q.quota_used]]</td>
                                <td>[[q.total_quota-q.quota_used]]</td>
                            </tr>
                            <tr ng-if="quotas.length<=0">
                                <td class="text text-yellow" colspan="5">No data available</td>
                            </tr>

                        </table>

                    </div>
                    <?php /*<form id="offer-quota-form" action="<?php echo e(URL::route('update_offer_quota')); ?>" method="post">
                        <?php echo e(csrf_field()); ?>

                        <?php foreach($quota as $q): ?>
                        <div class="row margin-bottom-input form-group">
                            <div class="col-sm-4">
                                <label class="control-label"><?php echo e($q->unit_name_eng); ?></label>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <input type="hidden" name="quota_id[]" value="<?php echo e($q->unit); ?>">
                                    <input type="text"  class="form-control" name="quota_value[]"
                                           placeholder="Enter quota" value="<?php echo e($q->quota); ?>">
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <button id="update-quota"  type="submit" class="btn btn-primary">
                            <i id="ni" class="fa fa-save"></i></i>&nbsp; Save</button>
                    </form>*/ ?>
                </div>
            </div>
        </section>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('template.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>