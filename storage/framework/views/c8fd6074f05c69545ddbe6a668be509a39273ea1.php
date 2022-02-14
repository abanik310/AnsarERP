<?php /*User: Shreya*/ ?>
<?php /*Date: 10/15/2015*/ ?>
<?php /*Time: 10:49 AM*/ ?>


<?php $__env->startSection('title','All notification'); ?>
<?php $__env->startSection('content'); ?>

    <div>
        <div id="all-loading"
             style="position:fixed;width: 100%;height: 100%;background-color: rgba(255, 255, 255, 0.27);z-index: 100; margin-left: 30%; display: none; overflow: hidden">
            <div style="position: fixed;width: 20%;height: auto;margin: 20% auto;text-align: center;background: #FFFFFF">
                <img class="img-responsive" src="<?php echo e(asset('dist/img/loading-data.gif')); ?>"
                     style="position: relative;margin: 0 auto">
                <h4>Loading....</h4>
            </div>

        </div>
        <!-- Content Header (Page header) -->

        <!-- Main content -->
        <?php if(Session::has('success')): ?>
            <div style="padding: 10px 20px 0 20px;">
                <div class="alert alert-success">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <span class="glyphicon glyphicon-ok"></span> <?php echo e(Session::get('success')); ?>

                </div>
            </div>
        <?php endif; ?>
        <?php if(Session::has('error')): ?>
            <div style="padding: 10px 20px 0 20px;">
                <div class="alert alert-danger">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <span class="glyphicon glyphicon-warning-sign"></span> <?php echo e(Session::get('error')); ?>

                </div>
            </div>
        <?php endif; ?>
        <section class="content">
            <?php $i=1; ?>
            <div class="box box-solid">
                <div class="box-body">
                    <table class="table table-stripped table-bordered">
                        <tr>
                            <th>Sl. no</th>
                            <th>User</th>
                            <th>Request Date</th>
                            <th>Action</th>
                        </tr>
                        <?php $__empty_1 = true; foreach(Notification::getAllForgetPasswordNotification() as $notification): $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($i++); ?></td>
                                <td><?php echo e($notification->user_name); ?></td>
                                <td><?php echo e(\Carbon\Carbon::parse($notification->created_at)->format('d M, y')); ?></td>
                                <td><a href="<?php echo e(URL::to('/change_password/'.$notification->user_name)); ?>" class="btn btn-info btn-xs">Change password</a> or <a href="<?php echo e(URL::to((request()->route()?request()->route()->getPrefix():'').'/remove_request/'.$notification->user_name)); ?>" class="btn btn-xs btn-danger">Remove request</a>
                            </tr>
                            <?php endforeach; if ($__empty_1): ?>
                            <tr>
                                <td colspan="4">No request available</td>
                            </tr>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
            <!-- /.box
            -footer -->
            <!--Modal Close-->
            <!-- /.row -->
        </section>
        <!-- /.content -->
    </div><!-- /.content-wrapper -->
<?php $__env->stopSection(); ?>
<?php echo $__env->make('template.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>