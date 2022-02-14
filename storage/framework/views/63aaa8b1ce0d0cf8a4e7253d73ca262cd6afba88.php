<?php /*User: Shreya*/ ?>
<?php /*Date: 12/26/2015*/ ?>
<?php /*Time: 10:19 AM*/ ?>


<?php $__env->startSection('title','Direct Panel'); ?>
<?php /*<?php $__env->startSection('small_title','DG'); ?>*/ ?>
<?php $__env->startSection('breadcrumb'); ?>
    <?php echo Breadcrumbs::render('direct_panel'); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <script>
        $(document).ready(function () {
            $('#direct_panel_date').datepicker({
                dateFormat:'dd-M-yy',
                onSelect: function(datetext) {
                    var d = new Date(); // for now

                    var h = d.getHours();
                    h = (h < 10) ? ("0" + h) : h ;

                    var m = d.getMinutes();
                    m = (m < 10) ? ("0" + m) : m ;

                    var s = d.getSeconds();
                    s = (s < 10) ? ("0" + s) : s ;

                    datetext = datetext + " " + h + ":" + m + ":" + s;

                    $('#direct_panel_date').val(datetext);
                }
            });
            $("input[name='direct_panel_date']").val(moment().format("DD-MMM-YYYY HH:mm:ss"));
        })
        GlobalApp.controller('DGPanelController', function ($scope, $http, $sce, $window) {
            $scope.ansarId = "";
            $scope.ansarDetail = {};
            $scope.ansar_ids = [];
            $scope.totalLength = 0;
            $scope.loadingAnsar = false;

            $scope.loadAnsarDetail = function (id) {
                //alert('rintu test');
                $scope.loadingAnsar = true;
                $http({
                    method: 'get',
                    url: '<?php echo e(URL::to('HRM/direct_panel_ansar_details')); ?>',
                    params: {ansar_id: id}
                }).then(function (response) {
                    console.log(response.status);
                   

                    $scope.ansarDetail = response.data
                    console.log($scope.ansarDetail)
                    $scope.loadingAnsar = false;
                    $scope.totalLength--;
                },
                    function(response){
			//alert('rintu');
                             $window.location.reload();
							 

                   }); 
            }
            $scope.makeQueue = function (id) {
                $scope.ansar_ids.push(id);
                $scope.totalLength += 1;
            }
            $scope.$watch('totalLength', function (n, o) {
                if (!$scope.loadingAnsar && n > 0) {
                    $scope.loadAnsarDetail($scope.ansar_ids.shift())
                }
                else {
                    if (!$scope.ansarId) $scope.ansarDetail = {}
                }
            })
        })
    </script>

    <div ng-controller="DGPanelController">
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
                        <div class="col-sm-4">
                            <?php echo Form::model(Request::old(),array('route' => 'direct_panel_entry', 'id' => 'direct_panel_entry')); ?>

                            <div class="form-group">
                                <?php echo Form::label('ansar_id','Ansar ID to add to Panel'); ?>

                                <?php if(Request::old('ansar_id')): ?>
                                    <?php echo Form::text('ansar_id',null,['class'=>'form-control','placeholder'=>'Enter Ansar ID','ng-model'=>'ansarId','ng-init'=>"ansarId=".Request::old('ansar_id'),'ng-change'=>'makeQueue(ansarId)']); ?>

                                <?php else: ?>
                                    <?php echo Form::text('ansar_id',null,['class'=>'form-control','placeholder'=>'Enter Ansar ID','ng-model'=>'ansarId','ng-change'=>'makeQueue(ansarId)']); ?>

                                <?php endif; ?>
                                <?php echo $errors->first('ansar_id','<p class="text text-danger">:message</p>'); ?>

                            </div>
                            <div class="form-group">
                                <?php echo Form::label('memorandum_id','Memorandum no.'); ?>

                                <?php echo Form::text('memorandum_id',null,['class'=>'form-control','placeholder'=>'Enter Memorandum no.']); ?>

                                <?php echo $errors->first('memorandum_id','<p class="text text-danger">:message</p>'); ?>

                            </div>
                            <div class="form-group">
                                <?php echo Form::label('direct_panel_date','Panel Date'); ?>

                                <?php echo Form::text('direct_panel_date',null,['class'=>'form-control','id'=>'direct_panel_date','placeholder'=>'Enter Panel Date']); ?>

                                <?php echo $errors->first('direct_panel_date','<p class="text text-danger">:message</p>'); ?>

                            </div>
                            <div class="form-group">
                                <?php echo Form::label('direct_panel_comment','Comment for adding to Panel'); ?>

                                <?php echo Form::textarea('direct_panel_comment', null, $attributes = array('class' => 'form-control', 'id' => 'direct_panel_comment', 'size' => '30x4', 'placeholder' => "Write Comment", 'ng-model' => 'direct_panel_comment')); ?>

                            </div>
                            <button id="add-panel-for-dg" class="btn btn-primary">
                                <img ng-show="loadingSubmit" src="<?php echo e(asset('dist/img/facebook-white.gif')); ?>"
                                     width="16" style="margin-top: -2px">Add to Panel
                            </button>
                            <?php echo Form::close(); ?>

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
                                        [[ansarDetail.ansar_details.data_of_birth]]
                                    </p>
                                </div>
                                <div class="form-group">
                                    <label class="control-label status-check">Current Status</label>

                                    <p>
                                        [[ansarDetail.status]]
                                    </p>
                                </div>
                                <input type="hidden" name="ansar_status" value="[[ansarDetail.status]]">
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>
    </div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('template.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>