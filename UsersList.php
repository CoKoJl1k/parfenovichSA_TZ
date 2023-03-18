<?php


class UsersList
{

    public $usersId = array();
    /**
     * @var PDO
     */
    private $db;

    public function __construct($search_str = null)
    {
        $this->db = DataBase::getInstance();
        if (!empty($search_str)) {
            $this->getUsersId($search_str);
            $this->usersId = $this->getUsersId($search_str);
        }

    }

    public function getUsersId($search_str): array
    {
        $sql = "select id from users
            where INSTR(id,:str_id) > 0
               or INSTR(first_name,:str_fn) > 0
               or INSTR(last_name,:str_ln) > 0
               or INSTR(date_born,:str_db) > 0
               or INSTR(gender,:str_g) > 0
               or INSTR(city_born,:str_cb) > 0";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array('str_id' => $search_str,
            'str_fn' => $search_str,
            'str_ln' => $search_str,
            'str_db' => $search_str,
            'str_g' => $search_str,
            'str_cb' => $search_str));

        $usersId = array();
        while ($row = $stmt->fetch()) {
            $usersId[] = $row['id'];
        }
        return $usersId;
    }

    /**
     * @throws Exception
     */
    public function getUsers($usersId)
    {
        if (class_exists('Userss')) {
            $user = new Users();
            return $user->getUserById($usersId);
        } else {
            throw new Exception('CLASS doesnt exist !');
        }
    }


    public function deleteUsers($usersId): string
    {
        $user = new Users();
        return $user->delete($usersId);
    }
}