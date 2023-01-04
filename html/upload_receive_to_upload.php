
<?php
include("functions.php");
$dblink=db_connect("Document_Manager");

chdir('receive');
exec('ls -l', $output, $retval);
echo "Returned with status $retval and output:\n";

// for each file in receive folder 
foreach ($output as $key => $file) 
{
    if ($key > 0){

        $tmp=preg_split('/\s+/', $file);
        $timestr="2022-$tmp[5]-$tmp[6] $tmp[7]";
        $time=strtotime($timestr);
        // $time=strtotime($timestr);
        $datetime=date('Y-m-d_H:i',$time);
        $filename=$tmp[8];
        $path="/var/www/html/uploads/";
        $type=filetype($path.$filename);
        $tmp=explode("-",$filename);
        $loanNum=$tmp[0];
        $tag=$tmp[1];
        // $loan=$tmp[0];
        // $type=$tmp[1];
        $fp=fopen($filename, 'r') or
            die("Could not open $fileName for reading");
        $content=fread($fp, filesize($filename));
        fclose($fp);
        $uploadDate=date("Y-m-d H:i:s");
        $username="wmi593";
        $new_filename="$datetime--$filename";
        $content_clean=addslashes($content);
        // echo $content;
        
        $sql="Insert into `File` (`idFile`,`nameUser`,`fileTag`,`fileName`,`filePath`,`fileType`,`fileContent`,`dateReceived`,`dateFileUpload`,`dateLastViewed`,`fileStatus`) values 
        ('0','$username','$tag','$new_filename','$path','$type','$content_clean','$datetime','$uploadDate','$uploadDate','active')";
        $dblink->query($sql) or
            die("Something went wrong with $sql<br>".$dblink->error);

        $fp=fopen($path.$new_filename,"wb") or
            die("Could not open $path$new_filename for writing");
        fwrite($fp,$content);
        fclose($fp);
        
        unlink("/var/www/html/receive/$filename");

        $datetime=date("Y-m-d H:i:s");
        $sql="Insert into `File Log` (`idLog`,`filename`,`sid`,`uid`,`date`,`message`,`errorStatus`) values 
        ('0', '$filename' , 'None', '$username','$datetime','File uploaded directly from /upload/', '0')";
        $dblink->query($sql) or
            die("Something went wrong with $sql<br>".$dblink->error);
        // echo "$tmp[0]\n";

    }
}
?>
