<?php

namespace Beahoo\Controller\Decorator;

use Beahoo\Controller\Request;
use Beahoo\Controller\Response;

/**
 * 结果集装饰器
 */
class ResultDecorator extends \Beahoo\Controller\Decorator
{
    /**
     * 选项
     *
     * @var array $options
     */
    protected $options = array();

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
        try {
            parent::execute($request, $response);
        } catch (\Exception $e) {
            ob_start();
            $code = $e->getCode();
            $msg = $e->getMessage() . "<br/>" . PHP_EOL;
            $msg.= $e->getTraceAsString();
            include ROOT . "/app/View/layout/error.phtml";
            echo ob_get_clean();
            exit;
        }
    }

}
