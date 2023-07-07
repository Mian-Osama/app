<?php
// Connect to your database (replace the credentials with your own)
include 'db_connect.php';

// Get the tasks data from your database
$sql = "SELECT * FROM stgchart WHERE aircraft='" . $_REQUEST['aircraft'] . "' AND airbase='" . $_REQUEST['airbase'] . "' ORDER BY flying_hours asc";
if (isset($_REQUEST['tail_id'])) {
    $sql = "SELECT * FROM stgchart WHERE aircraft='" . $_REQUEST['aircraft'] . "' AND tail_id='" . $_REQUEST['tail_id'] . "' AND airbase='" . $_REQUEST['airbase'] . "' ORDER BY flying_hours asc";
}

$result = $conn->query($sql);

// Check if the query was successful
if (!$result) {
    // Prepare the error response
    $error = array(
        "error" => "Database query failed",
        "message" => $conn->error
    );

    // Set the Content-Type header to application/json
    header('Content-Type: application/json');

    // Encode the error array as JSON and print it
    echo json_encode($error);
    exit(); // Stop execution
}

// Format the tasks data as JSON
$tasks = array();

while ($row = $result->fetch_assoc()) {
    $flying_hours = floatval($row["flying_hours"]);
    $max_hours = intval($row["max_hours"]);

    if ($flying_hours > $max_hours) {
        $flying_hours = $max_hours - ($flying_hours - $max_hours);
    }

    $task = array(
        "id" => intval($row["id"]),
        "aircraft" => $row["aircraft"],
        "tail_id" => $row["tail_id"],
        "aircraftMod" => $row["aircraftMod"],
        "flying_hours" => $flying_hours,
        "details" => $row["details"],
        "max_hours" => $max_hours,
        "last_updated" => $row["last_updated"]
    );

    $tasks[] = $task;
}
// Sort the array based on flying_hours
usort($tasks, function ($a, $b) {
    return $a["flying_hours"] - $b["flying_hours"];
});
// Set the Content-Type header to application/json
header('Content-Type: application/json');

// Encode the tasks array as JSON and print it
echo json_encode($tasks);
?>
