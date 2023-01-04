<?php
    $page="report_2.php";
    include("functions.php");
    $dblink=db_connect("Document_Manager");
    $sql="SELECT * FROM `File` WHERE `nameUser`='wmi593'";
    $result=$dblink->query($sql) or
        die("Something went wrong with $sql<br>".$dblink->error);

    // $loans=array();
    chdir("uploads");
    $total_size=0;
    $size_array=array();
    while ($data=$result->fetch_array(MYSQLI_ASSOC))
    {
        // $tmp=str_replace("_"," ",$data['fileName']);
        $size=filesize($data['fileName']);
        $size_array[]=$size;
        $total_size=$total_size+$size;
        
    }
    $size_avg=array_sum($size_array)/count($size_array);
    echo '<div>Total size of all documents: '.$total_size.' bytes</div>';
    echo '<div>Avg size of all documents: '.$size_avg.' bytes</div>';
    
    // while ($data=$result->fetch_array(MYSQLI_ASSOC))
    // {
    //     // $tmp=str_replace("_"," ",$data['fileName']);
    //     $size=filesize($data['fileName']);
        
    //     $size_avg=$total_size/$size;
    //     echo '<div>Total size of all files: '.$total_size.' bytes</div>';
        
    // }
    

?>
