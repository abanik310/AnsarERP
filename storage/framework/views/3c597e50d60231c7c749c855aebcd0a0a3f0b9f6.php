<?php if(count($logs)>0): ?>
    <ul class="timeline">
        <?php $__empty_1 = true; foreach($logs as $date=>$log): $__empty_1 = false; ?>
            <li class="time-label">
                            <span class="bg-green">
                                <?php echo e($date); ?>

                            </span>
            </li>
            <?php foreach($log as $item): ?>
                <li>
                    <!-- timeline icon -->
                    <i class="fa fa-cog bg-blue"></i>

                    <div class="timeline-item">
                        <span class="time"><i class="fa fa-clock-o"></i> <?php echo e($item->time); ?></span>

                        <h3 class="timeline-header" style="background: rgba(0, 120, 112, 0.15);"><a
                                    href="#"><?php echo e(isset($item->action_type) ? $item->action_type : 'UNDEFINED'); ?></a></h3>
                        <?php if($item->action_type=="TRANSFER"): ?>
                            <div class="timeline-body">
                                 <?php
                                  if($item->getAnsar->designation_id == 1){
                                      echo 'Ansar';
                                  }elseif($item->getAnsar->designation_id == 2){
                                      echo 'APC';
                                  }else{
                                      echo 'PC';
                                  }
                                ?>
                                (<?php echo e($item->ansar_id); ?>) transferred from <b><?php echo e($item->getFromKpi()); ?></b> kpi to
                                <b><?php echo e($item->getToKpi()); ?></b>
                            </div>
                        <?php else: ?>
                            <div class="timeline-body">
                                 <?php
                                 if($item->getAnsar){
                                  if($item->getAnsar->designation_id == 1){
                                      echo 'Ansar';
                                  }elseif($item->getAnsar->designation_id == 2){
                                      echo 'APC';
                                  }else{
                                      echo 'PC';
                                 }}
                                ?>
                                (<?php echo e($item->ansar_id); ?>) transferred to status <?php echo e($item->to_state); ?>

                            </div>
                        <?php endif; ?>
                    </div>
                </li>
            <?php endforeach; ?>
        <?php endforeach; if ($__empty_1): ?>

        <?php endif; ?>
    </ul>
<?php else: ?>
    <div class="alert alert-warning">
        <i class="fa fa-warning"></i>&nbsp;No Activity Available
    </div>
<?php endif; ?>