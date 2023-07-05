<?php 
include 'db_connect.php';
if(isset($_GET['id'])){
	$qry = $conn->query("SELECT * FROM task_list where id = ".$_GET['id'])->fetch_array();
	foreach($qry as $k => $v){
		$$k = $v;
	}
}
?>
<div class="container-fluid">
	<dl>
		<dt><b class="border-bottom border-primary">Task</b></dt>
		<dd><?php echo ucwords($task) ?></dd>
	</dl>
	<dl>
		<dt><b class="border-bottom border-primary">Status</b></dt>
		<dd>
			<?php 
        	if($status == 1){
		  		echo "<span class='badge badge-secondary'>Pending</span>";
        	}elseif($status == 2){
		  		echo "<span class='badge badge-primary'>On-Progress</span>";
        	}elseif($status == 3){
		  		echo "<span class='badge badge-success'>Done</span>";
        	}
        	?>
		</dd>
	</dl>
	<dl>
		<dt><b class="border-bottom border-primary">Description</b></dt>
		<dd><?php echo html_entity_decode($description) ?></dd>
	</dl>
	<dl>
		<dt><b class="border-bottom border-primary">Date Created</b></dt>
		<dd><?php echo date('F d, Y', strtotime($date_created)) ?></dd>
	</dl>
	<dl>
		<dt><b class="border-bottom border-primary">Duration</b></dt>
		<dd><?php echo $duration ?> day(s)</dd>
	</dl>
	<dl>
		<dt><b class="border-bottom border-primary">End Date</b></dt>
		<dd><?php echo date('F d, Y', strtotime($end_date)) ?></dd>
	</dl>
	<dl>
		<dt><b class="border-bottom border-primary">Logged Days</b></dt>
		<dd><?php echo $logged_days ?> day(s)</dd>
	</dl>

	<div class="progress">
    <div class="progress-bar" role="progressbar" style="width: <?php echo ($logged_days/$duration)*100 ?>%;" aria-valuenow="<?php echo $logged_days ?>" aria-valuemin="0" aria-valuemax="<?php echo $duration ?>"><?php echo $logged_days.'/'.$duration ?> days</div>
</div>
</div>