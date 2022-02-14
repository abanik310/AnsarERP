<aside class="main-sidebar" ng-controller="MenuController">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">

        <ul class="sidebar-menu">
            <li>
                <a href="<?php echo e(URL::to('/AVURP')); ?>">
                    <i class="fa fa-dashboard"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <?php echo $__env->make('HRM::Partial_view.partial_menu',['menus'=>config('menu.avurp')], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
        </ul>
    </section>
    <!-- /.sidebar -->
    <script>
        $(window).load(function(){
            var l = $('.sidebar-menu').children('li');
            function removeMenu(m){

                m.each(function () {
                    //console.log({parent: $.trim($(this).parents('li').eq($(this).parents('li').length-1).children('a').text()),children: m.text()})
                    //alert($(this).children('ul').length+" "+$(this).children('ul').children('li').length)
                    if($(this).children('ul').length>0) {
                        if ($(this).children('ul').children('li').length > 0) {
                            removeMenu($(this).children('ul').children('li'));
                        }
                        else if ($(this).children('ul').children('li').length <= 0) {
                            // alert(m.length)
                            $(this).remove();
                        }
                    }
                })
            }
            removeMenu(l)
        })
    </script>
</aside>