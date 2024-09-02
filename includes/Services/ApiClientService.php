<?php

use Includes\Traits\ApiRequestTrait;

/**
 * Este é um exemplo de serviço de utilização da trait, cuja ideia é
 * montar funções reutilizáveis e de fácil manutenção para fazer requisições
 * em qualquer API (Uma vez configurada no options.php).
 * Qualquer dúvida com a utilização do serviço, tratar com: raul.oliveira@versatecnologia.com.br
 */

class ApiClientService{
    use ApiRequestTrait;

    private $baseUrl;
    private $authToken;

    public function __construct(){
        $this->baseUrl = API_UNIVERSA_BASE;
        $this->authToken = get_option('universa_auth_token');
    }

    // //Função para autenticação
    // public function authenticate($email, $password){
    //     $url = $this->baseUrl . '/auth/login';
    //     $response = $this->postRequest($url, ['email' => $email, 'password' => $password]);
    //     $data = json_decode($response, true);

    //     if (isset($data['token'])) {
    //         $this->authToken = $data['token'];
    //         update_option('universa_auth_token', $this->authToken);
    //         return $this->authToken;
    //     }

    //     return null;
    // }

    //Função GET
    public function getData($endpoint, $params = []){
        $url = $this->baseUrl . $endpoint;
        $headers = ['Authorization: Bearer ' . $this->authToken];
        return $this->getRequest($url, $params, $headers);
    }

    //Função POST
    public function createData($endpoint, $data){
        $url = $this->baseUrl . $endpoint;
        $headers = ['Authorization: Bearer ' . $this->authToken];
        return $this->postRequest($url, $data, $headers);
    }

    //Função PUT
    public function updateData($endpoint, $data){
        $url = $this->baseUrl . $endpoint;
        $headers = ['Authorization: Bearer ' . $this->authToken];
        return $this->putRequest($url, $data, $headers);
    }

    //Função DELETE
    public function deleteData($endpoint, $data = []){
        $url = $this->baseUrl . $endpoint;
        $headers = ['Authorization: Bearer ' . $this->authToken];
        return $this->deleteRequest($url, $data, $headers);
    }
}
