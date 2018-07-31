<?php session_start();
$_SESSION['user']='HPCS1111';
include_once('includes/config.inc.php');
$con= new mysqli(DB_HOSTNAME,DB_USERNAME,DB_PASSWORD,DB_DATABASE)or die("Could not connect to mysql".mysqli_error($con));
$query=" SELECT * FROM  `task_manager` WHERE assign_employee = 'HPCS1111' ORDER BY A_priority ";
$query2=" SELECT * FROM  `task_manager` WHERE assign_employee = 'HPCS1111' ORDER BY w_priority ";
$pro=array();
$pro2=array();
$result = mysqli_query($con, $query) or die(mysqli_error($con));
$result2 = mysqli_query($con, $query2) or die(mysqli_error($con));
while ($row = mysqli_fetch_assoc($result)){
  $pro[]=$row;

}
while ($row = mysqli_fetch_assoc($result2)){
  $pro2[]=$row;

}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Task Manager</title>
	<link rel="stylesheet" href="css/bootstrap.min.css">
  <link rel="stylesheet" href="css/jqueryui.css" />
  <link href="css/glyphicons.css" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="css/style.css">
  <link rel="shortcut icon" href="img/logoicon.ico" >

  <!--<script src="assets/jquery-2.1.3.js"></script>-->

  <script src="js/jquery.js"></script>
  <script type="text/javascript" src="js/jqueryui.js"></script>
  <script src="js/bootstrap.min.js" ></script>
  <script type="text/javascript">
  	
var date_diff_indays = function(date1, date2) {
                dt1 = new Date(date1);
                dt2 = new Date(date2);
                return Math.floor((Date.UTC(dt2.getFullYear(), dt2.getMonth(), dt2.getDate()) - Date.UTC(dt1.getFullYear(), dt1.getMonth(), dt1.getDate()) ) /(1000 * 60 * 60 * 24));
              }
