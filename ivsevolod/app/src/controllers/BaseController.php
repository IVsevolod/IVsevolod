<?php
namespace App\Controllers;

use Phalcon\Mvc\Controller;

class BaseController extends Controller
{
    public function initialize()
    {
        $this->view->setLayout('main');
    }

}