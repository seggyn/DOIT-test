<?php


class DB
{
    private static $instance = null;
    private $conn;

    private static $DB_HOST = '';
    private static $DB_NAME = '';
    private static $DB_USER = '';
    private static $DB_PASS = '';

    public function __construct()
    {
        $this->conn = new PDO(
            'mysql:host=' . self::$DB_HOST . ';dbname=' . self::$DB_NAME,
            self::$DB_USER, self::$DB_PASS,
            [PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"]
        );
    }

    public static function getInstance()
    {
        if(!self::$instance)
        {
            self::$instance = new self;
        }
        return self::$instance;
    }

    public static function connect($host, $db_name, $user, $password) {
        self::$DB_HOST = $host;
        self::$DB_NAME = $db_name;
        self::$DB_USER = $user;
        self::$DB_PASS = $password;
        return self::getInstance();
    }

    private function combine_array_keys($array) {
        return array_map(function ($x) {return $x.'=:'.$x;}, array_keys($array));
    }

    private function add_where($sql, $array) {
        if (!empty($array)) {
            $where = $this->combine_array_keys($array);
            $sql .= ' WHERE '.implode(' AND ', $where);
        }
        return $sql;
    }

    private function add_order_by($sql, $array) {
        if (!empty($array)) {
            $temp = array_map(function ($key, $value) {
                return "$key $value";
            }, array_keys($array), $array);
            $sql .= ' ORDER BY '.implode(',', $temp);
        }
        return $sql;
    }

    public function query($sql, $array) {
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($array);
        $result = $stmt->fetchAll();
        return $result;
    }

    public function select($table, $array, $where = [], $order_by = []) {
        $sql = 'SELECT '.implode(',', $array).' FROM '.$table;
        $sql = $this->add_where($sql, $where);
        $sql = $this->add_order_by($sql, $order_by);
        $stmt = $this->conn->prepare($sql);
        if (!empty($where))
            $stmt->execute($where);
        else
            $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $result = $stmt->fetchAll();
        return $result;
    }

    public function select_one($table, $array, $where = []) {
        $result = $this->select($table, $array, $where);
        return (empty($result)) ? [] : $result[0];
    }

    public function insert($table, $array) {
        $into = implode(',', array_keys($array));
        $temp = array_map(function($x) {return ':'.$x;}, array_keys($array));
        $values = implode(',', $temp);
        $sql = "INSERT INTO $table ($into) VALUES ($values)";
        $this->conn->prepare($sql)->execute($array);
    }

    public function update($table, $array, $where = []) {
        $temp = $this->combine_array_keys($array);
        $sql = 'UPDATE '.$table.' SET '.implode(',', $temp);
        $sql = $this->add_where($sql, $where);
        $data = array_merge($array, $where);
        $this->conn->prepare($sql)->execute($data);
    }

    public function delete($table, $where) {
        $sql = 'DELETE FROM '.$table;
        $sql = $this->add_where($sql, $where);
        $this->conn->prepare($sql)->execute($where);
    }
}