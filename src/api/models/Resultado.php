<?php

namespace Api\Models;

use InvalidArgumentException;
use JsonSerializable;

class Resultado implements JsonSerializable
{
    private int $id;

    private int $partida_id;

    private int $participante_id;

    private string $robo = "";

    private float $tempo = 0;


    public function __construct()
    {
        // error_log("Resultado::__construct()");
    }


    public function getId(): ?int
    {
        return $this->id;
    }


    public function setId(int $value): void
    {
        if (!is_int($value)) {
            throw new InvalidArgumentException(
                "id deve ser um número inteiro."
            );
        }

        if ($value < 0) {
            throw new InvalidArgumentException(
                "id deve ser maior ou igual a zero."
            );
        }

        $this->id = $value;
    }


    public function getPartidaId(): ?int
    {
        return $this->partida_id;
    }


    public function setPartidaId(int $value): void
    {
        if (!is_int($value)) {
            throw new InvalidArgumentException(
                "partida_id deve ser um número inteiro."
            );
        }

        if ($value <= 0) {
            throw new InvalidArgumentException(
                "partida_id deve ser maior que zero."
            );
        }

        $this->partida_id = $value;
    }


    public function getParticipanteId(): ?int
    {
        return $this->participante_id;
    }


    public function setParticipanteId(int $value): void
    {
        if (!is_int($value)) {
            throw new InvalidArgumentException(
                "participante_id deve ser um número inteiro."
            );
        }

        if ($value <= 0) {
            throw new InvalidArgumentException(
                "participante_id deve ser maior que zero."
            );
        }

        $this->participante_id = $value;
    }


    public function getRobo(): ?string
    {
        return $this->robo;
    }


    public function setRobo(string $value): void
    {
        $robo = trim($value);

        if ($robo === '') {
            throw new InvalidArgumentException(
                "robo não pode ser vazio."
            );
        }

        if ($robo !== "Vermelho" && $robo !== "Azul") {
            throw new InvalidArgumentException(
                "robo deve ser Vermelho ou Azul."
            );
        }

        $this->robo = $robo;
    }


    public function getTempo(): ?float
    {
        return $this->tempo;
    }


    public function setTempo(float $value): void
    {
        if ($value <= 0) {
            throw new InvalidArgumentException(
                "tempo deve ser maior que zero."
            );
        }

        $this->tempo = $value;
    }


    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'partida_id' => $this->getPartidaId(),
            'participante_id' => $this->getParticipanteId(),
            'robo' => $this->getRobo(),
            'tempo' => $this->getTempo()
        ];
    }
}