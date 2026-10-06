<?php

namespace Api\Server;

use Slim\App;
use Psr\Http\Message\ServerRequestInterface;
use Api\Http\ErrorResponse;

use Api\Routes\ParticipanteRouter;
use Api\Routes\PartidaRouter;

class Server
{
    private App $app;

    private ParticipanteRouter $participanteRouter;
    private PartidaRouter $partidaRouter;

    public function __construct(
        App $app,
        ParticipanteRouter $participanteRouter,
        PartidaRouter $partidaRouter
    ) {
        error_log("Server::__construct()");

        $this->app = $app;

        $this->participanteRouter = $participanteRouter;
        $this->partidaRouter = $partidaRouter;

        $this->setupMiddlewares();
        $this->setupRoutes();
        $this->setupErrorHandling();
    }

    private function setupMiddlewares(): void
    {
        // Permite que o Slim leia JSON enviado no body
        $this->app->addBodyParsingMiddleware();

        // CORS
        $this->app->add(function ($request, $handler) {

            $response = $handler->handle($request);

            return $response
                ->withHeader('Access-Control-Allow-Origin', '*')
                ->withHeader(
                    'Access-Control-Allow-Methods',
                    'GET, POST, PUT, DELETE, OPTIONS'
                )
                ->withHeader(
                    'Access-Control-Allow-Headers',
                    'Content-Type'
                );
        });
    }

    private function setupRoutes(): void
    {
        // Rotas de participantes
        $this->participanteRouter->setupRoutes();

        // Rotas de partidas
        $this->partidaRouter->setupRoutes();

        // Rota inicial
        $this->app->get('/', function ($request, $response) {

            $caminho = __DIR__ . '/../../../public/index.html';

            if (!file_exists($caminho)) {

                $response->getBody()->write(
                    'index.html não encontrado.'
                );

                return $response->withStatus(404);
            }

            $conteudo = file_get_contents($caminho);

            $response->getBody()->write($conteudo);

            return $response
                ->withHeader('Content-Type', 'text/html; charset=UTF-8')
                ->withStatus(200);
        });
    }

    private function setupErrorHandling(): void
    {
        $errorMiddleware = $this->app->addErrorMiddleware(
            true,
            true,
            true
        );

        $errorMiddleware->setDefaultErrorHandler(
            function (
                ServerRequestInterface $request,
                \Throwable $exception
            ) {

                $response = new \Slim\Psr7\Response();

                $status = 500;

                if ($exception instanceof ErrorResponse) {

                    $payload = [
                        'success' => false,
                        'message' => $exception->getMessage(),
                        'error' => $exception->getError() ?? (object) []
                    ];

                    $status = $exception->getHttpCode();

                } elseif ($exception instanceof \Slim\Exception\HttpException) {

                    $status = $exception->getCode();
                    $payload = [
                        'success' => false,
                        'message' => $exception->getMessage(),
                        'error' => (object) []
                    ];

                } else {

                    $payload = [
                        'success' => false,
                        'message' => $exception->getMessage(),
                        'error' => [
                            'code' => $exception->getCode(),
                            'file' => $exception->getFile(),
                            'line' => $exception->getLine()
                        ]
                    ];
                }

                $response->getBody()->write(
                    json_encode(
                        $payload,
                        JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
                    )
                );

                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus($status);
            }
        );
    }

    public function run(): void
    {
        $this->app->run();
    }
}