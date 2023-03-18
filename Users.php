<?php


class Users
{
    public $first_name;
    public $last_name;
    public $date_born;
    public $gender;
    public $city_born;
    /**
     * @var PDO
     */
    private $db;

    public function __construct($id = null, $first_name = null, $last_name = null, $date_born = null, $gender = null, $city_born = null)
    {
        $this->db = DataBase::getInstance();

        if (!empty($id)) {
            $user = $this->getUserById($id);
            if (!empty($user)) {
                $this->first_name = $user[0]['first_name'];
                $this->last_name = $user[0]['last_name'];
                $this->date_born = $user[0]['date_born'];
                $this->gender = $user[0]['gender'];
                $this->city_born = $user[0]['city_born'];
            }
        }

        if (!empty($first_name)) {
            $this->first_name = $first_name;
            $this->last_name = $last_name;
            $this->date_born = $date_born;
            $this->gender = $gender;
            $this->city_born = $city_born;
            $resValid = $this->validation();
            //var_dump($resValid);
            if ($resValid === 'success') {
                $this->create();
            }
        }

    }

    public function create()
    {
        $sql = "insert into users (first_name,last_name,date_born,gender,city_born) 
        values (:first_name,:last_name,:date_born,:gender,:city_born)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array(':first_name' => $this->first_name,
            ':last_name' => $this->last_name,
            ':date_born' => $this->date_born,
            ':gender' => $this->gender,
            ':city_born' => $this->city_born));
        return $this->db->lastInsertId();
    }

    public function delete($id): string
    {
        if (is_array($id)) {
            $id = implode(",",$id);
        }
        $sql = "delete from users where id in (".$id.")";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            return "User deleted with id  = $id .";
        } else {
            return "Cannot Delete user with id = $id .";
        }

    }

    public function edit($id): stdClass
    {
        $userObj = new stdClass();
        $user = $this->getUserById($id);
        if (!empty($user)){
            $userObj = new stdClass();
            $userObj->first_name = $user[0]['first_name'];
            $userObj->last_name = $user[0]['last_name'];
            $userObj->date_born = static::getAge($user[0]['date_born']);
            $userObj->gender = static::getGender($user[0]['gender']);
            $userObj->city_born = $user[0]['city_born'];
        }
        return $userObj;
    }

    public function getUserById($id)
    {
        if (is_array($id)) {
            $id = implode(",",$id);
        }
        $sql = "select * from users where id in (".$id.")";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchall();
    }


    public static function getAge($date_born): string
    {
        $current_date = date_create(date('Y-m-d'));
        $date_born = date_create($date_born);
        $interval = date_diff($date_born, $current_date);
        return $interval->format('%Y');
    }


    public static function getGender($gender): string
    {
        return $gender == 0 ? 'male' : 'female';
    }

    public function validation(): string
    {
        if (empty($this->first_name) || empty($this->last_name) || empty($this->date_born) || empty($this->gender) ||
            empty($this->city_born)) {
            return 'Data is empty. Type data.';
        } else {
            if (!preg_match("/^[A-z]/", $this->first_name)
                || !preg_match("/^[A-z]/", $this->last_name)) {
                return "Only letters are allowed.";
            }
            return 'success';
        }
    }

}