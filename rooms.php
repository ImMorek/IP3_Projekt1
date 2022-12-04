<?php 
if(!$order = filter_input(INPUT_GET, 'orderBy', FILTER_DEFAULT)) {
    $order = "name DESC";
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
$string = "SELECT * FROM room ORDER BY " . $order;
$stmt = $pdo->query($string);


if ($stmt->rowCount() == 0) {
    echo "Záznam neobsahuje žádná data";
} else {
    echo "<h1 class='table'>Seznam místností</h1>";
    echo "<table class='table table-striped'>";
    echo "<tr>";
    echo "<th>Name <a href='rooms.php?orderBy=name DESC' class='glyphicon glyphicon-arrow-down'></a> <a href='rooms.php?orderBy=name ASC' class='glyphicon glyphicon-arrow-up'></a></th>";
    echo "<th>No. <a href='rooms.php?orderBy=no DESC' class='glyphicon glyphicon-arrow-down'></a> <a href='rooms.php?orderBy=no ASC' class='glyphicon glyphicon-arrow-up'></a></th>";
    echo "<th>Phone <a href='rooms.php?orderBy=phone DESC' class='glyphicon glyphicon-arrow-down'></a><a href='rooms.php?orderBy=phone ASC' class='glyphicon glyphicon-arrow-up'></a></th>";
    echo "</tr>";
    while ($row = $stmt->fetch()) {
        echo "<tr>";
        echo "<td><a href='room.php?roomId={$row->room_id}'>{$row->name}</a></td><td>{$row->no}</td><td>{$row->phone}</td>";
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