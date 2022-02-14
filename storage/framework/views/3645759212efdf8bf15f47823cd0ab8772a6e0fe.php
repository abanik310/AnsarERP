<?php $__env->startSection('title','Mark Distribution'); ?>
<?php $__env->startSection('small_title'); ?>
    <a href="<?php echo e(URL::route('recruitment.mark_distribution.create')); ?>" class="btn btn-primary btn-sm"><i
                class="fa fa-clipboard"></i>&nbsp;Add New Mark Distribution</a>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('breadcrumb'); ?>
    <?php echo Breadcrumbs::render('recruitment.setting.mark_distribution'); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <div>
        <section class="content">
            <?php if(Session::has('session_error')): ?>
                <div class="alert alert-danger">
                    <i class="fa fa-warning"></i>&nbsp;<?php echo e(Session::get('session_error')); ?>

                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                </div>
            <?php elseif(Session::has('session_success')): ?>
                <div class="alert alert-success">
                    <i class="fa fa-check"></i>&nbsp;<?php echo e(Session::get('session_success')); ?>

                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                </div>
            <?php endif; ?>
            <div class="box box-solid">
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h4 class="text text-bold">All Job Circular Mark Distribution</h4>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tr>
                                <th>SL. No</th>
                                <th>Job Circular Title</th>
                                <th>Physical</th>
                                <th>Education & Training</th>
                                <th>Education & Experience</th>
                                <th>Physical & Age</th>
                                <th>Written</th>
                                <th>Viva</th>
                                <th>Action</th>
                            </tr>
                            <?php $i=1;?>
                            <?php $__empty_1 = true; foreach($mark_distributions as $mark_distribution): $__empty_1 = false; ?>
                                <tr>
                                    <td><?php echo e($i++); ?></td>
                                    <td><?php echo e($mark_distribution->circular->circular_name); ?></td>
                                    <td><?php echo e(isset($mark_distribution->physical) ? $mark_distribution->physical : '--'); ?></td>
                                    <td><?php echo e(isset($mark_distribution->edu_training) ? $mark_distribution->edu_training : '--'); ?></td>
                                    <td><?php echo e(isset($mark_distribution->edu_experience) ? $mark_distribution->edu_experience : '--'); ?></td>
                                    <td><?php echo e(isset($mark_distribution->physical_age) ? $mark_distribution->physical_age : $mark_distribution->physical_age?$mark_distribution->physical_age:'--'); ?></td>
                                    <td><?php echo e(isset($mark_distribution->written) ? $mark_distribution->written : '--'); ?></td>
                                    <td><?php echo e(isset($mark_distribution->viva) ? $mark_distribution->viva : '--'); ?></td>
                                    <td>
                                        <a class="btn btn-primary btn-xs" href="<?php echo e(URL::route('recruitment.mark_distribution.edit',['id'=>$mark_distribution->id])); ?>">
                                            <i class="fa fa-edit"></i>&nbsp;Edit
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; if ($__empty_1): ?>
                                <tr>
                                    <td class="warning" colspan="7">No mark distribution available</td>
                                </tr>
                            <?php endif; ?>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('template.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>