<?php

namespace App;

use Phalcon\Di\FactoryDefault;
use Phalcon\Autoload\Loader;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Router;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Http\Response;
use Phalcon\Http\Request;
use Phalcon\Mvc\Model\Manager as ModelsManager;
use Phalcon\Mvc\Application as BaseApplication;
use Phalcon\Mvc\Model\Metadata\Memory as MemoryMetaData;

class Application extends BaseApplication
{
    protected function registerAutoloaders()
    {
        $loader = new Loader();

        $loader->setNamespaces(
            [
                'App\Controllers' => '../src/controllers/',
                'App\Models'      => '../src/models/'
            ]
        );

        $loader->register();
    }

    /**
     * This methods registers the services to be used by the application
     */
    protected function registerServices()
    {
        $di = new FactoryDefault();

        // Registering a router
        $di->set('router', function () {
            $router = new Router();
            return $router;
        });

        // Registering a dispatcher
        $di->set('dispatcher', function () {
            $dispatcher = new Dispatcher();
            $dispatcher->setDefaultNamespace('App\Controllers\\');

            return $dispatcher;
        });

        // Registering a Http\Response
        $di->set('response', function () {
            return new Response();
        });

        // Registering a Http\Request
        $di->set('request', function () {
            return new Request();
        });

        // Registering the view component
        $di->set('view', function () {
            $view = new View();
            $view->setViewsDir('../src/views/');

            return $view;
        });

//        $di->set('db', function () {
//            return new Database(
//                [
//                    "host"     => "localhost",
//                    "username" => "root",
//                    "password" => "",
//                    "dbname"   => "invo"
//                ]
//            );
//        });

        // Registering the Models-Metadata
        $di->set('modelsMetadata', function () {
            return new MemoryMetaData();
        });

        // Registering the Models Manager
        $di->set('modelsManager', function () {
            return new ModelsManager();
        });

        $this->setDI($di);
    }

    public function main()
    {
        $this->registerServices();
        $this->registerAutoloaders();

        $response = $this->handle($this->request->getURI());

        $response->send();
    }
}

try {
    $application = new Application();
    $application->main();
} catch (\Exception $e) {
    echo $e->getMessage();
}