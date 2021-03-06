<?php
namespace agilman\a2\model;
session_start();

use mysqli;

/**
 * Class Model
 *
 * @package agilman/a2
 * @author  Andrew Gilman <a.gilman@massey.ac.nz>
 */
class Model
{
    protected $db;

    // is this the best place for these constants?
    const DB_HOST = 'mysql';
    const DB_USER = 'root';
    const DB_PASS = 'root';
    const DB_NAME = 'fredatovichHood_a2';

    /**
     * Model constructor.
     * Creates a new DB
     */
    public function __construct()
    {

        /**
         * The database to cretae in SQL
         */
        $this->db = new mysqli(
            Model::DB_HOST,
            Model::DB_USER,
            Model::DB_PASS
            //            Model::DB_NAME
        );

        if (!$this->db) {
            error_log("Error connecting to mySQL");
        }

        //----------------------------------------------------------------------------
        // Create Database
        $this->db->query("CREATE DATABASE IF NOT EXISTS " . Model::DB_NAME . ";");

        if (!$this->db->select_db(Model::DB_NAME)) {
            error_log("Mysql database not available!", 0);
        }
        //----------------------------------------------------------------------------

        //----------------------------------------------------------------------------
        //Create User Table
        $user = $this->db->query("SHOW TABLES LIKE 'user';");

        if ($user->num_rows == 0) {
            ///If table doesn't exist create it
            $user = $this->db->query(
                "CREATE TABLE `user` ( 
									`id` INT(8) NOT NULL AUTO_INCREMENT , 
									`first_name` VARCHAR(20) NOT NULL , 
									`last_name` VARCHAR(20) NOT NULL , 
									`email` VARCHAR(80) NOT NULL , 
									`password` VARCHAR(100) NOT NULL , 
									
									PRIMARY KEY (`id`)) ENGINE = InnoDB CHARSET=utf8 COLLATE utf8_general_ci;"
            );

            if (!$user) {
                error_log("Failed creating table user", 0);
            }

            if (!$this->db->query(
                "INSERT INTO `user` VALUES 
                          (NULL,'admin', 'admin', 'admin', 'N/A'),
                          (NULL,'Bob', 'Jones', 'bob@gmail.com', 'N/A'), 
                          (NULL,'Mary',  'Antoinette', 'mary@gmail.com', 'N/A'), 
                          (NULL,'jim',  'Jones', 'jim@gmail.com', 'N/A');"
            )) {
                // handle appropriately
                error_log("Failed creating user sample data!", 0);
            }
        }
        //----------------------------------------------------------------------------


        //----------------------------------------------------------------------------
        //Create account Table
        $account = $this->db->query("SHOW TABLES LIKE 'account';");

        if ($account->num_rows == 0) {
            //If table doesn't exist create it
            $account = $this->db->query(
                "CREATE TABLE `account` (
                            `account_id` int(11) NOT NULL AUTO_INCREMENT,
                            `user_id_FK` int(11),
                            `balance` decimal(11) NOT NULL,
                            PRIMARY KEY (`account_id`),
                            FOREIGN KEY (`user_id_FK`) REFERENCES `user` (`id`)
                            ON DELETE CASCADE 
                            ON UPDATE CASCADE
                        )ENGINE = INNODB;"
            );

            if (!$account) {
                // handle appropriately
                error_log("Failed creating table account", 0);
            }