function stop(event,u){
          event.stopPropagation();
          $("#myModal").children().children().children().siblings(".modal-body").html("<strong>"+$(u).attr('data-content')+"</strong>");
     $("#myModal").modal();
    }
  	function toggle(z){

  		var u=$(z);
  		u.siblings(".slide").slideToggle("slow");

  	}
  	$(window).on('load',function(){

    $("#sort1,#sort2,#sort3,#sort4,#sort5").css({'background': '#eee','padding': '5px'});
    $("#sort1").sortable({
        connectWith: ".tosortable",
        helper: "clone",
        dropOnEmpty: true,
        receive: function(ev, ui) {
    
          if(ui.item.attr("status")=="Delivered"||ui.item.attr("status")=="WIP"||ui.item.attr("status")=="Success"||ui.item.attr("status")=="Hold"){
            ui.sender.sortable("cancel");

          

            
          }
        }, stop: function (event, ui) {
        var data = $(this).sortable('serialize');
         $.ajax({
            data: data,
            type: 'POST',
            url: 'update_priority.php',
            success:function(datastring){
             
          console.log(data);    
      var temp = data.split("&");
      var i=1;
      for(s in temp){
       
       $("#TSK_"+temp[s].split("TSK[]=")[1]).children().children(".priority").text(i);
       i++;
      }

            }
        });

        },
       update: function (event, ui) {
        var data = $(this).sortable('serialize');
         $.ajax({
            data: data,
            type: 'POST',
            url: 'update_priority.php',
            success:function(datastring){
              
           
      var temp = data.split("&");
      var i=1;
      for(s in temp){
       
       $("#TSK_"+temp[s].split("TSK[]=")[1]).children().children(".priority").text(i);
       i++;
      }

            }
        });

        }
        });
        
        $("#sort4").sortable({
              connectWith: ".tosortable",
              helper: "clone",
              dropOnEmpty: true,
              receive: function(ev, ui) {
    
          if($(ev.target).attr("eid")!=ui.item.attr("eid")||ui.item.attr("status")=="Hold"||ui.item.attr("status")=="Success")
          {
            
                  ui.sender.sortable("cancel");
                }
                else{
                  var itemid = $(ev.originalEvent.toElement).parent().attr("itemid");
    var status =$(ev.originalEvent.toElement).parent().attr("status");
    var eid=$(ev.originalEvent.toElement).parent().attr("eid");
     
      $(".box-item").each(function() {
        if ($(this).attr("itemid") === itemid&&(status=="WIP"||status=="Assigned")) {
         
          if(status=="Assigned"){
            $(this).children("div:first").removeClass("btn-warning");
            $(this).children("div:first").addClass("btn-info");
            $(this).children("div:last").removeClass("alert-warning");
            $(this).children("div:last").addClass("alert-info");
           
          }
          else if(status=="WIP"){
            $(this).children("div:first").removeClass("btn-WIP");
            $(this).children("div:first").addClass("btn-info");
            $(this).children("div:last").removeClass("alert-WIP");
            $(this).children("div:last").addClass("alert-info");
            
          }
          $(this).children("div:first").children("span").remove();
          $(this).attr("status","Delivered");
          
          var changed_status="Delivered";
          $.post("status_update.php",{
            "id" : itemid,
            "status" : changed_status,
          },function(result){
                   
           
                    $("#messg").html('<div class="alert alert-success"><strong>Task Delivered Successfully!</strong></div>').fadeIn('slow');
                   setTimeout(function(){
                     $("#messg").html('<div class="alert alert-success"><strong>Task Delivered Successfully!</strong></div>').fadeOut('slow');
                    },1000);
                   

                 });

        }
      });
                }
                
              }
              });
              


              $("#sort3").sortable({
            connectWith: ".tosortable",
            helper: "clone",
            dropOnEmpty: true,
            receive: function(ev, ui) {
    console.log($(ev.target).attr("eid"));
              if($(ev.target).attr("eid")!=ui.item.attr("eid")||ui.item.attr("status")=="Delivered"||ui.item.attr("status")=="Success")
              {
                ui.sender.sortable("cancel");
              }
              else{
                var itemid = $(ev.originalEvent.toElement).parent().attr("itemid");
    var status =$(ev.originalEvent.toElement).parent().attr("status");
    var eid=$(ev.originalEvent.toElement).parent().attr("eid");
    
    $(".box-item").each(function() {

        
        if ($(this).attr("itemid") === itemid&&(status=="WIP"||status=="Assigned")) {
          
          if(status=="Assigned"){
            $(this).children("div:first").removeClass("btn-warning");
            $(this).children("div:first").addClass("btn-hold");
            $(this).children("div:last").removeClass("alert-warning");
            $(this).children("div:last").addClass("alert-hold");
            
          }
          else if(status=="WIP"){
            $(this).children("div:first").removeClass("btn-WIP");
            $(this).children("div:first").addClass("btn-hold");
            $(this).children("div:last").removeClass("alert-WIP");
            $(this).children("div:last").addClass("alert-hold");
           
          }
          $(this).children("div:first").children("span").remove();
          $(this).attr("status","Hold");
          
          var changed_status="Hold";
          $.post("status_update.php",{
            "id" : itemid,
            "status" : changed_status,
          },function(result){
                   
            
                    $("#messg").html('<div class="alert alert-success"><strong>Task on Hold!</strong></div>').fadeIn('slow');
                   setTimeout(function(){
                     $("#messg").html('<div class="alert alert-success"><strong>Task on Hold!</strong></div>').fadeOut('slow');
                    },1000);
                   

                 });

        }

      });
              }
            }
       

            });






             $("#sort2").sortable({
          connectWith: ".tosortable",
          helper: "clone",
          dropOnEmpty: true,
          receive: function(ev, ui) {
            console.log("receive");
            
            if($(ev.target).attr("eid")!=ui.item.attr("eid")||ui.item.attr("status")=="Success"){
              ui.sender.sortable("cancel");
              }
            else{
              
    var itemid = $(ev.originalEvent.toElement).parent().attr("itemid");
    var status =$(ev.originalEvent.toElement).parent().attr("status");
    var eid=$(ev.originalEvent.toElement).parent().attr("eid");
     
      $(".box-item").each(function() {
        if ($(this).attr("itemid") === itemid&&(status=="Assigned"||status=="Delivered"||status=="Hold")) {
          
          if(status=="Assigned"){
            $(this).children("div:first").removeClass("btn-warning");
            $(this).children("div:first").addClass("btn-WIP");
            $(this).children("div:last").removeClass("alert-warning");
            $(this).children("div:last").addClass("alert-WIP");
            
          }
          else if(status=="Delivered"){
            $(this).children("div:first").removeClass("btn-info");
            $(this).children("div:first").addClass("btn-WIP");
            $(this).children("div:last").removeClass("alert-info");
            $(this).children("div:last").addClass("alert-WIP");
            $(this).children("div:first").css("position","relative");
            $(this).children("div:first").append("<span class=\'priority\'></span>");
           
          }
          else if(status=="Hold"){
            $(this).children("div:first").removeClass("btn-hold");
            $(this).children("div:first").addClass("btn-WIP");
            $(this).children("div:last").removeClass("alert-hold");
            $(this).children("div:last").addClass("alert-WIP");
             $(this).children("div:first").css("position","relative");
            $(this).children("div:first").append("<span class=\'priority\'></span>");
            
          }
          $(this).attr("status","WIP");
          
         
          var changed_status="WIP";
          $.post("status_update.php",{
            "id" : itemid,
            "status" : changed_status,
          },function(result){
                    
                   
                    $("#messg").html('<div class="alert alert-success"><strong>Task in Progress!</strong></div>').fadeIn('slow');
                   setTimeout(function(){
                     $("#messg").html('<div class="alert alert-success"><strong>Task in Progress!</strong></div>').fadeOut('slow');
                    },1000);
                   

                 });

        }
      });

            }

          },
          update: function (event, ui) {
        var data = $(this).sortable('serialize');
         $.ajax({
            data: data,
            type: 'POST',
            url: 'update_priority_wip.php',
            success:function(datastring){
             
             
      var temp = data.split("&");
      var i=1;
      for(s in temp){
       
       $("#TSK_"+temp[s].split("TSK[]=")[1]).children().children(".priority").text(i);
       i++;
      }
    }
    });     
     
         },
  stop: function (event, ui) {
        var data = $(this).sortable('serialize');
         $.ajax({
            data: data,
            type: 'POST',
            url: 'update_priority_wip.php',
            success:function(datastring){
              
          console.log(data);    
      var temp = data.split("&");
      var i=1;
      for(s in temp){
       
       $("#TSK_"+temp[s].split("TSK[]=")[1]).children().children(".priority").text(i);
       i++;
      }
    }
    });     
     
         }


          });
          

  	});




  </script>
