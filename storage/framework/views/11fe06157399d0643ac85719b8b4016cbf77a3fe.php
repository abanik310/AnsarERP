<?php /*User: Shreya*/ ?>
<?php /*Date: 10/14/2015*/ ?>
<?php /*Time: 11:00 AM*/ ?>


<?php $__env->startSection('title','Session Information'); ?>
<?php $__env->startSection('small_title'); ?>
    <a class="btn btn-primary btn-sm" href="<?php echo e(URL::to('HRM/session')); ?>">
        <span class="glyphicon glyphicon-plus"></span> Add new Session
    </a>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('breadcrumb'); ?>
    <?php echo Breadcrumbs::render('session_information_list'); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>


    <div>
        <?php if(Session::has('success_message')): ?>
            <div style="padding: 10px 20px 0 20px;">
                <div class="alert alert-success">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <span class="glyphicon glyphicon-ok"></span> <?php echo e(Session::get('success_message')); ?>

                </div>
            </div>
            <?php endif; ?>
                    <!-- Content Header (Page header) -->

            <!-- Main content -->
            <section class="content">
                <div class="box box-solid">

                    <div class="box-body">
                        <table id="example2" class="table table-bordered table-hover table-striped">
                            <thead>
                            <tr>
                                <th>Session Year</th>
                                <th>Starting Session Month</th>
                                <th>Ending Session Month</th>
                                <th>Session Name</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if(count($session_info)>0): ?>
                                <?php foreach($session_info as $session_infos): ?>
                                    <tr>
                                        <td><?php echo e($session_infos->session_year); ?></td>
                                        <td><?php echo e($session_infos->session_start_month); ?></td>
                                        <td><?php echo e($session_infos->session_end_month); ?></td>
                                        <td><?php echo e($session_infos->session_name); ?></td>

                                        <td>
                                            <a href="<?php echo e(URL::to('HRM/session-edit/'.$session_infos->id)."/"); ?><?php if(Request::exists('page')): ?><?php echo e(Request::get('page')); ?><?php else: ?><?php echo e('1'); ?><?php endif; ?>"
                                               class="btn btn-primary btn-xs" title="Edit"><span
                                                        class="glyphicon glyphicon-edit"></span></a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr class="warning">
                                    <td colspan="5">No information found.</td>
                                </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <!-- /.box-body -->
                    <div class="table_pagination">
                        <?php echo $session_info->render(); ?>

                    </div>
                </div>
                <!-- /.row -->
            </section>
            <!-- /.content -->
    </div><!-- /.content-wrapper -->
    <script>
        function check() {
            return confirm('Are you sure to delete this entry');
        }
    </script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('template.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>