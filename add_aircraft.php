<?php
    // Include database connection script
    require_once 'db_connect.php';

    // Read the log file into a string
    $log_file = 'aircraft_maint_data.txt';
    $log_data = file_get_contents($log_file);

    // Parse the data from the log file
    eval($log_data);

    // Initialize the arrays for the aircraft and their corresponding hours
    $aircraft_array = array_keys($phase_arrays);
    $hours_array = array_unique(array_reduce($phase_arrays, function($result, $value) {
        return array_merge($result, array_keys($value));
    }, array()));

    // Sort the aircraft and hours arrays
    sort($aircraft_array);
    sort($hours_array);

    // Generate the HTML for the aircraft select options
    $aircraft_options = implode('', array_map(function($aircraft_name) {
        return '<option value="' . $aircraft_name . '">' . $aircraft_name . '</option>';
    }, $aircraft_array));

    $aircraft_hours = array();

    // Extract array subscript names
    foreach ($phase_arrays as $aircraft => $hours_array) {
        $hours = array_keys($hours_array);
        $aircraft_hours[$aircraft] = $hours;
    }
    
    // Convert to JSON array
    $json_array = json_encode($aircraft_hours);
?>


<div class="col-md-8">
	<div class="card card-outline card-primary">
		<div class="card-body">
        <div class="card-header" style="font-weight: bold; font-size: 20px;">
                Registration Maintenance Aircrafts
            </div>
			<form action="" id="addAircraft">
            <div class="form-group row mb-3">
            <label for="aircraft" class="col-sm-2 col-form-label">Aircraft:</label>
                <div class="col-sm-10">
                    <select id="aircraft" name="aircraft" class="form-control" required>
                        <option value="">-- Select an aircraft --</option>
                        <?php echo $aircraft_options; ?>
                    </select>
                </div>
            </div>


                <div class="form-group row mb-3">
                    <label for="tail_number" class="col-sm-2 col-form-label">Tail Number:</label>
                    <div class="col-sm-10">
                    <input type="text" id="tail_number" name="tail_number" class="form-control" required>
                    </div>
                </div>

                <div class="form-group row mb-3">
                    <label for="inspection_type" class="col-sm-2 col-form-label">Inspection Type:</label>
                    <div class="col-sm-10">
                    <select id="inspection_type" name="inspection_type" class="form-control" required>
                        <option value="">-- Select an inspection type --</option>
                        <option value="scheduled">Scheduled</option>
                        <option value="unscheduled">Unscheduled</option>
                    </select>
                    </div>
                </div>

                <div id="scheduled_inspection" style="display:none;">
                    <div class="form-group row mb-3">
                    <label for="hours" class="col-sm-2 col-form-label">Number of Hours:</label>
                    <div class="col-sm-10">
                        <select id="hours" name="hours" class="form-control">
                        <option value="">-- Select number of hours --</option>
                        </select>
                    </div>
                    </div>
                    <div class="form-group row mb-3">
                        <label for="start_date" class="col-sm-2 col-form-label">Start Date:</label>
                        <div class="col-sm-10">
                            <input type="date" id="start_date" name="start_date" class="form-control form-control-sm" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                    </div>
                </div>

                <div id="unscheduled_inspection" style="display:none;">
                <div class="form-group row mb-3">
                    <label for="details" class="col-sm-2 col-form-label">Details:</label>
                    <div class="col-sm-10">
                        <textarea id="details" name="details" class="form-control"></textarea>
                    </div>
                </div>
                <div class="form-group row mb-3">
                    <label for="exp_date" class="col-sm-2 col-form-label">Expected Completion Date:</label>
                    <div class="col-sm-10">
                        <input type="date" id="exp_date" name="exp_date" class="form-control form-control-sm" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                </div>
            </div>

                <div class="form-group row">
                    <div class="col-sm-10 offset-sm-2">
                    <button class="btn btn-primary" form="addAircraft">Submit</button>
                    </div>
                </div>
			</form>
		</div>
	</div>
</div>

