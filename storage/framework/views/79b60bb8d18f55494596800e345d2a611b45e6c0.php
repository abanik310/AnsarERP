<table>
    <tr>
        <th>SL. No</th>
        <th>KPI Name</th>
        <th>Organization Type</th>
        <th>Division</th>
        <th>Unit</th>
        <th>Thana</th>
        <th>KPI Address</th>
        <th>KPI Contact No.</th>
        <th>Total Capacity</th>
        <th>Total Embodied Ansar</th>
        <th>Percent</th>
        <th>Vacancy</th>
    </tr>
    <?php $__empty_1 = true; foreach($ansars as $kpi): $__empty_1 = false; ?>
        <tr>
            <td>
                <?php echo e($index++); ?>

            </td>
            <td>
                <?php echo e($kpi->kpi_bng); ?>

            </td>
            <td>
                <?php echo e($kpi->organization_name_bng); ?>

            </td>
            <td>
                <?php echo e($kpi->division_eng); ?>

            </td>
            <td>
                <?php echo e($kpi->unit); ?>

            </td>
            <td>
                <?php echo e($kpi->thana); ?>

            </td>
            <td>
                <?php echo e($kpi->address); ?>

            </td>
            <td>
                <?php echo e($kpi->contact); ?>

            </td>
            <td><?php echo e($kpi->total_ansar_given); ?></td>
            <td><?php echo e($kpi->total_embodied); ?></td>
            <td><?php echo e($kpi->total_ansar_given>0?($kpi->total_embodied*100)/$kpi->total_ansar_given:'infinity'); ?></td>
            <td><?php echo e($kpi->total_ansar_request-$kpi->total_embodied>0?(($kpi->total_ansar_request-$kpi->total_embodied)):0); ?></td>
        </tr>
    <?php endforeach; if ($__empty_1): ?>
    <?php endif; ?>
</table>