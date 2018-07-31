<?php session_start();
include_once('includes/config.inc.php');
  $con= new mysqli(DB_HOSTNAME,DB_USERNAME,DB_PASSWORD,DB_DATABASE)or die("Could not connect to mysql".mysqli_error($con));
  $createdby=$_SESSION['createdby'];
  if(isset($_POST['dev_name'])){
$dev_name=$_POST['dev_name'];
//echo $dev_name;
$t_id=$_POST['id'];
$assign_date=$_POST['assign_date'];
$count_t=array();
$count_query=" SELECT t_id FROM  `task_manager` WHERE created_by = 'HPCS1234' AND t_status='Assigned' AND assign_employee='$dev_name'";
$count_result=mysqli_query($con,$count_query);
while ($countrow = mysqli_fetch_assoc($count_result)){
  $count_t[]=$countrow;

}
$t_priority=count($count_t);
$t_priority=$t_priority+1;
$query="UPDATE task_manager SET assign_employee='$dev_name',t_status='Assigned',A_priority='$t_priority',assign_date='$assign_date' WHERE t_id='$t_id' ";

$q=mysqli_query($con,$query) or die(mysqli_error($con));

$sql2 = "SELECT name FROM  `employees` WHERE  `EmployeeID` IN (SELECT  `assign_employee` FROM  `task_manager` WHERE  `t_id` ='$t_id')";
              $res = mysqli_query($con, $sql2);
              if(mysqli_num_rows($res)>0){
              	 $row = mysqli_fetch_assoc($res);
              }
              else{
              	$row['name']=" ";
              }

 $arrayName = array('t_name'=>$row['name'],'t_priority'=>$t_priority);
 echo json_encode($arrayName);


  }