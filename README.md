
©  FEI STU                                                                                             

*ak chcete niekde pridať logout tak ```html <a href="logout.php" class="btn btn-danger">Sign Out of Your Account</a>  ```

*ak chcete pridať na stránku to, že je prístupna len prihlásenemu používateľovi tak 
```html 
// Initialize the session
session_start();
// Check if the user is already logged in, if yes then redirect him to welcome page
if(!isset($_SESSION["ldap"]) && $_SESSION["ldap"] !== true){
    header("location: ../index.php");
    exit;
}
