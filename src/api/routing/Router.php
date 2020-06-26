<?php


namespace api\routing;


use ReflectionException;
use ReflectionMethod;
use const app\PATH_TO_CONTROLLERS;

/**
 * Class Router
 * Upon request, it finds a suitable route and performs the corresponding action.
 * Returns the result or error message in JSON format.
 * @package api\routing
 * @api
 * @see Route
 * @see Request
 * @author Boris Shaposhnikov bshaposhnikov01@gmail.com
 */
class Router
{
    private static array $routes = array();
    private Request $request;

    /**
     * Router constructor.
     * @param Request $request received request to api
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Creates a JSON with a message about the successful processing of the request.
     * @param int $httpCode response code (expected to have format <var>2xx</var>)
     * @param array $response dictionary with a response to the request.
     * The method adds a key-value pair<var>"status" : true</var> to the dictionary.
     * @return false|string a JSON encoded string on success or <b>FALSE</b> on failure.
     */
    public static function successfulResponse(int $httpCode, array $response)
    {
        http_response_code($httpCode);
        $response["status"] = true;
        return json_encode($response);
    }

    /**
     * Creates a JSON with a message about an error.
     * @param int $httpCode response code (expected to have format <var>4xx</var>)
     * @param string $errorMessage dictionary with an error message.
     * The method adds a key-value pair<var>"status" : false</var> to the dictionary.
     * @return false|string a JSON encoded string on success or <b>FALSE</b> on failure.
     */
    public static function badResponse(int $httpCode, string $errorMessage)
    {
        http_response_code($httpCode);
        $response = [
            "status" => false,
            "message" => $errorMessage
        ];
        return json_encode($response);
    }

    /**
     * Returns the result of processing the request passed to the constructor or error message in JSON format.
     * If it was not possible to find a suitable route or some parameters are missing, a bad answer is returned.
     * @return false|mixed|string a JSON encoded string on success or <b>FALSE</b> on failure.
     */
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
                    try {
                        $rm = new ReflectionMethod($controllerName, $methodName);
                    } catch (ReflectionException $e) {
                        return self::badResponse(400, "Unsupported controller or method");
                    }
                    $params = $this->request->getParams();
                    $pass = array();
                    foreach ($rm->getParameters() as $param) {
                        $passedParam = &$params[$param->getName()];
                        if (isset($passedParam)) {
                            array_push($pass, $passedParam);
                        } else {
                            try {
                                array_push($pass, $param->getDefaultValue());
                            } catch (ReflectionException $e) {
                                return self::badResponse(400, "The query parameter was not found.");
                            }
                        }
                    }
                    return $rm->invokeArgs($controller, $pass);
                }
                return self::badResponse(400, "Method " . $methodName . " not found");
            } else {
                return self::badResponse(400, "Action is not ok: " . $execRoute->getAction());
            }
        }
        return self::badResponse(404, "No route found.");
    }

    /**
     * Adds a route to the list of supported ones.
     * Upon receipt of a suitable request, the <var>Router</var> will call the necessary method,
     * passed to this {@see Route} constructor.
     * @param Route $route route parameters
     * @see Route
     */
    public static function addRoute(Route $route): void
    {
        array_push(self::$routes, $route);
    }
}