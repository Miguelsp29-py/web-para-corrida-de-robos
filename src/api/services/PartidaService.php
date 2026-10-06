<?php

namespace Api\Services;

use Api\Models\Partida;
use Api\DAO\PartidaDAO;
use Api\Database\MysqlDatabase;
use Api\Http\ErrorResponse;
use stdClass;

class PartidaService
{
    private PartidaDAO $partidaDAO;
    private ParticipanteService $participanteService;
    private MysqlDatabase $database;

    public function __construct(
        PartidaDAO $partidaDAODependency,
        ParticipanteService $participanteServiceDependency,
        MysqlDatabase $database
    ) {
        error_log("PartidaService::__construct()");

        $this->partidaDAO = $partidaDAODependency;
        $this->participanteService = $participanteServiceDependency;
        $this->database = $database;
    }


    public function createService(stdClass $objPHP): Partida
    {
        error_log("PartidaService::createService()");

        if (
            !isset($objPHP->partida)
            || !$objPHP->partida instanceof stdClass
            || !isset(
                $objPHP->partida->nomeparticipante1,
                $objPHP->partida->nomeparticipante2,
                $objPHP->partida->robo1,
                $objPHP->partida->tempo1,
                $objPHP->partida->robo2,
                $objPHP->partida->tempo2
            )
            || !is_string($objPHP->partida->nomeparticipante1)
            || !is_string($objPHP->partida->nomeparticipante2)
            || !is_string($objPHP->partida->robo1)
            || !is_string($objPHP->partida->robo2)
        ) {
            throw new ErrorResponse(
                400,
                "Dados da partida inválidos",
                [
                    "message" => "Informe nomes, robôs e tempos dos dois participantes."
                ]
            );
        }

        $dados = $objPHP->partida;
        $nome1 = trim($dados->nomeparticipante1);
        $nome2 = trim($dados->nomeparticipante2);
        $nome1Comprimento = preg_match_all('/./us', $nome1, $matches1);
        $nome2Comprimento = preg_match_all('/./us', $nome2, $matches2);

        if (
            $nome1Comprimento === false
            || $nome1Comprimento < 3
            || $nome1Comprimento > 40
            || $nome2Comprimento === false
            || $nome2Comprimento < 3
            || $nome2Comprimento > 40
        ) {
            throw new ErrorResponse(
                400,
                "Nome dos participantes inválido",
                [
                    "message" => "Os nomes devem conter de 3 a 40 caracteres."
                ]
            );
        }

        foreach (["robo1", "robo2"] as $campoRobo) {
            if (!in_array($dados->{$campoRobo}, ["Azul", "Vermelho"], true)) {
                throw new ErrorResponse(
                    400,
                    "Robô inválido",
                    [
                        "message" => "Cada robô deve ser Azul ou Vermelho."
                    ]
                );
            }
        }

        foreach (["tempo1", "tempo2"] as $campoTempo) {
            $tempo = $dados->{$campoTempo};

            if (
                !is_numeric($tempo)
                || !is_finite((float) $tempo)
                || (float) $tempo <= 0
            ) {
                throw new ErrorResponse(
                    400,
                    "Tempo inválido",
                    [
                        "message" => "Os dois tempos devem ser números maiores que zero."
                    ]
                );
            }
        }

        $partida = new Partida();
        $partida->setNomeParticipante1($nome1);
        $partida->setNomeParticipante2($nome2);

        $connection = $this->database->getConnection();
        $connection->beginTransaction();

        try {
            $this->participanteService->createService(
                (object) [
                    'participante' => (object) [
                        'nome' => $nome1,
                        'robo' => $dados->robo1,
                        'tempo' => $dados->tempo1
                    ]
                ]
            );
            $this->participanteService->createService(
                (object) [
                    'participante' => (object) [
                        'nome' => $nome2,
                        'robo' => $dados->robo2,
                        'tempo' => $dados->tempo2
                    ]
                ]
            );

            $this->partidaDAO->create($partida);
            $connection->commit();
        } catch (\Throwable $exception) {
            if ($connection->inTransaction()) {
                $connection->rollBack();
            }

            throw $exception;
        }

        return $partida;
    }


    public function findByIdService(int $id): ?Partida
    {
        error_log("PartidaService::findByIdService()");

        return $this->partidaDAO->findById($id);
    }


    public function findAllService(): array
    {
        error_log("PartidaService::findAllService()");

        return $this->partidaDAO->findAll();
    }
}
