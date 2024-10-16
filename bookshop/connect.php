<?php

$connection=mysqli_connect('localhost','root','','bookshop');

if (!$connection) {
    echo"connection faild".mysqli_connect_error();
}



?>