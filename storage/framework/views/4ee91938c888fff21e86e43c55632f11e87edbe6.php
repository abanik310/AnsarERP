<?php /*User: Shreya*/ ?>
<?php /*Date: 12/5/2015*/ ?>
<?php /*Time: 12:23 PM*/ ?>


<?php $__env->startSection('title','Edit Unit Information'); ?>
<?php $__env->startSection('breadcrumb'); ?>
    <?php echo Breadcrumbs::render('unit_information_edit',$unit_info->id); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <div>
        <?php echo Form::model($unit_info,array('route' => ['HRM.unit.update',$unit_info->id],'method'=>'patch', 'class' => 'form-horizontal','name' => 'unitForm',)); ?>

                <!-- Content Header (Page header) -->

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <!-- left column -->
                <div class="col-lg-6 col-centered">
                    <!-- general form elements -->

                    <!-- Input addon -->

                    <div class="box box-info">
                        <div class="box-body">
                            <div class="box-body">
                                <div class="form-group">
                                    <?php echo Form::label('division_id', 'Division:', $attributes = array('class' => 'col-sm-4 control-label')); ?>

                                    <div class="col-sm-8">
                                        <?php echo Form::select('division_id', $division,null, $attributes = array('class' => 'form-control')); ?>

                                    </div>
                                </div>
                                <input type="hidden" name="id" class="form-control" value="<?php echo e($unit_info->id); ?>">

                                <div class="form-group required">
                                    <?php echo Form::label('unit_name_eng', 'Unit Name:', $attributes = array('class' => 'col-sm-4 control-label')); ?>

                                    <div class="col-sm-8 <?php if($errors->has('unit_name_eng')): ?> has-error <?php endif; ?>">
                                        <?php echo Form::text('unit_name_eng', $value = (Request::old('unit_name_eng')) ? Request::old('unit_name_eng') : $unit_info->unit_name_eng, $attributes = array('class' => 'form-control', 'id' => 'unit_name_eng', 'placeholder' => 'Enter Unit Name in English')); ?>

                                        <p class="text-danger"><?php echo e($errors->first('unit_name_eng')); ?></p>
                                    </div>
                                </div>
                                <div class="form-group required">
                                    <?php echo Form::label('unit_name_bng', 'জেলার নাম:', $attributes = array('class' => 'col-sm-4 control-label')); ?>

                                    <div class="col-sm-8 <?php if($errors->has('unit_name_bng')): ?> has-error <?php endif; ?>">
                                        <?php echo Form::text('unit_name_bng', null, $attributes = array('class' => 'form-control', 'id' => 'unit_name_bng', 'placeholder' => 'জেলার নাম লিখুন বাংলায়')); ?>

                                        <p class="text-danger"><?php echo e($errors->first('unit_name_bng')); ?></p>
                                    </div>
                                </div>
                                <div class="form-group required">
                                    <?php echo Form::label('unit_code', 'Unit Code:', $attributes = array('class' => 'col-sm-4 control-label')); ?>

                                    <div class="col-sm-8 <?php if($errors->has('unit_code')): ?> has-error <?php endif; ?>">
                                        <?php echo Form::text('unit_code', null, $attributes = array('class' => 'form-control', 'id' => 'unit_code', 'placeholder' => 'Enter Unit Code in English')); ?>

                                        <p class="text-danger"><?php echo e($errors->first('unit_code')); ?></p>
                                    </div>
                                </div>
                            </div>
                            <!-- /.box-body -->
                        </div>
                        <!-- /.box-body -->

                    </div>
                    <!-- /.box -->
                    <div>
                        <button type="submit" class="btn btn-info pull-right"
                                ng-disabled="unitForm.unit_name_eng.$error.required||unitForm.unit_name_bng.$error.required||unitForm.unit_code.$error.required">
                            Update
                        </button>
                    </div>
                    <!-- /.box-footer -->
                    <?php echo Form::close(); ?>


                </div>
                <!--/.col (left) -->
                <!-- right column -->

            </div>
            <!-- /.row -->
        </section>
        <!-- /.content -->
    </div><!-- /.content-wrapper -->
<?php $__env->stopSection(); ?>
<?php echo $__env->make('template.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>