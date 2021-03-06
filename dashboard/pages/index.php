<?php
	ob_start();
	session_start();
	require_once '../../db/dbconn.php';
	
	if( !isset($_SESSION['user']) ) {
		header("Location:../../login.php");
		exit;
	}
	// select loggedin users detail
	else
	{
	$res=mysql_query("SELECT * FROM clients WHERE cname= '".$_SESSION['user']."'");

	$userRow=mysql_fetch_array($res);
	}
	
	$cid = $userRow["cid"];
	
	$query = "SELECT * FROM user_drink WHERE user_id ='".$cid."'";
	$records = mysql_query($query);
	$count = mysql_num_rows($records); 
	$record = mysql_fetch_array($records);
	//$targetquery = "SELECT * FROM user_drink WHERE user_id ='".$cid."'";
	
	
	$chart_query = mysql_query("SELECT * FROM user_drink");
	
	
	
	
	
	
	if(isset($_POST['btn-add'])) {
	  
	 $targetname = $_POST['tname'];
	 $target_money = $_POST['budget'];
	 $current_money = 0;
	 $end_date = $_POST['enddate'];
	 $dt = date("Y-m-d");
	 $tomorrow  = date("Y-m-d", strtotime($dt." + ". $end_date." day"));
	 $query = "INSERT INTO target(target_name,cid,target_money,current_money,start_date,end_date,status) VALUES
	 ('$targetname','$cid','$target_money','$current_money',now(),'$tomorrow',0)";
	 $insert = mysql_query($query);
  
  if ($insert) {
	   $errTyp = "success";
	   $errMSG = "successfully add record !";   
	  } else {
	   $errTyp = "danger";
	   $errMSG = "Something went wrong, try again later...". $cid; 
	  } 
	   
	
  
  if ($insert) {
	   $errTyp = "success";
	   $errMSG = "successfully add record !";   
	  } else {
	   $errTyp = "danger";
	   $errMSG = "Something went wrong, try again later...". $qty; 
	  } 
	   
	 
}
	
?>


<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Welcome</title>

		
    <!-- Bootstrap Core CSS -->
    <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- MetisMenu CSS -->
    <link href="../vendor/metisMenu/metisMenu.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="../dist/css/sb-admin-2.css" rel="stylesheet">

    <!-- Morris Charts CSS -->
    <link href="../vendor/morrisjs/morris.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="../vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
	
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js"></script>
	

</head>

<body>


