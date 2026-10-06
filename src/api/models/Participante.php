<?php

namespace Api\Models;

use InvalidArgumentException;
use JsonSerializable;

class Participante implements JsonSerializable
{
    private int $id;

    private string $nome = "";

    private string $robo = "";

    private float $tempo = 0;


    public function __construct()
    {
        // error_log("Participante::__construct()");
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


    public function getNome(): ?string
    {
        return $this->nome;
    }


    public function setNome(string $value): void
    {
        $nome = trim($value);

        if ($nome === '') {
            throw new InvalidArgumentException(
                "nome não pode ser vazio."
            );
        }

        $len = preg_match_all('/./us', $nome, $matches);

        if ($len === false) {
            throw new InvalidArgumentException(
                "nome deve conter texto UTF-8 válido."
            );
        }

        if ($len < 3) {
            throw new InvalidArgumentException(
                "nome deve ter pelo menos 3 caracteres."
            );
        }

        if ($len > 40) {
            throw new InvalidArgumentException(
                "nome deve ter no máximo 40 caracteres."
            );
        }

        $this->nome = $nome;
    }

    public function getRobo(): ?string
    {
        return $this->robo;
    }


    public function setRobo(string $value): void
    {
        $robo = trim($value);

        if ($robo !== "Azul" && $robo !== "Vermelho") {
            throw new InvalidArgumentException(
                "robo deve ser Azul ou Vermelho."
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
        if (!is_finite($value) || $value <= 0) {
            throw new InvalidArgumentException(
                "tempo deve ser um número maior que zero."
            );
        }

        $this->tempo = round($value, 3);
    }


    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'nome' => $this->getNome(),
            'robo' => $this->getRobo(),
            'tempo' => $this->getTempo()
        ];
    }
}