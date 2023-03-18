<?php


class DataBase
{
    public static $instance;

    static public function getInstance($file = null) {

        if(!isset(self::$instance)) {
            $db_ini = !empty($file) ? $file : __DIR__.'/db/localhost.ini';
            $db_conf = parse_ini_file($db_ini, TRUE);

            $settings = array('database' => $db_conf['database']);

            $dsn = $settings['database']['driver'] . ':host=' . $settings['database']['host'] .
                ((!empty($settings['database']['port'])) ? (';port=' . $settings['database']['port']) : '') .
                ';dbname=' . $settings['database']['schema'];

            self::$instance = new PDO($dsn, $settings['database']['username'], $settings['database']['password'], array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''));
            self::$instance->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            self::$instance->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        return self::$instance;
    }
}