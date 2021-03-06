<?php
namespace agilman\a2\controller;
session_start();

use agilman\a2\model\{UserModel, UserCollectionModel};
use agilman\a2\view\View;

/**
 * Class UserController
 *
 * @package agilman/a2
 */
class UserController extends Controller
{
    /**
     * User Index action
     * Gets the users from the database.
     * Displays them to the admin user.
     */
    public function indexAction()
    {
        $users = (new UserCollectionModel())->getUsers();
        echo (new View('userIndex'))
            ->addData('users', $users)
            ->render();
    }

    /**
     * User create action
     * Display the User Create view.
     */

    public function createAction()
    {
        echo (new View('userCreate'))->render();
    }

    /**
     * User newUser action
     * Creates a new user.
     * Displays the a user created success view.
     */
    public function newUserAction()
    {
        $user = (new UserModel())->createUser();
        echo (new View('userCreated'))
            ->addData('user', $user)
            ->render();
    }

    /**
     * User Delete action
     * @param int $id User id to be deleted.
     * Loads the passed in user, then deletes user.
     * Displays a deleted user success view.
     */
    public function deleteAction($id)
    {
        (new UserModel())->load($id)->delete();
        echo (new View('userDeleted'))
            ->addData('userId', $id)
            ->render();
    }
}
