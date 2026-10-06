<?php

namespace Api\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Api\Http\ErrorResponse;
use Api\Services\ParticipanteService;

class ParticipanteController
{
    private ParticipanteService $participanteService;


    public function __construct(
        ParticipanteService $participanteServiceDependency
    ) {
        error_log("ParticipanteController::__construct()");

        $this->participanteService = $participanteServiceDependency;
    }


    public function createController(
        Request $request,
        Response $response,
        array $args
    ): Response {
        error_log("ParticipanteController::createController()");

        $body = $request->getBody()->getContents();

        $objPHP = json_decode($body);

        if (!$objPHP instanceof \stdClass) {
            throw new ErrorResponse(
                400,
                "Corpo da requisição inválido",
                [
                    "message" => "Envie um objeto JSON válido."
                ]
            );
        }

        $novoParticipante =
            $this->participanteService->createService($objPHP);


        $resposta = [
            'success' => true,
            'message' => 'Cadastro realizado com sucesso',
            'data' => [
                'participantes' => [
                    [
                        'id' => $novoParticipante->getId(),
                        'nome' => $novoParticipante->getNome(),
                        'robo' => $novoParticipante->getRobo(),
                        'tempo' => $novoParticipante->getTempo()
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
        error_log("ParticipanteController::findAllController()");

        $participantes =
            $this->participanteService->findAllService();


        $resposta = [
            'success' => true,
            'message' => 'Busca realizada com sucesso',
            'data' => [
                'participantes' => $participantes
            ]
        ];


        $response->getBody()->write(
            json_encode($resposta)
        );


        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }


    public function findByIdController(
        Request $request,
        Response $response,
        array $args
    ): Response {
        error_log("ParticipanteController::findByIdController()");

        $id = (int) $args['id'];

        $participante =
            $this->participanteService->findByIdService($id);


        $resposta = [
            'success' => true,
            'message' => 'Busca realizada com sucesso',
            'data' => [
                'participantes' => [
                    $participante
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