<?php
    // fetch data from database
    $qry = $conn->query("SELECT phase_name, project_name, details, inspectionType, MAX(end_date) AS last_end_date FROM project_tasks WHERE phase_name != 'stg' AND airbase = '{$_SESSION['login_airbase']}' GROUP BY project_name");

    // initialize array to store data
    $data = array();

    // loop through each row and calculate required fields
    while ($row = $qry->fetch_assoc()) {
        
        $project_name = $row['project_name'];
        $details = $row['details'];
        $last_end_date = $row['last_end_date'];
        $inspectionType = $row['inspectionType'];
        $start_date = '';
        $flyingdate = '';
        $delays = '';

        // fetch the start date for the project
        $result = $conn->query("SELECT start_date FROM project_tasks WHERE project_name = '$project_name' ORDER BY start_date ASC LIMIT 1");
        if ($result->num_rows > 0) {
            $start_date = $result->fetch_assoc()['start_date'];
        }

        // calculate duration by subtracting start date from last end date
        $start = new DateTime($start_date);
        $end = new DateTime($last_end_date);
        $duration = $end->diff($start)->format('%a');
        $status = 0;
        // calculate percentage of duration and completed duration
        $result = $conn->query("SELECT status, inspectionType, SUM(duration) as total_duration, SUM(completed_duration) AS total_completed_duration, flydate FROM project_tasks WHERE project_name = '$project_name'");
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $total_duration = $row['total_duration'];
            if($row['inspectionType'] === 'unscheduled')
            {
                $status = $row['status'];
            }
            else
            {
                if ($row['total_completed_duration'] === null ) {
                    $status = 0;
                } else {
                    if($total_duration > 0)
                    {
                        $status = round(($row['total_completed_duration'] / $row['total_duration']) * 100);
                    
                        if($status >= 100)
                            $status = 100;

                    }
                }
            } 
        }
        // flying date entering;
        if(($status == 100)){
            $flyingdate = $row['flydate'];
            $flyingtime = time();
            $task_end_date = strtotime($last_end_date);
            $delays1 = $flyingtime - $task_end_date;
            $days_diff = $delays1 / (60 * 60 * 24);
            $delays = (int)floor($days_diff);
        }

        // split project name by underscore to get aircraft name and tail id
        $name_parts = explode('_', $project_name);
        $aircraft_name = $name_parts[0];
        $tail_id = $name_parts[1];

        // add data to array
        $data[] = array(
            'aircraft_name' => $aircraft_name,
            'tail_id' => $tail_id,
            'start_date' => $start_date,
            'completion_date' => $last_end_date,
            'duration' => $duration,
            'status' => $status,
            'flydate' => $flyingdate,
            'delays' => $delays,
            'inspectionType' => $inspectionType,
            'details' => $details,
        );
    }

    // output data as HTML table
?>


