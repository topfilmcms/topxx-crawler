<?php

class OFim_AdminCrawlTopxx_Controller{
    private $cache;
    public function __construct(){
        $this->cache = new oCache();
    }

    public function display(){
        $schedule_log = $this->getLastLog();
        $genres = $this->getTopxxGenres();
        include_once(OFIM_TEMPLADE_PATCH."/backend/crawl-topxx.php"); // included template file
    }

    /**
     * Lấy danh sách thể loại từ API Topxx để làm bộ lọc
     */
    public function getTopxxGenres() {
        return $this->cache->remember('topxx_genres.txt', 3600, function() {
            $url = 'https://topxx.vip/api/v1/genres';
            $resp = @file_get_contents($url);
            if ($resp === false) {
                return array();
            }
            $json = json_decode($resp);
            if (empty($json->data) || !is_array($json->data)) {
                return array();
            }
            $genres = array();
            foreach ($json->data as $item) {
                $name_vi = $name_en = $item->code;
                if (!empty($item->translations) && is_array($item->translations)) {
                    foreach ($item->translations as $t) {
                        if (!empty($t->locale) && !empty($t->name)) {
                            if ($t->locale === 'vi') $name_vi = $t->name;
                            if ($t->locale === 'en') $name_en = $t->name;
                        }
                    }
                }
                $genres[] = array(
                    'code' => $item->code,
                    'name_vi' => $name_vi,
                    'name_en' => $name_en,
                );
            }
            return $genres;
        });
    }

    public function getLastLog() {
        $log_path = WP_CONTENT_DIR  . '/crawl_ophim_logs_topxx';
        $log_filename = 'log_' . date('d-m-Y') . '.log';
        $log_data = $log_path.'/'.$log_filename;
        if (file_exists($log_data)) {
            $log = file_get_contents($log_data);
        }else{
            $log = "The file $log_filename does not exist";
        }
        return array(
            'log_filename' => $log_filename,
            'log_data' => $log
        );
    }

}