            //Insert sample data
            if (!$this->db->query(
                "INSERT INTO `account` (`account_id`, `user_id_FK`, `balance`)
                        VALUES
                          (1,1,500.00),
                          (2,1,1500.00),
                          (3,2,222.00),
                          (4,3,999.00),
                          (5,3,1010.00),
                          (6,2,-100.00),
                          (7,1,10.00),
                          (8,3,800.00),
                          (9,2,70.00),
                          (10,2,20.00);"
            )) {
                // handle appropriately
                error_log("Failed creating account sample data!", 0);
            }
        }
        //----------------------------------------------------------------------------



        //----------------------------------------------------------------------------
        //Create transaction Table
        $transaction = $this->db->query("SHOW TABLES LIKE 'transaction';");

        if ($transaction->num_rows == 0) {
            //If table doesn't exist create it
            $transaction = $this->db->query(
                "CREATE TABLE `transaction` (
                            `transaction_id` int(11) NOT NULL AUTO_INCREMENT,
                            `type` varchar(1) NOT NULL DEFAULT '',
                            `amount` decimal(11,2) NOT NULL,
                            `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                            `account_id_FK` int(11),                            
                            PRIMARY KEY (`transaction_id`),
                            FOREIGN KEY (`account_id_FK`) REFERENCES `account` (`account_id`)
                            ON DELETE CASCADE 
                            ON UPDATE CASCADE
                        )ENGINE = INNODB;"
            );

            if (!$transaction) {
                // handle appropriately
                error_log("Failed creating table transaction", 0);
            }

            //Insert sample data
            if (!$this->db->query(
                "INSERT INTO `transaction` (`transaction_id`, `type`, `amount`, `date`, `account_id_FK`)
                        VALUES
                      (1,'D',20.00,'2018-09-20 13:46:06',1),
                      (2,'W',5.00,'2018-09-20 13:46:27',3),
                      (3,'D',25.00,'2018-09-20 13:53:17',2),
                      (4,'D',30.00,'2018-09-20 13:53:35',4),
                      (5,'D',12.00,'2018-09-20 13:54:50',1),
                      (6,'W',56.00,'2018-09-20 13:55:17',3),
                      (7,'W',33.00,'2018-09-20 13:55:24',1),
                      (8,'D',3.00,'2018-09-20 13:55:24',2),
                      (9,'W',75.00,'2018-09-20 13:55:24',4),
                      (10,'D',13.00,'2018-09-20 13:55:24',6),
                      (11,'D',7.00,'2018-09-20 13:55:24',8),
                      (12,'D',23.00,'2018-09-20 13:55:24',10),
                      (13,'D',56.00,'2018-09-20 13:55:24',7),
                      (14,'D',21.00,'2018-09-20 13:55:24',9),
                      (15,'W',7.00,'2018-09-20 13:55:24',4),
                      (16,'W',13.00,'2018-09-20 13:55:24',1),
                      (17,'D',5.00,'2018-09-20 13:55:24',3),
                      (18,'W',13.00,'2018-09-20 13:55:24',5),
                      (19,'D',53.00,'2018-09-20 13:55:24',7),
                      (20,'D',9.00,'2018-09-20 13:55:24',9),
                      (21,'D',10.00,'2018-09-20 13:55:24',10),
                      (22,'D',4.00,'2018-09-20 13:55:24',7),
                      (23,'D',24.00,'2018-09-20 13:55:24',1),
                      (24,'D',57.00,'2018-09-20 13:55:24',7),
                      (25,'W',68.00,'2018-09-20 13:55:24',8),
                      (26,'D',35.00,'2018-09-20 13:55:24',5),
                      (27,'D',3.00,'2018-09-20 13:55:24',6),
                      (28,'D',23.00,'2018-09-20 13:55:24',9),
                      (29,'W',6.00,'2018-09-20 13:55:24',6),
                      (30,'W',76.00,'2018-09-20 13:55:24',4),
                      (31,'D',7.00,'2018-09-20 13:55:24',3),
                      (32,'W',3.00,'2018-09-20 13:55:24',8),
                      (33,'D',3.00,'2018-09-20 13:55:24',9),
                      (34,'D',56.00,'2018-09-20 13:55:24',1),
                      (35,'D',8.00,'2018-09-20 13:55:24',10),
                      (36,'D',75.00,'2018-09-20 13:55:24',5),
                      (37,'D',24.00,'2018-09-20 13:55:24',6),
                      (38,'D',5.00,'2018-09-20 13:55:24',8),
                      (39,'W',23.00,'2018-09-20 13:55:24',7),
                      (40,'W',9.00,'2018-09-20 13:55:24',9);"
            )) {
                // handle appropriately
                error_log("Failed creating transaction sample data!", 0);
            }
        }
        //----------------------------------------------------------------------------
    }
}
