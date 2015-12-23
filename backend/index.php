<?php
/**
 * Created by PhpStorm.
 * User: raiym
 * Date: 12/23/15
 * Time: 12:09 PM
 */
error_reporting(E_ALL);

var_dump($_GET);
if (!isset($_GET['action'])) {
    return json_encode(['error' => 1, 'message' => 'No parameter action.']);
}
$action = $_GET['action'];

if (!isset($_POST['email'], $_POST['password'])) {
    return json_encode(['error' => 1, 'message' => 'User credentials not found.']);
}
$email = $_POST['email'];
$password = $_POST['password'];
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    return json_encode(['error' => 1, 'message' => 'Email is invalid.']);
}

$pg_credentials = "host=ec2-107-21-223-110.compute-1.amazonaws.com port=5432 dbname=d9drs0g01eqeir user=xdmfdolmqushkf password=iBwpLgt1wIhrSZa5cPi7FIW_Op sslmode=require";
$db = pg_connect($pg_credentials);

if (!$db) {
    return json_encode(['error' => 1, 'message' => 'Database connection error.']);
}

switch ($action) {
    case 'login':
        $result = pg_query_params($db, "SELECT * FROM tbl_User WHERE email = $1", [$email]);
        if (!$result) {
            pg_close($db);
            return json_encode(['error' => 1, 'message' => 'An error has occurred when trying to find user.']);
        }
        if (pg_num_rows($result) == 0) {
            pg_close($db);
            return json_encode(['error' => 1, 'message' => 'No user with this email.']);
        }
        $user_row = pg_fetch_array($result);
        if (!password_verify($password, $user_row['password'])) {
            pg_close($db);
            return json_encode(['error' => 1, 'message' => 'Password incorrect.']);
        }
        return json_encode(['error' => 0, 'message' => 'Welcome ' . $user_row['email'], 'data' => $user_row]);
        break;
    case 'signup':
        $result = pg_query_params($db, 'SELECT * FROM tbl_User WHERE email = $1', [$email]);
        if (!$result) {
            pg_close($db);
            return json_encode(['error' => 1, 'message' => 'An error has occurred when trying to find user.']);
        }
        if (pg_num_rows($result) != 0) {
            pg_close($db);
            return json_encode(['error' => 1, 'message' => 'User already exists.']);
        }
        $password = password_hash($password, PASSWORD_BCRYPT);
        $token = str_shuffle(MD5(microtime()));
        $result = pg_query_params($db, 'INSERT INTO tbl_user (email, password, token) VALUES  ($1,$2,$3)', [$email, $password, $token]);
        if (!$result) {
            pg_close($db);
            return json_encode(['error' => 1, 'message' => 'An error has occurred when trying to sign up.']);
        }

        $user = ['email' => $email, 'password' => $password, 'token' => $token];
        pg_close($db);
        return json_encode(['error' => 0, 'message' => 'Sign up successfully', 'data' => $user]);
        break;
    default:
        pg_close($db);
        return json_encode(['error' => 1, 'message' => 'Unknown action.']);
}
//pg_close($db);
//echo 'Connection is alive';
// postgres://xdmfdolmqushkf:iBwpLgt1wIhrSZa5cPi7FIW_Op@ec2-107-21-223-110.compute-1.amazonaws.com:5432/d9drs0g01eqeir