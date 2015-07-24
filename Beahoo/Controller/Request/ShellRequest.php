<?php

namespace Beahoo\Controller\Request;

class ShellRequest extends \Beahoo\Controller\Request
{
    public function getArg($name, $default = null)
    {
        $short = null;
        $long  = null;

        if (strlen($name) > 1) {
            $long = array($name . '::');
        } else {
            $short = $name . '::';
        }

        $options = getopt($short, $long);

        if (isset($options[$name])) {
            return $this->getArgFromArgs($name, $default, $options);
        }

        return $this->getServerArg($name, $default);
    }
}
