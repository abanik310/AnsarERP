<?php $__env->startSection('title','404 Error'); ?>
<?php $__env->startSection('breadcrumb'); ?>
    <?php /*<?php echo Breadcrumbs::generate(); ?>*/ ?>
    <?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <div>
        <!-- Content Header (Page header) -->

        <!-- Main content -->
        <section class="content">
            <div class="error-page">
                <h2 class="headline text-yellow" style="margin-top: -10px"> 404</h2>

                <div class="error-content">
                    <h3><i class="fa fa-warning text-yellow"></i> Oops! Page not found.</h3>

                    <p style="margin-top: 20px">
                        We could not find the page you were looking for.
                        Meanwhile, you may <a href="<?php echo e(URL::to('/')); ?>">return to dashboard</a>
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