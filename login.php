<!DOCTYPE html>
<html lang="en">
<?php 
session_start();
include('./db_connect.php');
  ob_start();
  // if(!isset($_SESSION['system'])){

    $system = $conn->query("SELECT * FROM system_settings")->fetch_array();
    foreach($system as $k => $v){
      $_SESSION['system'][$k] = $v;
    }
  // }
  ob_end_flush();
?>
<?php 
if(isset($_SESSION['login_id']))
header("location:index.php?page=home");

?>
<?php include 'header.php' ?>
<body class="hold-transition login-page bg-info">
  <div class="login-box">
    <div class="login-logo">
      <a href="#" class="text-white"><b><?php echo $_SESSION['system']['name'] ?> - Admin</b></a>
    </div>
    <!-- /.login-logo -->
    <div class="card">
      <div class="card-body login-card-body">
        <form action="" id="login-form">
          <div class="input-group mb-3">
            <input type="email" class="form-control" name="email" required placeholder="Email" autocomplete="username">
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-envelope"></span>
              </div>
            </div>
          </div>
          <div class="input-group mb-3">
            <input type="password" class="form-control" name="password" required placeholder="Password" autocomplete="current-password">
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-lock"></span>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-8">
              <div class="icheck-primary">
                <input type="checkbox" id="remember" name="remember">
                <label for="remember">
                  Remember Me
                </label>
              </div>
            </div>
            <div class="col-4">
              <button type="submit" class="btn btn-primary btn-block">Sign In</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</body>


<!-- /.login-box -->
<script>
$(document).ready(function(){
  // Check if the 'rememberedUser' cookie exists
  var rememberedUser = getCookie('rememberedUser');
  if (rememberedUser) {
    // Fill in the email field with the saved email
    $('input[name="email"]').val(rememberedUser);
    // Set the "Remember Me" checkbox as checked
    $('#remember').prop('checked', true);
    // Trigger the login process
    performLogin();
  }

  $('#login-form').submit(function(e){
    e.preventDefault();
    start_load();
    if($(this).find('.alert-danger').length > 0)
      $(this).find('.alert-danger').remove();
    performLogin();
  });

  function performLogin() {
    $.ajax({
      url: 'ajax.php?action=login',
      method: 'POST',
      data: $('#login-form').serialize(),
      error: err => {
        console.log(err);
        end_load();
      },
      success: function(resp){
        if(resp == 1){
          if($('#remember').is(':checked')) {
            // Set a cookie named 'rememberedUser' with the email
            document.cookie = 'rememberedUser=' + $('input[name="email"]').val() + '; max-age=86400; path=/'; // Cookie expires in 24 hours
          }
          location.href ='index.php?page=main';
        } else {
          $('#login-form').prepend('<div class="alert alert-danger">Username or password is incorrect.</div>');
          end_load();
        }
      }
    });
  }

  function getCookie(name) {
    var cookieArr = document.cookie.split(';');
    for(var i = 0; i < cookieArr.length; i++) {
      var cookiePair = cookieArr[i].split('=');
      if (name == cookiePair[0].trim()) {
        return decodeURIComponent(cookiePair[1]);
      }
    }
    return null;
  }
});

</script>
<?php include 'footer.php' ?>

</body>
</html>
