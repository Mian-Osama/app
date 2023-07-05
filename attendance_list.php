<!DOCTYPE html>
<html>
<head>
    <title>Attendance List</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <div class="card" id="list">
            <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="text-center" style="font-weight: bold; font-size: 24px;">Attendance List</h1>
                <button class="btn btn-flat btn-primary" onclick="printCard()"><i class="fa fa-print"></i>Print</button>
            </div>
            </div>
            <div class="card-body">
                <table class="table table-hover">
                <input type="text" id="search-input" class="form-control mb-3" placeholder="Search">
                 <table class="table table-hover table-condensed" id="list">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            // Include database connection code
                            include 'db_connect.php';

                            // Query to fetch attendance records from the database
                            $query = "SELECT * FROM attendance";
                            $result = mysqli_query($conn, $query);

                            // Loop through each row in the result set and display the data in a table
                            while($row = mysqli_fetch_assoc($result)) {
                                echo "<tr>";
                                echo "<td>".$row['name']."</td>";
                                echo "<td>".$row['date']."</td>";
                                echo "<td>".$row['status']."</td>";
                                echo "</tr>";
                            }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
<script>
    function printCard() {
        var printContents = document.getElementById("list").outerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
    }
     // Filter table rows based on search term
     $("#search-input").on("keyup", function() {
        var searchTerm = $(this).val().toLowerCase();
        $("#list tbody tr").each(function() {
            var $row = $(this);
            var rowData = $row.text().toLowerCase();
            if (rowData.indexOf(searchTerm) === -1) {
                $row.hide();
            } else {
                $row.show();
            }
        });
    });
    </script>