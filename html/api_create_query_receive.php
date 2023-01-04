<?php
include("functions.php");
$dblink=db_connect("Document_Manager");

$username="wmi593";
$password="LCYQr6jxXKRfkGW";
$data="username=$username&password=$password";

echo "Starting up\n";
// $ch=curl_init('https://cs4743.professorvaladez.com/api/clear_session?username=wmi593&password=LCYQr6jxXKRfkGW');
$ch=curl_init('https://cs4743.professorvaladez.com/api/create_session');
curl_setopt($ch, CURLOPT_POST,1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
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
    echo "Started\n";
    $sid=$cinfo[2];
    $data="sid=$sid&uid=$username";
    echo "\r\nSession Created Successfully!\r\n";
    echo "> SID: $sid\r\n";
    echo "> Create time: $execution_time\r\n";

    echo "Querying files.";
    $ch=curl_init('https://cs4743.professorvaladez.com/api/query_files');
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
    if($cinfo[0]=="Status: OK")
    {
        
        if($cinfo[1]=="Action: None")
        {
            echo "No files found. Terminating session.";
            echo "\r\nNo new files to import \r\n";
            echo "> SID: $sid\r\n";
            echo "> UID: $username\r\n";
            echo "> Query execution time: $execution_time\r\n";
        }
        else
        {
            echo "Files found, importing";
            $tmp=explode(":",$cinfo[1]);
            $files=explode(",",$tmp[1]);
            echo "\r\nNumber of new files to import found: ".count($files)."\r\n";
            foreach($files as $key=>$value)
            {
                $tmp=explode("/",$value);
                $file=$tmp[4];
                echo "\r\n> File: $file\r";
                $data="sid=$sid&uid=$username&fid=$file";
                $ch=curl_init('https://cs4743.professorvaladez.com/api/request_file');
                curl_setopt($ch, CURLOPT_POST,1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'content-type: application/x-www-form-urlencoded',
                    'content-length: '.strlen($data))
                );
                $time_start = microtime(true);
                $result = curl_exec($ch);
                $time_end = microtime(true);
                $execution_time = ($time_end - $time_start)/60;
                $content=$result;
                $fp=fopen("/var/www/html/receive/$file","wb");
                fwrite($fp,$content);
                fclose($fp);
                echo "\r\n$file written to /receive.";
                // upload file to db as inactive

            
            }
            echo "\n> Query Files Time: $execution_time\r\n";
            echo "File transfer(s) complete. Terminating session.";
        }

        // $sid=$cinfo[2];
        $data="sid=$sid";
        $ch=curl_init('https://cs4743.professorvaladez.com/api/close_session');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
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
            echo "Closed.";
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