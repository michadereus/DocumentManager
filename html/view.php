<link href="assets/css/bootstrap.css" rel="stylesheet" />
<!-- JQUERY SCRIPTS -->
<script src="assets/js/jquery-1.12.4.js"></script>
<!-- BOOTSTRAP SCRIPTS -->
<script src="assets/js/bootstrap.js"></script>
<?php
include("functions.php");
$dblink=db_connect("Document_Manager");
// $autoid=$_REQUEST['fid'];
// $autoid='7';
echo '<div id="page-inner">';
echo '<h2 class="page-head-line">&nbspView</h2>';
echo '<div class="panel-body">';
$sql="Select * from `File`";
// where `auto_id`='$autoid'
$result=$dblink->query($sql) or
	die("Something went wrong with $sql<br>".$dblink->error);
while ($data=$result->fetch_array(MYSQLI_ASSOC))
{
	if ($data['filePath']!=NULL)
		echo '<p>File: <a href="uploads/'.$data['fileName'].'" target="_blank">'.$data['fileName'].'</a></p>';
	else
	{
		$content=$data['fileContent'];
		$fname=date("Y-m-d_H:i:s")."-userid-file.pdf";
		if (!($fp=fopen("/var/www/html/uploads/$fname","w")))
			echo "<p>File could not be loaded at this time</p>";
		else
		{
			fwrite($fp,$content);
			fclose($fp);
			echo '<p>File: <a href="uploads/'.$fname.'" target="_blank">'.$data['fileName'].'</a></p>';
		}
	}
}
echo '</div>';//end panel-body
echo '</div>';//end page-inner
?>