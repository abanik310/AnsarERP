<?php $__env->startSection('title','Sub Training Setting'); ?>
<?php /*<?php $__env->startSection('small_title','DG'); ?>*/ ?>
<?php $__env->startSection('breadcrumb'); ?>
    <?php echo Breadcrumbs::render('range.index'); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <?php if(Session::has('success_messsage')): ?>
        <div class="alert alert-success">
            <i class="fa fa-check"></i>&nbsp;<?php echo e(Session::get('success_message')); ?>

        </div>
        <?php endif; ?>
    <?php if(Session::has('error_messsage')): ?>
        <div class="alert alert-danger">
            <i class="fa fa-remove"></i>&nbsp;<?php echo e(Session::get('error_message')); ?>

        </div>
    <?php endif; ?>
    <section class="content">
        <div class="box box-solid">
            <div class="box-header">
                <a href="<?php echo e(URL::route('HRM.sub_training.create')); ?>" title="New Training Info" class="btn btn-primary btn-sm pull-right">
                    <i class="fa fa-plus"></i>&nbsp;New Sub Training Info
                </a>
                <h3 class="box-title">Total : <?php echo e(count($data)); ?></h3>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th>#</th>
                            <th>Main Training Name</th>
                            <th>Sub Training Name ENG</th>
                            <th>Sub Training Name BNG</th>
                            <th>Action</th>
                        </tr>
                        <?php $i=1; ?>
                        <?php $__empty_1 = true; foreach($data as $training): $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($i++); ?></td>
                                <td><?php echo e($training->mainTraining->training_name_bng); ?></td>
                                <td><?php echo e($training->training_name_eng); ?></td>
                                <td><?php echo e($training->training_name_bng); ?></td>
                                <td>
                                    <a title="Edit" href="<?php echo e(URL::route('HRM.sub_training.edit',['sub_training'=>$training->id])); ?>" class="btn btn-primary btn-xs">
                                        <i class="fa fa-edit"></i>&nbsp;Edit
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; if ($__empty_1): ?>
                            <tr>
                                <td colspan="5" class="bg-warning">No Data Available</td>
                            </tr>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('template.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>