<?php $__env->startSection('title','Edit KPI Information'); ?>
<?php $__env->startSection('breadcrumb'); ?>
    <?php echo Breadcrumbs::render('kpi_edit',$id); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <script>
        $(document).ready(function () {
            var a = $('#activation_date').val();
            var b = $('#withdraw_date').val();
//            if (a) $('#activation_date').val(moment(a).format("D-MMM-YYYY"))
//            if (b) $('#withdraw_date').val(moment(b).format("D-MMM-YYYY"));
            $('#activation_date').datepicker({                dateFormat:'yy-mm-dd'            })({
                defaultValue:a
            });
            $("#withdraw_date").datepicker({                dateFormat:'yy-mm-dd'            })({
                defaultValue:b
            });

        })
        GlobalApp.controller('KPIController', function ($scope, getNameService) {
            $scope.division = [];
            $scope.district = [];
            $scope.SelectedOrganization = '<?php echo e($kpi_info->org_id); ?>';
            $scope.SelectedDivision = '<?php echo e($kpi_info->division_id); ?>';
            $scope.SelectedDistrict = '<?php echo e($kpi_info->unit_id); ?>';
            $scope.ThanaModel = '<?php echo e($kpi_info->thana_id); ?>';
            $scope.kpi_name = `<?php echo e($kpi_info->kpi_name); ?>`;
            $scope.kpi_address = `<?php echo e($kpi_info->kpi_address); ?>`;
            $scope.kpi_contact_no =`<?php echo e($kpi_info->kpi_contact_no); ?>`;
            $scope.total_ansar_request = '<?php echo e($kpi_info->details->total_ansar_request); ?>';
            $scope.total_ansar_given = '<?php echo e($kpi_info->details->total_ansar_given); ?>';
            $scope.is_special_kpi = parseInt('<?php echo e($kpi_info->details->is_special_kpi); ?>')==1?true:false;
            $scope.special_amount = '<?php echo e($kpi_info->details->special_amount); ?>';
            $scope.with_weapon = `<?php echo e($kpi_info->details->with_weapon); ?>`;
            $scope.weapon_count = `<?php echo e($kpi_info->details->weapon_count); ?>`;
            $scope.bullet_no = `<?php echo e($kpi_info->details->bullet_no); ?>`;
            $scope.weapon_description = `<?php echo e($kpi_info->details->weapon_description); ?>`;
            $scope.activation_date = '<?php echo e($kpi_info->details->activation_date); ?>';
            $scope.withdraw_date = ('<?php echo e($kpi_info->details->withdraw_date); ?>');
            $scope.no_of_ansar = '<?php echo e($kpi_info->details->no_of_ansar); ?>';
            $scope.no_of_apc = '<?php echo e($kpi_info->details->no_of_apc); ?>';
            $scope.no_of_pc = '<?php echo e($kpi_info->details->no_of_pc); ?>';

            $scope.isAdmin = parseInt('<?php echo e(Auth::user()->type); ?>');
            $scope.dcDistrict = parseInt('<?php echo e(Auth::user()->district_id); ?>');
            
            getNameService.getOrganization().then(function (response) {
                $scope.organization = response.data;
                <?php /*<?php if(!is_null($kpi_info->org_id)): ?>;*/ ?>
                <?php /*$scope.SelectedOrganization = '<?php echo e($kpi_info->org_id); ?>';*/ ?>
                <?php /**/ ?>
                <?php /*<?php endif; ?>*/ ?>
                //$scope.SelectedItemChanged();
            });

            getNameService.getDivision().then(function (response) {
                $scope.division = response.data;
                <?php /*<?php if(!is_null($kpi_info->division_id)): ?>;*/ ?>
                <?php /*$scope.SelectedDivision = '<?php echo e($kpi_info->division_id); ?>';*/ ?>
                <?php /**/ ?>
                <?php /*<?php endif; ?>*/ ?>
                $scope.SelectedItemChanged();
            });
            $scope.SelectedItemChanged = function () {
                getNameService.getDistric($scope.SelectedDivision).then(function (response) {
                    $scope.district = response.data;
                    <?php /*$scope.SelectedDistrict = '<?php echo e($kpi_info->unit_id); ?>';*/ ?>
                    <?php /*<?php if(!is_null($kpi_info->unit_id)): ?>*/ ?>
                    <?php /*$scope.SelectedDistrict = '<?php echo e($kpi_info->unit_id); ?>';*/ ?>
                    $scope.SelectedDistrictChanged();
                    <?php /*<?php endif; ?>*/ ?>
//                        $scope.SelectedDistrictChanged();
                })
            }
            $scope.SelectedDistrictChanged = function () {
                getNameService.getThana($scope.SelectedDistrict).then(function (response) {
                    $scope.thana = response.data;
                    <?php /*$scope.ThanaModel = '<?php echo e($kpi_info->thana_id); ?>';*/ ?>
                    <?php /*<?php if(!is_null($kpi_info->thana_id)): ?>*/ ?>
                    <?php /*$scope.ThanaModel = '<?php echo e($kpi_info->thana_id); ?>';*/ ?>
                    <?php /*<?php endif; ?>*/ ?>
                })

            }
            if ($scope.isAdmin == 11) {
                getNameService.getDivision();
            }
            else {
                if (!isNaN($scope.dcDistrict)) {
                    $scope.SelectedItemChanged($scope.dcDistrict)
                }
            }
            <?php /*<?php if(!is_null($kpi_info->unit_id)): ?>*/ ?>
            <?php /*$scope.SelectedDistrict = '<?php echo e($kpi_info->unit_id); ?>';*/ ?>
            <?php /*$scope.SelectedDistrictChanged();*/ ?>
            <?php /*<?php endif; ?>*/ ?>


        });
        GlobalApp.factory('getNameService', function ($http) {
            return {
                getOrganization: function () {
                    return $http.get("<?php echo e(URL::to('HRM/OrganizationName')); ?>");
                },
                getDivision: function () {
                    return $http.get("<?php echo e(URL::to('HRM/DivisionName')); ?>");
                },
                getDistric: function (data) {

                    return $http.get("<?php echo e(URL::to('HRM/DistrictName')); ?>", {params: {id: data}});
                },
                getThana: function (data) {
                    return $http.get("<?php echo e(URL::to('HRM/ThanaName')); ?>", {params: {id: data}});
                }
            }

        });

    </script>
    <style>
        .form-horizontal .control-label {
            padding-top: 7px;
            margin-bottom: 0;
            text-align: left;
        }
    </style>
    <div style="position: relative; padding-bottom: 30px">
        <?php echo Form::open(array('route' => 'kpi-update', 'class' => 'form-horizontal', 'name' => 'kpiForm', 'id'=> 'kpi-form', 'ng-controller' => 'KPIController', 'ng-app' => 'myValidateApp', 'novalidate')); ?>


        <?php /*<div class="breadcrumbplace">*/ ?>
        <?php /*<?php echo Breadcrumbs::render('kpi_edit'); ?>*/ ?>
        <?php /*</div>*/ ?>
        <section class="content">
            <div class="row">
                <div class="col-lg-8 col-centered">
                    <div class="box box-solid">
                        <div class="box-body">
                            <div class="nav-tabs-custom">
                                <ul class="nav nav-tabs">
                                    <li class="active">
                                        <a data-toggle="tab" href="#kpi_general" style="display: none;">General Kpi
                                            Form</a>
                                    </li>
                                    <li><a data-toggle="tab" href="#kpi_details" type="button" style="display: none;">Details
                                            Kpi Form</a></li>
                                </ul>

                                <div class="tab-content">
                                    <div id="kpi_general" class="tab-pane fade in active">
                                        <h3 style="text-align: center">General Information of KPI</h3>

                                        <div class="box-body">
                                            <div class="form-group required">
                                                <?php echo Form::label('kpi_name', 'KPI Name:', $attributes = array('class' => 'col-sm-4 control-label')); ?>

                                                <div class="col-sm-8"
                                                     ng-class="{ 'has-error': kpiForm.kpi_name.$touched && kpiForm.kpi_name.$invalid }">
                                                    <?php echo Form::text('kpi_name', $value = null, $attributes = array('class' => 'form-control', 'id' => 'kpi_name', 'placeholder' => 'Enter KPI Name', 'required', 'ng-model' => 'kpi_name')); ?>

                                                    <span ng-if="kpiForm.kpi_name.$touched && kpiForm.kpi_name.$error.required"><p
                                                                class="text-danger">KPI name is required.</p></span>
                                                </div>
                                            </div>
                                            <input type="hidden" name="id" class="form-control" id="session_year"
                                                   value="<?php echo e($kpi_info->id); ?>">
                                            
                                            <div class="form-group required">
                                                <?php echo Form::label('org_id', 'Organization:', $attributes = array('class' => 'col-sm-4 control-label')); ?>

                                                <div class="col-sm-8"
                                                     ng-class="{ 'has-error': kpiForm.organization_name_eng.$touched && kpiForm.organization_name_eng.$invalid }">
                                                    <?php /*<?php echo Form::text('org_id', $value = null, $attributes = array('class' => 'form-control', 'id' => 'org_id', 'required')); ?>*/ ?>
                                                    <select name="organization_name_eng" class="form-control"
                                                            id="org_id"
                                                            ng-model="SelectedOrganization"
                                                            required>
                                                        <option value="">--Select a organization--</option>
                                                        <option ng-repeat="x in organization" value="[[x.id]]">
                                                            [[x.organization_name_eng]]
                                                        </option>
                                                    </select>
                                            <span ng-if="kpiForm.organization_name_eng.$touched && kpiForm.organization_name_eng.$error.required"><p
                                                        class="text-danger">
                                                    Organization is required.</p></span>
                                                </div>
                                            </div>

                                            <div class="form-group required" ng-show="isAdmin==11">
                                                <?php echo Form::label('division_id', 'Division:', $attributes = array('class' => 'col-sm-4 control-label')); ?>

                                                <div class="col-sm-8"
                                                     ng-class="{ 'has-error': kpiForm.division_name_eng.$touched && kpiForm.division_name_eng.$invalid }">
                                                    <?php /*<?php echo Form::text('division_id', $value = null, $attributes = array('class' => 'form-control', 'id' => 'division_id', 'required')); ?>*/ ?>
                                                    <select name="division_name_eng" class="form-control"
                                                            id="division_id"
                                                            ng-model="SelectedDivision"
                                                            ng-change="SelectedItemChanged()"
                                                            required>
                                                        <option value="">--Select a division--</option>
                                                        <option ng-repeat="x in division" value="[[x.id]]">
                                                            [[x.division_name_eng]]
                                                        </option>
                                                    </select>
                                            <span ng-if="kpiForm.division_name_eng.$touched && kpiForm.division_name_eng.$error.required"><p
                                                        class="text-danger">
                                                    KPI division is required.</p></span>
                                                </div>
                                            </div>
                                            <div class="form-group required">
                                                <?php echo Form::label('unit_id', 'Unit:', $attributes = array('class' => 'col-sm-4 control-label')); ?>

                                                <div class="col-sm-8"
                                                     ng-class="{ 'has-error': kpiForm.unit_name_eng.$touched && kpiForm.unit_name_eng.$invalid }">
                                                    <?php /*<?php echo Form::text('unit_id', $value = null, $attributes = array('class' => 'form-control', 'id' => 'unit_id', 'required')); ?>*/ ?>
                                                    <select name="unit_name_eng" class="form-control" id="unit_id"
                                                            ng-model="SelectedDistrict"
                                                            ng-change="SelectedDistrictChanged()"
                                                            required>
                                                        <option value="">--Select a district--</option>
                                                        <option ng-repeat="x in district" value="[[x.id]]">[[
                                                            x.unit_name_eng
                                                            ]]
                                                        </option>
                                                    </select>
                                            <span ng-if="kpiForm.unit_name_eng.$touched && kpiForm.unit_name_eng.$error.required"><p
                                                        class="text-danger">KPI
                                                    division is required.</p></span>
                                                </div>
                                            </div>
                                            <div class="form-group required">
                                                <?php echo Form::label('thana_id', 'Thana:', $attributes = array('class' => 'col-sm-4 control-label')); ?>

                                                <div class="col-sm-8"
                                                     ng-class="{ 'has-error': kpiForm.thana_name_eng.$touched && kpiForm.thana_name_eng.$invalid }">
                                                    <?php /*<?php echo Form::text('thana_id', $value = null, $attributes = array('class' => 'form-control', 'id' => 'thana_id', 'required')); ?>*/ ?>
                                                    <select name="thana_name_eng" class="form-control" id="thana_id"
                                                            ng-model="ThanaModel" ng-change="SelectedThanaChanged()"
                                                            required>
                                                        <option value="">--Select a thana--</option>
                                                        <option ng-repeat="x in thana" value="[[x.id]]">[[
                                                            x.thana_name_eng ]]
                                                        </option>
                                                    </select>
                                            <span ng-if="kpiForm.thana_name_eng.$touched && kpiForm.thana_name_eng.$error.required"><p
                                                        class="text-danger">KPI
                                                    division is required.</p></span>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <?php echo Form::label('kpi_address', 'Address:', $attributes = array('class' => 'col-sm-4 control-label')); ?>

                                                <div class="col-sm-8">
                                                    <?php echo Form::textarea('kpi_address', $value = null, $attributes = array('class' => 'form-control', 'id' => 'kpi_address', 'size' => '30x4', 'placeholder' => "Write the Address", 'ng-model' => 'kpi_address')); ?>

                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <?php echo Form::label('kpi_contact_no', 'Contact No. and Person:', $attributes = array('class' => 'col-sm-4 control-label')); ?>

                                                <div class="col-sm-8">
                                                    <?php echo Form::textarea('kpi_contact_no', $value = null, $attributes = array('class' => 'form-control', 'id' => 'kpi_contact_no', 'size' => '30x4','placeholder' => 'Write Contact No. and Person Info', 'ng-model' => 'kpi_contact_no')); ?>

                                                </div>
                                            </div>
                                            <button style="background: #5bc0de; border-color: #46b8da; color: #FFFFFF"
                                                    class="btn btn-primary pull-right" id="nexttab" type="button">Next Page
                                            </button>
                                        </div>
                                        <?php /*<?php echo Form::close(); ?>*/ ?>
                                    </div>
                                    <div id="kpi_details" class="tab-pane fade">
                                        <div class="box-body">
                                            <h3 style="text-align: center">Details Information of KPI</h3>

                                            <div class="box-body">
                                                <div class="form-group required">
                                                    <?php echo Form::label('total_ansar_request', 'Total Ansar Request:', $attributes = array('class' => 'col-sm-4 control-label')); ?>

                                                    <div class="col-sm-8"
                                                         ng-class="{ 'has-error': kpiForm.total_ansar_request.$touched && kpiForm.total_ansar_request.$invalid }">
                                                        <?php echo Form::text('total_ansar_request', $value = null, $attributes = array('class' => 'form-control', 'id' => 'total_ansar_request', 'placeholder' => 'Enter Total Ansar Request Number', 'required', 'ng-model' => 'total_ansar_request')); ?>

                                                        <span ng-if="kpiForm.total_ansar_request.$touched && kpiForm.total_ansar_request.$error.required"><p
                                                                    class="text-danger">Total Ansar Request field is
                                                                required.</p></span>
                                                    </div>
                                                </div>
                                                <div class="form-group required">
                                                    <?php echo Form::label('total_ansar_given', 'Total Ansar Given:', $attributes = array('class' => 'col-sm-4 control-label')); ?>

                                                    <div class="col-sm-8"
                                                         ng-class="{ 'has-error': kpiForm.total_ansar_given.$touched && kpiForm.total_ansar_given.$invalid }">
                                                        <?php echo Form::text('total_ansar_given', $value = null, $attributes = array('class' => 'form-control', 'id' => 'total_ansar_given', 'placeholder' => 'Enter Total Ansar given Number', 'required', 'ng-model' => 'total_ansar_given')); ?>

                                                        <span ng-if="kpiForm.total_ansar_given.$touched && kpiForm.total_ansar_given.$error.required"><p
                                                                    class="text-danger">Total Ansar Given field is
                                                                required.</p></span>
                                                    </div>
                                                </div>
                                                <div class="form-group required">
                                                    <?php echo Form::label('with_weapon', 'Ansar With Weapon:', $attributes = array('class' => 'col-sm-4 control-label')); ?>

                                                    <div class="col-sm-8"
                                                         ng-class="{ 'has-error': kpiForm.with_weapon.$touched && kpiForm.with_weapon.$invalid }">
                                                        <?php /*<?php echo Form::text('thana_id', $value = null, $attributes = array('class' => 'form-control', 'id' => 'thana_id', 'required')); ?>*/ ?>
                                                        <select class="form-control" id="with_weapon" name="with_weapon"
                                                                ng-model="with_weapon" required>
                                                            <option value="">--Select Yes or No--</option>
                                                            <option value="1">Yes</option>
                                                            <option value="0">No</option>
                                                        </select>
                                                <span ng-if="kpiForm.with_weapon.$touched && kpiForm.with_weapon.$error.required"><p
                                                            class="text-danger">Ansar With Weapon field is required.</p></span>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <?php echo Form::label('is_special_kpi', 'Is Special:', $attributes = array('class' => 'col-sm-4 control-label')); ?>

                                                    <div class="col-sm-8">
                                                        <?php echo Form::checkbox('is_special_kpi', 1, null, $attributes = array( 'id' => 'is_special_kpi', 'placeholder' => 'Enter Total Ansar given Number','ng-checked'=>'is_special_kpi', 'ng-model' => 'is_special_kpi')); ?>

                                                    </div>
                                                </div>
                                                <div class="form-group" ng-show="is_special_kpi">
                                                    <div class="col-sm-8 col-sm-offset-4"
                                                         ng-class="{ 'has-error': kpiForm.total_ansar_given.$touched && kpiForm.total_ansar_given.$invalid }">
                                                        <?php echo Form::text('special_amount', $value = null, $attributes = array('class' => 'form-control','ng-disabled'=>'!is_special_kpi', 'id' => 'special_amount', 'placeholder' => 'Custom percentage of 15-20%', 'required', 'ng-model' => 'special_amount','numeric-field')); ?>

                                                        <span ng-if="kpiForm.total_ansar_given.$touched && kpiForm.total_ansar_given.$error.required"><p
                                                                    class="text-danger">Total Ansar Given field is required.</p></span>
                                                    </div>
                                                </div>
                                                <div class="form-group required">
                                                    <?php echo Form::label('weapon_count', 'Weapon Number:', $attributes = array('class' => 'col-sm-4 control-label')); ?>

                                                    <div class="col-sm-8"
                                                         ng-class="{ 'has-error': kpiForm.weapon_count.$touched && kpiForm.weapon_count.$invalid }">
                                                        <?php echo Form::text('weapon_count', $value = null, $attributes = array('class' => 'form-control', 'id' => 'weapon_count', 'placeholder' => 'Enter Weapon Number.e.g., For no weapon, enter 0', 'required', 'ng-model' => 'weapon_count')); ?>

                                                        <span ng-if="kpiForm.weapon_count.$touched && kpiForm.weapon_count.$error.required"><p
                                                                    class="text-danger">Weapon Number field is
                                                                required.</p></span>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <?php echo Form::label('bullet_no', 'Number of Bullets:', $attributes = array('class' => 'col-sm-4 control-label')); ?>

                                                    <div class="col-sm-8">
                                                        <?php echo Form::text('bullet_no', $value = null, $attributes = array('class' => 'form-control', 'id' => 'bullet_no', 'placeholder' => 'Enter Number of Bullets', 'ng-model' => 'bullet_no')); ?>

                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <?php echo Form::label('weapon_description', 'Weapon Description:', $attributes = array('class' => 'col-sm-4 control-label')); ?>

                                                    <div class="col-sm-8">
                                                        <?php echo Form::textarea('weapon_description', $value = null, $attributes = array('class' => 'form-control', 'id' => 'weapon_description', 'size' => '30x4', 'placeholder' => "Write Description", 'ng-model' => 'weapon_description')); ?>

                                                    </div>
                                                </div>
                                                <div class="form-group required">
                                                    <?php echo Form::label('activation_date', 'Activation Date:', $attributes = array('class' => 'col-sm-4 control-label')); ?>

                                                    <div class="col-sm-8"
                                                         ng-class="{ 'has-error': kpiForm.activation_date.$touched && kpiForm.activation_date.$invalid }">
                                                        <?php echo Form::text('activation_date', $value = $kpi_info->details->activation_date, $attributes = array('class' => 'form-control', 'id' => 'activation_date', 'required', 'ng-model' => 'activation_date','placeholder'=>'Activation Date')); ?>

                                                        <span ng-if="kpiForm.activation_date.$touched && kpiForm.activation_date.$error.required"><p
                                                                    class="text-danger">Activation Date field is
                                                                required.</p></span>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <?php echo Form::label('withdraw_date', 'Withdraw Date:', $attributes = array('class' => 'col-sm-4 control-label')); ?>

                                                    <div class="col-sm-8">
                                                        <?php echo Form::text('withdraw_date', $value = $kpi_info->details->withdraw_date, $attributes = array('class' => 'form-control', 'id' => 'withdraw_date', 'ng-model' => 'withdraw_date','placeholder'=>'Withdraw Date')); ?>

                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <?php echo Form::label('no_of_pc', 'No of PC:', $attributes = array('class' => 'col-sm-4 control-label')); ?>

                                                    <div class="col-sm-8">
                                                        <?php echo Form::text('no_of_pc', $value = null, $attributes = array('class' => 'form-control', 'id' => 'no_of_pc', 'placeholder' => 'Enter Number of PC', 'ng-model' => 'no_of_pc')); ?>

                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <?php echo Form::label('no_of_apc', 'No of APC:', $attributes = array('class' => 'col-sm-4 control-label')); ?>

                                                    <div class="col-sm-8">
                                                        <?php echo Form::text('no_of_apc', $value = null, $attributes = array('class' => 'form-control', 'id' => 'no_of_apc', 'placeholder' => 'Enter Number of APC', 'ng-model' => 'no_of_apc')); ?>

                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <?php echo Form::label('no_of_ansar', 'No of Ansar:', $attributes = array('class' => 'col-sm-4 control-label')); ?>

                                                    <div class="col-sm-8">
                                                        <?php echo Form::text('no_of_ansar', $value = null, $attributes = array('class' => 'form-control', 'id' => 'no_of_ansar', 'placeholder' => 'Enter Number of Ansar', 'ng-model' => 'no_of_ansar')); ?>

                                                    </div>
                                                </div>
                                                <button style="background: #5bc0de; border-color: #46b8da; color: #FFFFFF"
                                                        class="btn btn-primary" id="prevtab" type="button">Previous Page
                                                </button>
                                                <button type="submit" id="next-button" class="btn btn-primary pull-right"
                                                        ng-disabled="kpiForm.kpi_name.$error.required||kpiForm.unit_name_eng.$error.required||kpiForm.thana_name_eng.$error.required||kpiForm.total_ansar_request.$error.required||kpiForm.total_ansar_given.$error.required||kpiForm.with_weapon.$error.required||kpiForm.activation_date.$error.required">
                                                    Update KPI Information
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php echo Form::close(); ?>

        </section>
    </div>
    <script>
        var $tabs = $('.nav-tabs-custom li');

        $('#prevtab').on('click', function () {
            $tabs.filter('.active').prev('li').find('a[data-toggle="tab"]').tab('show');
        });

        $('#nexttab').on('click', function () {
            $tabs.filter('.active').next('li').find('a[data-toggle="tab"]').tab('show');
        });
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('template.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>