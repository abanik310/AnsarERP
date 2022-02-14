<?php $__env->startSection('title','Embodiment Letter'); ?>
<?php $__env->startSection('breadcrumb'); ?>
    <?php echo Breadcrumbs::render('embodiment_letter_view'); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <script>
        GlobalApp.controller('EmbodimentLetterController', function ($scope, $http, $sce,$rootScope,httpService) {
            $scope.letterView = $sce.trustAsHtml("&nbsp;")
            $scope.printType = "smartCardNo"
            $scope.unit = {
                selectedUnit: []
            };
            $scope.units = [];
            $scope.allLoading = false;
            $scope.q = '';
            $scope.isDc = $rootScope.user.type==22 ? true : false;
            if ($scope.isDc) {
                $scope.unit.selectedUnit = $rootScope.user.district_id
            }
            else {
                httpService.unit().then(function (response) {
                    $scope.units = response.data;
                }, function (response) {

                })
            }
            $scope.loadData = function (url,q) {
                $scope.allLoading = true;
                $http({
                    method: 'get',
                    params: {type: "EMBODIMENT",q:q},
                    url: url==undefined?'<?php echo e(URL::route('letter_data')); ?>':url
                }).then(function (response) {
                    if (!$scope.isDc) $scope.unit.selectedUnit = [];
                    $scope.letterView = $sce.trustAsHtml(response.data);
                    $scope.allLoading = false;
                },function (res) {
                    $scope.allLoading = false;
                })
            }
        })
        GlobalApp.directive('compileHtml',function ($compile) {
            return {
                restrict:'A',
                link:function (scope,elem,attr) {
                    scope.$watch('letterView',function(n){

                        if(attr.ngBindHtml) {
                            $compile(elem[0].children)(scope)
                        }
                    })

                }
            }
        })
    </script>
    <div ng-controller="EmbodimentLetterController" ng-init="loadData()">
        <section class="content">
            <div class="box box-solid">
                <div class="overlay" ng-if="allLoading">
                    <span class="fa">
                        <i class="fa fa-refresh fa-spin"></i> <b>Loading...</b>
                    </span>
                </div>
               <div class="box-body">
                   <div class="row">
                       <div class="col-sm-3 col-xs-6">
                           <div class="form-group">
                               <input type="radio" ng-model="printType" value="smartCardNo">
                               <span class="text text-bold" style="vertical-align: top">Print by Smart card no.</span>
                           </div>
                       </div>
                       <div class="col-sm-3 col-xs-6">
                           <div class="form-group">
                               <input type="radio" ng-model="printType" value="memorandumNo">
                               <span class="text text-bold" style="vertical-align: top">Print by Memorandum no.</span>
                           </div>
                       </div>
                   </div>
                   <div ng-if="printType=='smartCardNo'">
                       <div class="row">
                           <div class="col-sm-3">
                               <div class="form-group">
                                   <label for="">Smart Card no.</label>
                                   <input type="text" ng-model="smartCardNo" placeholder="Enter Smart Card no." class="form-control">
                               </div>
                               <!-- <div class="form-group">
                                   <filter-template
                                           show-item="['unit']"
                                           type="single"
                                           data="unit.param"
                                           start-load="unit"
                                           layout-vertical="1"
                                   >
                                   </filter-template>
                               </div> -->
                               <div class="form-group">
                                   <?php echo Form::open(['route'=>'print_letter','target'=>'_blank']); ?>

                                   <?php echo Form::hidden('option','smartCardNo'); ?>

                                   <?php echo Form::hidden('id','[[smartCardNo]]'); ?>

                                   <?php echo Form::hidden('type','EMBODIMENT'); ?>

                                   <?php if(auth()->user()->type!=22): ?>
                                       <?php echo Form::hidden('unit','0'); ?>

                                   <?php else: ?>
                                       <?php echo Form::hidden('unit',auth()->user()->district?auth()->user()->district->id:''); ?>

                                   <?php endif; ?>
                                   <button class="btn btn-primary">Generate Embodied Letter</button>
                                   <?php echo Form::close(); ?>

                               </div>
                           </div>
                       </div>
                   </div>
                   <div ng-if="printType=='memorandumNo'">
                       <?php /*<div class="table-responsive">
                           <table class="table table-bordered table-striped">
                               <caption>
                                   <table-search q="q" results="results" place-holder="Search Memorandum no."></table-search>
                               </caption>
                               <tr>
                                   <th>#</th>
                                   <th>Memorandum no.</th>
                                   <th>Memorandum Date</th>
                                   <th>Unit</th>
                                   <th>Action</th>
                               </tr>

                               <tr ng-repeat="d in datas|filter: q as results">
                                   <td>[[$index+1]]</td>
                                   <td>
                                       [[d.memorandum_id]]
                                   </td>
                                   <td>[[d.mem_date?(d.mem_date):'n/a']]</td>
                                   <td>
                                       <select ng-if="!isDc" class="form-control" name="unit" ng-model="unit.selectedUnit[$index]"
                                               ng-disabled="units.length==0">
                                           <option value="">--<?php echo app('translator')->get('title.unit'); ?>--</option>
                                           <option ng-repeat="u in units" value="[[u.id]]">[[u.unit_name_bng]]</option>
                                       </select>

                                       <div ng-if="isDc">
                                           <?php echo e(auth()->user()->district?auth()->user()->district->unit_name_eng:''); ?>

                                       </div>
                                   </td>
                                   <td>
                                       <?php echo Form::open(['route'=>'print_letter','target'=>'_blank']); ?>

                                       <?php echo Form::hidden('option','memorandumNo'); ?>

                                       <?php echo Form::hidden('id','[[d.memorandum_id]]'); ?>

                                       <?php echo Form::hidden('type','EMBODIMENT'); ?>

                                       <?php if(auth()->user()->type!=22): ?>
                                           <?php echo Form::hidden('unit','[[unit.selectedUnit[$index] ]]'); ?>

                                           <?php else: ?>
                                       <?php echo Form::hidden('unit',auth()->user()->district?auth()->user()->district->id:''); ?>

                                       <?php endif; ?>
                                       <button class="btn btn-primary">Generate Embodied Letter</button>
                                       <?php echo Form::close(); ?>

                                   </td>
                               </tr>

                               <tr ng-if="datas==undefined||datas.length<=0||results.length<=0">
                                   <td class="warning" colspan="5">No Memorandum no. available</td>
                               </tr>
                           </table>
                       </div>*/ ?>
                       <div id="letter_data_view" ng-bind-html="letterView" compile-html></div>
                   </div>
               </div>
            </div>
        </section>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('template.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>