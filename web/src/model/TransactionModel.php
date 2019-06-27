<?php
namespace agilman\a2\model;
session_start();

/**
 * Class AccountModel
 *
 * @package agilman/a2
 * @author  Andrew Gilman <a.gilman@massey.ac.nz>
 */
class TransactionModel extends Model
{
    /**
     * @var integer Account ID
     */
    private $transactionId;

    /**
     * @var int AccountIDFK
     */
    private $accountIDFK;
    /**
     * @var string Account Name
     */
    private $type;
    /**
     * @var int amount of transaction
     */
    private $amount;
    /**
     * @var datestamp of transaction
     */
    private $date;

    /**
     * Gets account ID
     * @return int Account ID
     */
    public function getId()
    {
        return $this->transactionId;
    }

    /**
     * Gets account name
     * @return string Account Name
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * gets account amount
     * @return int transaction amount
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Gets current datestamp
     * @return datestamp transaction date stamp
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * gtes current accountID
     * @return int account ID FK
     */
    public function getAccountID()
    {
        return $this->accountIDFK;
    }

    /**
     * gets user current logged status
     * @return bool check if user is logged in
     */
    public function getLoggedStatus()
    {
        return (new LoginModel())->checkLoggedState();
    }



    /**
     * Loads account information from the database
     *
     * @param int $id transaction ID
     *
     * @return $this TransactionModel
     */
    public function load($id)
    {
        if (!$result = $this->db->query("SELECT * FROM `transaction` WHERE `transaction_id` = $id;")) {
            error_log("TransactionModel :: Failed to load from transactions database");
        }
        if ($result = $result->fetch_assoc()) {
            $this->transactionId = $result['transaction_id'];
            $this->type = $result['type'];
            $this->amount = $result['amount'];
            $this->date = $result['date'];
            $this->accountIDFK['account_id_FK'];
        } else {
            error_log('TransactionsModel load error');
        }
        return $this;
    }

    /**
     * Gets all transactions from the database as array.
     *Iterates through each one, totalling the amounts.
     * Updates the Account table with the new balance
     * @param $accountID
     * @return int total balance of all transactions
     */
    public function getAccountBalance($accountID)
    {
        if (!$result = $this->db->query("SELECT `type`,`amount` 
                  FROM `transaction` WHERE `account_id_FK` =$accountID")) {
            error_log("Error in loading transaction amounts from database");
        }
        $balance = 0;
        foreach ($result as $transaction) {
            if ($transaction['type'] == 'D') {
                $balance += $transaction['amount'];
            } else {
                $balance -= $transaction['amount'];
            }
        }
        if (!$result = $this->db->query("UPDATE account set balance = '" . $balance . "' 
        WHERE account_id = '" . $accountID . "';")) {
            error_log("Error in updating the balance to the Account table");
        }



        return $balance;
    }


    /**
     * Saves the current transaction to the database
     * @return $this transaction
     */
    public function save()
    {
        if (!$result = $this->db->query("INSERT INTO `transaction` 
                VALUES (NULL,'$this->type', '$this->amount', NULL, $this->accountIDFK)")) {
            error_log("Error in saving transaction to transaction database");
        }
        return $this;
    }

    /**
     * Performs the deposit or withdrawal transaction.
     * Receives the type of transaction (D/W) from the form.
     * Then saves it to the database.
     * @return bool true for successful save, false otherwise
     */
    public function transact()
    {
        if ($_POST['type'] == 'D') {
            $transaction['accountId'] = $_POST['accountId'];
            $this->type = $_POST['type'];
            $this->amount = $_POST['amount'];
            if ($this->save()) {
                return true;
            }
        } elseif ($_POST['type'] == 'W') {
            $transaction['accountId'] = $_POST['accountId'];
            $this->type = $_POST['type'];
            $this->amount = $_POST['amount'];
            if ($this->save()) {
                return true;
            }
        }
        return false;
    }


    /**
     * Creates the transaction
     * Receives account id and amount from the form.
     * Checks input, and then executes transaction.
     * Any errors returned as info to be displayed to the user.
     * @return array containing status of transaction success.
     */
    public function createTransaction()
    {
        $transaction['accountId'] = $_POST['accountId'];
        $this->accountIDFK = $_POST['accountId'];

        if (isset($_POST['submit'])) {
            if ($_POST['amount'] != 0) {
                if ($this->testInput($_POST['amount'])) {
                    if ($this->transact()) {
                        $transaction["info"] = "success";
                    } else {
                        $transaction["info"] = "Transaction error. Transaction not completed";
                    }
                } else {
                    $transaction['info'] = "You must enter a numerical number.";
                }
            } else {
                $transaction["info"] = "The transaction had no amount and was not completed.";
            }
        } else {
            error_log("Transaction not completed.");
        }
        return $transaction;
    }

    /**
     * Tests inputted data
     * Strips whitespace, slashes.
     * Takes out html specal chars (prevents malicious code).
     * Check only a number is inserted.
     * @param $data form string (amount)
     * @return string Corrected string.
     */
    public function testInput($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        $data = is_numeric($data);
        return $data;
    }
}
