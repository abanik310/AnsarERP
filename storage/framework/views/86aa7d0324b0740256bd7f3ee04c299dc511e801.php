<?php $__env->startSection('title','User Permission'); ?>
<?php $__env->startSection('breadcrumb'); ?>
    <?php echo Breadcrumbs::render('user_permission',$id); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <script>
        $(document).ready(function (e) {
            var pLength = $("input[type='checkbox']").length - 1;

            $('body').on('click', '.toggle-view', function (e) {
                e.preventDefault();
                $(this).children('img').toggleClass('rotate-img-up rotate-img-down')
                $($(this).parents('div')[0]).siblings('.p_continer').slideToggle(300)
            })
            checkLength();
            $("input[type='checkbox']:not(#all)").on('change', function () {
                checkLength();
            })
            $("#all").on('change', function () {
                if (this.checked) {
                    $("input[type='checkbox']:not(#all)").each(function () {
                        $(this).prop('checked', true);
                    })
                }
                else {
                    $("input[type='checkbox']:not(#all)").each(function () {
                        $(this).prop('checked', false);
                    })
                }
            })
            function checkLength() {
                var checked = $("input[type='checkbox']:not(#all):checked").length;
                if (checked == pLength) $("#all").prop('checked', true);
                else $("#all").prop('checked', false);
            }

            $(".permission-group").each(function () {
                var t = $(this).find("input[type='checkbox']").length;
                var c = $(this).find("input[type='checkbox']:checked").length;
                $(this).children('.legend').children('span').text(`(${c} of ${t})`);
            })
            $(".empty-class").each(function(){
                var h = $(this).html();
                if(!h.trim()) $(this).addClass("hide")
            })
        })
    </script>
    <div>
        <form action="<?php echo e(action('UserController@updatePermission',['id'=>$id])); ?>" method="post">
            <?php echo e(csrf_field()); ?>

            <section class="content">
                <div class="box box-solid">
                    <div class="box-header">
                        <p>Edit permission of : <strong><?php echo e($user->user_name); ?>(<?php echo e($user->usertype->type_name); ?>)</strong>
                        </p>
                        <label class="control-label">
                            Grant All Permission &nbsp;
                            <div class="styled-checkbox">
                                <?php echo Form::checkBox('permit_all','permit_all',null,['id'=>'all']); ?>

                                <label for="all"></label>
                            </div>

                        </label>
                        <button type="submit" class="btn btn-primary pull-right">
                            <i class="fa fa-save"></i> Save Permission
                        </button>
                    </div>
                    <div class="box-body">
                        <div class="row" style="">
                            <?php for($j=0;$j<3;$j++): ?>
                                <div class="col-lg-4 empty-class">
                                    <?php for($i=$j;$i<count($routes);$i+=3): ?>
                                        <?php if($user->type==111): ?>
                                            <?php if(!strcasecmp($routes[$i]->root,"Recruitment")||!strcasecmp($routes[$i]->root,"Common Permission")): ?>
                                                <div style="margin-top: 5px" class="permission-group">
                                                    <div class="legend">
                                                        <?php echo e($routes[$i]->root); ?><span
                                                                style="color: black;font-weight:bold;font-size: 12px;margin-left: 10px"></span>
                                                        <button class="btn btn-default btn-xs pull-right toggle-view">
                                                            <img src="<?php echo e(asset('dist/img/down_icon.png')); ?>"
                                                                 class="rotate-img-down"
                                                                 style="width: 18px;height: 20px;">
                                                        </button>
                                                    </div>
                                                    <div class="box-body p_continer"
                                                         style="background-color: #FFFFFF;display: none">
                                                        <ul class="permission-list">
                                                            <?php echo $__env->make('User.permission_partial',['data'=>$routes[$i]->children], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                                                        </ul>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <div style="margin-top: 5px" class="permission-group">
                                                <div class="legend">
                                                    <?php echo e($routes[$i]->root); ?><span
                                                            style="color: black;font-weight:bold;font-size: 12px;margin-left: 10px"></span>
                                                    <button class="btn btn-default btn-xs pull-right toggle-view">
                                                        <img src="<?php echo e(asset('dist/img/down_icon.png')); ?>"
                                                             class="rotate-img-down"
                                                             style="width: 18px;height: 20px;">
                                                    </button>
                                                </div>
                                                <div class="box-body p_continer"
                                                     style="background-color: #FFFFFF;display: none">
                                                    <ul class="permission-list">
                                                        <?php echo $__env->make('User.permission_partial',['data'=>$routes[$i]->children], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                                                    </ul>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    <?php endfor; ?>
                                </div>
                            <?php endfor; ?>

                        </div>
                    </div>
                </div>
            </section>
        </form>
    </div>
    <script>
        $(document).ready(function () {
            $('body').on('click', ".tree-view", function (e) {
                e.preventDefault();
                var i = $(this).attr('data-open');
                if (parseInt(i)) {
                    $(this).children('i').addClass('fa-minus').removeClass('fa-plus')
                    $(this).parents('span').siblings('ul').slideToggle(200);
                    $(this).attr('data-open', 0)
                }
                else {
                    $(this).children('i').addClass('fa-plus').removeClass('fa-minus')
                    $(this).parents('span').siblings('ul').slideToggle(200);
                    $(this).attr('data-open', 1)
                }
            })
        })
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('template.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>