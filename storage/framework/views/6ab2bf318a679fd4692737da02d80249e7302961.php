<?php foreach($menus as $menu_title=>$values): ?>
    <?php if(isset($values['route'])): ?>
        <?php if($values['route']=='#'&&isset($values['children'])&&UserPermission::isMenuExists($values['children'])): ?>
            <li class="treeview">
                <a href="<?php echo e(URL::to($values['route'])); ?>">
                    <i class="fa <?php echo e($values['icon']); ?>"></i>
                    <span><?php echo e($menu_title); ?></span>
                    <i class="fa fa-angle-right pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <?php echo $__env->make('HRM::Partial_view.partial_menu',['menus'=>$values['children']], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                </ul>
            </li>
        <?php elseif($values['route']=='#'&&isset($values['children'])): ?>
            <li class="treeview">
                <a href="<?php echo e(URL::to($values['route'])); ?>">
                    <i class="fa <?php echo e($values['icon']); ?>"></i>
                    <span><?php echo e($menu_title); ?></span>
                    <i class="fa fa-angle-right pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <?php echo $__env->make('HRM::Partial_view.partial_menu',['menus'=>$values['children']], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                </ul>
            </li>
        <?php elseif($values['route']=='#'): ?>
            <li>
                <a href="<?php echo e(URL::to($values['route'])); ?>">
                    <i class="fa <?php echo e($values['icon']); ?>"></i>
                    <span><?php echo e($menu_title); ?></span>
                </a>
            </li>
        <?php elseif(UserPermission::isMenuExists($values['route'])): ?>
            <li>
                <a href="<?php echo e(URL::route($values['route'])); ?>">
                    <i class="fa <?php echo e($values['icon']); ?>"></i>
                    <span><?php echo e($menu_title); ?></span>
                </a>
            </li>
        <?php endif; ?>
    <?php endif; ?>
<?php endforeach; ?>