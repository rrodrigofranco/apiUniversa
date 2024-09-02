# Api Universa Plugin

Este plugin do WordPress permite realizar autenticação e consultas à API da Universa.

## Pré-requisitos

Antes de instalar e usar o plugin, certifique-se de que seu ambiente WordPress atende aos seguintes requisitos:

- **Versão do WordPress:** 5.0 ou superior
- **Versão do PHP:** 7.4 ou superior
- **Extensões PHP:** cURL, JSON (ambas geralmente estão ativas por padrão)

## Instalação

### Método 1: Instalação via WordPress Admin

1. **Baixe o Plugin:**
   - Clique no botão "Download" para baixar o arquivo zip do plugin.

2. **Acesse o Admin do WordPress:**
   - Faça login no painel administrativo do WordPress.

3. **Navegue até a Página de Plugins:**
   - No menu à esquerda, clique em **Plugins** > **Adicionar Novo**.

4. **Carregue o Plugin:**
   - Clique no botão **Carregar Plugin**.
   - Selecione o arquivo zip do plugin que você baixou.

5. **Instale e Ative o Plugin:**
   - Clique em **Instalar Agora** e, após a instalação, clique em **Ativar**.

### Método 2: Instalação Manual via FTP

1. **Extrair o Plugin:**
   - Extraia o arquivo zip do plugin para o seu computador.

2. **Conectar ao seu Servidor via FTP:**
   - Use um cliente FTP para conectar-se ao servidor onde seu site WordPress está hospedado.

3. **Upload do Plugin:**
   - Navegue até o diretório `/wp-content/plugins/` em seu servidor.
   - Faça o upload da pasta extraída do plugin para este diretório.

4. **Ativar o Plugin:**
   - Acesse o painel administrativo do WordPress.
   - Navegue até **Plugins** e encontre o "Api Universa Plugin".
   - Clique em **Ativar**.

## Configuração

1. **Acesse a Página de Configurações do Plugin:**
   - Após ativar o plugin, vá até **Configurações** > **API Universa** no painel administrativo.

2. **Insira suas Credenciais da API:**
   - Insira as credenciais fornecidas pela API da Universa.

3. **Atualize os Links Permanentes:**
   - Vá até **Configurações** > **Links Permanentes** no painel administrativo.
   - Clique em **Salvar Alterações** sem fazer nenhuma modificação para atualizar as regras de rewrite. Isso é necessário para garantir que todas as rotas e endpoints personalizados do plugin funcionem corretamente.

4. **Salvar Configurações:**
   - Retorne à página de configurações do plugin e clique no botão **Salvar Alterações** para aplicar as configurações.

## Como Utilizar

### Usando o Serviço `ApiClientService`

- **Autenticação:**
  - Utilize as configurações fornecidas para autenticar na API da Universa.
- **Consultas:**
  - Após autenticar, você pode usar o plugin para realizar consultas específicas à API.

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