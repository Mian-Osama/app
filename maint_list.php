<?php include'db_connect.php' ?>




<script>
function loadGanttChart() {
  google.charts.load('current', {'packages':['gantt']});
  google.charts.setOnLoadCallback(drawChart);

  function drawChart() {
    var selectedProject = $("#project-dropdown").val();
    var jsonData = $.ajax({
      url: "get_maint_data.php?project_id=" + selectedProject,
      dataType: "json",
      async: false
    }).responseText;

    var dataRowCount = JSON.parse(jsonData).length;
    var chartHeight = dataRowCount * 50 + 100;

    var data = new google.visualization.DataTable();
    data.addColumn('string', 'Task ID');
    data.addColumn('string', 'Task Name');
    data.addColumn('date', 'Start Date');
    data.addColumn('date', 'End Date');
    data.addColumn('number', 'Duration');
    data.addColumn('number', 'Percent Complete');
    data.addColumn('string', 'Dependencies');

    var dataArray = JSON.parse(jsonData);
    for (var i = 0; i < dataArray.length; i++) {
      data.addRow([
        dataArray[i].id,
        dataArray[i].name,
        new Date(dataArray[i].start),
        new Date(dataArray[i].end),
        dataArray[i].duration,
        dataArray[i].Percent,
        null
      ]);
    }

    var options = {
      height: chartHeight,
      hAxis: {
        format: 'MMM d'
      },
      gantt: {
        trackHeight: 50,
        barHeight: 30,
        barCornerRadius: 4,
        labelStyle: {
          fontName: 'Roboto',
          fontSize: 14,
          color: '#000000'
        },
        tooltip: { // Added tooltip configuration
          isHtml: true // Enable HTML content in the tooltip
        },
        palette: [
          {
            "color": "#db4437",
            "dark": "#0f9d58",
            "light": "#0f9d58"
          }
        ]
      }
    };


      var chart = new google.visualization.Gantt(document.getElementById('chart_div'));

      chart.draw(data, options);
    }
}
  </script>



  <div class="col-md-12">
  <?php
    $where = "";
    if($_SESSION['login_type'] == 2){
      $where = " where manager_id = '{$_SESSION['login_id']}' ";
    }elseif($_SESSION['login_type'] == 3){
      $where = " where concat('[',REPLACE(user_ids,',','],['),']') LIKE '%[{$_SESSION['login_id']}]%' ";
    }
    $result = $conn->query("SELECT distinct project_name FROM project_tasks where inspectionType = 'scheduled' and airbase ='".$_SESSION['login_airbase']."' order by project_name asc");
  ?>

<div class="card">
  <div class="card-header">
    <ul class="nav nav-tabs card-header-tabs">
      <li class="nav-item">
        <a class="nav-link active" data-toggle="tab" href="#tab1">Gantt Charts of Scheduled Aircrafts</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" data-toggle="tab" href="#tab2">Details of Scheduled Aircrafts (Tasks & Delays) </a>
      </li>
    </ul>
  </div>
  <div class="card-body">
    <div class="tab-content">
      <div class="tab-pane fade show active" id="tab1">
      <div class="d-flex justify-content-between align-items-center">
          <label for="project-dropdown">Select a Project:</label>
          
          <button class="btn btn-flat btn-primary" onclick="printCard()"><i class="fa fa-print"></i>Print</button>
      </div>
        <select id="project-dropdown" onchange="loadGanttChart()">
          <?php while ($row = $result->fetch_assoc()) { ?>
            <option value="<?php echo $row['project_name']; ?>"><?php echo $row['project_name']; ?></option>
          <?php } ?>
        </select>
        <script>
          loadGanttChart(); // call the function after the drop-down is loaded
        </script>
          <div id="chart_div"></div>
      </div>
      <div class="tab-pane fade" id="tab2">
      <div class="d-flex justify-content-between align-items-center">
        <label for="project-dropdown2">Select a Project:</label>
        <button class="btn btn-flat btn-primary" onclick="printCard2()"><i class="fa fa-print"></i>Print</button>
          </div>
        <select id="project-dropdown2">
        <?php
            $result = $conn->query("SELECT distinct project_name FROM project_tasks where phase_name != 'stg' AND airbase ='".$_SESSION['login_airbase']."' order by project_name asc");
          ?>
          <?php while ($row = $result->fetch_assoc()) { ?>
            <option value="<?php echo $row['project_name']; ?>"><?php echo $row['project_name']; ?></option>
          <?php } ?>
        </select>

            <div class="card card-outline card-success">
              <div class="card-body">
                      <div class="card-header" style="font-weight: bold; font-size: 20px;">
                          Scheduled Maintenance Aircraft Details
                      </div>
                      <div class="card-body">
                          <input type="text" id="search-input" class="form-control mb-3" placeholder="Search">
                          <table class="table table-hover table-condensed" id="list">
                              <thead>
                                  <tr>
                                      <th class="text-center">#</th>
                                      <th class="text-center">Project Name</th>
                                      <th class="text-center">Phase Name</th>
                                      <th class="text-center">Task Name</th>
                                      <th class="text-center">Trade</th>
                                      <th class="text-center">Start Date</th>
                                      <th class="text-center">Expected Completion Date</th>
                                      <th class="text-center">Duration</th>
                                      <th class="text-center">Details</th>
                                  </tr>
                              </thead>
                              <tbody>
                              <?php
                                $result = $conn->query("SELECT * FROM project_tasks WHERE phase_name != 'stg' ORDER BY id ASC");
                                $count = 0; // Counter variable

                                while ($row = $result->fetch_assoc()) {
                                  $count = $count + 1;
                                ?>
                                <tr>
                                  <td class="text-center"><?php echo $count; ?></td>
                                  <td class="text-center"><?php echo $row['project_name']; ?></td>
                                  <td class="text-center"><?php echo $row['phase_name']; ?></td>
                                  <td><?php echo $row['task_name'] ?></td>
                                  <td><?php echo $row['trade'] ?></td>
                                  <td><?php echo $row['start_date'] ?></td>
                                  <td><?php echo $row['end_date'] ?></td>
                                  <td><?php echo $row['duration'] ?></td>
                                  <td>
                                    <?php if (!empty($row['details']) && strlen($row['details']) > 0) { ?>
                                      <a class="details-toggle" data-toggle="collapse" href="#details-<?php echo $row['id']; ?>" role="button" aria-expanded="false" aria-controls="details-<?php echo $row['id']; ?>">Hide Details</a>
                                      <div class="collapse show" id="details-<?php echo $row['id']; ?>">
                                        <?php echo $row['details'] ?>
                                      </div>
                                    <?php } ?>
                                  </td>
                                </tr>
                              <?php } ?>
                            </tbody>

                          </table>
                      </div>
              </div>
          </div>

      </div>
    </div>
  </div>
</div>


</div>
<script>
    function printCard() {
        var printContents = document.getElementById("tab1").outerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
    }
    function printCard2() {
        var printContents = document.getElementById("tab2").outerHTML;
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
    // Load rows based on selected project
    $('#project-dropdown2').change(function() {
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


$(document).ready(function() {
  // Call the change function manually
  $('#project-dropdown2').trigger('change');

});


</script>
