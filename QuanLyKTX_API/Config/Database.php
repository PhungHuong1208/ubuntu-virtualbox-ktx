<?php
namespace Config;

class Database {

    private static $connection = null;



    public static function getConnection() {

        if (self::$connection === null) {

            $host = 'db';       // Tên service MySQL trong docker-compose

            $user = 'root';

            $pass = 'root_password'; // Khớp với MYSQL_ROOT_PASSWORD trong docker-compose.yml

            $db   = 'quanlykytucxa';

            $port = 3306;        // Port mặc định MySQL nội bộ Docker



            self::$connection = new \mysqli($host, $user, $pass, $db, $port);



            if (self::$connection->connect_error) {

                die("Lỗi kết nối CSDL: " . self::$connection->connect_error);

            }

            self::$connection->set_charset("utf8");

        }

        return self::$connection;

    }

} 

