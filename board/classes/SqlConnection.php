<?php

    //namespace board\classes;

    class SqlConnection
    {
        protected $db_server = "localhost";
        protected $db_table = "message_board";
        protected $db_user = "tester";
        protected $db_pw = "test5566";

        public function dbConnection()
        {
            $mysqli = new \mysqli($this->db_server, $this->db_user, $this->db_pw, $this->db_table);
            if ($mysqli->connect_errno) {
                printf("Connect failed: %s\n", $mysqli->connect_error);
                exit();
            } else {
                return $mysqli;
            }
        }
    }
?>
