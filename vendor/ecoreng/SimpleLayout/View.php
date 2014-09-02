<?php

namespace ecoreng\SimpleLayout;

class View extends \Slim\View
{

    public $viewFile = '';
    public $layoutFile = '';

    public function __construct()
    {
        parent::__construct();
        $this->setData(array(
            'pageTitle' => '',
            'pageStylesheets' => '',
            'pageScripts' => '',
            'jsBuffer' => '',
            'template' => '',
        ));
    }

    public function view($template)
    {
        $this->setData('template', preg_replace('/[^a-z0-9]/i', '', basename($template)));
        $this->viewFile = $template;
        return $this;
    }

    public function layout($template)
    {
        $this->setData('layout', preg_replace('/[^a-z0-9]/i', '', basename($template)));
        $this->layoutFile = $template;
        return $this;
    }

    public function render($template, $data = NULL)
    {
        if ($template != '') {
            $this->view($template);
        }
        if (is_readable($this->viewFile) && is_readable($this->layoutFile)) {

            // render inner view
            $vars = $this->data->all();
            unset($vars['flash']);
            extract($vars);
            ob_start();
            require ($this->viewFile);
            $buffer = ob_get_contents();
            ob_end_clean();
            $this->buffer = $buffer;
            
            // render layout and place view inside
            // variable $buffer
            ob_start();
            require ($this->layoutFile);
            $buffer = ob_get_contents();
            ob_end_clean();

            return $buffer;
        } else {
            throw new \Exception('Missing View: ' . $this->viewFile . ' or Layout: ' . $this->layoutFile);
        }
    }

}
