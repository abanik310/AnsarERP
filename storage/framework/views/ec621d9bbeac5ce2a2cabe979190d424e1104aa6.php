<?php $__env->startSection('title','Entry Report'); ?>
<?php $__env->startSection('breadcrumb'); ?>
    <?php echo Breadcrumbs::render('entry_report',$ansarAllDetails->ansar_id); ?>

    <?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>

    <script>
        GlobalApp.controller("EntryReportController", function ($scope) {
            $scope.changeToLocal = function (v) {
                var b = moment(v);
                return b.locale('bn').format('DD-MMMM-YYYY');
            }
        })
        $(document).ready(function () {
            $("#print-report").on('click', function (e) {
                e.preventDefault();
//                $("#entry-report").find(".col-md-4").addClass("col-xs-4")
//                $("#entry-report").find(".col-md-6").addClass("col-xs-6")
//                $("#entry-report").find(".col-md-12").addClass("col-xs-12")
//                $("#entry-report").find(".col-md-offset-2").addClass("col-xs-offset-2")
//                $("#entry-report").find("img").removeClass("img-thumbnail img-responsive")
//                $("#entry-report table").removeClass("table table-bordered table-stripped borderless")
                var html = $("#entry-report").html();
                $('body').append('<div id="print-area">' + html + '</div>')
                window.print();
                $("#print-area").remove()
            })
        })
    </script>

    <div ng-controller="EntryReportController">
        <section class="content">
            <div class="row " >
                <div class="box box-solid" style="width:80%;margin:0 auto;">
                    <div class="box-body" id="entry-report">
                        <?php echo $__env->make('HRM::Entryform.entry_info',compact('ansarAllDetails','label'), array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                    </div>
                    <div class="box-footer print-hide">
                        <a href="#" id="print-report" class="btn btn-info pull-right">
                            <i class="fa fa-print"></i>&nbsp;Print
                        </a>
                    </div>
                </div>
            </div>
            <?php /*<div style="width: 70%;margin: 12px auto;position: relative;left: -10px">*/ ?>
            <?php /*<button id="print-report" class="btn btn-primary" style="display: block;">*/ ?>
            <?php /*<i class="fa fa-print"></i> Print Report*/ ?>
            <?php /*</button>*/ ?>
            <?php /*</div>*/ ?>
        </section>
    </div>


<?php $__env->stopSection(); ?>
<?php echo $__env->make('template/master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>