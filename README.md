# 📡 Documentação da API

Documentação completa dos endpoints disponíveis na API do **Corrida De
Robôs**.

------------------------------------------------------------------------

## 🌐 URL Base

``` text
http://localhost:8080
```

A API é executada localmente utilizando o servidor embutido do PHP e o
**Slim Framework 4**.

------------------------------------------------------------------------

## 📌 Sobre o Projeto

A API é responsável por armazenar e consultar os dados da competição de
robôs.

O sistema trabalha principalmente com dois recursos:

-   `participantes` --- armazena o nome do participante, o robô
    utilizado e o tempo obtido.
-   `partidas` --- armazena os nomes dos dois participantes de uma
    partida.

O frontend utiliza a API para:

-   verificar se os nomes dos participantes já estão cadastrados;
-   registrar uma partida;
-   registrar os dois participantes e seus respectivos tempos;
-   consultar participantes;
-   consultar partidas;
-   montar a classificação com base nos tempos cadastrados.

------------------------------------------------------------------------

## 🔐 Autenticação

A API **não possui autenticação obrigatória** nas rotas atualmente
implementadas.

Não é necessário enviar:

``` text
Authorization: Bearer {token}
```

O arquivo `composer.json` possui a dependência `firebase/php-jwt`, porém
as rotas atuais de participantes e partidas não utilizam JWT para
autenticação.

------------------------------------------------------------------------

# 👥 Participantes

## 1. Criar Participante

**Endpoint:** `POST /participantes`

Cadastra um novo participante diretamente na tabela `participantes`.

### Cabeçalhos

``` text
Content-Type: application/json
```

### Corpo da Requisição

``` json
{
  "participante": {
    "nome": "Equipe Azul",
    "robo": "Azul",
    "tempo": 12.345
  }
}
```

### Campos

  Campo     Tipo     Obrigatório   Regra
  --------- -------- ------------- ----------------------
  `nome`    string   Sim           De 3 a 40 caracteres
  `robo`    string   Sim           `Azul` ou `Vermelho`
  `tempo`   number   Sim           Maior que zero

### Resposta (201 Created)

``` json
{
  "success": true,
  "message": "Cadastro realizado com sucesso",
  "data": {
    "participantes": [
      {
        "id": 1,
        "nome": "Equipe Azul",
        "robo": "Azul",
        "tempo": 12.345
      }
    ]
  }
}
```

### Exemplo cURL

``` bash
curl -X POST http://localhost:8080/participantes \
  -H "Content-Type: application/json" \
  -d '{"participante":{"nome":"Equipe Azul","robo":"Azul","tempo":12.345}}'
```

### Possíveis erros

Nome inválido:

``` json
{
  "success": false,
  "message": "Nome inválido",
  "error": {
    "message": "O nome deve conter de 3 a 40 caracteres."
  }
}
```

Robô inválido:

``` json
{
  "success": false,
  "message": "Robô inválido",
  "error": {
    "message": "O robô deve ser Azul ou Vermelho."
  }
}
```

Tempo inválido:

``` json
{
  "success": false,
  "message": "Tempo inválido",
  "error": {
    "message": "O tempo deve ser um número maior que zero."
  }
}
```

Nome já cadastrado:

``` json
{
  "success": false,
  "message": "Nome de participante já cadastrado",
  "error": {
    "message": "Escolha um nome que ainda não esteja cadastrado."
  }
}
```

**Código HTTP:** `409 Conflict`

------------------------------------------------------------------------

## 2. Listar Todos os Participantes

**Endpoint:** `GET /participantes`

Retorna todos os participantes cadastrados.

### Resposta (200 OK)

``` json
{
  "success": true,
  "message": "Busca realizada com sucesso",
  "data": {
    "participantes": [
      {
        "id": 1,
        "nome": "Equipe Azul",
        "robo": "Azul",
        "tempo": 12.345
      },
      {
        "id": 2,
        "nome": "Equipe Vermelha",
        "robo": "Vermelho",
        "tempo": 13.275
      }
    ]
  }
}
```

### Exemplo cURL

``` bash
curl -X GET http://localhost:8080/participantes
```

Os participantes são retornados em ordem alfabética pelo campo `nome`.

------------------------------------------------------------------------

## 3. Buscar Participante por ID

