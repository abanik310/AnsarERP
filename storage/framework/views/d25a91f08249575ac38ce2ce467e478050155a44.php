<?php $__env->startSection('title','Change user password'); ?>
<?php $__env->startSection('breadcrumb'); ?>
    <?php /*<?php echo Breadcrumbs::render('edit_user',$id); ?>*/ ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <script>
    </script>
    <section class="content">
        <div class="box box-solid">
            <div class="box-body">
                <div class="row" >
                    <div class="col-sm-4 col-sm-offset-4" style="margin-bottom: 20px">
                        <?php if(Session::has('success')): ?>
                            <div class="alert alert-success" style="padding: 5px 10px">
                                <i class="fa fa-check"></i>&nbsp;&nbsp;<?php echo e(Session::get('success')); ?>

                            </div>
                            <?php endif; ?>
                            <?php if(Session::has('error')): ?>
                                <div class="alert alert-danger" style="padding: 5px 10px">
                                    <i class="fa fa-warning"></i>&nbsp;&nbsp;<?php echo e(Session::get('error')); ?>

                                </div>
                            <?php endif; ?>
                        <h4>Change user password for : <?php echo e($user); ?></h4>
                        <form id="user-password-form" action="<?php echo e(URL::route('handle_change_password')); ?>" method="post">
                            <?php echo csrf_field(); ?>

                            <input type="hidden" name="user" value="<?php echo e($user); ?>">
                            <div class="form-group has-feedback">
                                <input type="password" name="password" value="" class="form-control" placeholder="Enter password"/>
                                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                                <?php if($errors->has('password')): ?>
                                <p class="text text-danger"><i class="fa fa-warning"></i>&nbsp;<?php echo e($errors->first('password')); ?></p>
                                    <?php endif; ?>
                            </div>
                            <div class="form-group has-feedback">
                                <input type="password" name="c_password" value="" class="form-control" placeholder="Type password again"/>
                                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                                <?php if($errors->has('c_password')): ?>
                                    <p class="text text-danger"><i class="fa fa-warning"></i>&nbsp;<?php echo e($errors->first('c_password')); ?></p>
                                <?php endif; ?>
                            </div>
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    Change password
                                </button>
                                <div class="clearfix"></div>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('template.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>