<div id="wrapper">

         <!-- Navigation -->
        <!-- Navigation -->
      
            <nav class="navbar navbar-inverse">
                <?php
					include 'nav.php';
				?>
        </nav>
            
            <!-- /.navbar-top-links -->
	  
            <div class="navbar-default sidebar" role="navigation">
                <div class="sidebar-nav navbar-collapse">
                    <?php include "sidebar.php"; ?>
                        
                </div>
                <!-- /.sidebar-collapse -->
            </div>
            <!-- /.navbar-static-side -->
      


        <div id="page-wrapper">
		
			<div class="row">
                <div class="col-lg-6">
							
							<div class="panel panel-default">
								<div class="panel-heading">
									<i class="fa fa-list-alt fa-fw"></i> 
								 
									</div>
									<!-- /.panel-heading -->
									<div class="panel-body">
									
									<?php
										$overall_query = mysql_query("SELECT count(*) As times, sum(cost)as scost, avg(std_drink) as avgs FROM `user_drink` WHERE user_id='$cid'");
										$overall_res = mysql_fetch_array($overall_query);
										$drink_times = $overall_res["times"];
										$drink_cost = $overall_res["scost"];
										$drink_avg = Round($overall_res["avgs"],1);
										?>
									
									
									<br/>
									<ul class = "list-group">
									   <li class="well"> <b>Recording Since: <?php echo $userRow["reg_date"];  ?></b></li>
									   <li  class="well"><b>Number of Drinking times: <?php echo $drink_times;  ?></b></li>
									   <li  class="well"><b>Total Expenditure: <?php echo $drink_cost;  ?></b></li>									 
									   <li  class="well"><b>Average standard drinks per time: <?php echo $drink_avg;  ?></b></li>
								   </ul>
								</div>
							<!-- /.panel-body -->
							</div>
						<!-- /.panel -->
						</div>
						
						
						<div class="col-lg-6">
							
							<div class="panel panel-default">
								<div class="panel-heading">
									<i class="fa fa-dollar fa-fw"></i> Weekly Spending
								 
									</div>
									<!-- /.panel-heading -->
									<div class="panel-body">
									<div id = "cost-barchart"></div>
								</div>
							<!-- /.panel-body -->
							</div>
						<!-- /.panel -->
						</div>
			</div>
		
		
		
            <div class="row">
				
					
						<div class="col-lg-6">
							
							<div class="panel panel-default">
								<div class="panel-heading">
									<i class="fa fa-bar-chart-o fa-fw"></i> Standard Drink Trend (<a href="records.php">more details</a>)
								 
									</div>
									<!-- /.panel-heading -->
									<div class="panel-body">
									   <div id="morris-line-chart"></div>
								   
								</div>
							<!-- /.panel-body -->
							</div>
						<!-- /.panel -->
						</div>
						
						
						
						<div class="col-lg-6">
								<div class="panel panel-default">
								<div class="panel-heading">
									<i class="fa fa-bar-chart-o fa-fw"></i> Expenditure Trend (<a href="records.php">more details</a>)
								 
									</div>
									<!-- /.panel-heading -->
									<div class="panel-body">
									   <div id="morris-cost-chart"></div>
								   
								</div>
							<!-- /.panel-body -->
							</div>
						</div>
						
					
		    </div>
        
			
			
			
						
            <!-- /.row -->
			
		
        <div class="row">
            <div class="panel-body">						
                           <!---chart place----->
				<?php	
					$rt = mysql_query("SELECT DATE(`recorded_time`) AS drink_date,ROUND(SUM(`std_drink`),1) AS Stand_drink FROM user_drink WHERE `user_id` = '$cid' GROUP BY DATE(`recorded_time`) ORDER BY user_drink_id DESC LIMIT 20") ;
					$data_array = array();
					while($rowx = mysql_fetch_assoc($rt)) {
						$data_array[] = $rowx;								
					}
					//echo json_encode($data_array);
					
					
					$cost_chart_query = mysql_query("SELECT DATE(`recorded_time`) AS drink_date,ROUND(SUM(`cost`),1) AS cost_money FROM user_drink WHERE `user_id` = '$cid' GROUP BY DATE(`recorded_time`) ORDER BY user_drink_id DESC LIMIT 20") ;
					$cost_array = array();
					while($rowcost =  mysql_fetch_assoc($cost_chart_query)) {
						$cost_array[] = $rowcost;								
					}
					//echo jsson_encode ($cost_array);
					
					
					$total_cost = mysql_query("SELECT Week(`recorded_time`) AS Week, sum(`cost`) AS Cost from user_drink WHERE `user_id` = '$cid' GROUP BY Week(`recorded_time`)");
					$total_cost_array = array();
					while($weeklycost =  mysql_fetch_assoc($total_cost)) {
						$total_cost_array[] = $weeklycost;								
					}
					
				?>
				<script> 
					Morris.Line({
						// ID of the element in which to draw the chart.
						element: 'morris-line-chart',
						// Chart data records -- each entry in this array corresponds to a point
						// on the chart.
						data: <?php echo json_encode($data_array);?>,
								 
						// The name of the data record attribute that contains x-values.
						xkey: 'drink_date',
								 
						// A list of names of data record attributes that contain y-values.
						ykeys: ['Stand_drink'],
								 
						// Labels for the ykeys -- will be displayed when you hover over the
						// chart.
						labels: ['Stand drink'],
						lineColors: ['#0b62a4'],
						xLabels: 'day',
						goals: [2.0,],
						goalLineColors: ['red'],
						goalLabels:['Health standard drink per day'],
						// Disables line smoothing
						smooth: true,
						resize: true
						});
				</script>
				
				
				
	<!--cost---->
			<script> 
					Morris.Line({
						// ID of the element in which to draw the chart.
						element: 'morris-cost-chart',
						// Chart data records -- each entry in this array corresponds to a point
						// on the chart.
						data: <?php echo json_encode($cost_array);?>,
								 
						// The name of the data record attribute that contains x-values.
						xkey: 'drink_date',
								 
						// A list of names of data record attributes that contain y-values.
						ykeys: ['cost_money'],
								 
						// Labels for the ykeys -- will be displayed when you hover over the
						// chart.
						labels: ['cost $'],
						lineColors: ['#0B3B0B'],
						xLabels: 'day',
						
						// Disables line smoothing
						smooth: true,
						resize: true
						});
						
						
						
		<!--weeky cost bar chart->
			Morris.Bar({
			 element: 'cost-barchart',
			 data:<?php echo json_encode($total_cost_array);?>,
			 xkey: 'Week',
			 ykeys: ['Cost'],
			 barColors: ["#669900", "#B29215"],
			 labels: ['Cost:$'],
			 
			 smooth: true,
			 resize: true
			});
						
				</script>
				
				
					
				
			</div>	 
            <!-- /.row -->
		</div>
        <!-- /#page-wrapper -->
	</div>
	
    </div>
    <!-- /#wrapper -->

    <!-- jQuery -->
    <script src="../vendor/jquery/jquery.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="../vendor/bootstrap/js/bootstrap.min.js"></script>

    <!-- Metis Menu Plugin JavaScript -->
    <script src="../vendor/metisMenu/metisMenu.min.js"></script>

    
    <!-- Custom Theme JavaScript -->
    <script src="../dist/js/sb-admin-2.js"></script>

</body>

</html>
