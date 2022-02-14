<aside class="main-sidebar" ng-controller="MenuController">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">

        <ul class="sidebar-menu">
            <li>
                <a href="{{URL::to('/HRM')}}">
                    <i class="fa fa-dashboard"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            @include('HRM::Partial_view.partial_menu',['menus'=>config('menu.hrm')])
            {{-- added by anik --}}
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-sitemap"></i>
                    <span>Unit Company</span>
                    <i class="fa fa-angle-right pull-right"></i>
                 </a>
                <ul class="treeview-menu">
				   
                    <li>
                        <a href="{{URL::to('/HRM/show_available_unit_company_ansar_list')}}">
                            <i class="fa fa-users"></i>
                            <span>Unit Company Wise Ansars</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{URL::to('/HRM/show_pending_unit_company_ansar_list')}}">
                            <i class="fa fa-hourglass-end"></i>
                            <span> Pending Unit Company Wise Ansars</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="treeview">
                <a href="#">
                    <i class="fa fa-level-up"></i>
                    <span>Ansar Promotion</span>
                    <i class="fa fa-angle-right pull-right"></i>
                 </a>
                <ul class="treeview-menu">
				   
                    <li>
                        <a href="{{URL::to('/HRM/promotion')}}">
                            <i class="fa fa-upload"></i>
                            <span>Initial Batch Upload</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{URL::to('/HRM/SendToPanelBatchUploadView')}}">
                            <i class="fa fa-upload"></i>
                            <span>Send to Panel-Batch Upload</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{URL::to('/HRM/promotionList')}}">
                            <i class="fa fa-users"></i>
                            <span>Ansar Promotion List</span>
                        </a>
                    </li>
                    
					
                </ul>
            </li>

            <li class="treeview">
                <a href="#">
                    <i class="fa fa-chrome"></i>
                    <span>Newly Added Menu</span>
                    <i class="fa fa-angle-right pull-right"></i>
                 </a>
                <ul class="treeview-menu">
				   
				   
                    <li>
                        <a href="{{URL::to('/HRM/show_ansar_list/same_kpi_six_month_ansar')}}">
                            <i class="fa fa-dashboard"></i>
                            <span>6 Months Over In Guard</span>
                        </a>
                    </li>
					@if((auth()->user()->type ==11) || (auth()->user()->type ==66) ||(auth()->user()->id == 348))

                    <li>
                        <a href="{{URL::to('/HRM/embodiment_count_view')}}">
                            <i class="fa fa-dashboard"></i>
                            <span>View Daily Embodiment Count Log</span>
                        </a>
                    </li>
					
                    <li>
                        <a href="{{URL::to('/HRM/show_available_ansar_list')}}">
                            <i class="fa fa-dashboard"></i>
                            <span>Unit Wise Offer Available Ansar</span>
                        </a>
                    </li>
                                    
					 @endif
                </ul>
            </li>
                
        </ul>
    </section>
    <!-- /.sidebar -->
    <script>
        $(window).load(function(){
            var l = $('.sidebar-menu').children('li');
            
            function removeMenu(m){

                m.each(function () {
                    //console.log({parent: $.trim($(this).parents('li').eq($(this).parents('li').length-1).children('a').text()),children: m.text()})
                    //alert($(this).children('ul').length+" "+$(this).children('ul').children('li').length)
                    if($(this).children('ul').length>0) {
                        if ($(this).children('ul').children('li').length > 0) {
                            removeMenu($(this).children('ul').children('li'));
                        }
                        else if ($(this).children('ul').children('li').length <= 0) {
                            // alert(m.length)
                            $(this).remove();
                        }
                    }
                })
            }
            removeMenu(l)
        })
    </script>
</aside>