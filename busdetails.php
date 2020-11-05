<?php
    define('DBSERVER', 'localhost');
    define('DBUSERNAME', 'root');
    define('DBPASSWORD', '');
    define('DBNAME', 'bus');
    $db=mysqli_connect(DBSERVER,DBUSERNAME,DBPASSWORD,DBNAME);
    if($db===false){
        die("Error: connection error.".mysql_connect_error());
    }
    if(isset($_POST['searchBtn'])){
        
        
        $source=trim($_POST['source']);
        $destination=trim($_POST['destination']);
        $date=trim($_POST['date']);

        $sql = "create table t1(stop_name char(15) NOT NULL)";
        if (mysqli_query($db, $sql)) {
           
            $sqlinsert1 = $db->prepare("INSERT INTO t1 VALUES(?)");
            $sqlinsert1->bind_param('s', $source);
            $result1 = $sqlinsert1->execute();
            $sqlinsert2 = $db->prepare("INSERT INTO t1 VALUES(?)");
            $sqlinsert2->bind_param('s', $destination);
            $result2 = $sqlinsert2->execute();
            if($result1 & $result2){
              
                $bus1 = "create table bus1 as SELECT DISTINCT s1.bus_no AS bus_no FROM stops s1 WHERE NOT EXISTS(SELECT t1.stop_name FROM t1 WHERE t1.stop_name NOT IN (SELECT s2.stop_name FROM stops s2 WHERE s2.bus_no = s1.bus_no))";
                if (mysqli_query($db, $bus1)) {
                    $alter1="ALTER TABLE bus1 ADD COLUMN src_id TINYINT UNSIGNED NOT NULL DEFAULT 0";
                    $alter2="ALTER TABLE bus1 ADD COLUMN dst_id TINYINT UNSIGNED NOT NULL DEFAULT 0";
                    if(mysqli_query($db, $alter1) & mysqli_query($db, $alter2)){
                        $sqlinsert3 = $db->prepare("UPDATE bus1 inner join stops on bus1.bus_no = stops.bus_no set bus1.src_id=stops.stop_id where stop_name=?");
                        $sqlinsert3->bind_param('s', $source);
                        $result4 = $sqlinsert3->execute();
                        $sqlinsert4 = $db->prepare("UPDATE bus1 inner join stops on bus1.bus_no = stops.bus_no set bus1.dst_id=stops.stop_id where stop_name=?");
                        $sqlinsert4->bind_param('s', $destination);
                        $result5 = $sqlinsert4->execute();

                       
                        $selectQuery = "CREATE TABLE REM AS SELECT * from bus1 where src_id < dst_id";
                        $result6 = mysqli_query($db,$selectQuery);

                        $viewQuery="CREATE VIEW jo as SELECT REM.bus_no,REM.src_id,REM.dst_id,stops.stop_name 'src_name', stops.stop_time 'src_time' FROM REM LEFT JOIN stops ON REM.bus_no=stops.bus_no AND REM.src_id = stops.stop_id;";
                        $result7 = mysqli_query($db,$viewQuery);

                        $final="SELECT jo.bus_no,jo.src_name,jo.src_time,stops.stop_name 'dst_name', stops.stop_time 'dst_time' FROM jo LEFT JOIN stops ON jo.bus_no=stops.bus_no AND jo.dst_id = stops.stop_id";
                        $result = mysqli_query($db,$final);
                        
                        

                        $fareview="CREATE VIEW fareview as SELECT jo.bus_no,jo.src_name 'src_city',jo.src_time,stops.stop_name 'dst_city', stops.stop_time 'dst_time' FROM jo LEFT JOIN stops ON jo.bus_no=stops.bus_no AND jo.dst_id = stops.stop_id";
                        mysqli_query($db,$fareview);
                        $faretable1="CREATE TABLE t3 as SELECT * FROM fareview";
                        mysqli_query($db,$faretable1);
                        $fareview1="CREATE view src1 as SELECT t3.bus_no,t3.src_city,t3.dst_city,stops.stop_id 'src_id' FROM t3 INNER JOIN stops ON stops.bus_no =t3.bus_no and t3.src_city=stops.stop_name;";
                        mysqli_query($db,$fareview1);
                        $fareview2="CREATE view dst1 as SELECT t3.bus_no,t3.src_city,t3.dst_city,stops.stop_id 'dst_id' FROM t3 INNER JOIN stops ON stops.bus_no =t3.bus_no and t3.dst_city=stops.stop_name;";
                        mysqli_query($db,$fareview2);
                        $farealter1="ALTER TABLE t3 ADD COLUMN src_id TINYINT UNSIGNED NOT NULL DEFAULT 0;";
                        mysqli_query($db,$farealter1);
                        $fareupdate1="UPDATE t3 INNER JOIN src1 ON t3.bus_no=src1.bus_no SET t3.src_id = src1.src_id;";
                        mysqli_query($db,$fareupdate1);
                        $farealter2="ALTER TABLE t3 ADD COLUMN dst_id TINYINT UNSIGNED NOT NULL DEFAULT 0;";
                        mysqli_query($db,$farealter2);
                        $fareupdate2="UPDATE t3 INNER JOIN dst1 ON t3.bus_no=dst1.bus_no SET t3.dst_id = dst1.dst_id;";
                        mysqli_query($db,$fareupdate2);
                        $faretable2="CREATE table fare1 as select t3.*,stop_amnt from stops inner join t3 where t3.bus_no =stops.bus_no and(stops.stop_id>src_id and stops.stop_id<=dst_id);";
                        mysqli_query($db,$faretable2);
                        $farecalc="SELECT bus_no,src_city,src_time,dst_city,dst_time,sum(stop_amnt) 'stop_amnt' from fare1 group by bus_no;";
                        $fareresult=mysqli_query($db,$farecalc);
                        

                        $drop1="DROP TABLE REM";
                        $drop2="DROP TABLE t1";
                        $drop3="DROP VIEW jo";
                        $drop4="DROP TABLE bus1";

                        

                        $drop5="DROP TABLE t3";
                        $drop6="DROP VIEW src1";
                        $drop7="DROP VIEW dst1";
                        $drop8="DROP TABLE fare1";
                        $drop9="DROP VIEW fareview";

                        mysqli_query($db,$drop1);
                        mysqli_query($db,$drop2);
                        mysqli_query($db,$drop3);
                        mysqli_query($db,$drop4);

                        mysqli_query($db,$drop5);
                        mysqli_query($db,$drop6);
                        mysqli_query($db,$drop7);
                        mysqli_query($db,$drop8);
                        mysqli_query($db,$drop9);

                        if(mysqli_num_rows($result) > 0){

                        }else{
                            $msg = "No Buses found";
                        }
                    }
                    else {
                        echo "ERROR";
                    }
                }
                else{
                    echo "ERROR";
                }
                

            }else{
                echo "ERROR";
            }

        } 
        else {
            echo "Error creating table";
        }
            
    }
    mysqli_close($db);
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <title>Bootstrap Example</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body>

<div class="container">
  
  <table class="table">
    <thead class="thead-dark">
      <tr>
        <th>Bus No</th>
        <th>Source</th>
        <th>Time</th>
        <th>Destination</th>
        <th>Time</th>
        <th>Fare</th>
      </tr>
    </thead>
    <tbody>
        <?php
            while($row = mysqli_fetch_assoc($fareresult)){?>
                <tr>
                    <td><?php echo $row['bus_no']; ?></td>
                    <td><?php echo $row['src_city']; ?></td>
                    <td><?php echo $row['src_time']; ?></td>
                    <td><?php echo $row['dst_city']; ?></td>
                    <td><?php echo $row['dst_time']; ?></td>
                    <td><?php echo $row['stop_amnt']; ?></td>
                </tr>
            <?php }
        ?>
    </tbody>
  </table>
</div>

</body>
</html>
