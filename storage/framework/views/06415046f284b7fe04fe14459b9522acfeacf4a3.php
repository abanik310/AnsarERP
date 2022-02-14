<?php $__env->startSection('title','401 Error'); ?>
<?php $__env->startSection('content'); ?>
    <div>
        <!-- Content Header (Page header) -->

        <!-- Main content -->
        <section class="content">
            <div class="error-page">
                <h2 class="headline text-yellow" style="margin-top: -10px"> 401</h2>

                <div class="error-content">
                    <h3><i class="fa fa-warning text-yellow"></i> Oops! Unauthorized error.</h3>

                    <p style="margin-top: 20px">
                        You currently not authorized to view this page
                        Meanwhile, you may <a href="<?php echo e(URL::to('HRM')); ?>">return to dashboard</a>
                    </p>

                </div>
                <!-- /.error-content -->
            </div>
            <!-- /.error-page -->
        </section>
        <!-- /.content -->
    </div><!-- /.content-wrapper -->
<?php $__env->stopSection(); ?>
<?php echo $__env->make('template.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>