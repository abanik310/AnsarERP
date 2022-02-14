<?php $__env->startSection('title','Add Ansar in Blocklist'); ?>
<?php $__env->startSection('breadcrumb'); ?>
    <?php echo Breadcrumbs::render('add_to_blocklist'); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <script>
        GlobalApp.controller('BlockController', function ($scope, $http, $sce) {
            $scope.ansarId = "";
            $scope.ansarDetail = {};
            $scope.loadingAnsar = false;
            $scope.isShow = false;
            $scope.loadAnsarDetail = function (id) {
                $scope.loadingAnsar = true;
                $http({
                    method: 'get',
                    url: '<?php echo e(URL::route('blocklist_ansar_details')); ?>',
                    params: {ansar_id: id}
                }).then(function (response) {
                    $scope.ansarDetail = response.data;
                    $scope.loadingAnsar = false;
                })
            }
            
            $scope.updatePeriodical = function() {

                    if ($scope.is_periodical) {
                         $scope.isShow = true;
                    }
                    else {
                         $scope.isShow = false;
                    } 
                }
        })
    </script>
    <div ng-controller="BlockController">
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
                    <span class="glyphicon glyphicon-remove"></span> <?php echo e(Session::get('error_message')); ?>

                </div>
            </div>
        <?php endif; ?>
        <section class="content" style="position: relative;">
            <notify></notify>
            <div class="box box-solid">
                <?php echo Form::open(array('route' => 'blocklist_entry', 'id' =>'block_entry')); ?>

                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="ansar_id" class="control-label">Ansar ID</label>
                                <input type="text" name="ansar_id" id="ansar_id" class="form-control"
                                       placeholder="Enter Ansar ID" ng-model="ansarId"
                                       ng-change="loadAnsarDetail(ansarId)">
                                <?php echo $errors->first('ansar_id','<p class="text text-danger">:message</p>'); ?>

                            </div>
                            <div class="form-group">
                                <datepicker-separate-fields label="Blocking Date:" notify="blockInvalidDate"
                                                            rdata="block_date"></datepicker-separate-fields>
                                <input type="hidden" name="block_date" ng-value="block_date">
                                <?php echo $errors->first('block_date','<p class="text text-danger">:message</p>'); ?>

                            </div>
                            
                            
                            
                            <div class="form-check">
                                <input class="form-check-input" name="is_periodical" id="is_periodical" ng-change="updatePeriodical()" ng-model="is_periodical" type="checkbox" value="true" id="flexCheckDefault">
                                <label class="form-check-label" for="iis_periodicals">
                                   Periodical Block
                                </label>
                              </div>

                            <div ng-show="isShow">
                                <datepicker-separate-fields label="Unlock Date:" notify="unblockInvalidDate"
                                                        rdata="unblock_date"></datepicker-separate-fields>
                                <input type="hidden" name="unblock_date" id="unblock_date" class="form-control"
                                   ng-value="unblock_date">
                                <div class="form-group">
                                    <label for="move_status" class="control-label">Move To Status</label>
                                    <select ng-model="move_status" name="move_status" class="form-control" id="move_status"
                                            required>
                                        <option value="">Please Select a Status</option>
                                        <option value="free">Free</option>
                                        <option value="panel">Panel</option>
                                        <option value="rest">Rest</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">                                
                                <?php echo $errors->first('unblock_date','<p class="text text-danger">:message</p>'); ?>

                                <?php echo $errors->first('move_status','<p class="text text-danger">:message</p>'); ?>


                            </div>
                            <div class="form-group">
                                <label for="block_comment" class="control-label">Reason</label>
                                <?php echo Form::textarea('block_comment', $value = null, $attributes = array('class' => 'form-control', 'id' => 'block_comment', 'size' => '30x4', 'placeholder' => "Write Reason", 'ng-model' => 'block_comment')); ?>

                                <?php echo $errors->first('block_comment','<p class="text text-danger">:message</p>'); ?>

                            </div>
                            <button id="block-ansar" type="submit" class="btn btn-primary"
                                    ng-disabled="blockInvalidDate">Block Ansar
                            </button>
                        </div>
                        <div class="col-sm-6 col-sm-offset-2"
                             style="min-height: 400px;border-left: 1px solid #CCCCCC">
                            <div id="loading-box" ng-if="loadingAnsar">
                            </div>
                            <div ng-if="ansarDetail.ansar_details.ansar_name_eng==undefined">
                                <h3 style="text-align: center">No Ansar Found</h3>
                            </div>
                            <div ng-if="ansarDetail.ansar_details.ansar_name_eng!=undefined">
                                <div class="form-group">
                                    <label class="control-label">Name</label>

                                    <p>
                                        [[ansarDetail.ansar_details.ansar_name_eng]]
                                    </p>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Rank</label>

                                    <p>
                                        [[ansarDetail.ansar_details.name_eng]]
                                    </p>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Unit</label>

                                    <p>
                                        [[ansarDetail.ansar_details.unit_name_eng]]
                                    </p>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Sex</label>

                                    <p>
                                        [[ansarDetail.ansar_details.sex]]
                                    </p>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Date of Birth</label>

                                    <p>
                                        [[ansarDetail.ansar_details.data_of_birth|dateformat:'DD-MMM-YYYY']]
                                    </p>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Current Status</label>

                                    <p>
                                        [[ansarDetail.status]]
                                    </p>
                                </div>
                                <input type="hidden" name="ansar_status" value="[[ansarDetail.status]]">
                                <input type="hidden" name="from_id" value="[[ansarDetail.ansar_details.id]]">
                            </div>
                        </div>
                    </div>
                </div>
                <?php echo Form::close(); ?>

            </div>
        </section>
    </div>
    <script>
        $("#block-ansar").confirmDialog({
            message: 'Are you sure to add this Ansar in the Blocklist',
            ok_button_text: 'Confirm',
            cancel_button_text: 'Cancel',
            ok_callback: function (element) {
                $("#block_entry").submit()
            },
            cancel_callback: function (element) {
            }
        })
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('template.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>