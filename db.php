<?php
$CONFIG = [
  "databaseFile" => "db.db.php",
  "apiKey" => false, // set to false to diable API
  "accessKey" => false, // set to false to allow any key
  "maskNonexistingKeys" => true
];

$dbPath = explode('/', $_SERVER['PATH_INFO']);

$databNameIndex = 1;
$keyIndex = 2;

if ($keyIndex >= sizeof($dbPath) || $databNameIndex >= sizeof($dbPath)) {
    header("Content-Type: text/plain");
    die('not enought information provided to access database');
}

function generateRandomString($length = 10)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

$databName = urldecode($dbPath[$databNameIndex]);
$key = urldecode($dbPath[$keyIndex]);

$db = [];

if (file_exists($CONFIG["databaseFile"])) {
    foreach (explode("\n", file_get_contents($CONFIG["databaseFile"])) as $line) {
        if (strpos($line, "// database_content: ") !== false) {
            $db = json_decode(base64_decode(substr(trim($line), 21)), true);
            break;
        }
    }
}

if ($databName == "api") {
    header("Content-Type: application/json");
    if ($CONFIG['apiKey'] === false) {
        die('{"status": "error", "message": "api is disabled"}');
    } elseif (strlen($CONFIG['apiKey']) && (!isset($_REQUEST['key']) || $CONFIG['apiKey'] != $_REQUEST['key'])) {
        die('{"status": "error", "message": "wrong api key"}');
    }

    if ($key == 'backup') {
        die(json_encode($db));
    }

    if ($key == 'clear') {
        if (file_exists($CONFIG["databaseFile"])) {
            unlink($CONFIG["databaseFile"]);
            die('{"status": "ok", "message": "cleared"}');
        }
    }

    die('{"status": "error", "message": "unknown api call"}');
}

header("Content-Type: text/plain");

$accessKeyValid = $CONFIG["accessKey"] === false || strlen($CONFIG['accessKey']) == 0 || (isset($_REQUEST['key']) && $CONFIG['accessKey'] == $_REQUEST['key']);

// user only wants to get value
if (!$accessKeyValid || !array_key_exists("val", $_REQUEST)) {
    $value = "";
    if (!$accessKeyValid || !array_key_exists($databName, $db) || !array_key_exists($key, $db[$databName])) {
        if ($CONFIG["maskNonexistingKeys"]) {
            $value = base64_encode(generateRandomString(rand(20, 4096)));
        }
    } else {
        $value = $db[$databName][$key];
    }

    die($value);
}

// user also wants to save data to the database
if (!array_key_exists($databName, $db)) {
    $db[$databName] = [];
}

$db[$databName][$key] = $_REQUEST["val"];

$dbB64 = base64_encode(json_encode($db));
file_put_contents($CONFIG["databaseFile"], "<?php
header('Content-Type: text/plain');
die('request denied');
// database_content: {$dbB64}
?>");
chmod($CONFIG["databaseFile"], 0600);
die($db[$databName][$key]);

?>
