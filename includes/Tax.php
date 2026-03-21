<?php

function fb_opengraph() {
    global $post;
    if(is_single()) {
        if(has_post_thumbnail($post->ID)) {
            $img_src = wp_get_attachment_image_src(get_post_thumbnail_id( $post->ID ), 'medium');
        } else {
            $img_src = op_get_poster_url();
        }
        ?>
        <meta property="og:title" content="<?php echo the_title(); ?>"/>
        <meta property="og:description" content="<?php the_excerpt() ?>"/>
        <meta property="og:type" content="article"/>
        <meta property="og:url" content="<?php the_permalink() ?>"/>
        <meta property="og:site_name" content="<?php echo get_bloginfo(); ?>"/>
        <meta property="og:image" content="<?php echo $img_src; ?>"/>

        <?php
    } else {
        return;
    }
}
add_action('wp_head', 'fb_opengraph', 5);
add_filter( 'document_title', 'mod_browser_tab_title');
function mod_browser_tab_title( $title )
{
    if(episodeName()){
        $title = $title.' - Tập '.episodeName();
    }
    return $title;
}

function new_rewrite_rule()
{
    $getslug = get_option('ophim_watch_urls');
    if($getslug){
        $slug = $getslug;
    }else{
        $slug = 'xem-phim';
    }
    add_rewrite_rule($slug.'/([^/]*)/([^/]*)', 'index.php?ophim=$matches[1]', 'top');
}

add_action('init', 'new_rewrite_rule');
//classic widget and editer
add_filter('use_block_editor_for_post', '__return_false');
add_filter( 'use_widgets_block_editor', '__return_false' );
//filter feature
function wisdom_filter_tracked_plugins()
{
    global $typenow;
    if ($typenow == 'ophim') {
        $current_plugin = oIsset($_GET,'featured');?>
        <select name="featured" id="featured">
            <option value="all" <?php selected('all', $current_plugin); ?>>Tất cả</option>
            <option value="1" <?php selected('1', $current_plugin); ?>>Nổi bật</option>
        </select>
    <?php }
}

add_action('restrict_manage_posts', 'wisdom_filter_tracked_plugins');
// end filter
function wisdom_sort_plugins_by_slug($query)
{
    global $pagenow;
    $post_type = oIsset($_GET,'post_type');
    if (is_admin() && $pagenow == 'edit.php' && $post_type == 'ophim' && isset($_GET['featured']) && $_GET['featured'] == '1') {
        $query->query_vars['meta_key'] = 'ophim_featured_post';
        $query->query_vars['meta_value'] = $_GET['featured'];
        $query->query_vars['meta_compare'] = '=';
    }
}

add_filter('parse_query', 'wisdom_sort_plugins_by_slug');

//custom columm phim
function filter_movies($defaults)
{
    $defaults['rating'] = 'Rating';
    $defaults['vote'] = 'Vote';
    $defaults['cviews'] = 'Views';
    $defaults['featur'] = 'Featured';
    $defaults['thumb'] = 'Thumb';
    $defaults['poster'] = 'Poster';
    return $defaults;
}

add_filter('manage_ophim_posts_columns', 'filter_movies');
add_action('manage_ophim_posts_custom_column', function ($column_key, $post_id) {
    if ($column_key == 'rating') {
        echo op_get_rating();
    }
    if ($column_key == 'vote') {
        echo op_get_rating_count();
    }
    if ($column_key == 'cviews') {
        echo op_get_post_view();
    }
    if ($column_key == 'thumb') {
        op_the_thumbnail();
    }
    if ($column_key == 'poster') {
        op_the_poster();
    }
    if ($column_key == 'featur') {
        $hideA = (1 == op_get_meta('featured_post')) ? 'style="display:none"' : '';
        $hideB = (1 != op_get_meta('featured_post')) ? 'style="display:none"' : '';
        echo '<a id="feature-add-' . $post_id . '" class="button add-to-featured button-primary" data-postid="' . $post_id . '" data-nonce="' . wp_create_nonce('dt-featured-' . $post_id) . '"  ' . $hideA . '>' . __('Add') . '</a>';
        echo '<a id="feature-del-' . $post_id . '" class="button del-of-featured" data-postid="' . $post_id . '" data-nonce="' . wp_create_nonce('dt-featured-' . $post_id) . '" ' . $hideB . '>' . __('Remove') . '</a>';
    }
}, 10, 2);
//end cusstom columm fim


