<?php

namespace Api\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Api\Http\ErrorResponse;
use Api\Services\PartidaService;

class PartidaController
{
    private PartidaService $partidaService;


    public function __construct(
        PartidaService $partidaServiceDependency
    ) {
        error_log("PartidaController::__construct()");

        $this->partidaService = $partidaServiceDependency;
    }


    /**
     * =========================================================
     * POST /partidas
     * =========================================================
     * Cria uma nova partida.
     */
    public function createController(
        Request $request,
        Response $response,
        array $args
    ): Response {

        error_log("PartidaController::createController()");

        $objPHP = json_decode($request->getBody()->getContents());

        if (!$objPHP instanceof \stdClass) {
            throw new ErrorResponse(
                400,
                "Corpo da requisição inválido",
                [
                    "message" => "Envie um objeto JSON válido."
                ]
            );
        }

        $novaPartida =
            $this->partidaService->createService($objPHP);


        $resposta = [
            'success' => true,
            'message' => 'Partida registrada com sucesso',
            'data' => [
                'partidas' => [
                    [
                        'id' => $novaPartida->getId(),
                        'nomeparticipante1' =>
                            $novaPartida->getNomeParticipante1(),
                        'nomeparticipante2' =>
                            $novaPartida->getNomeParticipante2()
                    ]
                ]
            ]
        ];


        $response->getBody()->write(
            json_encode($resposta)
        );


        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(201);
    }


    /**
     * =========================================================
     * GET /partidas
     * =========================================================
     * Lista todas as partidas.
     */
    public function findAllController(
        Request $request,
        Response $response,
        array $args
    ): Response {

        error_log("PartidaController::findAllController()");


        $partidas =
            $this->partidaService->findAllService();


        $resposta = [
            'success' => true,
            'message' => 'Busca realizada com sucesso',
            'data' => [
                'partidas' => $partidas
            ]
        ];


        $response->getBody()->write(
            json_encode($resposta)
        );


        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }


    /**
     * =========================================================
     * GET /partidas/{id}
     * =========================================================
     * Busca uma partida pelo ID.
     */
    public function findByIdController(
        Request $request,
        Response $response,
        array $args
    ): Response {

        error_log("PartidaController::findByIdController()");


        $id = (int) $args['id'];


        $partida =
            $this->partidaService->findByIdService($id);


        $resposta = [
            'success' => true,
            'message' => 'Busca realizada com sucesso',
            'data' => [
                'partidas' => [
                    $partida
                ]
            ]
        ];


        $response->getBody()->write(
            json_encode($resposta)
        );


        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
}
