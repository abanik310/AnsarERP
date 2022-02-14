<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <ul class="sidebar-menu">
            <?php /*<li class="header">MAIN NAVIGATION</li>*/ ?>
            <li class="treeview">
                <a href="<?php echo e(URL::to('/')); ?>">
                    <i class="fa fa-dashboard"></i> <span>Dashboard</span>
                </a>

            </li>
            <?php if(auth()->user()->type!=111): ?>
                <li>
                    <a href="<?php echo e(URL::to('HRM')); ?>">
                        <i class="fa fa-users"></i>
                        <span>Human Resource Management</span>
                    </a>
                </li>
                <li class="disable_menu">
                    <a href="<?php echo e(URL::to('SD')); ?>">
                        <i class="fa  fa-money"></i>
                        <span>Salary Disbursement</span>
                    </a>
                </li>
                <li class="disable_menu">
                    <a href="#">
                        <i class="fa fa-gears"></i>
                        <span>Ansar Deployment Application<br/>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Processing System</span>
                    </a>
                </li>
            <?php endif; ?>
            <li>
                <a href="<?php echo e(URL::to('recruitment')); ?>">
                    <i class="fa fa-user"></i>
                    <span><?php echo e(ucfirst('ansar recruitment')); ?></span>
                </a>
            </li>
            <?php if(auth()->user()->type==11): ?>

                <li>
                    <a href="<?php echo e(URL::to('user_management')); ?>">
                        <i class="fa fa-user"></i>
                        <span>Manage User</span>
                    </a>
                </li>
            <?php endif; ?>
            <?php if(auth()->user()->type==22): ?>

                <li>
                    <a href="<?php echo e(URL::route('user_create_request.index')); ?>">
                        <i class="fa fa-user"></i>
                        <span>Manage User</span>
                    </a>
                </li>
            <?php endif; ?>
            <?php /*<li>*/ ?>
            <?php /*<a href="pages/calendar.html">*/ ?>
            <?php /*<i class="fa fa-calendar"></i> <span>Calendar</span>*/ ?>
            <?php /*<small class="label pull-right bg-red">3</small>*/ ?>
            <?php /*</a>*/ ?>
            <?php /*</li>*/ ?>
            <?php /*<li>*/ ?>
            <?php /*<a href="pages/mailbox/mailbox.html">*/ ?>
            <?php /*<i class="fa fa-envelope"></i> <span>Mailbox</span>*/ ?>
            <?php /*<small class="label pull-right bg-yellow">12</small>*/ ?>
            <?php /*</a>*/ ?>
            <?php /*</li>*/ ?>
        </ul>
    </section>
    <!-- /.sidebar -->
</aside>