<?php

namespace includes\Services;
use includes\Traits\ApiRequestTrait;

/**
 * Este é um exemplo de serviço de utilização da trait, cuja ideia é
 * montar funções reutilizáveis e de fácil manutenção para fazer requisições
 * em qualquer API (Uma vez configurada no options.php).
 * Qualquer dúvida com a utilização do serviço, tratar com: raul.oliveira@versatecnologia.com.br
 */

 final class ApiClientService{
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

    //Método GET
    public function getData($endpoint, $params = []){
        $url = $this->baseUrl . $endpoint;
        $headers = ['Authorization: Bearer ' . $this->authToken];
        return json_decode($this->getRequest($url, $params, $headers), true);
    }

    //Método POST
    public function createData($endpoint, $data){
        $url = $this->baseUrl . $endpoint;
        $headers = ['Authorization: Bearer ' . $this->authToken];
        return json_decode($this->postRequest($url, $data, $headers), true);
    }

    //Método PUT
    public function updateData($endpoint, $data){
        $url = $this->baseUrl . $endpoint;
        $headers = ['Authorization: Bearer ' . $this->authToken];
        return json_decode($this->putRequest($url, $data, $headers), true);
    }

    //Método DELETE
    public function deleteData($endpoint, $data = []){
        $url = $this->baseUrl . $endpoint;
        $headers = ['Authorization: Bearer ' . $this->authToken];
        return json_decode($this->deleteRequest($url, $data, $headers), true);
    }

    // Método de sincronização de campus
    public function synchronize_campuses() {
        $url = $this->baseUrl . '/v1/campuses';
        $headers = ['Authorization: Bearer ' . $this->authToken];
        $campus_data = json_decode($this->getRequest($url, [], $headers), true);

        foreach($campus_data as $campus) {
            $campuses_id = $campus["id"];
    
            $args = [
                'post_type'  => 'campus',
                'meta_query' => [
                    [
                        'key'   => 'campuses_id',
                        'value' => $campuses_id,
                        'compare' => '='
                    ]
                ]
            ];
        
            $post_data = [
                'post_title'   => sanitize_text_field($campus["name"]),
                'post_content' => '',
                'post_status'  => 'publish',
                'post_type'    => 'campus',
                'meta_input'   => [
                    'campuses_id'    => $campus["id"] ?? '',
                    'institution_id' => $campus["institution_id"] ?? '',
                    'phone'          => $campus["phone"] ?? '',
                    'email'          => $campus["email"] ?? '',
                    'website'        => $campus["website"] ?? '',
                ]
            ];
        
            $query = new \WP_Query($args);
        
            if ($query->have_posts()) {
                $existing_post_id = $query->posts[0]->ID;
                $post_data['ID'] = $existing_post_id;
                $updated_post_id = wp_update_post($post_data);
        
                if (is_wp_error($updated_post_id)) {
                    $results['errors'][] = [
                        'campus_id' => $campuses_id,
                        'error' => $updated_post_id->get_error_message()
                    ];
                } else {
                    $results['updated'][] = $updated_post_id;
                }
            } else {
                $post_id = wp_insert_post($post_data);
        
                if (is_wp_error($post_id)) {
                    $results['errors'][] = [
                        'campus_id' => $campuses_id,
                        'error' => $post_id->get_error_message()
                    ];
                } else {
                    $results['inserted'][] = $post_id;
                }
            }
        
            wp_reset_postdata();
        }

        return $results;
    }

    //Método de sincronização de cursos
    
    public function synchronize_courses() {
        $url = $this->baseUrl . '/v1/courses';
        $headers = ['Authorization: Bearer ' . $this->authToken];
        $course_data = json_decode($this->getRequest($url, [], $headers), true);
    
        foreach($course_data as $course) {
            $course_id =  $course["id"];
            $args = [
                'post_type'  => 'course',
                'meta_query' => [
                    [
                        'key'   => 'course_id',
                        'value' => $course_id,
                        'compare' => '='
                    ]
                ]
            ];
    
            $query = new \WP_Query($args);
            $campuses_data = [];
            foreach ($course["campuses"] as $campus) {
                $campuses_data[] = [
                    'campus_id'      => $campus["id"] ?? '',
                    'institution_id' => $campus["institution_id"] ?? '',
                    'campus_name'    => $campus["name"] ?? '',
                    'phone'          => $campus["phone"] ?? '',
                    'email'          => $campus["email"] ?? '',
                    'website'        => $campus["website"] ?? '',
                ];
            }
        
            $campuses_serialized = maybe_serialize($campuses_data);
        
            $post_data = [
                'post_title'   => sanitize_text_field($course["name"]),
                'post_content' => '',
                'post_status'  => 'publish',
                'post_type'    => 'course',
                'meta_input'   => [
                    'course_id'           => $course["id"] ?? '',
                    'acronym'             => $course["acronym"] ?? '',
                    'unit_of_measurement' => $course["unit_of_measurement"] ?? '',
                    'workload'            => $course["workload"] ?? '',
                    'url_to_enroll'       => $course["url_to_enroll"] ?? '',
                    'campuses'            => $campuses_serialized,
                    'degree_level_id'     => $course["degree_level"]["id"] ?? '',
                    'degree_level_name'   => $course["degree_level"]["name"] ?? '',
                    'knowledge_field_id'  => $course["knowledge_field"]["id"] ?? '',
                    'knowledge_field_desc'=> $course["knowledge_field"]["description"] ?? '',
                    'image_url'           => $course["image_url"] ?? '',
                ]
            ];
    
            if ($query->have_posts()) {
                $existing_post_id = $query->posts[0]->ID;
                $post_data['ID'] = $existing_post_id;
    
                $updated_post_id = wp_update_post($post_data);
    
                if (is_wp_error($updated_post_id)) {
                     $results['errors'][] = [
                        'campus_id' => $course_id,
                        'error' => $updated_post_id->get_error_message()
                    ];
                } else {
                    $results['updated'][] = $updated_post_id;
                }
            } else {
                $post_id = wp_insert_post($post_data);
    
                if (is_wp_error($post_id)) {
                    $results['errors'][] = [
                        'campus_id' => $course_id,
                        'error' => $updated_post_id->get_error_message()
                    ];
                } else {
                    $results['inserted'][] = $post_id;
                }
            }
    
            wp_reset_postdata();
        }

        return $results;
    }
}
