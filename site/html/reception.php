<?php
session_start();
if ($_SESSION["valid"] != 1) {
    session_unset();
    session_destroy();
    header("Location: login.php");
}
?>

<html>
<head>
    <title>Reception</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
<h1>Reception</h1>
<br>
<?php
if (isset($_GET['error'])) {
    echo htmlspecialchars($_GET['error']);
}
?>

<?php

// fetch all message destined to user

class DB extends SQLite3
{
    function __construct()
    {
        $this->open('../databases/database.sqlite');
    }
}

$db = new DB();

if (!$db) {
    $error = $db->lastErrorMsg();
    $db->close();
    header("Location: welcome.php");
}

$username = $_SESSION['username'];

$sql = <<<EOF
SELECT * from MESSAGE
WHERE DEST = "$username"
ORDER BY ID DESC;
EOF;

$stmt = $db->prepare('SELECT * from MESSAGE WHERE DEST = :usr ORDER BY ID DESC');
$stmt->bindValue(":usr", $username);

$ret = $db->query($sql);

if ($ret) {

    while ($row = $ret->fetchArray(SQLITE3_ASSOC)) {
        echo "Expeditor: " . $row['EXP'] . "<br>";
        echo "Subject: " . $row['SUBJECT'] . "<br>";
        echo "Date: " . $row['DATE'] . " - " . $row['TIME'] . "<br>";

        echo '<form action="read_message.php" method="post">
        <input type="hidden" name="id" value="' . $row['ID'] . '">
        <input class="button" type="submit" value="Read">
        </form>';
    }

}

$db->close();

?>

<form action="welcome.php" method="post">
    <input class="button" type="submit" value="Home">
</form>
