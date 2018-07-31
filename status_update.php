<?php session_start();
include_once('includes/config.inc.php');
  $con= new mysqli(DB_HOSTNAME,DB_USERNAME,DB_PASSWORD,DB_DATABASE)or die("Could not connect to mysql".mysqli_error($con));
  $createdby=$_SESSION['createdby'];
  if(isset($_POST['status'])){
//$status=$_POST['status'];
$t_id=$_POST['id'];
$status=$_POST['status'];
if($status=="Success"){
$currentdate = date_create(date("Y-m-d"));
$currentdate=date_format($currentdate,"Y-m-d");
$query="UPDATE task_manager SET t_status='$status',completion_date='$currentdate' WHERE t_id='$t_id' ";
$q=mysqli_query($con,$query);
$query2="SELECT * FROM task_manager WHERE t_id='$t_id' ";
$res=mysqli_query($con,$query2);
$row=mysqli_fetch_assoc($res);
$arrayName = array('assigndate' =>$row['assign_date'] , 'completion_date' =>$row['completion_date'] );
echo json_encode($arrayName);
}
else{
$query="UPDATE task_manager SET t_status='$status' WHERE t_id='$t_id' ";
$q=mysqli_query($con,$query);
}






  }