<?php
namespace Api;

Class Router
{
    static private $instance;
    static private $isRouteAvailable;
    public function __construct()
    {
        self::$isRouteAvailable = true;
    }
    static private function i()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        if (!self::$isRouteAvailable) {
            exit();
        }
    }
    static private function match($route, $requestMethod)
    {
        if ($_SERVER['REQUEST_METHOD'] != $requestMethod)
            return; // Not the good method (GET/POST)
        $formatedRoute = preg_replace('/{[a-zA-Z0-9_-]+}/', '[a-zA-Z0-9_-]+', $route);
        $formatedRoute = '/^' . str_replace('/', '\/', $formatedRoute) . '$/';
        // prevent $_GET values giving wrong url;
        $url = explode('?', $_SERVER['REQUEST_URI'])[0];
        preg_match($formatedRoute, $url, $match);
        if (empty($match))
            return; // Not the good route
        self::$isRouteAvailable = false;
        // Does route expect params ?
        preg_match_all('/{[a-zA-Z0-9_-]+}/', $route, $hasParams);
        $hasParams = !empty($hasParams[0]) ? $hasParams[0] : [];
        if (empty($hasParams))
            return true; // No params, but route still valid
        // If user expect params
        $params = explode('/', $route);
        $values = explode('/', $url);
        for ($i = 0; $i < count($params); $i++) {
            if ($params[$i] !== $values[$i]) {
                $param = str_replace(['{', '}'], '', $params[$i]);
                $_GET[$param] = $values[$i];
            }
        }
        // "it's a me, match-io!";
        return true;
    }
    static private function callback($callback = null)
    {
        if (!$callback)
            return;
        // If user callback is a function
        if ($callback && gettype($callback) == "object") {
            call_user_func($callback);
            http_response_code(200);
            return;
        }
        // If user gives Controller#method
        if ($callback && gettype($callback) == "string") {
            $callbackArgs = explode("#", $callback);
            $controller = $callbackArgs[0];
            $method = $callbackArgs[1];
            if (!class_exists($controller))
                throw new ErrorException('Class ' . $controller . " does not exists.");
            $class = new $controller();
            if (!method_exists($controller, $method))
                throw new ErrorException('Method ' . $method . " does not belong to controller " . $controller);
            call_user_func(array($class, $method));
            return;
        }
        throw new ErrorException('Callback given doesn\'t match with expected case. It must be a function or a Controller#method');
    }
    /*Router method */
    static public function get($route = '', $callback = null)
    {
        self::i();
        $match = self::match($route, 'GET');
        if (!$match)
            return;
        self::callback($callback);
        http_response_code(200);
    }
    static public function post($route = '', $callback = null)
    {
        self::i();
        $match = self::match($route, 'POST');
        if (!$match)
            return;
        self::callback($callback);
        http_response_code(200);
    }
    static public function error404($callback = null)
    {
        self::i();
        self::callback($callback);
        http_response_code(404);
    }
}
