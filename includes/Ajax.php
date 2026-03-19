<?php
//ajax future
if(!function_exists('dt_add_featured')){
    function dt_add_featured(){
        $postid	 = oIsset($_REQUEST,'postid');
        update_post_meta($postid, 'ophim_featured_post','1');
        die();
    }
    add_action('wp_ajax_dt_add_featured', 'dt_add_featured');
    add_action('wp_ajax_nopriv_dt_add_featured', 'dt_add_featured');
}

if(!function_exists('dt_remove_featured')){
    function dt_remove_featured(){
        $postid	= oIsset($_REQUEST,'postid');

        delete_post_meta( $postid, 'ophim_featured_post');
        die();
    }
    add_action('wp_ajax_dt_remove_featured', 'dt_remove_featured');
    add_action('wp_ajax_nopriv_dt_remove_featured', 'dt_remove_featured');
}

//end ajax


//ajax update ophim setting



add_action('wp_ajax_save_crawl_ophim_schedule_secret', 'save_crawl_ophim_schedule_secret');
function save_crawl_ophim_schedule_secret()
{
    update_option(CRAWL_OPHIM_OPTION_SECRET_KEY, $_POST['secret_key']);
    die();
}


add_action('wp_ajax_ophim_save_config_cssjs', 'ophim_save_config_cssjs');
function ophim_save_config_cssjs()
{
    update_option('ophim_include_css', stripslashes_deep($_POST['css']));
    update_option('ophim_include_js', stripslashes_deep($_POST['js']));
    die();
}

add_action('wp_ajax_crawl_ophim_schedule_enable', 'crawl_ophim_schedule_enable');
function crawl_ophim_schedule_enable()
{
    $schedule = array(
        'enable' => $_POST['enable'] === 'true' ? true : false
    );
    file_put_contents(CRAWL_OPHIM_PATH_SCHEDULE_JSON, json_encode($schedule));
    die();
}

add_action('wp_ajax_crawl_ophim_save_settings', 'crawl_ophim_save_settings');
function crawl_ophim_save_settings()
{
    $data = array(
        'pageFrom' => $_POST['pageFrom'] ?? 5,
        'pageTo' => $_POST['pageTo'] ?? 1,
        'crawl_resize_size_thumb' => $_POST['crawl_resize_size_thumb'] ?? null,
        'crawl_resize_size_thumb_w' => $_POST['crawl_resize_size_thumb_w'] ?? 0,
        'crawl_resize_size_thumb_h' => $_POST['crawl_resize_size_thumb_h'] ?? 0,
        'crawl_resize_size_poster' => $_POST['crawl_resize_size_poster'] ?? null,
        'crawl_resize_size_poster_w' => $_POST['crawl_resize_size_poster_w'] ?? 0,
        'crawl_resize_size_poster_h' => $_POST['crawl_resize_size_poster_h'] ?? 0,
        'crawl_convert_webp' => $_POST['crawl_convert_webp'] ?? null,
        'filterType' => $_POST['filterType'] ?? array(),
        'filterCategory' => $_POST['filterCategory'] ?? array(),
        'filterCountry' => $_POST['filterCountry'] ?? array(),
        'filterGenreTopxx' => isset($_POST['filterGenreTopxx']) && is_array($_POST['filterGenreTopxx']) ? array_map('sanitize_text_field', $_POST['filterGenreTopxx']) : array(),
    );
    if (!get_option(CRAWL_OPHIM_OPTION_SETTINGS)) {
        add_option(CRAWL_OPHIM_OPTION_SETTINGS, json_encode($data));
    } else {
        update_option(CRAWL_OPHIM_OPTION_SETTINGS, json_encode($data));
    }
    die();
}

add_action('wp_ajax_crawl_ophim_page', 'crawl_ophim_page');
function crawl_ophim_page()
{
    echo crawl_ophim_page_handle($_POST['url']);
    die();
}

add_action('wp_ajax_crawl_topxx_page', 'crawl_topxx_page');
function crawl_topxx_page()
{
    $language = isset($_POST['language']) ? sanitize_text_field($_POST['language']) : 'vi';
    echo crawl_topxx_page_handle($_POST['url'], $language);
    die();
}

add_action('wp_ajax_crawl_topxx_save_filter_genre', 'crawl_topxx_save_filter_genre');
function crawl_topxx_save_filter_genre()
{
    if (!defined('OFIM_CACHE_FILTER_GENRE_TOPXX')) {
        wp_send_json_error(array('msg' => 'Constant not defined'));
        return;
    }
    $list = isset($_POST['filterGenreTopxx']) && is_array($_POST['filterGenreTopxx'])
        ? $_POST['filterGenreTopxx']
        : (isset($_POST['filterGenreTopxx']) ? array($_POST['filterGenreTopxx']) : array());
    $list = array_values(array_map('sanitize_text_field', $list));
    $data = array('filterGenreTopxx' => array_values($list));
    $path = OFIM_CACHE_FILTER_GENRE_TOPXX;
    $dir = dirname($path);
    if (!is_dir($dir)) {
        wp_send_json_error(array('msg' => 'Cache dir not found'));
        return;
    }
    $written = @file_put_contents($path, json_encode($data, JSON_UNESCAPED_UNICODE));
    if ($written === false) {
        wp_send_json_error(array('msg' => 'Could not write file'));
        return;
    }
    wp_send_json_success(array('msg' => 'Đã lưu loại trừ thể loại'));
}

