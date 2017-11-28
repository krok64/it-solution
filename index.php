<?php
$mysqli = new mysqli('127.0.0.1', 'root', '', 'urls');
$mysqli->query("SET NAMES 'utf8';");
$mysqli->query("SET CHARACTER SET 'utf8';");
$mysqli->query("SET SESSION collation_connection = 'utf8_general_ci';");

$link = '';
if ($_SERVER['REQUEST_METHOD']=='POST' && isset($_POST['url']) && $_POST['url']!=''){
    $url = $_POST['url'];
    $stmt = $mysqli->prepare('SELECT * FROM `urls` WHERE `original`=?');
    $stmt->bind_param("s", $url);
    $stmt->execute();
    $stmt->bind_result($id, $original);
    if ($stmt->fetch()) {
    } else {
        $stmt = $mysqli->prepare('INSERT INTO `urls` (original) VALUES (?)');
        $stmt->bind_param("s", $url);
        $stmt->execute();
        $id = $stmt->insert_id;
    }
    if ($id) {
        $short = $_SERVER['SERVER_NAME'] . $_SERVER['SCRIPT_NAME'] . '?u=' . $id;
        $link = "<a href=$url>$short</a>";
    }
    else {
        $link = '';
    }
} elseif ($_SERVER['REQUEST_METHOD']=='GET' && isset($_GET['u'])) {
    $u = $_GET['u'];
    $stmt = $mysqli->prepare('SELECT * FROM `urls` WHERE `id`=?');
    $stmt->bind_param("i", $u);
    $stmt->execute();
    $stmt->bind_result($id, $original);
    if ($stmt->fetch()) {
        Header("Location: $original");
    }
}
?>

<html>
<head>
    <title>Url cut service</title>
</head>
<body>
<form method="post">
    <input name="url" placeholder="Enter url here">
    <button type="submit">Get short url</button>
    <label id="short_url"><?php echo $link?></label>
</form>
</body>
</html>
