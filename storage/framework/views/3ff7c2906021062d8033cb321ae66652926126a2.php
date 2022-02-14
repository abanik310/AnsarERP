<?php foreach($data as $p): ?>
    <li>
        <?php if(isset($p->name)): ?>
            <label class="control-label">
                <div class="styled-checkbox">
                    <?php echo Form::checkBox('permission[]',$p->value,is_array($access)?in_array($p->value,$access):($user->type==11||$user->type==33?true:false),['id'=>implode('_',explode(' ',$p->name))]); ?>

                    <?php /*<?php echo Form::label(,''); ?>*/ ?>
                    <label for="<?php echo e(implode('_',explode(' ',$p->name))); ?>"></label>
                </div>
                <?php echo e($p->name); ?>

            </label>
        <?php elseif(isset($p->text)): ?>
            <ul class="sub-permission">
                <li>
                <span class="title text text-bold">
                    <a class="tree-view" href="#" data-open="0">
                        <i class="fa fa-plus fa-xs"></i>
                    </a>&nbsp;<?php echo e($p->text); ?>

                </span>
                    <ul style="display: none">
                        <?php echo $__env->make('User.permission_partial',['data'=>$p->actions], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                    </ul>
                </li>
            </ul>
        <?php endif; ?>
    </li>
<?php endforeach; ?>