<?php


namespace api\routing;

/**
 * Class Request
 * Data about the request received by api
 * @package api\routing
 * @see Route
 * @see Router
 */
class Request
{
    private string $path;
    private array $params;
    private int $type;

    /**
     * Request constructor.
     */
    public function __construct()
    {
        $this->path = $_GET["path"];
        $this->params = $_GET;
        unset($this->params["path"]);

        switch ($_SERVER["REQUEST_METHOD"]) {
            case "POST":
                $this->type = Route::METHOD_POST;
                $this->params = $_POST;
                break;
            case "GET":
                $this->type = Route::METHOD_GET;
                break;
            case "PUT":
                $this->type = Route::METHOD_PUT;
                break;
            case "DELETE":
                $this->type = Route::METHOD_DELETE;
                break;
        }
    }

    /**
     * @return string request path
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return array GET parameters without 'path' if the request is GET, PUT or DELTE-request, POST parameters if the request is POST
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * @return int information about request type
     * - 1 — GET
     * - 2 — POST
     * - 3 — PUT
     * - 4 — DELETE
     *
     * @see Route
     */
    public function getType(): int
    {
        return $this->type;
    }

}