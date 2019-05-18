<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Initialize the session
session_start();

require_once "u2_config.php";

// Check if the user is already logged in, if yes then redirect him to logged page
if(isset($_SESSION["type"])){
    header("location: u2_index.php");
    exit;
}

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

    $sql = "SELECT * FROM student WHERE email='$username@is.stuba.sk'";

    // Validate credentials
    $result = $conn->query($sql);
    if (empty($username_err) && empty($password_err)) {    //ak vsetko vyplnil spravne tak su errory prazdne -> zacni overovanie

        //ak je v databaze k danemu uctu aj heslo, overi sa tu
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {   //Creates a loop to loop through results



                if (password_verify($password, $row['heslo'])) {
                    $_SESSION['type'] = student;
                    $_SESSION['id'] = $row['id'];
                    $_SESSION['email'] = $row['email'];

                    header("location: u2_studentView.php");

                } else {
                    // Display an error message if password is not valid
                    $password_err = "The password you entered was not valid.";
                }
            }

            //ak neni v databaze priradene heslo, prebehne prihlasenie cez LDAP tu
        } elseif ($result->num_rows <= 0) {
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {   //Creates a loop to loop through results

                }
            }

            $ldapuid = $username;
            $ldappass = $password;

            $dn = 'ou=People, DC=stuba, DC=sk';
            $ldaprdn = "uid=$ldapuid, $dn";

            $ldapconn = ldap_connect("ldap.stuba.sk")
            or die("Could not connect to LDAP server.");


            if ($ldapconn) {
                $set = ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
                $ldapbind = ldap_bind($ldapconn, $ldaprdn, $ldappass);

                if ($ldapbind) {
                    $results=ldap_search($ldapconn,$dn,"uid=$username");
                    $info=ldap_get_entries($ldapconn,$results);

                    $_SESSION['type'] = student;
                    $_SESSION['email'] = $info[0]['mail'][1];
                    $_SESSION['id'] = $info[0]['uisid'][0];
                    $_SESSION['suhlas'] = $row['suhlas'];

                    header("location: u2_studentView.php");
                } else {
                    $username_err = $password_err="Wrong username or password.";

                }
            }
        } else {
            $username_err = "Wrong username or password.";
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
    <a href="u2_index.php" class="btn btn-info ">Menu</a>

</div>
</body>
</html>
