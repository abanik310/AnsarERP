<?php $__env->startSection('title','Range Setting'); ?>
<?php /*<?php $__env->startSection('small_title','DG'); ?>*/ ?>
<?php $__env->startSection('breadcrumb'); ?>
    <?php echo Breadcrumbs::render('range.index'); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <?php if(Session::has('success')): ?>
        <div class="alert alert-success">
            <i class="fa fa-check"></i>&nbsp;<?php echo e(Session::get('success')); ?>

        </div>
        <?php endif; ?>
    <?php if(Session::has('error')): ?>
        <div class="alert alert-danger">
            <i class="fa fa-remove"></i>&nbsp;<?php echo e(Session::get('error')); ?>

        </div>
    <?php endif; ?>
    <section class="content">
        <div class="box box-solid">
            <div class="box-header">
                <a href="<?php echo e(URL::route('HRM.range.create')); ?>" title="New Range" class="btn btn-primary btn-sm pull-right">
                    <i class="fa fa-plus"></i>&nbsp;New Range
                </a>
                <h3 class="box-title">Total Range : <?php echo e(count($data)); ?></h3>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th>#</th>
                            <th>Range Name ENG</th>
                            <th>Range Name BNG</th>
                            <th>Range Code</th>
                            <th>Action</th>
                        </tr>
                        <?php $i=0; ?>
                        <?php $__empty_1 = true; foreach($data as $range): $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($i++); ?></td>
                                <td><?php echo e($range->division_name_eng); ?></td>
                                <td><?php echo e($range->division_name_bng); ?></td>
                                <td><?php echo e($range->division_code); ?></td>
                                <td>
                                    <a title="Edit" href="<?php echo e(URL::route('HRM.range.edit',['range'=>$range->id])); ?>" class="btn btn-primary btn-xs">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; if ($__empty_1): ?>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('template.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>