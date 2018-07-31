<?php session_start();
$_SESSION['createdby']='HPCS1234';
$user_manager=$_SESSION['createdby'];
include_once('includes/config.inc.php');
$con= new mysqli(DB_HOSTNAME,DB_USERNAME,DB_PASSWORD,DB_DATABASE)or die("Could not connect to mysql".mysqli_error($con));
$query=" SELECT name,EmployeeID,modeOfEmployment FROM  `employees` WHERE Employement_Status='1' ";
$query2=" SELECT * FROM  `task_manager` WHERE created_by = '$user_manager' AND t_status!='Cancelled' AND t_status!='Archieved' ORDER BY assign_employee ASC,A_priority ASC,time_stamp DESC ";
$query4=" SELECT * FROM  `task_manager` WHERE created_by = '$user_manager' AND t_status!='Cancelled' AND t_status!='Archieved' ORDER BY assign_employee ASC,w_priority ASC,time_stamp DESC ";
$query3=" SELECT assign_employee FROM  `task_manager` WHERE created_by = '$user_manager'AND t_status!='Cancelled' AND t_status!='Archieved'  GROUP BY assign_employee ORDER BY time_stamp DESC";
$result = mysqli_query($con, $query);
$records=array();
$pro=array();
$pro2=array();
$count=array();
while ($row = mysqli_fetch_assoc($result)){
  $records[]=$row;
}
//$records = mysqli_fetch_all ($result, MYSQLI_ASSOC);
$result2 = mysqli_query($con, $query2) or die(mysqli_error($con));
while ($row = mysqli_fetch_assoc($result2)){
  $pro[]=$row;

}
$result3 = mysqli_query($con, $query3) or die(mysqli_error($con));
while ($row = mysqli_fetch_assoc($result3)){
  $count[]=$row;

}
$result4 = mysqli_query($con, $query4) or die(mysqli_error($con));
while ($row = mysqli_fetch_assoc($result4)){
  $pro2[]=$row;

}
//echo count($count);
//var_dump($count);
//$pro = mysqli_fetch_all ($result2, MYSQLI_ASSOC);
//var_dump($pro);

