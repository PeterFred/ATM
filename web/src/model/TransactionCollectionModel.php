<?php
namespace agilman\a2\model;
session_start();



/**
 * Class AccountCollectionModel
 *
 * @package agilman/a2
 * @author  Andrew Gilman <a.gilman@massey.ac.nz>
 */
class TransactionCollectionModel extends Model
{
    /**
     * @var array TransactionIds
     */
    private $transactionIds;
    private $user;

    /**
     * gets user current logged status
     * @return bool
     * Check to see if user is still logged in
     */
    public function getLoggedStatus()
    {
        return (new LoginModel())->checkLoggedState();
    }

    /**
     * TransactionCollectionModel constructor
     * @param $id AccountId
     * Checks login status
     * Retrieves all transactions associated with the passed in account.
     */
    public function __construct($id)
    {
        parent::__construct();
        $userModel = new UserModel();
        $loginModel = new LoginModel();

        if ($loginModel->checkLoggedState()) {
            $this->user = $loginModel->getUser();
            if ($userModel->load($this->user['id'])) {
                if ($this->user = (new LoginModel())->getUser()) {
                    if (!$result = $this->db->query("SELECT * FROM `transaction` WHERE `account_id_FK` = $id;")) {
                    }
                    $this->transactionIds = array_column($result->fetch_all(), 0);
                    $_SESSION['timeout'] = time();
                }
            }
        }
    }

    /**
     * Get transaction collection
     *
     * @return \Generator|UserModel[] Accounts
     */
    public function getTransactions()
    {
        foreach ($this->transactionIds as $id) {
            // Use a generator to save on memory/resources
            // load transactions from DB one at a time only when required
            yield (new TransactionModel())->load($id);
        }
    }

    /**
     * Get user
     * Finds the current user as stored in the cookie.
     * If the cookie is empty, returns false.
     * @return array
     */
    public function getUser()
    {
        $user = array();
        if (!$query = $this->db->query("SELECT * FROM `user` WHERE `id` = '" . $_COOKIE['userID'] . "';")) {
            error_log("User not logged in ", 0);
            return false;
        }
        $result = $query->fetch_assoc();
        $_SESSION['timeout'] = time();

        $user['id'] = (int)$result['id'];
        $user['firstName'] = $result['first_name'];
        $user['lastName'] = $result['last_name'];
        $user['email'] = $result['email'];
        $user['password'] = $result['password'];
        $user['balance'] = $result['balance'];
        return $user;
    }
}
