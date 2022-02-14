<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="<?php echo e(asset('dist/css/letter.css?v=3.4.31')); ?>">
    <link href="<?php echo e(asset('dist/css/font-awesome.min.css')); ?>" rel="stylesheet" type="text/css"/>
</head>
<body>
<div>
    <?php $i = 0;?>
    <?php foreach(array_chunk($result,5) as $r): ?>
        <?php echo $__env->make('HRM::Letter.'.$view,['result'=>$r], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
        <?php $i++ ?>
    <?php endforeach; ?>
</div>

<script src="<?php echo e(asset('plugins/jQuery/jQuery-2.1.4.min.js')); ?>" type="text/javascript"></script>
<script src="<?php echo e(asset('dist/js/bangla_calender.js')); ?>" type="text/javascript"></script>
<script>
    $(function () {
        $(document).on('click', '#print-report', function (e) {
            e.preventDefault();
            window.print();
        });
    });
    jQuery(document).ready(function ($) {
        $.each($("td.jsDateConvert span"), function (index, value) {
            var dateText = $(value).html();
            dateText = dateText.replace(new RegExp(/[\n\s\r\t]+/g), '');
            dateText = dateText.split(" ");
            dateText = dateText[0].split("/");
            $(value).html(convertToBanglaDate(dateText[2], dateText[1], dateText[0]));
        });
    });
</script>
</body>
</html>
