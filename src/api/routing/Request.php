<?php


namespace api\routing;


class Request
{
    private string $path;
    private array $params;
    private int $type;

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
     * @return mixed
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

}