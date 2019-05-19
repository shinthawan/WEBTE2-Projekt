<?php
require_once "u3_config.php";



if (isset($_POST["addTemplate"]) && isset($_FILES["templateFile"])) {
    storeMailTemplate($_FILES["templateFile"]["tmp_name"], $_POST["addTemplate"]);
}


//operacia s prvym csv suborom -> prida hesla do posledneho stlpca a stiahne subor
if (isset($_FILES["initialCSV"]) && isset($_POST["delimeter"])) {
    generatePasswordsIncsv();
}





function generatePasswordsIncsv()
{
    $csvFile = csv_to_array($_FILES['initialCSV']['tmp_name'], $_POST["delimeter"]);
    //pridat prazdny stlpec na koniec
    for ($i = 0; $i < count($csvFile); $i++) {
        $csvFile[$i]["password"] = generatePassword(15);
    }

    //zapis udajov do csv suboru
    $fh = fopen($_FILES["initialCSV"]['tmp_name'], 'w');
    file_put_contents($fh, "");
    fputcsv($fh, array_keys($csvFile[0]), $_POST["delimeter"]);
    foreach ($csvFile as $row) {
        fputcsv($fh, $row, $_POST["delimeter"]);
    }
    fclose($fh);


    //file download
    if (file_exists($_FILES["initialCSV"]['tmp_name'])) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($_FILES["initialCSV"]['name']) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($_FILES["initialCSV"]['tmp_name']));
        readfile($_FILES["initialCSV"]['tmp_name']);
        exit;
    }
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

//generuje pseudonahodne heslo
function generatePassword($length, $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789')
{
    $pieces = [];
    $max = mb_strlen($keyspace, '8bit') - 1;
    for ($i = 0; $i < $length; ++$i) {
        $pieces [] = $keyspace[random_int(0, $max)];
    }
    return implode('', $pieces);
}

