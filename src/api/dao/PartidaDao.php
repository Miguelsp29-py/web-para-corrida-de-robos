<?php

namespace Api\DAO;

use Api\Models\Partida;
use Api\Database\MysqlDatabase;
use Exception;

class PartidaDAO
{
    private MysqlDatabase $database;

    public function __construct(MysqlDatabase $databaseInstance)
    {
        $this->database = $databaseInstance;

        error_log("PartidaDAO::__construct()");
    }



    public function create(Partida $objPartida): Partida
    {
        error_log("PartidaDAO::create()");

        $sql = "
            INSERT INTO partidas (nomeparticipante1, nomeparticipante2)
            VALUES (:nomeparticipante1, :nomeparticipante2)
        ";

        $parametros = [
            ':nomeparticipante1' => $objPartida->getNomeParticipante1(),
            ':nomeparticipante2' => $objPartida->getNomeParticipante2()
        ];

        $stmt = $this->database->getConnection()->prepare($sql);

        if (!$stmt->execute($parametros)) {
            throw new Exception("Erro ao cadastrar partida.");
        }

        $novoID = (int) $this->database->getConnection()->lastInsertId();

        $objPartida->setId($novoID);

        return $objPartida;
    }



    public function findById(int $id): ?Partida
    {
        error_log("PartidaDAO::findById()");

        $sql = "
            SELECT *
            FROM partidas
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

        $partida = new Partida();

        $partida->setId((int) $linha['id']);
        $partida->setNomeParticipante1($linha['nomeparticipante1']);
        $partida->setNomeParticipante2($linha['nomeparticipante2']);

        return $partida;
    }


    public function findAll(): array
    {
        error_log("PartidaDAO::findAll()");

        $sql = "
            SELECT *
            FROM partidas
            ORDER BY id DESC
        ";

        $stmt = $this->database->getConnection()->query($sql);

        $matrizArrays = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $partidas = [];

        foreach ($matrizArrays as $linha) {

            $partida = new Partida();

            $partida->setId((int) $linha['id']);
            $partida->setNomeParticipante1($linha['nomeparticipante1']);
            $partida->setNomeParticipante2($linha['nomeparticipante2']);

            $partidas[] = $partida;
        }

        return $partidas;
    }
}