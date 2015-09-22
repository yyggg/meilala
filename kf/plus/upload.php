<?php
    $base64 = $_POST['formFile'];
    $IMG = base64_decode($base64);
    $path = '../uploadimg/';
    $fname=$path.time().'.jpg';
    file_put_contents($fname,$IMG);
    echo $fname;
?>