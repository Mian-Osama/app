<?php
// Include database connection script
require_once 'db_connect.php';

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get input values from form
    $taskName = $_POST['task-name'];
    $startDate = $_POST['start-date'];
    $endDate = $_POST['end-date'];
    $progress = $_POST['progress'];

    // Prepare insert statement
    $stmt = $conn->prepare("INSERT INTO task_list (task_name, start_date, end_date, progress) VALUES (:task_name, :start_date, :end_date, :progress)");

    // Bind parameters
    $stmt->bindParam(':task_name', $taskName);
    $stmt->bindParam(':start_date', $startDate);
    $stmt->bindParam(':end_date', $endDate);
    $stmt->bindParam(':progress', $progress);

    // Execute statement
    $stmt->execute();

    // Redirect back to main page
    header('Location: index.php');
    exit();
}