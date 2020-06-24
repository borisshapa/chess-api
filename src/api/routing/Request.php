<?php


namespace api\routing;


class Request
{
    private $path;
    private $getParams;
    private $postParams;
    private $type;

    public function __construct()
    {
        $this->path = $_GET['path'];
        $this->getParams = $_GET;
        unset($this->getParams['path']);
        $this->postParams = $_POST;

        $requestMethod = $_SERVER['REQUEST_METHOD'];
        if ($requestMethod === 'POST') {
            $this->type = Route::METHOD_POST;
        }
        if ($requestMethod === 'GET') {
            $this->type = Route::METHOD_GET;
        }
    }

    /**
     * @return mixed
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return array
     */
    public function getGetParams(): array
    {
        return $this->getParams;
    }

    /**
     * @return array
     */
    public function getPostParams(): array
    {
        return $this->postParams;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

}