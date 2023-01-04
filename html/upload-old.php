<?php
if (!isset($_POST['submit']))
{
    echo '<form method="post" action="">';
    echo '<p>User ID: <input type="text" name="idUser"></p>';
    echo '<p>Email: <input type="text" name="userEmail"></p>';
    // echo '<p>Password: <input type="text" name="userPassword"></p>';
    echo '<button type="submit" name="submit" value="submit">Submit</button>';
    echo '</form>';
}
elseif (isset($_POST['submit']) && $_POST['submit']=="submit")
{
    $userid=$_POST['idUser'];
    $email=$_POST['userEmail'];
    // $password=$_GET['userPassword'];
    // echo '<h4>Results from previous page:</h4>';
    echo '<p>User ID: '.$userid.'</p>';
    echo '<p>Email: '.$email.'</p>';
    // echo '<p>Password: '.$password.'</p>';
}
else
    echo '<p>Error in flow</p>';
?>