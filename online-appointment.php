<?php
session_start();
include 'conn.php';
if(!empty($_SESSION["online_user_id"])){
  header("Location: online-appointment.php");
}
if (isset($_POST["submit"])) {
  $username = sanitizeInput($_POST["username"]);
  $password = sanitizeInput($_POST["password"]);
  $result = mysqli_query($connection, "SELECT * FROM users_online WHERE username = '$username'");
  $row = mysqli_fetch_assoc($result);
  if (mysqli_num_rows($result) > 0) {
      if ($password == $row['password'] && $row['online_user_id'] !== null) {
          $_SESSION["login"] = true;
          $_SESSION["online_user_id"] = $row["online_user_id"];
          $_SESSION['success'] = true;
      } else {
          $_SESSION['unsuccess'] = true;
      }
  } else {
      $_SESSION['unsuccess'] = true;
  }
}

// Delete section for archives
$deleteThreshold = date('Y-m-d H:i:s', strtotime('-30 days'));
function sanitizeInput($input) {
  return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Romantic Baboy</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="assets/rombab-logo.png">
    <link rel="stylesheet" href="node_modules/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="node_modules/ionicons/css/ionicons.min.css">
    <link rel="stylesheet" href="node_modules/ionicons/css/ionicons.min.css">
    <script src="node_modules/jquery/dist/jquery.min.js"></script>
    <script src="node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Bootstrap Icons CSS -->
    <link href="node_modules/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="d-flex align-items-center justify-content-center bg-grill container">
    <div class="login container">
      <div class="row">
        <div class="col-lg-4 d-flex flex-column align-items-center justify-content-center">
          <img src="assets/rombab-logo.png" alt="Image" class="img-fluid" width="200" height="200">
          <p class="text-center text-danger">ONLINE APPOINTMENT</p>
        </div>
        <div class="col-lg-8">
          <form class="needs-validation" method="POST">

            <div class="form-group was-validated">
              <label class="form-label text-white" for="username">Username</label>
              <input class="form-control" type="text" id="username" name="username" placeholder="Enter username" pattern=".{4,}" required>
                <div class="invalid-feedback">
                  <small style="font-size: 12px;"> Username consist of at least 4 characters long.</small>
                </div> 
            </div>
            
            <div class="form-group was-validated">
              <label class="form-label text-white" for="password">Password</label>
              <input class="form-control" type="password" id="password" name="password" placeholder="Enter password" pattern=".{8,}" required>
                <div class="invalid-feedback">
                  <small style="font-size: 12px;"> Password consist of at least 8 characters long.</small>
                </div>
            </div>
          
            <button class="btn btn-primary w-100 mt-2 mb-2" name="submit" type="submit">LOG IN <i class="bi bi-arrow-right"></i></button>
          </form>

          <div class="text-center">
            <a href="sign-up.php" class="text-white" style="font-size: 12px;">No account yet? Sign-up.</a>
          </div>
        </div>
      </div>
    </div>
    

    <!-- Success alert modal -->
    <div id="successModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="successModalLabel"
    aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
              <h5 class="modal-title" id="successModalLabel">Success</h5>
          </div>
          <div class="modal-body">
            <p>Log-in successfully!</p>
          </div>
          <div class="modal-footer">
              <a href="/rb-online-customer/dashboard.php" class="btn btn-primary">Proceed</a>
          </div>
        </div>
      </div>
    </div>
    <!-- End of success alert modal -->
    <!-- Not registered alert modal -->
    <div id="unsuccessModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="unsuccessModalLabel"
    aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
              <h5 class="modal-title" id="unsuccessModalLabel">Unsucess</h5>
          </div>
          <div class="modal-body">
            <p>Not Registered Account!</p>
          </div>
          <div class="modal-footer">
            <a href="online-appointment.php" class="btn btn-primary">Close</a>
          </div>
        </div>
      </div>
    </div>
    <!-- End of success Not registered -->
</body>
</html>

<?php if (isset($_SESSION['success'])) { ?>
<script>
  $(document).ready(function() {
  $("#successModal").modal("show");
})
</script>
<?php
  unset($_SESSION['success']);
  exit();
  } else if (isset($_SESSION['unsuccess'])) {
  ?>
<script>
  $(document).ready(function() {
  $("#unsuccessModal").modal("show");
})
</script>
<?php
  unset($_SESSION['unsuccess']);
  exit();
  }
?>

<script>
  function validateAccountInput(inputElement) {
    let inputValue = inputElement.value;
    let sanitizedValue = inputValue.replace(/[^a-zA-Z0-9\s\-_@]/g, ''); // Allow letters, numbers, spaces, hyphen, underscore, and at symbol
    inputElement.value = sanitizedValue; // Update the input value
    }

    const usernameInput = document.getElementById('username');
    const passwordInput = document.getElementById('password');

    usernameInput.addEventListener('input', function() {
        validateAccountInput(usernameInput);
    });

    passwordInput.addEventListener('input', function() {
        validateAccountInput(passwordInput);
  });
</script>