**Endpoint:** `GET /participantes/{id}`

Busca um participante pelo seu ID.

### Parâmetros de URL

-   `id` --- inteiro correspondente ao participante.

### Exemplo

``` text
GET /participantes/1
```

### Resposta (200 OK)

``` json
{
  "success": true,
  "message": "Busca realizada com sucesso",
  "data": {
    "participantes": [
      {
        "id": 1,
        "nome": "Equipe Azul",
        "robo": "Azul",
        "tempo": 12.345
      }
    ]
  }
}
```

### Exemplo cURL

``` bash
curl -X GET http://localhost:8080/participantes/1
```

> **Observação:** se o ID não existir, o serviço retorna `null` dentro
> de `data.participantes`, mantendo o status `200` na implementação
> atual.

------------------------------------------------------------------------

# 🏁 Partidas

## 1. Criar Partida

**Endpoint:** `POST /partidas`

Registra uma nova partida e cadastra os dois participantes com seus
respectivos robôs e tempos.

A operação é realizada dentro de uma **transação MySQL**. Se ocorrer
algum erro durante o cadastro, a transação é desfeita.

### Cabeçalhos

``` text
Content-Type: application/json
```

### Corpo da Requisição

``` json
{
  "partida": {
    "nomeparticipante1": "Equipe Azul",
    "nomeparticipante2": "Equipe Vermelha",
    "robo1": "Azul",
    "tempo1": 12.345,
    "robo2": "Vermelho",
    "tempo2": 13.275
  }
}
```

### Campos

  Campo                 Tipo     Obrigatório   Regra
  --------------------- -------- ------------- ----------------------
  `nomeparticipante1`   string   Sim           De 3 a 40 caracteres
  `nomeparticipante2`   string   Sim           De 3 a 40 caracteres
  `robo1`               string   Sim           `Azul` ou `Vermelho`
  `tempo1`              number   Sim           Maior que zero
  `robo2`               string   Sim           `Azul` ou `Vermelho`
  `tempo2`              number   Sim           Maior que zero

### Resposta (201 Created)

``` json
{
  "success": true,
  "message": "Partida registrada com sucesso",
  "data": {
    "partidas": [
      {
        "id": 1,
        "nomeparticipante1": "Equipe Azul",
        "nomeparticipante2": "Equipe Vermelha"
      }
    ]
  }
}
```

### Exemplo cURL

``` bash
curl -X POST http://localhost:8080/partidas \
  -H "Content-Type: application/json" \
  -d '{"partida":{"nomeparticipante1":"Equipe Azul","nomeparticipante2":"Equipe Vermelha","robo1":"Azul","tempo1":12.345,"robo2":"Vermelho","tempo2":13.275}}'
```

### Funcionamento

Ao criar uma partida, a API:

1.  valida os dados recebidos;
2.  cria o objeto da partida;
3.  inicia uma transação no banco;
4.  cadastra o primeiro participante;
5.  cadastra o segundo participante;
6.  cadastra a partida;
7.  confirma a transação.

Se algum cadastro falhar, a transação é revertida.

### Possíveis erros

Dados inválidos:

``` json
{
  "success": false,
  "message": "Dados da partida inválidos",
  "error": {
    "message": "Informe nomes, robôs e tempos dos dois participantes."
  }
}
```

Nome inválido:

``` json
{
  "success": false,
  "message": "Nome dos participantes inválido",
  "error": {
    "message": "Os nomes devem conter de 3 a 40 caracteres."
  }
}
```

Robô inválido:

``` json
{
  "success": false,
  "message": "Robô inválido",
  "error": {
    "message": "Cada robô deve ser Azul ou Vermelho."
  }
}
```

Tempo inválido:

``` json
{
  "success": false,
  "message": "Tempo inválido",
  "error": {
    "message": "Os dois tempos devem ser números maiores que zero."
  }
}
```

Nome já cadastrado:

``` json
{
  "success": false,
  "message": "Nome de participante já cadastrado",
  "error": {
    "message": "Escolha um nome que ainda não esteja cadastrado."
  }
}
```

**Código HTTP:** `409 Conflict`

------------------------------------------------------------------------

## 2. Listar Todas as Partidas

**Endpoint:** `GET /partidas`

Retorna todas as partidas cadastradas.

