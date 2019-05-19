<?php

require_once "u3_config.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader
require 'vendor/autoload.php';
if($_FILES["finalCSV"]["name"]=="")exit("file not loaded");
//check if  template is in db
$con = getDBConnection();
$sql = "select id from uloha3_sablony where sablona='" . $_POST["template"] . "'";
$result = $con->query($sql);
if ($result->num_rows == 0) {
    $sql = "INSERT INTO `uloha3_sablony`(`id`, `nazov`, `sablona`) VALUES (NULL,'" . $_POST["sender"] . "','" . $_POST["template"] . "')";
    $con->query($sql);
    $sql = "select id from uloha3_sablony where sablona='" . $_POST["template"] . "'";
    $result=$con->query($sql);
}
$result = $result->fetch_assoc();
$result = $result["id"];


//create proper texts from template
//get recipients from csv
$texts = createTextsfromTemplate($_POST["template"], $_FILES["finalCSV"]["tmp_name"]);
$recipients = getRecipientsfromCSV($_FILES["finalCSV"]["tmp_name"]);
//send mails and store info
for ($i = 0; $i < count($texts); $i++) {
    sendMails($texts[$i], $recipients[$i]);
    $sql = "INSERT INTO `uloha3_maily`(`id`, `odoslane`, `meno`, `predmet`, `sablona`) VALUES (NULL,NOW(),'" . $recipients[$i] . "','" . $_POST["object"] . "'," . $result . ")";
    $con->query($sql);
}
echo "<script>window.location.href='u3_homescreen.php'</script>";


function sendMails($text, $recipient)
{
    $mail = new PHPMailer(true);
    try {

        $mail->isSMTP();                                            // Set mailer to use SMTP
        $mail->Host = 'mail.stuba.sk';  // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;                                   // Enable SMTP authentication
        $mail->Username = 'xblaziceka@stuba.sk';                     // SMTP username
        $mail->Password = 'Adrianjemegasexy1';                               // SMTP password
        $mail->SMTPSecure = 'starttls';                                  // Enable TLS encryption, `ssl` also accepted
        $mail->Port = 587;                                    // TCP port to connect to
        //Recipients
        $mail->setFrom($_POST["senderMail"], $_POST["sender"]);
        $mail->addAddress($recipient);                           // Name is optional
        // Attachments
        if (isset($_FILES["attachment"]) && strlen($_FILES["attachment"]["name"]) > 0) {
            $mail->addAttachment($_FILES["attachment"]["tmp_name"], $_FILES["attachment"]["name"]);
        }

        $mail->Subject = $_POST["object"];
        //isHTML
        if ($_POST["isHTML"] === "true") {
            $mail->isHTML(true);
            $mail->Body = $text;
        } else {
            $mail->isHTML(false);
            $mail->Body = strip_tags($text);
        }

        $mail->send();

    } catch (Exception $e) {
        echo "Mailer Error: {$mail->ErrorInfo}";
    }

}

function getRecipientsfromCSV($csvFile)
{
    $csv = csv_to_array($csvFile, $_POST["delimeter"]);
    $keys = array_keys($csv[0]);
    $recipients = [];
    foreach ($csv as $row) {
        foreach ($keys as $key) {
            if (strpos($row[$key], "@") !== false && strpos($row[$key], ".") !== false) {
                array_push($recipients, $row[$key]);
            }
        }
    }
    return $recipients;
}


function createTextsfromTemplate($template, $csvFile)
{

    $csvFile = csv_to_array($csvFile);

    if (count($csvFile) < 1) exit("empty csv file");
    $texts = [];
    foreach ($csvFile as $row) {
        $checkedTemplate = $template;
        $keys = explode(";", array_keys($row)[0]);
        $values = explode(";", $row[array_keys($row)[0]]);
        for ($i = 0; $i < count($keys); $i++) {
            $checkedTemplate = str_replace("{{" . $keys[$i] . "}}", $values[$i], $checkedTemplate);
        }
        $checkedTemplate = str_replace("{{sender}}", $_POST["sender"], $checkedTemplate);
        $checkedTemplate = str_replace("{{senderMail}}", $_POST["senderMail"], $checkedTemplate);
        array_push($texts, $checkedTemplate);
    }
    return $texts;
}

//vrati obsah csv ako asociativne pole
function csv_to_array($filename = '', $delimiter = ',')
{
    if (!file_exists($filename) || !is_readable($filename))
        return FALSE;

    $header = NULL;
    $data = array();
    if (($handle = fopen($filename, 'r')) !== FALSE) {
        while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE) {
            if (!$header)
                $header = $row;
            else
                $data[] = array_combine($header, $row);
        }
        fclose($handle);
    }
    return $data;
}

function storeMailTemplate($template, $templateName)
{
    $con = getDBConnection();

    $sql = "INSERT INTO `uloha3_sablony`(`id`, `nazov` , `sablona`) VALUES (NULL,'" . $templateName . "','" . $content . "')";
    $con->query($sql);
    if ($con->error) {
        echo "error with sql" . $con->error;
    }
    $sql = "select id from uloha3_sablony where nazov='" . $templateName . "'";
    $result = $con->query($sql);
    $result = $result->fetch_assoc();
    return $result["id"];
}