<?php include('db_connect.php') ?>
<?php
$twhere ="";
if($_SESSION['login_type'] != 1)
  $twhere = "  ";
?>


<?php
    // fetch data from database
    $qry = $conn->query("SELECT id, project_name, details,inspectionType,  MAX(end_date) AS last_end_date FROM project_tasks where airbase ='".$_SESSION['login_airbase']."' AND phase_name != 'stg' GROUP BY project_name");

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
      $result = $conn->query("SELECT status, inspectionType, SUM(duration) as total_duration, SUM(completed_duration) AS total_completed_duration FROM project_tasks WHERE project_name = '$project_name'");
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
        if(($status == 100) && (empty($flyingdate))){
          $flyingdate = date('Y-m-d H:i:s');
          
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
          'details' => $details
      );
  }

    // output data as HTML table
?>

<header>
<link rel="stylesheet" href="orgchart.css" type="text/css" />
</header>
<!-- Info boxes -->
 <div class="col-12">
          <div class="card">
            <div class="card-body">
              Welcome <?php echo $_SESSION['login_name'] ?>!
            </div>
          </div>
  </div>
<div class="col-12">

 <div class="card">
 <div class="card-body">
 <label for="" class="control-label">Aircraft Selection</label>
    <div class="row">
      <div class="col">
              <div class="card">
                <div class="card-body">
                <div class="row">
                  <label for="" class="control-label">Select Database</label>
                    <select class="form-control form-control-sm select2" id="seldb">
                     <option></option>
                      <option>Maintenance Database</option>
                      <option>Staggering Database</option>
                    </select>
                </div>
                </div>
              </div>
      </div>
      <div class="col">
              <div class="card">
                <div class="card-body">
                <div class="row">
                  <label for="" class="control-label">Select Aircraft</label>
                    <select class="form-control form-control-sm select2" name="user_ids[]" id="selaircrafts">
                      <option></option>
                    </select>
                </div>
                </div>
              </div>
      </div>   
    </div>
 </div>  
