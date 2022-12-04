<?php 
if(!$order = filter_input(INPUT_GET, 'orderBy', FILTER_DEFAULT)) {
    $order = "employeeName DESC";
}

?>

<!DOCTYPE html>

<html>
<head>
    <meta charset="UTF-8">
    <!-- Bootstrap-->
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <title>Připojení k DB</title>
</head>
<body class="container">
<?php

require_once "inc/db.inc.php";

$string = "SELECT employee.name AS employeeName, employee.surname AS employeeSurname, employee.job AS employeeJob, employee.employee_id AS employeeId, room.name AS roomName, room.phone AS roomPhone FROM employee INNER JOIN room ON employee.room = room.room_id ORDER BY " . $order;
$stmt = $pdo->prepare($string);
$stmt->execute();

$stmtRoom = $pdo->prepare('SELECT name, phone FROM room WHERE room_id=:roomId');

if ($stmt->rowCount() == 0) {
    echo "Záznam neobsahuje žádná data";
} else {
    echo "<h1 class='table'>Seznam zaměstnanců</h1>";
    echo "<table class='table table-striped'>";
    echo "<tr>";
    echo "<th>Jméno <a href='lide.php?orderBy=employeeName DESC' class='glyphicon glyphicon-arrow-down'></a> <a href='lide.php?orderBy=employeeName ASC' class='glyphicon glyphicon-arrow-up'></a></th>";
    echo "<th>Místnost <a href='lide.php?orderBy=roomName DESC' class='glyphicon glyphicon-arrow-down'></a> <a href='lide.php?orderBy=roomName ASC' class='glyphicon glyphicon-arrow-up'></a></th>";
    echo "<th>Telefon <a href='lide.php?orderBy=roomPhone DESC' class='glyphicon glyphicon-arrow-down'></a> <a href='lide.php?orderBy=roomPhone ASC' class='glyphicon glyphicon-arrow-up'></a></th>";
    echo "<th>Pozice <a href='lide.php?orderBy=employeeJob DESC' class='glyphicon glyphicon-arrow-down'></a> <a href='lide.php?orderBy=employeeJob ASC' class='glyphicon glyphicon-arrow-up'></a></th>";
    echo "</tr>";
    while ($row = $stmt->fetch()) {
        echo "<tr>";
        echo "<td><a href='clovek.php?clovekId={$row->employeeId}'>{$row->employeeName} {$row->employeeSurname}</a></td><td>{$row->roomName}</td><td>{$row->roomPhone}</td><td>{$row->employeeJob}</td>";
        echo "</tr>";
    //foreach ($stmt as $row) {
//        var_dump($row);
//        var_dump($row->name);
//        var_dump($row['name']);
    }
    echo "</table>";
}
unset($stmt);
?>
</body>
</html>