<?php
    $page="report_3.php";
    include("functions.php");
    $dblink=db_connect("Document_Manager");
    $sql="SELECT * FROM `File` WHERE `nameUser`='wmi593'";
    $result=$dblink->query($sql) or
        die("Something went wrong with $sql<br>".$dblink->error);

    $loans=array();
    $fileids=array();
    while ($data=$result->fetch_array(MYSQLI_ASSOC))
    {
        $fileids[]=$data['idFile'];
        $tmp=explode("-",$data['fileName']);
        $loans[]=$tmp[4];
        
    }
    $total_files=sizeof($fileids);
    echo '<div>Total number of documents received: '.$total_files.'</div>';

    $loan_unique=array_unique($loans);
    $counts=array();
    foreach ($loan_unique as $val)
    {
        
        $sql="SELECT COUNT(`fileName`) FROM `File` WHERE `fileName` LIKE '%$val%'";
        $count=$dblink->query($sql) or
            die("Something went wrong with $sql<br>".$dblink->error);
        $temp=$count->fetch_array(MYSQLI_NUM);
        $counts[]=$temp[0];
        // echo '<div>Loan Number: '.$val.' has '.$temp[0].' number of documents.</div>';
    }
    // $size_count=sizeof($counts);
    $count_avg=array_sum($counts)/count($counts);
    echo '<div>Average number of documents for each loan: '.$count_avg.'</div>';
    foreach ($loan_unique as $val2)
    {
        
        $sql="SELECT COUNT(`fileName`) FROM `File` WHERE `fileName` LIKE '%$val2%'";
        $count=$dblink->query($sql) or
            die("Something went wrong with $sql<br>".$dblink->error);
        $temp=$count->fetch_array(MYSQLI_NUM);
        if (intval($count_avg) < intval($temp[0]))
        {
            echo '<div>Loan Number: '.$val.' has an above average number of documents: '.intval($temp[0]).'</div>';
        }
        else if(intval($count_avg) > intval($temp[0]))
        {
        echo '<div>Loan Number: '.$val.' has a below average number of documents: '.intval($temp[0]).'</div>';
        }
        else{
            echo '<div>Loan Number: '.$val.' has an about average number of documents: '.intval($temp[0]).'</div>';
        }
    }

?>