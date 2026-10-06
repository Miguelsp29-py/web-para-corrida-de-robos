<?php

namespace Api\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Api\Services\ResultadoService;

class ResultadoController
{
    private ResultadoService $resultadoService;


    public function __construct(
        ResultadoService $resultadoServiceDependency
    ) {
        error_log("ResultadoController::__construct()");

        $this->resultadoService = $resultadoServiceDependency;
    }


    public function createController(
        Request $request,
        Response $response,
        array $args
    ): Response {
        error_log("ResultadoController::createController()");

        $body = $request->getBody()->getContents();

        $objPHP = json_decode($body);

        $novoResultado =
            $this->resultadoService->createService($objPHP);


        $resposta = [
            'success' => true,
            'message' => 'Resultado registrado com sucesso',
            'data' => [
                'resultados' => [
                    [
                        'id' => $novoResultado->getId(),
                        'partida_id' => $novoResultado->getPartidaId(),
                        'participante_id' => $novoResultado->getParticipanteId(),
                        'robo' => $novoResultado->getRobo(),
                        'tempo' => $novoResultado->getTempo()
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


    public function findAllController(
        Request $request,
        Response $response,
        array $args
    ): Response {
        error_log("ResultadoController::findAllController()");

        $resultados =
            $this->resultadoService->findAllService();


        $resposta = [
            'success' => true,
            'message' => 'Busca realizada com sucesso',
            'data' => [
                'resultados' => $resultados
            ]
        ];


        $response->getBody()->write(
            json_encode($resposta)
        );


        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }


    public function findByPartidaController(
        Request $request,
        Response $response,
        array $args
    ): Response {
        error_log(
            "ResultadoController::findByPartidaController()"
        );

        $partidaId = (int) $args['partida_id'];

        $resultados =
            $this->resultadoService->findByPartidaService(
                $partidaId
            );


        $resposta = [
            'success' => true,
            'message' => 'Busca realizada com sucesso',
            'data' => [
                'resultados' => $resultados
            ]
        ];


        $response->getBody()->write(
            json_encode($resposta)
        );


        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }


    public function findRankingController(
        Request $request,
        Response $response,
        array $args
    ): Response {
        error_log(
            "ResultadoController::findRankingController()"
        );

        $ranking =
            $this->resultadoService->findRankingService();


        $resposta = [
            'success' => true,
            'message' => 'Ranking obtido com sucesso',
            'data' => [
                'ranking' => $ranking
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