<?php
namespace Geega\Micro\Tests;

use Geega\Micro\App;
use Geega\Micro\Http\Request;
use Geega\Micro\Http\Response;
use Geega\Micro\Router;
use PHPUnit\Framework\TestCase;


class AppTest extends TestCase
{
    public function testRun()
    {
        $this->markTestSkipped('Need refactoring');

        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/';
        $request = new Request();
        $router = new Router($request, '\\Geega\\Micro\\Tests\\AppTest\\Controller\\{{NAME}}Controller');
        $router->get('/', 'Main@actionTest');
        $app = new App($router, '');

        ob_start();
        $app->run();
        $content = ob_get_clean();
    }

    public function testCreateResponse()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/';
        $request = new Request();
        $router = new Router($request, '\\Geega\\Micro\\Tests\\AppTest\\Controller\\{{NAME}}Controller');
        $app = new App($router, '');

        $response = $app->createResponse();

        $this->assertInstanceOf(Response::class, $response);
    }
}