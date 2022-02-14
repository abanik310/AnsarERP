
<div class="letter-header">
    <div class="header-top" style="background: none;position: relative;">
        <h4 style="font-weight: 500;">গণপ্রজাতন্ত্রী বাংলাদেশ সরকার<br>বাংলাদেশ আনসার ও গ্রাম প্রতিরক্ষা বাহিনী<br>
            <?php if($user&&(trim($user->division)=="DMA"||trim($user->division)=="CMA")): ?>
                জোন অধিনায়কের কার্যালয়,&nbsp;
            <?php else: ?>
                জেলা কমান্ড্যান্টের কার্যালয়,&nbsp;
            <?php endif; ?>
            <?php if($user&&(trim($user->division)=="DMA"||trim($user->division)=="CMA")): ?>
                <?php echo e($user?preg_replace('/\).+/',')',preg_replace('/.+\(/',$user->division_bng.'(',$user->unit)):''); ?>

            <?php else: ?>
                <?php echo e($user?$user->unit:''); ?>

            <?php endif; ?>
            <br><span style="text-decoration: underline;">www.ansarvdp.gov.bd</span>
        </h4>
        <img src="<?php echo e(asset('dist/img/mujib-logo.png')); ?>" class="img-responsive mujib-logo" alt="Mujib100Logo">


    </div>
    <div class="header-bottom">
        <div class="pull-left" style="margin-top: 2%;">
            স্মারক নং-<?php echo e($mem->memorandum_id); ?>

        </div>
        <div class="pull-right">
            <table border="0" width="100%">
                <tr>
                    <td rowspan="2" width="10px">তারিখঃ</td>
                    <td style="border-bottom: solid 1px #000;text-align: center;" class="jsDateConvert">
                        <?php if($mem->created_at): ?>
                            <span><?php echo e(\Carbon\Carbon::parse($mem->created_at)->format('d/m/Y')); ?></span> বঙ্গাব্দ
                        <?php else: ?>
                            <span><?php echo e(\Carbon\Carbon::now()->format('d/m/Y')); ?></span> বঙ্গাব্দ
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td style="text-align: center;">
                        <?php if($mem->created_at): ?>
                            <?php echo e(LanguageConverter::engToBngWS(\Carbon\Carbon::parse($mem->created_at)->format('d/m/Y'))); ?>

                            খ্রিষ্টাব্দ
                        <?php else: ?>
                            <?php echo e(LanguageConverter::engToBngWS(\Carbon\Carbon::now()->format('d/m/Y'))); ?> খ্রিষ্টাব্দ
                        <?php endif; ?>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>
