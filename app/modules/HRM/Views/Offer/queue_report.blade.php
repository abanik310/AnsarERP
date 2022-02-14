@extends('template.master')
@section('title','Monitor Dashboard')
@section('breadcrumb')
@endsection
@section('content')
<style>
   
    ul.nav-tabs li.active a {
        background: #01655d !important;
        color: white !important;
    }
</style>

<div ng-controller="OfferController" id="offer-view">
    <section class="content">
       
        <div class="row">
              <!-- <a href="{{URL::to('HRM')}}" class="small-box-footer">-->
                    <div class="col-lg-4 col-xs-6">
                        <!-- small box -->
                        <div class="small-box bg-aqua">
                            <div class="inner">
                                <h3 style="color: #ffffff">Offer Queue</h3>

                                <p style="color: #ffffff"><span>Total Offer Queue</span><span style="float:right">{{$data['total_queue_offer']}}</p>
                                <p style="color: #ffffff"><span>Total Offer Queue(Today)</span><span style="float:right">{{$data['total_queue_today']}}</span></p>
                            </div>
                            <div class="small-box-footer" style="height: 15px"></div>
                        </div>
                    </div>
               <!--  </a> -->
            
               <!-- <a href="{{URL::to('HRM')}}" class="small-box-footer">-->
                    <div class="col-lg-4 col-xs-6">
                        <!-- small box -->
                         <div class="small-box bg-yellow">
                            <div class="inner">
                                <h3 style="color: #ffffff">Unauthorized</h3>

                                <p style="color: #ffffff"><span>Total Unauthorized Blocked</span><span style="float:right">{{$data['total_unwanted_blocked'][0]->total_ansar}}</p>
                                <p style="color: #ffffff"><span>Total Unauthorized Locked</span><span style="float:right">{{$data['total_unwanted_locked'][0]->total_ansar}}</span></p>
                            </div>
                            <div class="small-box-footer" style="height: 15px; background: #ADADAD"></div>
                        </div>
                    </div>
                <!--</a>-->
                
                <div class="col-lg-4 col-xs-6">
                        <!-- small box -->
                         <div class="small-box bg-blue">
                            <div class="inner">
                                <h3 style="color: #ffffff">Server</h3>

                                <p style="color: #ffffff"><span>Supervisor Status</span><span style="float:right">Online</p>
                                <p style="color: #ffffff"><span>PM Tool Status</span><span style="float:right">Running</span></p>
                            </div>
                            <div class="small-box-footer" style="height: 15px; background: #ADADAD"></div>
                        </div>
                    </div>
        </div>
        <div class="row text-center">
            <img src="{{asset('dist/img/monitoring.gif')}}">
        </div>
    </section>
    
</div>
<script>
    $(function () {
    $("#pc-table").sortTable()
    });
</script>
@stop