### Resposta (200 OK)

``` json
{
  "success": true,
  "message": "Busca realizada com sucesso",
  "data": {
    "partidas": [
      {
        "id": 2,
        "nomeparticipante1": "Equipe Azul 2",
        "nomeparticipante2": "Equipe Vermelha 2"
      },
      {
        "id": 1,
        "nomeparticipante1": "Equipe Azul",
        "nomeparticipante2": "Equipe Vermelha"
      }
    ]
  }
}
```

### Exemplo cURL

``` bash
curl -X GET http://localhost:8080/partidas
```

As partidas são retornadas em ordem decrescente pelo `id`, mostrando as
mais recentes primeiro.

------------------------------------------------------------------------

## 3. Buscar Partida por ID

**Endpoint:** `GET /partidas/{id}`

Busca uma partida pelo ID.

### Parâmetros de URL

-   `id` --- inteiro correspondente à partida.

### Exemplo

``` text
GET /partidas/1
```

### Resposta (200 OK)

``` json
{
  "success": true,
  "message": "Busca realizada com sucesso",
  "data": {
    "partidas": [
      {
        "id": 1,
        "nomeparticipante1": "Equipe Azul",
        "nomeparticipante2": "Equipe Vermelha"
      }
    ]
  }
}
```

### Exemplo cURL

``` bash
curl -X GET http://localhost:8080/partidas/1
```

> **Observação:** assim como na consulta de participante, um ID
> inexistente retorna `null` dentro de `data.partidas` na implementação
> atual.

------------------------------------------------------------------------

# 🏠 Rota Inicial

**Endpoint:** `GET /`

A rota inicial retorna o arquivo:

``` text
public/index.html
```

### Resposta

**200 OK**

``` text
Content-Type: text/html; charset=UTF-8
```

Caso o arquivo `index.html` não seja encontrado, a API retorna:

**404 Not Found**

``` text
index.html não encontrado.
```

------------------------------------------------------------------------

# ❌ Estrutura de Resposta de Erro

Quando ocorre um erro tratado pela API, a resposta segue o formato:

``` json
{
  "success": false,
  "message": "Descrição do erro",
  "error": {
    "message": "Detalhes do erro"
  }
}
```

Em erros internos não tratados, a implementação também pode retornar
informações como código, arquivo e linha da exceção.

------------------------------------------------------------------------

# 🔄 Códigos HTTP

  Código   Significado
  -------- ------------------------------------
  `200`    Requisição executada com sucesso
  `201`    Registro criado com sucesso
  `400`    Dados da requisição inválidos
  `404`    Recurso inicial não encontrado
  `409`    Nome de participante já cadastrado
  `500`    Erro interno do servidor

> **Observação:** atualmente não existem endpoints `PUT` ou `DELETE`
> para participantes ou partidas.

------------------------------------------------------------------------

# 🗄️ Banco de Dados

A API utiliza o banco de dados:

``` text
competicaoRobosAPI
```

O script para criação do banco está localizado em:

``` text
docs/banco.sql
```

O script começa removendo o banco existente:

``` sql
DROP DATABASE IF EXISTS competicaoRobosAPI;
```

Em seguida, cria novamente o banco e suas tabelas.

------------------------------------------------------------------------

## 📋 Tabelas

### `participantes`

  Campo     Tipo            Descrição
  --------- --------------- -------------------------------
  `id`      INT             Identificador do participante
  `nome`    VARCHAR(40)     Nome do participante
  `robo`    VARCHAR(14)     Robô utilizado
  `tempo`   DECIMAL(14,3)   Tempo em segundos

### `partidas`

  Campo                 Tipo          Descrição
  --------------------- ------------- -------------------------------
  `id`                  INT           Identificador da partida
  `nomeparticipante1`   VARCHAR(40)   Nome do primeiro participante
  `nomeparticipante2`   VARCHAR(40)   Nome do segundo participante

------------------------------------------------------------------------

## 🔗 Relacionamento dos Dados

A estrutura atual não utiliza chaves estrangeiras entre `partidas` e
`participantes`.

A relação é feita pelos nomes dos participantes:

``` text
Partida
├── nomeparticipante1
└── nomeparticipante2

Participante
├── nome
├── robo
└── tempo
```

