<?php session_start();
include_once('includes/config.inc.php');
  $con= new mysqli(DB_HOSTNAME,DB_USERNAME,DB_PASSWORD,DB_DATABASE)or die("Could not connect to mysql".mysqli_error($con));
  $createdby=$_SESSION['createdby'];
  if(isset($_POST['project'])){
  	$count_t=array();
  	$project=trim($_POST['project']);
  	$project=mysqli_real_escape_string($con,$project);
if(empty($_POST['dev_name'])){
$dev_name="";
$t_status="Created";
$assign_date="0000-00-00";
}
else{
	
$dev_name=$_POST['dev_name'];
$assign_date=$_POST['assign_date'];
$t_status="Assigned";
$count_query=" SELECT t_id FROM  `task_manager` WHERE created_by = 'HPCS1234' AND t_status='Assigned' AND assign_employee='$dev_name'";
$count_result=mysqli_query($con,$count_query);
while ($countrow = mysqli_fetch_assoc($count_result)){
  $count_t[]=$countrow;

}
//echo count($count_t);
}
if (!$con) {
    echo "Error: " . mysqli_connect_error();
 exit();
}
$d=date("d");
$h=date("h");
$i=date("i");
$s=date("s");
//$count=uniqid();
$t_priority=count($count_t);
$t_priority=$t_priority+1;
$tid="TSK_".$d.$h.$i.$s;
$sql1 = "INSERT INTO task_manager (t_id, t_name,t_status,A_priority ,assign_employee,created_by,assign_date) VALUES ('$tid','$project','$t_status','$t_priority','$dev_name','$createdby','$assign_date') ";
  $q=mysqli_query($con,$sql1);
 /*if ($con->query($sql))
 echo $sql;
*/
 $sql2 = "SELECT name FROM  `employees` WHERE  `EmployeeID` IN (SELECT  `assign_employee` FROM  `task_manager` WHERE  `t_id` ='$tid')";
              $res = mysqli_query($con, $sql2);
              if(mysqli_num_rows($res)>0){
              	 $row = mysqli_fetch_assoc($res);
              }
              else{
              	$row['name']=" ";
              }

 $arrayName = array('t_id' =>$tid ,'t_status'=>$t_status,'t_name'=>$row['name'],'t_priority'=>$t_priority);
 echo json_encode($arrayName);


}
 