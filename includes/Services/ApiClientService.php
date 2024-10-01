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
        $this->baseUrl   = API_UNIVERSA_BASE;
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
    public function synchronize_campuses($batch_size = 100) {
        $url = $this->baseUrl . '/v1/campuses';
        $headers = ['Authorization: Bearer ' . $this->authToken];
        $campus_data = json_decode($this->getRequest($url, [], $headers), true);
        $results = [];
    
        if(isset($campus_data['data'])) {
            foreach ($campus_data['data'] as $campus) {
                $campus_id = $campus["id"];
        
                $args = [
                    'post_type'  => 'campus',
                    'meta_query' => [
                        [
                            'key'   => 'campuses_id',
                            'value' => $campus_id,
                            'compare' => '='
                        ]
                    ]
                ];
        
                $query = new \WP_Query($args);
        
                $post_data = [
                    'post_title'   => $campus["name"],
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
        
                if ($query->have_posts()) {
                    $existing_post_id = $query->posts[0]->ID;
                    $post_data['ID'] = $existing_post_id;
                    $updated_post_id = wp_update_post($post_data);
        
                    if (is_wp_error($updated_post_id)) {
                        $results['errors'][] = [
                            'campus_id' => $campus_id,
                            'error' => $updated_post_id->get_error_message()
                        ];
                    } else {
                        $results['updated'][] = $updated_post_id;
                    }
                } else {
                    $post_id = wp_insert_post($post_data);
        
                    if (is_wp_error($post_id)) {
                        $results['errors'][] = [
                            'campus_id' => $campus_id,
                            'error' => $post_id->get_error_message()
                        ];
                    } else {
                        $results['inserted'][] = $post_id;
                    }
                }
        
                wp_reset_postdata();
            }
        }else{
            $results['errors'][] = [
                'error' => 'Token Inválido!'
            ];
        }
        
    
        return $results;
    }

    // Método de sincronização de cursos em lotes com depuração
    public function synchronize_courses($batch_size = 100)
    {
        $results = [];
        $knowledgeFields = [];

        // Recupera a URL da página da API a partir dos transients (se houver)
        $url = get_transient('courses_sync_url');
        if (!$url) {
            $url = $this->baseUrl . '/v1/courses?page=1'; // URL inicial da página
            error_log("Sincronização iniciada na URL inicial: {$url}");
        }

        $headers = ['Authorization: Bearer ' . $this->authToken];
        $processed_courses = 0;

        // Certifique-se de que a autenticação e a URL estão corretas
        if (!$this->authToken) {
            error_log("Erro: Token de autenticação ausente.");
            return ['error' => 'Token de autenticação ausente'];
        }
        if (!$url) {
            error_log("Erro: URL inválida.");
            return ['error' => 'URL inválida'];
        }

        while ($url && $processed_courses < $batch_size) {
            error_log("Fazendo requisição para: {$url}");

            $response = $this->getRequest($url, [], $headers);

            if (!$response) {
                error_log("Erro: Sem resposta da API na URL {$url}");
                return ['error' => "Sem resposta da API na URL {$url}"];
            }

            $course_data = json_decode($response, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                error_log("Erro na decodificação JSON: " . json_last_error_msg());
                return ['error' => 'Erro na decodificação JSON: ' . json_last_error_msg()];
            }

            if (!isset($course_data['data']) || !is_array($course_data['data'])) {
                error_log("Erro: Estrutura de dados de curso não encontrada ou inesperada.");
                return ['error' => 'Dados de curso não encontrados ou estrutura inesperada.'];
            }

            error_log("Dados recebidos corretamente da URL: {$url}");

            foreach ($course_data['data'] as $course) {
                $processed_courses++;
                if ($processed_courses > $batch_size) {
                    break; // Limita o lote
                }

                // Extraindo a área de conhecimento do curso
                $knowledge_field = $course["knowledge_field"] ?? [];
                $knowledge_field_id = $knowledge_field["id"] ?? '';
                $knowledge_field_desc = $knowledge_field["description"] ?? '';

                if ($knowledge_field_id && !isset($knowledgeFields[$knowledge_field_id])) {
                    $knowledgeFields[$knowledge_field_id] = $knowledge_field_desc;
                    error_log("Área de conhecimento adicionada: {$knowledge_field_desc} (ID: {$knowledge_field_id})");
                }

                $course_id = $course["id"];
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

                if (isset($course["campuses"])) {
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
                }

                $campuses_serialized = maybe_serialize($campuses_data);

                // Criar ou verificar a existência da categoria com base na área de conhecimento
                $category_id = null;
                if ($knowledge_field_desc) {
                    $category = term_exists($knowledge_field_desc, 'category');
                    if (!$category) {
                        $category = wp_insert_term(
                            $knowledge_field_desc,
                            'category'
                        );
                        error_log("Categoria criada: {$knowledge_field_desc} (ID: {$category['term_id']})");
                    }
                    $category_id = is_array($category) ? $category['term_id'] : $category;
                }

                // Buscando as disciplinas do curso no endpoint /v1/courses/{course_id}
                $discipline_endpoint = '/v1/courses/' . $course_id;
                $course_detail = $this->getData($discipline_endpoint);

                if (!$course_detail) {
                    error_log("Erro: Não foi possível buscar detalhes do curso ID {$course_id}");
                    return ['error' => "Erro ao buscar detalhes do curso ID {$course_id}"];
                }

                // Montando conteúdo das disciplinas para o post
                $disciplines_content = '';
                if (isset($course_detail['disciplines'])) {
                    $disciplines_content .= "<h2>Matriz Curricular</h2>\n\n";
                    foreach ($course_detail['disciplines'] as $discipline) {
                        $disciplines_content .= sprintf(
                            "- %s, Carga Horária: %s horas\n",
                            $discipline['name'] ?? '',
                            $discipline['workload'] ?? '0'
                        );
                    }
                }

                $post_data = [
                    'post_title'   => $course["name"],
                    'post_content' => $disciplines_content, // Insere as disciplinas no conteúdo do post
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
                        'knowledge_field_desc' => $course["knowledge_field"]["description"] ?? '',
                        'image_url'           => $course["image_url"] ?? '',
                    ],
                    'post_category' => [$category_id], // Define a categoria do curso
                ];

                if ($query->have_posts()) {
                    $existing_post_id = $query->posts[0]->ID;
                    $post_data['ID'] = $existing_post_id;

                    $updated_post_id = wp_update_post($post_data);
                    if (is_wp_error($updated_post_id)) {
                        $results['errors'][] = [
                            'course_id' => $course_id,
                            'error' => $updated_post_id->get_error_message()
                        ];
                        error_log("Erro ao atualizar curso ID {$course_id}: " . $updated_post_id->get_error_message());
                    } else {
                        $results['updated'][] = $updated_post_id;
                        error_log("Curso ID {$updated_post_id} atualizado com sucesso.");
                    }
                } else {
                    $post_id = wp_insert_post($post_data);

                    if (is_wp_error($post_id)) {
                        $results['errors'][] = [
                            'course_id' => $course_id,
                            'error' => $post_id->get_error_message()
                        ];
                        error_log("Erro ao inserir curso ID {$course_id}: " . $post_id->get_error_message());
                    } else {
                        $results['inserted'][] = $post_id;
                        error_log("Curso ID {$post_id} inserido com sucesso.");
                    }
                }

                wp_reset_postdata();
            }

            // Próxima página
            $url = isset($course_data['links']['next']) ? $course_data['links']['next'] : null;

            if ($url) {
                set_transient('courses_sync_url', $url, 12 * HOUR_IN_SECONDS); // Armazena a URL da próxima página
            } else {
                delete_transient('courses_sync_url');
                error_log("Sincronização finalizada.");
            }
        }

        return ['results' => $results, 'knowledgeFields' => $knowledgeFields];
    }
}
