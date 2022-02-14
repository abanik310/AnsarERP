<?php $i=1 ?>
<div class="table-responsive">
    <table class="table table-bordered">
        <tr>
            <th>SL. No</th>
            <th>Job Circular Name</th>
            <th>Selection Date</th>
            <th>Selection Place</th>
            <th>Written Date</th>
            <th>Viva Date</th>
            <th>Written Viva Place</th>
            <th>Units</th>
            <th style="width: 100px;">Action</th>
        </tr>
        <?php $__empty_1 = true; foreach($data as $d): $__empty_1 = false; ?>
            <tr>
                <td><?php echo e($i++); ?></td>
                <td><?php echo e($d->circular->circular_name); ?></td>
                <td><?php echo e($d->selection_date.' '.$d->selection_time); ?></td>
                <td><?php echo e($d->selection_place); ?></td>
                <td><?php echo e($d->written_date.' '.$d->written_time); ?></td>
                <td><?php echo e($d->viva_date.' '.$d->viva_time); ?></td>
                <td><?php echo e($d->written_viva_place); ?></td>
                <td>
                <?php foreach($d->units()->pluck('unit_name_bng') as $u): ?>
                    <?php echo e($u); ?>,
                    <?php endforeach; ?>
                </td>
                <td>
                    <a class="btn btn-primary btn-xs" href="<?php echo e(URL::route('recruitment.exam-center.edit',['id'=>$d->id])); ?>">
                        <i class="fa fa-edit"></i>
                    </a>
                    <?php echo Form::open(['route'=>['recruitment.exam-center.destroy',$d->id],'method'=>'delete','style'=>'display:inline-block']); ?>

                        <button class="btn btn-danger btn-xs">
                            <i class="fa fa-trash"></i>
                        </button>
                    <?php echo Form::close(); ?>

                </td>
            </tr>
        <?php endforeach; if ($__empty_1): ?>
            <tr>
                <td class="bg-warning" colspan="9">No Exam Center Available</td>
            </tr>
        <?php endif; ?>
    </table>
</div>