<?php

namespace Api\Services;

use Api\Models\Resultado;
use Api\DAO\ResultadoDAO;
use Api\Http\ErrorResponse;
use stdClass;

class ResultadoService
{
    private ResultadoDAO $resultadoDAO;

    public function __construct(ResultadoDAO $resultadoDAODependency)
    {
        error_log("ResultadoService::__construct()");

        $this->resultadoDAO = $resultadoDAODependency;
    }


    public function createService(stdClass $objPHP): Resultado
    {
        error_log("ResultadoService::createService()");

        $partidaId = (int) $objPHP->resultado->partida_id;
        $participanteId = (int) $objPHP->resultado->participante_id;
        $robo = $objPHP->resultado->robo;
        $tempo = (float) $objPHP->resultado->tempo;


        if ($tempo <= 0) {
            throw new ErrorResponse(
                400,
                "Tempo inválido",
                [
                    "message" => "O tempo deve ser maior que zero."
                ]
            );
        }


        if ($robo !== "Vermelho" && $robo !== "Azul") {
            throw new ErrorResponse(
                400,
                "Robô inválido",
                [
                    "message" => "O robô deve ser Vermelho ou Azul."
                ]
            );
        }


        $resultado = new Resultado();

        $resultado->setPartidaId($partidaId);
        $resultado->setParticipanteId($participanteId);
        $resultado->setRobo($robo);
        $resultado->setTempo($tempo);

        return $this->resultadoDAO->create($resultado);
    }


    public function findByPartidaService(int $partidaId): array
    {
        error_log("ResultadoService::findByPartidaService()");

        return $this->resultadoDAO->findByPartida($partidaId);
    }


    public function findAllService(): array
    {
        error_log("ResultadoService::findAllService()");

        return $this->resultadoDAO->findAll();
    }


    public function findRankingService(): array
    {
        error_log("ResultadoService::findRankingService()");

        return $this->resultadoDAO->findRanking();
    }
}