<table style="width: 100%" border="1">
    <tr>
        <th style="width:10px;">Sl No.</th>
	<th style="width:10px;">Roll No.</th>
        <th>Applicant Name</th>
        <th>Applicant ID</th>
        <th>Father Name</th>
        <th>National ID No.</th>
        <th>District</th>
        <th>Thana</th>
        <th>Height</th>
        <th>Marks</th>
        <th>Education(Max)</th>
        <th>Marks</th>
        <th>VDP/TDP Training</th>
        <th>Marks</th>
		<th>Technical Training</th>
        <th>Marks</th>
		<th>Sports</th>
        <th>Marks</th>
        <th>Written Mark</th>
        <th>Viva Mark</th>
        <th>Total Marks</th>        
        <th>Remarks</th>
    </tr>
    @if(count($applicants))
        @foreach($applicants as $a)
            <?php //$q = $a->govQuota;$pic =$a->profile_pic ?>
            <tr>
                <td style="width: 10px">{{($index++).''}}</td>
		        <td>{{$a->roll_no}}</td>
                <td>{{$a->applicant_name_bng}}</td>
                <td>{{$a->applicant_id}}</td>
                <td>{{$a->father_name_bng}}</td>
                <td>{{$a->national_id_no}}</td>
                <td>{{$a->district->unit_name_bng}}</td>
                <td>{{$a->thana->thana_name_bng}}</td>
                <td>{{$a->height_feet}} feet {{$a->height_inch}} inch</td>
                <td></td>
                <td>{{$a->education()->orderBy('priority','desc')->first()->education_deg_eng}}</td>
                <td></td>
                <td>@if($a->training_info == 'VDP/TDP Training')<b>YES</b>@endif</td>
                <td></td>
				<td>@if($a->technical_training)<b>YES</b>@endif</td>
                <td></td>				 
                <td>@if($a->sports)<b>YES</b>@endif</td>				
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
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