<?php
/* Attempt MySQL server connection. Assuming you are running MySQL
server with default setting (user 'root' with no password) */
$link = mysqli_connect("localhost","root","","penerbit_absensi");
 
// Check connection
if($link === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}
 
// Attempt select query execution
$sql = "SELECT * FROM t_karyawan";
if($result = mysqli_query($link, $sql)){
    
        while($row = mysqli_fetch_array($result)){
            $insert = "INSERT INTO t_kehadiran (id_karyawan,tanggal,active,id_alasan,computer_name,created_date,created_user,updated_date,updated_user) values ('".$row['id_karyawan']."','".date("Y-m-d")."','1','4','','".date("Y-m-d")."','".$row['id_karyawan']."','".date("Y-m-d")."','".$row['id_karyawan']."')";
            // echo $insert;
            mysqli_query($link, $insert);
        }
} else{
    echo "ERROR: Could not able to execute $sql. " . mysqli_error($link);
}
 
// Close connection
// mysqli_close($link);
?>