<?php

namespace Beahoo\Controller;

abstract class Decorator extends Action
{
    protected $action;

    public function __construct(Action $action)
    {
        $this->action = $action;
    }

    public function getNextAction()
    {
        return $this->action;
    }

    public function getLastAction()
    {
        $action = $this->action;

        while (is_subclass_of($action, __CLASS__)) {
            $action = $action->action;
        }

        return $action;
    }

    public function execute(Request $request, Response $response)
    {
        $this->action->execute($request, $response);
    }
}
