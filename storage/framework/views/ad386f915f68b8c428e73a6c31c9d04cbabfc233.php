<?php $__env->startSection('title','Entry Info'); ?>
<?php $__env->startSection('breadcrumb'); ?>
    <?php echo Breadcrumbs::render('orginal_info'); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <script>
        GlobalApp.controller('originalInfo', function ($scope, $http) {
            $scope.isSearching = false;
            $scope.fullInfo = function (keyEvent, id) {
                if (keyEvent.type == 'keypress') {
                    if (keyEvent.which === 13) {
                        $scope.ID = id;
                        $scope.isSearching = true;
                        $http({
                            url: "<?php echo e(URL::to('HRM/idsearch')); ?>",
                            method: 'post',
                            data: {ansarId: id}
                        }).then(function (response) {
//                        alert(JSON.stringify(response.data));
                            $scope.searchedAnsar = response.data;
                            console.log($scope.searchedAnsar);
                        })
                    }
                }
                else if (keyEvent.type == 'click') {
                    $scope.ID = id;
                    $scope.isSearching = true;
                    $http({
                        url: "<?php echo e(URL::to('HRM/idsearch')); ?>",
                        method: 'post',
                        data: {ansarId: id}
                    }).then(function (response) {
                        $scope.searchedAnsar = response.data;
                        $scope.fontURL = $scope.searchedAnsar.url.font
                        $scope.backURL = $scope.searchedAnsar.url.back
                        console.log($scope.searchedAnsar);
                    }, function (response) {
                        $scope.searchedAnsar = {status: false}
                    })
                }
            }
        })
        $(document).ready(function () {
            $("#print-report").on('click', function (e) {
                e.preventDefault();
                $("#entry-report").find(".col-md-4").addClass("col-xs-4")
                $("#entry-report").find(".col-md-6").addClass("col-xs-6")
                $("#entry-report").find(".col-md-12").addClass("col-xs-12")
                $("#entry-report").find(".col-md-offset-2").addClass("col-xs-offset-2")
                $("#entry-report").find("img").removeClass("img-thumbnail img-responsive")
                $("#entry-report table").removeClass("table table-bordered table-stripped borderless")
                var html = $("#entry-report").html();
                $('body').append('<div id="print-area">' + html + '</div>')
                window.print();
//                $("#entry-report").find(".col-md-4").removeClass("col-xs-4")
//                $("#entry-report").find(".col-md-6").removeClass("col-xs-6")
//                $("#entry-report").find(".col-md-12").removeClass("col-xs-12")
//                $("#entry-report").find(".col-md-offset-2").removeClass("col-xs-offset-2")
//                $("#entry-report table").addClass("table table-bordered table-stripped borderless")
//                $("#entry-report").find("img").addClass("img-thumbnail img-responsive")
                $("#print-area").remove()
            })
        })
    </script>

    <div ng-controller="originalInfo">
        <section class="content">
            <div class="box box-solid">
                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-8 col-md-8 col-xs-12 col-lg-6 col-centered">
                            <form method="post" action="">
                                <?php echo csrf_field(); ?>

                                <div class="center-search">
                                    <input type="text" name="ansar_id" placeholder="Enter Ansar ID to see Entry Information">
                                    <button type="submit" class="btn btn-success btn-md"
                                            style="display: block;margin: 20px auto;">View Entry Information
                                    </button>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-2 col-sm-2 col-xs-12 col-centered" style="margin-bottom: 20px">
                            <a href="#" id="print-report" class="btn btn-primary btn-block">
                                <i class="fa fa-print"></i>&nbsp; Print Info
                            </a>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-10 col-sm-12 col-xs-12 col-centered" id="entry-report">
                            <?php if(Session::has('entryInfo')): ?>

                                <?php echo Session::get('entryInfo'); ?>

                            <?php endif; ?>
                        </div>

                    </div>
                </div>
            </div>
        </section>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('template.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>