// add includejscss
function ophim_include_myuploadscript()
{

    if (!did_action('wp_enqueue_media')) {
        wp_enqueue_media();
    }

    wp_enqueue_style('admin_csss', OFIM_PUBLIC_URL . '/css/admin.style.min.css?ver=2.5.5', false, '');

    $page = isset($_GET['page']) ? $_GET['page'] : '';

    if ($page === 'ofim-manager-crawl-topxx') {
        // Trang Crawl Topxx: chỉ dùng script-topxx.js
        wp_enqueue_script('admin_js_topxx', OFIM_JS_URL . '/script-topxx.js', array('jquery'), null, false);
    } else {
        // Các trang khác: chỉ dùng script.js
        wp_enqueue_script('admin_js', OFIM_JS_URL . '/script.js', array('jquery'), null, false);
    }
}

add_action('admin_enqueue_scripts', 'ophim_include_myuploadscript');


//end upload


//add meta box
add_action('init', 'framework_core', 0);
function ophim_meta_box()
{
    global $typenow;
    if ($typenow == 'ophim') {
        add_meta_box('info_phim', 'Thông tin TOPXX', 'info_phim', 'ophim');
        if (isset($_GET['action']) && $_GET['action'] == 'edit') {
            add_meta_box('link_custom_box_html', 'Tập phim', 'link_custom_box_html', 'ophim');
        }
    }
}

add_action('add_meta_boxes', 'ophim_meta_box');

function info_phim($post)
{
    include_once OFIM_TEMPLADE_PATCH . '/backend/metabox_info.php';
}

function ophim_thongtin_save($post_id)
{
    if (isset($_POST['ophim'])) {
        $post = $_POST['ophim'];
        $post['ophim_is_copyright'] = oIsset($post,'ophim_is_copyright');
        foreach ($post as $key => $p) {
            update_post_meta($post_id, $key, $p);
        }
    }

    if (isset($_POST['ophim_preview_images'])) {
        $raw = $_POST['ophim_preview_images'];
        $clean = array_values(array_filter(array_map('esc_url_raw', (array) $raw)));
        $clean = array_slice($clean, 0, 6);
        update_post_meta($post_id, 'ophim_preview_images', $clean);
    } else {
        update_post_meta($post_id, 'ophim_preview_images', array());
    }

    return $post_id;
}

add_action('save_post', 'ophim_thongtin_save');
add_action('save_post', 'savephim');


function savephim($post_id)
{
    if (isset($_POST['episode'])) {
        $episode = $_POST['episode'];
        update_post_meta($post_id, 'ophim_episode_list', $episode);
    }
}


function link_custom_box_html($post)
{
    $postmneta = get_post_meta($post->ID, 'ophim_episode_list', true);
    $postid = $post->ID;
    include_once OFIM_TEMPLADE_PATCH . '/backend/episode.php';
}

//end custom box


//add menu tax admin

