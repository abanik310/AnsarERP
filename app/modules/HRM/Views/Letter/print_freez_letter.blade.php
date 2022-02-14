@if($i==0)
    <h3 style="text-align: center" class="print-hide">Freeze Letter&nbsp;&nbsp;<a href="#" id="print-report">
            <i class="fa fa-print"></i>
        </a>
    </h3>
@endif
<div class="letter">
    @include('HRM::Letter.letter_header',['user'=>$user])
    <div class="letter-body">
        <div class="body-top">
            <h4>“অফিস আদেশ”</h4>
        </div>
        <div class="letter-content-top">
            আনসার বাহিনী আইন ১৯৯৫ খি, এর ধারা ও এর স্মারক নং-৪৪.০৩.০০০০.০৪৪.১০.০৯৯.১৮-০০৩৭, তারিখঃ ০৮/০১/২০১৮ খ্রি. এর সাধারন আনসার, এপিসি ও পিসি অঙ্গীভূতকরণ ও অ-অঙ্গীভূতকরণ নীতিমালা-২০১৭ অনুচ্ছেদ ৬ এর ‘জ’ এর পরিপ্রেক্ষিতে নিম্নবর্ণিত আনসার সদস্যকে <span
                    style="border-bottom: 1px dashed #000000">"{{$mem->reason}}"</span> কারনে ফ্রীজ করা হলো।
        </div>
		
        <div class="letter-content-middle">
            <h4>“তফসিল "ক" (ফ্রীজ)”</h4>
            <table class="table table-bordered">
                <tr>
                    <th style="width: 1%">ক্রমিক<br>নং</th>
                    <th style="width: 1%">আইডি<br>নং</th>
                    <th style="width: 1%">পদবী</th>
                    <th style="width: 150px !important;">নাম ও<br>পিতার নাম</th>
                    <th>ঠিকানা:<br>গ্রাম , পোস্ট, উপজেলা ও জেলা</th>
                    <th>সংস্থার নাম ও<br>উপজেলা/থানা</th>
					<!--<th style="width: 1%">অঙ্গীভূতির<br>তারিখ</th>-->
                    <th style="width: 1%">ফ্রীজ<br>তারিখ</th>
                </tr>
                <?php $ii = 1 ?>
                @for($j=0;$j<count($result);$j++)
                    @if(isset($result[$j]))
                        <tr>
                            <td>{{LanguageConverter::engToBng($ii++)}}</td>
                            <td>{{LanguageConverter::engToBng($result[$j]->ansar_id)}}</td>
                            <td>{{$result[$j]->rank}}</td>
                            <td>{{$result[$j]->name}}<br>{{$result[$j]->father_name}}</td>
                            <td>{{$result[$j]->village_name}},&nbsp;{{$result[$j]->pon}},&nbsp;{{$result[$j]->thana}},&nbsp;{{$result[$j]->unit}}</td>
                            <td>{{$result[$j]->kpi_name.", ".$result[$j]->kpi_thana}}</td>
							
							<!--<td>@if($result[$j]->embodiment_date != null)
							 {{LanguageConverter::engToBng(date('d/m/Y',strtotime($result[$j]->embodiment_date)))}}
							   @else
							   
							   @endif
							 </td>-->
                            <td>{{LanguageConverter::engToBng(date('d/m/Y',strtotime($result[$j]->freez_date)))}}</td>
                        </tr>
                    @endif
					
                @endfor
                @if(count($result)<=0)
                    <tr>
                        <td colspan="8">No Ansar Found</td>
                    </tr>
                @endif
            </table>
			<h4 style="text-decoration: none;text-align: left;">মন্তব্যঃ  {{$mem->comment}}</h4>
        </div>
        @include('HRM::Letter.letter_footer',['user'=>$user])
    </div>
</div>