?>
<!DOCTYPE html>
<html>
<head>
	<title>Task Manager</title>
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <link rel="stylesheet" href="css/jqueryui.css" />
  <link href="css/glyphicons.css" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="css/style.css">
  
  <!--<script src="assets/jquery-2.1.3.js"></script>-->
  <link rel="shortcut icon" href="img/logoicon.ico" >

  <script src="js/jquery.js"></script>
  <script type="text/javascript" src="js/jqueryui.js"></script>
  
  <script src="js/bootstrap.min.js" ></script>
  <script type="text/javascript">
     var date_diff_indays = function(date1, date2) {
                dt1 = new Date(date1);
                dt2 = new Date(date2);
                return Math.floor((Date.UTC(dt2.getFullYear(), dt2.getMonth(), dt2.getDate()) - Date.UTC(dt1.getFullYear(), dt1.getMonth(), dt1.getDate()) ) /(1000 * 60 * 60 * 24));
              }

    function toggle(z){
      //alert("sj"+$(z).parent().text());
      var u=$(z);
      u.siblings(".slide").slideToggle("slow");

    }

    function stop(event,u){
          event.stopPropagation();
          $("#myModal").children().children().children().siblings(".modal-body").html("<p style=''>"+$(u).attr('data-content')+"</p>");
     $("#myModal").modal();
    }
    
    function toggle2(z){
      //alert("sj"+$(z).parent().text());
      var u=$(z);
      u.siblings(".tog").slideToggle("slow");

    }
    function del2(event,u){
      event.stopPropagation();
      var id =$(u).parent().parent().attr('id');
      var status=$(u).parent().parent().attr('status');
      html='<input type="hidden" value="'+id+'" status="'+status+'">';
      $("#del").children().children().children().siblings(".modal-body").append(html);
      $("#del").children().children().children().siblings(".modal-body").children().html("<strong>Are you sure want to archive the task ?</strong>");
       $("#del").modal({backdrop: 'static', keyboard: false}); 



    }
    function del(u){
      var id =$(u).parent().attr('id');
      var status=$(u).parent().attr('status');
      html='<input type="hidden" value="'+id+'" status="'+status+'">';
      $("#del").children().children().children().siblings(".modal-body").append(html);
      $("#del").children().children().children().siblings(".modal-body").children().html("<strong>Are you sure want to cancel the task ?</strong>");
      $("#del").modal({backdrop: 'static', keyboard: false}); 



    }
  function showtext(u){
    $(u).siblings("#showtext").fadeToggle('slow');
  }

    function delfinal(u){
     //alert($(u).parent().siblings(".modal-body").children().siblings("input").val());
     var id = $(u).parent().siblings(".modal-body").children().siblings("input").val();
     var status = $(u).parent().siblings(".modal-body").children().siblings("input").attr("status");
     $.post('delete_task.php',{
      'id' : id,
      'status':status
    },function(result){
                    //alert();
                    if(status=="Assigned"){
                       var array=[];
                       var ref=$("#"+id).parent();
                       $("#"+id).remove().fadeOut('slow');
                      ref.children().each(function(){
                        array.push($(this).attr('id')); 
                      });
                    
                    $.ajax({
                  data: {data:array},
                  type: 'POST',
                  url: 'update_priority_del.php',
                  success:function(datastring){
                    var value = $("#"+ref.attr("eid")).children(".btn-name").children(".countstatus").children("#assign").attr("set");
                    value=Number(value)-1;
                    $("#"+ref.attr("eid")).children(".btn-name").children(".countstatus").children("#assign").attr("set",value);
                     $("#"+ref.attr("eid")).children(".btn-name").children(".countstatus").children("#assign").text("A: "+value+" | ");
                       var i=1;
                    for(s in array){
                     
                     $("#"+array[s]).children().children(".priority").text(i);
                     i++;
                    }
                    $(u).parent().siblings(".modal-body").children().siblings("input").remove();
                    if(status=='Success'){
                      $("#myModal").children().children().children().siblings(".modal-body").html("<div class='alert alert-success'><strong>Task Archieved Successfully !</strong></div>");
                    }
                    else{
                      $("#myModal").children().children().children().siblings(".modal-body").html("<div class='alert alert-success'><strong>Task Cancelled Successfully !</strong></div>");
                    }
                    $("#myModal").modal();
                    }
                  });
                }
                else if(status=="WIP"){
                      var array=[];
                       var ref=$("#"+id).parent();
                       $("#"+id).remove().fadeOut('slow');
                      ref.children().each(function(){
                        array.push($(this).attr('id')); 
                      });
                    
                    $.ajax({
                  data: {data:array},
                  type: 'POST',
                  url: 'update_priority_wip_del.php',
                  success:function(datastring){
                    var value = $("#"+ref.attr("eid")).children(".btn-name").children(".countstatus").children("#wip").attr("set");
                    value=Number(value)-1;
                    $("#"+ref.attr("eid")).children(".btn-name").children(".countstatus").children("#wip").attr("set",value);
                     $("#"+ref.attr("eid")).children(".btn-name").children(".countstatus").children("#wip").text("W: "+value+" | ");
                       var i=1;
                    for(s in array){
                     
                     $("#"+array[s]).children().children(".priority").text(i);
                     i++;
                    }
                    $(u).parent().siblings(".modal-body").children().siblings("input").remove();
                    if(status=='Success'){
                      $("#myModal").children().children().children().siblings(".modal-body").html("<div class='alert alert-success'><strong>Task Archieved Successfully !</strong></div>");
                    }
                    else{
                      $("#myModal").children().children().children().siblings(".modal-body").html("<div class='alert alert-success'><strong>Task Cancelled Successfully !</strong></div>");
                    }
                    $("#myModal").modal();
                    }
                  });
                }
                else if(status=='Delivered'){
                    var ref=$("#"+id).parent();
                    $("#"+id).remove().fadeOut('slow');
                    var value = $("#"+ref.attr("eid")).children(".btn-name").children(".countstatus").children("#deli").attr("set");
                    value=Number(value)-1;
                    $("#"+ref.attr("eid")).children(".btn-name").children(".countstatus").children("#deli").attr("set",value);
                     $("#"+ref.attr("eid")).children(".btn-name").children(".countstatus").children("#deli").text("D: "+value);
                  }
                  else if(status=='Hold'){
                    var ref=$("#"+id).parent();
                    $("#"+id).remove().fadeOut('slow');
                    var value = $("#"+ref.attr("eid")).children(".btn-name").children(".countstatus").children("#hold").attr("set");
                    value=Number(value)-1;
                    $("#"+ref.attr("hold")).children(".btn-name").children(".countstatus").children("#hold").attr("set",value);
                     $("#"+ref.attr("eid")).children(".btn-name").children(".countstatus").children("#hold").text("H: "+value+" | ");

                  }
                else{
                  $("#"+id).remove().fadeOut('slow');
                  if($("#container1").children().length==0)
                       { 
                           console.log($("#container1").children().length);
                          $("#Created").css('display','none');
                          $("#main").attr('class','col-md-12 mycontainer');
                       }
                  
                }
                  $(u).parent().siblings(".modal-body").children().siblings("input").remove();
                  if(status=='Success'){
                      $("#myModal").children().children().children().siblings(".modal-body").html("<div class='alert alert-success'><strong>Task Archieved Successfully !</strong></div>");
                    }
                    else{
                      $("#myModal").children().children().children().siblings(".modal-body").html("<div class='alert alert-success'><strong>Task Cancelled Successfully !</strong></div>");
                    }
                    $("#myModal").modal();  
                    
                   //alert(""+$("#"+id).html());
                    
                 });

   }
   function cancel(u){
    $(u).parent().siblings(".modal-body").children().siblings("input").remove();
  }
  function mod(ev,u){
     ev.stopPropagation();
     //console.log($(u).attr('data-content'));
     $("#myModal1").children().children().children().siblings(".modal-body").children().html("<strong>"+$(u).attr('data-content')+"</strong>");
     $("#myModal1").modal();
  }
  function create(){
    var project=$("#project").val();
    var dev_name=$("#dev_name").val();

    if(project == null||project.trim()=="")  {
           // alert('khali'+project);
        //alert($("#myModal1").children().children().children().siblings(".modal-body").children().html("")+" ");
        $("#myModal1").modal();


      }else{
          //alert(''+project);
          var d = new Date();
          var assign_date=d.getFullYear()+"-"+(d.getMonth()+1)+"-"+d.getDate();
          var showdate = d.toLocaleString("en", { day: "numeric" }) + "-"+d.toLocaleString("en", { month: "short"  }) + "-" + d.toLocaleString("en", { year: "numeric"});
                
          $.post('create_task.php',{
            'project' : project,
            'dev_name' : dev_name,
            'assign_date':assign_date
          },function(result){
                    //alert();
                           //alert(""+$("td #dev_name").html()+result);
                           var data=JSON.parse(result);
                           $("#project").val("");
                           $("#dev_name").val("");
                           //$("#project").reset();
                           //$("#dev_name").reset();
                           if($("#container1").children().length==0)
                       { 
                           
                          $("#Created").css('display','block');
                          $("#main").attr('class','col-md-9 mycontainer');
                       }
                           if(data.t_status=="Created"){ 
                              $("#messg").html('<div class="alert alert-success"><strong>Task Created Successfully!</strong></div>').fadeIn('slow');setTimeout(function(){$("#messg").html('<div class="alert alert-success"><strong>Task Created Successfully!</strong></div>').fadeOut('slow'); },1000);
                            if(project.length>30){
                            var newname = project.substr(0,30);
                            var html='<div style="margin-top:8px;" id="'+data.t_id+'"><div itemid="'+data.t_id+'" status="'+data.t_status+'" onclick="toggle(this)" class="btn btn-primary box-item" >'+ newname+'<span  onclick="stop(event,this)" class="viewproject" data-content="'+project+'"> ...</span></div><span onclick="del(this)" <span style="vertical-align:middle;float:right;z-index:99;margin-top: -25px;margin-right: 5px;" class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span><input type="hidden" value="'+data.t_id+'"/><div class="slide" style="margin-top:3px;"><select  id="dev_name" class="form-control" style="width: 200px;font-size:11px;">'+$(" #dev_name").html()+'</select><button class="btn btn-success assign" onclick="assign(this)" type="button"  style="float:right;margin-top: -34px;"><span class="glyphicon glyphicon-send"></span></div></div>';
                          }
                          else{
                            var html='<div style="margin-top:8px;" id="'+data.t_id+'"><div itemid="'+data.t_id+'" status="'+data.t_status+'" onclick="toggle(this)" class="btn btn-primary box-item" >'+ project+'</div><span onclick="del(this)" <span style="vertical-align:middle;float:right;z-index:99;margin-top: -25px;margin-right: 5px;" class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span><input type="hidden" value="'+data.t_id+'"/><div class="slide" style="margin-top:3px;"><select  id="dev_name" class="form-control" style="width: 200px;font-size:11px;">'+$(" #dev_name").html()+'</select><button class="btn btn-success assign" onclick="assign(this)" type="button"  style="float:right;margin-top: -34px;"><span class="glyphicon glyphicon-send"></span></div></div>';
                          }
                             

                             $("#container1").prepend(html).fadeIn('slow');
                             
                    //assign();
                  }
                  else{
                    //console.log($("#"+dev_name).length);
                    if($("#"+dev_name).length>0){
                      if(project.length>18){
                            var newname = project.substr(0,18);
                            var html='<div class="box-item"style="margin-top:8px;" itemid="'+data.t_id+'" status="'+data.t_status+'" eid="'+dev_name+'" id="'+data.t_id+'"><div  onclick="toggle(this)" style="width:100%;position:relative;" class="btn btn-warning " style="text-align:left;overflow:hidden;white-space:nowrap;" >'+newname+'<span  onclick="stop(event,this)" class="viewproject" data-content="'+project+'"> ...</span><span class="priority">'+data.t_priority+'</span></div><span title="Click to Cancel Task" onclick="del(this)" style="vertical-align: middle;float:right;z-index:99;margin-top: -25px;margin-right: 5px;" class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span><div class="alert alert-warning slide"><p>Assigned Date: '+showdate+'</p><p>Days since First Assigned : 0 Days</p></div></div>';
                      }
                      else{
                         var html='<div class="box-item"style="margin-top:8px;" itemid="'+data.t_id+'" status="'+data.t_status+'" eid="'+dev_name+'" id="'+data.t_id+'"><div  onclick="toggle(this)" style="width:100%;position:relative;" class="btn btn-warning " style="text-align:left;overflow:hidden;white-space:nowrap;" >'+project+'<span class="priority">'+data.t_priority+'</span></div><span onclick="del(this)" style="vertical-align: middle;float:right;z-index:99;margin-top: -25px;margin-right: 5px;" title="Click to Cancel Task" class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span><div class="alert alert-warning slide"><p>Assigned Date: '+showdate+'</p><p>Days since First Assigned : 0 Days</p></div></div>';
                      }
                    
                    $("#sort1-"+dev_name).append(html).fadeIn('slow');
                    $("#messg").html('<div class="alert alert-success"><strong>Task Assigned Successfully!</strong></div>').fadeIn('slow');setTimeout(function(){$("#messg").html('<div class="alert alert-success"><strong>Task Assigned Successfully!</strong></div>').fadeOut('slow'); },1000);
                    $('.tog').slideUp('fast');
                    var value = $("#"+dev_name).children(".btn-name").children(".countstatus").children("#assign").attr("set");
                    value=Number(value)+1;
                    $("#"+dev_name).children(".btn-name").children(".countstatus").children("#assign").attr("set",value);
                     $("#"+dev_name).children(".btn-name").children(".countstatus").children("#assign").text("A: "+value+" | ");
                    if($("#"+dev_name).offset().top>$('#main').height())
                       {
                        //console.log($("#"+assignee).offset().top+" "+$("#"+assignee).children('.tog').outerHeight()+" "+$("#"+assignee).parent().offset().top)

                        $("#"+dev_name).children('.tog').slideDown('fast');
                        setTimeout(function(){
                            $("#main").animate({scrollTop:+$("#"+dev_name).offset().top+$("#"+dev_name).children('.tog').outerHeight()-$("#"+dev_name).parent().offset().top },10,'linear');
                                                   
                                                 },100);
                      }
                      else if($("#"+dev_name).offset().top<($('#main').height()/2)){
                        //console.log("dsl");
                        $("#main").animate({scrollTop:$("#"+dev_name).offset().top-$("#"+dev_name).parent().offset().top},10,'linear',function(){
                          $("#"+dev_name).children('.tog').slideDown('fast');
                        });
                       }
                      else{
                         $("#"+dev_name).children('.tog').slideDown('fast');
                        setTimeout(function(){
                            $("#main").animate({scrollTop:$("#"+dev_name).offset().top-$("#"+dev_name).parent().offset().top},10,'linear');
                                                   
                                                 },100);

                      }

                    }
                    else{
                      //write code here
///////////////////////////////////////////////////////////
          if(project.length>18){
                          var newname = project.substr(0,18);
                          var html2='<div class="box-item"style="margin-top:8px;" itemid="'+data.t_id+'" status="'+data.t_status+'" eid="'+dev_name+'" id="'+data.t_id+'"><div  onclick="toggle(this)" style="width:100%;position:relative;" class="btn btn-warning " style="text-align:left;overflow:hidden;white-space:nowrap;" >'+newname+'<span   onclick="stop(event,this)" class="viewproject" data-content="'+project+'"> ...</span><span class="priority">'+data.t_priority+'</span></div><span title="Click to Cancel Task" onclick="del(this)" style="vertical-align: middle;float:right;z-index:99;margin-top: -25px;margin-right: 5px;color:white;" class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span><div class="alert alert-warning slide"><p>Assigned Date: '+showdate+'</p><p>Days since First Assigned : 0</p></div></div>';
                      }
                      else{
                         var html2='<div class="box-item"style="margin-top:8px;" itemid="'+data.t_id+'" status="'+data.t_status+'" eid="'+dev_name+'" id="'+data.t_id+'"><div  onclick="toggle(this)" style="width:100%;position:relative;" class="btn btn-warning " style="text-align:left;overflow:hidden;white-space:nowrap;" >'+project+'<span class="priority">'+data.t_priority+'</span></div><span title="Click to Cancel Task" onclick="del(this)" style="vertical-align: middle;float:right;z-index:99;margin-top: -25px;margin-right: 5px;color:white;" class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span><div class="alert alert-warning slide"><p>Assigned Date: '+showdate+'</p><p>Days since First Assigned : 0</p></div></div>';
                      }



var html='<div id="'+dev_name+'"><div class="btn btn-primary btn-name" onclick="toggle2(this)" style="width:100%;">'+data.t_name+'<div class="countstatus"><span id="assign" set="1">A: 1 | </span><span id="wip" set="0" >W: 0 | </span><span id="hold" set="0">H: 0 | </span><span id="deli" set="0">D: 0 </span></div></div><div class="row alert alert-info tog"><div><div class=""> <div class="panel panel-default mypanel"> <div class="panel-heading"> <h1 class="panel-title">Assigned</h1> </div> <div id="container2'+dev_name+'" class=" panel-body box-container"> <div id="sort1-'+dev_name+'" eid="'+dev_name+'" class="connect_'+dev_name+'">'+html2+'</div> </div> </div> </div> </div><div class=""> <div class="panel panel-default mypanel"> <div class="panel-heading"> <h1 class="panel-title" style="overflow:hidden; white-space:nowrap;">Work in Progress</h1> </div> <div  class="container3 panel-body box-container"> <div id="sort2-'+dev_name+'" eid="'+dev_name+'" class="connect_'+dev_name+'"> </div> </div> </div></div> <div class=""> <div class="panel panel-default mypanel"> <div class="panel-heading"> <h1 class="panel-title">Hold</h1> </div> <div id="hold-'+dev_name+'"  class="hold panel-body box-container"> <div id="sort3-'+dev_name+'" eid="'+dev_name+'" class="connect_'+dev_name+'"> </div> </div> </div> </div> <div class=""> <div class="panel panel-default mypanel"> <div class="panel-heading"> <h1 class="panel-title">Delivered</h1> </div> <div id="container4-'+dev_name+'" class="container4 panel-body box-container"> <div id="sort4-'+dev_name+'" eid="'+dev_name+'" class="connect_'+dev_name+'"> </div> </div> </div> </div> <div class=""> <div class="panel panel-default mypanel"> <div class="panel-heading"> <h1 class="panel-title">Compeleted</h1> </div> <div id="container5-'+dev_name+'" class="container5 panel-body box-container"> <div id="sort5-'+dev_name+'" eid="'+dev_name+'" class="connect_'+dev_name+'"> </div> </div> </div> </div> </div><script type="text/javascript">var date_diff_indays = function(date1, date2) {dt1 = new Date(date1);dt2 = new Date(date2);return Math.floor((Date.UTC(dt2.getFullYear(), dt2.getMonth(), dt2.getDate()) - Date.UTC(dt1.getFullYear(), dt1.getMonth(), dt1.getDate()) ) /(1000 * 60 * 60 * 24));};$("#sort1-'+dev_name+',#sort2-'+dev_name+',#sort3-'+dev_name+',#sort4-'+dev_name+',#sort5-'+dev_name+'").css({\'background\': \'#eee\',\'padding\': \'5px\'}); $("#sort1-'+dev_name+'").sortable({connectWith: ".connect_'+dev_name+'", helper: "clone", dropOnEmpty: true, receive: function(ev, ui) {if($(ev.target).attr("eid")!=ui.item.attr("eid")||ui.item.attr("status")=="Delivered"||ui.item.attr("status")=="Success"||ui.item.attr("status")=="Hold"){ui.sender.sortable("cancel"); } else{var itemid = $(ev.originalEvent.toElement).parent().attr("itemid"); var status =$(ev.originalEvent.toElement).parent().attr("status"); var eid=$(ev.originalEvent.toElement).parent().attr("eid"); $(".box-item").each(function() {if ($(this).attr("itemid") === itemid&&status=="WIP") {var value = $("#"+eid).children(".btn-name").children(".countstatus").children("#wip").attr("set"); console.log(value); value=Number(value)-1; $("#"+eid).children(".btn-name").children(".countstatus").children("#wip").attr("set",value); $("#"+eid).children(".btn-name").children(".countstatus").children("#wip").text("W: "+value+" | "); $(this).children("div:first").removeClass("btn-WIP"); $(this).children("div:first").addClass("btn-warning"); $(this).children("div:last").removeClass("alert-WIP"); $(this).children("div:last").addClass("alert-warning"); $(this).attr("status","Assigned"); var changed_status="Assigned"; $.post("status_update.php",{"id" : itemid, "status" : changed_status, },function(result){var value = $("#"+eid).children(".btn-name").children(".countstatus").children("#assign").attr("set"); value=Number(value)+1; $("#"+eid).children(".btn-name").children(".countstatus").children("#assign").attr("set",value); $("#"+eid).children(".btn-name").children(".countstatus").children("#assign").text("A: "+value+" | "); $("#messg").html(\'<div class="alert alert-success"><strong>Task Assigned Successfully!</strong></div>\').fadeIn(\'slow\'); setTimeout(function(){$("#messg").html(\'<div class="alert alert-success"><strong>Task Assigned Successfully!</strong></div>\').fadeOut(\'slow\'); },1000); }); } }); } }, stop: function (event, ui) {var data = $(this).sortable(\'serialize\'); $.ajax({data: data, type: \'POST\', url: \'update_priority.php\', success:function(datastring){console.log(data); var temp = data.split("&"); var i=1; for(s in temp){$("#TSK_"+temp[s].split("TSK[]=")[1]).children().children(".priority").text(i); i++; } } }); }, update: function (event, ui) {var data = $(this).sortable(\'serialize\'); $.ajax({data: data, type: \'POST\', url: \'update_priority.php\', success:function(datastring){var temp = data.split("&"); var i=1; for(s in temp){$("#TSK_"+temp[s].split("TSK[]=")[1]).children().children(".priority").text(i); i++; } } }); } }); $("#sort4-'+dev_name+'").sortable({connectWith: ".connect_'+dev_name+'", helper: "clone", dropOnEmpty: true, receive: function(ev, ui) {if($(ev.target).attr("eid")!=ui.item.attr("eid")||ui.item.attr("status")=="Hold"||ui.item.attr("status")=="Success") {ui.sender.sortable("cancel"); } else{var itemid = $(ev.originalEvent.toElement).parent().attr("itemid"); var status =$(ev.originalEvent.toElement).parent().attr("status"); var eid=$(ev.originalEvent.toElement).parent().attr("eid"); $(".box-item").each(function() {if ($(this).attr("itemid") === itemid&&(status=="WIP"||status=="Assigned")) {if(status=="Assigned"){$(this).children("div:first").removeClass("btn-warning"); $(this).children("div:first").addClass("btn-info"); $(this).children("div:last").removeClass("alert-warning"); $(this).children("div:last").addClass("alert-info"); var value = $("#"+eid).children(".btn-name").children(".countstatus").children("#assign").attr("set"); value=Number(value)-1; $("#"+eid).children(".btn-name").children(".countstatus").children("#assign").attr("set",value); $("#"+eid).children(".btn-name").children(".countstatus").children("#assign").text("A: "+value+" | "); } else if(status=="WIP"){$(this).children("div:first").removeClass("btn-WIP"); $(this).children("div:first").addClass("btn-info"); $(this).children("div:last").removeClass("alert-WIP"); $(this).children("div:last").addClass("alert-info"); var value = $("#"+eid).children(".btn-name").children(".countstatus").children("#wip").attr("set"); value=Number(value)-1; $("#"+eid).children(".btn-name").children(".countstatus").children("#wip").attr("set",value); $("#"+eid).children(".btn-name").children(".countstatus").children("#wip").text("W: "+value+" | "); } $(this).children("div:first").children("span").remove(); $(this).attr("status","Delivered"); var changed_status="Delivered"; $.post("status_update.php",{"id" : itemid, "status" : changed_status, },function(result){var value = $("#"+eid).children(".btn-name").children(".countstatus").children("#deli").attr("set"); value=Number(value)+1; $("#"+eid).children(".btn-name").children(".countstatus").children("#deli").attr("set",value); $("#"+eid).children(".btn-name").children(".countstatus").children("#deli").text("D: "+value+" "); $("#messg").html(\'<div class="alert alert-success"><strong>Task Delivered Successfully!</strong></div>\').fadeIn(\'slow\'); setTimeout(function(){$("#messg").html(\'<div class="alert alert-success"><strong>Task Delivered Successfully!</strong></div>\').fadeOut(\'slow\'); },1000); }); } }); } } }); $("#sort3-'+dev_name+'").sortable({connectWith: ".connect_'+dev_name+'", helper: "clone", dropOnEmpty: true, receive: function(ev, ui) {console.log($(ev.target).attr("eid")); if($(ev.target).attr("eid")!=ui.item.attr("eid")||ui.item.attr("status")=="Delivered"||ui.item.attr("status")=="Success") {ui.sender.sortable("cancel"); } else{var itemid = $(ev.originalEvent.toElement).parent().attr("itemid"); var status =$(ev.originalEvent.toElement).parent().attr("status"); var eid=$(ev.originalEvent.toElement).parent().attr("eid"); $(".box-item").each(function() {if ($(this).attr("itemid") === itemid&&(status=="WIP"||status=="Assigned")) {if(status=="Assigned"){$(this).children("div:first").removeClass("btn-warning"); $(this).children("div:first").addClass("btn-hold"); $(this).children("div:last").removeClass("alert-warning"); $(this).children("div:last").addClass("alert-hold"); var value = $("#"+eid).children(".btn-name").children(".countstatus").children("#assign").attr("set"); value=Number(value)-1; $("#"+eid).children(".btn-name").children(".countstatus").children("#assign").attr("set",value); $("#"+eid).children(".btn-name").children(".countstatus").children("#assign").text("A: "+value+" | "); } else if(status=="WIP"){$(this).children("div:first").removeClass("btn-WIP"); $(this).children("div:first").addClass("btn-hold"); $(this).children("div:last").removeClass("alert-WIP"); $(this).children("div:last").addClass("alert-hold"); var value = $("#"+eid).children(".btn-name").children(".countstatus").children("#wip").attr("set"); value=Number(value)-1; $("#"+eid).children(".btn-name").children(".countstatus").children("#wip").attr("set",value); $("#"+eid).children(".btn-name").children(".countstatus").children("#wip").text("W: "+value+" | "); } $(this).children("div:first").children("span").remove(); $(this).attr("status","Hold"); var changed_status="Hold"; $.post("status_update.php",{"id" : itemid, "status" : changed_status, },function(result){var value = $("#"+eid).children(".btn-name").children(".countstatus").children("#hold").attr("set"); value=Number(value)+1; $("#"+eid).children(".btn-name").children(".countstatus").children("#hold").attr("set",value); $("#"+eid).children(".btn-name").children(".countstatus").children("#hold").text("H: "+value+" | "); $("#messg").html(\'<div class="alert alert-success"><strong>Task on Hold!</strong></div>\').fadeIn(\'slow\'); setTimeout(function(){$("#messg").html(\'<div class="alert alert-success"><strong>Task on Hold!</strong></div>\').fadeOut(\'slow\'); },1000); }); } }); } } }); $("#sort2-'+dev_name+'").sortable({connectWith: ".connect_'+dev_name+'", helper: "clone", dropOnEmpty: true, receive: function(ev, ui) {console.log("receive"); if($(ev.target).attr("eid")!=ui.item.attr("eid")||ui.item.attr("status")=="Success"){ui.sender.sortable("cancel"); } else{var itemid = $(ev.originalEvent.toElement).parent().attr("itemid"); var status =$(ev.originalEvent.toElement).parent().attr("status"); var eid=$(ev.originalEvent.toElement).parent().attr("eid"); $(".box-item").each(function() {if ($(this).attr("itemid") === itemid&&(status=="Assigned"||status=="Delivered"||status=="Hold")) {if(status=="Assigned"){$(this).children("div:first").removeClass("btn-warning"); $(this).children("div:first").addClass("btn-WIP"); $(this).children("div:last").removeClass("alert-warning"); $(this).children("div:last").addClass("alert-WIP"); var value = $("#"+eid).children(".btn-name").children(".countstatus").children("#assign").attr("set"); value=Number(value)-1; $("#"+eid).children(".btn-name").children(".countstatus").children("#assign").attr("set",value); $("#"+eid).children(".btn-name").children(".countstatus").children("#assign").text("A: "+value+" | "); } else if(status=="Delivered"){$(this).children("div:first").removeClass("btn-info"); $(this).children("div:first").addClass("btn-WIP"); $(this).children("div:last").removeClass("alert-info"); $(this).children("div:last").addClass("alert-WIP"); $(this).children("div:first").css("position","relative"); $(this).children("div:first").append("<span class=\'priority\'></span>"); var value = $("#"+eid).children(".btn-name").children(".countstatus").children("#deli").attr("set"); value=Number(value)-1; $("#"+eid).children(".btn-name").children(".countstatus").children("#deli").attr("set",value); $("#"+eid).children(".btn-name").children(".countstatus").children("#deli").text("D: "+value+" "); } else if(status=="Hold"){$(this).children("div:first").removeClass("btn-hold"); $(this).children("div:first").addClass("btn-WIP"); $(this).children("div:last").removeClass("alert-hold"); $(this).children("div:last").addClass("alert-WIP"); $(this).children("div:first").css("position","relative"); $(this).children("div:first").append("<span class=\'priority\'></span>"); var value = $("#"+eid).children(".btn-name").children(".countstatus").children("#hold").attr("set"); value=Number(value)-1; $("#"+eid).children(".btn-name").children(".countstatus").children("#hold").attr("set",value); $("#"+eid).children(".btn-name").children(".countstatus").children("#hold").text("H: "+value+" | "); } $(this).attr("status","WIP"); var changed_status="WIP"; $.post("status_update.php",{"id" : itemid, "status" : changed_status, },function(result){var value = $("#"+eid).children(".btn-name").children(".countstatus").children("#wip").attr("set"); value=Number(value)+1; $("#"+eid).children(".btn-name").children(".countstatus").children("#wip").attr("set",value); $("#"+eid).children(".btn-name").children(".countstatus").children("#wip").text("W: "+value+" | "); $("#messg").html(\'<div class="alert alert-success"><strong>Task in Progress!</strong></div>\').fadeIn(\'slow\'); setTimeout(function(){$("#messg").html(\'<div class="alert alert-success"><strong>Task in Progress!</strong></div>\').fadeOut(\'slow\'); },1000); }); } }); } }, update: function (event, ui) {var data = $(this).sortable(\'serialize\'); $.ajax({data: data, type: \'POST\', url: \'update_priority_wip.php\', success:function(datastring){var temp = data.split("&"); var i=1; for(s in temp){$("#TSK_"+temp[s].split("TSK[]=")[1]).children().children(".priority").text(i); i++; } } }); }, stop: function (event, ui) {var data = $(this).sortable(\'serialize\'); $.ajax({data: data, type: \'POST\', url: \'update_priority_wip.php\', success:function(datastring){console.log(data); var temp = data.split("&"); var i=1; for(s in temp){$("#TSK_"+temp[s].split("TSK[]=")[1]).children().children(".priority").text(i); i++; } } }); } }); $("#sort5-'+dev_name+'").sortable({connectWith: ".connect_'+dev_name+'", helper: "clone", dropOnEmpty: true, receive: function(ev, ui) {var u;if($(ev.target).attr("eid")!=ui.item.attr("eid")||ui.item.attr("status")!="Delivered"){ui.sender.sortable("cancel"); } else{var itemid = $(ev.originalEvent.toElement).parent().attr("itemid"); var status =$(ev.originalEvent.toElement).parent().attr("status"); var eid=$(ev.originalEvent.toElement).parent().attr("eid"); $(".box-item").each(function() {if ($(this).attr("itemid") === itemid&&status=="Delivered") {$(this).children("div:first").removeClass("btn-info"); $(this).children("div:first").addClass("btn-success"); $(this).children("span").remove();$(this).children("div:first").append(\'<i  title="Click to Archive Task" onclick="del2(event,this)"style="vertical-align:middle;margin-right:-8px;float:right;z-index:99;" class="material-icons">archive</i>\');$(this).children("div:last").removeClass("alert-info"); u=$(this);$(this).children("div:last").addClass("alert-success"); $(this).attr("status","Success"); var changed_status="Success"; $.post("status_update.php",{"id" : itemid, "status" : changed_status, },function(result){data=JSON.parse(result);'+
 'var objDate = new Date(data.assigndate);var objDate2 = new Date(data.completion_date);var strDate = objDate.toLocaleString("en", { day: "numeric" }) + "-"+objDate.toLocaleString("en", { month: "short"  }) + "-" + objDate.toLocaleString("en", { year: "numeric"});var strDate2 = objDate2.toLocaleString("en", { day: "numeric" }) + "-"+objDate2.toLocaleString("en", { month: "short"  }) + "-" + objDate2.toLocaleString("en", { year: "numeric"});u.children("div:last").html("<p>Assignned Date: "+strDate+"</p><p>Compeletion Date: "+strDate2+"</p><p>Days to Compelete: "+(Number(date_diff_indays(data.assigndate,data.completion_date))+1)+"</p>");var value = $("#"+eid).children(".btn-name").children(".countstatus").children("#deli").attr("set"); value=Number(value)-1; $("#"+eid).children(".btn-name").children(".countstatus").children("#deli").attr("set",value); $("#"+eid).children(".btn-name").children(".countstatus").children("#deli").text("D: "+value+" "); $("#messg").html(\'<div class="alert alert-success"><strong>Task Compeleted Successfully!</strong></div>\').fadeIn(\'slow\'); setTimeout(function(){$("#messg").html(\'<div class="alert alert-success"><strong>Task Compeleted Successfully!</strong></div>\').fadeOut(\'slow\'); },1000); }); } }); } } }); </scr'+'ipt></div></div>';
 
 $("#messg").html('<div class="alert alert-success"><strong>Task Assigned Successfully!</strong></div>').fadeIn('slow');
  setTimeout(function(){$("#messg").html('<div class="alert alert-success"><strong>Task Assigned Successfully!</strong></div>').fadeOut('slow'); },1000);
  $('.tog').slideUp('fast');
$("#main").prepend(html).fadeIn('slow',function(){
  var scroll=$("#"+dev_name).offset().top-$("#"+dev_name).parent().offset().top;
                           //console.log(scroll);
                          $("#main").animate({scrollTop:scroll},10,'linear',function(){
                                  $("#"+dev_name).children('.tog').slideDown('slow');
                                  //console.log("ho gya");
                             });
});


               /////////////////////////////////////////////////////////////////       
                    }
                    
                  }

                });

          

        }

  }
  function assign(y){
    var x= $(y);

    var id = x.parent().siblings('input').val();

    var assignee=x.siblings('select').val();
    var p_content= x.parent().siblings('div').text();
  //alert(p_content+"");
  if(assignee==null||assignee.trim()==""){

   //alert(" "+id);
   $("#myModal1").children().children().children().siblings(".modal-body").children().html("<strong>Select Assignee!</strong>");
   $("#myModal1").modal();
 }
 else{
  var d = new Date()
 var assign_date=d.getFullYear()+"-"+(d.getMonth()+1)+"-"+d.getDate();
 var showdate=d.toLocaleString("en", { day: "numeric" }) + "-"+d.toLocaleString("en", { month: "short"  }) + "-" + d.toLocaleString("en", { year: "numeric"});
  $.post('assign.php',{
    'id' : id,
    'dev_name' : assignee,
    'assign_date':assign_date
  },function(result){
                    //alert();
                    var data=JSON.parse(result);
                    $("#messg").html('<div class="alert alert-success"><strong>Task Assigned Successfully!</strong></div>').fadeIn('slow');setTimeout(function(){$("#messg").html('<div class="alert alert-success"><strong>Task Assigned Successfully!</strong></div>').fadeOut('slow'); },1000);
                    //$("#myModal").modal();
                   //alert(""+$("#"+id).html());
                  
                   $("#"+id).remove().fadeOut('slow'); 
               $('.tog').slideUp('fast');
                     if($("#"+assignee).length>0){
                if(p_content.length>18){
                      var newname=p_content.substr(0,18);
                      html= '<div class="box-item" style="margin-top:8px;" eid="'+assignee+'" itemid="'+id+'" status="Assigned" id="'+id+'"><div style="width:100%;position:relative;"onclick="toggle(this)" class="btn btn-warning  " style="text-align:left;overflow:hidden;white-space:nowrap;">'+newname+'<span   onclick="stop(event,this)" class="viewproject" data-content="'+p_content+'"> ...</span><span class="priority">'+data.t_priority+'</span></div><span onclick="del(this)" style="vertical-align: middle;float:right;z-index:99;margin-top: -25px;margin-right: 5px;"  title="Click to Cancel Task" class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span><div class="alert alert-warning slide" style="width:100%;"><p>Assigned Date: '+showdate+'</p><p>Days since First Assigned : 0 Days</p><div></div>';

                }
                else{
                  html= '<div class="box-item" style="margin-top:8px;" eid="'+assignee+'" itemid="'+id+'" status="Assigned" id="'+id+'"><div style="width:100%;position:relative;"onclick="toggle(this)" class="btn btn-warning  " style="text-align:left;overflow:hidden;white-space:nowrap;">'+p_content+'<span class="priority">'+data.t_priority+'</span></div><span onclick="del(this)" style="vertical-align: middle;float:right;z-index:99;margin-top: -25px;margin-right: 5px;" title="Click to Cancel Task" class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span><div class="alert alert-warning slide" style="width:100%;"><p>Assigned Date: '+showdate+'</p><p>Days since First Assigned : 0 Days</p><div></div>';

                }
                         




                       $("#sort1-"+assignee).append(html).fadeIn('slow');
                       //console.log($("#container1").children().length);
                       if($("#container1").children().length==0)
                       { 
                           console.log($("#container1").children().length);
                          $("#Created").css('display','none');
                          $("#main").attr('class','col-md-12 mycontainer');
                       }
                       var value = $("#"+assignee).children(".btn-name").children(".countstatus").children("#assign").attr("set");
                    value=Number(value)+1;
                    $("#"+assignee).children(".btn-name").children(".countstatus").children("#assign").attr("set",value);
                     $("#"+assignee).children(".btn-name").children(".countstatus").children("#assign").text("A: "+value+" | ");
                       //console.log("top:"+$("#"+assignee).offset().top+" offset"+$("#"+assignee).parent().offset().top);
                       //console.log($("#"+assignee).offset().top);
                       //console.log($("#"+assignee).parent().offset().top);
                       
                       //console.log($("#"+assignee).offset().top);
                       //console.log($("#main").height()+" ");
                       //if($("#main").height())
                         //console.log($('#main').prop('scrollHeight')+"");
                       if($("#"+assignee).offset().top>$('#main').height())
                       {
                        //console.log($("#"+assignee).offset().top+" "+$("#"+assignee).children('.tog').outerHeight()+" "+$("#"+assignee).parent().offset().top)
                        $("#"+assignee).children('.tog').slideDown('fast');
                        setTimeout(function(){
                            $("#main").animate({scrollTop:+$("#"+assignee).offset().top+$("#"+assignee).children('.tog').outerHeight()-$("#"+assignee).parent().offset().top },10,'linear');
                                                   
                                                 },100);
                      }
                      else if($("#"+assignee).offset().top<($('#main').height()/2)){
                        //console.log("dsl");
                        $("#main").animate({scrollTop:$("#"+assignee).offset().top-$("#"+assignee).parent().offset().top},10,'linear',function(){
                          $("#"+assignee).children('.tog').slideDown('fast');
                        });
                       }
                      else{
                         $("#"+assignee).children('.tog').slideDown('fast');
                        setTimeout(function(){
                            $("#main").animate({scrollTop:$("#"+assignee).offset().top-$("#"+assignee).parent().offset().top},10,'linear');
                                                   
                                                 },100);

                      }



                     }                   
                    else{
                      if(p_content.length>18){
                         var newname = p_content.substr(0,18);
                         var html2='<div class="box-item"style="margin-top:8px;" itemid="'+id+'" status="Assigned" eid="'+assignee+'" id="'+id+'"><div  onclick="toggle(this)" style="width:100%;position:relative;" class="btn btn-warning " style="text-align:left;overflow:hidden;white-space:nowrap;" >'+newname+'<span   onclick="stop(event,this)" class="viewproject" data-content="'+p_content+'"> ...</span><span class="priority">'+data.t_priority+'</span></div><span onclick="del(this)" style="vertical-align: middle;float:right;z-index:99;margin-top: -25px;margin-right: 5px;" class="glyphicon glyphicon-remove-sign" title="Click to Cancel Task" aria-hidden="true"></span><div class="alert alert-warning slide"><p>Assigned Date: '+showdate+'</p><p>Days since First Assigned : 0</p></div></div>';
                      }
                      else{
                         var html2='<div class="box-item"style="margin-top:8px;" itemid="'+id+'" status="Assigned" eid="'+assignee+'" id="'+id+'"><div  onclick="toggle(this)" style="width:100%;position:relative;" class="btn btn-warning " style="text-align:left;overflow:hidden;white-space:nowrap;" >'+p_content+'<span class="priority">'+data.t_priority+'</span></div><span onclick="del(this)" style="vertical-align: middle;float:right;z-index:99;margin-top: -25px;margin-right: 5px;" title="Click to Cancel Task" class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span><div class="alert alert-warning slide"><p>Assigned Date: '+showdate+'</p><p>Days since First Assigned : 0</p></div></div>';
                      }

                     
var html='<div id="'+assignee+'"><div class="btn btn-primary btn-name" onclick="toggle2(this)" style="width:100%;">'+data.t_name+'<div class="countstatus"><span id="assign" set="1">A: 1 | </span><span id="wip" set="0" >W: 0 | </span><span id="hold" set="0">H: 0 | </span><span id="deli" set="0">D: 0 </span></div></div><div class="row alert alert-info tog"><div><div class=""> <div class="panel panel-default mypanel"> <div class="panel-heading"> <h1 class="panel-title">Assigned</h1> </div> <div id="container2'+assignee+'" class=" panel-body box-container"> <div id="sort1-'+assignee+'" eid="'+assignee+'" class="connect_'+assignee+'">'+html2+'</div> </div> </div> </div> </div><div class=""> <div class="panel panel-default mypanel"> <div class="panel-heading"> <h1 class="panel-title" style="overflow:hidden; white-space:nowrap;">Work in Progress</h1> </div> <div  class="container3 panel-body box-container"> <div id="sort2-'+assignee+'" eid="'+assignee+'" class="connect_'+assignee+'"> </div> </div> </div> </div> <div class=""> <div class="panel panel-default mypanel"> <div class="panel-heading"> <h1 class="panel-title">Hold</h1> </div> <div id="hold-'+assignee+'"  class="hold panel-body box-container"> <div id="sort3-'+assignee+'" eid="'+assignee+'" class="connect_'+assignee+'"> </div> </div> </div> </div> <div class=""> <div class="panel panel-default mypanel"> <div class="panel-heading"> <h1 class="panel-title">Delivered</h1> </div> <div id="container4-'+assignee+'" class="container4 panel-body box-container"> <div id="sort4-'+assignee+'" eid="'+assignee+'" class="connect_'+assignee+'"> </div> </div> </div> </div> <div class=""> <div class="panel panel-default mypanel"> <div class="panel-heading"> <h1 class="panel-title">Compeleted</h1> </div> <div id="container5-'+assignee+'" class="container5 panel-body box-container"> <div id="sort5-'+assignee+'" eid="'+assignee+'" class="connect_'+assignee+'"> </div> </div> </div> </div> </div><script type="text/javascript"> var date_diff_indays = function(date1, date2) {dt1 = new Date(date1);dt2 = new Date(date2);return Math.floor((Date.UTC(dt2.getFullYear(), dt2.getMonth(), dt2.getDate()) - Date.UTC(dt1.getFullYear(), dt1.getMonth(), dt1.getDate()) ) /(1000 * 60 * 60 * 24));}; $("#sort1-'+assignee+',#sort2-'+assignee+',#sort3-'+assignee+',#sort4-'+assignee+',#sort5-'+assignee+'").css({\'background\': \'#eee\',\'padding\': \'5px\'}); $("#sort1-'+assignee+'").sortable({connectWith: ".connect_'+assignee+'", helper: "clone", dropOnEmpty: true, receive: function(ev, ui) {if($(ev.target).attr("eid")!=ui.item.attr("eid")||ui.item.attr("status")=="Delivered"||ui.item.attr("status")=="Success"||ui.item.attr("status")=="Hold"){ui.sender.sortable("cancel"); } else{var itemid = $(ev.originalEvent.toElement).parent().attr("itemid"); var status =$(ev.originalEvent.toElement).parent().attr("status"); var eid=$(ev.originalEvent.toElement).parent().attr("eid"); $(".box-item").each(function() {if ($(this).attr("itemid") === itemid&&status=="WIP") {var value = $("#"+eid).children(".btn-name").children(".countstatus").children("#wip").attr("set"); console.log(value); value=Number(value)-1; $("#"+eid).children(".btn-name").children(".countstatus").children("#wip").attr("set",value); $("#"+eid).children(".btn-name").children(".countstatus").children("#wip").text("W: "+value+" | "); $(this).children("div:first").removeClass("btn-WIP"); $(this).children("div:first").addClass("btn-warning"); $(this).children("div:last").removeClass("alert-WIP"); $(this).children("div:last").addClass("alert-warning"); $(this).attr("status","Assigned"); var changed_status="Assigned"; $.post("status_update.php",{"id" : itemid, "status" : changed_status, },function(result){var value = $("#"+eid).children(".btn-name").children(".countstatus").children("#assign").attr("set"); value=Number(value)+1; $("#"+eid).children(".btn-name").children(".countstatus").children("#assign").attr("set",value); $("#"+eid).children(".btn-name").children(".countstatus").children("#assign").text("A: "+value+" | "); $("#messg").html(\'<div class="alert alert-success"><strong>Task Assigned Successfully!</strong></div>\').fadeIn(\'slow\'); setTimeout(function(){$("#messg").html(\'<div class="alert alert-success"><strong>Task Assigned Successfully!</strong></div>\').fadeOut(\'slow\'); },1000); }); } }); } }, stop: function (event, ui) {var data = $(this).sortable(\'serialize\'); $.ajax({data: data, type: \'POST\', url: \'update_priority.php\', success:function(datastring){console.log(data); var temp = data.split("&"); var i=1; for(s in temp){$("#TSK_"+temp[s].split("TSK[]=")[1]).children().children("span").text(i); i++; } } }); }, update: function (event, ui) {var data = $(this).sortable(\'serialize\'); $.ajax({data: data, type: \'POST\', url: \'update_priority.php\', success:function(datastring){var temp = data.split("&"); var i=1; for(s in temp){$("#TSK_"+temp[s].split("TSK[]=")[1]).children().children("span").text(i); i++; } } }); } }); $("#sort4-'+assignee+'").sortable({connectWith: ".connect_'+assignee+'", helper: "clone", dropOnEmpty: true, receive: function(ev, ui) {if($(ev.target).attr("eid")!=ui.item.attr("eid")||ui.item.attr("status")=="Hold"||ui.item.attr("status")=="Success") {ui.sender.sortable("cancel"); } else{var itemid = $(ev.originalEvent.toElement).parent().attr("itemid"); var status =$(ev.originalEvent.toElement).parent().attr("status"); var eid=$(ev.originalEvent.toElement).parent().attr("eid"); $(".box-item").each(function() {if ($(this).attr("itemid") === itemid&&(status=="WIP"||status=="Assigned")) {if(status=="Assigned"){$(this).children("div:first").removeClass("btn-warning"); $(this).children("div:first").addClass("btn-info"); $(this).children("div:last").removeClass("alert-warning"); $(this).children("div:last").addClass("alert-info"); var value = $("#"+eid).children(".btn-name").children(".countstatus").children("#assign").attr("set"); value=Number(value)-1; $("#"+eid).children(".btn-name").children(".countstatus").children("#assign").attr("set",value); $("#"+eid).children(".btn-name").children(".countstatus").children("#assign").text("A: "+value+" | "); } else if(status=="WIP"){$(this).children("div:first").removeClass("btn-WIP"); $(this).children("div:first").addClass("btn-info"); $(this).children("div:last").removeClass("alert-WIP"); $(this).children("div:last").addClass("alert-info"); var value = $("#"+eid).children(".btn-name").children(".countstatus").children("#wip").attr("set"); value=Number(value)-1; $("#"+eid).children(".btn-name").children(".countstatus").children("#wip").attr("set",value); $("#"+eid).children(".btn-name").children(".countstatus").children("#wip").text("W: "+value+" | "); } $(this).children("div:first").children("span").remove(); $(this).attr("status","Delivered"); var changed_status="Delivered"; $.post("status_update.php",{"id" : itemid, "status" : changed_status, },function(result){var value = $("#"+eid).children(".btn-name").children(".countstatus").children("#deli").attr("set"); value=Number(value)+1; $("#"+eid).children(".btn-name").children(".countstatus").children("#deli").attr("set",value); $("#"+eid).children(".btn-name").children(".countstatus").children("#deli").text("D: "+value+" "); $("#messg").html(\'<div class="alert alert-success"><strong>Task Delivered Successfully!</strong></div>\').fadeIn(\'slow\'); setTimeout(function(){$("#messg").html(\'<div class="alert alert-success"><strong>Task Delivered Successfully!</strong></div>\').fadeOut(\'slow\'); },1000); }); } }); } } }); $("#sort3-'+assignee+'").sortable({connectWith: ".connect_'+assignee+'", helper: "clone", dropOnEmpty: true, receive: function(ev, ui) {console.log($(ev.target).attr("eid")); if($(ev.target).attr("eid")!=ui.item.attr("eid")||ui.item.attr("status")=="Delivered"||ui.item.attr("status")=="Success") {ui.sender.sortable("cancel"); } else{var itemid = $(ev.originalEvent.toElement).parent().attr("itemid"); var status =$(ev.originalEvent.toElement).parent().attr("status"); var eid=$(ev.originalEvent.toElement).parent().attr("eid"); $(".box-item").each(function() {if ($(this).attr("itemid") === itemid&&(status=="WIP"||status=="Assigned")) {if(status=="Assigned"){$(this).children("div:first").removeClass("btn-warning"); $(this).children("div:first").addClass("btn-hold"); $(this).children("div:last").removeClass("alert-warning"); $(this).children("div:last").addClass("alert-hold"); var value = $("#"+eid).children(".btn-name").children(".countstatus").children("#assign").attr("set"); value=Number(value)-1; $("#"+eid).children(".btn-name").children(".countstatus").children("#assign").attr("set",value); $("#"+eid).children(".btn-name").children(".countstatus").children("#assign").text("A: "+value+" | "); } else if(status=="WIP"){$(this).children("div:first").removeClass("btn-WIP"); $(this).children("div:first").addClass("btn-hold"); $(this).children("div:last").removeClass("alert-WIP"); $(this).children("div:last").addClass("alert-hold"); var value = $("#"+eid).children(".btn-name").children(".countstatus").children("#wip").attr("set"); value=Number(value)-1; $("#"+eid).children(".btn-name").children(".countstatus").children("#wip").attr("set",value); $("#"+eid).children(".btn-name").children(".countstatus").children("#wip").text("W: "+value+" | "); } $(this).children("div:first").children("span").remove(); $(this).attr("status","Hold"); var changed_status="Hold"; $.post("status_update.php",{"id" : itemid, "status" : changed_status, },function(result){var value = $("#"+eid).children(".btn-name").children(".countstatus").children("#hold").attr("set"); value=Number(value)+1; $("#"+eid).children(".btn-name").children(".countstatus").children("#hold").attr("set",value); $("#"+eid).children(".btn-name").children(".countstatus").children("#hold").text("H: "+value+" | "); $("#messg").html(\'<div class="alert alert-success"><strong>Task on Hold!</strong></div>\').fadeIn(\'slow\'); setTimeout(function(){$("#messg").html(\'<div class="alert alert-success"><strong>Task on Hold!</strong></div>\').fadeOut(\'slow\'); },1000); }); } }); } } }); $("#sort2-'+assignee+'").sortable({connectWith: ".connect_'+assignee+'", helper: "clone", dropOnEmpty: true, receive: function(ev, ui) {console.log("receive"); if($(ev.target).attr("eid")!=ui.item.attr("eid")||ui.item.attr("status")=="Success"){ui.sender.sortable("cancel"); } else{var itemid = $(ev.originalEvent.toElement).parent().attr("itemid"); var status =$(ev.originalEvent.toElement).parent().attr("status"); var eid=$(ev.originalEvent.toElement).parent().attr("eid"); $(".box-item").each(function() {if ($(this).attr("itemid") === itemid&&(status=="Assigned"||status=="Delivered"||status=="Hold")) {if(status=="Assigned"){$(this).children("div:first").removeClass("btn-warning"); $(this).children("div:first").addClass("btn-WIP"); $(this).children("div:last").removeClass("alert-warning"); $(this).children("div:last").addClass("alert-WIP"); var value = $("#"+eid).children(".btn-name").children(".countstatus").children("#assign").attr("set"); value=Number(value)-1; $("#"+eid).children(".btn-name").children(".countstatus").children("#assign").attr("set",value); $("#"+eid).children(".btn-name").children(".countstatus").children("#assign").text("A: "+value+" | "); } else if(status=="Delivered"){$(this).children("div:first").removeClass("btn-info"); $(this).children("div:first").addClass("btn-WIP"); $(this).children("div:last").removeClass("alert-info"); $(this).children("div:last").addClass("alert-WIP"); $(this).children("div:first").css("position","relative"); $(this).children("div:first").append("<span class=\'priority\'></span>"); var value = $("#"+eid).children(".btn-name").children(".countstatus").children("#deli").attr("set"); value=Number(value)-1; $("#"+eid).children(".btn-name").children(".countstatus").children("#deli").attr("set",value); $("#"+eid).children(".btn-name").children(".countstatus").children("#deli").text("D: "+value+" "); } else if(status=="Hold"){$(this).children("div:first").removeClass("btn-hold"); $(this).children("div:first").addClass("btn-WIP"); $(this).children("div:last").removeClass("alert-hold"); $(this).children("div:last").addClass("alert-WIP"); $(this).children("div:first").css("position","relative"); $(this).children("div:first").append("<span class=\'priority\'></span>"); var value = $("#"+eid).children(".btn-name").children(".countstatus").children("#hold").attr("set"); value=Number(value)-1; $("#"+eid).children(".btn-name").children(".countstatus").children("#hold").attr("set",value); $("#"+eid).children(".btn-name").children(".countstatus").children("#hold").text("H: "+value+" | "); } $(this).attr("status","WIP"); var changed_status="WIP"; $.post("status_update.php",{"id" : itemid, "status" : changed_status, },function(result){var value = $("#"+eid).children(".btn-name").children(".countstatus").children("#wip").attr("set"); value=Number(value)+1; $("#"+eid).children(".btn-name").children(".countstatus").children("#wip").attr("set",value); $("#"+eid).children(".btn-name").children(".countstatus").children("#wip").text("W: "+value+" | "); $("#messg").html(\'<div class="alert alert-success"><strong>Task in Progress!</strong></div>\').fadeIn(\'slow\'); setTimeout(function(){$("#messg").html(\'<div class="alert alert-success"><strong>Task in Progress!</strong></div>\').fadeOut(\'slow\'); },1000); }); } }); } }, update: function (event, ui) {var data = $(this).sortable(\'serialize\'); $.ajax({data: data, type: \'POST\', url: \'update_priority_wip.php\', success:function(datastring){var temp = data.split("&"); var i=1; for(s in temp){$("#TSK_"+temp[s].split("TSK[]=")[1]).children().children("span").text(i); i++; } } }); }, stop: function (event, ui) {var data = $(this).sortable(\'serialize\'); $.ajax({data: data, type: \'POST\', url: \'update_priority_wip.php\', success:function(datastring){console.log(data); var temp = data.split("&"); var i=1; for(s in temp){$("#TSK_"+temp[s].split("TSK[]=")[1]).children().children("span").text(i); i++; } } }); } }); $("#sort5-'+assignee+'").sortable({connectWith: ".connect_'+assignee+'", helper: "clone", dropOnEmpty: true, receive: function(ev, ui) {if($(ev.target).attr("eid")!=ui.item.attr("eid")||ui.item.attr("status")!="Delivered"){ui.sender.sortable("cancel"); } else{var u;var itemid = $(ev.originalEvent.toElement).parent().attr("itemid"); var status =$(ev.originalEvent.toElement).parent().attr("status"); var eid=$(ev.originalEvent.toElement).parent().attr("eid"); $(".box-item").each(function() {if ($(this).attr("itemid") === itemid&&status=="Delivered") {$(this).children("div:first").removeClass("btn-info"); $(this).children("div:first").addClass("btn-success");$(this).children("span").remove(); $(this).children("div:first").append(\'<i  title="Click to Archive Task" onclick="del2(event,this)"style="vertical-align:middle;margin-right:-8px;float:right;z-index:99;" class="material-icons">archive</i>\');$(this).children("div:last").removeClass("alert-info"); u=$(this);$(this).children("div:last").addClass("alert-success"); $(this).attr("status","Success"); var changed_status="Success"; $.post("status_update.php",{"id" : itemid, "status" : changed_status, },function(result){data=JSON.parse(result);'+
'var objDate = new Date(data.assigndate);var objDate2 = new Date(data.completion_date);var strDate = objDate.toLocaleString("en", { day: "numeric" }) + "-"+objDate.toLocaleString("en", { month: "short"  }) + "-" + objDate.toLocaleString("en", { year: "numeric"});var strDate2 = objDate2.toLocaleString("en", { day: "numeric" }) + "-"+objDate2.toLocaleString("en", { month: "short"  }) + "-" + objDate2.toLocaleString("en", { year: "numeric"});u.children("div:last").html("<p>Assignned Date: "+strDate+"</p><p>Compeletion Date: "+strDate2+"</p><p>Days to Compelete: "+(Number(date_diff_indays(data.assigndate,data.completion_date))+1)+"</p>");var value = $("#"+eid).children(".btn-name").children(".countstatus").children("#deli").attr("set"); value=Number(value)-1; $("#"+eid).children(".btn-name").children(".countstatus").children("#deli").attr("set",value); $("#"+eid).children(".btn-name").children(".countstatus").children("#deli").text("D: "+value+" "); $("#messg").html(\'<div class="alert alert-success"><strong>Task Compeleted Successfully!</strong></div>\').fadeIn(\'slow\'); setTimeout(function(){$("#messg").html(\'<div class="alert alert-success"><strong>Task Compeleted Successfully!</strong></div>\').fadeOut(\'slow\'); },1000); }); } }); } } }); </scr'+'ipt></div></div>';
$('.tog').slideUp('fast');
$("#main").prepend(html).fadeIn('slow',function(){
  var scroll=$("#"+assignee).offset().top-$("#"+assignee).parent().offset().top;
                           //console.log(scroll);
                          $("#main").animate({scrollTop:scroll},10,'linear',function(){
                                  $("#"+assignee).children('.tog').slideDown('slow');
                                  //console.log("ho gya");
                             });
                       
});





                      }
                     
                 });
}

}