</head>
<body>
<div class="container-fluid">
<div class="row">
	<center><h3>My Tasks</h3></center>
</div>
<div class="row">&nbsp;</div>
<div class="row" id="reload">

	<div class="col-md-1 mycol"></div>
	<div class="col-md-2 mycol">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h1 class="panel-title">Assigned</h1>
      </div>
      <div id="container2" class="panel-body box-container">
      	<div id="sort1" class="tosortable">
        <?php
        
   //var_dump($pro);
        
        foreach ($pro as $row) {
          if($row['t_status']=='Assigned'){
            if(strlen($row['t_name'])>18){
                 $temp_name=substr($row['t_name'], 0,18);
                  echo '<div  class="box-item" style="margin-top:8px;"  itemid="'.$row['t_id'].'" id="'.$row['t_id'].'" status="'.$row['t_status'].'" ><div  style="width:100%;position:relative;"onclick="toggle(this)" class="btn btn-warning"  >'.$temp_name.'<span   onclick="stop(event,this)" class="viewproject" data-content="'.$row['t_name'].'"> ...</span><span class="priority">'.$row['A_priority'].'</span></div><div class="alert alert-warning slide" >';
                }
              else{
                echo '<div  class="box-item"  style="margin-top:8px;"  itemid="'.$row['t_id'].'" id="'.$row['t_id'].'" status="'.$row['t_status'].'" ><div  style="width:100%;position:relative;"onclick="toggle(this)" class="btn btn-warning"  >'.$row['t_name'].'<span class="priority">'.$row['A_priority'].'</span></div><div class="alert alert-warning slide" >';
              }
             
              $id=$row['t_id'];
              $assign_date=date_create($row['assign_date']);
              if(date_format($assign_date,"Y-m-d")!='-0001-11-30')
              {
               
               $currentdate = date_create(date("Y-m-d"));
               $diff=date_diff($currentdate,$assign_date);
               //echo $diff->format("%a days");
             echo "<p>Assigned Date: ".date_format($assign_date,"d-m-Y")."</p>";
             echo "<p>Days Since First Assigned: ".$diff->format("%a days")."</p>";
             };

             echo '</div>
             </div>';
          }

        }
        ?>
    </div>
      </div>
    </div>
  </div>

  <div class="col-md-2 mycol">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h1 class="panel-title">Work in Progress</h1>
      </div>
      <div id="container3" class="panel-body box-container">
      		<div id="sort2" class="tosortable">
        <?php
        
   //var_dump($pro);
        foreach ($pro2 as  $row) {
    # code...
          if($row['t_status']=='WIP'){
             if(strlen($row['t_name'])>18){
                  $temp_name=substr($row['t_name'], 0,18);
                    echo '<div  class="box-item"  id="'.$row['t_id'].'" style="margin-top:8px;" itemid="'.$row['t_id'].'" status="'.$row['t_status'].'"><div  style="width:100%;position:relative;" onclick="toggle(this)" class="btn btn-WIP ">'.$temp_name.'<span  onclick="stop(event,this)"  class="viewproject" data-content="'.$row['t_name'].'"> ...</span><span class="priority" >'.$row['w_priority'].'</span></div><div class="alert alert-WIP slide">';
                 }
                 else{
                     echo '<div  class="box-item"  id="'.$row['t_id'].'" style="margin-top:8px;" itemid="'.$row['t_id'].'" status="'.$row['t_status'].'"><div  style="width:100%;position:relative;" onclick="toggle(this)" class="btn btn-WIP ">'.$row['t_name'].'<span class="priority" >'.$row['w_priority'].'</span></div><div class="alert alert-WIP slide">';
                 }
             
              $id=$row['t_id'];
              $assign_date=date_create($row['assign_date']);
              if(date_format($assign_date,"Y-m-d")!='-0001-11-30')
              {
              
               $currentdate = date_create(date("Y-m-d"));
               $diff=date_diff($currentdate,$assign_date);
               //echo $diff->format("%a days");
             echo "<p>Assigned Date: ".date_format($assign_date,"d-m-Y")."</p>";
             echo "<p>Days Since First Assigned: ".$diff->format("%a days")."</p>";
             };

              echo '</div></div>';
          }

        }
        ?>
    </div>
      </div>
    </div>
  </div>
  <div class="col-md-2 mycol">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h1 class="panel-title">Hold</h1>
      </div>
      <div id="hold" class="panel-body box-container">
      		<div id="sort3" class="tosortable">
        <?php
       
   //var_dump($pro);
        foreach ($pro as  $row) {
    # code...
          if($row['t_status']=='Hold'){
            if(strlen($row['t_name'])>18){
                  $temp_name=substr($row['t_name'], 0,18);
                  echo '<div class="box-item"  id="'.$row['t_id'].'" style="margin-top:8px;" itemid="'.$row['t_id'].'" status="'.$row['t_status'].'"><div  style="width:100%;" onclick="toggle(this)" class="btn btn-hold ">'.$temp_name.'<span onclick="stop(event,this)" class="viewproject" data-content="'.$row['t_name'].'"> ...</span></div><div class="alert alert-hold slide">';
                }
                else{
                  echo '<div class="box-item"  id="'.$row['t_id'].'" style="margin-top:8px;" itemid="'.$row['t_id'].'" status="'.$row['t_status'].'"><div  style="width:100%;" onclick="toggle(this)" class="btn btn-hold ">'.$row['t_name'].'</div><div class="alert alert-hold slide">';
                }
                
                $id=$row['t_id'];
                $assign_date=date_create($row['assign_date']);
              if(date_format($assign_date,"Y-m-d")!='-0001-11-30')
              {
              
               $currentdate = date_create(date("Y-m-d"));
               $diff=date_diff($currentdate,$assign_date);
               //echo $diff->format("%a days");
             echo "<p>Assigned Date: ".date_format($assign_date,"d-m-Y")."</p>";
             echo "<p>Days Since First Assigned: ".$diff->format("%a days")."</p>";
             };

                echo '</div></div>';
          }

        }
        ?>
    </div>
      </div>
    </div>
  </div>
