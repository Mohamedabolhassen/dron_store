<?php

$host = "localhost" ;
$name = "root" ;
$password = "" ;
$db = "dropuhut_ecommerce" ;


 $con = mysqli_connect($host,$name,$password,$db) ;

if($con){
    echo "Connectd" ;
}else{
    echo "Not Connected" ;
}


?>