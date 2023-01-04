<?php
    $page="reporting.php";
    include("functions.php");
    $dblink=db_connect("Document_Manager");
    $sql="SELECT * FROM `File` WHERE `nameUser`='wmi593'";
    $result=$dblink->query($sql) or
        die("Something went wrong with $sql<br>".$dblink->error);

    $loans=array();
    while ($data=$result->fetch_array(MYSQLI_ASSOC))
    {
        $tmp=explode("-",$data['fileName']);
        $loans[]=$tmp[4];
        
    }
    $loan_unique=array_unique($loans);
    foreach ($loan_unique as $val)
    {
        
        $sql="SELECT COUNT(`fileName`) FROM `File` WHERE `fileName` LIKE '%$val%'";
        $count=$dblink->query($sql) or
            die("Something went wrong with $sql<br>".$dblink->error);
        $temp=$count->fetch_array(MYSQLI_NUM);
        echo '<div>Loan Number: '.$val.' has '.$temp[0].' number of documents.</div>';
    }
    

?>
