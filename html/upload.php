<?php
echo '<div id="page-inner">';
if (isset($_REQUEST['msg']) && ($_REQUEST['msg']=="success"))
{
	echo '<div class="alert alert-success alert-dismissable">';
	echo '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
	echo '&nbspDocument successfully uploaded!</div>';
}
echo '<h2 class="page-head-line">Document Manager</h2>';
echo '<div class="panel-body">';
echo '<form method="post" enctype="multipart/form-data" action="">';
echo '<input type="hidden" name="uploadedby" value="user@test.mail">';
echo '<input type="hidden" name="MAX_FILE_SIZE" value="10000000">';
echo '<div class="form-group">';
echo '<label class="control-label col-lg-4">&nbspFile Upload</label>';
echo '<div class="">';
echo '<div class="fileupload fileupload-new" data-provides="fileupload">';
echo '<div class="fileupload-preview thumbnail" style="width: 200px; height: 150px;"></div>';
echo '<div class="row">';//buttons
echo '<div class="col-md-2">';
echo '<span class="btn btn-file btn-primary">';
echo '<span class="fileupload-new"></span>';
echo '<span class="fileupload-exists"></span>';
echo '<input name="userfile" type="file"></span></div>';
echo '<div class="col-md-2"><a href="#" class="btn btn-danger fileupload-exists" data-dismiss="fileupload">&nbspRemove</a></div>';
echo '</div>';//end buttons
echo '</div>';//end fileupload fileupload-new
echo '</div>';//end ""
echo '</div>';//end form-group
echo '<hr>';
echo '<button type="submit" name="submit" value="submit" class="btn btn-lg btn-block btn-success">&nbspUpload File</button>';
echo '</form>';
echo '</div>';//end panel-body
echo '</div>';//end page-inner
include("functions.php");
if (isset($_POST['submit']))
{
	$dblink=db_connect("Document_Manager");

	$uploadDate=date("Y-m-d H:i:s");
	$uploadDName=date("Y-m-d_H:i:s");
	$uploadBy="0";
	$fileName=str_replace(" ","_",$_FILES['userfile']['name']);
	$fileName=$uploadDName.$fileName;
	$tmpName=$_FILES['userfile']['tmp_name'];
	$fileSize=$_FILES['userfile']['size'];
	$fileType=$_FILES['userfile']['type'];
    $path="/var/www/html/uploads/";
	$fp=fopen($tmpName, 'r');
	$content=fread($fp, filesize($tmpName));
	fclose($fp);
	$contentsClean=addslashes($content);
	$sql="Insert into `File` (`idUserLastViewed`,`fileTag`,`fileName`,`filePath`,`idUserUpload`,`dateLastViewed`,`dateFileUpload`,`fileStatus`,`fileType`,`fileContent`) values 
    (0,'test_file_tag','$fileName','$path','$uploadBy','$uploadDate','$uploadDate','active','$fileType','$contentsClean')";
	$dblink->query($sql) or
		die("Something went wrong with $sql<br>".$dblink->error);

	$fp=fopen($path.$fileName,"wb") or
		die("Could not open $path$fileName for writing");
	fwrite($fp,$content);
	fclose($fp);

	$dns="ec2-3-129-14-207.us-east-2.compute.amazonaws.com";
	header("Location: $dns/upload.php?msg=success");
}
?>