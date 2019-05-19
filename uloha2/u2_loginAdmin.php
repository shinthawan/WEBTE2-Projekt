<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Initialize the session
session_start();

// Include config file
require_once "u2_config.php";

if(isset($_GET['language']) && $_GET['language'] == "EN"){
    header("location: u2en_loginAdmin.php");

}elseif(isset($_GET['language']) && $_GET['language'] == "SK"){
    header("location: u2_loginAdmin.php");
}

// Check if the user is already logged in, if yes then redirect him to logged page
if(isset($_SESSION["type"])){
    header("location: u2_index.php");
    exit;
}

// Define variables and initialize with empty values
$username = $password = "";
$username_err = $password_err = "";

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){

    // Check if username is empty
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter username.";
    } else{
        $username = trim($_POST["username"]);
    }

    // Check if password is empty
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter your password.";
    } else{
        $password = trim($_POST["password"]);
    }

    // Validate credentials
    if(empty($username_err) && empty($password_err)) {
        // Prepare a select statement
        $sql = "SELECT admin_id, admin_name, admin_password FROM admin WHERE admin_name='admin'";


        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {   //Creates a loop to loop through results

                if ($username == $row['admin_name']) {
                    //admin neni hasovany
                    //if (password_verify($password, $row['admin_password'])) {
                    if ($password==$row['admin_password']) {

                        $_SESSION['type'] = admin;
                        $_SESSION['username'] = $row['admin_name'];
                        $_SESSION['password'] = $row['admin_password'];

                        header("location: u2_showResult.php");

                    } else {
                        // Display an error message if password is not valid
                        $password_err = "The password you entered was not valid.";
                    }
                } else {
                    // Display an error message if username doesn't exist
                    $username_err = "No account found with that username.";
                }
            }
        }
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <title>Úloha 2 - Login - Admin</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <link rel="stylesheet" media="print" href="print.css" type="text/css">
    <style type="text/css">
        body{ font: 14px sans-serif; }
        .wrapper{ width: 350px; padding: 20px; }
    </style>
</head>
<body>
<div class="fixed-top">
    <ul><a href="u2en_loginAdmin.php?language=EN">Switch to <img name="en" src="u2_gb.png" alt="en"/></a></ul>
</div>
<div class="wrapper">
    <h2>Login Admin</h2>
    <p>Prosím vyplňte svoje údaje pre prihlásenie.</p>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
            <label>Meno</label>
            <input type="text" name="username" class="form-control" value="<?php echo $username; ?>">
            <span class="help-block"><?php echo $username_err; ?></span>
        </div>
        <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
            <label>Heslo</label>
            <input type="password" name="password" class="form-control">
            <span class="help-block"><?php echo $password_err; ?></span>
        </div>
        <div class="form-group">
            <input type="submit" class="btn btn-primary" value="Login">
        </div>
    </form>
    <a href="u2_index.php" class="btn btn-danger ">Späť</a>
</div>
</body>
</html>
