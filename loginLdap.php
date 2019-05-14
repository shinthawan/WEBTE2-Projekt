<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Initialize the session
session_start();

$ldapuid = $ldappass = "";
$username = $password = "";
$username_err = $password_err = "";

if($_SERVER["REQUEST_METHOD"] == "POST") {

    // Check if username is empty
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter username.";
    } else {
        $username = trim($_POST["username"]);
    }

    // Check if password is empty
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter your password.";
    } else {
        $password = trim($_POST["password"]);
    }
    if (empty($username_err) && empty($password_err)) {    //ak vsetko vyplnil spravne tak su errory prazdne -> zacni overovanie
        $ldapuid = $username;
        $ldappass = $password;

        $dn = 'ou=People, DC=stuba, DC=sk';
        $ldaprdn = "uid=$ldapuid, $dn";

        $ldapconn = ldap_connect("ldap.stuba.sk")
        or die("Could not connect to LDAP server.");

        if($ldapconn){
            $set = ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
            $ldapbind = ldap_bind($ldapconn, $ldaprdn, $ldappass);

            if($ldapbind){
                echo "LDAP bind successful...";
                $_SESSION["ldap"] = true;
                $_SESSION["student"] = true;
                $_SESSION["ldapName"] = $username;
                header("location: menu.php");
            }else {
                echo "LDAP bind failure...";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body{ font: 14px sans-serif; }
        .wrapper{ width: 350px; padding: 20px; }
    </style>
</head>
<body>
<div class="wrapper">
    <h2>Login LDAP</h2>
    <p>Please fill in your credentials to login.</p>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
            <label>Username</label>
            <input type="text" name="username" class="form-control" value="<?php echo $username; ?>">
            <span class="help-block"><?php echo $username_err; ?></span>
        </div>
        <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
            <label>Password</label>
            <input type="password" name="password" class="form-control">
            <span class="help-block"><?php echo $password_err; ?></span>
        </div>
        <div class="form-group">
            <input type="submit" class="btn btn-primary" value="Login">
        </div>
    </form>
    <a href="index.php" class="btn btn-info ">Menu</a>
</div>
</body>
</html>
