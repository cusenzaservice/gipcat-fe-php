<?php

require_once '../init.php';

function clean($v)
{
    global $con;
    return $con->real_escape_string(htmlspecialchars($v));
}

session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["permissionType"] < 2) {
    http_response_code(401);
    die('AJAX: You are not authenticated! Please provide a session cookie.');
}

if ($_SERVER['REQUEST_METHOD'] != "POST") {
    http_response_code(405);
    die('AJAX: This method is not allowed!');
}

$id = $con->real_escape_string($_POST['idInstallation']);

$res = $con->query("SELECT `version` FROM `installations` WHERE `idInstallation` = '$id'");

if ($con->affected_rows != 1) {
    http_response_code(404);
    die('AJAX: Installation not found!');
}

$arr = $res->fetch_assoc();

if ($_POST['version'] != $arr['version']) {
    http_response_code(400);
    die('AJAX: The version number for the entry provided by the client does not match
    with the one stored on the server. Try refreshing your page.');
}

$fieldnames = array("installationAddress", "installationCity", "heaterBrand", "heater", "heaterSerialNumber", "installationType", 
"manteinanceContractName", "toCall", "monthlyCallInterval", "contractExpiryDate", "footNote");
                    
$fields = "";

foreach ($fieldnames as $fn){
    // super ugly bugfix, ill fix it when i can
    if($fn == "toCall"){
        if (isset($_POST[$fn])) {
            $fields .= "`$fn` = '" . clean($_POST[$fn]) . "', ";
        }
    }else{
        if (isset($_POST[$fn]) && !empty(strval($_POST[$fn]))) {
            $fields .= "`$fn` = '" . clean($_POST[$fn]) . "', ";
        }
    }
}

if(empty($fields)){
    http_response_code(400);
    die('AJAX: No fields to update were found.');
}

$newversion = clean($_POST["version"]) + 1;
$username = $_SESSION["userName"];

$query = "UPDATE `installations` SET $fields `lastEditedBy` = '$username', `version` = '$newversion', `updatedAt` = now() WHERE `idInstallation` = '$id';";
$con->query($query);

if ($con->affected_rows > 0) {
    die('AJAX: OK!');
} else {
    http_response_code(500);
    die('AJAX: Internal server error, update failed.');
}
