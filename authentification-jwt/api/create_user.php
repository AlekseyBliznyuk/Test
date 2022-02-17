<?php
//Требуемые заголовки для приёма данных JSON
header("Access-Control-Allow-Origin: http://authentication-jwt/");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

//соединение с БД
//файлы, необходимые для подключения к БД
include_once 'config/database.php';
include_once 'objects/user.php';

//соединение с БД
$database = new DataBase();
$db = $database->getConnection();

//создание объекта 'User'
$user = new User($db);

//получение данных
$data = json_decode(file_get_contents("php://input"));

$user->login = $data->login;
$user->password = $data->password;
$user->email = $data->email;
$user->name = $data->name;

//создание пользователя
if (
    !empty($user->login) &&
    !empty($user->password) &&
    !empty($user->email) &&
    !empty($user->name) &&
    $user->create()
) {
    //код ответа
    http_response_code(200);

    //пользователь создан
    echo json_encode(array("message" => "Пользователь был создан."));
}

//если не удаётся создать пользователя
else {

    //код ответа
    http_response_code(400);

    //сообщение
    echo json_encode(array("message" => "Невозможно создать пользователя."));
}
