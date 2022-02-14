<table style="width: 100%" border="1">
    <tr>
        <th style="width:10px;">Sl No.</th>
	@if($ctype=='')

		<th style="width:10px;">Roll No.</th>
     @endif
        <th>Applicant Name</th>
        <th>Applicant ID</th>
		<!--<th>Designation</th>-->
        <th>Father Name</th>
        <th>Mother Name</th>
        <th>Birth Date</th>
        <th>National ID No.</th>
        <th>Division</th>
        <th>District</th>
        <th>Thana</th>
        <th>Village</th>
        <th>Post Office</th>
        <th>Union</th>
        <th>Height</th>
        <th>Education(Max)</th>
        <th>VDP/TDP Training</th>
        <th>Technical Training</th>
        <th>Sports</th>
        <th>Weight</th>
        <th>Mobile No.</th>
		@if($ctype=='apc_training')
            <th>Punishment(block, black,freez for punishment,not verified)</th>
            <th>embodiment duration</th>
        @endif
        <th>Status</th>
        <th>Photo</th>
    </tr>
   <!-- <tr>
        <th></th>
		@if($ctype=='')

		<th></th>
     @endif
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
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
		@if($ctype=='apc_training')
            <th></th>
            <th></th>
        @endif
        <th></th>
        <th></th>
    </tr> -->
    @if(count($applicants))
        @foreach($applicants as $a)
            <?php //$q = $a->govQuota;
			$pic =$a->profile_pic 
			?>
            <tr>
                <td style="width: 10px">{{($index++).''}}</td>
			@if($ctype=='')
	            <td>{{$a->roll_no}}</td>
            @endif
                <td>{{$a->applicant_name_bng}}</td>
                <td>{{$a->applicant_id}}</td>
				<!--<td>{{$a->designationdata->name_bng}}</td>-->
                <td>{{$a->father_name_bng}}</td>
                <td>{{$a->mother_name_bng}}</td>
                <td>{{$a->date_of_birth}}</td>
                <td>{{$a->national_id_no}}</td>
                <td>{{$a->division->division_name_bng}}</td>
                <td>{{$a->district->unit_name_bng}}</td>
                <td>{{$a->thana->thana_name_bng}}</td>
                <td>{{$a->village_name_bng}}</td>
                <td>{{$a->post_office_name_bng}}</td>
                <td>{{$a->union_name_bng}}</td>
                <td>{{$a->height_feet}} feet {{$a->height_inch}} inch</td>
                <td>{{$a->education()->orderBy('priority','desc')->first()->education_deg_eng}}</td>
                <td>@if($a->training_info == 'VDP/TDP Training')<b>YES</b>@endif</td>
                <td>@if($a->technical_training)<b>YES</b>@endif</td>
                <td>@if($a->sports)<b>YES</b>@endif</td>
                <td>{{$a->weight}} kg</td>
                <td>{{$a->mobile_no_self}}</td>
				@if($ctype=='apc_training')
					<td>@if(isset($a->freezlist))
					  freez,  
					@endif
					@if(isset($a->blocklist))
					   block,   
					@endif
					@if(isset($a->blacklist))
					   black,
					@endif
					
					</td>
					<td>{{$a->expCurrentDesignation()}}</td>
				@endif
                <td>{{$a->status}}</td>
                <td>@if($pic&&file_exists($pic)&&getimagesize($pic))<img src="{{$pic}}" width="100" height="100">@endif</td> 
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