</div>
</div>
  <hr>
  <?php 

    $where = "";
    if($_SESSION['login_type'] == 2){
      $where = " where manager_id = '{$_SESSION['login_id']}' ";
    }elseif($_SESSION['login_type'] == 3){
      $where = " where concat('[',REPLACE(user_ids,',','],['),']') LIKE '%[{$_SESSION['login_id']}]%' ";
    }
     $where2 = "";
    if($_SESSION['login_type'] == 2){
      $where2 = " where p.manager_id = '{$_SESSION['login_id']}' ";
    }elseif($_SESSION['login_type'] == 3){
      $where2 = " where concat('[',REPLACE(p.user_ids,',','],['),']') LIKE '%[{$_SESSION['login_id']}]%' ";
    }
    ?>
        
      <div class="row">
        <div class="col">
          <div class="row">
              <div class="card card-outline card-success col-12" id="status">
                <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                  <b>Maintenance Aircraft Status </b>
                  <button class="btn btn-flat btn-primary" onclick="printCard()"><i class="fa fa-print"></i>Print</button>
                </div>
                </div>
                <div class="card-body p-0" style="overflow: auto;">>
                <div class="table-responsive">
                  <table class="table m-0 table-hover">
                    <colgroup>
                      <col width="5%">
                      <col width="30%">
                      <col width="35%">
                      <col width="15%">
                      <col width="15%">
                    </colgroup>
                    <thead>
                      <th>#</th>
                      <th>Project</th>
                      <th>Progress</th>
                      <th>Status</th>
                      <th></th>
                    </thead>
                    <tbody>
                    <?php foreach ($data as $i => $row) { ?>
                      <tr>
                          <td><?php echo $i + 1 ?></td>
                          <td>
                              <a><?php echo ucwords($row['aircraft_name'].'_'.$row['tail_id']) ?></a>
                              <br>
                              <small>
                                  Due: <?php echo date("Y-m-d",strtotime($row['completion_date'])) ?>
                              </small>
                          </td>
                          <td class="project_progress">
                              <div class="progress progress-sm">
                                  <div class="progress-bar bg-green" role="progressbar" aria-valuenow="57" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $row['status'] ?>%">
                                  </div>
                              </div>
                              <small>
                                  <?php echo $row['status'] ?>% Complete
                              </small>
                          </td>
                          <td class="project-state">
                          <?php
                              $status = $row['status'];
                              if ($status > 0 && $status < 100) {
                                  echo "In progress";
                              } elseif ($status == 100) {
                                  echo "Complete";
                              } elseif ($status == 0) {
                                  echo "Not started";
                              }
                            ?>
                          </td>

                      </tr>
                      <?php } ?>
                    </tbody>  
                  </table>
                </div>
                </div>
              </div>
            </div>
            <div class="row">
            <div class="col-md-8">
        </div>
            </div>
        </div>
        <div class="col">
          <div class="card card-outline card-success" id="hrcy">
              <div class="card-header">
              <div class="d-flex justify-content-between align-items-center">
                <b>Engg Wing Hierarchy  - <?php echo $_SESSION['login_airbase'] ?> </b>
                <button class="btn btn-flat btn-primary" onclick="printhrc()"><i class="fa fa-print"></i>Print</button>
                </div>
              </div>
              <div class="card-body p-0">
                  <div id="chart_div"></div>
              </div>
            </div>
        </div>
      </div>

    <script type="text/javascript">
      google.charts.load('current', {packages:["orgchart"]});
      google.charts.setOnLoadCallback(drawChart);
      $base = "<?php echo $_SESSION['login_airbase'] ?>";
      function drawChart() {
  var jsonData = $.ajax({
    url: "getData.php",
    data:{base:$base},
    dataType: "json",
    async: false
  }).responseText;
  console.log(jsonData);
  var data = new google.visualization.DataTable(jsonData);

  // Create the chart.
  var chart = new google.visualization.OrgChart(document.getElementById('chart_div'));

  // Add event listeners to nodes.
  google.visualization.events.addListener(chart, 'select', function () {
    var selection = chart.getSelection();
    if (selection.length > 0) {
      // Get the selected row and its ID.
      var row = selection[0].row;
      var id = data.getValue(row, 0);

      // Prompt the user to enter the new name.
      var name = prompt("Enter the new name for this node:", data.getValue(row, 1));
      if (name !== null) {
        // Update the data and redraw the chart.
        data.setCell(row, 1, name);
        $.ajax({
          type: "POST",
          url: "updateData.php",
          data: { id: id, name: name },
          dataType: "json",
          success: function (response) {
            console.log(response);
          },
          error: function (xhr, status, error) {
            console.log(xhr.responseText);
          }
        });
        chart.draw(data, { allowHtml: true });
      }
    }
  });

  // Draw the chart.
  chart.draw(data, { allowHtml: true });
}

      function createOption(ddl, text, value) {
        var opt = document.createElement('option');
        opt.value = value;
        opt.text = text;
        ddl.options.add(opt);
      }
      document.getElementById("seldb").onchange = function() {
        
        var val = document.getElementById("seldb").selectedIndex;
        if (val ==1)
        { 
          $("#selaircrafts").empty();
          createOption(document.getElementById("selaircrafts"), "", "Select an option");
          createOption(document.getElementById("selaircrafts"), "K-8 (AJTS)", "K-8 (AJTS)");
          createOption(document.getElementById("selaircrafts"), "Super Mushak (PFT)", "Super Mushak (PFT)");
          createOption(document.getElementById("selaircrafts"), "T-37 (BFT)", "T-37 (BFT)");
        }
        if (val ==2)
        {
          $("#selaircrafts").empty();
          createOption(document.getElementById("selaircrafts"), "", "Select an option");
          createOption(document.getElementById("selaircrafts"), "K-8 (AJTS)", "K-8 (AJTS)");
          createOption(document.getElementById("selaircrafts"), "Super Mushak (PFT)", "Super Mushak (PFT)");
          createOption(document.getElementById("selaircrafts"), "T-37 (BFT)", "T-37 (BFT)");
        }
      };

      document.getElementById("selaircrafts").onchange = function() {
        var dbs = document.getElementById("seldb").selectedIndex;
        if(dbs === 1)
        {
            var val = document.getElementById("selaircrafts").value;
            if (val === "K-8 (AJTS)") {
              window.location.href = "index.php?page=k8";
            }
            if (val === "Super Mushak (PFT)") {
              window.location.href = "index.php?page=msk";
            }
            if (val === "T-37 (BFT)") {
              window.location.href = "index.php?page=t37";
            }
        }
        if(dbs === 2)
        {
            var val = document.getElementById("selaircrafts").value;
            if (val === "K-8 (AJTS)") {
              window.location.href = "index.php?page=K8_stg";
            }
            if (val === "Super Mushak (PFT)") {
              window.location.href = "index.php?page=Smk_stg";
            }
            if (val === "T-37 (BFT)") {
              window.location.href = "index.php?page=T37_stg";
            }
        }
      };
      function printCard() {
        var printContents = document.getElementById("status").outerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
    }
    function printhrc() {
        var printContents = document.getElementById("hrcy").outerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
    }
   </script>
