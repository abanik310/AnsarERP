<?php if($i==0): ?>
    <h3 style="text-align: center" class="print-hide">Embodiment Letter&nbsp;&nbsp;<a href="#" id="print-report">
            <i class="fa fa-print"></i>
        </a>
    </h3>
<?php endif; ?>
<div class="letter">
    <?php echo $__env->make('HRM::Letter.letter_header',['user'=>$user], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

    <div class="letter-body">
        <div class="body-top">
            <h4>“অফিস আদেশ”</h4>
        </div>
        <div class="letter-content-top">আনসার বাহিনী আইন ১৯৯৫ খ্রিঃ এর ধারা ৬ (৪), আনসার ও গ্রাম প্রতিরক্ষা বাহিনী সদর
            দপ্তরের স্মারক নং-আইন-৫১/আনস, তারিখঃ ২৪/০৩/১৯৯৬ খ্রিঃ, স্মারক নং-অপাঃ/কেপিআই/৮৮০(৩)/১২৯/আনস, তারিখঃ
            ০৩/০৩/২০০৯ খ্রিঃ এর পরিপ্রেক্ষিতে নিম্নবর্ণিত আনসার সদস্যকে অঙ্গীভূত করা হলো।</div>
        <div class="letter-content-middle">
            <h4>“তফসিল "ক" (অঙ্গীভূত)”</h4>
            <table class="table table-bordered table-condensed">
                <tr>
                    <th style="width: 1%">ক্রমিক<br>নং</th>
                    <th style="width: 1%">আইডি<br>নং</th>
                    <th style="width: 1%">পদবী</th>
                    <th>নাম ও<br>পিতার নাম</th>
                    <th>ঠিকানা:<br>গ্রাম, পোস্ট, উপজেলা ও জেলা</th>
                    <th>সংস্থার নাম ও<br>উপজেলা/থানা</th>
                    <th style="width: 1%">অঙ্গিভুতির<br>তারিখ</th>
                </tr>
                <?php $ii = 1; ?>
                <?php for($j=0;$j<count($result);$j++): ?>
                    <?php if(isset($result[$j])): ?>
                        <tr>
                            <td><?php echo e(LanguageConverter::engToBng($ii++)); ?></td>
                            <td><?php echo e(LanguageConverter::engToBng($result[$j]->ansar_id)); ?></td>
                            <td><?php echo e($result[$j]->rank); ?></td>
                            <td><?php echo e($result[$j]->name); ?><br><?php echo e($result[$j]->father_name); ?></td>
                            <td><?php echo e(isset($result[$j]->village_name) ? $result[$j]->village_name : ''); ?>,&nbsp;<?php echo e(isset($result[$j]->pon) ? $result[$j]->pon : ''); ?>

                                ,&nbsp;<?php echo e(isset($result[$j]->thana) ? $result[$j]->thana : ''); ?>,&nbsp;<?php echo e(isset($result[$j]->unit) ? $result[$j]->unit : ''); ?></td>
                            <td><?php echo e($result[$j]->kpi_name.", ".$result[$j]->kpi_thana); ?></td>
                            <td><?php echo e(LanguageConverter::engToBng(date('d/m/Y',strtotime($result[$j]->joining_date)))); ?></td>
                        </tr>
                    <?php endif; ?>
                <?php endfor; ?>
            </table>
        </div>
        <?php echo $__env->make('HRM::Letter.letter_footer',['user'=>$user], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    </div>
</div>