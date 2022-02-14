<?php $i = (intVal($unions->currentPage() - 1) * $unions->perPage()) + 1; ?>
<div class="table-responsive">

    <table class="table table-bordered table-condensed">
        <caption><span style="font-size: 20px;">Total Unions(<?php echo e($unions->total()); ?>)</span>
            <?php if(count($unions)): ?>
                <div class="pull-right" style="margin: -20px 0" paginate ref="loadPage(url)">
                    <?php echo e($unions->render()); ?>

                </div>
            <?php endif; ?>
        </caption>

        <tr>
            <th>#</th>
            <th>Union Name(English)</th>
            <th>Union Name(Bangla)</th>
            <th>Union Code</th>
            <th>Division</th>
            <th>District</th>
            <th>Thana</th>
            <th>Action</th>

        </tr>

        <?php if(count($unions)): ?>
            <?php foreach($unions as $union): ?>
                <tr>
                    <td><?php echo e($i++); ?></td>
                    <td><?php echo e($union->union_name_eng); ?></td>
                    <td><?php echo e($union->union_name_bng); ?></td>
                    <td><?php echo e($union->code); ?></td>
                    <td><?php echo e($union->division->division_name_bng); ?></td>
                    <td><?php echo e($union->unit->unit_name_bng); ?></td>
                    <td><?php echo e($union->thana->thana_name_bng); ?></td>
                    <td>
                        <a href="<?php echo e(URL::route('HRM.union.edit',$union->id)); ?>" class="btn btn-xs btn-primary">
                            <i class="fa fa-edit"></i>&nbsp;Edit
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="12" class="bg-warning">
                    No Union found
                </td>
            </tr>
        <?php endif; ?>

    </table>
</div>
<?php if(count($unions)): ?>
    <div class="pull-right" style="margin: -20px 0" paginate ref="loadPage(url)">
        <?php echo e($unions->render()); ?>

    </div>
<?php endif; ?>
