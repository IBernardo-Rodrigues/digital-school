<?php

require_once 'bootstrap/app.php';

use \App\Http\Router;

$router = new Router(URL);

require_once __DIR__.'/routes/allRoutes.php';

$router->run()->sendResponse();


// o run executa a closure que retorna um objeto response que tem como content 
//o conteudo retornado do controller
// O sendResponse() imprime o conteudo do Response::class