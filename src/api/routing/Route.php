<?php

namespace api\routing;

use api\Controller;

/**
 * Class Route
 * The route connecting the request to api and the method that processes it.
 * @package api\routing
 * @see Router
 * @see Request
 * @author Boris Shaposhnikov bshaposhnikov01@gmail.com
 */
class Route
{
    /**
     * The route accepts GET requests.
     */
    const METHOD_GET = 1;
    /**
     * The route accepts POST requests.
     */
    const METHOD_POST = 2;
    /**
     * The route accepts PUT requests.
     */
    const METHOD_PUT = 3;
    /**
     * The route accepts DELETE requests.
     */
    const METHOD_DELETE = 4;

    private string $path;
    private int $type;
    private string $action;

    /**
     * Route constructor.
     * @param string $path the path along which to send the request so that it is processed by the appropriate method.
     * @param int $type type of requests that route takes (GET - 1, POST - 2, PUT - 3, DELTE - 4)
     * @param string $action method in the format '{@see Controller}<code>@method</code>' that processes the request
     */
    public function __construct(string $path, int $type, string $action)
    {
        $this->path = $path;
        $this->type = $type;
        $this->action = $action;
    }

    /**
     * @return string the path to access the appropriate action.
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return string type of requests that route takes (GET - 1, POST - 2, PUT - 3, DELTE - 4)
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string method in the format '{@see Controller}<code>@method</code>' that processes the request
     */
    public function getAction(): string
    {
        return $this->action;
    }
}