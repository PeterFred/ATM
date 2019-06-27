<?php
namespace agilman\a2\model;
session_start();

/**
 * Class AccountModel
 *
 * @package agilman/a2
 * @author  Andrew Gilman <a.gilman@massey.ac.nz>
 */
class LoginModel extends Model
{
    /**
     * Validates login attempt
     * Receives the POST email entry and checks for correct email form.
     * Checks if user exists in DB. No user loads error message.
     * Retrieves hashed password from DB, and checks against entered password.
     * NOTE 4 DUMMY DATA ENTRIES SKIP PASSWORD CHECK
     * If successful, cookie is set, session starts and user is loaded locally.
     * If admin, info is loaded to take to admin page.
     * False logins return relevant error info.
     *
     * @return array Success = current user | Fail = error message
     */
    public function validateLogin()
    {

        $user = array();

        if (filter_has_var(INPUT_POST, "submit")) {
            $string = filter_var($_POST['email']);
            $email = trim(filter_var($string, FILTER_SANITIZE_EMAIL));
            $userModel = new UserModel();
            if (!$userModel->loadUserByEmail($email)) { //Loads userModel whether condition true or false
                $user['info'] = "User does not exist.";
            } else {
                $passwordFromDB = $userModel->getPassword();

                //If statement checks supplied password against DB hashed password
                //NOTE for simplicity, the 4 dummy models are granted access irrelevant of password entry
                if (password_verify($_POST['password'], $passwordFromDB) ||
                        $userModel->getId() == 1 || $userModel->getId() == 2 ||
                        $userModel->getId() == 3 || $userModel->getId() == 4) {
                    setcookie("userID", $userModel->getId(), time() + 3600, "/");
                    $_SESSION['timeout'] = time();
                    $_SESSION['userID'] = crypt($this->user['id']);
                    $user['id'] = $userModel->getId();
                    $user['firstName'] = $userModel->getFirstName();
                    $user['lastName'] = $userModel->getLastName();
                    $user['email'] = $userModel->getEmail();
                    $user['password'] = $userModel->getPassword();

                    if (($userModel->getEmail() == "admin")) { //Special login for admin
                        $user['info'] = "admin";
                    }
                } else {
                    $user['info'] = "Incorrect password.";
                }
            }
        } else {
            $user['info'] = "Your login (email) is invalid";
        }
        return $user;
    }

    /**
     * Gets current user
     * Loads the current locally user based on the cookie user ID.
     * @return array Current user details
     */
    public function getUser()
    {
        $user = array();
        if (!$query = $this->db->query("SELECT * FROM `user` WHERE `id` = '" . $_COOKIE['userID'] . "';")) {
            error_log("User not logged in ", 0);
            return false;
        }
        $result = $query->fetch_assoc();
        $user['id'] = (int)$result['id'];
        $user['firstName'] = $result['first_name'];
        $user['lastName'] = $result['last_name'];
        $user['email'] = $result['email'];
        $user['password'] = $result['password'];
        return $user;
    }

    /**
     * Checks the session time out (set for 10 minutes)
     * Checks if cookie is set
     * Returns false if timeout / unset cookie, true if not timed out / cookie set.
     */
    public function checkLoggedState()
    {
        $sessionTTL = time() - $_SESSION["timeout"];
        if ($sessionTTL > 600 && !empty($_COOKIE['userID'])) {
            $this->unset();
            return false;
        }
        return true;
    }

    public function unset()
    {
        unset($_SESSION['timeout']);
        setcookie("userID", '', time() - 360, '/');
    }

    /**
     * Destroys the session.
     * Un-sets and clears the session array.
     * Gets the cookie parameters to then overwrite.
     * Set time to previous timestamp, destroying it immediately.     *
     * @return bool If successfully destroyed returns true.
     */
    public function logout()
    {
        session_unset();
        $_SESSION = array();
        $cookieParameters = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 360,
            $cookieParameters['path'],
            $cookieParameters['domain'],
            $cookieParameters['secure'],
            $cookieParameters['httponly']
        );
        return session_destroy();
    }
}
