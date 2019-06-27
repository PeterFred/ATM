<?php

namespace agilman\a2\controller;
session_start();

/**
 * Class Controller
 *
 * @package agilman/a2
 */
class Controller
{
    /**
     * Redirect to another route
     *
     * @param $route
     * @param array $params
     */
    public function redirect($route, $params = [])
    {
        // Generate a redirect url for a given named route
        $url = $GLOBALS['router']->generate($route, $params);
        header("Location: $url");
    }
}
