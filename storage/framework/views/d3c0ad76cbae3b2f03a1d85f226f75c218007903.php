<?php $__env->startSection('title','Promotion Ansar List'); ?>
<?php $__env->startSection('breadcrumb'); ?>
    
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <script>
        GlobalApp.controller('PromotionController', function ($scope, $http, $sce, $parse, notificationService) {
            
            $scope.rank = 'all';
            $scope.queue = [];
            $scope.addAnsarBtn = false;
            $scope.exportPage = '';
            $scope.goToPanelBtn = false;
            $scope.defaultPage = {pageNum: 0, offset: 0, limit: $scope.itemPerPage, view: 'view'};
            $scope.total = 0;
            $scope.param = {};
            $scope.numOfPage = 0;
            $scope.itemPerPage = parseInt("<?php echo e(config('app.item_per_page')); ?>");
            $scope.currentPage = 0;
            $scope.allPromotionAnsar = [];
            $scope.ansar_id = [];
            $scope.ranks = [];
            $scope.pages = [];
            $scope.loadingPage = [];
            $scope.allLoading = true;
            $scope.showLoadScreen1 = true;
            $scope.orderBy = "";
            
            //$scope.rank = [];
            //$scope.from_date = moment().subtract(1, 'years').format("D-MMM-YYYY");
            //$scope.to_date = moment().format("D-MMM-YYYY");
            $scope.from_date = '';
            $scope.to_date = '';
            $scope.isDisabled = false;
            
            $scope.loadPagination = function () {
                
                $scope.pages = [];
                for (var i = 0; i < $scope.numOfPage; i++) {
                    $scope.pages.push({
                        pageNum: i,
                        offset: i * $scope.itemPerPage,
                        limit: $scope.itemPerPage
                    });
                    $scope.loadingPage[i] = false;
                }
            };
            $scope.rankUpdate = function () {
                
                var checkBox = document.getElementById("rankUpdate");
                var text = document.getElementById("text");
                var ansar = document.getElementById("ansar");
                var ansarLabel = document.getElementById("ansarLabel");
                var apc = document.getElementById("apc");
                var apcLabel = document.getElementById("apcLabel");
                var pc = document.getElementById("pc");
                var pcLabel = document.getElementById("pcLabel");
                ansar.disabled = true;
                if (checkBox.checked == true){
                    ansar.style.display = "block";
                    ansarLabel.style.display = "block";
                    apc.style.display = "block";
                    apcLabel.style.display = "block";
                    pc.style.display = "block";
                    pcLabel.style.display = "block";
                    
                } else {
                    
                    ansar.style.display = "none";
                    ansarLabel.style.display = "none";
                    apc.style.display = "none";
                    apcLabel.style.display = "none";
                    pc.style.display = "none";
                    pcLabel.style.display = "none";
                }
            };

            $scope.ansarUpdate = function () {
                //alert("allPromotionAnsar.ansar_id");
                if (ansar.checked == true){
                    apc.disabled = true;
                    apcLabel.disabled = true;
                    pc.disabled = true;
                    pcLabel.disabled = true;
                }else{
                    apc.disabled = false;
                    apcLabel.disabled = false;
                    pc.disabled = false;
                    pcLabel.disabled = false;
                }
                
                $scope.loadPage();
            }
            $scope.apcUpdate = function (id) {
                //alert("APC");
                ansar.disabled = true;
                if (apc.checked == true){
                    ansar.disabled = true;
                    ansarLabel.disabled = true;
                    pc.disabled = true;
                    pcLabel.disabled = true;
                
                    if (id) {
                        $scope.submitting = true;
                        $http({
                            url: "<?php echo e(URL::to('HRM/promotedToAPC')); ?>",
                            method: 'post',
                            data: angular.toJson({
                                request_id: id
                            })
                        }).then(function (response) {
                            console.log(response);
                            $scope.submitting = false;
                            if (response.data.status) {
                                notificationService.notify('success', response.data.message);
                                $("#confirm-panel-modal").modal('hide');
                                $scope.loadPage();
                            } else {
                                notificationService.notify('error', response.data.message)
                            }
                            
                        }, function (response) {
                            $scope.submitting = false;
                            notificationService.notify('error', "An unexpected error occur. Error code :" + response.status);
                            
                        })
                    }
                }
                else{
                    // ansar.disabled = false;
                    // ansarLabel.disabled = false;
                    pc.disabled = false;
                    pcLabel.disabled = false;
                }
                
            }

            $scope.pcUpdate = function (id) {
                //alert("APC");
                if (pc.checked == true){
                    ansar.disabled = true;
                    ansarLabel.disabled = true;
                    apc.disabled = true;
                    apcLabel.disabled = true;
                
                    if (id) {
                        $scope.submitting = true;
                        $http({
                            url: "<?php echo e(URL::to('HRM/promotedToPC')); ?>",
                            method: 'post',
                            data: angular.toJson({
                                request_id: id
                            })
                        }).then(function (response) {
                            console.log(response);
                            $scope.submitting = false;
                            if (response.data.status) {
                                notificationService.notify('success', response.data.message);
                                $("#confirm-panel-modal").modal('hide');
                                $scope.loadPage();
                            } else {
                                notificationService.notify('error', response.data.message)
                            }
                            
                        }, function (response) {
                            $scope.submitting = false;
                            notificationService.notify('error', "An unexpected error occur. Error code :" + response.status);
                        })
                    }
                }
                else{
                    // ansar.disabled = false;
                    // ansarLabel.disabled = false;
                    apc.disabled = false;
                    apcLabel.disabled = false;
                }
            }
            $scope.loadPage = function (page, $event) {

                if ($event != undefined) $event.preventDefault();
                $scope.exportPage = page;
                $scope.currentPage = page == undefined ? 0 : page.pageNum;
                $scope.loadingPage[$scope.currentPage] = true;
                $scope.allLoading = true;
                $http({
                    url: '<?php echo e(URL::to('HRM/getPromotionList')); ?>',
                    method: 'get',
                    params: 
                    {
                        offset: page == undefined ? 0 : page.offset,
                        limit: page == undefined ? $scope.itemPerPage : page.limit,
                        unit: $scope.param.unit == undefined ? 'all' : $scope.param.unit,
                        division: $scope.param.range == undefined ? 'all' : $scope.param.range,
                        gender: $scope.param.gender == undefined ? 'all' : $scope.param.gender,
                        q: $scope.q,
                        rank: $scope.rank,
                        sortBy: $scope.orderBy,
                    }
                }).then(function (response) {					
                    $scope.queue.shift();
                    if ($scope.queue.length > 1) $scope.loadPage();
                    $scope.allPromotionAnsar = response.data.allPromotionAnsar;
                    console.log($scope.allPromotionAnsar); 
                    // if($scope.allPromotionAnsar){
                    //    var not_verified_status = $scope.allPromotionAnsar.not_verified_status;
                    //    if($scope.allPromotionAnsarnot_verified_status == 1 ){alert("anik");exit;
                    //        $scope.goToPanelBtn = true;
                    //    }
                    // }
                    $scope.loadingPage[$scope.currentPage] = false;
                    $scope.allLoading = false;
                    $scope.total = sum(response.data.total);
                    $scope.gCount = response.data.total;
                    $scope.numOfPage = Math.ceil($scope.total / $scope.itemPerPage);
                    $scope.loadPagination();
                })
            };

            $scope.getPromotionList = function (url) {
                var data = $scope.params;
                $scope.allLoading = true;
                $http({
                    url: url || "<?php echo e(URL::route('getPromotionList')); ?>",
                    method: 'get',
                    params: data
                }).then(function (response) {
                    $scope.response = response.data.data;
                    $scope.allPromotionAnsar = response.data.data.data;
                    $scope.view = $sce.trustAsHtml(response.data.view);
                    $scope.checked = Array.apply(null, Array($scope.allPromotionAnsar.length)).map(Boolean.prototype.valueOf, false);
                    $scope.allLoading = false;
                }, function (response) {
                    $scope.allLoading = false;
                })
            };

            $scope.sendToPanel = function (id) {
                //if($scope.ansar_id == "") {alert("Please Enter a Ansar ID");}
                //alert("Anik");
                                
                if (id) {
                    $scope.submitting = true;
                    $http({
                        url: "<?php echo e(URL::to('HRM/SendToPanelFromAnsarList')); ?>",
                        method: 'post',
                        data: angular.toJson({
                            request_id: id,
                            memorandum_id:$scope.memorandumId,
                            panel_date:$scope.panel_date
                        })
                    }).then(function (response) {
                        console.log(response);
                        $scope.submitting = false;
                        if (response.data.status) {
                            notificationService.notify('success', response.data.message);
                            $("#send-to-panel-modal").modal('hide');
                            $scope.loadPage();
                        } else {
                            notificationService.notify('error', response.data.message)
                        }
                        $scope.ansars.splice($scope.ansars.indexOf($scope.getSingleRow), 1)
                    }, function (response) {
                        $scope.submitting = false;
                        notificationService.notify('error', "An unexpected error occur. Error code :" + response.status);
                    })
                   // alert($scope.comment );
                }
            };

            $scope.verifyAnsar = function () {
                //if($scope.ansar_id == "") {alert("Please Enter a Ansar ID");}
                                
                 $scope.allLodaing =true;
                 $http({
                    url: '<?php echo e(URL::to('HRM/checkUnitAnsarEligibility')); ?>',
                    method: 'post',
                    params: {
                        ansar_id: $scope.ansar_id,                        
                        unit: $scope.param.unit,
                        request_comment: $scope.request_comment
                    }
                }).then(function (response) {
                    
                    console.log(response);
                    if (response.data.status) {
                        notificationService.notify('success', response.data.message);
                        $("#confirm-panel-modal").modal('hide');
                        $scope.loadPage();
                    } else {
                        notificationService.notify('error', response.data.message)
                        }
                    }, function (response) {
                        $scope.submitting = false;
                        notificationService.notify('error', "An unexpected error occur. Error code :" + response.status);
                    })
            };

            $scope.verifyRankPromotion = function (id) {
                //if($scope.ansar_id == "") {alert("Please Enter a Ansar ID");}
                //alert("Anik");
                                
                if (id) {
                    $scope.submitting = true;
                    $http({
                        url: "<?php echo e(URL::to('HRM/verifyRankPromotion')); ?>",
                        method: 'post',
                        data: angular.toJson({
                            request_id: id
                        })
                    }).then(function (response) {
                        console.log(response);
                        $scope.submitting = false;
                        if (response.data.status) {
                            notificationService.notify('success', response.data.message);
                            $("#confirm-panel-modal").modal('hide');
                            $scope.loadPage();
                        } else {
                            notificationService.notify('error', response.data.message)
                        }
                        $scope.ansars.splice($scope.ansars.indexOf($scope.getSingleRow), 1)
                    }, function (response) {
                        $scope.submitting = false;
                        notificationService.notify('error', "An unexpected error occur. Error code :" + response.status);
                    })
                   // alert($scope.comment );
                }
            };

            $scope.backtoPrevious = function (id) {
                //if($scope.ansar_id == "") {alert("Please Enter a Ansar ID");}
                //alert("Anik");
                                
                if (id) {
                    $scope.submitting = true;
                    $http({
                        url: "<?php echo e(URL::to('HRM/backtoPrevious')); ?>",
                        method: 'post',
                        data: angular.toJson({
                            request_id: id
                        })
                    }).then(function (response) {
                        console.log(response);
                        $scope.submitting = false;
                        if (response.data.status) {
                            notificationService.notify('success', response.data.message);
                            $("#confirm-panel-modal").modal('hide');
                            $scope.loadPage();
                        } else {
                            notificationService.notify('error', response.data.message)
                        }
                        $scope.allPromotionAnsar.splice($scope.allPromotionAnsar.indexOf($scope.getSingleRow), 1)
                    }, function (response) {
                        $scope.submitting = false;
                        notificationService.notify('error', "An unexpected error occur. Error code :" + response.status);
                    })
                   // alert($scope.comment );
                }
            };
 
            $scope.exportData = function (type) {
                var page = $scope.exportPage;
                if (type == 'page') $scope.export_page = true;
                else $scope.export_all = true;
                $http({
                    url: '<?php echo e(URL::to('HRM/get_available_ansar_list')); ?>',
                    method: 'get',
                    params: {
                        type: $scope.ansarType,
                        offset: type == 'all' ? -1 : (page == undefined ? 0 : page.offset),
                        limit: type == 'all' ? -1 : (page == undefined ? $scope.itemPerPage : page.limit),
                        unit: $scope.param.unit == undefined ? 'all' : $scope.param.unit,
                        thana: $scope.param.thana == undefined ? 'all' : $scope.param.thana,
                        division: $scope.param.range == undefined ? 'all' : $scope.param.range,
                        gender: $scope.param.gender == undefined ? 'all' : $scope.param.gender,
                        filter_mobile_no: $scope.param.filter_mobile_no == undefined ? 0 : $scope.param.filter_mobile_no,
                        filter_age: $scope.param.filter_age == undefined ? 0 : $scope.param.filter_age,
                        q: $scope.q,
                        rank: $scope.rank,
                        export: type,
                        from_date: $scope.from_date,
                        to_date: $scope.to_date
                    }
                }).then(function (res) {
					//console.log(res);
                    $scope.export_data = res.data;
                    $scope.generating = true;
                    generateReport();
                    $scope.export_page = $scope.export_all = false;
                }, function (res) {
                    $scope.export_page = $scope.export_all = false;
                })
            };
            $scope.file_count = 1;

            function generateReport() {
                $http({
                    url: '<?php echo e(URL::to('HRM/generate/file')); ?>/' + $scope.export_data.id,
                    method: 'post'
                }).then(function (res) {
                    if ($scope.export_data.total_file > $scope.file_count) {
                        setTimeout(generateReport, 1000);
                        if (res.data.status) $scope.file_count++;
                    } else {
                        $scope.generating = false;
                        $scope.file_count = 1;
                        window.open($scope.export_data.download_url, '_blank')
                    }
                }, function (res) {
                    if ($scope.export_data.file_count > $scope.file_count) {
                        setTimeout(generateReport, 1000)
                    }
                })
            }

            $scope.search = function () {
            };
            $scope.filterMiddlePage = function (value, index, array) {
                var minPage = $scope.currentPage - 3 < 0 ? 0 : ($scope.currentPage > array.length - 4 ? array.length - 8 : $scope.currentPage - 3);
                var maxPage = minPage + 7;
                if (value.pageNum >= minPage && value.pageNum <= maxPage) {
                    return true;
                }
            };
            $scope.changeRank = function (i) {
                $scope.rank = i;
                $scope.loadPage()
            };
            $scope.loadRank = function (id) {
                // $scope.rank = i;
                //alert("anik");exit;
                // $scope.loadPage()
                if (id) {
                    $scope.submitting = true;
                    $http({
                        url: "<?php echo e(URL::to('HRM/rankPromotion')); ?>",
                        method: 'post',
                        data: angular.toJson({
                            request_id:id
                        })
                    }).then(function (response) {
                        console.log(response);
                        $scope.submitting = false;
                        if (response.data.status) {
                            notificationService.notify('success', response.data.message);
                            $("#confirm-panel-modal").modal('hide');
                            $scope.loadPage();
                        } else {
                            notificationService.notify('error', response.data.message)
                        }
                        $scope.ansars.splice($scope.ansars.indexOf($scope.getSingleRow), 1)
                    }, function (response) {
                        $scope.submitting = false;
                        notificationService.notify('error', "An unexpected error occur. Error code :" + response.status);
                    })
                   // alert($scope.comment );
                }
            };

            $scope.modal = function (data) {
                 console.log(data);
                //  alert($scope.comment );
                $scope.printLetter = false;
                $scope.getSingleRow = data;
                
            }

            function capitalizeLetter(s) {
                return s.charAt(0).toUpperCase() + s.slice(1);
            }

            function sum(t) {
                var s = 0;
                for (var i in t) {
                    s += parseInt(t[i])
                }
                return s;
            }
        });
        $(function () {
            $("#print-report").on('click', function (e) {
                $("#print-area").remove();
                $("#print_table table").removeClass('table table-bordered');
                $('body').append('<div id="print-area">' + $("#print_table").html() + '</div>');
                window.print();
                $("#print_table table").addClass('table table-bordered');
                $("#print-area").remove()
            })
        })
    </script>
    <div ng-controller="PromotionController">
        <section class="content">
            <div>
                <div class="box box-solid">
                    <div class="overlay" ng-if="allLoading">
                    <span class="fa">
                        <i class="fa fa-refresh fa-spin"></i> <b>Loading...</b>
                    </span>
                    </div>
                    <div class="box-body">
                        <div class="box-body" id="change-body">
                            <filter-template
                                    show-item="['range','unit','thana','rank','gender']"
                                    type="all"
                                    range-change="loadPage()"
                                    unit-change="loadPage()"
                                    thana-change="loadPage()"
                                    kpi-change="loadPage()"
                                    rank-change="loadPage()"
                                    gender-change="loadPage()"
                                    on-load="loadPage()"
                                    start-load="range"
                                    field-width="{range:'col-sm-2',unit:'col-sm-2',thana:'col-sm-2',kpi:'col-sm-2',rank:'col-sm-2',gender:'col-sm-2'}"
                                    data="params"
                                    
                            ></filter-template>
                            
                            <button id="print-report" class="btn btn-default"><i
                                class="fa fa-print" ></i>&nbsp;Print
                            </button>
                            
                            <div class="loading-data"><i class="fa fa-4x fa-refresh fa-spin loading-icon"></i>
                            </div>

                            <div id="print_table">
                            <div class="table-responsive">
                                <table class="table  table-bordered table-striped" id="ansar-table">
                                    <caption>
                                        <div class="row">
                                            <div class="col-sm-8">
                                                <span class="text text-bold" style="color:#000000;font-size: 1.1em">Total : [[total.toLocaleString()]]</span>
                                                
                                            </div>
                                            
                                            <div class="col-md-4 col-sm-12" style="margin-top: 10px">
                                                <database-search q="q" queue="queue" on-change="loadPage()"></database-search>
                                            </div>
                                        </div>
                                        <?php /* <a class="btn btn-primary btn-xs"
                                           href="<?php echo e(URL::route('loadPage',['export'=>1])); ?>">
                                            <i class="fa fa-file-excel-o"></i> Export
                                        </a> */ ?>
                                    </caption>
                                    <tr>
                                        
                                        <th class="text-center">SL</th>
                                        <th class="text-center">Ansar ID</th>
                                        <th class="text-center">Ansar Name</th>
                                        <th class="text-center">Rank</th>
                                        <th class="text-center">Circular Name</th>
                                        <th class="text-center">Not Verified Status</th>
                                        <th class="text-center">Rank Promotion</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Action</th>
                                        
                                    </tr>
                                    <tr ng-show="allPromotionAnsar.length>0"
                                        ng-repeat="promotionAnsar in allPromotionAnsar">
                                        
                                        <td class="text-center">[[$index+1]]</td>
                                        <td class="text-center">[[promotionAnsar.ansar_id]]</td>
                                        <td class="text-center">[[promotionAnsar.name]]</td>
                                        <td class="text-center">[[promotionAnsar.rank]]</td>
                                        <td class="text-center">[[promotionAnsar.circular_name]]</td>
                                        <td class="text-center">[[promotionAnsar.not_verified_status]]</td>
                                        <td class="text-center">[[promotionAnsar.promoted_status]]</td>
                                        <td class="text-center">[[promotionAnsar.status]]</td>
                                        <td>
                                            <div class="col-xs-1">
                                                <a href="" data-toggle="modal" ng-click="ppp(a.id,$index)"
                                                data-toggle="modal" modal-show data="promotionAnsar" callback="modal(data)" target="#back-to-previous-modal"
                                                data-target="#back-to-previous-modal" ng-disabled="promotionAnsar.status=='Completed'"
                                                   class="btn btn-danger btn-xs" title="Back to Previous">
                                                    <i class="fa fa-backward"></i>
                                                </a>
                                            </div>
                                            <div class="col-xs-1">
                                                <a href="" data-toggle="modal" ng-click="ppp(promotionAnsar.id)"
                                                ng-disabled="promotionAnsar.promoted_status==1"
                                                data-toggle="modal" modal-show data="promotionAnsar" callback="modal(data)" target="#verify-rank-promotion-modal"
                                                data-target="#verify-rank-promotion-modal" class="btn btn-warning btn-xs"
                                                   title="Make Verified ">
                                                    <i class="fa fa-check-square-o"></i>
                                                </a>
                                            </div>
                                            
                                            <div class="col-xs-1">
                                                <a href="" data-toggle="modal" ng-click="ppp(promotionAnsar.id)"
                                                ng-disabled="promotionAnsar.not_verified_status==0||promotionAnsar.promoted_status==0||promotionAnsar.status=='Completed'"
                                                class="btn btn-info btn-xs" data-toggle="modal" modal-show data="promotionAnsar" callback="modal(data)" target="#send-to-panel-modal"
                                                data-target="#send-to-panel-modal" title="Send to Panel">
                                                    <i class="fa fa-paper-plane"></i>
                                                </a>
                                            </div>
                                        </td>
                                        <?php /* <td>[[promotionAnsar.reporting_date|dateformat:'DD-MMM-YYYY']]</td>
                                        <td>[[promotionAnsar.freez_date|dateformat:'DD-MMM-YYYY']]</td> */ ?>
                                        
                                       
                                    </tr>
                                    <tr ng-show="allPromotionAnsar.length==0">
                                        <td class="warning" colspan="11">No information found</td>
                                    </tr>
                                </table>
                            </div>
                            </div>
                            
                            <div class="row" ng-if="response.total>response.per_page">
                                <div class="col-sm-3">
                                    <div class="form-group" ng-init="params.limit = '50'">
                                        <label for="" class="control-label">Load limit</label>
                                        <select class="form-control" ng-model="params.limit"
                                                ng-change="loadPage()">
                                            <option value="50">50</option>
                                            <option value="100">100</option>
                                            <option value="150">150</option>
                                            <option value="200">200</option>
                                            <option value="300">300</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-9">
                                    <div ng-bind-html="view" compile-g-html></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="back-to-previous-modal" class="modal fade" role="dialog">
                <div class="modal-dialog" style="width: 40%;overflow: auto;">
                    <div class="modal-content">
                      
            <form class="form" role="form" method="post" ng-submit="backtoPrevious(getSingleRow.row_id)">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"
                            ng-click="modalOpen = false">&times;
                    </button>
                    <h3 class="modal-title">Back to Previous</h3>
                    <h4 class="modal-title">Row ID:[[getSingleRow.row_id]]</h4>
                </div>
                <div class="modal-body">
                    <div class="register-box" style="width: auto;margin: 0">
                        <div class="register-box-body  margin-bottom">
                            <div class="row">
                                <div class="col-sm-6">
                                    <?php /* <div class="form-group">
                                        <label class="control-label">Comment.&nbsp;&nbsp;&nbsp;<span
                                                    ng-show="isVerifying"><i
                                                        class="fa fa-spinner fa-pulse"></i>&nbsp;Verifying</span><span
                                                    class="text-danger"
                                                    ng-if="isVerified&&!comment">Comment is required.</span><span>
                                                    
                                        </label>
                                        <input ng-model="comment"
                                               type="text" class="form-control" name="comment"
                                               placeholder="Enter Comments." required>
                                    </div> */ ?>
                                    
                                </div>
                            </div>
                       
                            <button class="btn btn-primary pull-right" type="submit">
                    <i ng-show="submitting" class="fa fa-spinner fa-pulse"></i>&nbsp;Confirm
                </button>
                
                
                        </div>
                    </div>

                </div>
            
            </form>
                    </div>
                </div>
            </div>

            <div id="send-to-panel-modal" class="modal fade" role="dialog">
                <div class="modal-dialog" style="width: 40%;overflow: auto;">
                    <div class="modal-content">
                      
            <form class="form" role="form" method="post" ng-submit="sendToPanel(getSingleRow.row_id)">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"
                            ng-click="modalOpen = false">&times;
                    </button>
                    <h3 class="modal-title">Confirmation for Send to Panel</h3>
                    <h4 class="modal-title">Row ID:[[getSingleRow.row_id]]</h4>
                </div>
                <div class="modal-body">
                    <div class="register-box" style="width: auto;margin: 0">
                        <div class="register-box-body  margin-bottom">
                            <div class="row">
                                <div class="col-sm-6">
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
                                        <?php echo Form::text('panel_date', $value = null, $attributes = array('class' => 'form-control', 'id' => 'panel_date', 'ng_model' => 'panel_date', 'required','date-picker'=>'moment()')); ?>

                                    </div>
                                </div>
                            </div>
                       
                            <button class="btn btn-primary pull-right" type="submit">
                                <i ng-show="submitting" class="fa fa-spinner fa-pulse"></i>&nbsp;Confirm
                            </button>
                
                
                        </div>
                    </div>

                </div>
            
            </form>
                    </div>
                </div>
            </div>

            <div id="verify-rank-promotion-modal" class="modal fade" role="dialog">
                <div class="modal-dialog" style="width: 40%;overflow: auto;">
                    <div class="modal-content">
                    
            <form class="form" role="form" method="post" ng-submit="verifyRankPromotion(getSingleRow.row_id)">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"
                            ng-click="modalOpen = false">&times;
                    </button>
                    <h3 class="modal-title">Verified Confirmation</h3>
                    <h4 class="modal-title">Row ID:[[getSingleRow.row_id]]</h4>
                </div>
                <div class="modal-body">
                    <div class="register-box" style="width: auto;margin: 0">
                        <div class="register-box-body  margin-bottom">
                            <div class="row">
                                <div class="col-sm-6">
                                    
                                               <?php /* <input type="checkbox" show-item="['rank']" ng-model="selected[]" value="checked"
                                               name="Ansar[]" ng-model="param.rankUpdate">
                                               <span class="radio-label" for="submitted">Will you want to update?</span><br/> */ ?>
                                               <?php /* <div class="form-group">
                                               <input type="checkbox" name="manual" ng-model="param.selectionProcess"
                                                        class="radio-label" value="manual"><span> Will you want to update? </span>
                                               </div>
                                                        
                                               <div ng-if="param.selectionProcess=='manual'">
                                                   
                                                    <input type="checkbox" ng-model="selected[]" value=""
                                                    name="APC[]">
                                                    <label class="radio-label" for="submitted">APC</label><br/>
    
                                                    <input type="checkbox" ng-model="selected[]" value=""
                                                    name="PC[]">
                                                    <label class="radio-label" for="submitted">PC</label><br/>
                                               </div> */ ?>
                                               <?php /* <input type="checkbox" ng-model="selected[]" value=""
                                               name="APC[]">
                                               <label class="radio-label" for="submitted">APC</label><br/>

                                               <input type="checkbox" ng-model="selected[]" value=""
                                               name="PC[]">
                                               <label class="radio-label" for="submitted">PC</label><br/> */ ?>

                                               <input type="checkbox" id="rankUpdate" ng-click="rankUpdate()">
                                               <label for="rankUpdate">Will you want to update?</label><br/>
                                               
                                                <?php /* <input type="checkbox" id="ansar" style="display:none" name="ranks[]" value="Ansar"> Ansar <br/>
                                                <input type="checkbox" id="apc" style="display:none" name="ranks[]" value="APC"> APC <br/>
                                                <input type="checkbox" id="pc" style="display:none" name="ranks[]" value="PC"> PC <br/> */ ?>
                                                
                                               <div>
                                                   <input type="checkbox" id="ansar" style="display:none"  ng-click="ansarUpdate()" disabled>
                                                   <label id="ansarLabel" for="ansar" style="display:none" >Ansar </label>
                                                   <input type="checkbox" id="apc" style="display:none" ng-click="apcUpdate(getSingleRow.row_id)" >
                                                   <label id="apcLabel" for="apc" style="display:none">APC</label>
                                                   <input type="checkbox" id="pc" style="display:none" ng-click="pcUpdate(getSingleRow.row_id)">
                                                   <label id="pcLabel" for="pc" style="display:none">PC</label>
                                               </div>
                                               
                                               
                                              
                                </div>
                            </div>
                            <button class="btn btn-primary pull-right" type="submit">
                                <i ng-show="submitting" class="fa fa-spinner fa-pulse"></i>&nbsp;Confirm
                            </button>
                        </div>
                    </div>
                </div>
            </form>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-4">
                    <label for="item_par_page">Show :</label>
                    <select name="item_per_page" ng-change="loadPage()" id="item_par_page"
                            ng-model="itemPerPage">
                        <option value="10" ng-selected="true">10</option>
                        <option value="15">15</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        
                    </select>
                </div>
            </div>
        </section>
        
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('template.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>