function framework_core()
{
    framework_create_post_type('ophim', 'TOPXX', 'TOPXX', get_option('ophim_slug_movies') ? get_option('ophim_slug_movies') : 'movie', ['title', 'editor'], 'dashicons-format-video');
    framework_create_taxonomies('ophim_directors', 'ophim', 'Đạo diễn', 'Đạo diễn', get_option('ophim_slug_directors') ? get_option('ophim_slug_directors') : 'directors');
    framework_create_taxonomies_cat('ophim_categories', 'ophim', 'Danh mục', 'Danh mục', get_option('ophim_slug_categories') ? get_option('ophim_slug_categories') : 'categories');
    framework_create_taxonomies('ophim_actors', 'ophim', 'Diễn viên', 'Diễn viên', get_option('ophim_slug_actors') ? get_option('ophim_slug_actors') : 'actors');
    framework_create_taxonomies('ophim_genres', 'ophim', 'Thể loại', 'Thể loại', get_option('ophim_slug_genres') ? get_option('ophim_slug_genres') : 'genres');
    framework_create_taxonomies('ophim_regions', 'ophim', 'Quốc gia', 'Quốc gia', get_option('ophim_slug_regions') ? get_option('ophim_slug_regions') : 'regions');
    framework_create_taxonomies('ophim_tags', 'ophim', 'Tags', 'Tags', get_option('ophim_slug_tags') ? get_option('ophim_slug_tags') : 'tags');
    framework_create_taxonomies('ophim_years', 'ophim', 'Năm', 'Năm', get_option('ophim_slug_years') ? get_option('ophim_slug_years') : 'years');
}

//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function framework_create_post_type($post_type, $singular_name, $plural_name, $slug, $support, $menu_icon)
{
    $labels = array('name' => _x($plural_name, 'post type general name', 'Ophim'), 'singular_name' => _x($singular_name, 'post type singular name', 'Ophim'), 'menu_name' => _x($plural_name, 'admin menu', 'Ophim'), 'name_admin_bar' => _x($singular_name, 'add new on admin bar', 'Ophim'), 'add_new' => _x('Thêm phim', $post_type, 'Ophim'), 'add_new_item' => __('Add New ' . $singular_name, 'Ophim'), 'new_item' => __('New ' . $singular_name, 'Ophim'), 'edit_item' => __('Edit ' . $singular_name, 'Ophim'), 'view_item' => __('View ' . $singular_name, 'Ophim'), 'all_items' => __('Danh sách ', 'Ophim'), 'search_items' => __('Search ' . $singular_name . 's', 'Ophim'), 'parent_item_colon' => __('Parent ' . $singular_name . ':', 'Ophim'), 'not_found' => __('No ' . $singular_name . ' found.', 'Ophim'), 'not_found_in_trash' => __('No ' . $singular_name . ' found in Trash.', 'Ophim'),);
    $args = array('labels' => $labels, 'description' => __('Description.', 'Ophim'), 'public' => ($post_type === 'process') ? false : true, 'publicly_queryable' => true, 'show_ui' => true, 'show_in_menu' => true, 'query_var' => true, 'rewrite' => ($post_type === 'process') ? false : array('slug' => $slug), 'capability_type' => 'post', 'has_archive' => true, 'hierarchical' => false, 'menu_position' => null, 'menu_icon' => $menu_icon, 'supports' => $support,);
    register_post_type($post_type, $args);
}

//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function framework_create_taxonomies($taxonomy_name, $post_type, $singular_name, $plural_name, $slug)
{
    $labels = array('name' => _x($plural_name, 'taxonomy general name', 'Ophim'), 'singular_name' => _x($singular_name, 'taxonomy singular name', 'Ophim'), 'search_items' => __('Search ' . $plural_name, 'Ophim'), 'all_items' => __('All ' . $plural_name, 'Ophim'), 'parent_item' => __('Parent ' . $singular_name, 'Ophim'), 'parent_item_colon' => __('Parent ' . $singular_name . ':', 'Ophim'), 'edit_item' => __('Edit ' . $singular_name, 'Ophim'), 'update_item' => __('Update ' . $singular_name, 'Ophim'), 'add_new_item' => __('Add New ' . $singular_name, 'Ophim'), 'new_item_name' => __('New ' . $singular_name . ' Name', 'Ophim'), 'menu_name' => __($singular_name, 'Ophim'),);
    $args = array('hierarchical' => false, 'labels' => $labels, 'show_ui' => true, 'show_admin_column' => true, 'query_var' => true, 'rewrite' => array('slug' => $slug),);
    register_taxonomy($taxonomy_name, array($post_type), $args);
}

