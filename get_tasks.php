<?php
// Connect to your database (replace the credentials with your own)
include 'db_connect.php';

$projectId = isset($_REQUEST['project_id']) ? $_REQUEST['project_id'] : null;

// Get the tasks data from your database
$sql = "SELECT * FROM task_list WHERE project_id = '" . $projectId . "'";
$result = $conn->query($sql);

// Format the tasks data as JSON
$tasks = array();

while ($row = $result->fetch_assoc()) {
    $task = array(
        "id" => $row["id"],
        "name" => $row["task"],
        "start" => date('Y-m-d', strtotime($row["date_created"])),
        "end" => date('Y-m-d', strtotime($row["end_date"])),
        "duration" => intval($row["duration"]),
        "Percent" => floatval($row["logged_days"]),
        "dep" =>'None',
    );

    $tasks[] = $task;
}

// Set the Content-Type header to application/json
header('Content-Type: application/json');

// Encode the tasks array as JSON and print it
echo json_encode($tasks);

?>