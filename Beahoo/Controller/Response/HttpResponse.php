<?php

namespace Beahoo\Controller\Response;

class HttpResponse extends \Beahoo\Controller\Response
{
    protected $headers = array();

    protected $body;

    public function getHeaders()
    {
        return $this->headers;
    }

    public function setHeaders(array $headers)
    {
        $this->headers = $headers;
    }

    public function getHeader($name, $default = null)
    {
        if (isset($this->headers[$name])) {
            return $this->headers[$name];
        }

        return $default;
    }

    public function setHeader($name, $value)
    {
        $this->headers[$name] = (array)$value;
    }

    public function addHeader($name, $value)
    {
        $this->headers[$name][] = $value;
    }

    public function hasHeader($name)
    {
        return isset($this->headers[$name]);
    }

    public function getBody()
    {
        return $this->body;
    }

    public function setBody($body)
    {
        $this->body = $body;
    }

    public function send()
    {
        foreach ($this->headers as $name => $values) {
            foreach ($values as $key => $value) {
                header("{$name}: {$value}", $key == 0);
            }
        }

        if (is_array($this->body)) {
            $this->body = json_encode($this->body);
        }

        echo $this->body;
    }
}
