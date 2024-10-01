<?php
defined('ABSPATH') or die('Not Authorized!');

include_once API_UNIVERSA_DIRECTORY_PATH . 'includes/Traits/ApiRequestTrait.php';
include_once API_UNIVERSA_DIRECTORY_PATH . 'includes/Services/ApiClientService.php';

use includes\Services\ApiClientService;

/**
 * Returns the main Service of ApiUniversa.
 *
 * @since  1.0
 * @return ApiClientService
 */

function ApiUniversa() {
	return new ApiClientService();
}

function api_universa_add_admin_menu() {
    add_menu_page(
        'Universa',
        'Universa',
        'manage_options',
        'api-universa-configuracoes',
        'api_universa_configuracoes_page',
        'dashicons-welcome-learn-more',
        20
    );

    add_submenu_page(
        'api-universa-configuracoes',
        'Campus',
        'Campus',
        'manage_options',
        'edit.php?post_type=campus'
    );

    add_submenu_page(
        'api-universa-configuracoes',
        'Cursos',
        'Cursos',
        'manage_options',
        'edit.php?post_type=course'
    );
    
    remove_submenu_page('api-universa-configuracoes', 'api-universa-configuracoes');
    add_submenu_page(
        'api-universa-configuracoes',
        'Configurações',
        'Configurações',
        'manage_options',
        'api-universa-configuracoes',
        'api_universa_configuracoes_page'
    );
}

add_action('admin_menu', 'api_universa_add_admin_menu');

function api_universa_admin_enqueue_styles($hook) {
    if ($hook === 'toplevel_page_api-universa-admin' || 
        strpos($hook, 'api-universa-configuracoes') !== false ||
        strpos($hook, 'edit.php?post_type=campus') !== false ||
        strpos($hook, 'edit.php?post_type=course') !== false) {
        
        wp_enqueue_style(
            'api-universa-admin-style',
            API_UNIVERSA_DIRECTORY_URL . '/assets/css/style.css',
            array(),
            '1.0',
            'all'
        );
    }
}
add_action('admin_enqueue_scripts', 'api_universa_admin_enqueue_styles');

function api_universa_configuracoes_page() {
    include API_UNIVERSA_DIRECTORY_PATH . 'admin/config.php';
}


function register_custom_post_types() {
    register_post_type('campus', [
        'labels' => [
            'name'               => __('Campus'),
            'singular_name'      => __('Campus'),
            'menu_name'          => __('Campus'),
            'all_items'          => __('Todos os Campus'),
            'add_new_item'       => __('Adicionar Novo Campus'),
            'edit_item'          => __('Editar Campus'),
            'new_item'           => __('Novo Campus'),
            'view_item'          => __('Ver Campus'),
            'search_items'       => __('Buscar Campus'),
            'not_found'          => __('Nenhum campus encontrado'),
            'not_found_in_trash' => __('Nenhum campus encontrado na lixeira'),
        ],
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => false,
        'menu_icon' => 'dashicons-building',
        'capability_type' => 'post',
        'map_meta_cap' => true,
        'supports' => ['title', 'custom-fields'],
    ]);

    register_post_type('course', [
        'labels' => [
            'name'               => __('Cursos'),
            'singular_name'      => __('Curso'),
            'menu_name'          => __('Cursos'),
            'all_items'          => __('Todos os Cursos'),
            'add_new_item'       => __('Adicionar Novo Curso'),
            'edit_item'          => __('Editar Curso'),
            'new_item'           => __('Novo Curso'),
            'view_item'          => __('Ver Curso'),
            'search_items'       => __('Buscar Cursos'),
            'not_found'          => __('Nenhum curso encontrado'),
            'not_found_in_trash' => __('Nenhum curso encontrado na lixeira'),
        ],
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => false,
        'menu_icon' => 'dashicons-book',
        'capability_type' => 'post',
        'map_meta_cap' => true,
        'supports' => ['title', 'custom-fields'],
    ]);

    
}

add_action('init', 'register_custom_post_types');

function add_and_reorder_course_columns($columns) {
    $reordered_columns = [];

    $reordered_columns['cb'] = $columns['cb'];
    $reordered_columns['title'] = $columns['title'];

    $reordered_columns['image_url'] = __('Imagem');
    foreach ($columns as $key => $value) {
        if ($key !== 'cb' && $key !== 'title' && $key !== 'date') {
            $reordered_columns[$key] = $value;
        }
    }

    $reordered_columns['date'] = $columns['date'];

    return $reordered_columns;
}
add_filter('manage_course_posts_columns', 'add_and_reorder_course_columns');

