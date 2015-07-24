<?php

namespace Beahoo\Controller\Decorator;

use Beahoo\Controller\Request;
use Beahoo\Controller\Response;
use Beahoo\Tool\Config;

/**
 * 日志装饰器
 */
class LogDecorator extends \Beahoo\Controller\Decorator
{
    /**
     * 内容
     *
     * @var array $decorators
     */
    protected $content = array();

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

        $file = Config::read('decorator.log.file') . '.' . date('Ymd');

        $content = date('H:i:s') . "\t" . get_class($this->getLastAction());

        if ($this->content) {
            $content .= ("\t" . json_encode($this->content));
        }

        file_put_contents($file, $content . "\n", FILE_APPEND);
    }

    public function add(array $data)
    {
        $this->content += $data;
    }
}
