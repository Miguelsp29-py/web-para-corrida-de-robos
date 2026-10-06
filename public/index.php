<?php

require __DIR__ . '/../vendor/autoload.php';

use DI\ContainerBuilder;
use Slim\Factory\AppFactory;

use Api\Database\MysqlDatabase;
use Api\Server\Server;


// ============================================================
// CONFIGURAÇÃO DO CONTAINER
// ============================================================

$builder = new ContainerBuilder();

$builder->useAutowiring(true);


// ============================================================
// CONEXÃO COM O BANCO DE DADOS
// ============================================================

$mysqlDatabase = new MysqlDatabase([
    'host' => 'localhost',
    'user' => 'root',
    'password' => '',
    'database' => 'competicaoRobosAPI',
]);


// ============================================================
// CRIAÇÃO DO CONTAINER
// ============================================================

$container = $builder->build();

$container->set(
    MysqlDatabase::class,
    $mysqlDatabase
);


// ============================================================
// CRIAÇÃO DA APLICAÇÃO SLIM
// ============================================================

AppFactory::setContainer($container);

$app = AppFactory::create();


// ============================================================
// CAMINHO BASE DA APLICAÇÃO
// ============================================================



// Coloca a aplicação Slim dentro do container
// para que o Server possa recebê-la por injeção de dependência.
$container->set(
    \Slim\App::class,
    $app
);


// ============================================================
// INICIALIZAÇÃO DO SERVER
// ============================================================

$server = $container->get(Server::class);

$server->run();