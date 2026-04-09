<?php

//view count
function op_get_post_view()
{
    $count = get_post_meta(get_the_ID(), 'ophim_view', true);
    $count = $count ? (int)$count : 0;
    
    if ($count >= 1000000000) {
        $formatted = round($count / 1000000000, 1);
        return ($formatted == (int)$formatted ? (int)$formatted : $formatted) . 'B';
    } elseif ($count >= 1000000) {
        $formatted = round($count / 1000000, 1);
        return ($formatted == (int)$formatted ? (int)$formatted : $formatted) . 'M';
    } elseif ($count >= 1000) {
        $formatted = round($count / 1000, 1);
        return ($formatted == (int)$formatted ? (int)$formatted : $formatted) . 'K';
    }
    
    return $count;
}

function op_get_rating()
{
    $count = get_post_meta(get_the_ID(), 'ophim_rating', true);
    return $count ? round($count, 1) : 0;
}

function op_get_rating_count()
{
    $count = get_post_meta(get_the_ID(), 'ophim_votes', true);
    $count = $count ? (int)$count : 0;
    
    if ($count >= 1000000000) {
        $formatted = round($count / 1000000000, 1);
        return ($formatted == (int)$formatted ? (int)$formatted : $formatted) . 'B';
    } elseif ($count >= 1000000) {
        $formatted = round($count / 1000000, 1);
        return ($formatted == (int)$formatted ? (int)$formatted : $formatted) . 'M';
    } elseif ($count >= 1000) {
        $formatted = round($count / 1000, 1);
        return ($formatted == (int)$formatted ? (int)$formatted : $formatted) . 'K';
    }
    
    return $count;
}

/**
 * % like hiển thị ở listing:
 * - Có vote: percent = like/(like+dislike) * 100 = ophim_rating * 100
 * - Chưa có vote: mặc định 100%
 */
function op_get_rating_percent()
{
    $votes = (int) get_post_meta(get_the_ID(), 'ophim_votes', true);
    if ($votes <= 0) {
        return 100;
    }

    $rating = get_post_meta(get_the_ID(), 'ophim_rating', true);
    $rating = $rating !== '' && $rating !== null ? (float) $rating : 0.0;
    $rating = max(0, min(1, $rating));

    return (int) round($rating * 100);
}

function op_format_vote_count($count)
{
    $count = (int) $count;
    if ($count >= 1000000000) {
        $formatted = round($count / 1000000000, 1);
        return ($formatted == (int)$formatted ? (int)$formatted : $formatted) . 'B';
    } elseif ($count >= 1000000) {
        $formatted = round($count / 1000000, 1);
        return ($formatted == (int)$formatted ? (int)$formatted : $formatted) . 'M';
    } elseif ($count >= 1000) {
        $formatted = round($count / 1000, 1);
        return ($formatted == (int)$formatted ? (int)$formatted : $formatted) . 'K';
    }
    return $count;
}

/**
 * Số like ước tính theo: like ~= ophim_rating(0..1) * ophim_votes.
 */
function op_get_like_count()
{
    $votes = (int) get_post_meta(get_the_ID(), 'ophim_votes', true);
    $rating = get_post_meta(get_the_ID(), 'ophim_rating', true);
    $rating = $rating !== '' && $rating !== null ? (float) $rating : 0.0;

    $like_count = (int) round($votes * $rating);
    $like_count = max(0, min($votes, $like_count));

    return op_format_vote_count($like_count);
}

/**
 * Số dislike ước tính theo: dislike = ophim_votes - like_count.
 */
function op_get_dislike_count()
{
    $votes = (int) get_post_meta(get_the_ID(), 'ophim_votes', true);
    $rating = get_post_meta(get_the_ID(), 'ophim_rating', true);
    $rating = $rating !== '' && $rating !== null ? (float) $rating : 0.0;

    $like_count = (int) round($votes * $rating);
    $like_count = max(0, min($votes, $like_count));
    $dislike_count = $votes - $like_count;

    return op_format_vote_count($dislike_count);
}

