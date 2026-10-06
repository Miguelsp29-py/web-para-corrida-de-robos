<?php

namespace Api\Routes;

use Slim\App;
use Api\Controllers\ResultadoController;

class ResultadoRouter
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
         * POST /resultados
         * =========================================================
         * Registra o resultado de um participante.
         */
        $this->app->post(
            '/resultados',
            [ResultadoController::class, 'createController']
        );

        /*
         * =========================================================
         * GET /resultados
         * =========================================================
         * Lista todos os resultados.
         */
        $this->app->get(
            '/resultados',
            [ResultadoController::class, 'findAllController']
        );

        /*
         * =========================================================
         * GET /resultados/partida/{partida_id}
         * =========================================================
         * Lista os resultados de uma determinada partida.
         */
        $this->app->get(
            '/resultados/partida/{partida_id}',
            [ResultadoController::class, 'findByPartidaController']
        );

        /*
         * =========================================================
         * GET /resultados/ranking
         * =========================================================
         * Retorna os 5 melhores tempos da competição.
         */
        $this->app->get(
            '/resultados/ranking',
            [ResultadoController::class, 'findRankingController']
        );
    }
}