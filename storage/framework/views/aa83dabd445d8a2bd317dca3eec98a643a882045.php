<?php /*User: Shreya*/ ?>
<?php /*Date: 12/15/2015*/ ?>
<?php /*Time: 5:39 PM*/ ?>


<?php $__env->startSection('title','Remove Ansar from Blacklist'); ?>
<?php $__env->startSection('breadcrumb'); ?>
    <?php echo Breadcrumbs::render('cancel_blacklist'); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>

    <script>
        $(document).ready(function () {
            $('#unblack_date').datepicker({
                dateFormat:'dd-M-yy'
            });
        })
        GlobalApp.controller('UnblackController', function ($scope, $http, $sce) {
            $scope.ansarId = "";
            $scope.ansarDetail = {};
            $scope.loadingAnsar = false;

            $scope.loadAnsarDetail = function (id) {
                $scope.loadingAnsar = true;
                $http({
                    method: 'get',
                    url: '<?php echo e(URL::route('unblacklist_ansar_details')); ?>',
                    params: {ansar_id: id}
                }).then(function (response) {
                    $scope.ansarDetail = response.data
                    $scope.loadingAnsar = false;
                })
            }
        })
    </script>

    <div ng-controller="UnblackController">
        <?php /*<div class="breadcrumbplace">*/ ?>
            <?php /*<?php echo Breadcrumbs::render('cancel_blacklist'); ?>*/ ?>
        <?php /*</div>*/ ?>
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
                <?php echo Form::model(Request::old(),array('route' => 'unblacklist_entry', 'id' => 'unblack_entry')); ?>

                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <?php echo Form::label('ansar_id','Ansar ID (Comes from Blacklist)'); ?>

                                <?php echo Form::text('ansar_id',null,['class'=>'form-control','placeholder'=>'Enter Ansar ID','ng-model'=>'ansarId','ng-init'=>'ansarId='.Request::old('ansar_id'),'ng-change'=>'loadAnsarDetail(ansarId)']); ?>

                                <?php echo $errors->first('ansar_id','<p class="text text-danger">:message</p>'); ?>

                            </div>
                            <div class="form-group">
                                <?php echo Form::label('unblack_date','Unblacking Date'); ?>

                                <?php echo Form::text('unblack_date',null,['class'=>'form-control','placeholder'=>'Unblack Date','id'=>'unblack_date','ng-model'=>'unblack_date']); ?>

                                <?php echo $errors->first('unblack_date','<p class="text text-danger">:message</p>'); ?>

                            </div>
                            <div class="form-group">
                                <?php echo Form::label('unblack_comment','Reason'); ?>

                                <?php echo Form::textarea('unblack_comment', $value = null, $attributes = array('class' => 'form-control', 'id' => 'unblack_comment', 'size' => '30x4', 'placeholder' => "Write Reason")); ?>

                                <?php echo $errors->first('unblack_comment','<p class="text text-danger">:message</p>'); ?>

                            </div>
                            <button id="unblack_ansar" type="submit" class="btn btn-primary">Remove Ansar from Blacklist</button>
                        </div>
                        <div class="col-sm-6 col-sm-offset-2"
                             style="min-height: 400px;border-left: 1px solid #CCCCCC">
                            <div id="loading-box" ng-if="loadingAnsar">
                            </div>
                            <div ng-if="ansarDetail.ansar_name_eng==undefined">
                                <h3 style="text-align: center">No Ansar Found</h3>
                            </div>
                            <div ng-if="ansarDetail.ansar_name_eng!=undefined">
                                <div class="form-group">
                                    <label class="control-label">Name</label>

                                    <p>
                                        [[ansarDetail.ansar_name_eng]]
                                    </p>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Rank</label>

                                    <p>
                                        [[ansarDetail.name_eng]]
                                    </p>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Unit</label>

                                    <p>
                                        [[ansarDetail.unit_name_eng]]
                                    </p>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Sex</label>

                                    <p>
                                        [[ansarDetail.sex]]
                                    </p>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Date of Birth</label>

                                    <p>
                                        [[ansarDetail.data_of_birth|dateformat:'DD-MMM-YYYY']]
                                    </p>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Status Before Blacked</label>

                                    <p>
                                        [[ansarDetail.black_list_from]]
                                    </p>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Date of being Blacked</label>

                                    <p>
                                        [[ansarDetail.black_listed_date|dateformat:'DD-MMM-YYYY']]
                                    </p>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Reason of being Blacked</label>

                                    <p>
                                        [[ansarDetail.black_list_comment]]
                                    </p>
                                </div>
                                <input type="hidden" name="ansar_prev_status"
                                       value="[[ansarDetail.black_list_from]]">
                            </div>
                        </div>
                    </div>
                </div>
                <?php echo Form::close(); ?>

            </div>
        </section>
    </div>
    <script>

        $("#unblack_ansar").confirmDialog({
            message: 'Are you sure to remove this Ansar from the Blacklist',
            ok_button_text: 'Confirm',
            cancel_button_text: 'Cancel',
            ok_callback: function (element) {
                $("#unblack_entry").submit()
            },
            cancel_callback: function (element) {
            }
        })
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('template.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>