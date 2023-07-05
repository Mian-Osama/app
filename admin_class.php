<?php
session_start();
ini_set('display_errors', 1);

Class Action {
	private $db;

	public function __construct() {
		ob_start();
   	include 'db_connect.php';
    
    $this->db = $conn;
	}
	function __destruct() {
	    $this->db->close();
	    ob_end_flush();
	}

	function login(){
		extract($_POST);
			$qry = $this->db->query("SELECT *,concat(firstname,' ',lastname) as name FROM users where email = '".$email."' and password = '".md5($password)."'  ");
		if($qry->num_rows > 0){
			foreach ($qry->fetch_array() as $key => $value) {
				if($key != 'password' && !is_numeric($key))
					$_SESSION['login_'.$key] = $value;
			}
				return 1;
		}else{
			return 2;
		}
	}
	function logout(){
		session_destroy();
		foreach ($_SESSION as $key => $value) {
			unset($_SESSION[$key]);
		}
		header("location:login.php");
	}
	function login2(){
		extract($_POST);
			$qry = $this->db->query("SELECT *,concat(lastname,', ',firstname,' ',middlename) as name FROM students where student_code = '".$student_code."' ");
		if($qry->num_rows > 0){
			foreach ($qry->fetch_array() as $key => $value) {
				if($key != 'password' && !is_numeric($key))
					$_SESSION['rs_'.$key] = $value;
			}
				return 1;
		}else{
			return 3;
		}
	}
	function save_user(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k => $v){
			if(!in_array($k, array('id','cpass','password')) && !is_numeric($k)){
				if(empty($data)){
					$data .= " $k='$v' ";
				}else{
					$data .= ", $k='$v' ";
				}
			}
		}
		if(!empty($password)){
					$data .= ", password=md5('$password') ";

		}
		$check = $this->db->query("SELECT * FROM users where email ='$email' ".(!empty($id) ? " and id != {$id} " : ''))->num_rows;
		if($check > 0){
			return 2;
			exit;
		}
		if(isset($_FILES['img']) && $_FILES['img']['tmp_name'] != ''){
			$fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['img']['name'];
			$move = move_uploaded_file($_FILES['img']['tmp_name'],'assets/uploads/'. $fname);
			$data .= ", avatar = '$fname' ";

		}
		if(empty($id)){
			$save = $this->db->query("INSERT INTO users set $data");
		}else{
			$save = $this->db->query("UPDATE users set $data where id = $id");
		}

		if($save){
			return 1;
		}else {
			return $this->db->error; // Return the query error message
		}
	}
	function signup(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k => $v){
			if(!in_array($k, array('id','cpass')) && !is_numeric($k)){
				if($k =='password'){
					if(empty($v))
						continue;
					$v = md5($v);

				}
				if(empty($data)){
					$data .= " $k='$v' ";
				}else{
					$data .= ", $k='$v' ";
				}
			}
		}

		$check = $this->db->query("SELECT * FROM users where email ='$email' ".(!empty($id) ? " and id != {$id} " : ''))->num_rows;
		if($check > 0){
			return 2;
			exit;
		}
		if(isset($_FILES['img']) && $_FILES['img']['tmp_name'] != ''){
			$fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['img']['name'];
			$move = move_uploaded_file($_FILES['img']['tmp_name'],'assets/uploads/'. $fname);
			$data .= ", avatar = '$fname' ";

		}
		if(empty($id)){
			$save = $this->db->query("INSERT INTO users set $data");

		}else{
			$save = $this->db->query("UPDATE users set $data where id = $id");
		}

		if($save){
			if(empty($id))
				$id = $this->db->insert_id;
			foreach ($_POST as $key => $value) {
				if(!in_array($key, array('id','cpass','password')) && !is_numeric($key))
					$_SESSION['login_'.$key] = $value;
			}
					$_SESSION['login_id'] = $id;
				if(isset($_FILES['img']) && !empty($_FILES['img']['tmp_name']))
					$_SESSION['login_avatar'] = $fname;
			return 1;
		}
	}

	function update_user(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k => $v){
			if(!in_array($k, array('id','cpass','table','password')) && !is_numeric($k)){
				if(empty($data)){
					$data .= " $k='$v' ";
				}else{
					$data .= ", $k='$v' ";
				}
			}
		}
		$check = $this->db->query("SELECT * FROM users where email ='$email' ".(!empty($id) ? " and id != {$id} " : ''))->num_rows;
		if($check > 0){
			return 2;
			exit;
		}
		if(isset($_FILES['img']) && $_FILES['img']['tmp_name'] != ''){
			$fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['img']['name'];
			$move = move_uploaded_file($_FILES['img']['tmp_name'],'assets/uploads/'. $fname);
			$data .= ", avatar = '$fname' ";

		}
		if(!empty($password))
			$data .= " ,password=md5('$password') ";
		if(empty($id)){
			$save = $this->db->query("INSERT INTO users set $data");
		}else{
			$save = $this->db->query("UPDATE users set $data where id = $id");
		}

		if($save){
			foreach ($_POST as $key => $value) {
				if($key != 'password' && !is_numeric($key))
					$_SESSION['login_'.$key] = $value;
			}
			if(isset($_FILES['img']) && !empty($_FILES['img']['tmp_name']))
					$_SESSION['login_avatar'] = $fname;
			return 1;
		}
	}
	function delete_user(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM users where id = ".$id);
		if($delete) {
			return 1;
		} else {
			return $this->db->error; // Return the query error message
		}
	}
	function save_system_settings(){
		extract($_POST);
		$data = '';
		foreach($_POST as $k => $v){
			if(!is_numeric($k)){
				if(empty($data)){
					$data .= " $k='$v' ";
				}else{
					$data .= ", $k='$v' ";
				}
			}
		}
		if($_FILES['cover']['tmp_name'] != ''){
			$fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['cover']['name'];
			$move = move_uploaded_file($_FILES['cover']['tmp_name'],'../assets/uploads/'. $fname);
			$data .= ", cover_img = '$fname' ";

		}
		$chk = $this->db->query("SELECT * FROM system_settings");
		if($chk->num_rows > 0){
			$save = $this->db->query("UPDATE system_settings set $data where id =".$chk->fetch_array()['id']);
		}else{
			$save = $this->db->query("INSERT INTO system_settings set $data");
		}
		if($save){
			foreach($_POST as $k => $v){
				if(!is_numeric($k)){
					$_SESSION['system'][$k] = $v;
				}
			}
			if($_FILES['cover']['tmp_name'] != ''){
				$_SESSION['system']['cover_img'] = $fname;
			}
			return 1;
		}
	}
	function save_image(){
		extract($_FILES['file']);
		if(!empty($tmp_name)){
			$fname = strtotime(date("Y-m-d H:i"))."_".(str_replace(" ","-",$name));
			$move = move_uploaded_file($tmp_name,'assets/uploads/'. $fname);
			$protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"],0,5))=='https'?'https':'http';
			$hostName = $_SERVER['HTTP_HOST'];
			$path =explode('/',$_SERVER['PHP_SELF']);
			$currentPath = '/'.$path[1]; 
			if($move){
				return $protocol.'://'.$hostName.$currentPath.'/assets/uploads/'.$fname;
			}
		}
	}
	function save_project(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k => $v){
			if(!in_array($k, array('id','user_ids')) && !is_numeric($k)){
				if($k == 'description')
					$v = htmlentities(str_replace("'","&#x2019;",$v));
				if(empty($data)){
					$data .= " $k='$v' ";
				}else{
					$data .= ", $k='$v' ";
				}
			}
		}
		if(isset($user_ids)){
			$data .= ", user_ids='".implode(',',$user_ids)."' ";
		}
		// echo $data;exit;
		if(empty($id)){
			$save = $this->db->query("INSERT INTO project_list set $data");
		}else{
			$save = $this->db->query("UPDATE project_list set $data where id = $id");
		}
		if($save){
			return 1;
		}
	}
	function delete_project(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM project_list where id = $id");
		if($delete){
			return 1;
		}
	}
	function save_task(){
		extract($_POST);
		$start_date = $_POST["start_date"];
		$end_date = $_POST["end_date"];
		$start = new DateTime($start_date);
		$end = new DateTime($end_date);

		// Calculate duration based on difference in days
		$duration = $end->diff($start)->days + 1;
		$_POST['duration'] = $duration;
		$_POST['date_created'] = $_POST["start_date"];
		unset($_POST['start_date']);
		$data = "";
		foreach($_POST as $k => $v){
			if(!in_array($k, array('id')) && !is_numeric($k)){
				if($k == 'description')
					$v = htmlentities(str_replace("'","&#x2019;",$v));

				if(empty($data)){
					$data .= " $k='$v' ";
				}else{
					$data .= ", $k='$v' ";
				}
			}
		}
		if(empty($id)){
			$save = $this->db->query("INSERT INTO task_list set $data");
		}else{
			$save = $this->db->query("UPDATE task_list set $data where id = $id");
		}
		if($save){
			return 1;
		}else {
			return $this->db->error;
		}
	}
	function delete_task(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM task_list where id = $id");
		if($delete){
			return 1;
		}
	}
	function save_progress(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k => $v){
			if(!in_array($k, array('id')) && !is_numeric($k)){
				if($k == 'comment')
					$v = htmlentities(str_replace("'","&#x2019;",$v));
				if(empty($data)){
					$data .= " $k='$v' ";
				}else{
					$data .= ", $k='$v' ";
				}
			}
		}
		$dur = abs(strtotime("2020-01-01 ".$end_time)) - abs(strtotime("2020-01-01 ".$start_time));
		$dur = $dur / (60 * 60);
		$data .= ", time_rendered='$dur' ";
		// echo "INSERT INTO user_productivity set $data"; exit;
		if(empty($id)){
			$data .= ", user_id={$_SESSION['login_id']} ";
			
			$save = $this->db->query("INSERT INTO user_productivity set $data");
		}else{
			$save = $this->db->query("UPDATE user_productivity set $data where id = $id");
		}
		if($save){
			$tid = $_POST['task_id'];
			$dur = $dur / 24;
			$save = $this->db->query("UPDATE task_list set logged_days = logged_days + $dur where id = $tid");
			if($save){
				return 1;
			}
			else
				return $this->db->error;
		}
	}
	function delete_progress(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM user_productivity where id = $id");
		if($delete){
			return 1;
		}
	}
	function get_report(){
		extract($_POST);
		$data = array();
		$get = $this->db->query("SELECT t.*,p.name as ticket_for FROM ticket_list t inner join pricing p on p.id = t.pricing_id where date(t.date_created) between '$date_from' and '$date_to' order by unix_timestamp(t.date_created) desc ");
		while($row= $get->fetch_assoc()){
			$row['date_created'] = date("M d, Y",strtotime($row['date_created']));
			$row['name'] = ucwords($row['name']);
			$row['adult_price'] = number_format($row['adult_price'],2);
			$row['child_price'] = number_format($row['child_price'],2);
			$row['amount'] = number_format($row['amount'],2);
			$data[]=$row;
		}
		return json_encode($data);

	}

	function save_base(){
		 extract($_POST);
		 $data = "";
		foreach($_POST as $k => $v){
			if(!in_array($k, array('id')) && !is_numeric($k)){
				if(empty($data)){
					$data .= " $k='$v' ";
				}else{
					$data .= ", $k='$v' ";
				}
			}
		}

		$check = $this->db->query("SELECT * FROM bases where name ='$name' ".(!empty($id) ? " and id != {$id} " : ''))->num_rows;
		if($check > 0){
			return 2;
			exit;
		}
		
		$save = $this->db->query("INSERT INTO bases set $data");
		if($save){
			return 1;
		}
		return $data;
	}

	function delete_base(){
		extract($_POST);
		$check = $this->db->query("DELETE FROM bases WHERE id = '$base_id'");
		if ($check) {
			return 1;
		} else {
			return $this->db->error();
		}
	}
	

	function add_aircraft_maint(){

		if ($_POST['req'] == 'delete') {
			// Handle delete request
			$project_name = $_POST['project_name'];
	
			// Perform delete operation based on the project name
			$sql_delete = "DELETE FROM project_tasks WHERE project_name = '$project_name'";
			$result_delete = $save = $this->db->query($sql_delete);
	
			if ($result_delete) {
				return 1;
			} else {
				echo "Error: " . mysqli_error($this->db);
				return 1;
			}
		} 
		
		$project_name = $_POST['aircraft']."_".$_POST['tail_number'];
		$startdate = $_POST['start_date'];
		$inspection_type = $_POST['inspection_type'];
		$airbase = $_POST['airbase'];

		if ($_POST['req'] == 'stg') {
			// Check if project name already exists
			$sql_check = "SELECT * FROM project_tasks WHERE project_name = '$project_name' AND phase_name = 'stg'";
			$result_check = $save = $this->db->query($sql_check);
			if ($result_check->num_rows > 0) {
				echo "Project name already exists!";
				return 0;
			}

			$enddate = $_POST['exp_date'];
			$details = $_POST['details'];
			$sql_insert = "INSERT INTO project_tasks (project_name, phase_name, task_name, trade, start_date, end_date, duration, completed_duration, inspectionType, details, airbase) VALUES ('$project_name', 'stg', '', '', '$startdate', '$enddate', '0', '0', '$inspection_type', '$details\n', '$airbase')";
			$result_check = $this->db->query($sql_insert);
			if(!$result_check) {
				echo "Error: " . mysqli_error($this->db);
				return 0;
			}
			return 1;
		}

			// Check if project name already exists
			$sql_check = "SELECT * FROM project_tasks WHERE project_name = '$project_name' AND phase_name != 'stg' AND airbase='$airbase'";
			$result_check = $save = $this->db->query($sql_check);
			if ($result_check->num_rows > 0) {
				echo "Project name already exists!";
				return 0;
			}
			$contents = file_get_contents('aircraft_maint_data.txt');
			eval($contents);
	
			if (isset($phase_arrays[$_POST['aircraft']][$_POST['hours']])) {
				$phases_tasks =  $phase_arrays[$_POST['aircraft']][$_POST['hours']];
			}
			else {
				echo "Aircraft Maintenance data does not exists for ".$_POST['aircraft']. "  Hours ".$_POST['hours'];
				return 0;
			}

			
			if($inspection_type == "scheduled")
			{
				$enddate = "";
				// Insert data into table
				foreach ($phases_tasks as $pt) {
					$phase = $pt[0];
					$task = $pt[1];
					$trade = $pt[2];
					$duration = ($pt[3]);
					$change = $pt[4];
					if($change == 1) {
						if($enddate != "")
							$startdate = $enddate;
					}
					if($duration != "") {
						$enddate = date('Y-m-d', strtotime($startdate . ' + ' . ($duration) . ' days'));
					}
				
					$sql_insert = "INSERT INTO project_tasks (project_name, phase_name, task_name, trade, start_date, end_date, duration, completed_duration, inspectionType, airbase) VALUES ('$project_name', '$phase', '$task', '$trade', '$startdate', '$enddate', '$duration', '0', '$inspection_type', '$airbase')";
					$result_check = $this->db->query($sql_insert);
					if(!$result_check) {
						echo "Error: " . mysqli_error($this->db);
						return 0;
					}
				}
				return 1;
			}
			else
			{
				$enddate = $_POST['exp_date'];
				$details = $_POST['details'];
				$sql_insert = "INSERT INTO project_tasks (project_name, phase_name, task_name, trade, start_date, end_date, duration, completed_duration, inspectionType, details, airbase) VALUES ('$project_name', '', '', '', '$startdate', '$enddate', '0', '0', '$inspection_type', '$details', '$airbase')";
				$result_check = $this->db->query($sql_insert);
				if(!$result_check) {
					echo "Error: " . mysqli_error($this->db);
					return 0;
				}
				return 1;
			}
	}


     //New Entities Enter for Sql Stagger Chart
	function add_aircraft_stgchart(){

		if ($_POST['req'] == 'delete') {
			// Handle delete request
			$id = $_POST['id'];
	
			// Perform delete operation based on the project name
			$sql_delete = "DELETE FROM stgchart WHERE id = '$id'";
			$result_delete = $save = $this->db->query($sql_delete);
	
			if ($result_delete) {
				return 1;
			} else {
				echo "Error: " . mysqli_error($this->db);
				return 1;
			}
		} 
		
		$aircraft = $_POST['aircraft'];
		$tail_id = $_POST['tail_id'];
		$flying_hours = $_POST['flying_hours'];
		$aircraftMod= $_POST['aircraftMod'];
		$details = $_POST['details'];
		$max_hours = $_POST['max_hours'];
		$airbase = $_POST['airbase'];

		// Check if project name already exists
		$sql_check = "SELECT * FROM stgchart WHERE aircraft = '$aircraft' and tail_id = '$tail_id'";
		$result_check = $save = $this->db->query($sql_check);
		
		if ($result_check->num_rows > 0) {
			echo "Aircraft name already exists!";
			return 0;
		} else {
			if ($_POST['req'] == 'stgchart') {
				$sql_insert = "INSERT INTO stgchart (aircraft, tail_id, flying_hours, aircraftMod, details, max_hours, airbase) VALUES ('$aircraft', '$tail_id', '$flying_hours','$aircraftMod', '$details', '$max_hours\n','$airbase')";
				$result_check = $this->db->query($sql_insert);
				if(!$result_check) {
					echo "Error: " . mysqli_error($this->db);
					return 0;
				}
				return 1;
			}
		}
	}
}