<table style="width: 100%" border="1">
    <tr>
        <th style="width:10px;">Sl No.</th>
		<th>Applicant Name</th>
	    <th style="width:10px;">Roll No.</th>
        <th>Applicant ID</th>
        <th>Father Name</th>
		<th>Mother Name</th>
        <th>DOB</th>
		<th>Division</th>
        <th>District</th>
        <th>Thana</th>
		<th>PO</th>
		<th>Village</th>
		<th>Union</th>
        <th>Height</th>
        <th>Education</th>
        <th>Quota</th>
        <th>Mobile No</th>
    </tr>
    @if(count($applicants))
        @foreach($applicants as $a)
            <?php //$q = $a->govQuota;
			//$pic =$a->profile_pic ?>
            <tr>
                <td style="width: 10px">{{($index++).''}}</td>
				<td>{{$a->applicant_name_bng}}</td>
		        <td>{{$a->roll_no}}</td>
                <td>{{$a->applicant_id}}</td>
                <td>{{$a->father_name_bng}}</td>
				<td>{{$a->mother_name_bng}}</td>
				<td>{{$a->date_of_birth}}</td>
				<td>{{$a->division->division_name_bng}}</td>
                <td>{{$a->district->unit_name_bng}}</td>
                <td>{{$a->thana->thana_name_bng}}</td>
				<td>{{$a->post_office_name_bng}}</td>				
				<td>{{$a->village_name_bng}}</td>
				<td>{{$a->union_name_bng}}</td>
                <td>{{$a->height_feet}} feet {{$a->height_inch}} inch</td>
                <td>{{$a->education()->orderBy('priority','desc')->first()->education_deg_eng}}</td>
                <td>{{$a->circular_applicant_quota_id?$a->circularQuota->quota_name_bng:'N\A'}}</td>
                <td>{{$a->mobile_no_self}}</td>
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