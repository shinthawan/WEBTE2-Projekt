<?php
if(isset($_POST['export'])){
    $rok=$_GET['schoolYear'];
    $predmet=$_GET['predmetName'];

    $sql="SELECT id FROM predmet WHERE nazov='$predmet' AND rok='$rok'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $predmetID=$row['id'];


    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=data.csv');
    $output = fopen("php://output", "w");
    fputcsv($output, array('ID', 'Meno', 'Body'));

    $query = "SELECT student.id, student.meno, zaznam.body_stud from student INNER JOIN zaznam ON student.id=zaznam.id_student WHERE zaznam.id_predmet='$predmetID' ORDER BY zaznam.id ASC";

    $result = mysqli_query($conn, $query);
    while($row = mysqli_fetch_assoc($result))
    {
        fputcsv($output, $row);
    }
    fclose($output);

    ob_flush();
    exit();
}
