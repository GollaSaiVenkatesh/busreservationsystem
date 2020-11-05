<?php
define('DBSERVER', 'localhost');
define('DBUSERNAME', 'root');
define('DBPASSWORD', '');
define('DBNAME', 'bus');
$db=mysqli_connect(DBSERVER,DBUSERNAME,DBPASSWORD,DBNAME);
if($db===false){
    die("Error: connection error.".mysql_connect_error());
}
if(isset($_POST['AddStop'])){
    echo "sucess2";
    $stop_id =trim($_POST['stop_id']);
    $stop_name =trim($_POST['stop_name']);
    $bus_no =trim($_POST['bus_no']);
    $arrival_time =trim($_POST['arrival_time']);
    $fare =trim($_POST['fare']);
    if($query = $db->prepare("SELECT * FROM stops where bus_no = ? and stop_no = ?")){
        $error = '';
        $query->bind_param('ss', $bus_no, $stop_id);
        $query->execute();
        $query->store_result();
        if($query->num_rows>0){
            $error='<p class="error">A bus with same bus_id already registered</p>';
        }else{
        
           //adding data of bus into bus table
                if(empty($error)){
                    $insertQuery = $db->prepare("INSERT INTO stops VALUES(?,?,?,?,?,?)");
                    $insertQuery->bind_param("sss",$bus_no,$stop_id,$stop_name,$fare,$arrival_time);
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