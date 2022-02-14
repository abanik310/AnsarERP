<?php $__env->startSection('title','Applicant Quota Type'); ?>
<?php $__env->startSection('breadcrumb'); ?>
    <?php echo Breadcrumbs::render('recruitment.quota_type.index'); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <section class="content">
        <?php if(Session::has('success_message')): ?>
            <div class="alert alert-success">
                <?php echo e(Session::get('success_message')); ?>

            </div>
        <?php elseif(Session::has('error_message')): ?>
            <div class="alert alert-danger">
                <?php echo e(Session::get('error_message')); ?>

            </div>
        <?php endif; ?>
        <div class="box box-solid">
            <div class="box-body">
                <div class="row">
                    <div class="col-sm-6">
                        <h3>All Applicant Quota Type</h3>
                    </div>
                    <div class="col-sm-6">
                        <a href="<?php echo e(URL::route('recruitment.quota_type.create')); ?>" class="btn btn-info btn-sm pull-right"
                           style="margin-top: 20px">Create New TYpe</a>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tr>
                            <th>Sl. No</th>
                            <th>Quota Type Name Eng</th>
                            <th>Quota Type Name Bng</th>
                            <th>Action</th>
                        </tr>
                        <?php $i = 1; ?>
                        <?php $__empty_1 = true; foreach($quotas as $quota): $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($i++); ?>

                                    <?php if($quota->trashed()): ?>
                                        <span class="label label-danger">deleted</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo e($quota->quota_name_eng); ?></td>
                                <td><?php echo e($quota->quota_name_bng); ?></td>
                                <?php if($quota->trashed()): ?>
                                    <td>
                                        <?php echo Form::model($quota,['method'=>'delete','route'=>['recruitment.quota_type.destroy',$quota],'style'=>'float:left;margin-right:5px']); ?>

                                        <input type="hidden" name="type" value="1"/>
                                        <button class="btn btn-danger btn-xs">Delete Permanently</button>
                                        <?php echo Form::close(); ?>

                                        <?php echo Form::model($quota,['route'=>['recruitment.quota.update',$quota],'method'=>'patch','style'=>'float:left']); ?>

                                        <input type="hidden" name="type" value="1"/>
                                        <button class="btn btn-success btn-xs">Restore</button>
                                        <?php echo Form::close(); ?>

                                        <span style="clear: both"></span>
                                    </td>
                                <?php else: ?>
                                    <td>
                                        <a href="<?php echo e(URL::route('recruitment.quota.edit',['id'=>$quota->id])); ?>"
                                           class="btn btn-info btn-xs pull-left" style="margin-right:5px;margin-top: 1.4px;">Edit Quota
                                            Type</a>
                                        <?php echo Form::model($quota,['method'=>'delete','route'=>['recruitment.quota_type.destroy',$quota],'style'=>'float:left;margin-right:5px']); ?>

                                        <input type="hidden" name="type" value="0"/>
                                        <button class="btn btn-danger btn-xs">Delete</button>
                                        <?php echo Form::close(); ?>

                                        <?php echo Form::model($quota,['method'=>'delete','route'=>['recruitment.quota_type.destroy',$quota],'style'=>'float:left']); ?>

                                        <input type="hidden" name="type" value="1"/>
                                        <button class="btn btn-danger btn-xs">Delete Permanently</button>
                                        <?php echo Form::close(); ?>

                                        <span style="clear: both"></span>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; if ($__empty_1): ?>
                            <tr>
                                <td colspan="4" class="bg-warning">No Quota Type Available</td>
                            </tr>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('template.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>