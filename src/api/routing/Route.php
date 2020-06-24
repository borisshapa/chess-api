<?php

namespace api\routing;

class Route
{
    const METHOD_GET = 1;
    const METHOD_POST = 2;
    const METHOD_PUT = 3;
    const METHOD_DELETE = 4;

    private $path;
    private $type;
    private $action;

    public function __construct($path, $type, $action)
    {
        $this->path = $path;
        $this->type = $type;
        $this->action = $action;
    }

    /**
     * @return mixed
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return mixed
     */
    public function getAction()
    {
        return $this->action;
    }
}