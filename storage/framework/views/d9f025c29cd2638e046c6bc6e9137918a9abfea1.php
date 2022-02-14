<?php $__env->startSection('title','Freeze For Different Reasons'); ?>
<?php $__env->startSection('breadcrumb'); ?>
    <?php echo Breadcrumbs::render('freeze'); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <script>
        GlobalApp.controller('FreezeController', function ($scope, $http, $sce) {
            $scope.ansarId = "";
            $scope.ansarDetail = {};
            $scope.ansar_ids = [];
            $scope.printLetter = [{}, {}];
            $scope.totalLength = 0;
            $scope.loadingAnsar = false;
            $scope.loadAnsarDetail = function (id) {
                $scope.printLetter = [{}, {}];

                $scope.loadingAnsar = true;
                $http({
                    method: 'get',
                    url: '<?php echo e(URL::route('load_ansar_for_freeze')); ?>',
                    params: {ansar_id: id}
                }).then(function (response) {
                    $scope.error = false;
                    $scope.ansarDetail = response.data;
                    console.log($scope.ansarDetail);
                    $scope.loadingAnsar = false;
                    $scope.totalLength--;
                }, function (response) {
                    $scope.error = true;
                    $scope.loadingAnsar = false;
                })
            };
            $scope.makeQueue = function (id) {
                $scope.ansar_ids.push(id);
                $scope.totalLength += 1;
            };
            
               $scope.$watch('responseData', function (newVal, oldVal) {
                $scope.selected = [];
                if (newVal !== undefined && newVal.constructor === Object) {
                    $("#withrdaw-option").modal('hide');
                    $scope.printLetter[0] = newVal.printData;
                }
            }, true);
            
            
            $scope.$watch('totalLength', function (n, o) {
                if (!$scope.loadingAnsar && n > 0) {
                    $scope.loadAnsarDetail($scope.ansar_ids.shift())
                } else {
                    if (!$scope.ansarId) $scope.ansarDetail = {}
                }
            });
            $scope.verifyMemorandumId = function () {
                var data = {
                    memorandum_id: $scope.memorandumId
                };
                $scope.isVerified = false;
                $scope.isVerifying = true;
                $http.post('<?php echo e(action('UserController@verifyMemorandumId')); ?>', data).then(function (response) {
                    $scope.isVerified = response.data.status;
                    $scope.isVerifying = false;
                }, function (response) {
                })
            };
            $scope.verifyDate = function (i, j) {
                if (moment(i).isValid() || moment(j).isValid()) {
                    $cd = moment(i).format('DD-MMM-YYYY');
                    return moment(j).isSameOrBefore($cd)
                } else return false;
            };
            $scope.convertDate = function (d) {
                return moment(d).format('DD-MMM-YYYY')
            }
        })
    </script>
    <div ng-controller="FreezeController">
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
            <div class="box box-solid">
                <div class="box-body">
                    <div class="row">
                         <?php echo Form::open(array('route' => 'freeze_entry', 'id' => 'freeze_entry', 'ng-app' => 'myValidateApp', 'novalidate','form-submit','errors','response-data'=>'responseData','loading','status')); ?>


                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="ansar_id" class="control-label">Ansar ID to Freeze</label>
                                <input type="text" name="ansar_id" id="ansar_id" class="form-control"
                                       placeholder="Enter Ansar ID" ng-model="ansarId" ng-change="makeQueue(ansarId)">
                                <?php echo $errors->first('ansar_id','<p class="text text-danger">:message</p>'); ?>

                            </div>
                            <div class="form-group">
                                <label for="memorandum_id" class="control-label">Memorandum no.</label>
                                <input ng-model="memorandumId" type="text" class="form-control" name="memorandum_id"
                                       placeholder="Enter Memorandum no.">
                                <?php echo $errors->first('memorandum_id','<p class="text text-danger">:message</p>'); ?>

                            </div>
                            <div class="form-group">
                                <label class="control-label" for="freeze_reason">Freeze Reason:</label>
                                <select name="freeze_reason" class="form-control">
                                    <option value="">Select a reason</option>
                                    <option value="Disciplinary Actions">Disciplinary Actions</option>
                                    <option value="Pre deployment"> Pre deployment</option>
                                    <option value="Leave without pay">Leave without pay</option>
                                </select>
                                <?php if($errors->has('freeze_reason')): ?><span
                                        style="color:red"><?php echo e($errors->first('freeze_reason')); ?></span><?php endif; ?>
                            </div>
                            <div class="form-group">
                                <datepicker-separate-fields label="Freeze Date:" notify="freezeInvalidDate"
                                                            rdata="freeze_date"></datepicker-separate-fields>
                                <input type="hidden" name="freeze_date" ng-value="freeze_date">
                                <?php echo $errors->first('freeze_date','<p class="text text-danger">:message</p>'); ?>

                            </div>
                            <div class="form-group">
                                <label for="freeze_comment" class="control-label">Comment for Freezing the Ansar</label>
                                <?php echo Form::textarea('freeze_comment', $value = null, $attributes = array('class' => 'form-control', 'id' => 'freeze_comment', 'size' => '30x4', 'placeholder' => "Write any Comment", 'ng-model' => 'freeze_comment')); ?>

                                <?php echo $errors->first('freeze_comment','<p class="text text-danger">:message</p>'); ?>

                            </div>
                            <button id="confirm-freeze" type="submit" class="btn btn-primary"
                                    ng-disabled="freezeInvalidDate">Freeze
                            </button>
                            
                               
                        </div>
                        <div class="col-sm-6 col-sm-offset-2" style="min-height: 400px;border-left: 1px solid #CCCCCC">
                            <div id="loading-box" ng-if="loadingAnsar"></div>
                            <div ng-if="ansarDetail.name==undefined&&!error">
                                <h3 style="text-align: center">No Ansar Found</h3>
                            </div>
                            <div ng-if="ansarDetail.name!=undefined&&!error">
                                <div class="form-group">
                                    <label class="control-label">Name</label>
                                    <p>[[ansarDetail.name]]</p>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Rank</label>
                                    <p>[[ansarDetail.rank]]</p>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">KPI Name</label>
                                    <p>[[ansarDetail.kpi]]</p>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">KPI Unit</label>
                                    <p>[[ansarDetail.unit]]</p>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">KPI Thana</label>
                                    <p>[[ansarDetail.thana]]</p>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Sex</label>
                                    <p>[[ansarDetail.sex]]</p>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Date of Birth</label>
                                    <p>[[convertDate(ansarDetail.dob)]]</p>
                                </div>
                                <div class="form-group">
                                    <label class="control-label status-check">Reporting Date</label>
                                    <p>[[convertDate(ansarDetail.r_date)]]</p>
                                </div>
                                <div class="form-group">
                                    <label class="control-label status-check">Embodiment Date</label>
                                    <p>[[convertDate(ansarDetail.j_date)]]</p>
                                </div>
                            </div>
                            <div ng-if="error">
                                An Server Occur. Contact with system administrator
                            </div>
                        </div>
                          <?php echo Form::close(); ?>

                    </div>
                    <div class="row">
                          <?php echo Form::open(['route'=>'print_letter','target'=>'_blank','class'=>'pull-left']); ?>

                    <input type="hidden" ng-repeat="(k,v) in printLetter[0]" name="[[k]]" value="[[v]]">
                    <button ng-show="printLetter[0].status" class="btn btn-primary">
                        <i class="fa fa-print"></i>&nbsp;Print Freez Letter
                    </button>
                       <?php echo Form::close(); ?>

                    </div>
                </div>
               
            </div>
           
        </section>
    </div>
    <script>
        $("#confirm-freeze").confirmDialog({
            message: 'Are you sure to Freeze this Ansar',
            ok_button_text: 'Confirm',
            cancel_button_text: 'Cancel',
            ok_callback: function (element) {
                $("#freeze_entry").submit()
            },
            cancel_callback: function (element) {
            }
        })
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('template.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>