//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function framework_create_taxonomies_cat($taxonomy_name, $post_type, $singular_name, $plural_name, $slug)
{
    $labels = array('name' => _x($plural_name, 'taxonomy general name', 'Ophim'), 'singular_name' => _x($singular_name, 'taxonomy singular name', 'Ophim'), 'search_items' => __('Search ' . $plural_name, 'Ophim'), 'all_items' => __('All ' . $plural_name, 'Ophim'), 'parent_item' => __('Parent ' . $singular_name, 'Ophim'), 'parent_item_colon' => __('Parent ' . $singular_name . ':', 'Ophim'), 'edit_item' => __('Edit ' . $singular_name, 'Ophim'), 'update_item' => __('Update ' . $singular_name, 'Ophim'), 'add_new_item' => __('Add New ' . $singular_name, 'Ophim'), 'new_item_name' => __('New ' . $singular_name . ' Name', 'Ophim'), 'menu_name' => __($singular_name, 'Ophim'),);
    $args = array('hierarchical' => true, 'labels' => $labels, 'show_ui' => true, 'show_admin_column' => true, 'query_var' => true, 'rewrite' => array('slug' => $slug),);
    register_taxonomy($taxonomy_name, array($post_type), $args);
}

//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
add_action('ophim_actors_add_form_fields', 'ophim_actors_add_avatar_field');
function ophim_actors_add_avatar_field($taxonomy) {
    ?>
    <div class="form-field">
        <label for="actor_avatar"><?php _e('Avatar', 'Ophim'); ?></label>
        <input type="hidden" name="actor_avatar" id="actor_avatar" value="">
        <div id="actor_avatar_preview" style="margin-bottom: 10px;"></div>
        <button type="button" class="button ophim-upload-avatar-btn"><?php _e('Chọn ảnh', 'Ophim'); ?></button>
        <button type="button" class="button ophim-remove-avatar-btn" style="display:none;"><?php _e('Xóa ảnh', 'Ophim'); ?></button>
        <p class="description"><?php _e('Ảnh đại diện của diễn viên', 'Ophim'); ?></p>
    </div>
    <?php
}

add_action('ophim_actors_edit_form_fields', 'ophim_actors_edit_avatar_field');
function ophim_actors_edit_avatar_field($term) {
    $avatar = get_term_meta($term->term_id, 'actor_avatar', true);
    ?>
    <tr class="form-field">
        <th scope="row"><label for="actor_avatar"><?php _e('Avatar', 'Ophim'); ?></label></th>
        <td>
            <input type="hidden" name="actor_avatar" id="actor_avatar" value="<?php echo esc_attr($avatar); ?>">
            <div id="actor_avatar_preview" style="margin-bottom: 10px;">
                <?php if ($avatar) : ?>
                    <img src="<?php echo esc_url($avatar); ?>" style="max-width: 150px; height: auto; border-radius: 8px;">
                <?php endif; ?>
            </div>
            <button type="button" class="button ophim-upload-avatar-btn"><?php _e('Chọn ảnh', 'Ophim'); ?></button>
            <button type="button" class="button ophim-remove-avatar-btn" <?php echo $avatar ? '' : 'style="display:none;"'; ?>><?php _e('Xóa ảnh', 'Ophim'); ?></button>
            <p class="description"><?php _e('Ảnh đại diện của diễn viên', 'Ophim'); ?></p>
        </td>
    </tr>
    <?php
}

add_action('created_ophim_actors', 'ophim_actors_save_avatar');
function ophim_actors_save_avatar($term_id) {
    if (isset($_POST['actor_avatar'])) {
        update_term_meta($term_id, 'actor_avatar', sanitize_text_field($_POST['actor_avatar']));
    }
}

add_action('edited_ophim_actors', 'ophim_actors_update_avatar');
function ophim_actors_update_avatar($term_id) {
    if (isset($_POST['actor_avatar'])) {
        update_term_meta($term_id, 'actor_avatar', sanitize_text_field($_POST['actor_avatar']));
    }
}

