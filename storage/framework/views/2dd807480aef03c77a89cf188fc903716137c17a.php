<?php $__env->startSection('title','Upload Bank Info'); ?>
<?php $__env->startSection('breadcrumb'); ?>
    <?php /*    <?php echo Breadcrumbs::render('upload_photo_original'); ?>*/ ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <section class="content">
        <div class="box box-solid">
            <div class="box-header">
                <h3 class="box-title">Upload File</h3>
            </div>
            <div class="box-body">
                <?php if(!empty($message)): ?>
                    <div class="alert alert-success" role="alert">
                        <?php echo $message; ?>

                    </div>
                <?php endif; ?>
                <p style="text-align: right;position: absolute;right: 1%;">
                    <a class="btn btn-primary"
                       href="<?php echo e(asset('sample_data_file/default_format.xls')); ?>">Download default format</a>
                </p>

                <p style="color: red;">Please check and re-check file(s) format and column structure before upload.<br/> Download
                    the sample file first and make sure file(s) are structured same.</p>

                <form id="bulk_bank_account_info_form" method="post" class="form"
                      action="<?php echo e(URL::to("HRM/bulk-upload-bank-info")); ?>" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>

                    <input type="submit" name="Upload" class="btn btn-primary" style="margin-bottom: 1%;">
                    <input type="file" name="bulk_bank_account_info[]" id="bulk_bank_account_info" multiple>
                    <?php if(isset($errors)&&$errors->first('bulk_bank_account_info')): ?>
                        <p class="text text-danger"><?php echo e($errors->first('bulk_bank_account_info')); ?></p>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('template.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>