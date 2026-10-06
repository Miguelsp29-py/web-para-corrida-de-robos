<?php

namespace Api\DAO;

use Api\Models\Resultado;
use Api\Database\MysqlDatabase;
use Exception;

class ResultadoDAO
{
    private MysqlDatabase $database;

    public function __construct(MysqlDatabase $databaseInstance)
    {
        $this->database = $databaseInstance;

        error_log("ResultadoDAO::__construct()");
    }


    public function create(Resultado $objResultado): Resultado
    {
        error_log("ResultadoDAO::create()");

        $sql = "
            INSERT INTO resultados
            (
                partida_id,
                participante_id,
                robo,
                tempo
            )
            VALUES
            (
                :partida_id,
                :participante_id,
                :robo,
                :tempo
            )
        ";

        $parametros = [
            ':partida_id' => $objResultado->getPartidaId(),
            ':participante_id' => $objResultado->getParticipanteId(),
            ':robo' => $objResultado->getRobo(),
            ':tempo' => $objResultado->getTempo()
        ];

        $stmt = $this->database->getConnection()->prepare($sql);

        if (!$stmt->execute($parametros)) {
            throw new Exception("Erro ao cadastrar resultado.");
        }

        $novoID = (int) $this->database->getConnection()->lastInsertId();

        $objResultado->setId($novoID);

        return $objResultado;
    }


    public function findByPartida(int $partidaId): array
    {
        error_log("ResultadoDAO::findByPartida()");

        $sql = "
            SELECT
                r.id,
                r.partida_id,
                r.participante_id,
                p.nome AS nome_participante,
                r.robo,
                r.tempo
            FROM resultados r
            INNER JOIN participantes p
                ON p.id = r.participante_id
            WHERE r.partida_id = :partida_id
            ORDER BY r.tempo ASC
        ";

        $stmt = $this->database->getConnection()->prepare($sql);

        $stmt->execute([
            ':partida_id' => $partidaId
        ]);

        $matrizArrays = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $resultados = [];

        foreach ($matrizArrays as $linha) {

            $resultado = new Resultado();

            $resultado->setId((int) $linha['id']);
            $resultado->setPartidaId((int) $linha['partida_id']);
            $resultado->setParticipanteId((int) $linha['participante_id']);
            $resultado->setRobo($linha['robo']);
            $resultado->setTempo($linha['tempo']);

            $resultados[] = $resultado;
        }

        return $resultados;
    }


    public function findAll(): array
    {
        error_log("ResultadoDAO::findAll()");

        $sql = "
            SELECT
                r.id,
                r.partida_id,
                r.participante_id,
                p.nome AS nome_participante,
                r.robo,
                r.tempo
            FROM resultados r
            INNER JOIN participantes p
                ON p.id = r.participante_id
            ORDER BY r.id DESC
        ";

        $stmt = $this->database->getConnection()->query($sql);

        $matrizArrays = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $resultados = [];

        foreach ($matrizArrays as $linha) {

            $resultado = new Resultado();

            $resultado->setId((int) $linha['id']);
            $resultado->setPartidaId((int) $linha['partida_id']);
            $resultado->setParticipanteId((int) $linha['participante_id']);
            $resultado->setRobo($linha['robo']);
            $resultado->setTempo($linha['tempo']);

            $resultados[] = $resultado;
        }

        return $resultados;
    }


    public function findRanking(): array
    {
        error_log("ResultadoDAO::findRanking()");

        $sql = "
            SELECT
                r.id,
                r.partida_id,
                r.participante_id,
                p.nome AS nome_participante,
                r.robo,
                r.tempo
            FROM resultados r
            INNER JOIN participantes p
                ON p.id = r.participante_id
            ORDER BY r.tempo ASC
            LIMIT 5
        ";

        $stmt = $this->database->getConnection()->query($sql);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}