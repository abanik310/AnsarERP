<?php $__env->startSection('title','Applicant Mark Rules'); ?>
<?php $__env->startSection('breadcrumb'); ?>
    <?php echo Breadcrumbs::render('recruitment.point.index'); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <script>
        GlobalApp.controller('circularPoint',function ($scope, httpService,$http,$sce) {
            $scope.jobCategories = [];
            $scope.jobCirculars = [];
            $scope.loadType = 'web';
            $scope.category_id = 'all'
            $scope.circular_id = 'all'
            $scope.pointsView = $sce.trustAsHtml('')
            $scope.loadJobCategories = function () {
                httpService.category({}).then(function (res) {
                    console.log(res)
                    $scope.jobCategories = res.data;
                })
            }
            $scope.loadJobCirculars = function () {
                httpService.circular({category_id:$scope.category_id}).then(function (res) {
                    $scope.circular_id = 'all'
                    $scope.jobCirculars = res.data;
                })
            }
            $scope.loadSearchData = function () {
                $http({
                    method:'get',
                    url:'<?php echo e(URL::route('recruitment.marks_rules.index')); ?>',
                    params:{circular_id:$scope.circular_id}
                }).then(function (res) {
                    $scope.loadType = 'ajax';
                    $scope.pointsView = $sce.trustAsHtml(res.data);
                },function (res) {

                })
            }
            $scope.loadJobCategories();
        })
    </script>
    <section class="content" ng-controller="circularPoint">
        <div class="box box-solid">
            <?php if(Session::has('session_error')): ?>
                <div class="alert alert-danger">
                    <i class="fa fa-warning"></i>&nbsp;<?php echo e(Session::get('session_error')); ?>

                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                </div>
            <?php elseif(Session::has('session_success')): ?>
                <div class="alert alert-success">
                    <i class="fa fa-check"></i>&nbsp;<?php echo e(Session::get('session_success')); ?>

                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                </div>
            <?php endif; ?>

            <div class="box-body">
                <div class="row">
                    <div class="col-sm-4">
                        <label class="control-label">Select Category</label>
                        <select class="form-control" ng-model="category_id" ng-change="loadJobCirculars()">
                            <option value="all">All</option>
                            <option ng-repeat="category in jobCategories" value="[[category.id]]">[[category.category_name_bng]]</option>
                        </select>
                    </div>
                    <div class="col-sm-4">
                        <label class="control-label">Select Circular</label>
                        <select class="form-control" ng-model="circular_id" ng-change="loadSearchData()">
                            <option value="all">All</option>
                            <option ng-repeat="circular in jobCirculars" value="[[circular.id]]">[[circular.circular_name]]</option>
                        </select>
                    </div>
                </div>
                <div class="table-responsive" ng-if="loadType=='web'">
                    <table class="table table-bordered table-condensed">
                        <caption style="font-size: 20px">Mark Rules<a
                                    href="<?php echo e(URL::route('recruitment.marks_rules.create')); ?>"
                                    class="btn btn-primary btn-xs pull-right">Add new field</a></caption>
                        <tr>
                            <th>SL. No</th>
                            <th>Circular name</th>
                            <th>Rule name</th>
                            <th>Rule for</th>
                            <th>Rules</th>
                            <th>Action</th>
                        </tr>
                        <?php $i = 1;?>
                        <?php $__empty_1 = true; foreach($points as $point): $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($i++); ?></td>
                                <td><?php echo e($point->circular->circular_name); ?></td>
                                <td><?php echo e($point->rule_name); ?></td>
                                <td><?php echo e($point->point_for); ?></td>
                                <?php if($point->rule_name==='education'): ?>
                                    <td><?php echo $point->getEducationRules(); ?></td>
                                <?php elseif($point->rule_name==='height'): ?>
                                    <td><?php echo $point->getHeightRules(); ?></td>
                                <?php elseif($point->rule_name==='training'): ?>
                                    <td><?php echo $point->getTrainingRules(); ?></td>
                                <?php elseif($point->rule_name==='experience'): ?>
                                    <td><?php echo $point->getExperienceRules(); ?></td>
                                <?php elseif($point->rule_name==='age'): ?>
                                    <td><?php echo $point->getAgeRules(); ?></td>
                                <?php endif; ?>
                                <td>
                                    <a class="btn btn-primary btn-xs" href="<?php echo e(URL::route('recruitment.marks_rules.edit',['id'=>$point->id])); ?>">
                                        <i class="fa fa-edit"></i>&nbsp;Edit
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; if ($__empty_1): ?>
                            <tr>
                                <td colspan="6" class="bg-warning">
                                    No Point Rule available.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </table>
                </div>
                <div class="table-responsive" ng-if="loadType=='ajax'" ng-bind-html="pointsView">

                </div>
            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('template.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>