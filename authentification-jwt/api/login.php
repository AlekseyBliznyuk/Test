<?php
//заголовки
header("Access-Control-Allow-Origin: http://authentication-jwt/");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

//соединение с БД
include_once 'config/database.php';
include_once 'objects/user.php';

$database = new Database();
$db = $database->getConnection();

//создание объекта 'User
$user = new User($db);

//получаем данные
$data = json_decode(file_get_contents("php://input"));

$user->login = $data->login;
$login_exists = $user->loginExists();

//подключение jwt
include_once 'config/core.php';
include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';
use \Firebase\JWT\JWT;

if($login_exists && password_verify($data->password, $user->password)) {

    $token = array(
        "iss" => $iss,
        "aud" => $aud,
        "iat" => $iat,
        "nbf" => $nbf,
        "data" => array(
            "id" => $user->id,
            "login" => $user->login,
            "email" => $user->email,
            "name" => $user->name
        )
    );

    http_response_code(200);

    $jwt = JWT::encode($token,$key);
    echo json_encode(
        array(
            "message" => "Успешный вход в систему.",
            "jwt" => $jwt
        )
    );
}

else {
    http_response_code(401);

    // сказать пользователю что войти не удалось
    echo json_encode(array("message" => "Ошибка входа."));
}
