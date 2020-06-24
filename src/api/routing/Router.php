<?php


namespace api\routing;


use const app\PATH_TO_CONTROLLERS;

class Router
{
    private static array $routes = array();
    private Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public static function successfulResponse(int $httpCode, array $response)
    {
        http_response_code($httpCode);
        $response["status"] = true;
        return json_encode($response);
    }

    public static function badResponse(int $httpCode, string $errorMessage) {
        http_response_code($httpCode);
        $response = [
            "status" => false,
            "message" => $errorMessage
        ];
        return json_encode($response);
    }

    public function getContent()
    {
        $execRoute = null;
        foreach (self::$routes as $route) {
            if (preg_match($route->getPath(), $this->request->getPath())
                && $route->getType() == $this->request->getType()) {
                $execRoute = $route;
            }
        }

        if ($execRoute) {
            $action = explode('@', $execRoute->getAction());
            if (isset($action[0]) && isset($action[1])) {
                $controllerName = PATH_TO_CONTROLLERS . $action[0];
                $methodName = $action[1];

                $controller = new $controllerName();

                if (method_exists($controller, $methodName)) {
                    $rm = new \ReflectionMethod($controllerName, $methodName);
                    $params = $this->request->getParams();
                    return $rm->invokeArgs($controller, $params);
                }
                return self::badResponse(400, "Method " . $methodName . " not found");
            } else {
                return self::badResponse(400, "Action is not ok: " . $execRoute->getAction());
            }
        }
        return self::badResponse(404, "No route found.");
    }

    public static function addRoute(Route $route)
    {
        array_push(self::$routes, $route);
    }
}