<?php
    $page="report_4.php";
    include("functions.php");
    $dblink=db_connect("Document_Manager");
    $sql="SELECT * FROM `File` WHERE `nameUser`='wmi593'";
    $result=$dblink->query($sql) or
        die("Something went wrong with $sql<br>".$dblink->error);

    $loans=array();
    $credit_total=0;
    $closing_total=0;
    $title_total=0;
    $financial_total=0;
    $personal_total=0;
    $internal_total=0;
    $legal_total=0;
    $other_total=0;
    while ($data=$result->fetch_array(MYSQLI_ASSOC))
    {
        $tmp=explode("-",$data['fileName']);
        $loans[]=$tmp[4];
        
    }
    $loan_unique=array_unique($loans);
    $loans_complete=array();
    $loans_incomplete=array();
    
    foreach ($loan_unique as $val)
    {
        $loan_report="documents missing: ";
        $credit=0;
        $closing=0;
        $title=0;
        $financial=0;
        $personal=0;
        $internal=0;
        $legal=0;
        $other=0;
        $sql="SELECT * FROM `File` WHERE `fileName` LIKE '%$val%'";
        $result=$dblink->query($sql) or
            die("Something went wrong with $sql<br>".$dblink->error);
        while($temp=$result->fetch_array(MYSQLI_ASSOC))
        {
            $fname=$temp['fileName'];
            if (str_contains($fname,"Credit"))
            {
                $credit=$credit+1;
                $credit_total=$credit_total+1;
            } 
            if (str_contains($fname,"Closing"))
            {
                $closing=$closing+1;
                $closing_total=$closing_total+1;

            } 
            if (str_contains($fname,"Title"))
            {
                $title=$title+1;
                $title_total=$title_total+1;
            } 
            if (str_contains($fname,"Financial"))
            {
                $financial=$financial+1;
                $financial_total=$financial_total+1;
            } 
            if (str_contains($fname,"Personal"))
            {
                $personal=$personal+1;
                $personal_total=$personal_total+1;

            } 
            if (str_contains($fname,"Internal"))
            {
                $internal=$internal+1;
                $internal_total=$internal_total+1;

            } 
            if (str_contains($fname,"Legal"))
            {
                $legal=$legal+1;
                $legal_total=$legal_total+1;

            } 
            if (str_contains($fname,"Other"))
            {
                $other=$other+1;
                $other_total=$other_total+1;

            } 
            // echo '<div>Loan Number: '.$val.' filename: '.$temp['fileName'].' </div>';
        }
        if($credit==0)
            $loan_report .= " Credit";
        if($closing==0)
            $loan_report .= " Closing";
        if($title==0)
            $loan_report .= " Title";
        if($financial==0)
            $loan_report .= " Financial";
        if($personal==0)
            $loan_report .= " Personal";
        if($internal==0)
            $loan_report .= " Internal";
        if($legal==0)
            $loan_report .= " Legal";
        if($other==0)
            $loan_report .= " Other";
        if($credit==1 and $closing==1 and $title==1 and $financial==1 and $personal==1 and $internal==1 and $legal==1 and $other==1)
        {
            $loan_report .= " None";
            $loans_complete["$val"]=$loan_report;
        }
        if ($credit!=1 or $closing!=1 or $title!=1 or $financial!=1 or $personal!=1 or $internal!=1 or $legal!=1 or $other!=1)
        {
            $loans_incomplete["$val"]=$loan_report;
        }

        // echo '<div>Loan Number: '.$val.' has '.$loan_report.'.</div>';
    }

    echo '<div>Total number of Credit documents: '.$credit_total.'</div>';
    echo '<div>Total number of Closing documents: '.$closing_total.'</div>';
    echo '<div>Total number of Title documents: '.$title_total.'</div>';
    echo '<div>Total number of Financial documents: '.$financial_total.'</div>';
    echo '<div>Total number of Personal documents: '.$personal_total.'</div>';
    echo '<div>Total number of Internal documents: '.$internal_total.'</div>';
    echo '<div>Total number of Legal documents: '.$legal_total.'</div>';
    echo '<div>Total number of Other documents: '.$other_total.'</div>';

    echo '<div>Completed loan documents:  </div>';
    foreach ($loans_complete as $key=>$missing)
        echo '<div>Completed Loan Number: '.$key.' has '.$missing.'.</div>';
        
    echo '<div>Incomplete loan documents:  </div>';
    foreach ($loans_incomplete as $key=>$missing)
        echo '<div>Incomplete Loan Number: '.$key.' has '.$missing.'.</div>';
    


?>