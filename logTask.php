<!DOCTYPE html>
<html>
<head>
<?php
include 'db_connect.php'; ?>
    <title>Add Task Form</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body>

<div class="container mt-5">
    <h2>Log Task Form</h2>
    <form action="log_day.php" method="post">
        <div class="form-group">
            <label for="task_id">Select Task:</label>
            <select class="form-control" id="task_id" name="task_id">
                <?php
                // Get all tasks for the user with ID "root"
                $sql = "SELECT * FROM tasks WHERE user_id = '0'";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='" . $row["id"] . "'>" . $row["task_name"] . "</option>";
                    }
                }
                ?>
            </select>
        </div>
        <input type="hidden" id="user_id" name="user_id" value="1">
        <button type="submit" class="btn btn-primary">Log Task for Today</button>
    </form>
</div>

</body>
</html>

