<?php

namespace Api\Middlewares\Professor;

use Psr\Http\Message\ServerRequestInterface as Request;   // Interface PSR-7 para requisições HTTP
use Psr\Http\Message\ResponseInterface as Response;       // Interface PSR-7 para respostas HTTP
use Psr\Http\Server\RequestHandlerInterface as RequestHandler; // Interface PSR-15 para o próximo handler
use Psr\Http\Server\MiddlewareInterface;                  // Interface PSR-15 para middlewares
use Slim\Routing\RouteContext;                             // Classe Slim para acessar informações da rota
use Api\Http\ErrorResponse;                               // Classe personalizada de erro da aplicação

/**
 * Middleware para validar a presença do parâmetro 'idProfessor' em rotas que precisam de um ID de Professor.
 *
 * Este middleware verifica se a rota existe e se o parâmetro obrigatório 'idProfessor' foi informado.
 * Caso a validação falhe, uma exceção ErrorResponse é lançada para padronizar o retorno JSON de erro.
 *
 * @package Api\Middlewares\Professor
 */
class ValidateProfessorId implements MiddlewareInterface
{
    /**
     * Método principal do middleware.
     *
     * @param Request $request  Requisição HTTP recebida
     * @param RequestHandler $handler  Próximo middleware ou controller na cadeia
     * @return Response  Resposta HTTP
     * @throws ErrorResponse  Se a rota ou o parâmetro 'idProfessor' estiverem ausentes
     */
    public function process(Request $request, RequestHandler $handler): Response
    {
        // Obtém o contexto da rota atual usando o objeto de requisição
        $routeContext = RouteContext::fromRequest($request);

        // Recupera a rota que está sendo chamada
        $route = $routeContext->getRoute();


        // Recupera os argumentos/parametros passados na rota (ex: /cargos/{idCargo})
        $routeArgs = $route->getArguments();

        // -----------------------------------------------------------
        // Validação 2: verificar se o parâmetro obrigatório 'idProfessor' existe e não está vazio
        // -----------------------------------------------------------
        if (!isset($routeArgs['id_professor']) || $routeArgs['id_professor'] === "") {
            throw new ErrorResponse(
                httpCode: 400,
                message: "Erro na validação de dados",
                error: [
                    "message" => "O parâmetro 'id_professor' é obrigatório!"  // Mensagem detalhada
                ]
            );
        }

        // -----------------------------------------------------------
        // Se todas as validações passaram, repassa a requisição para o próximo handler
        // Pode ser outro middleware ou o controller responsável pela rota
        // -----------------------------------------------------------
        return $handler->handle(request: $request);
    }
}
