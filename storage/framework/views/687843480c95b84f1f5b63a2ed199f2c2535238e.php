<?php $__env->startSection('title','Application Instruction'); ?>
<?php $__env->startSection('breadcrumb'); ?>
    <?php echo Breadcrumbs::render('recruitment.setting.application_instruction'); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <section class="content">
        <?php if(Session::has('success')): ?>
            <div class="alert alert-success">
                <?php echo Session::get('success'); ?>

            </div>
        <?php elseif(Session::has('error')): ?>
            <div class="alert alert-danger">
                <?php echo Session::get('error'); ?>

            </div>
        <?php endif; ?>
        <div class="box box-solid">

            <div class="box-body">
                <div class="row" style="margin-bottom: 20px">
                    <div class="col-sm-12">
                        <span class="text-bold" style="font-size: 20px">All Instruction</span>
                        <a href="<?php echo e(URL::route('recruitment.instruction.create')); ?>" class="btn btn-primary pull-right">
                            <i class="fa fa-plus"></i>&nbsp;New Instruction
                        </a>
                    </div>
                </div>
                <div class="panel-group" id="accordion">
                    <?php $__empty_1 = true; foreach($instructions as $instruction): $__empty_1 = false; ?>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#accordion" href="#<?php echo e($instruction->type); ?>">
                                        <?php echo e(ucwords(implode(" ",explode("_",$instruction->type)))); ?></a>
                                    <a href="<?php echo e(URL::route('recruitment.instruction.edit',['id'=>$instruction->id])); ?>" class="btn btn-link" title="edit instruction">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                </h4>
                            </div>
                            <div id="<?php echo e($instruction->type); ?>" class="panel-collapse collapse">
                                <div class="panel-body">
                                    <?php echo $instruction->instruction; ?>

                                </div>
                            </div>
                        </div>
                        <?php endforeach; if ($__empty_1): ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('template.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>