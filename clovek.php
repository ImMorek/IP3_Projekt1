<?php
$id = filter_input(INPUT_GET,
    'clovekId',
    FILTER_VALIDATE_INT,
    ["options" => ["min_range"=> 1]]
);

if ($id === null || $id === false) {
    http_response_code(400);
    $status = "bad_request";
} else {

    require_once "inc/db.inc.php";

    $stmt = $pdo->prepare("SELECT * FROM employee WHERE employee_id=:clovekId");
    $stmt->execute(['clovekId' => $id]);
    if ($stmt->rowCount() === 0) {
        http_response_code(404);
        $status = "not_found";
    } else {
        $employee = $stmt->fetch();
        $status = "OK";
    }

    $stmtCurrentRoom = $pdo->prepare("SELECT name, room_id FROM room WHERE room_id = {$employee->room}");
    $stmtCurrentRoom->execute();
    $employeeRoom = $stmtCurrentRoom->fetch();

    $stmtKey = $pdo->prepare("SELECT room.room_id AS room_id, room.name AS name
    FROM `key` 
    INNER JOIN room 
    ON (room.room_id = key.room) 
    WHERE key.employee =  {$employee->employee_id}");
    $stmtKey->execute();
    $keyRooms = $stmtKey->fetchAll();
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <title>Zaměstnanec</title>
</head>
<body>
<?php
switch ($status) {
    case "bad_request":
        echo "<h1>Error 400: Bad request</h1>";
        break;
    case "not_found":
        echo "<h1>Error 404: Not found</h1>";
        break;
    default:
        print_person($employee, $employeeRoom, $keyRooms);
        break;
}
function print_person($employee, $employeeRoom, $keyRooms) {
    echo("<h1>Karta osoby: {$employee->surname} {$employee->name[0]}.</h1>");
    echo "<table class='table'>";
    echo "<tr>";
    echo "<td><b>Jméno:</b></td><td>{$employee->name}</td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td><b>Příjmení: </b></td><td>{$employee->surname}</td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td><b>Zaměstnání: </b></td><td>{$employee->job}</td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td><b>Mzda:</b></td><td>{$employee->wage}</td>";
    echo "</tr>";

    echo "<tr>";
    echo "<td><b>Místnost: </b></td><td><a href='room.php?roomId={$employeeRoom->room_id}'>{$employeeRoom->name}</a></td>";
    echo "</tr>";

    echo "<tr>";
    echo "<td><b>Klíče: </b></td>";
    if(count($keyRooms)) {
        echo "<td>";
        foreach($keyRooms as $keyToRoom) {
            echo "<a href='room.php?roomId={$keyToRoom->room_id}'>{$keyToRoom->name}</a><br>";
        }
        echo "</td>";
    } else {
        echo "<td><b>Místnost: </b></td><td>----</td>";
    }
    echo "</table>";

    echo("<a href='lide.php'>Zpět na seznam zaměstnanců</a>");

}