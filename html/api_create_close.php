<?php
   	$username="wmi593";
    $password="LCYQr6jxXKRfkGW";
    $data="username=$username&password=$password";
    // $ch=curl_init('https://cs4743.professorvaladez.com/api/clear_session?username=wmi593&password=LCYQr6jxXKRfkGW');
    $ch=curl_init('https://cs4743.professorvaladez.com/api/create_session');
    curl_setopt($ch, CURLOPT_POST,1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch,CURLOPT_HTTPHEADER, array(
        'content-type: application/x-www-form-urlencoded',
        'content-length: '.strlen($data))
    );
    $time_start = microtime(true);
    $result = curl_exec($ch);
    $time_end = microtime(true);
    $execution_time = ($time_end - $time_start)/60;
    curl_close($ch);
    $cinfo=json_decode($result,true);
    if($cinfo[0]="Status: OK" && $cinfo[1]=="MSG: Session Created")
    {
        $sid=$cinfo[2];
        $data="sid=$sid&uid=$username";
        echo "\r\nSession Created Successfully!\r\n";
        echo "> SID: $sid\r\n";
        echo "> Create time: $execution_time\r\n";

        $ch=curl_init('https://cs4743.professorvaladez.com/api/close_session');
        curl_setopt($ch, CURLOPT_POST,1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch,CURLOPT_HTTPHEADER, array(
            'content-type: application/x-www-form-urlencoded',
            'content-length: '.strlen($data))
        );
        $time_start = microtime(true);
        $result = curl_exec($ch);
        $time_end = microtime(true);
        $execution_time = ($time_end - $time_start)/60;
        curl_close($ch);
        $cinfo=json_decode($result,true);
        if($cinfo[0]="Status: OK")
        {
            // $sid=$cinfo[2];
            $data="sid=$sid&uid=$username";
            echo "\r\nSession Closed Successfully!\r\n";
            echo "> SID: $sid\r\n";
            echo "> Close time: $execution_time\r\n";
        }
        else
        {
        echo $cinfo[0];
        echo "\r\n";
        echo $cinfo[1];
        echo "\r\n";
        echo $cinfo[2];
        echo "\r\n";
        }
    }
    else
    {
        echo $cinfo[0];
        echo "\r\n";
        echo $cinfo[1];
        echo "\r\n";
        echo $cinfo[2];
        echo "\r\n";
    
    }
?>