
©  FEI STU     

*stránka funguje takto: 
1. index.php - obsahuje menu s tlačítkami "Uloha 1" "Uloha2" "Uloha3" po kliknutí na tlačítko vás to redirectne už na danú úlohu. Napr. "Uloha 1" vás pošle na uloha1index.php, tu si ja riešim login a ďalšiu logiku úlohy. Po prihlásení sa zobrazí menu, kde sú funkcie robené v danej úlohe.
2. Menu po prihlásení kľudne skopírujte z branch "uloha 1" je tam rozdiel len v prepísaní linkov.
                      

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
