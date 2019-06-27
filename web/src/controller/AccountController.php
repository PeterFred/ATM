<?php
namespace agilman\a2\controller;
session_start();

use agilman\a2\model\{AccountModel, AccountCollectionModel};
use agilman\a2\view\View;

/**
 * Class AccountController
 *
 * @package agilman/a2
 */
class AccountController extends Controller
{

    /**
     * Account Index action
     * Displays a list of user accounts
     */
    public function indexAction()
    {
        $collection = new AccountCollectionModel();
        $accounts = $collection->getAccounts();
        echo (new View('accountsIndex'))
            ->addData('collection', $collection)
            ->addData('accounts', $accounts)
            ->render();
    }

    /**
     * Account Created Action
     * Creates a user account, then displays a success page
     */
    public function createdAction()
    {
        $collection = new AccountCollectionModel();
        $user = (new AccountCollectionModel())->getUser();
        (new AccountModel())->createAccount($user['id']);
        echo (new View('accountCreated'))
            ->addData('collection', $collection)
            ->addData('user', $user)
            ->render();
    }

    /**
     * Account Delete action
     * @param int $id Account id to be deleted
     * Loads the correct account, deletes the transactions for that account, then deletes the account
     */
    public function deleteAction($id)
    {
        $collection = new AccountCollectionModel();
        ( new AccountModel() )-> load($id) -> delete();
        echo (new View('accountDeleted'))
            ->addData('collection', $collection)
            ->addData('accountId', $id)
            ->render();
    }
}
