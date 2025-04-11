<?php
// configuring database
$conn = mysqli_connect("localhost","root","","database");
if(!($conn)){
    echo "Connection not established";
}

?>