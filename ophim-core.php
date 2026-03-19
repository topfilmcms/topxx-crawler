<?php
/**
 * Plugin Name: XPhim-xAPI
 * Description: Topxx.vip là kho dữ liệu phim tập trung, cung cấp đầy đủ meta (tiêu đề, mô tả, poster, diễn viên, thể loại, quốc gia, nguồn phát) cho các nền tảng xem phim thông qua API đơn giản và linh hoạt. Plugin XPhim-xAPI kết nối WordPress với Topxx.vip.
 * Version: 1.0.1
 * Author: XPhim
 * Author URI: https://topxx.vip
 */
if ( ! defined( 'WPINC' ) ) {
    die;
}
require_once 'define.php';
require_once OFIM_HELPERS_PATCH.'/cache.php';
require_once OFIM_HELPERS_PATCH.'/functions.php';
require_once OFIM_INCLUDE_PATCH.'/Controller.php';
require_once OFIM_INCLUDE_PATCH.'/Permalink.php';
require_once OFIM_INCLUDE_PATCH.'/Tax.php';
require_once OFIM_INCLUDE_PATCH.'/Shortcuts.php';
require_once OFIM_INCLUDE_PATCH.'/Ajax.php';
require_once 'crawl_movies.php';
require_once 'crawl_movies_topxx.php';

global $oController;
$oController = new oController();

if (is_admin()){
    require_once 'backend.php';
    new oFim_Backend();
}else{
    require_once OFIM_INCLUDE_PATCH.'/Episode.php';
}
