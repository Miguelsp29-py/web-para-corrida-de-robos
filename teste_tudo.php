<?php

// 1. Carrega as classes do projeto
require_once __DIR__ . '/vendor/autoload.php';

use Api\Database\MysqlDatabase;
use Api\DAO\CursoDAO;
use Api\Services\CursoService;

try {
    echo "⚙️  Iniciando teste de fogo do Curso...\n";

    $database = new MysqlDatabase();
    $cursoDAO = new CursoDAO($database);
    $cursoService = new CursoService($cursoDAO);

    echo "\n🔹 Teste 1: Contar cursos antes\n";
    echo "Quantidade no banco: " . $cursoService->countService() . "\n";

    echo "\n🔹 Teste 2: Simulando o Insomnia do seu amigo (Mandando Nome e ID)\n";
    
    // Aqui montamos o objeto IGUALZINHO ao JSON do Insomnia
    $dadosSimulados = new stdClass();
    $dadosSimulados->curso = new stdClass();
    $dadosSimulados->curso->id_curso = 5; // <-- ADICIONAMOS O ID AQUI!
    $dadosSimulados->curso->nomeCurso = "Sistemas de Informação";
    
    // Chama o Service dele passando o objeto completo
    $novoCurso = $cursoService->createService($dadosSimulados);
    
    echo "🎉 SE CHEGOU AQUI, O CÓDIGO DELE ESTÁ 100% CERTO!\n";

    echo "\n🔹 Teste 3: Lista atualizada\n";
    $listaCursos = $cursoService->findAllService();
    foreach ($listaCursos as $c) {
        echo "- ID: " . $c->getid_curso() . " | Nome: " . $c->getNomeCurso() . "\n";
    }

} catch (Exception $e) {
    echo "\n❌ O teste parou! Erro encontrado:\n";
    echo $e->getMessage() . "\n";
}