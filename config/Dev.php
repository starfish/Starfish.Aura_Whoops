<?php
namespace Starfish\Whoops\_Config;

use Aura\Di\Config;
use Aura\Di\Container;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

class Dev extends Config
{
    protected $handler;
    protected $whoops;

    public function define(Container $di)
    {
        $this->whoops  = new Run;
        $this->handler = new PrettyPageHandler;
        $this->whoops->pushHandler($this->handler);
    }
    
    public function modify(Container $di)
    {
        $request = $di->get('aura/web-kernel:request');
        $this->handler->addDataTable('Aura', array(
            'Method'    => $request->method->get(),
            'Path'      => $request->url->get(PHP_URL_PATH),
            'Params'    => $request->params->get()
        ));
        $this->whoops->register();

        $dispatcher = $di->get('aura/web-kernel:dispatcher');
        
        // use 'action' from the route params
        $dispatcher->setObjectParam('action');
        
        // for when the kernel has caught an exception
        $dispatcher->setObject(
            'aura.web_kernel.caught_exception',
            function ($exception) {
                throw $exception;
            }
        );
    }
}
