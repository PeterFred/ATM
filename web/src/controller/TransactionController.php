<?php
namespace agilman\a2\controller;
session_start();

use agilman\a2\model\{TransactionModel, TransactionCollectionModel};
use agilman\a2\view\View;

/**
 * Class TransactionController
 *
 * @package agilman/a2
 */
class TransactionController extends Controller
{
    /**
     * Transaction Index action
     * @param int $id Account id relevant to transactions.
     * Retrieves transactions and current user.
     * Displays the transactions for that user.
     */
    public function indexAction($id)
    {
        $collection = new TransactionCollectionModel($id);
        $transactions = $collection->getTransactions();
        $user = $collection->getUser();
        $accountBalance = (new TransactionModel())->getAccountBalance($id);

        echo (new View('transactionsIndex'))
            ->addData('transactions', $transactions)
            ->addData('id', $id)
            ->addData('user', $user)
            ->addData('accountBalance', $accountBalance)
            ->addData('collection', $collection)
            ->render();
    }

    /**
     * Transaction transactAction
     * Creates a new transaction, verifies, then saves to DB.
     * Displays transaction created page.
     *
     */
    public function transactAction()
    {
        $transactionModel = (new TransactionModel());
        $transaction = $transactionModel->createTransaction();

        echo (new View('transactionCreated'))
            ->addData('transactionModel', $transactionModel)
            ->addData('transaction', $transaction)
            ->render();
    }

    /**
     * Transaction loadAction
     * @param $id Transaction ID to load.
     * Loads the transaction passed in.
     * Displays the Trasnsaction Index.
     */
    public function loadAction($id)
    {
        (new TransactionModel())->load($id);//->display();
        echo (new View('transactionsIndex'))
            ->addData('transactionId', $id)
            ->render();
    }
}
