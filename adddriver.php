<?php
define('DBSERVER', 'localhost');
define('DBUSERNAME', 'root');
define('DBPASSWORD', '');
define('DBNAME', 'bus');
$db=mysqli_connect(DBSERVER,DBUSERNAME,DBPASSWORD,DBNAME);
if($db===false){
    die("Error: connection error.".mysql_connect_error());
}
if(isset($_POST['adddriver'])){
    echo "sucess2";
    $driver_id =trim($_POST['driver_id']);
    $driver_username =trim($_POST['driver_username']);
    $password =trim($_POST['password']);
    $confirm_password =trim($_post['confirm_password'])
    $password_hash= password_hash($password,PASSWORD_BCRYPT);
    if($query = $db->prepare("SELECT * FROM driver where driver_id = ?")){
        $error = '';
        $query->bind_param('s', $driver_id);
        $query->execute();
        $query->store_result();
        if($query->num_rows>0){
            $error='<p class="error">A driver with same driver_id already registered</p>';
        }else{
        
            //validate confrim password
            if(empty($confirm_password)){
                $error='<p class="error">Please Enter the confrim password</p>';
            }else{
                if(empty($error)&&($password != $confirm_password)){
                    $error='<p class="error">Password did not match!</p>';
                }
           //adding data of driver into driver table
                if(empty($error)){
                    $insertQuery = $db->prepare("INSERT INTO driver VALUES(?,?,?)");
                    $insertQuery->bind_param("sss",$driver_id,$driver_username,$password_hash);
                    $result = $insertQuery->execute();
                    if($result){
                        $error='<p class="error">Driver is registered successful</p>';
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