<style>
    .control-label {
        text-align: left !important;
    }
    p.text-danger{
        margin-bottom: 5px !important;
    }
    table{
        margin-bottom: 5px !important;
    }
</style>
<script>
    var formData = new FormData();
    GlobalApp.controller('InfoController', function ($scope, $http, httpService, $q, notificationService,$rootScope) {
        $scope.info = {urL: '', form: {}};
        $scope.errors = {};
        $scope.subTraining = [];
        $scope.info.url = '<?php echo e($url); ?>'
        $scope.educationDegrees = [1];
        $scope.training_info = [1];
        $scope.allLoading = true
        $q.all([
            httpService.range()
            <?php if(isset($id)): ?>
            , $http.get("<?php echo e(URL::route('AVURP.kpi.edit',['kpi'=>$id])); ?>")
            <?php endif; ?>

        ]).then(function (response) {
            $scope.divisions = response[0];
            <?php if(isset($id)): ?>
                $scope.info.form = response[1].data;
            $scope.info.form['_method'] = 'patch';
            $scope.info.form['division_id'] += '';
            $scope.info.form['unit_id'] += '';
            $scope.info.form['thana_id'] += '';
            $scope.loadUnit($scope.info.form['division_id']);
            $scope.loadThana($scope.info.form['division_id'], $scope.info.form['unit_id']);
//            $scope.loadUnion($scope.info.form['division_id'], $scope.info.form['unit_id'], $scope.info.form['thana_id']);
            Object.keys($scope.info.form).forEach(function (key) {
                if(!$scope.info.form[key]) delete $scope.info.form[key];
            })
            <?php endif; ?>
                $scope.allLoading = false
        })
        $scope.loadUnit = function (rangeId) {
            $scope.units = $scope.thanas = $scope.unions = [];
            httpService.unit(rangeId).then(function (response) {
                $scope.units = response;
            })
        }
        $scope.loadThana = function (rangeId, unitId) {
            $scope.thanas = $scope.unions = [];
            httpService.thana(rangeId, unitId).then(function (response) {
                $scope.thanas = response;
            })
        }
        $scope.submitForm = function (event) {
            $scope.allLoading = true;
            event.preventDefault();
//            console.log(data.getAll('educationInfo'))
            $http({
                method: 'post',
                url: $scope.info.url,
                data: angular.toJson($scope.info.form)
            }).then(function (response) {
//                console.log(response.data)
//                if($rootScope.ws) $rootScope.ws.send(JSON.stringify({type:'notification',data:{to:[1],message:response.data.message}}))
                window.location.href = '<?php echo e(URL::route('AVURP.kpi.index')); ?>'
            }, function (response) {
                $scope.allLoading = false
                if (response.status === 422) {
                    $scope.errors = response.data;
                }
                else {
                    notificationService.notify("error", response.data.message)
                }
            })
        }
    })
</script>
<div ng-controller="InfoController">
    <div class="overlay" style="z-index:100;position: absolute;width: 100%;height: 100%;" ng-if="allLoading">
                    <span class="fa">
                        <i class="fa fa-refresh fa-spin"></i> <b>Loading...</b>
                    </span>
    </div>
    <form  ng-submit="submitForm($event)">
        <div class="form-group">
            <label for="kpi_name" class="control-label">????????????????????? ?????????<sup class="text-red">*</sup>
                <span class="pull-right">:</span>
            </label>
            <div>
                <input type="text" class="form-control" placeholder="????????????????????? ?????????" ng-model="info.form.kpi_name"
                       id="kpi_name">
                <p ng-if="errors.kpi_name&&errors.kpi_name.length>0" class="text text-danger">
                    [[errors.kpi_name[0] ]]</p>
            </div>
        </div>
        <div class="form-group">
            <label for="division_id" class="control-label">???????????????<sup class="text-red">*</sup>
                <span class="pull-right">:</span>
            </label>
            <div class="">
                <select class="form-control" ng-model="info.form.division_id" id="division_id"
                        ng-change="loadUnit(info.form.division_id)">
                    <option value="">--??????????????? ???????????????????????? ????????????--</option>
                    <option ng-repeat="d in divisions" value="[[d.id]]">[[d.division_name_bng]]</option>
                </select>
                <p ng-if="errors.division_id&&errors.division_id.length>0" class="text text-danger">
                    [[errors.division_id[0] ]]</p>
            </div>
        </div>
        <div class="form-group">
            <label for="unit_id" class="control-label">????????????<sup class="text-red">*</sup>
                <span class="pull-right">:</span>
            </label>
            <div>
                <select class="form-control" ng-model="info.form.unit_id" id="unit_id"
                        ng-change="loadThana(info.form.division_id,info.form.unit_id)">
                    <option value="">--???????????? ???????????????????????? ????????????--</option>
                    <option ng-repeat="u in units" value="[[u.id]]">[[u.unit_name_bng]]</option>
                </select>
                <p ng-if="errors.unit_id&&errors.unit_id.length>0" class="text text-danger">[[errors.unit_id[0]
                    ]]</p>
            </div>
        </div>
        <div class="form-group">
            <label for="thana_id" class="control-label">??????????????????<sup class="text-red">*</sup>
                <span class="pull-right">:</span>
            </label>
            <div>
                <select class="form-control" ng-model="info.form.thana_id" id="thana_id"
                        ng-change="loadUnion(info.form.division_id,info.form.unit_id,info.form.thana_id)">
                    <option value="">--?????????????????? ???????????????????????? ????????????--</option>
                    <option ng-repeat="t in thanas" value="[[t.id]]">[[t.thana_name_bng]]</option>
                </select>
                <p ng-if="errors.thana_id&&errors.thana_id.length>0" class="text text-danger">[[errors.thana_id[0]
                    ]]</p>
            </div>
        </div>
        <div class="form-group">
            <label for="address" class="control-label">????????????????????? ??????????????????
                <span class="pull-right">:</span>
            </label>
            <div >
                <input type="text" class="form-control" placeholder="????????????????????? ??????????????????"
                       ng-model="info.form.address" id="address">
                <p ng-if="errors.address&&errors.address.length>0" class="text text-danger">
                    [[errors.address[0] ]]</p>
            </div>
        </div>
        <div class="form-group">
            <label for="post_office_name" class="control-label">??????????????????????????? ?????????????????????
                <span class="pull-right">:</span>
            </label>
            <div >
                <input type="text" class="form-control" placeholder="??????????????????????????? ?????????????????????" ng-model="info.form.contact_no"
                       id="post_office_name">
                <p ng-if="errors.contact_no&&errors.contact_no.length>0" class="text text-danger">
                    [[errors.contact_no[0] ]]</p>
            </div>
        </div>
        <button type="submit" class="btn btn-primary pull-right">
            <i class="fa fa-file"></i>&nbsp;Submit
        </button>
    </form>
</div>