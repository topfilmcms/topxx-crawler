<?php
require_once __DIR__ . '/../../../wp-load.php';
require_once __DIR__ . '/../../../wp-admin/includes/taxonomy.php';
require_once __DIR__ . '/../../../wp-admin/includes/image.php';

set_time_limit(0);
require_once CRAWL_OPHIM_PATH . 'define.php';

if (!isset($argv[1])) return;
if ($argv[1] != get_option(CRAWL_OPHIM_OPTION_SECRET_KEY, 'secret_key')) return;

require_once CRAWL_OPHIM_PATH . 'helpers/functions.php';
require_once CRAWL_OPHIM_PATH . 'crawl_movies_topxx.php';

// Get & Check Settings
$crawl_ophim_settings = json_decode(get_option(CRAWL_OPHIM_OPTION_SETTINGS, false));
if (!$crawl_ophim_settings) return;

// Check enable
if (getEnable() === false) {
    update_option(CRAWL_OPHIM_OPTION_RUNNING, 0);
    return;
}
// Check running
if ((int)get_option(CRAWL_OPHIM_OPTION_RUNNING, 0) === 1) return;

// Update Running
update_option(CRAWL_OPHIM_OPTION_RUNNING, 1);

// Load filter genre from topxx cache file
$filter_genre = array();
if (defined('OFIM_CACHE_FILTER_GENRE_TOPXX') && file_exists(OFIM_CACHE_FILTER_GENRE_TOPXX)) {
    $raw = @file_get_contents(OFIM_CACHE_FILTER_GENRE_TOPXX);
    if ($raw !== false) {
        $dec = json_decode($raw, true);
        if (!empty($dec['filterGenreTopxx']) && is_array($dec['filterGenreTopxx'])) {
            $filter_genre = $dec['filterGenreTopxx'];
        }
    }
}
if (empty($filter_genre) && isset($crawl_ophim_settings->filterGenreTopxx) && is_array($crawl_ophim_settings->filterGenreTopxx)) {
    $filter_genre = $crawl_ophim_settings->filterGenreTopxx;
}

try {
    // Crawl Pages via Topxx API
    $pageFrom = $crawl_ophim_settings->pageFrom;
    $pageTo = $crawl_ophim_settings->pageTo;
    $listMovies = array();
    for ($i = $pageFrom; $i >= $pageTo; $i--) {
        if (getEnable() === false) {
            update_option(CRAWL_OPHIM_OPTION_RUNNING, 0);
            return;
        }
        $result = crawl_topxx_page_handle("https://topxx.vip/api/v1/movies/latest?page=$i");
        if (is_array($result)) {
            continue;
        }
        $result = explode("\n", $result);
        $listMovies = array_merge($listMovies, $result);
    }
    shuffle($listMovies);

    $countMovies = count($listMovies);
    $countDone = 0;
    $countStatus = array(0, 0, 0, 0, 0);

    write_log("Start crawler {$countMovies} movies");
    // Crawl Movies
    foreach ($listMovies as $key => $data_post) {
        if (getEnable() === false) {
            update_option(CRAWL_OPHIM_OPTION_RUNNING, 0);
            write_log("Force Stop => Done {$countDone}/{$countMovies} movies (Nothing Update: {$countStatus[0]} | Insert: {$countStatus[1]} | Update: {$countStatus[2]} | Error: {$countStatus[3]} | Filter: {$countStatus[4]})");
            return;
        }

        $parts = explode('|', $data_post);
        if (count($parts) < 6) {
            $countStatus[SCHEDULE_CRAWLER_TYPE_ERROR]++;
            $countDone++;
            continue;
        }

        $url       = $parts[0];
        $code      = $parts[1];
        $updated_at = $parts[2];
        $title     = $parts[3];
        $slug      = $parts[4];
        $language  = $parts[5];

        $result = crawl_topxx_movies_handle($url, $code, $updated_at, $slug, $language, $title, $filter_genre);
        $result = json_decode($result);
        if ($result->schedule_code == SCHEDULE_CRAWLER_TYPE_ERROR) write_log(sprintf("ERROR: %s ==>>> %s", $url, $result->msg));
        $countStatus[$result->schedule_code]++;
        $countDone++;
    }

} catch (\Throwable $th) {
    write_log(sprintf("ERROR: THROW ==>>> %s", $th->getMessage()));
}

// Update Running
update_option(CRAWL_OPHIM_OPTION_RUNNING, 0);

write_log("Done {$countDone}/{$countMovies} movies (Nothing Update: {$countStatus[0]} | Insert: {$countStatus[1]} | Update: {$countStatus[2]} | Error: {$countStatus[3]} | Filter: {$countStatus[4]})");

function getEnable()
{
    $schedule = json_decode(file_get_contents(CRAWL_OPHIM_PATH_SCHEDULE_JSON));
    if ($schedule->enable) {
        return $schedule->enable;
    }
    return false;
}

function write_log($log_msg, $new_line = "\n")
{
    $log_filename = __DIR__ . '/../../crawl_ophim_logs_topxx';
    if (!file_exists($log_filename)) {
        mkdir($log_filename, 0777, true);
    }
    $log_file_data = $log_filename . '/log_' . date('d-m-Y') . '.log';
    file_put_contents($log_file_data, '[' . date("d-m-Y H:i:s") . '] ' . $log_msg . $new_line, FILE_APPEND);
}
