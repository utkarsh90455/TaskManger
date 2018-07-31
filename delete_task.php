<?php session_start();
include_once('includes/config.inc.php');
  $con= new mysqli(DB_HOSTNAME,DB_USERNAME,DB_PASSWORD,DB_DATABASE)or die("Could not connect to mysql".mysqli_error($con));
  
 if(isset($_POST['id'])){

$t_id=$_POST['id'];
if($_POST['status']=='Success'){
	$query="UPDATE task_manager SET t_status='Archieved' WHERE t_id='$t_id' ";
}
else{
	$query="UPDATE task_manager SET t_status='Cancelled' WHERE t_id='$t_id' ";
}
$q=mysqli_query($con,$query);
if($q){
	echo "Success";
}



}