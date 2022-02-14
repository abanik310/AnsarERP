<?php
/**
 * Created by PhpStorm.
 * User: shuvo
 * Date: 2/11/2018
 * Time: 11:58 AM
 */

namespace App\Helper;



trait SMSTrait
{
    public function sendSMS($mobile_no, $message)
    {

   $BanglaText = "ব্যাটালিয়ন আনসার পদে প্রাথমিক বাছাইয়ের জন্য প্রবেশপত্র ও প্রয়োজনীয় সকল কাগজপত্রসহ আপনাকে আগামী ১৯.১০.২০২০ খ্রিঃ তারিখ সকাল ০৮০০ ঘটিকায় মাস্ক পরিধান করে ও করোনা স্বাস্থ্যবিধি মেনে আনসার ও ভিডিপি একাডেমি, সফিপুর, গাজীপুর উপস্থিত থাকার জন্য অনুরোধ করা হলো। বিস্তারিতঃ www.ansarvdp.gov.bd";

    $str = strtoupper(bin2hex(iconv('UTF-8', 'UCS-2BE', $BanglaText)));
    $mobile = '01817551138';  
    $phone = '88' . trim($mobile);
  $user = 'ansarapi';
    $pass = '75@5S01j';
    $sid = 'ANSARVDPBANGLA';
    $url = "http://sms.sslwireless.com/pushapi/dynamic/server.php";

  $param = "user=$user&pass=$pass&sms[0][0]=$phone&sms[0][1]=" . urlencode($str) . "&sid=$sid";
    $crl = curl_init();
    curl_setopt($crl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($crl, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($crl, CURLOPT_URL, $url);
    curl_setopt($crl, CURLOPT_HEADER, 0);
    curl_setopt($crl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($crl, CURLOPT_POST, 1);
    curl_setopt($crl, CURLOPT_POSTFIELDS, $param);
    $response = curl_exec($crl);
    curl_close($crl);
    var_dump($response);
    }
}