<div class="col-md-2 mycol">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h1 class="panel-title">Delivered</h1>
      </div>
      <div id="container4" class="panel-body box-container">
      		<div id="sort4" class="tosortable">
        <?php
        
   //var_dump($pro);
        foreach ($pro as  $row) {
    # code...
          if($row['t_status']=='Delivered'){
           if(strlen($row['t_name'])>18){
                  $temp_name=substr($row['t_name'], 0,18);
                  echo '<div class="box-item" style="margin-top:8px;" id="'.$row['t_id'].'" itemid="'.$row['t_id'].'" status="'.$row['t_status'].'"><div  style="width:100%;" onclick="toggle(this)" class="btn btn-info ">'.$temp_name.'<span   onclick="stop(event,this)" class="viewproject" data-content="'.$row['t_name'].'"> ...</span></div><div class="alert alert-info slide">';
                }
                else{
                  echo '<div class="box-item"  style="margin-top:8px;" id="'.$row['t_id'].'" itemid="'.$row['t_id'].'" status="'.$row['t_status'].'"><div  style="width:100%;" onclick="toggle(this)" class="btn btn-info ">'.$row['t_name'].'</div><div class="alert alert-info slide">';
                }
                  
                  $id=$row['t_id'];
                  $assign_date=date_create($row['assign_date']);
              if(date_format($assign_date,"Y-m-d")!='-0001-11-30')
              {
               
               $currentdate = date_create(date("Y-m-d"));
               $diff=date_diff($currentdate,$assign_date);
               //echo $diff->format("%a days");
             echo "<p>Assigned Date: ".date_format($assign_date,"d-m-Y")."</p>";
             echo "<p>Days Since First Assigned: ".$diff->format("%a days")."</p>";
             };

                  echo '</div></div>';
          }

        }
        ?>
    </div>
      </div>
    </div>
  </div>

