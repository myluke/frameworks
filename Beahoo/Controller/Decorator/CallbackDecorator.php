<?php

namespace Beahoo\Controller\Decorator;

use Beahoo\Controller\Request;
use Beahoo\Controller\Response;

/**
 * 前端JSONP回调函数装饰器
 */
class CallbackDecorator extends \Beahoo\Controller\Decorator
{
    /**
     * 执行
     *
     * @param \Beahoo\Controller\Request $request
     * @param \Beahoo\Controller\Response $response
     *
     * @result void
     */
    public function execute(Request $request, Response $response)
    {
        parent::execute($request, $response);

        $callback = $request->getQueryArg('callback');

        if ($callback && preg_match('/^[\._a-z0-9]+$/i', $callback)) {
            $body = $response->getBody();

            if (is_array($body)) {
                $body = json_encode($body);
            }

            $body = "{$callback}({$body})";

            $response->setBody($body);
        }
    }
}
