<?php $i = (intVal($ansars->currentPage() - 1) * $ansars->perPage()) + 1; ?>
<div class="table-responsive">
    <table class="table table-bordered table-condensed">
        <caption>
            <div class="input-group">
                <input type="text" class="form-control" placeholder="search by Ansar ID" ng-model="param.ansar_id"
                       ng-keypress="$event.keyCode==13?loadData():''">
                <span class="input-group-btn">
                    <button class="btn btn-default" ng-click="loadData()">
                        <i class="fa fa-search"></i>
                    </button>
                    <button class="btn btn-default" ng-click="clearSearch()">
                        <i class="fa fa-close"></i>
                    </button>
                </span>
            </div>
        </caption>
        <tr>
            <th>Sl. No</th>
            <th>Ansar ID</th>
            <th>Name</th>
            <th>Rank</th>
            <th>Home Division</th>
            <th>Home District</th>
            <th>Last Offer District</th>
            <th>Block Date/Last Offer Date</th>
            <th>Action</th>
        </tr>
        <?php $__empty_1 = true; foreach($ansars as $ansar): $__empty_1 = false; ?>
            <tr>
                <td><?php echo e($i++); ?></td>
                <td><?php echo e($ansar->ansar->ansar_id); ?></td>
                <td><?php echo e($ansar->ansar->ansar_name_bng); ?></td>
                <td><?php echo e($ansar->ansar->designation->name_bng); ?></td>
                <td><?php echo e($ansar->ansar->division->division_name_bng); ?></td>
                <td><?php echo e($ansar->ansar->district->unit_name_bng); ?></td>
                <td><?php echo e($ansar->unit_data->unit->unit_name_bng); ?></td>
                <td><?php if($ansar->pannel_status == 1): ?>
                   <?php echo date('d-M-Y', strtotime($ansar->unit_data->offered_date));?>


                    <?php else: ?>
                       <?php echo e($ansar->offer_block->blocked_date); ?>  
                    <?php endif; ?>
                </td>
                <td>
                    <button class="btn btn-primary btn-xs" ng-click="rollback('<?php echo e($ansar->id); ?>')">Rollback offer</button>
                    <!--<button class="btn btn-primary btn-xs" ng-click="sendToPanel('<?php echo e($ansar->id); ?>')">Send to panel
                    </button>-->
                </td>
            </tr>
        <?php endforeach; if ($__empty_1): ?>
            <tr>
                <td colspan="9" class="bg-warning">
                    No Ansar Available
                </td>
            </tr>
        <?php endif; ?>
    </table>
</div>
<?php if(count($ansars)): ?>
    <div class="row">
        <div class="col-sm-9 col-sm-offset-3">
            <div class="pull-right" paginate ref="loadData(url)">
                <?php echo e($ansars->render()); ?>

            </div>
        </div>
    </div>
<?php endif; ?>