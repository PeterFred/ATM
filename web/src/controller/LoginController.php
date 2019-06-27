<?php
namespace agilman\a2\controller;
session_start();

use agilman\a2\model\LoginModel;
use agilman\a2\view\View;

/**
 * Class LoginController
 *
 * @package agilman/a2
 */
class LoginController extends Controller
{
    /**
     * Login index action
     * DISPLAYS THE HOMEPAGE
     */
    public function indexAction()
    {
        echo (new View('loginIndex'))->render();
    }

    /**
     * Login validateAction
     * Takes user to the logged in page, after validating there login
     */
    public function validateAction()
    {
        $collection = new LoginModel();
        $user = (new LoginModel())->validateLogin();
        echo (new View('loginValidation'))
            ->addData('collection', $collection)
            ->addData('user', $user)
            ->render();
    }

    /**
     *  Login logoutAction
     *  Takes the user to the logout page, after logging them out
     */
    public function logoutAction()
    {
        (new LoginModel())->logout();
        echo (new View('logout'))->render();
    }
}
