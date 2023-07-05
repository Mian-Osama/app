  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <div class="dropdown">
   	<a href="./" class="brand-link">
        <h3 class="text-center p-0 m-0"><b>Base: <?php echo $_SESSION['login_airbase'] ?></b></h3>
    </a>
      
    </div>
    <div class="sidebar pb-4 mb-4">
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column nav-flat" data-widget="treeview" role="menu" data-accordion="false">
          <li class="nav-item dropdown">
            <a href="./" class="nav-link nav-home">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                Dashboard
              </p>
            </a>
          </li>  
          <li class="nav-item">
            <a href="#" class="nav-link nav-edit_project nav-view_project">
              <i class="nav-icon fas fa-layer-group"></i>
              <p>
                Unscheduled Projects
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">

              <li class="nav-item">
                <a href="./index.php?page=new_project" class="nav-link nav-new_project tree-item">
                  <i class="fas fa-angle-right nav-icon"></i>
                  <p>Add New Project</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="./index.php?page=project_list" class="nav-link nav-project_list tree-item">
                  <i class="fas fa-angle-right nav-icon"></i>
                  <p>List of Projects</p>
                </a>
              </li>
            </ul>
          </li> 
          <li class="nav-item">
                <a href="./index.php?page=task_list" class="nav-link nav-task_list">
                  <i class="fas fa-tasks nav-icon"></i>
                  <p>Tasks of a Project</p>
                </a>
          </li>


        <li class="nav-item">
            <a href="./index.php?page=reports" class="nav-link nav-reports">
                <i class="fas fa-th-list nav-icon"></i>
                <p>Unscheduled Project Report</p>
            </a>
        </li>


        <li class="nav-item">
            <a href="#" class="nav-link nav-edit_project nav-view_project">
              <i class="nav-icon fas fa-layer-group"></i>
              <p>
                Maintenance Database
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">

              <li class="nav-item">
              <a href="./index.php?page=add_aircraft" class="nav-link add_aircraft">
                  <i class="fas fa-angle-right nav-icon"></i>
                  <p>Register maintenance Aircrafts List</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="./index.php?page=maint_list" class="nav-link nav-project_list tree-item">
                  <i class="fas fa-angle-right nav-icon"></i>
                  <p>Scheduled Aircrafts Maintenance</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="./index.php?page=add_maint_log" class="nav-link nav-project_list tree-item">
                  <i class="fas fa-angle-right nav-icon"></i>
                  <p>Update Maintenance Tasks</p>
                </a>
              </li>
            </ul>
          </li> 

        <li class="nav-item">
          <a href="./index.php?page=add_aircraft_stg" class="nav-link add_aircraftt">
          <i class="fas fa-list nav-icon"></i>
            <p>Base Record</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="./index.php?page=add_aircraft_stgchart" class="nav-link add_aircraftt">
          <i class="fas fa-list nav-icon"></i>
            <p>Stagger Charts</p>
          </a>
        </li>
          <?php if($_SESSION['login_type'] == 1): ?>
          <li class="nav-item">
            <a href="#" class="nav-link nav-edit_user">
              <i class="nav-icon fas fa-users"></i>
              <p>
                Users
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="./index.php?page=new_user" class="nav-link nav-new_user tree-item">
                  <i class="fas fa-angle-right nav-icon"></i>
                  <p>Add New</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="./index.php?page=user_list" class="nav-link nav-user_list tree-item">
                  <i class="fas fa-angle-right nav-icon"></i>
                  <p>List</p>
                </a>
              </li>
            </ul>
          </li>
          <?php endif; ?>
          <?php if($_SESSION['login_type'] < 3): ?>
          <li class="nav-item">
          <a href="./index.php?page=attendance" class="nav-link nav-attendance">
            <i class="fas fa-check-square nav-icon"></i>
            <p>Mark Attendance</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="./index.php?page=attendance_list" class="nav-link nav-attendance_list">
            <i class="fas fa-list-ul nav-icon"></i>
            <p>Attendance List</p>
          </a>
        </li>
        <?php endif; ?>
        <?php if($_SESSION['login_type'] == 1): ?>
           <li class="nav-item">
                <a href="./index.php?page=bases" class="nav-link nav-reports">
                  <i class="fas fa-fighter-jet nav-icon"></i>
                  <p>Air Bases</p>
                </a>
          </li>
          <?php endif; ?>
        </ul>
      </nav>
    </div>
  </aside>
  <script>
  	$(document).ready(function(){
      var page = '<?php echo isset($_GET['page']) ? $_GET['page'] : 'main' ?>';
  		var s = '<?php echo isset($_GET['s']) ? $_GET['s'] : '' ?>';
      if(s!='')
        page = page+'_'+s;
  		if($('.nav-link.nav-'+page).length > 0){
             $('.nav-link.nav-'+page).addClass('active')
  			if($('.nav-link.nav-'+page).hasClass('tree-item') == true){
            $('.nav-link.nav-'+page).closest('.nav-treeview').siblings('a').addClass('active')
  				$('.nav-link.nav-'+page).closest('.nav-treeview').parent().addClass('menu-open')
  			}
        if($('.nav-link.nav-'+page).hasClass('nav-is-tree') == true){
          $('.nav-link.nav-'+page).parent().addClass('menu-open')
        }

  		}
     
  	})
  </script>