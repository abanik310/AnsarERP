<div class="letter-footer">
    <div class="footer-top">
        <ul class="pull-right" style="margin-top: 35px;width:35%">
            <li><?php echo e($user?$user->first_name.' '.$user->last_name:''); ?></li>
            <li>
                <?php if($user&&(trim($user->division)=="DMA"||trim($user->division)=="CMA")): ?>
                    জোন অধিনায়ক,&nbsp;
                <?php else: ?>
                    জেলা কমান্ড্যান্ট<br>
                <?php endif; ?>
                <?php if($user&&(trim($user->division)=="DMA"||trim($user->division)=="CMA")): ?>
                    <?php echo e($user?preg_replace('/\).+/',')',preg_replace('/.+\(/',$user->division_bng.'(',$user->unit)):''); ?>

                <?php else: ?>
                        বাংলাদেশ আনসার ও গ্রাম প্রতিরক্ষা বাহিনী, <?php echo e($user?$user->unit_short:''); ?>

                <?php endif; ?>
            </li>
            <li id="mobile">মোবাইলঃ&nbsp;<span
                        style="display: inline-block;position: relative;"><?php echo e($user?$user->mobile_no:''); ?></span>
            </li>
            <li id="email">ই-মেইলঃ&nbsp;<span><?php echo e($user?$user->email:''); ?></span></li>
        </ul>
    </div>
    <div class="footer-bottom">
        <ul class="pull-left" style="width: 50%">
            <li style="margin-top: 3%;margin-bottom: 2%;">স্মারক নং&nbsp;-&nbsp;<b><?php echo e($mem->memorandum_id); ?></b></li>
            <li style="text-decoration: underline">অনুলিপিঃ</li>
            <li style="margin: 1% 0">১। অপারেশন (কেপিআই) শাখা<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;বাংলাদেশ আনসার ও গ্রাম
                প্রতিরক্ষা বাহিনী, সদর
                দপ্তর, ঢাকা।
            </li>
            <?php if($user&&(trim($user->division)=="DMA"||trim($user->division)=="CMA")): ?>
                <li style="margin: 1% 0">২। উপ-মহাপরিচালক/পরিচালক<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;বাংলাদেশ আনসার ও গ্রাম
                    প্রতিরক্ষা বাহিনী<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="position: relative">&nbsp;&nbsp;&nbsp;<span
                                style="position: absolute;left: 20%;"><?php echo e(($user && isset($user->unit_short))?$user->unit_short:""); ?></span>………………………মেট্রোপলিটন আনসার</span>
                </li>
                <li style="margin: 1% 0">৩। উপ-পুলিশ কমিশনার…………………………
                </li>
                <li style="margin: 1% 0">৪। সংস্থা…………………………</li>
                <li style="margin: 1% 0">৫। থানা আনসার ও ভিডিপি কর্মকর্তা<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(সংশ্লিষ্ট)……………………………
                </li>
                <li style="margin: 1% 0">৬। পিসি/এপিসি/ভারপ্রাপ্ত</li>
                <li style="margin: 1% 0">৭। অফিসকপি</li>
            <?php else: ?>
                <li style="margin: 1% 0">২। উপ-মহাপরিচালক/পরিচালক<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;বাংলাদেশ আনসার ও গ্রাম
                    প্রতিরক্ষা বাহিনী<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="position: relative">&nbsp;&nbsp;&nbsp;<span
                                style="position: absolute;left: 36%;"><?php echo e($user->division_bng); ?></span>………………………রেঞ্জ</span>
                </li>
                <li style="margin: 1% 0">৩। জেলা প্রশাসক<span style="position: relative">…………………………<span
                                style="position: absolute;left: 36%;"><?php echo e(($user && isset($user->unit_short))?$user->unit_short:""); ?></span></span>
                </li>
                <li style="margin: 1% 0">৪। পুলিশসুপার<span style="position: relative">…………………………<span
                                style="position: absolute;left: 36%;"><?php echo e(($user && isset($user->unit_short))?$user->unit_short:""); ?></span></span>
                </li>
                <li style="margin: 1% 0">৫। সংস্থা……………………………………</li>
                <li style="margin: 1% 0">৬। উপজেলা/থানা আনসার ও ভিডিপি কর্মকর্তা<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(সংশ্লিষ্ট)……………………………
                </li>
                <li style="margin: 1% 0">৭। পিসি/এপিসি/ভারপ্রাপ্ত</li>
                <li style="margin: 1% 0">৮। অফিস কপি</li>
            <?php endif; ?>
        </ul>
        <ul class="pull-right" style="width: 35% !important;">
            <li>
                <table border="0" width="75%">
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
            </li>
            <li style="margin-top: 5%">সদয় অবগতির জন্য</li>
            <li class="ppp" style="margin-top: 8%;line-height: 35px;">&nbsp;&nbsp;"<br>&nbsp;&nbsp;"<br></li>
            <?php if($user&&!(trim($user->division)=="DMA"||trim($user->division)=="CMA")): ?>
                <li class="ppp">&nbsp;&nbsp;"</li>
            <?php endif; ?>
            <li>অবগতি ও কার্যক্রমের জন্য</li>
            <li class="ppp" style="line-height: 30px;margin-top: 5%;">&nbsp;&nbsp;"<br>&nbsp;&nbsp;"</li>

        </ul>
    </div>
    <div class="footer-bottom">
        <ul class="pull-right" style="width: 33% !important; margin-top: 25px;">
            <li>
                <?php if($user&&(trim($user->division)=="DMA"||trim($user->division)=="CMA")): ?>
                    জোন অধিনায়ক<br>
                <?php else: ?>
                    জেলা কমান্ড্যান্ট<br>
                <?php endif; ?>
            </li>
        </ul>
    </div>
</div>