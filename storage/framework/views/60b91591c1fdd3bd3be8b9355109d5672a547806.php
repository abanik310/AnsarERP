<?php $__env->startSection('title','Download Applicant Marks Report'); ?>
<?php $__env->startSection('breadcrumb'); ?>
    <?php echo Breadcrumbs::render('recruitment.reports.download_applicant_marks_report'); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <script>
        GlobalApp.controller('applicantSearch', function ($scope, $http, $q, httpService) {
            $scope.circulars = [];
            $scope.param = {};
            httpService.circular({status: 'running'}).then(function (response) {
                $scope.circulars = response.data;
            })

        })

    </script>
    <section class="content" ng-controller="applicantSearch">
        <div class="box box-solid">
            <div class="overlay" ng-if="allLoading">
                    <span class="fa">
                        <i class="fa fa-refresh fa-spin"></i> <b>Loading...</b>
                    </span>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-sm-6 col-centered">
                        <form action="<?php echo e(URL::route('report.applicants.applicat_marks_list')); ?>" method="post" target="_blank">
                            <?php echo csrf_field(); ?>

                            <div class="form-group">
                                <label for="" class="control-label">Job Circular</label>
                                <select name="circular" ng-model="param.circular"
                                        class="form-control">
                                    <option value="">--Select a circular--</option>
                                    <option ng-repeat="c in circulars" value="[[c.id]]">[[c.circular_name]]</option>
                                </select>
                            </div>
                            <filter-template
                                    show-item="['range','unit']"
                                    type="all"
                                    data="param"
                                    start-load="range"
                                    field-name="{unit:'unit',range:'range'}"
                                    unit-field-disabled="!param.circular"
                                    range-field-disabled="!param.circular"
                                    field-width="{unit:'col-sm-12',range:'col-sm-12'}"
                            >
                            </filter-template>
                            <div class="form-group">
                                <button type="submit" ng-disabled="!param.circular" class="btn btn-primary btn-block">
                                    Download applicants mark
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('template.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>