O tempo de cada robô fica armazenado diretamente no registro do
participante.

Não existe uma tabela separada de `resultados` no banco atual.

------------------------------------------------------------------------

# 🌐 Integração com o Frontend

O frontend está localizado em:

``` text
public/
```

Entre os arquivos JavaScript utilizados estão:

``` text
public/ApiService.js
public/js/competicao.js
public/js/partida.js
public/js/resultados.js
public/js/historico.js
```

A classe `ApiService` possui métodos para comunicação HTTP:

``` text
simpleGet()
get()
getById()
post()
put()
delete()
```

As páginas também realizam chamadas `fetch()` diretamente para a API.

------------------------------------------------------------------------

## 📡 Fluxo da Competição

O fluxo principal da aplicação é:

``` text
competicao.html
       ↓
Verifica os nomes dos participantes
       ↓
localStorage
       ↓
partida.html
       ↓
Cronometra os dois robôs
       ↓
POST /partidas
       ↓
PHP / Controller
       ↓
Service
       ↓
DAO
       ↓
MySQL
       ↓
historico.html
```

Na página de resultados, o frontend consulta:

``` text
GET /participantes
GET /partidas
```

Os participantes são ordenados pelo tempo para formar a classificação.

------------------------------------------------------------------------

# 🏗️ Arquitetura da API

A API utiliza uma arquitetura organizada em camadas:

``` text
Frontend
   ↓
Rotas
   ↓
Controller
   ↓
Service
   ↓
DAO
   ↓
MySQL
```

------------------------------------------------------------------------

## Controller

Os Controllers recebem as requisições HTTP e montam as respostas JSON.

Arquivos:

``` text
src/api/controllers/ParticipanteController.php
src/api/controllers/PartidaController.php
```

Exemplos de responsabilidades:

-   receber o JSON;
-   validar se o corpo é um objeto JSON;
-   chamar o Service;
-   montar a resposta;
-   definir o código HTTP.

------------------------------------------------------------------------

## Service

Os Services concentram as regras de negócio.

Arquivos:

``` text
src/api/services/ParticipanteService.php
src/api/services/PartidaService.php
```

Eles realizam validações como:

-   tamanho dos nomes;
-   robô permitido;
-   tempo maior que zero;
-   existência de nomes cadastrados;
-   criação dos participantes durante uma partida;
-   controle da transação de uma partida.

------------------------------------------------------------------------

## DAO

Os DAOs são responsáveis pelo acesso ao banco de dados.

Arquivos:

``` text
src/api/dao/ParticipanteDao.php
src/api/dao/PartidaDao.php
```

Eles executam comandos SQL como:

``` sql
INSERT
SELECT
```

e transformam os dados retornados pelo banco em objetos do sistema.

------------------------------------------------------------------------

## Models

Os Models representam as entidades da aplicação.

Arquivos:

``` text
src/api/models/Participante.php
src/api/models/Partida.php
```

### Participante

Possui:

``` text
id
nome
robo
tempo
```

### Partida

Possui:

``` text
id
nomeparticipante1
nomeparticipante2
```

------------------------------------------------------------------------

## Banco de Dados

A conexão com o MySQL é gerenciada por:

``` text
src/api/database/MysqlDatabase.php
```

A conexão utiliza PDO.

Configuração atual:

``` text
Host: localhost
Porta: 3306
Usuário: root
Senha: vazia
Banco: competicaoRobosAPI
```

------------------------------------------------------------------------

# 🧱 Estrutura do Projeto

A estrutura principal do projeto é:

``` text
API_competicao_robo/
│
├── docs/
│   └── banco.sql
│
├── public/
│   ├── index.php
│   ├── index.html
│   ├── competicao.html
│   ├── partida.html
│   ├── resultados.html
│   ├── historico.html
│   ├── robos.html
│   ├── ApiService.js
│   │
│   ├── css/
│   │   ├── style.css
│   │   └── bootstrap.min.css
│   │
│   ├── js/
│   │   ├── competicao.js
│   │   ├── partida.js
│   │   ├── resultados.js
│   │   ├── historico.js
│   │   └── conometro.js
│   │
│   └── img/
│
├── src/
│   └── api/
│       ├── controllers/
│       ├── dao/
│       ├── database/
│       ├── http/
│       ├── middlewares/
│       ├── models/
│       ├── routes/
│       ├── server/
│       └── services/
│
├── composer.json
├── composer.lock
├── run
└── teste_tudo.php
```