function display_course_image_column($column, $post_id) {
    if ($column === 'image_url') {
        $image_url = get_post_meta($post_id, 'image_url', true);
        if ($image_url) {
            echo '<img src="' . esc_url($image_url) . '" style="max-width:100px; height:auto;" />';
        } else {
            echo __('Sem Imagem');
        }
    }
}
add_action('manage_course_posts_custom_column', 'display_course_image_column', 10, 2);



function customize_sync_button() {
    global $pagenow, $typenow;

    if (($pagenow === 'edit.php' || $pagenow === 'post.php') && ($typenow === 'course' or $typenow === 'campus')) {
        ?>
        <script type="text/javascript">
            document.addEventListener('DOMContentLoaded', function() {
                const updateButton = document.querySelector('#publish');

                if (updateButton) {
                    updateButton.style.display = 'none';
                }
                const addNewButton = document.querySelector('.page-title-action');
                if (addNewButton) {
                    addNewButton.textContent = '<?php echo $typenow === 'course' ? 'Sincronizar Cursos' : 'Sincronizar Campus'; ?>';

                    addNewButton.addEventListener('click', function(e) {
                        e.preventDefault();

                        addNewButton.textContent = 'Sincronizando...';

                        fetch('<?php echo admin_url('admin-ajax.php?action=sync_' . ($typenow === 'course' ? 'courses' : 'campuses')); ?>', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            credentials: 'same-origin'
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert('Cursos sincronizados com sucesso!');
                                window.location.reload();
                            } else {
                                alert('Erro na sincronização: ' + data.data);
                            }
                            addNewButton.textContent = '<?php echo $typenow === 'course' ? 'Sincronizar Cursos' : 'Sincronizar Campus'; ?>';
                        })
                        .catch(error => {
                            alert('Erro na sincronização.');
                            console.error('Erro:', error);
                            addNewButton.textContent = '<?php echo $typenow === 'course' ? 'Sincronizar Cursos' : 'Sincronizar Campus'; ?>';
                        });
                    });
                }
            });
        </script>
        <?php
    }
}
add_action('admin_footer', 'customize_sync_button');

function sync_courses_callback() {
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Permissão negada.');
    }

    $client = ApiUniversa();
    $result = $client->synchronize_courses(get_option('universa_batch_size'));

    if (!empty($result['errors'])) {
        wp_send_json_error('Erros durante a sincronização.');
    } else {
        wp_send_json_success();
    }
}
add_action('wp_ajax_sync_courses', 'sync_courses_callback');

function sync_campuses_callback() {
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Permissão negada.');
    }

    $client = ApiUniversa();
    $result = $client->synchronize_campuses(get_option('universa_batch_size'));

    if (!empty($result['errors'])) {
        wp_send_json_error('Erros durante a sincronização.');
    } else {
        wp_send_json_success();
    }
}
add_action('wp_ajax_sync_campuses', 'sync_campuses_callback');

function remove_add_new_menu_items() {
    global $submenu;
    unset($submenu['edit.php?post_type=course'][10]);
    unset($submenu['edit.php?post_type=campus'][10]);
}
add_action('admin_menu', 'remove_add_new_menu_items');

function remove_add_new_button() {
    global $wp_admin_bar;
    $wp_admin_bar->remove_node('new-course');
    $wp_admin_bar->remove_node('new-campus');
}
add_action('admin_bar_menu', 'remove_add_new_button', 999);

function schedule_token_verification() {
    $client   = ApiUniversa();
    $email    = get_option('universa_email');
    $password = get_option('universa_password');
    $data = [
        'email' => $email,
        'password' => $password
    ];
    
    $data_token = $client->createData('/v1/auth/login', $data);

    if (isset($data_token["token"])) {
        $token = $data_token["token"];
        update_option('universa_email', $email);
        update_option('universa_password', $password);
        update_option('universa_auth_token', $token);
        return $token;
    }

    return false;
 }
 add_action( 'evento_verificar_token','schedule_token_verification' );

 $token_timer = get_option('universa_token_timer') != '' ? get_option('universa_token_timer') : 60;
 wp_schedule_single_event( time() + $token_timer * 60, 'evento_verificar_token' );

 function schedule_synchronic() {
    $client = ApiUniversa();
    $client->synchronize_courses(get_option('universa_batch_size'));
    $client->synchronize_campuses(get_option('universa_batch_size'));
 }

 add_action( 'evento_sincronizar','schedule_synchronic' );

 $sync_timer  = get_option('universa_sync_timer')  != '' ? get_option('universa_sync_timer')  : 24;
 wp_schedule_single_event( time() + $sync_timer * 60 * 60, 'evento_sincronizar' );