add_filter('manage_edit-ophim_actors_columns', 'ophim_actors_add_avatar_column');
function ophim_actors_add_avatar_column($columns) {
    $new_columns = array();
    foreach ($columns as $key => $value) {
        if ($key == 'name') {
            $new_columns['actor_avatar'] = __('Avatar', 'Ophim');
        }
        $new_columns[$key] = $value;
    }
    return $new_columns;
}

add_filter('manage_ophim_actors_custom_column', 'ophim_actors_avatar_column_content', 10, 3);
function ophim_actors_avatar_column_content($content, $column_name, $term_id) {
    if ($column_name === 'actor_avatar') {
        $avatar = get_term_meta($term_id, 'actor_avatar', true);
        if ($avatar) {
            $content = '<img src="' . esc_url($avatar) . '" style="width: 40px; height: 40px; object-fit: cover; border-radius: 50%;">';
        } else {
            $content = '<span style="color: #999;">—</span>';
        }
    }
    return $content;
}

// Thể loại (ophim_genres): thêm trường thumbnail
add_action('ophim_genres_add_form_fields', 'ophim_genres_add_thumbnail_field');
function ophim_genres_add_thumbnail_field($taxonomy) {
    ?>
    <div class="form-field">
        <label for="genre_thumbnail"><?php _e('Thumbnail', 'Ophim'); ?></label>
        <input type="hidden" name="genre_thumbnail" id="genre_thumbnail" value="">
        <div id="genre_thumbnail_preview" style="margin-bottom: 10px;"></div>
        <button type="button" class="button ophim-upload-genre-thumb-btn"><?php _e('Chọn ảnh', 'Ophim'); ?></button>
        <button type="button" class="button ophim-remove-genre-thumb-btn" style="display:none;"><?php _e('Xóa ảnh', 'Ophim'); ?></button>
        <p class="description"><?php _e('Ảnh đại diện thể loại (từ crawl hoặc tùy chỉnh)', 'Ophim'); ?></p>
    </div>
    <?php
}

add_action('ophim_genres_edit_form_fields', 'ophim_genres_edit_thumbnail_field');
function ophim_genres_edit_thumbnail_field($term) {
    $thumb = get_term_meta($term->term_id, 'genre_thumbnail', true);
    ?>
    <tr class="form-field">
        <th scope="row"><label for="genre_thumbnail"><?php _e('Thumbnail', 'Ophim'); ?></label></th>
        <td>
            <input type="hidden" name="genre_thumbnail" id="genre_thumbnail" value="<?php echo esc_attr($thumb); ?>">
            <div id="genre_thumbnail_preview" style="margin-bottom: 10px;">
                <?php if ($thumb) : ?>
                    <img src="<?php echo esc_url($thumb); ?>" style="max-width: 150px; height: auto; border-radius: 8px;">
                <?php endif; ?>
            </div>
            <button type="button" class="button ophim-upload-genre-thumb-btn"><?php _e('Chọn ảnh', 'Ophim'); ?></button>
            <button type="button" class="button ophim-remove-genre-thumb-btn" <?php echo $thumb ? '' : 'style="display:none;"'; ?>><?php _e('Xóa ảnh', 'Ophim'); ?></button>
            <p class="description"><?php _e('Ảnh đại diện thể loại', 'Ophim'); ?></p>
        </td>
    </tr>
    <?php
}

add_action('created_ophim_genres', 'ophim_genres_save_thumbnail');
function ophim_genres_save_thumbnail($term_id) {
    if (isset($_POST['genre_thumbnail'])) {
        update_term_meta($term_id, 'genre_thumbnail', esc_url_raw($_POST['genre_thumbnail']));
    }
}

add_action('edited_ophim_genres', 'ophim_genres_update_thumbnail');
function ophim_genres_update_thumbnail($term_id) {
    if (isset($_POST['genre_thumbnail'])) {
        update_term_meta($term_id, 'genre_thumbnail', esc_url_raw($_POST['genre_thumbnail']));
    }
}

