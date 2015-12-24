<?php
/**
 * Created by PhpStorm.
 * User: raiym
 * Date: 12/23/15
 * Time: 12:09 PM
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);
if (!isset($_GET['action'])) {
    echo json_encode(['error' => 1, 'message' => 'No parameter action.'], true);
    exit;
}
$action = $_GET['action'];
$pg_credentials = "host=ec2-107-21-223-110.compute-1.amazonaws.com port=5432 dbname=d9drs0g01eqeir user=xdmfdolmqushkf password=iBwpLgt1wIhrSZa5cPi7FIW_Op sslmode=require";
$db = pg_connect($pg_credentials);
if (!$db) {
    echo json_encode(['error' => 1, 'message' => 'Database connection error.']);
    exit;
}
switch ($action) {
    case 'login':
        $user = json_decode(file_get_contents("php://input"));
        checkUserData($user, $db);
        $result = pg_query_params($db, "SELECT * FROM tbl_User WHERE email = $1", [$user->email]);
        if (!$result) {
            pg_close($db);
            echo json_encode(['error' => 1, 'message' => 'An error has occurred when trying to find user.']);
            exit;
        }
        if (pg_num_rows($result) == 0) {
            pg_close($db);
            echo json_encode(['error' => 1, 'message' => 'No user with this email.']);
            exit;
        }
        $user_row = pg_fetch_array($result);
        if (!password_verify($user->password, $user_row['password'])) {
            pg_close($db);
            echo json_encode(['error' => 1, 'message' => 'Password incorrect.']);
            exit;
        }
        echo json_encode(['error' => 0, 'message' => 'Welcome ' . $user_row['email'], 'data' => $user_row]);
        break;
    case 'signup':
        $user = json_decode(file_get_contents("php://input"));
        checkUserData($user, $db);
        $result = pg_query_params($db, 'SELECT * FROM tbl_User WHERE email = $1', [$user->email]);
        if (!$result) {
            pg_close($db);
            echo json_encode(['error' => 1, 'message' => 'An error has occurred when trying to find user.']);
            exit;
        }
        if (pg_num_rows($result) != 0) {
            pg_close($db);
            echo json_encode(['error' => 1, 'message' => 'User already exists.']);
            exit;
        }
        $password = password_hash($user->password, PASSWORD_BCRYPT);
        $token = str_shuffle(MD5(microtime()));
        $result = pg_query_params($db, 'INSERT INTO tbl_user (email, password, token) VALUES  ($1,$2,$3)', [$user->email, $password, $token]);
        if (!$result) {
            pg_close($db);
            echo json_encode(['error' => 1, 'message' => 'An error has occurred when trying to sign up.']);
            exit;
        }
        $data = ['email' => $user->email, 'password' => $password, 'token' => $token];
        pg_close($db);
        echo json_encode(['error' => 0, 'message' => 'Sign up successfully', 'data' => $data]);
        exit;
        break;
    case 'getAnswer':
        if (!isset($_GET['token'])) {
            pg_close($db);
            echo json_encode(['error' => 1, 'message' => 'accessToken not found.']);
            exit;
        }
        $token = $_GET['token'];
        $result = pg_query_params($db, 'SELECT * FROM tbl_User WHERE token = $1', [$token]);
        if (!$result) {
            pg_close($db);
            echo json_encode(['error' => 1, 'message' => 'An error has occurred when trying to find user.']);
            exit;
        }
        if (pg_num_rows($result) === 0) {
            pg_close($db);
            echo json_encode(['error' => 1, 'message' => 'Only a believer pass. You will suffer the punishment of God, stranger...']);
            exit;
        }
        $user_row = pg_fetch_array($result);
        echo json_encode(['error' => 1, 'message' => 'Answer: Life is about "playing your best hand, with the cards you are dealt."']);
        pg_close($db);
        break;
    default:
        pg_close($db);
        echo json_encode(['error' => 1, 'message' => 'Unknown action.']);
}

function checkUserData($user, $db)
{
    if (!isset($user->email, $user->password)) {
        echo json_encode(['error' => 1, 'message' => 'User credentials not found.']);
        pg_close($db);
        exit;
    }
    if (!filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['error' => 1, 'message' => 'Email is invalid.']);
        pg_close($db);
        exit;
    }
}