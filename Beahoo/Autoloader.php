<?php
/**
* $Id: Autoloader.php 2305 2014-12-15 03:04:53Z hepenghui $
*/

namespace Beahoo;

include __DIR__ . '/defined.php';

class Autoloader
{
    protected $extensions = array('.php');

    protected $namespaces = array(
        __NAMESPACE__ => array(__DIR__),
    );

    public function addExtension($extension)
    {
        $this->extensions[] = $extension;
    }

    public function addNamespace($namespace, $dir)
    {
        $this->namespaces[$namespace][] = realpath($dir);
    }

    public function register()
    {
        spl_autoload_register(array($this, 'loadClass'));
    }

    public function unregister()
    {
        spl_autoload_unregister(array($this, 'loadClass'));
    }

    protected function loadClass($class)
    {
        $prefix = $class;

        while (($position = strrpos($prefix, '\\')) !== false) {
            $prefix = substr($class, 0, $position);

            if (!isset($this->namespaces[$prefix])) {
                continue;
            }

            $path = substr($class, $position + 1);
            $path = str_replace('\\', DIRECTORY_SEPARATOR, $path);

            foreach ($this->namespaces[$prefix] as $dir) {
                foreach ($this->extensions as $extension) {
                    $file = $dir . DIRECTORY_SEPARATOR . $path . $extension;

                    if (is_readable($file) && (include $file)) {
                        return;
                    }
                }
            }
        }
    }
}
