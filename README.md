# Api Universa Plugin

Este plugin do WordPress permite realizar autenticação e consultas à API da Universa.

## Instalação

1. Como fazer a instalação do plugin????

## Configuração

Antes de utilizar o plugin, configure as opções necessárias em `includes/options.php`.

## Como Utilizar

### Usando o Serviço `ApiClientService`

O serviço `ApiClientService` encapsula as funcionalidades de requisições cURL e permite interagir facilmente com a API da Universa.

#### Autenticação
Para autenticar um usuário e obter um token:

```php
use includes\Services\ApiClientService;

$client = new ApiClientService();
```

#### Consultar Dados
Para fazer uma requisição GET:
```php
$response = $client->getData('/endpoint', ['param' => 'value']);
// $response conterá a resposta da API
```

#### Criar Novo Registro
Para criar um novo registro via POST:
```php
$response = $client->createData('/endpoint', [
    'field1' => 'value1',
    'field2' => 'value2'
]);
// $response conterá a resposta da API
```

#### Atualizar Registro
Para atualizar um registro via PUT:
```php
$response = $client->updateData('/endpoint/123', [
    'field1' => 'new value'
]);
// $response conterá a resposta da API
```

#### Deletar Registro
Para deletar um registro via DELETE:
```php
$response = $client->deleteData('/endpoint/123');
// $response conterá a resposta da API
```

## Créditos

Este plugin foi criado por Rodrigo Franco e recebeu uma feature de funcionalidades de consumo à API por Raul de Oliveira Gonçalves.