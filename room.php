<?php
$id = filter_input(INPUT_GET,
    'roomId',
    FILTER_VALIDATE_INT,
    ["options" => ["min_range"=> 1]]
);


if ($id === null || $id === false) {
    http_response_code(400);
    $status = "bad_request";
} else {

    require_once "inc/db.inc.php";

    $stmt = $pdo->prepare("SELECT * FROM room WHERE room_id=:roomId");
    $stmt->execute(['roomId' => $id]);
    if ($stmt->rowCount() === 0) {
        http_response_code(404);
        $status = "not_found";
    } else {
        $room = $stmt->fetch();
        $status = "OK";
    }

    $stmt2 = $pdo->prepare("SELECT employee_id, name, surname, wage FROM employee WHERE room = {$room->room_id}");
    $stmt2->execute();
    $attendees = $stmt2->fetchAll();

    $stmt3 = $pdo->prepare("SELECT employee.employee_id AS employee_id, employee.name AS name, employee.surname AS surname 
    FROM `key` 
    INNER JOIN employee 
    ON (employee.employee_id = key.employee) 
    WHERE key.room =  {$room->room_id}");
    $stmt3->execute();
    $keyOwners = $stmt3->fetchAll();
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
    <title>Místnost</title>
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
        print_room($room,$attendees, $keyOwners);
        break;
}
function print_room($room, $attendees, $keyOwners) {
echo("<h1>Místnost číslo {$room->no}</h1>");
echo "<table class='table'>";
echo "<tr>";
echo "<td><b>Místnost: </b></td><td>{$room->no}</td>";
echo "</tr>";
echo "<tr>";
echo "<td><b>Telefon: </b></td><td>{$room->phone}</td>";
echo "</tr>";
echo "<tr>";
echo "<td><b>Název: </b></td><td>{$room->name}</td>";
echo "</tr>";

echo "<tr>";
echo "<td><b>Lidé: </b></td>";
if(count($attendees)) {
    echo "<td>";
    foreach($attendees as $employee) {
        echo "<a href='clovek.php?clovekId={$employee->employee_id}'>{$employee->name} {$employee->surname}</a><br>";
        }
    echo "</td>";
}
else {
    echo "<td><b>Lidé: </b></td><td>----</td>";
}
echo "</tr>";

echo "<tr>";
if(count($attendees)) {
    $avgPay = array_sum(array_column($attendees, 'wage')) / count($attendees);
    echo "<td><b>Průměrná mzda: </b></td><td>{$avgPay}</td>";
} else {
    echo "<td><b>Průměrná mzda: </b></td><td>----</td>";
}
echo "</tr>";


echo "<tr>";
echo "<td><b>Klíče: </b></td>";
if(count($keyOwners)) {
    echo "<td>";
    foreach($keyOwners as $keyOwner) {
        echo "<a href='clovek.php?clovekId={$keyOwner->employee_id}'>{$keyOwner->name} {$keyOwner->surname}</a><br>";
    }
    echo "</td>";
} else {
    echo "<td><b>Klíče: </b></td><td>----</td>";
}
echo "</tr>";

echo "</table>";
echo("<a href='rooms.php'>Zpět na seznam místností</a>");

}
?>
</body>
</html>