add_action('wp_ajax_add_server_phim', 'add_server_phim');
function add_server_phim()
{
    $pages_array =array();
    $data = get_post_meta($_POST['postid'], 'ophim_episode_list', true);
    if($data){
        foreach ($data as $d){
            $pages_array[] =$d;
        }
        $pages_array[] = array('server_name' => $_POST['namesv']);
    }else{
        $pages_array[] = array('server_name' => $_POST['namesv']);
    }

    update_post_meta($_POST['postid'], 'ophim_episode_list', $pages_array);
    echo $pages_array;
    die();
}

//end ajax

add_action('wp_ajax_search_film' , 'search_film');
add_action('wp_ajax_nopriv_search_film','search_film');
function search_film(){
    global $wpdb;

    $search_string = isset($_POST['keyword']) ? sanitize_text_field($_POST['keyword']) : '';
    $limit = isset($_POST['limit']) ? intval($_POST['limit']) : 5;

    $keywords = array_filter(array_map('trim', explode(' ', $search_string)));
    if (empty($keywords)) {
        echo json_encode([]);
        die();
    }

    $where_parts = [];
    $params = [];
    foreach ($keywords as $kw) {
        $word = '% ' . $wpdb->esc_like(mb_strtolower($kw, 'UTF-8')) . ' %';
        $where_parts[] = "(LOWER(CONCAT(' ', p.post_title, ' ')) COLLATE utf8mb4_bin LIKE %s OR LOWER(CONCAT(' ', COALESCE(pm_ot.meta_value,''), ' ')) COLLATE utf8mb4_bin LIKE %s OR LOWER(CONCAT(' ', COALESCE(t.name,''), ' ')) COLLATE utf8mb4_bin LIKE %s)";
        $params[] = $word;
        $params[] = $word;
        $params[] = $word;
    }
    $where_keywords = implode(' AND ', $where_parts);

    $like_full = '% ' . $wpdb->esc_like(mb_strtolower($search_string, 'UTF-8')) . ' %';
    $params[] = $like_full;
    $params[] = $limit;

    $sql = "
        SELECT DISTINCT p.ID
        FROM {$wpdb->posts} p
        LEFT JOIN {$wpdb->postmeta} pm_ot
            ON p.ID = pm_ot.post_id AND pm_ot.meta_key = 'ophim_original_title'
        LEFT JOIN {$wpdb->term_relationships} tr
            ON p.ID = tr.object_id
        LEFT JOIN {$wpdb->term_taxonomy} tt
            ON tr.term_taxonomy_id = tt.term_taxonomy_id
            AND tt.taxonomy IN ('ophim_tags','ophim_categories','ophim_genres','ophim_directors','ophim_actors','ophim_regions','ophim_years')
        LEFT JOIN {$wpdb->terms} t
            ON tt.term_id = t.term_id
        WHERE p.post_type = 'ophim'
        AND p.post_status = 'publish'
        AND ({$where_keywords})
        ORDER BY
            CASE WHEN LOWER(CONCAT(' ', p.post_title, ' ')) COLLATE utf8mb4_bin LIKE %s THEN 0 ELSE 1 END ASC,
            p.post_date DESC
        LIMIT %d
    ";

    $prepared = $wpdb->prepare($sql, ...$params);
    $post_ids = $wpdb->get_col($prepared);

    $post = [];
    if (!empty($post_ids)) {
        foreach ($post_ids as $pid) {
            $post[] = array(
                'title'          => get_the_title($pid),
                'original_title' => get_post_meta($pid, 'ophim_original_title', true),
                'year'           => get_post_meta($pid, 'ophim_year', true),
                'total_episode'  => get_post_meta($pid, 'ophim_total_episode', true),
                'image'          => get_post_meta($pid, 'ophim_thumb_url', true),
                'image_poster'   => get_post_meta($pid, 'ophim_poster_url', true),
                'slug'           => get_permalink($pid),
            );
        }
    }

    echo json_encode($post);
    die();
}
add_action( 'pre_get_posts', function( $q )
{
    if( $title = $q->get( '_meta_or_title' ) )
    {
        add_filter( 'get_meta_sql', function( $sql ) use ( $title )
        {
            global $wpdb;

            // Only run once:
            static $nr = 0;
            if( 0 != $nr++ ) return $sql;

            // Modified WHERE
            $sql['where'] = sprintf(
                " AND ( %s OR %s ) ",
                $wpdb->prepare( "{$wpdb->posts}.post_title like '%%%s%%'", $title),
                mb_substr( $sql['where'], 5, mb_strlen( $sql['where'] ) )
            );

            return $sql;
        });
    }
});