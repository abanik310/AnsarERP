@extends('template.master')
@section('title','Send To Panel-Batch Upload')
{{-- @section('breadcrumb')
    {!! Breadcrumbs::render('HRM.Ansar Promotion.Ansar Promotion') !!}
@endsection --}}
@section('content')
    <script>
        
        GlobalApp.controller('applicantSearch', function ($scope, $http, $q, httpService, $sce, notificationService) {
            $scope.isCatOther = false;
            $scope.circulars = [];
            $scope.param = {};

            var init = function () {
                //$scope.allLoading = true;
                $http({
                    method: 'get',
                    url: '{{URL::route('HRM.promotion.circulars')}}',
                    params: {
                        "status":"running",
                        "category":5
                    }
                }).then(function (response) {
                    console.log(response);
                  $scope.circulars = response.data;
                }, function (response) {
                    $scope.allLoading = false;
                });

            };
            
            init();
           
            $scope.loadCircular = function () {
                //alert("Anik");
             
            };

            $scope.loadApplicant = function () {
                //alert("Anik");
                $scope.allLoading = true;
                $http({
                    method: 'post',
                    url: '{{URL::route('HRM.promotion.promotion')}}',
                    data: angular.toJson($scope.param)
                }).then(function (response) {
                    $scope.allLoading = false;
                    $scope.applicants = $sce.trustAsHtml(response.data);
                }, function (response) {
                    $scope.allLoading = false;
                })
            };
            $scope.confirmPromotion = function () {
                
                $scope.allLoading = true;
                $http({
                    method: 'post',
                    url: '{{URL::route('HRM.promotion.confirm_promotion')}}',
                    data: angular.toJson($scope.param)
                }).then(function (response) {
                    $scope.allLoading = false;
                    $scope.applicants = $sce.trustAsHtml('');
                    notificationService.notify(response.data.status, response.data.message)
                }, function (response) {
                    $scope.allLoading = false;
                    notificationService.notify('error', response.statusText)
                })
            };

            //===========~~~
            $scope.changeCircular = function () {
                //alert("Anik");
                var keepGoing = true;
                $scope.circulars.forEach(function (value) {
                    if (keepGoing) {
                        if (value.id === parseInt($scope.param.circular) && value.category.category_type === "other") {
                            $scope.isCatOther = true;
                            keepGoing = false;
                        } else {
                            $scope.isCatOther = false;
                        }
                    }
                });
            };
            $scope.uploadFileForm = function(){
                var index = 0;
                var fd = new FormData(document.getElementById("uploadFileForm"))
                $scope.allLoading = true;
                $http({
                    url:"{{URL::route('HRM.promotion.SendToPanelBatchUploadByFile')}}",
                    data:fd,
                    method:'post',
                    headers:{
                        "Content-Type":undefined
                    },
                    eventHandlers:{
                        progress:function (event) {
                            var response = event.currentTarget.response;
                            $scope.message = response.substr(index,response.length-index);
                            console.log(response.substr(index,response.length-index))
                            index = response.length;
                        }
                    }
                }).then(function (response) {
                    $scope.message = response.data;
                    $scope.allLoading = false;
                },function (response) {

                })
            }

        });
        GlobalApp.directive('compileHtml', function ($compile) {
            return {
                restrict: 'A',
                link: function (scope, elem, attr) {
                    var newScope;
                    scope.$watch('applicants', function (n) {

                        if (attr.ngBindHtml) {
                            if (newScope) newScope.$destroy();
                            newScope = scope.$new();
                            $compile(elem[0].children)(newScope)
                        }
                    })

                }
            }
        });
        GlobalApp.directive('confirmDialog', function () {
            return {
                restrict: 'A',
                link: function (scope, elem, attr) {
                    $(element).confirmDialog({
                        message: scope.message || "Are u sure?",
                        ok_button_text: 'Confirm',
                        cancel_button_text: 'Cancel',
                        event: 'click',
                        ok_callback: function (element) {
                            scope.confirmPromotion()
                        },
                        cancel_callback: function (element) {
                        }
                    })

                }
            }
        });
    </script>
    <section class="content" ng-controller="applicantSearch">
        @if(Session::has('success_message'))
        <div style="padding: 10px 20px 0 20px;">
            <div class="alert alert-success">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <span class="glyphicon glyphicon-ok"></span> {{Session::get('success_message')}}
            </div>
        </div>
    @endif
        <div class="box box-solid">
            <div class="overlay" ng-if="allLoading">
                    <span class="fa">
                        <i class="fa fa-refresh fa-spin"></i> <b>Loading...</b>
                    </span><br>
                <span>[[message]]</span>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-sm-6 col-centered">
                        <div class="form-group">
                            <label for="" class="control-label">Job Circular</label>
                            <select name="" ng-model="param.circular" ng-change="changeCircular()"
                                    class="form-control" >
                                <option value="" >--Select a circular--</option>
                                <option ng-repeat="c in circulars" value="[[c.id]]">[[c.circular_name]]</option>
                                {{-- <option value="commander">Commander</option>
                                <option value="manual">Manual</option> --}}
                            </select>
                        </div>
                        <div class="form-group">
                            <form method="post" enctype="multipart/form-data" id="uploadFileForm" ng-submit="uploadFileForm()">
                                {!! csrf_field() !!}
                                <input type="hidden" name="circular" ng-value="param.circular">
                                <div class="form-group">
                                    <label for="" class="control-label">Upload File</label>
                                    <input type="file" name="applicant_id_list" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Memorandum no.&nbsp;&nbsp;&nbsp;<span
                                        ng-show="isVerifying"><i
                                            class="fa fa-spinner fa-pulse"></i>&nbsp;Verifying</span><span
                                        class="text-danger"
                                        ng-if="isVerified&&!memorandumId">Memorandum no. is required.</span><span
                                        class="text-danger"
                                        ng-if="isVerified&&memorandumId">This id already taken.</span></label>
                            <input ng-blur="verifyMemorandumId()" ng-model="memorandumId"
                                   type="text" class="form-control" name="memorandum_id"
                                   placeholder="Enter Memorandum no." required>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Panel Date <span class="text-danger"
                                                                                  ng-show="panelForm.panel_date.$touched && panelForm.panel_date.$error.required"> Date is required.</span></label>
                                    &nbsp;&nbsp;&nbsp;</label>
                                    {!! Form::text('panel_date', $value = null, $attributes = array('class' => 'form-control', 'id' => 'panel_date', 'ng_model' => 'panel_date', 'required','date-picker'=>'moment()')) !!}
                                </div>

                                <div class="form-group">
                                    <button class="btn btn-primary pull-right">Upload file</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
                
        </div>   
    </section>

@endsection
