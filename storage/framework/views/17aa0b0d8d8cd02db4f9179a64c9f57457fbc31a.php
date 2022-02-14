<?php $__env->startSection('title','View Service Record'); ?>
<?php $__env->startSection('breadcrumb'); ?>
    <?php echo Breadcrumbs::render('view_ansar_service_record'); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <script>
        GlobalApp.controller('TransferController', function ($scope,$http,$sce) {
            $scope.ansarDetail = {};
            $scope.allLoading = false;
            $scope.exist = false;
            $scope.errorFound=0;
            $scope.loadAnsarDetail = function (id) {
                $scope.allLoading = true;
                $http({
                    method: 'get',
                    url: '<?php echo e(URL::route('ansar_detail_info')); ?>',
                    params: {ansar_id: id}
                }).then(function (response) {
                    $scope.errorFound=0;
                    $scope.ansarDetail = response.data
                    //$scope.checkFile($scope.ansarDetail.apid.profile_pic)
                    $scope.allLoading = false;
                },function(response){
                    $scope.ansarDetail = '';
                    $scope.errorFound = 1;
                    $scope.errorMessage = "Please enter a valid Ansar ID";
                    $scope.allLoading = false;
//                    $scope.ansarDetail = $sce.trustAsHtml("<tr class='warning'><td colspan='"+$('.table').find('tr').find('th').length+"'>"+response.data+"</td></tr>");
                })
            }
            $scope.loadAnsarDetailOnKeyPress = function (ansar_id,$event) {
                if($event.keyCode==13) {
                    $scope.allLoading = true;
                    $http({
                        method: 'get',
                        url: '<?php echo e(URL::route('ansar_detail_info')); ?>',
                        params: {ansar_id: ansar_id}
                    }).then(function (response) {
                        $scope.errorFound=0;
                        $scope.ansarDetail = response.data
                        //if($scope.ansarDetail.apid)$scope.checkFile($scope.ansarDetail.apid.profile_pic)
                        $scope.allLoading = false;
                    }, function (response) {
                        $scope.ansarDetail = '';
                        $scope.errorFound = 1;
                        $scope.errorMessage = "Please enter a valid Ansar ID";
                        $scope.allLoading = false;
//                        $scope.ansarDetail = $sce.trustAsHtml("<tr class='warning'><td colspan='"+$('.table').find('tr').find('th').length+"'>"+response.data+"</td></tr>");
                    })
                }
            }
            <?php /*$scope.checkFile = function(url){*/ ?>
                <?php /*$http({*/ ?>
                    <?php /*url:'<?php echo e(URL::to('/check_file')); ?>',*/ ?>
                    <?php /*params:{path:url},*/ ?>
                    <?php /*method:'get'*/ ?>
                <?php /*}).then(function (response) {*/ ?>
                    <?php /*$scope.exist = response.data.status;*/ ?>
                <?php /*}, function () {*/ ?>
                    <?php /*$scope.exist = false;*/ ?>
                <?php /*})*/ ?>
            <?php /*}*/ ?>
//            $scope.dateConvert=function(date){
//                return (moment(date).locale('bn').format('DD-MMMM-YYYY'));
//            }
        })
        $(function () {
            $('body').on('click','#print-report', function (e) {
                //alert("pppp")
                e.preventDefault();
                $("#print-area").remove();
//                console.log($("body").find("#print-body").html())
                $('body').append('<div id="print-area">'+$("#ansar_service_record").html()+'</div>')
               // beforePrint()
                window.print();
                $("#print-area").remove()
               // afterPrint()
            })
        })
    </script>
    <style>
        input::-webkit-input-placeholder {
            color: #7b7b7b !important;
        }

        input:-moz-placeholder { /* Firefox 18- */
            color: #7b7b7b !important;
        }

        input::-moz-placeholder {  /* Firefox 19+ */
            color: #7b7b7b !important;
        }

        input:-ms-input-placeholder {
            color: #7b7b7b !important;
        }
    </style>
    <style>
        @page  {
            size: 9in 13in;
            margin: 27mm 16mm 27mm 16mm;
        }
    </style>
    <div ng-controller="TransferController">
        <section class="content">
            <div class="box box-solid">
                <div class="overlay" ng-if="allLoading">
                    <span class="fa">
                        <i class="fa fa-refresh fa-spin"></i> <b>Loading...</b>
                    </span>
                </div>
                <div class="box-body"><br>
                    <div class="row">
                        <div class="col-md-6 col-centered">
                            <div class="col-md-6 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    <?php /*<label class="control-label">Enter a ansar id</label>*/ ?>
                                    <input type="text" ng-model="ansar_id" class="form-control" placeholder="Enter Ansar ID" ng-keypress="loadAnsarDetailOnKeyPress(ansar_id,$event)">
                                    <span class="text-danger" ng-if="errorFound==1"><p>[[errorMessage]]</p></span>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12 col-xs-12">
                                <button class="btn btn-primary" ng-click="loadAnsarDetail(ansar_id)">Generate Ansar Service Record</button>

                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12" id="ansar_service_record">
                            <h3 style="text-align: center">View Ansar Service Record&nbsp;<a href="#" id="print-report"><span class="glyphicon glyphicon-print"></span></a></h3>

                            <template-list data="ansarDetail" key="ansar_history"></template-list>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('template.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>