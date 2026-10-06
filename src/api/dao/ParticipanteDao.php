<?php

namespace Api\DAO;

use Api\Models\Participante;
use Api\Database\MysqlDatabase;
use Exception;

class ParticipanteDAO
{
    private MysqlDatabase $database;

    public function __construct(MysqlDatabase $databaseInstance)
    {
        $this->database = $databaseInstance;

        error_log("ParticipanteDAO::__construct()");
    }


    public function create(Participante $objParticipante): Participante
    {
        error_log("ParticipanteDAO::create()");

        $sql = "
            INSERT INTO participantes (nome, robo, tempo)
            VALUES (:nome, :robo, :tempo)
        ";

        $parametros = [
            ':nome' => $objParticipante->getNome(),
            ':robo' => $objParticipante->getRobo(),
            ':tempo' => $objParticipante->getTempo()
        ];

        $stmt = $this->database->getConnection()->prepare($sql);

        if (!$stmt->execute($parametros)) {
            throw new Exception("Erro ao cadastrar participante.");
        }

        $novoID = (int) $this->database->getConnection()->lastInsertId();

        $objParticipante->setId($novoID);

        return $objParticipante;
    }


    public function existsByNome(string $nome): bool
    {
        error_log("ParticipanteDAO::existsByNome()");

        $sql = "
            SELECT 1
            FROM participantes
            WHERE nome = :nome
            LIMIT 1
        ";

        $stmt = $this->database->getConnection()->prepare($sql);
        $stmt->execute([
            ':nome' => $nome
        ]);

        return $stmt->fetchColumn() !== false;
    }


    public function findById(int $id): ?Participante
    {
        error_log("ParticipanteDAO::findById()");

        $sql = "
            SELECT *
            FROM participantes
            WHERE id = :id
        ";

        $stmt = $this->database->getConnection()->prepare($sql);

        $stmt->execute([
            ':id' => $id
        ]);

        $linha = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$linha) {
            return null;
        }

        $participante = new Participante();

        $participante->setId((int) $linha['id']);
        $participante->setNome($linha['nome']);
        $participante->setRobo($linha['robo']);
        $participante->setTempo((float) $linha['tempo']);

        return $participante;
    }


    public function findAll(): array
    {
        error_log("ParticipanteDAO::findAll()");

        $sql = "
            SELECT *
            FROM participantes
            ORDER BY nome
        ";

        $stmt = $this->database->getConnection()->query($sql);

        $matrizArrays = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $participantes = [];

        foreach ($matrizArrays as $linha) {

            $participante = new Participante();

            $participante->setId((int) $linha['id']);
            $participante->setNome($linha['nome']);
            $participante->setRobo($linha['robo']);
            $participante->setTempo((float) $linha['tempo']);

            $participantes[] = $participante;
        }

        return $participantes;
    }
}