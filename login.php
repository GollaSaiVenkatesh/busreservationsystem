<?php
define('DBSERVER', 'localhost');
define('DBUSERNAME', 'root');
define('DBPASSWORD', '');
define('DBNAME', 'bus');
$db=mysqli_connect(DBSERVER,DBUSERNAME,DBPASSWORD,DBNAME);
if($db===false){
    die("Error: connection error.".mysql_connect_error());
}
require_once "session.php";
$error='';
if($_SERVER["REQUEST_METHOD"]=="POST" && isset($_POST['login'])){
    $username =trim($_POST['username']);
    $password =trim($_POST['password']);
            //validate if email is empty
            if(empty($username)){
               $error='<p class="error">Please enter username</p>';
            }
            //validate if password is empty
            if(empty($password)){
                $error='<p class="error">Please enter the password</p>';
            }
            if(empty($error)){
                
                if($query = $db->prepare("SELECT password FROM customer WHERE username =? ")){
                   $query->bind_param('s',$username);
                   $query->execute();
                   $query->bind_result($temp);
                   $row = $query->fetch();
                   if($row){
                       echo $temp;
                       echo $password;
                       if(password_verify($password,$temp)){
                           $_session["username"]='username';
                           header("location:mainpage.html");
                           exit;
                       }else{
                           $error='<p class="error">The password is not vaild</p>';
                           echo "error";
                       }
                   }
                    else{
                    $error='<p class="error">No user exists with the given details</p>';
                }
            }
$query->close();
    }
mysqli_close($db);
}