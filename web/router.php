<?php
use PHPRouter\RouteCollection;
use PHPRouter\Router;
use PHPRouter\Route;

$collection = new RouteCollection();

//Redirect to login page
$collection->attachRoute(
    new Route(
        '/',
        array(
            '_controller' => 'agilman\a2\controller\LoginController::indexAction',
            'methods' => 'GET',
            'name' => 'Home'
        )
    )
);


//--------------------------------------------------
//Login routes
$collection->attachRoute(
    new Route(
        '/login/',
        array(
            '_controller' => 'agilman\a2\controller\LoginController::indexAction',
            'methods' => 'GET',
            'name' => 'loginIndex'
        )
    )
);

$collection->attachRoute(
    new Route(
        '/login/logout',
        array(
            '_controller' => 'agilman\a2\controller\LoginController::logoutAction',
            'methods' => 'POST',
            'name' => 'logout'
        )
    )
);

$collection->attachRoute(
    new Route(
        '/login/validation',
        array(
            '_controller' => 'agilman\a2\controller\LoginController::validateAction',
            'methods' => 'POST',
            'name' => 'loginValidation'
        )
    )
);



//--------------------------------------------------
//User routes

$collection->attachRoute(
    new Route(
        '/user/',
        array(
        '_controller' => 'agilman\a2\controller\UserController::indexAction',
        'methods' => 'GET',
        'name' => 'userIndex'
        )
    )
);

$collection->attachRoute(
    new Route(
        '/user/create/',
        array(
            '_controller' => 'agilman\a2\controller\UserController::createAction',
            'methods' => 'GET',
            'name' => 'userCreate'
        )
    )
);

//New User Creation
$collection->attachRoute(
    new Route(
        '/user/userCreated',
        array(
            '_controller' => 'agilman\a2\controller\UserController::newUserAction',
            'methods' => 'POST',
            'name' => 'userCreated'
        )
    )
);

$collection->attachRoute(
    new Route(
        '/user/delete/:id',
        array(
        '_controller' => 'agilman\a2\controller\UserController::deleteAction',
        'methods' => 'GET',
        'name' => 'userDelete'
        )
    )
);

$collection->attachRoute(
    new Route(
        '/user/update/:id',
        array(
            '_controller' => 'agilman\a2\controller\UserController::updateAction',
            'methods' => 'GET',
            'name' => 'userUpdate'
        )
    )
);

//--------------------------------------------------
//Account routes
$collection->attachRoute(
    new Route(
        '/accounts/delete/:id',
        array(
            '_controller' => 'agilman\a2\controller\AccountController::deleteAction',
            'methods' => 'GET',
            'name' => 'accountDelete' //SORT THIS ONE
        )
    )
);


$collection->attachRoute(
    new Route(
        '/accounts/created/',
        array(
            '_controller' => 'agilman\a2\controller\AccountController::createdAction',
            'methods' => 'GET',
            'name' => 'accountCreated'
        )
    )
);


$collection->attachRoute(
    new Route(
        '/accounts/',
        array(
            '_controller' => 'agilman\a2\controller\AccountController::indexAction',
            'methods' => 'GET',
            'name' => 'accountIndex'
        )
    )
);


//--------------------------------------------------
//Transaction routes


$collection->attachRoute(
    new Route(
        '/transactions/:id',
        array(
            '_controller' => 'agilman\a2\controller\TransactionController::indexAction',
            'methods' => 'GET',
            'name' => 'transactionsIndex'
        )
    )
);


$collection->attachRoute(
    new Route(
        '/transaction/transact/',
        array(
            '_controller' => 'agilman\a2\controller\TransactionController::transactAction',
            'methods' => 'POST',
            'name' => 'transactTransaction'
        )
    )
);




$router = new Router($collection);
$router->setBasePath('/');
