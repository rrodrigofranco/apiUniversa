<?php

namespace includes\Traits;

/**
 * Esta TRAIT tem a funcionalidade de padronizar utilização de requisições diretas na API (Uma vez configurada no options.php)
 * É aconselhável o uso do serviço, cuja ideia é ser "Plug and play", onde será necessário menor gama de configuraçoes
 * e ajustes no código para a utilização das funções.
 * Qualquer dúvida com a utilização da TRAIT, tratar com: raul.oliveira@versatecnologia.com.br
 */

trait ApiRequestTrait{
    //Função para montar a requisição
    protected function makeRequest($url, $method = 'GET', $data = [], $headers = []){
        $curl = curl_init();

        //Configurando metodo de requisição
        switch (strtoupper($method)) {
            case 'POST':
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
                break;
            case 'PUT':
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
                curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
                break;
            case 'DELETE':
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;
            default: //Caso não seja nenhum dos metodos acima, será GET por padrão.
                if (!empty($data)) {
                    $url .= '?' . http_build_query($data);
                }
                break;
        }

        //Configurações gerais do cURL
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($curl);

        if (curl_errno($curl)) {
            throw new Exception('Request Error: ' . curl_error($curl));
        }

        curl_close($curl);

        return $response;
    }

    public function getRequest($url, $data = [], $headers = []){
        return $this->makeRequest($url, 'GET', $data, $headers);
    }

    public function postRequest($url, $data = [], $headers = []){
        return $this->makeRequest($url, 'POST', $data, $headers);
    }

    public function putRequest($url, $data = [], $headers = []){
        return $this->makeRequest($url, 'PUT', $data, $headers);
    }

    public function deleteRequest($url, $data = [], $headers = []){
        return $this->makeRequest($url, 'DELETE', $data, $headers);
    }
}
