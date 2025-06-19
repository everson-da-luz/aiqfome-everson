
# Aiqfome - API

Projeto utilizado para um teste técnico, não existe uma aplicação em produção que rode esse código. Esse projeto consiste em criar uma API para criar clientes e os mesmos poderem gerenciar produtos nos seus favoritos.

## Instalação
Para instalar esse projeto é necessário ter o Docker instalado. Após feito o clone do projeto, acessar a pasta do projeto e executar o seguinte comando:
```
docker-compose build
```
Esse comando montará uma nova imagem chamada `aiqfome-everson`. Para montar essa imagem foi utilizado como base a imagem `php:8.4-apache`.

Após isso execute o seguinte comando:
```
docker-compose up -d
```
Esse comando criará dois containers, um para a aplicação, chamado `aiqfome-everson-api`, e outro para a base de dados, chamado `aiqfome-everson-db`. Também será criado uma rede para os dois containers chamada `aiqfome-everson-network`.

Ao criar os containers, o docker executará o arquivo SQL `docker/db/init.sql`, que vai criar a estrutura de tabelas, e também, será adicionado um usuário padrão na tabela `user`.

**ATENÇÃO**: os containers usam a porta 80 e 3306, então caso exista uma aplicação rodando em uma dessas portas, as mesmas devem ser paradas para não conflitar com o projeto.

## Endpoints da API e como utilizar
Para testar a API utilizei o programa Insomnia ([https://insomnia.rest](https://insomnia.rest)). Adicionei um arquivo com os endpoints para ser importado no Insomnia e facilitar os testes `doc/aiqfome-everson-collection.yaml` (acredito que esse arquivo só funcionará para esse programa).

Segue as possíveis rotas da API.

### User (Autenticação)
#### Logar e autenticar na API (POST):
```
http://localhost/user/login
```
Deve ser passado os seguintes dados como `multipart form-data`:
- username
- password (usar senha descrita abaixo)

**ATENÇÃO:** Já deixei um usuário criado no banco de dados, a senha dele é `123456`.

#### Deslogar da API (POST):
```
http://localhost/user/logout
```
Enviar o token gerado na autenticação, enviando-o como Header Auth `Bearer Token`.

### Customer (Cliente)
#### Buscar um cliente (GET): 
```
http://localhost/customer/get/{id}
```
Enviar o token gerado na autenticação, enviando-o como Header Auth `Bearer Token`.

#### Criar um novo cliente (POST):
```
http://localhost/customer/create
```
Deve ser passado os seguintes dados como `multipart form-data`:
- name
- email
Enviar o token gerado na autenticação, enviando-o como Header Auth `Bearer Token`.

#### Editar um cliente existente (PUT):
```
http://localhost/customer/update/{id}
```
Deve ser passado os seguintes dados como JSON no corpo da requisição:
- name
- email
Enviar o token gerado na autenticação, enviando-o como Header Auth `Bearer Token`.

**ATENÇÃO**: Não utilizar `multipart form-data` no PUT, pois o PHP não consegue recuperar os dados assim.


#### Excluir um cliente (DELETE):
```
http://localhost/customer/delete/{id}
```
Enviar o token gerado na autenticação, enviando-o como Header Auth `Bearer Token`.

### Favorite (Favorito)
#### Buscar os produtos favoritos por cliente (GET):
```
http://localhost/favorite/get/{customer_id}
```
Enviar o token gerado na autenticação, enviando-o como Header Auth `Bearer Token`.

#### Adicionar produto nos favoritos (POST):
```
http://localhost/favorite/add
```
Deve ser passado os seguintes dados como `multipart form-data`:
- customer_id
- product_id (ID do produto pode ser encontrado na API Fake [https://fakestoreapi.com/products](https://fakestoreapi.com/products))
Enviar o token gerado na autenticação, enviando-o como Header Auth `Bearer Token`.

#### Excluir dos favoritos (DELETE):
```
http://localhost/favorite/remove/{id}
```
Enviar o token gerado na autenticação, enviando-o como Header Auth `Bearer Token`.

## Tecnologias utilizadas
O servidor é um Apache e o banco de dados MySQL 8.4. Como linguagem de programação foi utilizado o PHP 8.4.

## Como foi construído esse projeto
Utilizei PHP puro, sem nenhum framework.

Existe um arquivo chamado `.env` onde é definido as informações para conexão com o banco de dados.

Quando o container do banco de dados é criado, é executado um SQL inicial, que encontra-se no diretório `docker/db`, isso para ser criado a estrutura de tabelas necessária. Adicionei o diagrama ER no diretório `doc/aiqfome-everson-er.png`

Criei um arquivo `.htaccess` para utilizar URLs amigáveis.

Existe um arquivo de rotas `app/config/routes.php`, nele é definido as rotas possíveis da API.

O arquivo `index.php` inicia a aplicação, busca as rotas e verifica se a URL digitada se enquadra em uma rota, caso se enquadre, é chamado um controller especifico para tratar a requisição solicitada.

Um tempo atrás tinha desenvolvido uma classe para fazer requisições em APIs, utilizando o `cURL`, então coloquei essa minha biblioteca no projeto, ela está no diretório `app\lib\Api.php`. Utilizei essa bilioteca para buscar os produtos fake.

E como recomendado utilizei a API fake [https://fakestoreapi.com](https://fakestoreapi.com) para buscar e validar produtos.

A aplicação autenticará o token enviado nos headers, bem como verificará o verbo HTTP da requisição, caso seja conforme o esperado, ela validará os dados e retornará uma resposta com o código HTTP adequado.

## Considerações e ressalvas
O fato de eu não ter utilizado um framework não quer dizer que eu não os conheça. Já trabalhei com diversos frameworks, como CakePHP, CodeIgniter, Yii, Zend, e Laravel. Optei em não criar um projeto grande e ter que configura-lo, sem contar que algumas coisas eu teria que pesquisar como fazer no framework e poderia levar mais tempo que o esperado.

Por eu não ter utilizado um framework, reconheço que eu fiz algumas coisas de forma simples, mas isso aconteceu somente porque não podia perder muito tempo para melhora-las, por exemplo, poderia ter criado um ORM melhor, mas apenas coloquei o SQL na model e utilizei o `PDO` que é nativo do PHP. E também, as rotas e os controllers, sei que poderia ter sido melhor, mas não tive tanto tempo para faze-los.

Na autenticação criei um token simples, apenas colocando MD5 no usuário e senha e criptografando com base 64, sei que não é o ideal, poderia ter criado um token JWT, mas eu levaria mais tempo para isso. Então resolvi criar uma coisa simples apenas para demonstrar como seria uma autenticação.

Estou falando de não ter tempo porque que eu tinha serviços marcados para o meu apartamento, estou em fase de obras e mudanças, então precisei acompanhar os serviços.

Não utilizei o autoload do `composer`, portanto tive que incluir diversos arquivos com `require`, com isso quase todas as classes tem um `require_once`. Optei por não utilizar o `composer` devido o fato que teria que coloca-lo na imagem criada pelo Docker, e após subir o container seria necessário acessar o container e executar o comando para criar as dependências e criar o autoload, eu poderia tentar criar um `entrypoint` ou algo parecido para tentar rodar o comando automaticamente, mas não sei fazer agora, então poderia perder muito tempo tentando implementar isso.