function op_get_meta($name)
{
    $data = get_post_meta(get_the_ID(), 'ophim_' . $name, true);
    return $data;
}

function op_the_poster()
{
    echo '<img src="' . get_post_meta(get_the_ID(), 'ophim_poster_url', true) . '" style="width:100%" >';
}

function op_the_thumbnail()
{
    echo '<img src="' . get_post_meta(get_the_ID(), 'ophim_thumb_url', true) . '" style="width:100%" >';
}

function op_the_logo($style = '')
{
    if (has_custom_logo()): ?>
        <?php
        $custom_logo_id = get_theme_mod('custom_logo');
        $custom_logo_data = wp_get_attachment_image_src($custom_logo_id, 'full');
        $custom_logo_url = $custom_logo_data[0];
        ?>
        <img style="<?= $style ?>" src="<?php echo esc_url($custom_logo_url); ?>"
             alt="<?php echo esc_attr(get_bloginfo('name')); ?>"/>
    <?php else: ?>
        <h2><?php bloginfo('name'); ?></h2>
    <?php endif;
}


function op_remove_domain($url)
{
    return str_replace(explode("/wp-content/", $url)[0], '', $url);
}

function op_set_post_view()
{
    $key = 'ophim_view';
    $post_id = get_the_ID();
    $count = (int)get_post_meta($post_id, $key, true);
    $count++;
    update_post_meta($post_id, $key, $count);
}

/*
Copy the code below and paste it into single.php file in the while loop.
        <?php op_set_post_view(); ?>
          <?= op_get_post_view(); ?>
 */
//end view cont


//include admin css
add_action('wp_head', 'config_css');
function config_css()
{
    echo get_option('ophim_include_js_tag_head');
    echo "\n<style type='text/css'>\n";
    echo '#player-wrapper{
            height: 27vh!important;
        }';
    echo get_option('ophim_include_css');
    echo "</style>\n";

}

add_action('wp_footer', 'config_js');
function config_js()
{
    echo get_option('ophim_include_js_tag_footer');
    echo "\n<script>\n";
    echo get_option('ophim_include_js', '');
    echo "</script>\n";
}

function op_jwpayer_js()
{
  echo '
    <script src="'.OFIM_PUBLIC_URL.'/js/jwplayer-8.9.3.js"></script>
    <script src="'.OFIM_PUBLIC_URL.'/js/hls.min.js"></script>
    <script src="'.OFIM_PUBLIC_URL.'/js/jwplayer.hlsjs.min.js"></script>';

}
function op_wordpress_logo() {
?>
<style type="text/css">
    body.login div#login h1 a {
        background-image: url(<?php echo esc_url(OFIM_IMAGE_URL . '/logo-xphim.png'); ?>);
    }
</style>
<?php }
add_action( 'login_enqueue_scripts', 'op_wordpress_logo' );
function op_get_menu_array($current_menu)
{
    $menu_name = $current_menu;
    $locations = get_nav_menu_locations();
    $menu = wp_get_nav_menu_object(oIsset($locations,$menu_name));
    if(isset($menu->term_id)):
    $array_menu = wp_get_nav_menu_items($menu->term_id);
    $menu = array();
    foreach ($array_menu as $m) {
        if (empty($m->menu_item_parent)) {
            $menu[$m->ID] = array();
            $menu[$m->ID]['ID'] = $m->ID;
            $menu[$m->ID]['title'] = $m->title;
            $menu[$m->ID]['url'] = $m->url;
            $menu[$m->ID]['children'] = array();
        }
    }
    $submenu = array();
    foreach ($array_menu as $m) {
        if ($m->menu_item_parent) {
            $submenu[$m->ID] = array();
            $submenu[$m->ID]['ID'] = $m->ID;
            $submenu[$m->ID]['title'] = $m->title;
            $submenu[$m->ID]['url'] = $m->url;
            $menu[$m->menu_item_parent]['children'][$m->ID] = $submenu[$m->ID];
        }
    }
    else:
        $menu = array();
    endif;
    return $menu;
}