$(window).on('load',function(){
   if($("#container1").children().length==0)
   {
    $("#Created").css('display','none');
    $("#main").attr('class','col-md-12 mycontainer');
   }
 
  $('#submit').click(function(){
      create();
       
      });
$(document).keypress(function(e) {
    if(e.which == 13) {
        create();
    }
});

});


</script>

</head>
<body>
  
  <div  class="container-fluid" style="position: relative;">
    <div class="row " style="padding-left: 15px;padding-right: 30px;margin-top: 5px;">
      <div class="panel panel-primary">
        <div class="panel-heading" style="text-align: center;font-size: 1.2em;font-weight: bold;">Task Manager</div>
      </div>
      
    </div>
        
        <div class="row" style="margin-top:-10px;margin-left: 0px;
      margin-right: 0px;">
          <div class="col-md-7" style="padding-right: 0;padding-left: 0;">
            <input class="form-control" style="width: 100%;" name="" id="project" type="text" placeholder="Task"  autofocus="true" required>

          </div>
          <div class="col-md-3" style="padding-right: 0;">
            <select  id="dev_name" class="form-control" style="width: 100%;font-size: 11px;">
              <option value="" selected>Employee Name</option>
              <?php 
              foreach ($records as $row) {
                echo '<option value="'.$row['EmployeeID'].'" >'.$row['name']."-".$row['EmployeeID'].'</option>';
              }
              ?>
            </select>

          </div>
          <div class="col-md-2" >
            <button style="width: 100%;text-align: center;"class="btn btn-success" type="button" id="submit"><i class="glyphicon glyphicon-plus"></i> Create Task</button>
          </div>
        </div>
        
    
