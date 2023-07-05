<?php
// load database connection
include 'db_connect.php';

    $where = "";

    $qry = "SELECT DISTINCT SUBSTRING_INDEX(project_name, '_', 1) AS project_name FROM project_tasks where airbase='" . $_REQUEST['airbase'] . "' AND phase_name <> 'stg'";

    if(isset($_REQUEST['aircraft_id'])){
        $aircraft = $_REQUEST['aircraft_id'];
        $where = "WHERE project_name LIKE '{$aircraft}_%' AND airbase='" . $_REQUEST['airbase'] . "' AND phase_name <> 'stg'";
        $qry ="SELECT DISTINCT project_name AS project_name FROM project_tasks ".$where;
       
    }

    if(isset($_REQUEST['project_name'])){
        $where = "WHERE project_name = '{$_REQUEST['project_name']}' AND airbase='" . $_REQUEST['airbase'] . "' AND phase_name <> 'stg'";
        $qry = "SELECT DISTINCT phase_name FROM project_tasks ".$where." AND phase_name <> 'stg'";       
    }

    if(isset($_REQUEST['project_name']) && isset($_REQUEST['phase_name'])){
        $where = "WHERE project_name = '{$_REQUEST['project_name']}' AND phase_name = '{$_REQUEST['phase_name']}'";
        $qry ="SELECT DISTINCT task_name FROM project_tasks ".$where;
       
    }

    if(isset($_REQUEST['project_name']) && isset($_REQUEST['phase_name']) && isset($_REQUEST['task_name'])){
        $where = "WHERE project_name = '{$_REQUEST['project_name']}' AND phase_name = '{$_REQUEST['phase_name']}' AND task_name = '{$_REQUEST['task_name']}'";
        $qry ="SELECT * FROM project_tasks ".$where;       
    }

    if(isset($_REQUEST['status'])){
        $where = "WHERE project_name = '{$_REQUEST['project_name']}' ";
        $qry = "UPDATE project_tasks SET status = '{$_REQUEST['status']}', details = CONCAT(details, '" . $_REQUEST['details'] . "')" . $where;
        // fetch data from database
        $qry = $conn->query($qry);
        exit;
    }

    if(isset($_REQUEST['project_name']) && isset($_REQUEST['phase_name']) && isset($_REQUEST['task_name']) && isset($_REQUEST['duration']) && !isset($_REQUEST['status'])){
        $where = "WHERE project_name = '{$_REQUEST['project_name']}' AND phase_name = '{$_REQUEST['phase_name']}' AND task_name = '{$_REQUEST['task_name']}'";
        $qry = "UPDATE project_tasks SET completed_duration = completed_duration + 1, details = '" . $_REQUEST['details'] . "' " . $where;
        
        // fetch data from database
        $qry = $conn->query($qry);
        exit;
    }

    // fetch data from database
    $qry = $conn->query($qry);

    // initialize array to store data
    $data = array();

    // loop through each row and add to array
    while ($row = $qry->fetch_assoc()) {
        $data[] = $row;
    }

    // output data as JSON
    echo json_encode($data);

?>