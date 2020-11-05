<?php
define('DBSERVER', 'localhost');
define('DBUSERNAME', 'root');
define('DBPASSWORD', '');
define('DBNAME', 'bus');
$db=mysqli_connect(DBSERVER,DBUSERNAME,DBPASSWORD,DBNAME);
if($db===false){
    die("Error: connection error.".mysql_connect_error());
}
if(isset($_POST['addBus'])){
    echo "sucess2";
    $bus_type =trim($_POST['bus_type']);
    $bus_no =trim($_POST['bus_no']);
    $no_of_seats =trim($_POST['no_of_seats']);
    $driver_id =trim($_POST['driver_id']);
    $source_city =trim($_POST['source_city']);
    $dst_city =trim($_POST['dst_city']);
    if($query = $db->prepare("SELECT * FROM bus where bus_no = ?")){
        $error = '';
        $query->bind_param('s', $bus_no);
        $query->execute();
        $query->store_result();
        if($query->num_rows>0){
            $error='<p class="error">A bus with same bus_id already registered</p>';
        }else{
        
           //adding data of bus into bus table
                if(empty($error)){
                    $insertQuery = $db->prepare("INSERT INTO bus VALUES(?,?,?,?,?,?)");
                    $insertQuery->bind_param("sss",$bus_type,$bus_no,$no_of_seats,$driver_id,$source_city,$dst_city);
                    $result = $insertQuery->execute();
                    if($result){
                        $error='<p class="error">Bus is registered successful</p>';
                    }else{
                        $error='<p class="error">Something went worng</p>';
                    }
                }
            
        }
    }
else{
    echo "error";
}
$query->close();
$insertQuery->close();
mysqli_close($db);
}