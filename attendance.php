<?php
// Include database connection
include 'db_connect.php';

// Check if form is submitted
if (isset($_POST['submit'])) {
    if (empty($_POST['user_id']) || empty($_POST['date']) || empty($_POST['status']) || !in_array($_POST['status'], array('present', 'absent', 'leave', 'late'))){
        echo '<p style="color:red;">Error: All fields are required.</p>';
    } else {
        // Retrieve form data
        $user_id = $_POST['user_id'];
        $date = $_POST['date'];
        $in_time = $_POST['in_time'] ?? null;
        $out_time = $_POST['out_time'] ?? null;
        $status = $_POST['status'];
        $leaves_query = "SELECT * FROM users WHERE id = '$user_id'";
        $leaves_result = mysqli_query($conn, $leaves_query);
        if (!$leaves_result) {
            echo '<p style="color:red;">Error: ' . mysqli_error($conn) . '</p>';
        } else {
            $leaves_row = mysqli_fetch_assoc($leaves_result);
            $leaves_left = $leaves_row['leaves'];
            $name = $leaves_row['firstname']. ' '. $leaves_row['lastname'];
            if ($status === 'leave') {
                // Check if the user has enough leaves
                if ($leaves_left > 0) {
                    // Insert the attendance record into the database
                    $insert_attendance = $conn->query("INSERT INTO attendance (user_id, name, date, in_time, out_time, status) VALUES ('$user_id', '$name', '$date', '$in_time', '$out_time', '$status')");
                    if (!$insert_attendance) {
                        echo '<p style="color:red;">Error: ' . mysqli_error($conn) . '</p>';
                    } else {
                        // Decrement the number of leaves left for the user
                        $new_leaves_left = $leaves_left - 1;
                        $update_leaves = $conn->query("UPDATE users SET leaves = '$new_leaves_left' WHERE id = '$user_id'");
                        if (!$update_leaves) {
                            echo '<p style="color:red;">Error: ' . mysqli_error($conn) . '</p>';
                        } else {
                            // Redirect to attendance list page
                            echo "<script>location.replace('index.php?page=attendance_list');</script>";
                            exit();
                        }
                    }
                } else {
                    // Show error message
                    echo '<p style="color:red;">Error: User does not have enough leaves.</p>';
                }
            } else {
                // Insert the attendance record into the database
                $insert_attendance = $conn->query("INSERT INTO attendance (user_id, name, date, in_time, out_time, status) VALUES ('$user_id', '$name', '$date', '$in_time', '$out_time', '$status')");
                if (!$insert_attendance) {
                    echo '<p style="color:red;">Error: ' . mysqli_error($conn) . '</p>';
                } else {
                    // Redirect to attendance list page
                    echo "<script>location.replace('index.php?page=attendance_list');</script>";
                    exit();
                }
            }
        }
    }
}

// Retrieve list of users
$users = $conn->query("SELECT * FROM users");

?>

<!DOCTYPE html>
<html>
<head>
    <title>Attendance</title>
</head>
<body>
    <div class="card">
        <div class="card-header">
        <h3 class="card-title" style="font-weight: bold; font-size: 24px;">Record Attendance</h3>
        </div>
        <div class="card-body">
            <form method="POST">
                <div class="form-group">
                    <label for="user_id">User:</label>
                    <select name="user_id" id="user_id" class="form-control">
                        <?php 
                        $users = $conn->query("SELECT * FROM users");
                        while($row = $users->fetch_assoc()): ?>
                            <option value="<?php echo $row['id'] ?>"><?php echo $row['firstname'] . ' ' . $row['lastname'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="date">Date:</label>
                    <input type="date" name="date" id="date" class="form-control">
                </div>

                <div class="form-group">
                    <label for="status">Status:</label>
                    <select name="status" id="status" class="form-control">
                        <option value="" selected disabled>Select attendance status</option>
                        <option value="present">Present</option>
                        <option value="absent">Absent</option>
                        <option value="leave">Leave</option>
                        <option value="late">Late</option>
                    </select>
                </div>

                <div class="form-group" id="in-out-time" style="display: none;">
                    <div class="row">
                        <div class="col-md-6">
                            <label for="in_time">In Time:</label>
                            <input type="time" name="in_time" id="in_time" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label for="out_time">Out Time:</label>
                            <input type="time" name="out_time" id="out_time" class="form-control">
                        </div>
                    </div>
                </div>

                    <button type="submit" name="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>

<script>
    const statusSelect = document.getElementById('status');
    const inOutTimeDiv = document.getElementById('in-out-time');

    statusSelect.addEventListener('change', () => {
        if (statusSelect.value === 'present') {
            inOutTimeDiv.style.display = 'block';
        } else {
            inOutTimeDiv.style.display = 'none';
        }
    });
</script>