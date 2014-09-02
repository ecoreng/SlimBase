<?php

namespace ecoreng\SlimBase;

use \ecoreng\SlimBase\PluginRouter as PR;

abstract class Controller
{

    /**
     * Plugin path
     * @var string 
     */
    public $plugin;

    /**
     * Controller Name
     * @var string 
     */
    public $controller;

    /**
     * Controller method called
     * @var string 
     */
    public $method;

    /**
     * Get the plugin path from the route closure
     */
    public function __construct()
    {
        $app = \Slim\Slim::getInstance();
        $ns = $app->router->getCurrentRoute()->getCallable();
        $ns = new \ReflectionFunction($ns);
        $ns = $ns->getStaticVariables();
        $this->method = $ns['method'];
        $ns = $ns['class'];
        $ns = explode('\\Controller\\', $ns);
        $this->controller = $ns[1];
        $this->plugin = $ns[0];
        $app->applyHook('ecoreng.plugin.beforeAction', array(
            'plugin' => $this->plugin
                )
        );
        $callee = array(
            'method' => $this->method,
            'controller' => $this->controller,
            'plugin' => $this->plugin
        );
        $app->hook('slim.after.dispatch', function() use ($app, $callee) {
            if (empty($app->view->viewFile)) {
                // called plugin
                $plugin = explode('\\', $callee['plugin']);
                $plugin['namespace'] = $plugin[1];
                $plugin['name'] = $plugin[2];

                // default view
                $default = $app->config('defaultLayout');

                $app->view->layout(PR::view($default[0], $default[1], $default[2]));
                $app->render(PR::view($plugin['namespace'], $plugin['name'], $callee['method']));
            }
        });
    }

}
