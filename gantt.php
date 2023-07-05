<?php include'db_connect.php' ?>




<script>
function loadGanttChart() {
  
    google.charts.load('current', {'packages':['gantt']});
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
      var selectedProject = $("#project-dropdown").val();
      var jsonData = $.ajax({
        url: "get_tasks.php?project_id=" + selectedProject,
        dataType: "json",
        async: false
      }).responseText;

      
      //console.log(jsonData);
      var data = new google.visualization.DataTable();
      data.addColumn('string', 'Task ID');
      data.addColumn('string', 'Task Name');
      data.addColumn('date', 'Start Date');
      data.addColumn('date', 'End Date');
      data.addColumn('number', 'Duration');
      data.addColumn('number', 'Percent Complete');
      data.addColumn('string', 'Dependencies');

      const dataArray = JSON.parse(jsonData);
      
       for (var i = 0; i < dataArray.length; i++) {
        //console.log(dataArray[i]);
            data.addRow([    dataArray[i].id,
                dataArray[i].name,
                new Date(dataArray[i].start),
                new Date(dataArray[i].end),
                dataArray[i].duration,
                dataArray[i].Percent,
                null
        ]);
      }
      var options = {
  height: 400,
  gantt: {
    trackHeight: 50,
    // Set the base color to green
    labelStyle: {
      fontName: 'Roboto',
      fontSize: 14,
      color: '#000000'
    },
    barCornerRadius: 4,
    barHeight: 30,
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
    $result = $conn->query("SELECT * FROM project_list $where order by name asc");
  ?>
  <label for="project-dropdown">Select a Project:</label>
  <select id="project-dropdown" onchange="loadGanttChart()">
    <?php while ($row = $result->fetch_assoc()) { ?>
      <option value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?></option>
    <?php } ?>
  </select>
  <script>
    loadGanttChart(); // call the function after the drop-down is loaded
  </script>
    <div id="chart_div"></div>
</div>

