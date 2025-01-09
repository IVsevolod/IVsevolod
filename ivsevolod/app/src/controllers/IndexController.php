<?php
namespace App\Controllers;

use Phalcon\Assets\Filters\Cssmin;

class IndexController extends BaseController
{

    public function indexAction()
    {
        $this->assets->addCss('assets/css/main.css', false, Cssmin::class);
    }
}