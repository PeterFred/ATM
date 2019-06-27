<?php
namespace agilman\a2\model;
session_start();

/**
 * Class UserModel
 *
 * @package agilman/a2
 * @author  Andrew Gilman <a.gilman@massey.ac.nz>
 */
class UserModel extends Model
{
    /**
     * @var integer User ID
     */
    private $id;
    /**
     * @var string User first Name
     */
    private $firstName;

    /**
     * @var string user last name
     */
    private $lastName;

    /**
     * @var string user email
     */
    private $email;

    /**
     * @var string user password
     */
    private $password;

    /**
     * @return int User ID
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Gets users first name
     * @return string User Name
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Returns users last name
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * returns users email address
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * returns users hashed password
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Loads user information
     * @param int $id User ID
     * Loads the user information from the database.
     * Stores it locally.
     * @return $this UserModel
     */
    public function load($id)
    {
        if (!$result = $this->db->query("SELECT * FROM `user` WHERE `id` = $id;")) {
            error_log("Failed to load from database");
        }
        //If user exists, load the data
        if ($result = $result->fetch_assoc()) {
            $this->id = (int)$id;
            $this->firstName = $result['first_name'];
            $this->lastName = $result['last_name'];
            $this->email = $result['email'];
            $this->password = $result['password'];
        } else {
            //This user does not exist, return null
        }
        return $this;
    }

    /**
     * Saves user information
     * Checks if the current user exists locally, then saves to the database.
     * Saves new DB generated ID locally.
     * @return $this UserModel
     */
    public function save()
    {
        if (!isset($this->id)) {
            // New user - Perform INSERT
            if (!$result = $this->db->query("INSERT INTO `user` 
                VALUES (NULL,'$this->firstName', '$this->lastName', '$this->email', '$this->password');")) {
                // Handle appropiately
                error_log("ERROR USERMODEL::save() - Error in saving user to database");
            }
            $this->id = $this->db->insert_id;
        }
        return $this;
    }

    /**
     * Deletes a user account
     * Deletes user from the database

     * @return $this UserModel
     */
    public function delete()
    {

        if (!$results = $this->db->query("SELECT * FROM `account` WHERE `user_id_FK` =$this->id")) {
            error_log('Failed to select accounts');
        }

        foreach ($results as $result) {
            if (!$result = $this->db->query("DELETE FROM `transaction` 
                                          WHERE `transaction`.`account_id_FK` =  '" . $result['user_id_FK'] . "';")) {
                error_log("UserModel delete couldn't read record");
            }
        }

       //Delete all transactions related to the account from the transaction table
        if (!$result = $this->db->query("DELETE FROM `transaction` WHERE `transaction`.`account_id_FK` = $this->id;")) {
            error_log('Failed to delete transactions AccountModel::delete');
        }

        //Then delete the relevant accounts
        if (!$result = $this->db->query("DELETE FROM `account` WHERE `account`.`user_id_FK` = $this->id;")) {
            error_log('Failed to delete account AccountModel::delete');
        }

        //Then delete the relevant user
        if (!$result = $this->db->query("DELETE FROM `user` WHERE `user`.`id` = $this->id;")) {
            error_log("User not deleted from database");
        }

        return $this;
    }

    /**
     * Creates a new user.
     * Checks to see if user email exists. If so, returns user simply as false.
     * Validates form entry. Any errors are returned as info.
     * If there are no errors from the form data, it then saves locally.
     * Password is hashed so only hashed password is saved.
     * Current user is retrieved to return.
     * Any errors from form validation are returned as info.
     * @return array - on success (user details / no error) or errors (error info)
     */

    public function createUser()
    {
        $user = array();
        error_log('CREATING USER');
        if ($this->loadUserByEmail($_POST['email'])) {
            $user['error'] = 'true';
            $user['info'] = 'That email is already in use. Please check your account details.';
            return $user;
        }
        if (isset($_POST['submit'])) {
            $formData = $this->validateFormEntry();
            if ($formData['error'] == 'false') {
                $this->firstName = $_POST['firstName'];
                $this->lastName = $_POST['lastName'];
                $this->email = $_POST['email'];
                //Hash password to store in database
                $password = $_POST['password'];
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                error_log($password_hash);
                $this->password = $password_hash;
                //$this->balance = 0;
                $user = $this->getUser();
                $this->save();
                $user['error'] = 'false';
            } else {
                $user['error'] = 'true';
                $user['info'] = $formData['info'];
            }
        } else {
            $user['error'] = 'true';
            $user['info'] = "From incorrectly submitted";
        }
        return $user;
    }

    /**
     * Validates form entry
     * Checks for empty fields and incorrect characters.
     * Checks correct email format.
     * @return array info about any error message, and true for any errors.
     */
    public function validateFormEntry()
    {
        $info['error'] = 'false';
        //Check firstName
        if (empty($_POST["firstName"])) {
            $info['info'] = "First name is required";
            $info['error'] = 'true';
        } else {
            $firstName = $this->testInput($_POST["firstName"]);
            // check if name only contains letters and whitespace
            if (!preg_match("/^[a-zA-Z ]*$/", $firstName)) {
                $info['info'] = "Only letters and white space allowed";
                $info['error'] = 'true';
            }
        }

        //Check lastName
        if (empty($_POST["lastName"])) {
            $info['info'] = "Last name is required";
            $info['error'] = 'true';
        } else {
            $lastName = $this->testInput($_POST["lastName"]);
            // check if name only contains letters and whitespace
            if (!preg_match("/^[a-zA-Z ]*$/", $lastName)) {
                $info['info'] = "Only letters and white space allowed";
                $info['error'] = "true";
            }
        }

        //Check email
        if (empty($_POST["email"])) {
            $info['info'] = "Email is required";
            $info['error'] = 'true';
        } else {
            $email = $this->testInput($_POST["email"]);
            // check if email is well formed
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $info['info'] = "Invalid Email Format";
                $info['error'] = 'true';
            }
        }

        //Check password is not empty
        if (empty($_POST["password"])) {
            $info['info'] = "Password is required";
            $info['error'] = 'true';
        }
        return $info;
    }

    /**
     * Tests inputted data
     * Strips whitespace, slashes.
     * Takes out html specal chars (prevents malicious code).
     * @param $data form string
     * @return string Corrected string.
     */
    public function testInput($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    /**
     * Loads the current user into an array and returns it.
     */
    public function getUser()
    {
        $user['id'] = $this->id;
        $user['firstName'] = $this->firstName;
        $user['lastName'] = $this->lastName;
        $user['email'] = $this->email;
        $user['password'] = $this->password;
        return $user;
    }


    /**
     * Loads a user by passed in email address.
     * Finds the user in the DB, saves it locally, then returns it.
     * @param $email
     * @return $this|bool
     */
    public function loadUserByEmail($email)
    {
        if (!$result = $this->db->query("SELECT * FROM `user` WHERE `email` = '" . $email . "';")) {
            error_log("ERROR: USERMODEL::loadUserByEmail failed to load from database", 0);
        }
        $result = $result->fetch_assoc();
        if (!$result) {
            return false;  // No user found
        }
        $this->id = (int)$result['id'];
        $this->firstName = $result['first_name'];
        $this->lastName = $result['last_name'];
        $this->email = $email;
        $this->password = $result['password'];
        return $this;
    }
}
