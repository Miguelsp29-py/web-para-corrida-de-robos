<?php

namespace Api\Routes;

use Slim\App;
use Api\Controllers\ParticipanteController;

class ParticipanteRouter
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
         * POST /participantes
         * =========================================================
         * Cadastra um novo participante.
         */
        $this->app->post(
            '/participantes',
            [ParticipanteController::class, 'createController']
        );

        /*
         * =========================================================
         * GET /participantes
         * =========================================================
         * Lista todos os participantes.
         */
        $this->app->get(
            '/participantes',
            [ParticipanteController::class, 'findAllController']
        );

        /*
         * =========================================================
         * GET /participantes/{id}
         * =========================================================
         * Busca um participante pelo ID.
         */
        $this->app->get(
            '/participantes/{id}',
            [ParticipanteController::class, 'findByIdController']
        );
    }
}