------------------------------------------------------------------------

# 🛠️ Tecnologias Utilizadas

-   **PHP**
-   **Slim Framework 4**
-   **PHP-DI**
-   **PDO**
-   **MySQL**
-   **Composer**
-   **JavaScript**
-   **HTML5**
-   **CSS3**
-   **Bootstrap**

------------------------------------------------------------------------

# ⚙️ Instalação e Execução

## 1. Requisitos

É necessário ter instalado:

-   PHP;
-   MySQL;
-   Composer.

No ambiente utilizado pelo projeto também é possível executar o PHP
através do XAMPP.

------------------------------------------------------------------------

## 2. Instalar as dependências

Dentro da pasta do projeto:

``` bash
composer install
```

------------------------------------------------------------------------

## 3. Criar o banco de dados

Execute o arquivo:

``` text
docs/banco.sql
```

no MySQL.

O script cria o banco:

``` text
competicaoRobosAPI
```

e as tabelas:

``` text
participantes
partidas
```

> **Atenção:** o script contém `DROP DATABASE IF EXISTS`, portanto ele
> apaga o banco `competicaoRobosAPI` antes de recriá-lo.

------------------------------------------------------------------------

## 4. Iniciar a API

Na pasta raiz do projeto:

``` bash
php -S localhost:8080 -t public/
```

No XAMPP, também pode ser utilizado:

``` bash
c:\xampp\php\php.exe -S localhost:8080 -t public/
```

------------------------------------------------------------------------

## 5. Acessar o sistema

Após iniciar o servidor:

``` text
http://localhost:8080
```

------------------------------------------------------------------------

# 🧪 Testando no Postman

Uma forma simples de testar a API é utilizar o Postman.

### Testar participantes

``` text
POST http://localhost:8080/participantes
```

Enviar:

``` json
{
  "participante": {
    "nome": "Equipe Azul",
    "robo": "Azul",
    "tempo": 12.345
  }
}
```

Depois testar:

``` text
GET http://localhost:8080/participantes
```

e:

``` text
GET http://localhost:8080/participantes/1
```

### Testar partidas

Enviar:

``` text
POST http://localhost:8080/partidas
```

com:

``` json
{
  "partida": {
    "nomeparticipante1": "Equipe Azul",
    "nomeparticipante2": "Equipe Vermelha",
    "robo1": "Azul",
    "tempo1": 12.345,
    "robo2": "Vermelho",
    "tempo2": 13.275
  }
}
```

Depois testar:

``` text
GET http://localhost:8080/partidas
```

e:

``` text
GET http://localhost:8080/partidas/1
```

------------------------------------------------------------------------

# 📊 Classificação

A página `resultados.html` consulta os participantes através de:

``` text
GET /participantes
```

Depois:

1.  verifica os tempos válidos;
2.  converte os tempos para números;
3.  ordena do menor para o maior;
4.  seleciona os 10 primeiros participantes;
5.  apresenta nome, robô e tempo.

Também são consultadas as partidas através de:

``` text
GET /partidas
```

para mostrar a quantidade total de partidas.

------------------------------------------------------------------------

# 📝 Observações da Implementação Atual

-   A API não possui endpoints de atualização (`PUT`) implementados.
-   A API não possui endpoints de exclusão (`DELETE`) implementados.
-   Não existe uma tabela `resultados`.
-   Não existe autenticação JWT nas rotas atuais.
-   Os tempos são armazenados em segundos com até três casas decimais.
-   O cadastro de uma partida cria também os dois participantes.
-   A criação de uma partida utiliza transação para evitar registros
    incompletos.
-   Os nomes dos participantes precisam ter entre 3 e 40 caracteres.
-   Os robôs aceitos são `Azul` e `Vermelho`.
-   Os tempos precisam ser maiores que zero.
-   O frontend impede, antes do cadastro, o uso de nomes iguais e nomes
    já cadastrados.
-   O backend também verifica se os nomes já existem.
-   A classificação considera os menores tempos como melhores
    resultados.

------------------------------------------------------------------------

**Última atualização:** 6 de Outubro de 2026
