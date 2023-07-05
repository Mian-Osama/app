<!DOCTYPE html>
<html>
<head>
<?php include 'db_connect.php'; ?>
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
<span style="font-weight: bold; margin-left: 10px;">(Can fill multiple times)</span>
    <form action="">
    <div class="form-group">
	            <label for="aircraft_id">Aircraft:</label>
	            <select class="form-control" id="aircraft_id" name="aircraft_id">
	                <option>Select aircraft</option>
	            </select>
	        </div>
        <div class="form-group">
	            <label for="tail_id">Tail ID:</label>
	            <select class="form-control" id="tail_id" name="tail_id">
	                <option>Select tail ID</option>
	            </select>
	    </div>
        <div id='sch'>
            <div class="form-group" >
                    <label for="phase_name">Phase Name:</label>
                    <select class="form-control" id="phase_name" name="phase_name">
                        <option>Select Phase</option>
                    </select>
            </div>
            <div class="form-group">
                    <label for="task_name">Task Name:</label>
                    <select class="form-control" id="task_name" name="task_name">
                        <option>Select Task</option>
                    </select>
            </div>

                <div class="form-group" >
                    <div class="row">
                        <div class="col">
                            <label for="task_start">Task ID:</label>
                            <input type="text" class="form-control" id="task_id" name="task_id" readonly>
                        </div>
                        <div class="col">
                            <label for="task_start">Task Start Date:</label>
                            <input type="text" class="form-control" id="task_start" name="task_start" readonly>
                        </div>
                        <div class="col">
                            <label for="task_end">Task Expected End Date:</label>
                            <input type="text" class="form-control" id="task_end" name="task_end" readonly>
                        </div>
                        <div class="col">
                            <label for="task_duration">Task Duration (days):</label>
                            <input type="text" class="form-control" id="task_duration" name="task_duration" readonly>
                        </div>
                        <div class="col">
                            <label for="task_duration">Task Remaining Duration (days):</label>
                            <input type="text" class="form-control" id="task_remain_duration" name="task_remain_duration" readonly>
                        </div>
                    </div>
                </div>

                <input type="hidden" id="user_id" name="user_id" value="1">
                <div class="form-group">

                    <div class="col-sm-6" id='sch'>   
                        <label for="days">Scheduled days assigned to same task: </label><br>
                         <span>(Can submit one by one, even on the same day)</span>
                        <select class="form-control" id="days" name="days"></select>
                    </div>
                </div>
    </div>
                    <div class="form-group">
                                <label for="details">Report Details: </label><br>
                                <span> (Any Delays, AO Days, MICAP etc )</span>
                                <textarea class="form-control" id="details" name="details"></textarea>
                    </div>
                    <div class="form-group col-sm-4" id='unsch'>
                                <label for="status">Status: (Work Done %)</label>
                                <input type="number" id="status" name="status" class="form-control" min="0" max="100" required>
                    </div>
                    
        <div class="form-group">
            <button type="submit" class="btn btn-primary" id="log-task-button">Log Task for Today</button>
        </div>
    </form>
</div>

