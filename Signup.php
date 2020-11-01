<?php
define('DBSERVER', 'localhost');
define('DBUSERNAME', 'root');
define('DBPASSWORD', '');
define('DBNAME', 'bus');
$db=mysqli_connect(DBSERVER,DBUSERNAME,DBPASSWORD,DBNAME);
if($db===false){
    die("Error: connection error.".mysql_connect_error());
}
if(isset($_POST['SignUp'])){
    echo "sucess2";
    $name =trim($_POST['name']);
    $username =trim($_POST['username']);
    $password =trim($_POST['password']);
    $confirm_password =trim($_POST['confirm_password']);
    $password_hash= password_hash($password,PASSWORD_BCRYPT);
    if($query = $db->prepare("SELECT * FROM customer where username = ?")){
        $error = '';
        $query->bind_param('s', $username);
        $query->execute();
        $query->store_result();
        if($query->num_rows>0){
            $error='<p class="error">The username is already used!</p>';
        }else{
        
            //validate password
            if(strlen($password ) <6){

               $error='<p class="error">Password should of 6 characters</p>';
            }
            //validate confrim password
            if(empty($confirm_password)){
                $error='<p class="error">Please Enter the confrim password</p>';
            }else{
                if(empty($error)&&($password != $confirm_password)){
                    $error='<p class="error">Password did not match!</p>';
                }
            }
            if(empty($error)){
                $insertQuery = $db->prepare("INSERT INTO customer VALUES(?,?,?)");
                $insertQuery->bind_param("sss",$username,$name,$password_hash);
                $result = $insertQuery->execute();
                if($result){
                    $error='<p class="error">Your Registration is successful</p>';
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