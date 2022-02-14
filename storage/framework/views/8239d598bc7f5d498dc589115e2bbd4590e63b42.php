<?php $__env->startSection('title','Disease Information'); ?>
<?php $__env->startSection('small_title'); ?>
    <a class="btn btn-primary" href="<?php echo e(URL::to('HRM/add_disease')); ?>">
        <span class="glyphicon glyphicon-plus"></span> Add New Disease
    </a>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('breadcrumb'); ?>
    <?php echo Breadcrumbs::render('disease_information_list'); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>

    <?php $i = 1; ?>
    <div>
        <?php if(Session::has('success_message')): ?>
            <div style="padding: 10px 20px 0 20px;">
                <div class="alert alert-success">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <span class="glyphicon glyphicon-ok"></span> <?php echo e(Session::get('success_message')); ?>

                </div>
            </div>
        <?php endif; ?>
        <?php if(Session::has('error_message')): ?>
            <div style="padding: 10px 20px 0 20px;">
                <div class="alert alert-danger">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <?php echo e(Session::get('error_message')); ?>

                </div>
            </div>
            <?php endif; ?>
                    <!-- Content Header (Page header) -->
            <!-- Main content -->
            <section class="content">

                <div class="box box-solid">

                    <div class="box-body">
                        <table id="unit-table" class="table table-bordered table-hover table-striped">
                            <thead>
                            <tr>
                                <th>Id</th>
                                <th>Disease Name in English</th>
                                <th>Disease Name in Bangla</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if(count($disease_infos)>0): ?>
                                <?php foreach($disease_infos as $disease_info): ?>
                                    <tr>
                                        <th scope="row"><?php echo e($i++); ?></th>
                                        <td><?php echo e($disease_info->disease_name_eng); ?></td>
                                        <td><?php echo e($disease_info->disease_name_bng); ?></td>
                                        <td><a href="<?php echo e(URL::to('HRM/disease_edit/'.$disease_info->id)); ?>"
                                               class="btn btn-primary btn-xs" title="Edit"><span
                                                        class="glyphicon glyphicon-edit"></span></a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr class="warning">
                                    <td colspan="4">No information found.</td>
                                </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <!-- /.box-body -->
                    <div class="table_pagination">
                        <?php echo $disease_infos->render(); ?>

                    </div>
                </div>
                <!-- /.row -->
            </section>
            <!-- /.content -->
    </div><!-- /.content-wrapper -->
    <script>
        $("#unit-table").sortTable({
            exclude: 7
        })
    </script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('template.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>