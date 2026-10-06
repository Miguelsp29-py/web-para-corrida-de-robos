<?php

namespace Api\Services;

use Api\Models\Participante;
use Api\DAO\ParticipanteDAO;
use Api\Http\ErrorResponse;
use stdClass;

class ParticipanteService
{
    private ParticipanteDAO $participanteDAO;

    public function __construct(ParticipanteDAO $participanteDAODependency)
    {
        error_log("ParticipanteService::__construct()");

        $this->participanteDAO = $participanteDAODependency;
    }


    public function createService(stdClass $objPHP): Participante
    {
        error_log("ParticipanteService::createService()");

        if (
            !isset($objPHP->participante)
            || !$objPHP->participante instanceof stdClass
            || !isset(
                $objPHP->participante->nome,
                $objPHP->participante->robo,
                $objPHP->participante->tempo
            )
        ) {
            throw new ErrorResponse(
                400,
                "Dados do participante inválidos",
                [
                    "message" => "Informe nome, robo e tempo do participante."
                ]
            );
        }

        $dados = $objPHP->participante;
        $nome = is_string($dados->nome) ? trim($dados->nome) : null;
        $nomeComprimento = $nome !== null
            ? preg_match_all('/./us', $nome, $matches)
            : false;

        if (
            $nomeComprimento === false
            || $nomeComprimento < 3
            || $nomeComprimento > 40
        ) {
            throw new ErrorResponse(
                400,
                "Nome inválido",
                [
                    "message" => "O nome deve conter de 3 a 40 caracteres."
                ]
            );
        }

        if ($this->participanteDAO->existsByNome($nome)) {
            throw new ErrorResponse(
                409,
                "Nome de participante já cadastrado",
                [
                    "message" => "Escolha um nome que ainda não esteja cadastrado."
                ]
            );
        }

        if (
            !is_string($dados->robo)
            || !in_array($dados->robo, ["Azul", "Vermelho"], true)
        ) {
            throw new ErrorResponse(
                400,
                "Robô inválido",
                [
                    "message" => "O robô deve ser Azul ou Vermelho."
                ]
            );
        }

        if (
            !is_numeric($dados->tempo)
            || !is_finite((float) $dados->tempo)
            || (float) $dados->tempo <= 0
        ) {
            throw new ErrorResponse(
                400,
                "Tempo inválido",
                [
                    "message" => "O tempo deve ser um número maior que zero."
                ]
            );
        }

        $participante = new Participante();

        $participante->setNome($nome);
        $participante->setRobo($dados->robo);
        $participante->setTempo((float) $dados->tempo);

        return $this->participanteDAO->create($participante);
    }


    public function findByIdService(int $id): ?Participante
    {
        error_log("ParticipanteService::findByIdService()");

        return $this->participanteDAO->findById($id);
    }


    public function findAllService(): array
    {
        error_log("ParticipanteService::findAllService()");

        return $this->participanteDAO->findAll();
    }
}