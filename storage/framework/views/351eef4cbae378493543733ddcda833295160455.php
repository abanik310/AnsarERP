<?php $i = (intVal($applicants->currentPage() - 1) * $applicants->perPage()) + 1; ?>
<div>
    <div class="table-responsive">
        <table class="table table-bordered table-condensed">
            <caption style="font-size: 20px;color:#111111">All applied applicants(<?php echo e($applicants->total()); ?>)
                <button class="btn btn-primary btn-xs" ng-click="selectAllApplicant()">Select all applicant</button>
                <button class="btn btn-primary btn-xs" ng-disabled="selectedList.length<=0"
                        ng-click="confirmSelectionOrRejection()">Confirm selection
                </button>
                <button class="btn btn-danger btn-xs" ng-disabled="selectedList.length<=0"
                        ng-click="selectApplicants('rejection')">Reject selection
                </button>

                <div class="row" style="margin-top: 10px">
                    <h4 style="margin-left: 2%">Search</h4>
                    <div class="col-md-4">
                        <div class="form-group">
                            <input type="text" placeholder="Mobile Number" class="form-control" ng-model="param.q.mobNo"
                                   ng-keyup="$event.keyCode==13?loadApplicant():''">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <input type="text" placeholder="Applicant ID" class="form-control" ng-model="param.q.appId"
                                   ng-keyup="$event.keyCode==13?loadApplicant():''">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <input type="text" placeholder="National ID" class="form-control" ng-model="param.q.nId"
                                   ng-keyup="$event.keyCode==13?loadApplicant():''">
                        </div>
                    </div>
                    <?php /*<div class="col-md-4">*/ ?>
                        <?php /*<div class="form-group">*/ ?>
                            <?php /*<input type="text" placeholder="Date of Birth" class="form-control" ng-model="param.q.dob"*/ ?>
                                   <?php /*ng-keyup="$event.keyCode==13?loadApplicant():''">*/ ?>
                        <?php /*</div>*/ ?>
                    <?php /*</div>*/ ?>
                    <div class="col-md-12">
                        <button class="btn btn-primary" ng-click="loadApplicant()">
                            <i class="fa fa-search"></i>&nbsp; Search
                        </button>
                    </div>
                </div>
            </caption>
            <tr>
                <th>Sl. No</th>
                <th>Applicant Name</th>
                <th>Gender</th>
                <th>Birth Date</th>
                <th>Division</th>
                <th>District</th>
                <th>Thana</th>
                <th>Height</th>
                <th>Chest</th>
                <th>Weight</th>
                <th>Action</th>
            </tr>
            <?php $__empty_1 = true; foreach($applicants as $a): $__empty_1 = false; ?>
                <tr>
                    <td><?php echo e($i++); ?></td>
                    <td><?php echo e($a->applicant_name_bng); ?>

                        <a href="#"
                           ng-click="editApplicant('<?php echo e(URL::route('recruitment.applicant.detail_view',['id'=>$a->applicant_id])); ?>')"
                           class="">
                            <i class="fa fa-edit"></i>
                        </a>
                    </td>
                    <td><?php echo e($a->gender); ?></td>
                    <td><?php echo e($a->date_of_birth); ?></td>
                    <td><?php echo e($a->division_name_bng); ?></td>
                    <td><?php echo e($a->unit_name_bng); ?></td>
                    <td><?php echo e($a->thana_name_bng); ?></td>
                    <td><?php echo e($a->height_feet); ?> feet <?php echo e($a->height_inch); ?> inch</td>
                    <td><?php echo e($a->chest_normal.'-'.$a->chest_extended); ?> inch</td>
                    <td><?php echo e($a->weight); ?> kg</td>
                    <td>
                        <button ng-if="selectedList.indexOf('<?php echo e($a->applicant_id); ?>')<0" class="btn btn-primary btn-xs"
                                ng-click="addToSelection('<?php echo e($a->applicant_id); ?>')"><i class="fa fa-plus"></i>&nbsp; Add
                            To Selection
                        </button>
                        <button ng-if="selectedList.indexOf('<?php echo e($a->applicant_id); ?>')>=0" class="btn btn-danger btn-xs"
                                ng-click="removeToSelection('<?php echo e($a->applicant_id); ?>')"><i class="fa fa-minus"></i>&nbsp;
                            Remove Selection
                        </button>
                        <button class="btn btn-xs btn-primary" ng-click="acceptedAsSpecial('<?php echo e($a->applicant_id); ?>')">
                            Accepted as special
                        </button>
                    </td>
                </tr>
            <?php endforeach; if ($__empty_1): ?>
                <tr>
                    <td class="bg-warning" colspan="11">No data available</td>
                </tr>
            <?php endif; ?>
        </table>
    </div>
    <?php if(count($applicants)): ?>
        <div class="row">
            <div class="col-sm-3">
                <div class="form-group">
                    <label for="" class="control-label">Load limit</label>
                    <select class="form-control" ng-model="param.limit" ng-change="loadApplicant()">
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="150">150</option>
                        <option value="200">200</option>
                        <option value="300">300</option>
                    </select>
                </div>
            </div>
            <div class="col-sm-9">
                <div class="pull-right" paginate ref="loadApplicant(url)">
                    <?php echo e($applicants->render()); ?>

                </div>
            </div>
        </div>
    <?php endif; ?>
</div>