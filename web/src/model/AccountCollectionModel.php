<?php
namespace agilman\a2\model;
session_start();

/**
 * Class AccountCollectionModel
 *
 * @package agilman/a2
 */
class AccountCollectionModel extends Model
{
    /**
     * @var array Stores all account ID's
     */
    private $accountIds;

    /**
     * @var array Stores User details
     */
    private $user;


    /**
     * get current Logged status
     * @return bool
     * Check to see if user is still logged in
     */
    public function getLoggedStatus()
    {
        return (new LoginModel())->checkLoggedState();
    }

    /**
     * AccountCollectionModel constructor.
     * Checks user is logged in, and retrieves user info.
     * Queries DB to retrieve and store all associated accounts.
     */
    public function __construct()
    {
        parent::__construct();
        $userModel = new UserModel();
        $loginModel = new LoginModel();
        if ($loginModel->checkLoggedState()) {
            $this->user = $loginModel->getUser();
            if ($userModel->load($this->user['id'])) {
                if (!$result = $this->db->query("SELECT `account_id`
                                                    FROM `account`
                                                    WHERE `user_id_FK` = " . $this->user['id'] . ";")) {
                    error_log("Error retrieving from Account DB");
                }
                $_SESSION['timeout'] = time();
                $this->accountIds = array_column($result->fetch_all(), 0);
            }
        }
    }

    /**
     *  Get account collection
     *  Retreives all associated accounts with the user, as required.
     * @return \Generator|UserModel[] Accounts
     */
    public function getAccounts()
    {
        foreach ($this->accountIds as $id) {
            // Use a generator to save on memory/resources
            // load accounts from DB one at a time only when required
            yield (new AccountModel())->load($id);
        }
    }

    /**
     * Get current User
     * Retrieves current user from DB for use in account controller.
     * @return array
     */
    public function getUser()
    {
        $user = array();

        if (!$query = $this->db->query("SELECT * FROM `user` WHERE `id` = '" . $_COOKIE['userID'] . "';")) {
            // handle appropriately
            error_log("User not logged in ", 0);
        }
        $result = $query->fetch_assoc();

        $user['id'] = (int)$result['id'];
        $user['firstName'] = $result['first_name'];
        $user['lastName'] = $result['last_name'];
        $user['email'] = $result['email'];
        $user['password'] = $result['password'];
        $user['balance'] = $result['balance'];
        $_SESSION['timeout'] = time();
        return $user;
    }

    /**
     * get total account balance.
     * Retrieves all associated account balances, and calculates the total balance.
     * @return int $balance is the total account(s) balance.
     */
    public function getTotalBalance()
    {
        $balance = 0;
        foreach ($this->accountIds as $id) {
            $balance += (new AccountModel())->load($id)->getBalance($id);
        }
        return $balance;
    }
}
