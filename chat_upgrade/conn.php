<?php 
$conn = mysqli_connect("localhost", "root", "", "chatDB");
if(!$conn){
    echo "Connection error: ". mysqli_connect_error();
    
}
?>