</body>


	<script>
        function load_tail_id()
        {
            var aircraft_id = $("#aircraft_id").val();
                $.ajax({
                    url: "load_maint_data.php",
                    type: "POST",
                    data: {aircraft_id: aircraft_id, airbase:"<?php echo $_SESSION['login_airbase']; ?>" },
                    success: function(data){
                        
                        data = JSON.parse(data);
                        var options = "<option>Select Tail ID</option>";
                        $.each(data, function(index, value){
                            options += "<option value='" + value.project_name + "'>" + value.project_name + "</option>";
                        });
                        $("#tail_id").html(options);
                    }
                });
        }

            $(document).ready(function(){
                // Load aircraft options
                $.ajax({
                    url: "load_maint_data.php",
                    type: "POST",
                    data: {type: "aircraft", airbase:"<?php echo $_SESSION['login_airbase']; ?>" },
                    success: function(data){
                        data = JSON.parse(data);
                        var options ="";
                        $.each(data, function(index, value){
                            options += "<option value='" + value.project_name + "'>" + value.project_name + "</option>";
                        });
                        $("#aircraft_id").html(options);
                        load_tail_id();
                    }
                });
            });


			// Load tail ID options
            $("#aircraft_id").change(function(){
                load_tail_id();
            });

			// Load phase name options,tasks and Status settings of Loog Form
			$("#tail_id").change(function(){
                var project_name = $("#tail_id").val();
				$.ajax({
					url: "load_maint_data.php",
					type: "POST",
					data: {project_name: project_name, airbase:"<?php echo $_SESSION['login_airbase']; ?>" },
					success: function(data){
                        console.log(data);
                        data = JSON.parse(data);
                        var options =  "<option>Select Phase</option>";
                        $.each(data, function(index, value){
                                options += "<option value='" + value.phase_name + "'>" + value.phase_name + "</option>";
                        });
                        $("#phase_name").html(options);
                        console.log(data);
                        if (data.length === 1) {
                            
                            $("#sch").hide();
                            $("#unsch").show();
                            $('#status').val(data[0].status);
                            $('#details').val(data[0].details);
                        } else {
                            $("#sch").show();
                            $("#unsch").hide();
                        }
					}
				});
				$("#task_name").html("<option>Select phase name first</option>");
			});

			// Load task name options
			$("#phase_name").change(function(){
				var project_name = $("#tail_id").val();
				var phase_name = $(this).val();
				$.ajax({
					url: "load_maint_data.php",
					type: "POST",
					data: {project_name: project_name, phase_name: phase_name, airbase:"<?php echo $_SESSION['login_airbase']; ?>" },
					success: function(data){
                        data = JSON.parse(data);
                        var options = "<option>Select Task</option>";
                        $.each(data, function(index, value){
                            options += "<option value='" + value.task_name + "'>" + value.task_name + "</option>";
                        });
						$("#task_name").html(options);
					}
				});
			});
            $("#task_name").change(function(){
				var project_name = $("#tail_id").val();
				var phase_name = $("#phase_name").val();
                var task_name = $("#task_name").val();

				$.ajax({
					url: "load_maint_data.php",
					type: "POST",
					data: {project_name: project_name, phase_name: phase_name, task_name: task_name, airbase:"<?php echo $_SESSION['login_airbase']; ?>" },
					success: function(data){
                        console.log(data);
                        data = JSON.parse(data)[0];
                        $("#task_id").val(data.id);
                        $("#task_duration").val(data.duration);
                        $("#task_start").val(data.start_date);
                        $("#task_end").val(data.end_date);
                        $("#task_remain_duration").val(data.duration - data.completed_duration);
                        var options = "<option>Select Days to Log</option>";
                        for (var i = 1; i <= data.duration; i++) {
                            options += "<option value='" + i + "'>" + i + "</option>";
                        }
                        $("#days").html(options);
					}
				});
			});
            //Log Task form buttton submission Checks
            $("#log-task-button").click(function(event) {
                event.preventDefault(); // prevent form submission
                var schEmpty = false;
                $("select").each(function() {
                if ($(this).attr("id") !== "aircraft_id" && $(this).prop('selectedIndex') <= 0) {
                    schEmpty = true;
                    return false;
                }
                });
                var unschEmpty = false;
                $("select").each(function() {
                    if ($(this).attr("id") !== "aircraft_id" && $("#status").val() === null || $("#status").val() < 1 || $("#status").val() > 100) {
                    unschEmpty = true;
                    return false;
                    }
                });
                if (schEmpty && !$("#sch").is(":hidden")) {
                    alert("Please fill in all the fields before submitting.");
                } else if (unschEmpty && $("#sch").is(":hidden"))
                {
                    alert("Please Select Status of work done from 1 to 100");
                } else{
                    var project_name = $("#tail_id").val();
                    var phase_name = $("#phase_name").val();
                    var task_name = $("#task_name").val();
                    var details = $("#details").val();
                    if(($("#task_remain_duration").val() == 0) && !$("#sch").is(":hidden"))
                    {
                        alert_toast("Task already completed", "error");
                        return false;
                    }
                    var params = {project_name: project_name, phase_name: phase_name, task_name: task_name, duration:0, details:details}
                    if ($("#sch").is(":hidden")) {
                        params = {project_name: project_name, duration:0, details:details, status:$("#status").val()}
                    }
                    $.ajax({
					url: "load_maint_data.php",
					type: "POST",
					data: params,
					success: function(data){
                        console.log(data);
                        alert_toast("Task updated", "success");
                        setTimeout(function(){
						location.href = 'index.php?page=add_maint_log'
					    },2000)
					}
				});
                }
            });
	</script>

</html>

