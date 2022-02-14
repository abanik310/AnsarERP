<?php

  echo $applicants->applicant_name_bng;
  //echo $applicants->profile_pic;
  $profile_img=str_replace("/storage/receruitment/","/var/www/html/ansarerp/local/storage/receruitment/",$applicants->profile_pic);
 // echo $profile_img;
 // echo '<br><br>';
 echo 'aaAaaAa';
 echo public_path();
?>

<img src="<?php echo public_path();?>/local/storage/receruitment/tbjOzNNTWA_profile_pic_1572934443293.jpg" alt="Girl in a jacket" width="500" height="600">
