<?php


namespace api\routing;


class Router
{
    private static $routes = [];
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function getContent()
    {
        $execRoute = null;
        foreach (self::$routes as $route) {
            echo preg_match($route->getPath(), $this->request->getPath());
            if (preg_match($route->getPath(), $this->request->getPath())
                && $route->getType() == $this->request->getType()) {
                $execRoute = $route;
            }
        }

        if ($execRoute) {
            $action = explode('@', $execRoute->getAction());
            if (isset($action[0]) && isset($action[1])) {
                $controllerName = "app\mvc\controllers\\" . $action[0];
                $methodName = $action[1];

                echo $controllerName;
                $controller = new $controllerName();

                if (method_exists($controller, $methodName)) {
                    $rm = new \ReflectionMethod($controllerName, $methodName);
                    $params = $this->request->getGetParams();
                    var_dump($params);
                    return $rm->invokeArgs($controller, $params);
                }
                return "Method " . $methodName . " not found";
            } else {
                return "Action is not ok" . $execRoute->getAction();
            }
        }
        return "404";
    }

    public static function addRoute(Route $route)
    {
        array_push(self::$routes, $route);
    }
}