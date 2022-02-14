<table style="width: 100%" border="1">
    <tr>
	   <th colspan="20" align="center">
	   ২২তম ব্যাচ (পুরুষ) ব্যাটালিয়ন আনসার প্রাথমিক নির্বাচন-২০২১ এ অংশগ্রহণকারী প্রার্থীদের প্রাথমিক স্বাস্থ্য, লিখিত ও মৌখিক পরীক্ষায় প্রাপ্ত নম্বরের ভিত্তিতে প্রাথমিক ফলাফলঃ <br>
	   রেঞ্জঃ ............................, জেলাঃ ..........................., তারিখঃ ....................... 

	   <th>
	</tr>
    <tr>
        <th  style="width: 10px">ক্রমিক নং</th>
        <th >আইডি নং</th>
        <th >কোড নং</th>
		<th >রোল নং</th>
        <th >নাম,পিতা ও মাতার নাম<br>জাতীয় পরিচয়পত্র নং(জাপা নং) এবং<br>স্মার্ট কার্ড নং(যদি থাকে) </th>
        <th >স্থায়ী ঠিকানা<br>(গ্রাম,ডাকঘর,থানা/উপজেলা,<br>জেলা ও বিভাগ)</th>
		<th >মোবাইল নম্বর)</th>
        <th >শিক্ষাগত যোগ্যতা</th>
        <th >জন্ম তারিখ</th>
        <th >বয়স<br>(২৬/০৮/২০২১ খ্রিঃ তারিখ)</th>
        <th >উচ্চতা</th>
        <th>ওজন</th>
        <th>গৃহীত পরীক্ষাসমূহের প্রাপ্ত নম্বর</th>
        <th></th>
        <th></th>
        <th></th>
        <th >ছবি</th>
        <th >মেধাক্রম</th>
        <th >ফলাফল<br>(নির্বাচিত/<br>অনির্বাচিত/<br>অপেক্ষমাণ)</th>
        <th >মন্তব্য/<br>কোটা</th>
    </tr>
    <tr>
        <th></th>
		<th></th>
		<th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th>স্বাস্থ্য<br>পরীক্ষা<br>যোগ্য/অযোগ্য</th>
        <th>লিখিত<br>পরীক্ষা<br>পূর্ণমান-৩৫</th>
        <th>মৌখিক<br>পরীক্ষা <br>পূর্ণমান-১৫</th>
        <th>মোট প্রাপ্ত<br>নম্বর</th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>

    </tr>

    @if(count($applicants))
        @foreach($applicants as $a)
            <?php $q = $a->govQuota;$pic =$a->profile_pic ?>
            <tr>
                <td style="width: 10px">{{($index++).''}}</td>
                <td>{{$a->applicant_id}}</td>
                <td>&nbsp;</td>
				<td>{{$a->roll_no}}</td>
                <td>{{$a->applicant_name_bng.", ".$a->father_name_bng.", ".$a->mother_name_bng}}<br>{{$a->national_id_no}}</td>
                <td>{{$a->village_name_bng.", ".$a->post_office_name_bng.", ".$a->thana->thana_name_bng}}<br>{{$a->district->unit_name_bng.", ".$a->division->division_name_bng}}</td>
				<td>{{$a->mobile_no_self}}</td>
                <td>{{$a->education()->orderBy('priority','desc')->first()->education_deg_eng}}</td>
                <td>{{$a->date_of_birth}}</td>
                <td>{{\Carbon\Carbon::parse($a->date_of_birth)->diff(\Carbon\Carbon::parse("26-08-2021"))->format("%yy %mm %dd")}}</td>
                <td>{{$a->height_feet}} feet {{$a->height_inch}} inch</td>
                <td>{{$a->weight}} kg</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>@if($pic&&file_exists($pic)&&getimagesize($pic))<img src="{{$pic}}" width="100" height="100">@endif</td>
                <td></td>
                <td></td>
                <td>{{$a->circular_applicant_quota_id?$a->circularQuota->quota_name_bng:'N\A'}}</td>
				
                
            </tr>
        @endforeach
    @else
        <tr>
            <td colspan="12" class="bg-warning">
                No applicants found
            </td>
        </tr>
    @endif

</table>