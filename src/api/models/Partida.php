<?php

namespace Api\Models;

use InvalidArgumentException;
use JsonSerializable;

class Partida implements JsonSerializable
{
    private int $id;

    private string $nomeParticipante1 = "";

    private string $nomeParticipante2 = "";


    public function __construct()
    {
        // error_log("Partida::__construct()");
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


    public function getNomeParticipante1(): ?string
    {
        return $this->nomeParticipante1;
    }


    public function setNomeParticipante1(string $value): void
    {
        $nome = trim($value);

        if ($nome === '') {
            throw new InvalidArgumentException(
                "nomeparticipante1 não pode ser vazio."
            );
        }

        $len = preg_match_all('/./us', $nome, $matches);

        if ($len === false || $len > 40) {
            throw new InvalidArgumentException(
                "nomeparticipante1 deve ter no máximo 40 caracteres."
            );
        }

        $this->nomeParticipante1 = $nome;
    }


    public function getNomeParticipante2(): ?string
    {
        return $this->nomeParticipante2;
    }


    public function setNomeParticipante2(string $value): void
    {
        $nome = trim($value);

        if ($nome === '') {
            throw new InvalidArgumentException(
                "nomeparticipante2 não pode ser vazio."
            );
        }

        $len = preg_match_all('/./us', $nome, $matches);

        if ($len === false || $len > 40) {
            throw new InvalidArgumentException(
                "nomeparticipante2 deve ter no máximo 40 caracteres."
            );
        }

        $this->nomeParticipante2 = $nome;
    }


    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'nomeparticipante1' => $this->getNomeParticipante1(),
            'nomeparticipante2' => $this->getNomeParticipante2()
        ];
    }
}