
<?php $i=1; ?>
<?php foreach($data as $d): ?>

    <span class="bg-green" style="padding: 1px 5px;border-radius: 5px;margin-bottom: 5px;display: inline-block"><?php echo e($d); ?></span>
    <?php if($i++%4==0): ?> <br> <?php endif; ?>
<?php endforeach; ?>