<div class="card card-outline card-success">
    <div class="card-body">
        <div class="card-header" style="font-weight: bold; font-size: 20px;">
            <div class="d-flex justify-content-between align-items-center">
                <div>Maintenance Aircrafts List</div>
                <button class="btn btn-flat btn-primary" onclick="printCard()"><i class="fa fa-print"></i>Print</button>
                <button class="btn btn-flat btn-primary" onclick="exportToExcel()"><i class="fa fa-file-excel"></i>Export to Excel</button>
            </div>
        </div>
            <div class="card-body">
                <input type="text" id="search-input" class="form-control mb-3" placeholder="Search">
                <table class="table table-hover table-condensed" id="list">
                    <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th class="text-center">Aircraft Name</th>
                            <th class="text-center">Tail ID</th>
                            <th class="text-center">Inspection Type</th>
                            <th>Date Started</th>
                            <th>Expected Completion Date</th>
                            <th>Duration (Days)</th>
                            <th>Status</th>
                            <th>Details</th>
                            <th>Aircraft Ready date (Actual)</th>
                            <th>Delays (Days)</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data as $i => $row) { ?>
                            <tr >
                                <td class="text-center"><?php echo $i + 1 ?></td>
                                <td class="text-center"><?php echo $row['aircraft_name']; ?></td>
                                <td class="text-center"><?php echo $row['tail_id']; ?></td>
                                <td class="text-center"><?php echo $row['inspectionType']; ?></td>
                                <td><?php echo $row['start_date'] ?></td>
                                <td><?php echo $row['completion_date'] ?></td>
                                <td><?php echo $row['duration'] ?></td>
                                <td><?php echo $row['status'] ?>%</td>
                                <td>
                                <button class="btn btn-link details-toggle" data-toggle="collapse" data-target="#details-row-<?php echo $i + 1 ?>">Hide Details</button>
                                <div id="details-row-<?php echo $i + 1 ?>" class="collapse show">
                                    <?php echo $row['details'] ?>
                                </div>
                                </td>
                                <td><?php echo $row['flydate'] ?></td>
                            <td>
                                <?php
                                $delays = $row['delays'];
                                if ($delays == 0) {
                                    echo '<span style="color: yellow;text-align: center; display: block;">No delays</span>';
                                } elseif ($delays > 0) {
                                    echo '<span style="color: red;text-align: center; display: block;">' . $delays . '<br>Days Late</span>';
                                } elseif (($delays < 0)){
                                    $newdelays = abs((int)$delays);
                                    if ($newdelays != 0)
                                    echo '<span style="color: green;text-align: center; display: block;">'. $newdelays .'<br>Days before time</span>';
                                }
                                ?>
                            </td>
                                <td>
                                    <button class="btn btn-danger btn-sm delete-btn" data-row='<?php echo json_encode($row); ?>'>
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
    </div>
</div>


<script>

function exportToExcel() {
    var table = document.getElementById('list');
    var csvString = '';
    for (var i = 0; i < table.rows.length; i++) {
      var rowData = table.rows[i].cells;
      for (var j = 0; j < rowData.length; j++) {
        csvString += rowData[j].innerText + ",";
      }
      csvString = csvString.substring(0, csvString.length - 1);
      csvString += '\n';
    }
    var filename = 'maint_data.csv';
    var link = document.createElement('a');
    link.href = 'data:text/csv;charset=utf-8,' + encodeURIComponent(csvString);
    link.target = '_blank';
    link.download = filename;
    link.click();
  }

    function printCard() {
        var printContents = document.getElementById("list").outerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
    }
$(document).ready(function () {
    console.log(<?php echo json_encode($json_array); ?>);
    $(".delete-btn").click(function () {
        // Get the row data from the data attribute
        var rowData = $(this).data("row");

        // Construct the project name
        var project_name = rowData.aircraft_name + "_" + rowData.tail_id;

        // Send AJAX POST request to the specified URL
        $.ajax({
            url:'ajax.php?action=add_aircraft_maint',
            type: "POST",
            data: {
                req: "delete",
                project_name: project_name
            },
            success: function (response) {
                if (response == 1) {
                    alert_toast('Delete successful', "success");
                    setTimeout(function () {
                        location.href = 'index.php?page=add_aircraft';
                    }, 2000);
                } else {
                    alert_toast(response, "error", 5000);
                }
            },
            error: function (xhr, status, error) {
                // Handle error
                alert("An error occurred while processing the delete request");
            }
        });
    });
});

    document.addEventListener("DOMContentLoaded", function() {
        const inspectionTypeSelect = document.getElementById("inspection_type");
        const scheduledInspectionDiv = document.getElementById("scheduled_inspection");
        const unscheduledInspectionDiv = document.getElementById("unscheduled_inspection");
        inspectionTypeSelect.addEventListener("change", function() {
            if (inspectionTypeSelect.value === "scheduled") {
                scheduledInspectionDiv.style.display = "block";
                unscheduledInspectionDiv.style.display = "none";
            } else {
                scheduledInspectionDiv.style.display = "none";
                unscheduledInspectionDiv.style.display = "block";
            }
        });
    });

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

    $("#aircraft").change(function() {
        var aircraft = $(this).val();
        var aircraftHours = <?php echo $json_array; ?>;
        var options = aircraftHours[aircraft] || [];
        
        var optionsHtml = "";
        for (var i = 0; i < options.length; i++) {
            optionsHtml += "<option value='" + options[i] + "'>" + options[i] + "</option>";
        }
        
        $("#hours").html(optionsHtml);
    });


    $('#addAircraft').submit(function(e){
        e.preventDefault(); // Prevent form submission
        var aircraft = $('#aircraft').val();
        var tail_number = $('#tail_number').val();
        var inspection_type = $('#inspection_type').val();
        var hours = $('#hours').val();
        var start_date = $('#start_date').val();
        var details = $('#details').val();
        var exp_date = $('#exp_date').val();
        var airbase = "<?php echo $_SESSION['login_airbase']; ?>";

        $.ajax({
            url:'ajax.php?action=add_aircraft_maint',
            data: {
                req: "",
                aircraft: aircraft,
                tail_number: tail_number,
                inspection_type: inspection_type,
                hours: hours,
                start_date: start_date,
                details: details,
                exp_date:exp_date,
                airbase:airbase
            },
            method: 'POST',
            success:function(resp){
                if(resp == 1){
                    alert_toast('Data successfully saved',"success");
                    setTimeout(function(){
                        location.href = 'index.php?page=add_aircraft'
                    },2000)
                }
                else
                {
                    alert_toast(resp, "error",50000);
                    console.log(resp);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert_toast('AJAX request error: ' + textStatus + ' - ' + errorThrown,50000);
                
            }
        })
    })

</script>
