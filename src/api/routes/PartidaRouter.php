<?php

namespace Api\Routes;

use Slim\App;
use Api\Controllers\PartidaController;

class PartidaRouter
{
    private App $app;

    public function __construct(App $app)
    {
        $this->app = $app;
    }

    public function setupRoutes(): void
    {
        /*
         * =========================================================
         * POST /partidas
         * =========================================================
         * Cria uma nova partida.
         */
        $this->app->post(
            '/partidas',
            [PartidaController::class, 'createController']
        );

        /*
         * =========================================================
         * GET /partidas
         * =========================================================
         * Lista todas as partidas.
         */
        $this->app->get(
            '/partidas',
            [PartidaController::class, 'findAllController']
        );

        /*
         * =========================================================
         * GET /partidas/{id}
         * =========================================================
         * Busca uma partida pelo ID.
         */
        $this->app->get(
            '/partidas/{id}',
            [PartidaController::class, 'findByIdController']
        );
    }
}