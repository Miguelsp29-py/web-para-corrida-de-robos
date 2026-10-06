<?php

namespace Api\Middlewares\Professor;

use Psr\Http\Message\ServerRequestInterface as Request;   // Interface do PSR-7 para requisições HTTP
use Psr\Http\Message\ResponseInterface as Response;       // Interface do PSR-7 para respostas HTTP
use Psr\Http\Server\RequestHandlerInterface as RequestHandler; // Interface que representa o próximo middleware/handler
use Psr\Http\Server\MiddlewareInterface;                  // Interface obrigatória para criar middlewares no PSR-15
use Api\Http\ErrorResponse;                               // Classe personalizada para padronizar erros da aplicação

/**
 * Middleware para validar o corpo de requisições que envolvem a entidade "Professor".
 *
 * Este middleware intercepta requisições HTTP antes de chegar ao Controller,
 * garantindo que o corpo da requisição (JSON ou form data) contenha os campos
 * obrigatórios para criar ou atualizar um Professor.
 *
 * @package Api\Middlewares\Professor
 */
class ValidateProfessorBody implements MiddlewareInterface
{
    /**
     * Método principal do middleware.
     *
     * @param Request $request
     * @param RequestHandler $handler
     * @return Response
     * @throws ErrorResponse
     */
    public function process(Request $request, RequestHandler $handler): Response
    {
        // Lê o JSON bruto enviado no body
        $body = $request->getBody()->getContents();

        // Converte JSON para objeto stdClass
        $objPHP = json_decode($body);

        // -----------------------------------------------------------
        // Validação 1: verificar se o campo principal 'professor' existe
        // -----------------------------------------------------------
        if (!isset($objPHP->professor)) {
            throw new ErrorResponse(
                httpCode: 400,
                message: "Erro na validação de dados",
                error: [
                    "message" => "O campo 'professor' é obrigatório!"
                ]
            );
        }

        // Armazena objeto professor
        $professor = $objPHP->professor;

        // -----------------------------------------------------------
        // Validação 2: verificar se 'nomeProfessor' existe e não está vazio
        // -----------------------------------------------------------
        if (!isset($professor->nomeProfessor) || trim((string) $professor->nomeProfessor) === "") {
            throw new ErrorResponse(
                httpCode: 400,
                message: "Erro na validação de dados",
                error: [
                    "message" => "O campo 'nomeProfessor' é obrigatório!"
                ]
            );
        }
        if (!isset($professor->email) || trim((string) $professor->email) === "") {
            throw new ErrorResponse(
                400,
                "Erro na validação de dados",
                [
                    "message" => "O campo 'email' é obrigatório!"
                ]
            );
        }

        if (!isset($professor->senha) || trim((string) $professor->senha) === "") {
            throw new ErrorResponse(
                400,
                "Erro na validação de dados",
                [
                    "message" => "O campo 'senha' é obrigatório!"
                ]
            );
        }

        

        
        return $handler->handle($request);
    }
}