<!DOCTYPE html>
<html>
<head>
    <?php echo $__env->make('template.resource', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    <style>
        .table > tbody > tr > td, .table > thead > tr > td, .table > tr > td, .table > tr > th {
            background: rgba(255, 255, 255, .4) !important;
        }
    </style>

    <style>
        table > tbody > tr > th {
            background: rgba(255, 255, 255, .5) !important;
        }

        table > tbody > tr {
            background: transparent !important;
        }
    </style>

</head>
<body class="login-page">
<div class="login-box" style="margin: 1% auto !important;"  ng-controller="loginController">
    <div class="login-logo">
        <a href="<?php echo e(URL::to('/')); ?>" style="color: #ffffff;"><b>Ansar & VDP</b>ERP</a>
    </div>
    <!-- /.login-logo -->
    <div class="login-box-body" style="background: rgba(255, 255, 255, 0.32);">
        <p class="login-box-msg" style="color: #000;font-weight: bold">Sign in to start your session</p>
        <?php if(Session::has('error')): ?>
            <p class="text text-bold text-danger" style="text-align: center;text-transform: uppercase;color:lightyellow"><?php echo Session::get('error'); ?></p>
        <?php endif; ?>
        <form action="<?php echo e(action('UserController@handleLogin')); ?>" method="post">
            <?php echo e(csrf_field()); ?>

            <div class="form-group has-feedback">
                <input type="text" name="user_name" class="form-control" value="" placeholder="User Name"/>
                <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback">
                <input type="password" name="password" class="form-control" placeholder="Password"/>
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
            </div>
            <div class="row">
                <!-- /.col -->
                <div class="col-xs-4 col-xs-offset-8">
                    <button id="login" type="submit" class="btn btn-primary btn-block btn-flat">Sign In</button>
                </div>
                <!-- /.col -->
            </div>
        </form>
        <a href="<?php echo e(URL::route('forget_password_request')); ?>" style="color: #ffffff;text-transform: uppercase" >I forgot my password</a><br>

    </div>
</div>
</body>
</html>