<hr style="margin-top: 12px;">
<div class="row" style="margin-top: -8px;">

  <div class="col-md-3 " id="Created" >
    <div class="panel panel-default">
      <div class="panel-heading">
        <h1 class="panel-title">Created</h1>
      </div>
      <div id="container1" class="panel-body box-container">
        <?php
        
   //var_dump($pro);
        $i=1;
        foreach ($pro as $row) {

          if($row['t_status']=='Created'){
            echo '<div style="margin-top:8px;" id="'.$row['t_id'].'"><div id="box-'.$i.'" itemid="'.$row['t_id'].'" status="'.$row['t_status'].'" onclick="toggle(this)" class="btn btn-primary box-item">'.$row['t_name'].'</div><span onclick="del(this)" title="Click to Cancel Task" style="vertical-align: middle;float:right;z-index:99;margin-top: -25px;
            margin-right: 5px;" class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span>
            <input type="hidden" value="'.$row['t_id'].'"/>';
            echo '<div class="slide" style="margin-top:3px;"><select  style="width:200px;font-size:11px;" id="dev_name" class="form-control" >
            <option value="" selected>Employee Name</option>';

            foreach ($records as $row) {
              echo '<option value="'.$row['EmployeeID'].'" >'.$row['name']."-".$row['EmployeeID'].'</option>';
            }
            echo'</select>';
            echo '<button class="btn btn-success assign" onclick="assign(this)" type="button"  style="float:right;margin-top:-34px;"><span class="glyphicon glyphicon-send" aria-hidden="true" ></span></div>
            </div>';
            $i++;
          }

        }
        ?>
      </div>
     </div>
      
  </div>
  
  <div class="col-md-9 mycontainer" id="main" > 
    <?php 
    $flag=null;
    foreach ($count as $emp) {
      $eid=$emp['assign_employee'];
      //echo "id :".$eid;
      $esql = "SELECT name FROM  `employees` WHERE  `EmployeeID`='$eid' ";
      $eres = mysqli_query($con, $esql);
             $erow = mysqli_fetch_assoc($eres);
             //echo "<strong>Employee: ".$erow['name']."</strong>";
      if($eid!=null){
        $assign=0;$wip=0;$hold=0;$deli=0;
         foreach ($pro as $row) {

                if($eid==$row['assign_employee'])
                  {
                      
                       
                       //
                       if($row['t_status']=='Assigned'){
                            $assign++;
                       }
                       elseif ($row['t_status']=='WIP') {
                            $wip++;
                       }
                       elseif ($row['t_status']=='Hold') {
                            $hold++;
                       }
                       elseif ($row['t_status']=='Delivered') {
                            $deli++;
                       }
                  }
              }
        echo '<div id="'.$eid.'"><div class="btn btn-primary btn-name" onclick="toggle2(this)" style="width:100%;">'.$erow['name'].'<div class="countstatus"><span id="assign" set="'.$assign.'">A: '.$assign.'&nbsp;|&nbsp;</span><span id="wip" set="'.$wip.'" >W: '.$wip.'&nbsp;|&nbsp;</span><span id="hold" set="'.$hold.'">H: '.$hold.'&nbsp;|&nbsp;</span><span id="deli" set="'.$deli.'">D: '.$deli.'&nbsp;</span></div></div><div class="row alert alert-info tog">';
        echo '<div>
        <div class="">
        <div class="panel panel-default mypanel">
        <div class="panel-heading">
        <h1 class="panel-title">Assigned</h1>
        </div>
        <div id="container2'.$eid.'" class=" panel-body box-container">';
        echo '<div id="sort1-'.$eid.'" eid="'.$eid.'" class="connect_'.$eid.'">';
        foreach ($pro as $row) {
          if($row['t_status']=='Assigned'){
            if($eid==$row['assign_employee']){
              if(strlen($row['t_name'])>12){
                 $temp_name=substr($row['t_name'], 0,12);
                  echo '<div  class="box-item" eid="'.$eid.'" style="margin-top:8px;"  itemid="'.$row['t_id'].'" id="'.$row['t_id'].'" status="'.$row['t_status'].'" ><div  style="width:100%;position:relative;"onclick="toggle(this)" class="btn btn-warning"  >'.$temp_name.'<span   onclick="stop(event,this)" class="viewproject" data-content="'.$row['t_name'].'"> ...</span><span class="priority">'.$row['A_priority'].'</span></div><span  title="Click to Cancel Task" onclick="del(this)"style="vertical-align: middle;float:right;z-index:99;margin-top: -25px;margin-right: 2%;" class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span><div class="alert alert-warning slide" >';
                }
              else{
                echo '<div  class="box-item" eid="'.$eid.'" style="margin-top:8px;"  itemid="'.$row['t_id'].'" id="'.$row['t_id'].'" status="'.$row['t_status'].'" ><div  style="width:100%;position:relative;"onclick="toggle(this)" class="btn btn-warning"  >'.$row['t_name'].'<span class="priority">'.$row['A_priority'].'</span></div><span  title="Click to Cancel Task" onclick="del(this)"style="vertical-align: middle;float:right;z-index:99;margin-top: -25px;margin-right: 2%;" class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span><div class="alert alert-warning slide" >';
              }
             
              $id=$row['t_id'];
              $assign_date=date_create($row['assign_date']);
              if(date_format($assign_date,"Y-m-d")!='-0001-11-30')
              {
               
               $currentdate = date_create(date("Y-m-d"));
               $diff=date_diff($currentdate,$assign_date);
               //echo $diff->format("%a days");
             echo "<p>Assigned Date: ".date_format($assign_date,"d-M-Y")."</p>";
             echo "<p>Days Since First Assigned: ".$diff->format("%a")."</p>";
             };

             echo '</div>
             </div>';
           }
         }

       }
       echo '</div>
       </div>
       </div>
       </div>';

        echo '<div class="">
        <div class="panel panel-default mypanel">
        <div class="panel-heading">
        <h1 class="panel-title" style="overflow:hidden;
white-space:nowrap;">Work in Progress</h1>
        </div>
        <div  class="container3 panel-body box-container">';
        echo '<div id="sort2-'.$eid.'" eid="'.$eid.'" class="connect_'.$eid.'">';
        foreach ($pro2 as  $row) {
    # code...
          if($row['t_status']=='WIP'){
            if($eid==$row['assign_employee']){
                 if(strlen($row['t_name'])>12){
                  $temp_name=substr($row['t_name'], 0,12);
                    echo '<div  class="box-item" eid="'.$eid.'" id="'.$row['t_id'].'" style="margin-top:8px;" itemid="'.$row['t_id'].'" status="'.$row['t_status'].'"><div  style="width:100%;position:relative;" onclick="toggle(this)" class="btn btn-WIP ">'.$temp_name.'<span  onclick="stop(event,this)"  class="viewproject" data-content="'.$row['t_name'].'"> ...</span><span class="priority" >'.$row['w_priority'].'</span></div><span title="Click to Cancel Task" onclick="del(this)"style="vertical-align: middle;float:right;z-index:99;margin-top: -25px;margin-right: 2%;" class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span><div class="alert alert-WIP slide">';
                 }
                 else{
                     echo '<div  class="box-item" eid="'.$eid.'" id="'.$row['t_id'].'" style="margin-top:8px;" itemid="'.$row['t_id'].'" status="'.$row['t_status'].'"><div  style="width:100%;position:relative;" onclick="toggle(this)" class="btn btn-WIP ">'.$row['t_name'].'<span class="priority" >'.$row['w_priority'].'</span></div><span  title="Click to Cancel Task" onclick="del(this)"style="vertical-align: middle;float:right;z-index:99;margin-top: -25px;margin-right: 2%;" class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span><div class="alert alert-WIP slide">';
                 }
             
              $id=$row['t_id'];
              $assign_date=date_create($row['assign_date']);
              if(date_format($assign_date,"Y-m-d")!='-0001-11-30')
              {
              
               $currentdate = date_create(date("Y-m-d"));
               $diff=date_diff($currentdate,$assign_date);
               //echo $diff->format("%a days");
             echo "<p>Assigned Date: ".date_format($assign_date,"d-M-Y")."</p>";
             echo "<p>Days Since First Assigned: ".$diff->format("%a")."</p>";
             };

              echo '</div></div>';
            }
          }

        }
        echo '</div>
        </div>
        </div>
        </div>';

          echo '<div class="">
          <div class="panel panel-default mypanel">
          <div class="panel-heading">
          <h1 class="panel-title">Hold</h1>
          </div>
          <div id="hold-'.$eid.'"  class="hold panel-body box-container">';
          echo "<div eid='".$eid."' id='sort3-".$eid."'  class='connect_".$eid."'>";
          foreach ($pro as  $row) {
    # code...
            if($row['t_status']=='Hold'){
              if($eid==$row['assign_employee']){
                if(strlen($row['t_name'])>12){
                  $temp_name=substr($row['t_name'], 0,12);
                  echo '<div class="box-item" eid="'.$eid.'" id="'.$row['t_id'].'" style="margin-top:8px;" itemid="'.$row['t_id'].'" status="'.$row['t_status'].'"><div  style="width:100%;" onclick="toggle(this)" class="btn btn-hold ">'.$temp_name.'<span onclick="stop(event,this)" class="viewproject" data-content="'.$row['t_name'].'"> ...</span></div><span  title="Click to Cancel Task" onclick="del(this)"style="vertical-align: middle;float:right;z-index:99;margin-top: -25px;margin-right: 2%;" class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span><div class="alert alert-hold slide">';
                }
                else{
                  echo '<div class="box-item" eid="'.$eid.'" id="'.$row['t_id'].'" style="margin-top:8px;" itemid="'.$row['t_id'].'" status="'.$row['t_status'].'"><div  style="width:100%;" onclick="toggle(this)" class="btn btn-hold ">'.$row['t_name'].'</div><span  title="Click to Cancel Task" onclick="del(this)"style="vertical-align: middle;float:right;z-index:99;margin-top: -25px;margin-right: 2%;" class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span><div class="alert alert-hold slide">';
                }
                
                $id=$row['t_id'];
                $assign_date=date_create($row['assign_date']);
              if(date_format($assign_date,"Y-m-d")!='-0001-11-30')
              {
              
               $currentdate = date_create(date("Y-m-d"));
               $diff=date_diff($currentdate,$assign_date);
               //echo $diff->format("%a days");
             echo "<p>Assigned Date: ".date_format($assign_date,"d-M-Y")."</p>";
             echo "<p>Days Since First Assigned: ".$diff->format("%a")."</p>";
             };

                echo '</div></div>';
              }
            }

          }
          echo '</div>
          </div>
          </div>
          </div>';

            echo '<div class="">
            <div class="panel panel-default mypanel">
            <div class="panel-heading">
            <h1 class="panel-title">Delivered</h1>
            </div>
            <div id="container4-'.$eid.'" class="container4 panel-body box-container">';
            echo '<div id="sort4-'.$eid.'" eid="'.$eid.'" class="connect_'.$eid.'">';
            foreach ($pro as  $row) {
    # code...
              if($row['t_status']=='Delivered'){
                if($eid==$row['assign_employee']){
                  if(strlen($row['t_name'])>12){
                  $temp_name=substr($row['t_name'], 0,12);
                  echo '<div class="box-item" eid="'.$eid.'" style="margin-top:8px;" id="'.$row['t_id'].'" itemid="'.$row['t_id'].'" status="'.$row['t_status'].'"><div  style="width:100%;" onclick="toggle(this)" class="btn btn-info ">'.$temp_name.'<span   onclick="stop(event,this)" class="viewproject" data-content="'.$row['t_name'].'"> ...</span></div><span title="Click to Cancel Task" onclick="del(this)"style="vertical-align: middle;float:right;z-index:99;margin-top: -25px;margin-right: 2%;" class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span><div class="alert alert-info slide">';
                }
                else{
                  echo '<div class="box-item" eid="'.$eid.'" style="margin-top:8px;" id="'.$row['t_id'].'" itemid="'.$row['t_id'].'" status="'.$row['t_status'].'"><div  style="width:100%;" onclick="toggle(this)" class="btn btn-info ">'.$row['t_name'].'</div><span  title="Click to Cancel Task" onclick="del(this)"style="vertical-align: middle;float:right;z-index:99;margin-top: -25px;margin-right: 2%;" class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span><div class="alert alert-info slide">';
                }
                  
                  $id=$row['t_id'];
                  $assign_date=date_create($row['assign_date']);
              if(date_format($assign_date,"Y-m-d")!='-0001-11-30')
              {
               
               $currentdate = date_create(date("Y-m-d"));
               $diff=date_diff($currentdate,$assign_date);
               //echo $diff->format("%a days");
             echo "<p>Assigned Date: ".date_format($assign_date,"d-M-Y")."</p>";
             echo "<p>Days Since First Assigned: ".$diff->format("%a")."</p>";
             };

                  echo '</div></div>';
                }
              }

            }
            echo '</div>
            </div>
            </div>
            </div>';

              echo '

              <div class="">
              <div class="panel panel-default mypanel">
              <div class="panel-heading">
              <h1 class="panel-title">Compeleted</h1>
              </div>
              <div id="container5-'.$eid.'" class="container5 panel-body box-container">';
              echo '<div id="sort5-'.$eid.'" eid="'.$eid.'" class="connect_'.$eid.'">';
              foreach ($pro as  $row) {
    # code...
                if($row['t_status']=='Success'){
                  if($eid==$row['assign_employee']){

                     if(strlen($row['t_name'])>12){
                  $temp_name=substr($row['t_name'], 0,12);
                   echo '<div class="box-item" eid="'.$eid.'" style="margin-top:8px;" itemid="'.$row['t_id'].'" id="'.$row['t_id'].'" status="'.$row['t_status'].'"><div  style="width:100%;" onclick="toggle(this)" class="btn btn-success">'.$temp_name.'<span onclick="stop(event,this)" class="viewproject" data-content="'.$row['t_name'].'"> ...</span><i  title="Click to Archive Task" onclick="del2(event,this)"style="vertical-align: middle;float:right;z-index:99;margin-right: -8px" class="material-icons">archive</i></div>

                    <div class="alert alert-success slide">';
                }
                else{
                   echo '<div class="box-item" eid="'.$eid.'" style="margin-top:8px;" itemid="'.$row['t_id'].'" id="'.$row['t_id'].'" status="'.$row['t_status'].'"><div  style="width:100%;" onclick="toggle(this)" class="btn btn-success">'.$row['t_name'].'<i  title="Click to Archive Task" onclick="del2(event,this)"style="
                   vertical-align: middle;margin-right: -8px;float:right;z-index:99;" class="material-icons">archive</i></div>

                    <div class="alert alert-success slide">';
                }
                   
              $assign_date=date_create($row['assign_date']);
              $completion_date=date_create($row['completion_date']);
              if(date_format($assign_date,"Y-m-d")!='-0001-11-30'&&date_format($completion_date,"Y-m-d")!='-0001-11-30')
              {
               $currentdate = date_create(date("Y-m-d"));
               $diff=date_diff($assign_date,$completion_date);
               //echo $diff->format("%a days");
             echo "<p>Assigned Date: ".date_format($assign_date,"d-M-Y")."</p>";
             echo "<p>Compeletion Date: ".date_format($completion_date,"d-M-Y")."</p>";
             echo "<p>Days to Compelete: ".($diff->format("%a")+1)."</p>";
             };

                    echo '</div></div>';
                  }
                }

              }
              echo'</div>';
              echo ' </div>
              </div>
              </div>';


              echo '</div><script type="text/javascript">
             
              var date_diff_indays = function(date1, date2) {
                dt1 = new Date(date1);
                dt2 = new Date(date2);
                return Math.floor((Date.UTC(dt2.getFullYear(), dt2.getMonth(), dt2.getDate()) - Date.UTC(dt1.getFullYear(), dt1.getMonth(), dt1.getDate()) ) /(1000 * 60 * 60 * 24));
              }

              $("#sort1-'.$eid.',#sort2-'.$eid.',#sort3-'.$eid.',#sort4-'.$eid.',#sort5-'.$eid.'").css({\'background\': \'#eee\',\'padding\': \'5px\'});
$("#sort1-'.$eid.'").sortable({
        connectWith: ".connect_'.$eid.'",
        helper: "clone",
        dropOnEmpty: true,
        start: function( event, ui ) {
          $( "#sort1-'.$eid.'" ).sortable( "refreshPositions" );
        },
        receive: function(ev, ui) {
    
          if($(ev.target).attr("eid")!=ui.item.attr("eid")||ui.item.attr("status")=="Delivered"||ui.item.attr("status")=="Success"||ui.item.attr("status")=="Hold"){
            ui.sender.sortable("cancel");

          

            
          }
          else{
            
    var itemid = $(ev.originalEvent.toElement).parent().attr("itemid");
    var status =$(ev.originalEvent.toElement).parent().attr("status");
    var eid=$(ev.originalEvent.toElement).parent().attr("eid");
      
      $(".box-item").each(function() {
        if ($(this).attr("itemid") === itemid&&status=="WIP") {
              var value = $("#"+eid).children(".btn-name").children(".countstatus").children("#wip").attr("set");
              console.log(value);
                    value=Number(value)-1;
                    $("#"+eid).children(".btn-name").children(".countstatus").children("#wip").attr("set",value);
                     $("#"+eid).children(".btn-name").children(".countstatus").children("#wip").text("W: "+value+" | ");
          
          $(this).children("div:first").removeClass("btn-WIP");
          $(this).children("div:first").addClass("btn-warning");
           $(this).children("div:last").removeClass("alert-WIP");
           $(this).children("div:last").addClass("alert-warning");
           $(this).attr("status","Assigned");
           //$(this).appendTo("#container5");
           var changed_status="Assigned";
           $.post("status_update.php",{
            "id" : itemid,
            "status" : changed_status,
          },function(result){
                    var value = $("#"+eid).children(".btn-name").children(".countstatus").children("#assign").attr("set");
                    value=Number(value)+1;
                    $("#"+eid).children(".btn-name").children(".countstatus").children("#assign").attr("set",value);
                     $("#"+eid).children(".btn-name").children(".countstatus").children("#assign").text("A: "+value+" | ");
                   $("#messg").html(\'<div class="alert alert-success"><strong>Task Assigned Successfully!</strong></div>\').fadeIn(\'slow\');
                   setTimeout(function(){
                     $("#messg").html(\'<div class="alert alert-success"><strong>Task Assigned Successfully!</strong></div>\').fadeOut(\'slow\');
                    },1000);

                 });

         }
       }); 
          }
        }, stop: function (event, ui) {
        var data = $(this).sortable(\'serialize\');
         $.ajax({
            data: data,
            type: \'POST\',
            url: \'update_priority.php\',
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
        var data = $(this).sortable(\'serialize\');
         $.ajax({
            data: data,
            type: \'POST\',
            url: \'update_priority.php\',
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
        

        $("#sort4-'.$eid.'").sortable({
              connectWith: ".connect_'.$eid.'",
              helper: "clone",
              dropOnEmpty: true,
              start: function( event, ui ) {
          $( "#sort4-'.$eid.'" ).sortable( "refreshPositions" );
        },
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
            var value = $("#"+eid).children(".btn-name").children(".countstatus").children("#assign").attr("set");
                    value=Number(value)-1;
                    $("#"+eid).children(".btn-name").children(".countstatus").children("#assign").attr("set",value);
                     $("#"+eid).children(".btn-name").children(".countstatus").children("#assign").text("A: "+value+" | ");
          }
          else if(status=="WIP"){
            $(this).children("div:first").removeClass("btn-WIP");
            $(this).children("div:first").addClass("btn-info");
            $(this).children("div:last").removeClass("alert-WIP");
            $(this).children("div:last").addClass("alert-info");
            var value = $("#"+eid).children(".btn-name").children(".countstatus").children("#wip").attr("set");
                    value=Number(value)-1;
                    $("#"+eid).children(".btn-name").children(".countstatus").children("#wip").attr("set",value);
                     $("#"+eid).children(".btn-name").children(".countstatus").children("#wip").text("W: "+value+" | ");
          }
          $(this).children("div:first").children("span").remove();
          $(this).attr("status","Delivered");
          
          var changed_status="Delivered";
          $.post("status_update.php",{
            "id" : itemid,
            "status" : changed_status,
          },function(result){
                   
            var value = $("#"+eid).children(".btn-name").children(".countstatus").children("#deli").attr("set");
                    value=Number(value)+1;
                    $("#"+eid).children(".btn-name").children(".countstatus").children("#deli").attr("set",value);
                     $("#"+eid).children(".btn-name").children(".countstatus").children("#deli").text("D: "+value+" ");
                    $("#messg").html(\'<div class="alert alert-success"><strong>Task Delivered Successfully!</strong></div>\').fadeIn(\'slow\');
                   setTimeout(function(){
                     $("#messg").html(\'<div class="alert alert-success"><strong>Task Delivered Successfully!</strong></div>\').fadeOut(\'slow\');
                    },1000);
                   

                 });

        }
      });
                }
                
              }
              });
              


              $("#sort3-'.$eid.'").sortable({
            connectWith: ".connect_'.$eid.'",
            helper: "clone",
            dropOnEmpty: true,
            start: function( event, ui ) {
          $( "#sort3-'.$eid.'" ).sortable( "refreshPositions" );
        },
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
            var value = $("#"+eid).children(".btn-name").children(".countstatus").children("#assign").attr("set");
                    value=Number(value)-1;
                    $("#"+eid).children(".btn-name").children(".countstatus").children("#assign").attr("set",value);
                     $("#"+eid).children(".btn-name").children(".countstatus").children("#assign").text("A: "+value+" | ");
          }
          else if(status=="WIP"){
            $(this).children("div:first").removeClass("btn-WIP");
            $(this).children("div:first").addClass("btn-hold");
            $(this).children("div:last").removeClass("alert-WIP");
            $(this).children("div:last").addClass("alert-hold");
            var value = $("#"+eid).children(".btn-name").children(".countstatus").children("#wip").attr("set");
                    value=Number(value)-1;
                    $("#"+eid).children(".btn-name").children(".countstatus").children("#wip").attr("set",value);
                     $("#"+eid).children(".btn-name").children(".countstatus").children("#wip").text("W: "+value+" | ");
          }
          $(this).children("div:first").children("span").remove();
          $(this).attr("status","Hold");
          
          var changed_status="Hold";
          $.post("status_update.php",{
            "id" : itemid,
            "status" : changed_status,
          },function(result){
                   
            var value = $("#"+eid).children(".btn-name").children(".countstatus").children("#hold").attr("set");
                    value=Number(value)+1;
                    $("#"+eid).children(".btn-name").children(".countstatus").children("#hold").attr("set",value);
                     $("#"+eid).children(".btn-name").children(".countstatus").children("#hold").text("H: "+value+" | ");
                    $("#messg").html(\'<div class="alert alert-success"><strong>Task on Hold!</strong></div>\').fadeIn(\'slow\');
                   setTimeout(function(){
                     $("#messg").html(\'<div class="alert alert-success"><strong>Task on Hold!</strong></div>\').fadeOut(\'slow\');
                    },1000);
                   

                 });

        }

      });
              }
            }
       

            });






             $("#sort2-'.$eid.'").sortable({
          connectWith: ".connect_'.$eid.'",
          helper: "clone",
          dropOnEmpty: true,
          start: function( event, ui ) {
          $( "#sort2-'.$eid.'" ).sortable( "refreshPositions" );
        },
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
            var value = $("#"+eid).children(".btn-name").children(".countstatus").children("#assign").attr("set");
                    value=Number(value)-1;
                    $("#"+eid).children(".btn-name").children(".countstatus").children("#assign").attr("set",value);
                     $("#"+eid).children(".btn-name").children(".countstatus").children("#assign").text("A: "+value+" | ");
          }
          else if(status=="Delivered"){
            $(this).children("div:first").removeClass("btn-info");
            $(this).children("div:first").addClass("btn-WIP");
            $(this).children("div:last").removeClass("alert-info");
            $(this).children("div:last").addClass("alert-WIP");
            $(this).children("div:first").css("position","relative");
            $(this).children("div:first").append("<span class=\'priority\'></span>");
            var value = $("#"+eid).children(".btn-name").children(".countstatus").children("#deli").attr("set");
                    value=Number(value)-1;
                    $("#"+eid).children(".btn-name").children(".countstatus").children("#deli").attr("set",value);
                     $("#"+eid).children(".btn-name").children(".countstatus").children("#deli").text("D: "+value+" ");
          }
          else if(status=="Hold"){
            $(this).children("div:first").removeClass("btn-hold");
            $(this).children("div:first").addClass("btn-WIP");
            $(this).children("div:last").removeClass("alert-hold");
            $(this).children("div:last").addClass("alert-WIP");
             $(this).children("div:first").css("position","relative");
            $(this).children("div:first").append("<span class=\'priority\'></span>");
            var value = $("#"+eid).children(".btn-name").children(".countstatus").children("#hold").attr("set");
                    value=Number(value)-1;
                    $("#"+eid).children(".btn-name").children(".countstatus").children("#hold").attr("set",value);
                     $("#"+eid).children(".btn-name").children(".countstatus").children("#hold").text("H: "+value+" | ");
          }
          $(this).attr("status","WIP");
          
         
          var changed_status="WIP";
          $.post("status_update.php",{
            "id" : itemid,
            "status" : changed_status,
          },function(result){
                    
                    var value = $("#"+eid).children(".btn-name").children(".countstatus").children("#wip").attr("set");
                    value=Number(value)+1;
                    $("#"+eid).children(".btn-name").children(".countstatus").children("#wip").attr("set",value);
                     $("#"+eid).children(".btn-name").children(".countstatus").children("#wip").text("W: "+value+" | ");
                    $("#messg").html(\'<div class="alert alert-success"><strong>Task in Progress!</strong></div>\').fadeIn(\'slow\');
                   setTimeout(function(){
                     $("#messg").html(\'<div class="alert alert-success"><strong>Task in Progress!</strong></div>\').fadeOut(\'slow\');
                    },1000);
                   

                 });

        }
      });

            }

          },
          update: function (event, ui) {
        var data = $(this).sortable(\'serialize\');
         $.ajax({
            data: data,
            type: \'POST\',
            url: \'update_priority_wip.php\',
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
        var data = $(this).sortable(\'serialize\');
         $.ajax({
            data: data,
            type: \'POST\',
            url: \'update_priority_wip.php\',
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
          $("#sort5-'.$eid.'").sortable({
          connectWith: ".connect_'.$eid.'",
          helper: "clone",
          dropOnEmpty: true,
          start: function( event, ui ) {
          $( "#sort5-'.$eid.'" ).sortable( "refreshPositions" );
        },
          receive: function(ev, ui) {
             var u;
            if($(ev.target).attr("eid")!=ui.item.attr("eid")||ui.item.attr("status")!="Delivered"){
              ui.sender.sortable("cancel");
            }
            else{
                var itemid = $(ev.originalEvent.toElement).parent().attr("itemid");
    var status =$(ev.originalEvent.toElement).parent().attr("status");
    var eid=$(ev.originalEvent.toElement).parent().attr("eid");
      
      $(".box-item").each(function() {
        if ($(this).attr("itemid") === itemid&&status=="Delivered") {
          
          $(this).children("div:first").removeClass("btn-info");
          $(this).children("div:first").addClass("btn-success");
          $(this).children("span").remove();
           $(this).children("div:first").append(\'<i  title="Click to Archive Task" onclick="del2(event,this)"style="vertical-align:middle;margin-right:-8px;float:right;z-index:99;" class="material-icons">archive</i>\');
           $(this).children("div:last").removeClass("alert-info");
           $(this).children("div:last").addClass("alert-success");
           u=$(this);
           //console.log( $(this).children("div:last").attr("class"));
           $(this).attr("status","Success");
           
           var changed_status="Success";
           $.post("status_update.php",{
            "id" : itemid,
            "status" : changed_status,
          },function(result){
                   data=JSON.parse(result);
                  var objDate = new Date(data.assigndate);
                   var objDate2 = new Date(data.completion_date);
                  var strDate = objDate.toLocaleString("en", { day: "numeric" }) + "-"+objDate.toLocaleString("en", { month: "short"  }) + "-" + objDate.toLocaleString("en", { year: "numeric"});
                 var strDate2 = objDate2.toLocaleString("en", { day: "numeric" }) + "-"+objDate2.toLocaleString("en", { month: "short"  }) + "-" + objDate2.toLocaleString("en", { year: "numeric"});
                    
            u.children("div:last").html("<p>Assignned Date: "+strDate+"</p><p>Compeletion Date: "+strDate2+"</p><p>Days to Compelete: "+(Number(date_diff_indays(data.assigndate,data.completion_date))+1)+"</p>");

            var value = $("#"+eid).children(".btn-name").children(".countstatus").children("#deli").attr("set");
                    value=Number(value)-1;
                    $("#"+eid).children(".btn-name").children(".countstatus").children("#deli").attr("set",value);
                     $("#"+eid).children(".btn-name").children(".countstatus").children("#deli").text("D: "+value+" ");
                    $("#messg").html(\'<div class="alert alert-success"><strong>Task Compeleted Successfully!</strong></div>\').fadeIn(\'slow\');
                   setTimeout(function(){
                     $("#messg").html(\'<div class="alert alert-success"><strong>Task Compeleted Successfully!</strong></div>\').fadeOut(\'slow\');
                    },1000);
                  

                 });

         }
       }); 
            }
          }
          });



              </script>';
              echo '</div></div>';
            }
          }

          ?>

        </div>




      </div>



 <div id="messg">
            <div class="alert alert-success">
              <strong></strong> 
            </div>
    </div>
  <div id="myModal1" class="modal fade" role="dialog">
      <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            <div class="alert alert-danger">
              <strong>Fill the Project Details!</strong> 
            </div>
          </div>
        </div>

      </div>
    </div>
    <div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"></h4>
      </div>
      <div class="modal-body">
        <p>Some text in the modal.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>
    
    <div id="del" class="modal fade" role="dialog">
      <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header">
          </div>
          <div class="modal-body ">
            <div class="alert alert-danger" >
             <strong >Are you sure want to delete task ?</strong>
           </div>

         </div>
         <div class="modal-footer">
           <button type="button" class="btn btn-default" onclick="delfinal(this)" data-dismiss="modal">Yes</button>
           <button type="button" class="btn btn-default" onclick="cancel(this)" data-dismiss="modal">Close</button>
         </div>
       </div>

     </div>
   </div>
    </div>
   
  

 </body>
 </html>