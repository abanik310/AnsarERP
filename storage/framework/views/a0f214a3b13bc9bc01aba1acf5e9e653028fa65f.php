<?php $__env->startSection('title','System Setting'); ?>
<?php $__env->startSection('breadcrumb'); ?>
    <?php echo Breadcrumbs::render('system_setting'); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>

    <div>
        <section class="content">
            <?php if(Session::has('success')): ?>
                <div class="alert alert-success"><?php echo e(Session::get('success')); ?></div>
            <?php elseif(Session::has('error')): ?>
                <div class="alert alert-error"><?php echo e(Session::get('error')); ?></div>
            <?php endif; ?>
            <div class="box box-solid">
                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <tr>
                                        <th>Setting Name</th>
                                        <th>Setting Value</th>
                                        <th>Setting Description</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                    <?php if(count($data)): ?>
                                        <?php foreach($data as $d): ?>
                                            <tr>
                                                <td><?php echo e($d->setting_name); ?></td>
                                                <td><?php echo $d->getValueAsString(); ?></td>
                                                <td><?php echo e($d->description); ?></td>
                                                <td><?php echo e($d->active?"active":"inactive"); ?></td>
                                                <td>
                                                    <a href="<?php echo e(URL::route('system_setting_edit',['id'=>$d->id])); ?>"
                                                       class="btn btn-primary btn-xs">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td class="bg bg-warning" colspan="5">No data available</td>
                                        </tr>
                                    <?php endif; ?>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('template.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>