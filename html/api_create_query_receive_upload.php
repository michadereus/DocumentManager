<?php
include("functions.php");
$dblink=db_connect("Document_Manager");

$username="wmi593";
$password="LCYQr6jxXKRfkGW";
$data="username=$username&password=$password";

echo "Starting up\n";
curl_exec(curl_init('https://cs4743.professorvaladez.com/api/clear_session?username=wmi593&password=LCYQr6jxXKRfkGW'));
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
    $datetime=date("Y-m-d H:i:s");
    $sql="Insert into `Session Log` (`idLog`,`sid`,`uid`,`date`,`message`,`query`,`errorStatus`) values 
        ('0', '$sid', '$username','$datetime','Session created', '/api/create_session', '0')";
    $dblink->query($sql) or
        die("Something went wrong with $sql<br>".$dblink->error);


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
            $datetime=date("Y-m-d H:i:s");
            $sql="Insert into `Session Log` (`idLog`,`sid`,`uid`,`date`,`message`,`query`,`errorStatus`) values 
                ('0', '$sid', '$username','$datetime','No new files to import. Session closed', '/api/query_files', '1')";
            $dblink->query($sql) or
                die("Something went wrong with $sql<br>".$dblink->error);
        }
        else
        {
            echo "Files found, importing";
            $tmp=explode(":",$cinfo[1]);
            $files=explode(",",$tmp[1]);
            $count=count($files);
            echo "\r\nNumber of new files to import found: ".count($files)."\r\n";
            
            foreach($files as $key=>$value)
            {
                if($count==1){
                    echo "$tmp[0]";
                    break;
                }
                // echo "$tmp $value";
                $tmp=explode("/",$value);
                $file=$tmp[4];
                echo "\r\n> File: $file";
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
                $path="/var/www/html/receive/";
                // if (is_writable("$path$file")) {
                $fp=fopen("$path$file","wb");
                fwrite($fp,$content);
                fclose($fp);
                echo "\r\nwritten to /receive/.";

                $datetime=date("Y-m-d H:i:s");
                $sql="Insert into `Session Log` (`idLog`,`sid`,`uid`,`date`,`message`,`query`,`errorStatus`) values 
                ('0', '$sid', '$username','$datetime','File succesfully received. Uploading to database now.', '/api/request_file', '0')";
                $dblink->query($sql) or
                    die("Something went wrong with $sql<br>".$dblink->error);

                // }
                // else {
                //     echo "\n unable to write to $path$file";
                //     $datetime=date("Y-m-d H:i:s");
                //     $sql="Insert into `Session Log` (`idLog`,`sid`,`uid`,`date`,`message`,`query`,`errorStatus`) values 
                //     ('0', '$sid', '$username','$datetime','Error writing file to received, terminating session.', '/api/request_file', '1')";
                //     $dblink->query($sql) or
                //         die("Something went wrong with $sql<br>".$dblink->error);
                //     break;
                // }

                $type=filetype("$path$file");
                $datetime=date("Y-m-d H:i:s");
                $new_filename="$datetime--$file";
                $content_clean=addslashes($content);
                $tmp=explode("-",$file);
                $tag=$tmp[1];
                
                $path="/var/www/html/uploads/";
                $sql="Insert into `File` (`idFile`,`nameUser`,`fileTag`,`fileName`,`filePath`,`fileType`,`fileContent`,`dateReceived`,`dateFileUpload`,`dateLastViewed`,`fileStatus`) values 
                ('0','$username','$tag','$new_filename','$path','$type','$content_clean','$datetime','$datetime','$datetime','active')";
                $dblink->query($sql) or
                    die("Something went wrong with $sql<br>".$dblink->error);

                // if (is_writable("$path$new_filename")) {
                $fp=fopen("$path$new_filename","wb") or
                    die("Could not open $path$new_filename for writing");
                fwrite($fp,$content);
                fclose($fp);
                // }else{
                    // echo "\n unable to write to $path$new_filename";
                    // $datetime=date("Y-m-d H:i:s");
                    // $sql="Insert into `Session Log` (`idLog`,`sid`,`uid`,`date`,`message`,`query`,`errorStatus`) values 
                    // ('0', '$sid', '$username','$datetime','Error writing file to upload, terminating session.', '/api/request_file', '1')";
                    // $dblink->query($sql) or
                    //     die("Something went wrong with $sql<br>".$dblink->error);
                // break;
                // }
                echo "\r\nwritten to /uploads/.";
                $datetime=date("Y-m-d H:i:s");
                $sql="Insert into `File Log` (`idLog`,`filename`,`sid`,`uid`,`date`,`message`,`errorStatus`) values 
                ('0', '$new_filename' , '$sid', '$username','$datetime','File uploaded', '0')";
                $dblink->query($sql) or
                    die("Something went wrong with $sql<br>".$dblink->error);

                unlink("/var/www/html/receive/$file");
            
            }
            echo "\n> Query Files Time: $execution_time\r\n";
            echo "File transfer(s) complete. Terminating session.\n";
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
            echo "\nClosed.\n";
            $datetime=date("Y-m-d H:i:s");
            $sql="Insert into `Session Log` (`idLog`,`sid`,`uid`,`date`,`message`,`query`,`errorStatus`) values 
                ('0', '$sid', '$username','$datetime','$key file(s) received and uploaded. Session closed', '/api/close_session', '0')";
            $dblink->query($sql) or
                    die("Something went wrong with $sql<br>".$dblink->error);
        }
        else
        {
            $datetime=date("Y-m-d H:i:s");
            $sql="Insert into `Session Log` (`idLog`,`sid`,`uid`,`date`,`message`,`query`,`errorStatus`) values 
                ('0', '$sid', '$username','$datetime','Error closing session $cinfo[0]', '/api/close_session', '1')";
            $dblink->query($sql) or
                    die("Something went wrong with $sql<br>".$dblink->error);
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
        $datetime=date("Y-m-d H:i:s");
            $sql="Insert into `Session Log` (`idLog`,`sid`,`uid`,`date`,`message`,`query`,`errorStatus`) values 
                ('0', '$sid', '$username','$datetime','Error querying files $cinfo[0]', '/api/close_session', '1')";
            $dblink->query($sql) or
                    die("Something went wrong with $sql<br>".$dblink->error);

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
    $datetime=date("Y-m-d H:i:s");
    $sql="Insert into `Session Log` (`idLog`,`sid`,`uid`,`date`,`message`,`query`,`errorStatus`) values 
        ('0', 'None', '$username','$datetime','Error creating session $cinfo[0]', '/api/close_session', '1')";
    $dblink->query($sql) or
        die("Something went wrong with $sql<br>".$dblink->error);

    echo $cinfo[0];
    echo "\r\n";
    echo $cinfo[1];
    echo "\r\n";
    echo $cinfo[2];
    echo "\r\n";

}
?>