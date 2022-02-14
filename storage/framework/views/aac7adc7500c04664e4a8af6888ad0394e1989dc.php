<?php $__env->startSection('title','Draft Entry List'); ?>
<?php $__env->startSection('breadcrumb'); ?>
    <?php echo Breadcrumbs::render('draft_list'); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <script>
        GlobalApp.controller('draftController', function ($scope, getDraftService) {
            getDraftService.getAllDraftValues().then(function (response) {

                $scope.draft = response.data;

//            alert(JSON.stringify($scope.draft));
                console.log($scope.draft);
            });

        });
        GlobalApp.factory('getDraftService', function ($http) {
            return {
                getAllDraftValues: function () {
                    return $http.get("<?php echo e(URL::to('HRM/getdraftlist')); ?>");
                }
            }
        })
    </script>
    <div ng-controller="draftController">
        <?php /*<div class="breadcrumbplace">*/ ?>
        <?php /*<?php echo Breadcrumbs::render('draft_entry'); ?>*/ ?>
        <?php /*</div>*/ ?>
        <section class="content">
            <?php if(Session::has('success')): ?>
                <div class="alert alert-success">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <span class="glyphicon glyphicon-ok"></span><?php echo e(Session::get('success')); ?>

                </div>
            <?php endif; ?>
            <?php if(Session::has('error')): ?>
                <div class="alert alert-danger">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <span class="glyphicon glyphicon-ok"></span><?php echo e(Session::get('error')); ?>

                </div>
            <?php endif; ?>
            <?php if(Session::has('add_success')): ?>
                <div class="alert alert-success">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <span class="glyphicon glyphicon-ok"></span><?php echo e(Session::get('add_success')); ?>

                </div>
            <?php endif; ?>
                <?php if(Session::has('update_draft')): ?>
                    <div class="alert alert-success">
                        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                        <span class="glyphicon glyphicon-ok"></span><?php echo e(Session::get('update_draft')); ?>

                        <?php Session::forget('update_draft'); ?>
                    </div>
                <?php endif; ?>
            <div class="box box-solid">
                <div class="box-body" id="change-body">
                    <div class="loading-data"><i class="fa fa-4x fa-refresh fa-spin loading-icon"></i>
                    </div>

                    <table class="table table-bordered" id="ansar-table">

                        <tr>

                            <th>Name</th>
                            <th>Father name</th>
                            <th>Sex</th>
                            <th>Mobile</th>
                            <th style="width:140px">Action</th>
                        </tr>
                        <tr ng-show="!draft">
                            <td colspan="5">No draft data</td>
                        </tr>
                        <tr ng-repeat="drafts in draft">
                            <td>[[ drafts.ansar_name_eng ]]</td>
                            <td>[[ drafts.father_name_eng ]]</td>
                            <td>[[ drafts.sex]]</td>
                            <td>[[ drafts.mobile_no_self ]]</td>
                            <td>
                                <a class="btn btn-success btn-xs "
                                   href="<?php echo e(URL::to('HRM/singledraftedit')); ?>/[[ drafts.filename ]]" title="Edit"><span
                                            class="glyphicon glyphicon-edit"></span></a>
                                <a class="btn btn-danger btn-xs " title="Delete"
                                   href="<?php echo e(URL::to('HRM/draftdelete')); ?>/[[ drafts.filename ]]"><i
                                            class="glyphicon glyphicon-trash"></i></a></td>
                        </tr>
                    </table>
                </div>
            </div>
        </section>

    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('template/master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>