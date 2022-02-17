<?php
//объект 'user'
class User {

    //подключение к таблице 'users'
    private $conn;
    private $table_name = "users";

    //свойства
    public $id;
    public $login;
    public $password;
    public $email;
    public $name;

    public function __constructor($db) {
        $this->conn = $db;
    }

    //создание пользователя
    function create() {

        //Вставка
        $query = "INSERT INTO " . $this->table_name . "
                SET
                    login = :login,
                    password = :password,
                    email = :email,
                    name = :name";
        $stmt = $this->conn->prepare($query);

        //инъекция
        $this->login=htmlspecialchars(strip_tags($this->login));
        $this->password=htmlspecialchars(strip_tags($this->password));
        $this->email=htmlspecialchars(strip_tags($this->email));
        $this->name=htmlspecialchars(strip_tags($this->name));

        //привязка
        $stmt->bindParam(':login', $this->login);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':name', $this->name);

        //хэширование пароля
        $password_hash = password_hash($this->password, PASSWORD_BCRYPT);
        $stmt->bindParam(':password', $this->password);

        //если выполнен запрос, информация о пользователе сохранится
        if($stmt->execute()) {
            return true;
        }

        return false;
    }
    //проверка существования логина
    function loginExists(){
        //запрос
        $query = "SELECT id,login,password,email,name FROM " . $this->table_name . "WHERE login = ?
        LIMIT 0,1";
        //подготовка запроса
        $stmt = $this->conn->prepare($query);
        $this->login=htmlspecialchars(strip_tags($this->login));
        $stmt->bindParam(1,$this->login);
        $stmt->execute();
        $num = $stmt->rowCount();
        if($num>0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            $this->id = $row['id'];
            $this->login = $row['login'];
            $this->password = $row['password'];
            $this->email = $row['email'];
            $this->name = $row['name'];
            return true;
        }
        return false;
    }
    public function update() {
        $password_set=!empty($this->password) ? ", password = :password" : "";
        $query = "UPDATE " . $this->table_name . "
            SET
                login = :login,
                email = :email,
                name = :name
                {$password_set}
            WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        // инъекция (очистка)
        $this->login=htmlspecialchars(strip_tags($this->login));
        $this->email=htmlspecialchars(strip_tags($this->email));
        $this->name=htmlspecialchars(strip_tags($this->name));

        $stmt->bindParam(':login', $this->login);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':name', $this->name);

        if(!empty($this->password)){
            $this->password=htmlspecialchars(strip_tags($this->password));
            $password_hash = password_hash($this->password, PASSWORD_BCRYPT);
            $stmt->bindParam(':password', $password_hash);
        }

        // уникальный идентификатор записи для редактирования
        $stmt->bindParam(':id', $this->id);

        // Если выполнение успешно, то информация о пользователе будет сохранена в базе данных
        if($stmt->execute()) {
            return true;
        }

        return false;
    }
}
