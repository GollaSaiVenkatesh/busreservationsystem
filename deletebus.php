<?php
define('DBSERVER', 'localhost');
define('DBUSERNAME', 'root');
define('DBPASSWORD', '');
define('DBNAME', 'bus');
$db=mysqli_connect(DBSERVER,DBUSERNAME,DBPASSWORD,DBNAME);
if($db===false){
    die("Error: connection error.".mysql_connect_error());
}
if(isset($_post['DeleteBus'])){
    $bus_no =trim($_post['bus_no']);
    if($query = $db->prepare("SELECT * from bus where bus_no = ?")){
        $error = '';
        $query->bind_param('s',$bus_no);
        $query->execute();
        $query->store_result();
        if($query->num_rows=0){
            $error='<p class="error">A bus with bus_no does not exist</p>';
        }else{
            //deleting data of bus into bus table
              if(empty($error)){
                  $deleteQuery = $db->prepare("DELETE INTO bus where bus_no=(?)");
                  $deleteQuery->bind_param("s",$bus_no);
                  $result = $deleteQuery->execute();
                  if($result){
                      $error='<p class="error">Bus is De-Registered successful</p>';
                    }else{
                        $error='<p class="error">Something went worng</p>';
                  }
              }
        }
    }
}