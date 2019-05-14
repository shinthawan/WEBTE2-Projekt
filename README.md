
©  FEI STU     

*stránka funguje takto: 
1. index.php - vyberiete si spôsob prihlásenie, každý spôsob vás redirectne na menu.php
2. menu.php - Tu sa zisťuje pomocou session premenných ako ste prihlásený a na základe toho sa nastaví kam má redirectnuť tlačítko Uloha1, Uloha2... (Ak ste študent tak sa uloha1 nastaví na "studentView.php" ak ste admin tak "importResult.php")
                      

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