<div class="col-md-2 mycol">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h1 class="panel-title">Compeleted</h1>
      </div>
      <div id="container5" class="panel-body box-container">
      		<div id="sort5" class="tosortable">
        <?php
       
   //var_dump($pro);
        foreach ($pro as  $row) {
    # code...
          if($row['t_status']=='Success'){
             if(strlen($row['t_name'])>18){
                  $temp_name=substr($row['t_name'], 0,18);
                   echo '<div class="box-item"  style="margin-top:8px;" itemid="'.$row['t_id'].'" id="'.$row['t_id'].'" status="'.$row['t_status'].'"><div  style="width:100%;" onclick="toggle(this)" class="btn btn-success">'.$temp_name.'<span onclick="stop(event,this)" class="viewproject" data-content="'.$row['t_name'].'"> ...</span></div>

                    <div class="alert alert-success slide">';
                }
                else{
                   echo '<div class="box-item"  style="margin-top:8px;" itemid="'.$row['t_id'].'" id="'.$row['t_id'].'" status="'.$row['t_status'].'"><div  style="width:100%;" onclick="toggle(this)" class="btn btn-success">'.$row['t_name'].'</div>

                    <div class="alert alert-success slide">';
                }
                   
              $assign_date=date_create($row['assign_date']);
              $completion_date=date_create($row['completion_date']);
              if(date_format($assign_date,"Y-m-d")!='-0001-11-30'&&date_format($completion_date,"Y-m-d")!='-0001-11-30')
              {
               $currentdate = date_create(date("Y-m-d"));
               $diff=date_diff($assign_date,$completion_date);
               //echo $diff->format("%a days");
             echo "<p>Assigned Date: ".date_format($assign_date,"d-m-Y")."</p>";
             echo "<p>Compeletion Date: ".date_format($completion_date,"d-m-Y")."</p>";
             echo "<p>Days to Compelete: ".$diff->format("%a days")."</p>";
             };

                    echo '</div></div>';
          }

        }
        ?>
    </div>
      </div>
    </div>
  </div>
<div class="col-md-1 mycol"></div>


</div>
 <div id="messg" style=" top: 1%;left:80%;">
            <div class="alert alert-success">
              <strong>Task in Progress!</strong> 
            </div>
    </div>

</div>
<div id="myModal" class="modal fade" role="dialog">
      <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            <div class="alert alert-success">
              <strong>Task created Successfully!</strong> 
            </div>
          </div>

        </div>
      </div>
    </div>
</body>
</html>