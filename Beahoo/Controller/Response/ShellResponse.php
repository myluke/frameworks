<?php

namespace Beahoo\Controller\Response;

class ShellResponse extends \Beahoo\Controller\Response
{
    public function send($body = null)
    {
        echo $body, "\n";
    }
}
