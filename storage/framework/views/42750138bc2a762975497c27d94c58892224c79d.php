<?php $__env->startSection('title','Ansar Promotion'); ?>
<?php /* <?php $__env->startSection('breadcrumb'); ?>
    <?php echo Breadcrumbs::render('HRM.Ansar Promotion.Ansar Promotion'); ?>

<?php $__env->stopSection(); ?> */ ?>
<?php $__env->startSection('content'); ?>
    <script>
        
        GlobalApp.controller('applicantSearch', function ($scope, $http, $q, httpService, $sce, notificationService) {
            $scope.isCatOther = false;
            $scope.circulars = [];
            $scope.param = {};

            var init = function () {
                //$scope.allLoading = true;
                $http({
                    method: 'get',
                    url: '<?php echo e(URL::route('HRM.promotion.circulars')); ?>',
                    params: {
                        "status":"running",
                        "category":5
                    }
                }).then(function (response) {
                    console.log(response);
                  $scope.circulars = response.data;
                }, function (response) {
                    $scope.allLoading = false;
                });

            };
            
            init();
           
            $scope.loadCircular = function () {
                //alert("Anik");
             
            };

            $scope.loadApplicant = function () {
                //alert("Anik");
                $scope.allLoading = true;
                $http({
                    method: 'post',
                    url: '<?php echo e(URL::route('HRM.promotion.promotion')); ?>',
                    data: angular.toJson($scope.param)
                }).then(function (response) {
                    $scope.allLoading = false;
                    $scope.applicants = $sce.trustAsHtml(response.data);
                }, function (response) {
                    $scope.allLoading = false;
                })
            };
            $scope.confirmPromotion = function () {
                
                $scope.allLoading = true;
                $http({
                    method: 'post',
                    url: '<?php echo e(URL::route('HRM.promotion.confirm_promotion')); ?>',
                    data: angular.toJson($scope.param)
                }).then(function (response) {
                    $scope.allLoading = false;
                    $scope.applicants = $sce.trustAsHtml('');
                    notificationService.notify(response.data.status, response.data.message)
                }, function (response) {
                    $scope.allLoading = false;
                    notificationService.notify('error', response.statusText)
                })
            };

            //===========~~~
            $scope.changeCircular = function () {
                //alert("Anik");
                var keepGoing = true;
                $scope.circulars.forEach(function (value) {
                    if (keepGoing) {
                        if (value.id === parseInt($scope.param.circular) && value.category.category_type === "other") {
                            $scope.isCatOther = true;
                            keepGoing = false;
                        } else {
                            $scope.isCatOther = false;
                        }
                    }
                });
            };
            $scope.uploadFileForm = function(){
                var index = 0;
                var fd = new FormData(document.getElementById("uploadFileForm"))
                $scope.allLoading = true;
                $http({
                    url:"<?php echo e(URL::route('HRM.promotion.confirm_promotion_by_uploading_file')); ?>",
                    data:fd,
                    method:'post',
                    headers:{
                        "Content-Type":undefined
                    },
                    eventHandlers:{
                        progress:function (event) {
                            var response = event.currentTarget.response;
                            $scope.message = response.substr(index,response.length-index);
                            console.log(response.substr(index,response.length-index))
                            index = response.length;
                        }
                    }
                }).then(function (response) {
                    $scope.message = response.data;
                    $scope.allLoading = false;
                },function (response) {

                })
            }

        });
        GlobalApp.directive('compileHtml', function ($compile) {
            return {
                restrict: 'A',
                link: function (scope, elem, attr) {
                    var newScope;
                    scope.$watch('applicants', function (n) {

                        if (attr.ngBindHtml) {
                            if (newScope) newScope.$destroy();
                            newScope = scope.$new();
                            $compile(elem[0].children)(newScope)
                        }
                    })

                }
            }
        });
        GlobalApp.directive('confirmDialog', function () {
            return {
                restrict: 'A',
                link: function (scope, elem, attr) {
                    $(element).confirmDialog({
                        message: scope.message || "Are u sure?",
                        ok_button_text: 'Confirm',
                        cancel_button_text: 'Cancel',
                        event: 'click',
                        ok_callback: function (element) {
                            scope.confirmPromotion()
                        },
                        cancel_callback: function (element) {
                        }
                    })

                }
            }
        });
    </script>
    <section class="content" ng-controller="applicantSearch">
        <div class="box box-solid">
            <div class="overlay" ng-if="allLoading">
                    <span class="fa">
                        <i class="fa fa-refresh fa-spin"></i> <b>Loading...</b>
                    </span><br>
                <span>[[message]]</span>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-sm-6 col-centered">
                        <div class="form-group">
                            <label for="" class="control-label">Job Circular</label>
                            <select name="" ng-model="param.circular" ng-change="changeCircular()"
                                    class="form-control" >
                                <option value="" >--Select a circular--</option>
                                <option ng-repeat="c in circulars" value="[[c.id]]">[[c.circular_name]]</option>
                                <?php /* <option value="commander">Commander</option>
                                <option value="manual">Manual</option> */ ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <form method="post" enctype="multipart/form-data" id="uploadFileForm" ng-submit="uploadFileForm()">
                                <?php echo csrf_field(); ?>

                                <input type="hidden" name="circular" ng-value="param.circular">
                                <div class="form-group">
                                    <label for="" class="control-label">Upload File</label>
                                    <input type="file" name="applicant_id_list" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label for="" class="control-label">Comment</label>
                                    <input type="text" name="comment" class="form-control" placeholder="Enter comment" required>
                                </div>
                                <div class="form-group">
                                    <button class="btn btn-primary pull-right">Upload file</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
                
        </div>   
    </section>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('template.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>