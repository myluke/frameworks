<?php

namespace Beahoo\Controller\Decorator;

use Beahoo\Controller\Request;
use Beahoo\Controller\Response;
use Beahoo\Exception;

require_once ROOT . '/vendor/loginsdk/login.php';
/**
 * 认证装饰器
 *
 * @package K7659\Controller\Decorator
 */
class AuthDecorator extends \Beahoo\Controller\Decorator
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
        if(empty($_SESSION['uid'])){
            $user = \IntraLogin::login();
            $_SESSION['uid'] = $user['uid'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['tel'] = $user['tel'];
            $_SESSION['sign'] = $user['sign'];
        }
        parent::execute($request, $response);
    }
}