add_filter('manage_edit-ophim_genres_columns', 'ophim_genres_add_thumbnail_column');
function ophim_genres_add_thumbnail_column($columns) {
    $new_columns = array();
    foreach ($columns as $key => $value) {
        if ($key == 'name') {
            $new_columns['genre_thumbnail'] = __('Thumbnail', 'Ophim');
        }
        $new_columns[$key] = $value;
    }
    return $new_columns;
}

add_filter('manage_ophim_genres_custom_column', 'ophim_genres_thumbnail_column_content', 10, 3);
function ophim_genres_thumbnail_column_content($content, $column_name, $term_id) {
    if ($column_name === 'genre_thumbnail') {
        $thumb = get_term_meta($term_id, 'genre_thumbnail', true);
        if ($thumb) {
            $content = '<img src="' . esc_url($thumb) . '" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">';
        } else {
            $content = '<span style="color: #999;">—</span>';
        }
    }
    return $content;
}

add_action('admin_footer', 'ophim_actors_avatar_script');
function ophim_actors_avatar_script() {
    $screen = get_current_screen();
    if ($screen->taxonomy !== 'ophim_actors') return;
    ?>
    <script>
    jQuery(document).ready(function($) {
        var mediaUploader;
        
        $(document).on('click', '.ophim-upload-avatar-btn', function(e) {
            e.preventDefault();
            
            if (mediaUploader) {
                mediaUploader.open();
                return;
            }
            
            mediaUploader = wp.media({
                title: 'Chọn Avatar',
                button: { text: 'Sử dụng ảnh này' },
                multiple: false
            });
            
            mediaUploader.on('select', function() {
                var attachment = mediaUploader.state().get('selection').first().toJSON();
                $('#actor_avatar').val(attachment.url);
                $('#actor_avatar_preview').html('<img src="' + attachment.url + '" style="max-width: 150px; height: auto; border-radius: 8px;">');
                $('.ophim-remove-avatar-btn').show();
            });
            
            mediaUploader.open();
        });
        
        $(document).on('click', '.ophim-remove-avatar-btn', function(e) {
            e.preventDefault();
            $('#actor_avatar').val('');
            $('#actor_avatar_preview').html('');
            $(this).hide();
        });
        
        $(document).ajaxComplete(function(event, xhr, settings) {
            if (settings.data && settings.data.indexOf('action=add-tag') !== -1) {
                $('#actor_avatar').val('');
                $('#actor_avatar_preview').html('');
                $('.ophim-remove-avatar-btn').hide();
            }
        });
    });
    </script>
    <?php
}

add_action('admin_footer', 'ophim_genres_thumbnail_script');
function ophim_genres_thumbnail_script() {
    $screen = get_current_screen();
    if (!$screen || $screen->taxonomy !== 'ophim_genres') return;
    ?>
    <script>
    jQuery(document).ready(function($) {
        var mediaUploader;
        $(document).on('click', '.ophim-upload-genre-thumb-btn', function(e) {
            e.preventDefault();
            if (mediaUploader) { mediaUploader.open(); return; }
            mediaUploader = wp.media({
                title: 'Chọn Thumbnail Thể loại',
                button: { text: 'Sử dụng ảnh này' },
                multiple: false
            });
            mediaUploader.on('select', function() {
                var attachment = mediaUploader.state().get('selection').first().toJSON();
                $('#genre_thumbnail').val(attachment.url);
                $('#genre_thumbnail_preview').html('<img src="' + attachment.url + '" style="max-width: 150px; height: auto; border-radius: 8px;">');
                $('.ophim-remove-genre-thumb-btn').show();
            });
            mediaUploader.open();
        });
        $(document).on('click', '.ophim-remove-genre-thumb-btn', function(e) {
            e.preventDefault();
            $('#genre_thumbnail').val('');
            $('#genre_thumbnail_preview').html('');
            $(this).hide();
        });
        $(document).ajaxComplete(function(event, xhr, settings) {
            if (settings.data && settings.data.indexOf('action=add-tag') !== -1) {
                $('#genre_thumbnail').val('');
                $('#genre_thumbnail_preview').html('');
                $('.ophim-remove-genre-thumb-btn').hide();
            }
        });
    });
    </script>
    <?php
}