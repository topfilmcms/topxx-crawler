<?php

function crawl_topxx_page_handle($url, $language = 'vi')
{

    $sourcePage = file_get_contents($url);
    $sourcePage = json_decode($sourcePage);
    $listMovies = [];
    if (isset($sourcePage->data)) {
        $sourcePage = $sourcePage->data;
    }

    if (count($sourcePage) > 0) {
        foreach ($sourcePage as $key => $item) {
            $title = $item->name;
            $slug = $item->slug;

            if (isset($item->trans) && is_array($item->trans) && count($item->trans) > 0) {
                $primaryTitle = null;
                $secondaryTitle = null;
                $primarySlug = null;
                $secondarySlug = null;

                foreach ($item->trans as $trans) {
                    if (!isset($trans->locale) || !isset($trans->title)) {
                        continue;
                    }

                    if ($trans->locale === $language && $primaryTitle === null) {
                        $primaryTitle = $trans->title;
                        $primarySlug = $trans->slug;
                    }

                    if ($language === 'vi' && $trans->locale === 'en' && $secondaryTitle === null) {
                        $secondaryTitle = $trans->title;
                        $secondarySlug = $trans->slug;
                    }

                    if ($language === 'en' && $trans->locale === 'vi' && $secondaryTitle === null) {
                        $secondaryTitle = $trans->title;
                        $secondarySlug = $trans->slug;
                    }
                }

                if ($primaryTitle !== null) {
                    $title = $primaryTitle;
                    $slug = $primarySlug;
                } elseif ($secondaryTitle !== null) {
                    $title = $secondaryTitle;
                    $slug = $secondarySlug;
                }
            }

            array_push($listMovies, API_DOMAIN . "/movies/{$item->code}|{$item->code}|{$item->updated_at}|{$title}|{$slug}|{$language}");
        }
        return join("\n", $listMovies);
    }
    return $listMovies;
}

add_action('wp_ajax_crawl_topxx_movies', 'crawl_topxx_movies');
function crawl_topxx_movies()
{
    $data_post = $_POST['url'];
    $url = explode('|', $data_post)[0];
    $code = explode('|', $data_post)[1];
    $updated_at = explode('|', $data_post)[2];
    $title = explode('|', $data_post)[3];
    $slug = explode('|', $data_post)[4];
    $language = explode('|', $data_post)[5];
    $filter_genre = isset($_POST['filterGenre']) && is_array($_POST['filterGenre']) ? array_map('sanitize_text_field', $_POST['filterGenre']) : array();
    $result = crawl_topxx_movies_handle($url, $code, $updated_at, $slug, $language, $title, $filter_genre);
    echo $result;
    die();
}

function crawl_topxx_movies_handle($url, $code, $updated_at, $slug, $language, $title, $filter_genre = array())
{

    try {
        $sourcePage = @file_get_contents($url);
        if ($sourcePage === false) {
            return json_encode(array(
                'status' => false,
                'msg' => 'Lỗi: không tải được dữ liệu từ API (URL hoặc mạng)',
                'wait' => true,
                'schedule_code' => SCHEDULE_CRAWLER_TYPE_ERROR
            ));
        }
        $sourcePage = json_decode($sourcePage, true);
        if (empty($sourcePage) || empty($sourcePage['data'])) {
            return json_encode(array(
                'status' => false,
                'msg' => 'Lỗi: phản hồi API không hợp lệ hoặc thiếu dữ liệu phim',
                'wait' => true,
                'schedule_code' => SCHEDULE_CRAWLER_TYPE_ERROR
            ));
        }

        if (!empty($filter_genre) && !empty($sourcePage['data']['genres'])) {
            $movie_genre_codes = array();
            foreach ($sourcePage['data']['genres'] as $g) {
                if (!empty($g['code'])) {
                    $movie_genre_codes[] = $g['code'];
                }
            }
            foreach ($filter_genre as $exclude_code) {
                if (in_array($exclude_code, $movie_genre_codes)) {
                    return json_encode(array(
                        'status' => false,
                        'msg' => 'Bỏ qua: phim thuộc thể loại nằm trong danh sách loại trừ',
                        'wait' => false,
                        'schedule_code' => SCHEDULE_CRAWLER_TYPE_FILTER
                    ));
                }
            }
        }

        $args = array(
            'post_type' => 'ophim',
            'meta_query' => array(
                array(
                    'key' => 'ophim_fetch_topxx_code',
                    'value' => $code,
                    'compare' => '=',
                )
            )
        );
        $wp_query = new WP_Query($args);

        $total = $wp_query->found_posts;

        if ($total > 0) { # Trường hợp đã có

            $args = array(
                'post_type' => 'ophim',
                'meta_query' => array(
                    array(
                        'key' => 'ophim_fetch_topxx_code',
                        'value' => $code,
                        'compare' => '=',
                    )
                )
            );
            $wp_query = new WP_Query($args);
            if ($wp_query->have_posts()) : while ($wp_query->have_posts()) : $wp_query->the_post();
                global $post;
                $get_fetch_time = get_post_meta($post->ID, 'ophim_fetch_topxx_updated_at', true);
                if ($get_fetch_time == $updated_at) { // Không có gì cần cập nhật
                    $result = array(
                        'status' => false,
                        'post_id' => $post->ID,
                        'list_episode' => [],
                        'msg' => 'Phim đã tồn tại trên site — dữ liệu API không đổi, không cập nhật',
                        'wait' => false,
                        'schedule_code' => SCHEDULE_CRAWLER_TYPE_NOTHING
                    );
                    return json_encode($result);
                }

                $data = create_data_topxx($sourcePage, $url, $code, $updated_at, $slug, $language, $title);

                $status = getStatus_topxx($data['status']);

                // Re-Update Movies Info
                $formality = 'single_movies';
                //
                $post_id = $post->ID;

                update_post_meta($post_id, 'ophim_movie_formality', $formality);
                update_post_meta($post_id, 'ophim_movie_status', $status);
                update_post_meta($post_id, 'ophim_fetch_info_url', $data['fetch_url']);
                update_post_meta($post_id, 'ophim_fetch_topxx_code', $data['fetch_topxx_code']);
                update_post_meta($post_id, 'ophim_fetch_topxx_updated_at', $data['fetch_topxx_updated_at']);
                update_post_meta($post_id, 'ophim_original_title', $data['title']);
                update_post_meta($post_id, 'ophim_runtime', $data['duration']);
                update_post_meta($post_id, 'ophim_total_episode', $data['total_episode']);
                update_post_meta($post_id, 'ophim_quality', $data['quality']);

                if (!empty($data['seo_title'])) {
                    update_post_meta($post_id, 'rank_math_title', sanitize_text_field($data['seo_title']));
                    update_post_meta($post_id, '_yoast_wpseo_title', sanitize_text_field($data['seo_title']));
                }
                if (!empty($data['seo_description'])) {
                    update_post_meta($post_id, 'rank_math_description', sanitize_textarea_field($data['seo_description']));
                    update_post_meta($post_id, '_yoast_wpseo_metadesc', sanitize_textarea_field($data['seo_description']));
                }
                if (!empty($data['seo_keywords'])) {
                    $kw_string = is_string($data['seo_keywords']) ? $data['seo_keywords'] : '';
                    update_post_meta($post_id, 'rank_math_focus_keyword', sanitize_text_field($kw_string));
                    $first_kw = trim(explode(',', $kw_string)[0]);
                    if ($first_kw !== '') {
                        update_post_meta($post_id, '_yoast_wpseo_focuskw', sanitize_text_field($first_kw));
                    }
                }
                if (!empty($data['preview_images']) && is_array($data['preview_images'])) {
                    update_post_meta($post_id, 'ophim_preview_images', $data['preview_images']);
                }

                $updatepost = array(
                    'post_modified'  => date(),
                    'post_modified_gmt'   => date(),
                    'ID'          => $post_id, 
                );
                wp_update_post( $updatepost );

                // Check & Update Image
                $crawl_settings = json_decode(get_option(CRAWL_TOPXX_OPTION_SETTINGS, false));
                $ophim_thumb_url = get_post_meta($post_id, 'ophim_thumb_url', true);
                $ophim_poster_url = get_post_meta($post_id, 'ophim_poster_url', true);
                if(!file_exists(ABSPATH . $ophim_thumb_url)) {
                    $thumb_image_url = download_resize_thumb_topxx($data, $post_id, $crawl_settings);
                    if($thumb_image_url != $ophim_thumb_url)  update_post_meta($post_id, 'ophim_thumb_url', $thumb_image_url);
                }
                if(!file_exists(ABSPATH . $ophim_poster_url)) {
                    $poster_image_url = download_resize_poster_topxx($data, $post_id, $crawl_settings);
                    if($poster_image_url != $ophim_poster_url) update_post_meta($post_id, 'ophim_poster_url', $poster_image_url);
                }

                // Re-Update Episodes
                $list_episode = get_list_episode_topxx($sourcePage, $post->ID);
                $result = array(
                    'status' => false,
                    'post_id' => $post->ID,
                    'data' => $data,
                    'list_episode' => $list_episode,
                    'msg' => 'Phim đã tồn tại trên site — đã cập nhật metadata / tập phim',
                    'wait' => true,
                    'schedule_code' => SCHEDULE_CRAWLER_TYPE_UPDATE
                );
                //wp_update_post($post);
                return json_encode($result);
            endwhile;
            endif;
        }

        $data = create_data_topxx($sourcePage, $url, $code, $updated_at, $slug, $language, $title);
        $post_id = add_posts_topxx($data);
        $list_episode = get_list_episode_topxx($sourcePage, $post_id);
        $result = array(
            'status' => true,
            'post_id' => $post_id,
            'data' => $url,
            'list_episode' => 'Add new',
            'msg' => 'Đã thêm phim mới',
            'wait' => true,
            'schedule_code' => SCHEDULE_CRAWLER_TYPE_INSERT
        );
        return json_encode($result);
    } catch (Exception $e) {
        $result = array(
            'status' => false,
            'post_id' => null,
            'data' => null,
            'list_episode' => null,
            'msg' => $e->getMessage(),
            'wait' => false,
            'schedule_code' => SCHEDULE_CRAWLER_TYPE_ERROR
        );
        return json_encode($result);
    }
}

function create_data_topxx($sourcePage, $url, $code, $updated_at, $slug, $language, $title)
{
    $arrCat = [];
    $arrGenres = [];
    foreach ($sourcePage["data"]["genres"] as $key => $value) {
        $genreName = isset($value["name"]) ? $value["name"] : '';

        if (isset($value["trans"]) && is_array($value["trans"]) && count($value["trans"]) > 0) {
            $primaryName = null;
            $secondaryName = null;

            foreach ($value["trans"] as $trans) {
                if (!isset($trans["locale"]) || !isset($trans["name"])) {
                    continue;
                }

                if ($trans["locale"] === $language && $primaryName === null) {
                    $primaryName = $trans["name"];
                }

                if ($language === 'vi' && $trans["locale"] === 'en' && $secondaryName === null) {
                    $secondaryName = $trans["name"];
                }

                if ($language === 'en' && $trans["locale"] === 'vi' && $secondaryName === null) {
                    $secondaryName = $trans["name"];
                }
            }

            if ($primaryName !== null) {
                $genreName = $primaryName;
            } elseif ($secondaryName !== null) {
                $genreName = $secondaryName;
            }
        }

        // Lưu cả tên và thumbnail (term meta) cho mỗi genre
        $thumbnail = isset($value['thumbnail']) ? $value['thumbnail'] : '';
        $arrGenres[] = array('name' => $genreName, 'thumbnail' => $thumbnail);
    }
    array_push($arrCat, "Phim Lẻ");

    $arrCountry = [];
    foreach ($sourcePage["data"]["countries"] as $key => $value) {
      $countryName = isset($value["name"]) ? $value["name"] : '';

      if (isset($value["trans"]) && is_array($value["trans"]) && count($value["trans"]) > 0) {
          $primaryName = null;
          $secondaryName = null;

          foreach ($value["trans"] as $trans) {
              if (!isset($trans["locale"]) || !isset($trans["name"])) {
                  continue;
              }

              if ($trans["locale"] === $language && $primaryName === null) {
                  $primaryName = $trans["name"];
              }

              if ($language === 'vi' && $trans["locale"] === 'en' && $secondaryName === null) {
                  $secondaryName = $trans["name"];
              }

              if ($language === 'en' && $trans["locale"] === 'vi' && $secondaryName === null) {
                  $secondaryName = $trans["name"];
              }
          }

          if ($primaryName !== null) {
              $countryName = $primaryName;
          } elseif ($secondaryName !== null) {
              $countryName = $secondaryName;
          }
      }

      array_push($arrCountry, $countryName);
    }

    $arrTags = [];
    // Tags lấy từ API keywords (data.keywords[].trans[].name), không dùng tên phim
    if (!empty($sourcePage["data"]["keywords"]) && is_array($sourcePage["data"]["keywords"])) {
        foreach ($sourcePage["data"]["keywords"] as $kw) {
            $tagName = isset($kw["name"]) ? trim($kw["name"]) : '';

            if (isset($kw["trans"]) && is_array($kw["trans"]) && count($kw["trans"]) > 0) {
                $primaryName = null;
                $secondaryName = null;

                foreach ($kw["trans"] as $trans) {
                    if (!isset($trans["locale"]) || !isset($trans["name"])) {
                        continue;
                    }

                    if ($trans["locale"] === $language && $primaryName === null) {
                        $primaryName = trim($trans["name"]);
                    }

                    if ($language === 'vi' && $trans["locale"] === 'en' && $secondaryName === null) {
                        $secondaryName = trim($trans["name"]);
                    }

                    if ($language === 'en' && $trans["locale"] === 'vi' && $secondaryName === null) {
                        $secondaryName = trim($trans["name"]);
                    }
                }

                if ($primaryName !== null && $primaryName !== '') {
                    $tagName = $primaryName;
                } elseif ($secondaryName !== null && $secondaryName !== '') {
                    $tagName = $secondaryName;
                }
            }

            if ($tagName !== '') {
                $arrTags[] = $tagName;
            }
        }
    }

    $actor = [];
    foreach ($sourcePage["data"]["actors"] as $key => $value) {
        $actorName = isset($value["name"]) ? $value["name"] : '';

        if (isset($value["trans"]) && is_array($value["trans"]) && count($value["trans"]) > 0) {
          $primaryName = null;
          $secondaryName = null;

          foreach ($value["trans"] as $trans) {
              if (!isset($trans["locale"]) || !isset($trans["name"])) {
                  continue;
              }

              if ($trans["locale"] === $language && $primaryName === null) {
                  $primaryName = $trans["name"];
              }

              if ($language === 'vi' && $trans["locale"] === 'en' && $secondaryName === null) {
                  $secondaryName = $trans["name"];
              }

              if ($language === 'en' && $trans["locale"] === 'vi' && $secondaryName === null) {
                  $secondaryName = $trans["name"];
              }
          }

          if ($primaryName !== null) {
              $actorName = $primaryName;
          } elseif ($secondaryName !== null) {
              $actorName = $secondaryName;
          }
      }

        // Lưu cả tên và avatar cho mỗi diễn viên
        $avatar = isset($value['avatar']) ? $value['avatar'] : '';
        $actor[] = array('name' => $actorName, 'avatar' => $avatar);
    }

    $content = "";
    $seo_title = "";
    $seo_description = "";
    $seo_keywords = "";

    if (isset($sourcePage["data"]["trans"]) && is_array($sourcePage["data"]["trans"])) {
        $primaryContent = null;
        $secondaryContent = null;
        $primarySeoTitle = null;
        $secondarySeoTitle = null;
        $primarySeoDesc = null;
        $secondarySeoDesc = null;
        $primarySeoKw = null;
        $secondarySeoKw = null;

        foreach ($sourcePage["data"]["trans"] as $key => $value) {
            if (!isset($value["locale"])) {
                continue;
            }

            if ($value["locale"] === $language) {
                if ($primaryContent === null && isset($value["content"])) {
                    $primaryContent = $value["content"];
                }
                if ($primarySeoTitle === null && !empty($value["seo_title"])) {
                    $primarySeoTitle = $value["seo_title"];
                }
                if ($primarySeoDesc === null && isset($value["seo_description"])) {
                    $primarySeoDesc = $value["seo_description"];
                }
                if ($primarySeoKw === null && isset($value["seo_keywords"])) {
                    $primarySeoKw = $value["seo_keywords"];
                }
            }

            if ($language === 'vi' && $value["locale"] === 'en') {
                if ($secondaryContent === null && isset($value["content"])) {
                    $secondaryContent = $value["content"];
                }
                if ($secondarySeoTitle === null && !empty($value["seo_title"])) {
                    $secondarySeoTitle = $value["seo_title"];
                }
                if ($secondarySeoDesc === null && isset($value["seo_description"])) {
                    $secondarySeoDesc = $value["seo_description"];
                }
                if ($secondarySeoKw === null && isset($value["seo_keywords"])) {
                    $secondarySeoKw = $value["seo_keywords"];
                }
            }

            if ($language === 'en' && $value["locale"] === 'vi') {
                if ($secondaryContent === null && isset($value["content"])) {
                    $secondaryContent = $value["content"];
                }
                if ($secondarySeoTitle === null && !empty($value["seo_title"])) {
                    $secondarySeoTitle = $value["seo_title"];
                }
                if ($secondarySeoDesc === null && isset($value["seo_description"])) {
                    $secondarySeoDesc = $value["seo_description"];
                }
                if ($secondarySeoKw === null && isset($value["seo_keywords"])) {
                    $secondarySeoKw = $value["seo_keywords"];
                }
            }
        }

        if ($primaryContent !== null) {
            $content = $primaryContent;
        } elseif ($secondaryContent !== null) {
            $content = $secondaryContent;
        }
        if ($primarySeoTitle !== null) {
            $seo_title = $primarySeoTitle;
        } elseif ($secondarySeoTitle !== null) {
            $seo_title = $secondarySeoTitle;
        }
        if ($primarySeoDesc !== null) {
            $seo_description = $primarySeoDesc;
        } elseif ($secondarySeoDesc !== null) {
            $seo_description = $secondarySeoDesc;
        }
        if ($primarySeoKw !== null) {
            $seo_keywords = is_string($primarySeoKw) ? trim($primarySeoKw) : $primarySeoKw;
        } elseif ($secondarySeoKw !== null) {
            $seo_keywords = is_string($secondarySeoKw) ? trim($secondarySeoKw) : $secondarySeoKw;
        }
    }

    // Ảnh preview từ API (data.images – tối đa 6 ảnh): dùng khi hover item
    $preview_images = array();
    if (!empty($sourcePage["data"]["images"]) && is_array($sourcePage["data"]["images"])) {
        foreach ($sourcePage["data"]["images"] as $img) {
            if (!empty($img["path"])) {
                $preview_images[] = $img["path"];
            }
        }
    }
    if (empty($preview_images) && !empty($sourcePage["data"]["thumbnail"])) {
        $preview_images[] = $sourcePage["data"]["thumbnail"];
    }
    if (empty($preview_images) && !empty($sourcePage["data"]["images"][0]["path"])) {
        $preview_images[] = $sourcePage["data"]["images"][0]["path"];
    }

    $data = array(
        'crawl_filter' => false,
        'fetch_url' => $url,
        'fetch_topxx_code' => $code,
        'fetch_topxx_updated_at' => $updated_at,
        'title' => $title,
        'org_title' => $title,
        'thumbnail' => $sourcePage["data"]["thumbnail"],
        'poster' => !empty($sourcePage["data"]["images"][0]["path"]) ? $sourcePage["data"]["images"][0]["path"] : $sourcePage["data"]["thumbnail"],
        'preview_images' => $preview_images,
        'trailer_url' => '',
        'episode' => '',
        'total_episode' => '1',
        'tags' => $arrTags,
        'content' => strip_tags(preg_replace('/\\r?\\n/s', '', $content ?: (isset($sourcePage["movie"]["content"]) ? $sourcePage["movie"]["content"] : ''))),
        // Lưu dạng mảng tên + avatar thay vì chuỗi
        'actor' => $actor,
        'director' => '',
        'country' => $arrCountry,
        'cat' => $arrCat,
        'genres' => $arrGenres,
        'type' => 'single_movies',
        'lang' => '',
        'showtime' => '',
        'year' => '',
        'is_copyright' => '',
        'status' => 'completed',
        'duration' => $sourcePage["data"]["duration"],
        'quality' => $sourcePage["data"]["quality"],
        // SEO từ API (Rank Math / Yoast SEO)
        'seo_title' => $seo_title,
        'seo_description' => $seo_description,
        'seo_keywords' => $seo_keywords,
    );

    return $data;
}

function add_posts_topxx($data)
{

    $director = explode(',', sanitize_text_field($data['director']));

    $cat_id = array();
    foreach ($data['cat'] as $cat) {
        if (!term_exists($cat) && $cat != '') {
            wp_insert_term($cat, 'ophim_categories');
        }
    }
    foreach ($data['tags'] as $tag) {
        if (!term_exists($tag) && $tag != '') {
            wp_insert_term($tag, 'ophim_tags');
        }
    }
    $formality = 'single_movies';
    $post_data = array(
        'post_title' => $data['title'],
        'post_content' => $data['content'],
        'post_status' => 'publish',
        'post_type' => 'ophim',
        'comment_status' => 'closed',
        'ping_status' => 'closed',
        'post_author' => get_current_user_id()
    );
    $post_id = wp_insert_post($post_data);

    // Download & resize image
    $crawl_settings = json_decode(get_option(CRAWL_TOPXX_OPTION_SETTINGS, false));
    $thumb_image_url = download_resize_thumb_topxx($data, $post_id, $crawl_settings);
    $poster_image_url = download_resize_poster_topxx($data, $post_id, $crawl_settings);

    $status = getStatus_topxx($data['status']);
    //
    update_post_meta($post_id, 'ophim_movie_formality', $formality);
    update_post_meta($post_id, 'ophim_movie_status', $status);
    update_post_meta($post_id, 'ophim_fetch_info_url', $data['fetch_url']);
    update_post_meta($post_id, 'ophim_fetch_topxx_code', $data['fetch_topxx_code']);
    update_post_meta($post_id, 'ophim_fetch_topxx_updated_at', $data['fetch_topxx_updated_at']);
    update_post_meta($post_id, 'ophim_thumb_url', $thumb_image_url);
    update_post_meta($post_id, 'ophim_poster_url', $poster_image_url);
    if (!empty($data['preview_images']) && is_array($data['preview_images'])) {
        update_post_meta($post_id, 'ophim_preview_images', $data['preview_images']);
    }
    update_post_meta($post_id, 'ophim_original_title', $data['title']);
    update_post_meta($post_id, 'ophim_runtime', $data['duration']);
    update_post_meta($post_id, 'ophim_rating', '');
    update_post_meta($post_id, 'ophim_votes', '');
    update_post_meta($post_id, 'ophim_episode', '');
    update_post_meta($post_id, 'ophim_total_episode', $data['total_episode']);
    update_post_meta($post_id, 'ophim_quality', $data['quality']);
    update_post_meta($post_id, 'ophim_lang', $data['lang']);
    update_post_meta($post_id, 'ophim_showtime_movies', '');
    update_post_meta($post_id, 'ophim_year', $data['year']);
    update_post_meta($post_id, 'ophim_is_copyright', '');

    // SEO từ API: Rank Math & Yoast SEO
    if (!empty($data['seo_title'])) {
        update_post_meta($post_id, 'rank_math_title', sanitize_text_field($data['seo_title']));
        update_post_meta($post_id, '_yoast_wpseo_title', sanitize_text_field($data['seo_title']));
    }
    if (!empty($data['seo_description'])) {
        update_post_meta($post_id, 'rank_math_description', sanitize_textarea_field($data['seo_description']));
        update_post_meta($post_id, '_yoast_wpseo_metadesc', sanitize_textarea_field($data['seo_description']));
    }
    if (!empty($data['seo_keywords'])) {
        $kw_string = is_string($data['seo_keywords']) ? $data['seo_keywords'] : '';
        update_post_meta($post_id, 'rank_math_focus_keyword', sanitize_text_field($kw_string));
        $first_kw = trim(explode(',', $kw_string)[0]);
        if ($first_kw !== '') {
            update_post_meta($post_id, '_yoast_wpseo_focuskw', sanitize_text_field($first_kw));
        }
    }

    //
    wp_set_object_terms($post_id, $status, 'status', false);

    wp_set_object_terms($post_id, $director, 'ophim_directors', false);

    // Diễn viên: tạo/cập nhật term và lưu avatar vào term meta, sau đó gán cho post
    $actor_items = array();
    if (is_array($data['actor'])) {
        $actor_items = $data['actor'];
    } else {
        // Trường hợp cũ: chuỗi tên, không có avatar
        $names = array_filter(array_map('trim', explode(',', sanitize_text_field($data['actor']))));
        foreach ($names as $name) {
            $actor_items[] = array('name' => $name, 'avatar' => '');
        }
    }

    $actor_names = array();
    foreach ($actor_items as $actor_item) {
        $name = is_array($actor_item) ? (isset($actor_item['name']) ? $actor_item['name'] : '') : $actor_item;
        $avatar = is_array($actor_item) ? (isset($actor_item['avatar']) ? $actor_item['avatar'] : '') : '';

        if ($name === '') {
            continue;
        }

        $term = get_term_by('name', $name, 'ophim_actors');
        if (!$term) {
            $res = wp_insert_term($name, 'ophim_actors');
            if (!is_wp_error($res)) {
                $term_id = $res['term_id'];
                if ($avatar !== '') {
                    update_term_meta($term_id, 'actor_avatar', esc_url_raw($avatar));
                }
            }
        } else {
            $term_id = $term->term_id;
            if ($avatar !== '') {
                update_term_meta($term_id, 'actor_avatar', esc_url_raw($avatar));
            }
        }

        $actor_names[] = $name;
    }
    if (!empty($actor_names)) {
        wp_set_object_terms($post_id, $actor_names, 'ophim_actors', false);
    }

    wp_set_object_terms($post_id, sanitize_text_field($data['year']), 'ophim_years', false);
    wp_set_object_terms($post_id, $data['country'], 'ophim_regions', false);
    wp_set_object_terms($post_id, $data['cat'], 'ophim_categories', false);
    wp_set_object_terms($post_id, $data['tags'], 'ophim_tags', false);

    // Genres: tạo/cập nhật term và lưu thumbnail vào term meta, sau đó gán cho post
    $genre_names = array();
    foreach ($data['genres'] as $genre_item) {
        $name = is_array($genre_item) ? $genre_item['name'] : $genre_item;
        $thumbnail = is_array($genre_item) ? (isset($genre_item['thumbnail']) ? $genre_item['thumbnail'] : '') : '';
        if ($name === '') {
            continue;
        }
        $term = get_term_by('name', $name, 'ophim_genres');
        if (!$term) {
            $res = wp_insert_term($name, 'ophim_genres');
            if (!is_wp_error($res)) {
                $term_id = $res['term_id'];
                if ($thumbnail !== '') {
                    update_term_meta($term_id, 'genre_thumbnail', esc_url_raw($thumbnail));
                }
            }
        } else {
            $term_id = $term->term_id;
            if ($thumbnail !== '') {
                update_term_meta($term_id, 'genre_thumbnail', esc_url_raw($thumbnail));
            }
        }
        $genre_names[] = $name;
    }
    wp_set_object_terms($post_id, $genre_names, 'ophim_genres', false);

    return $post_id;
}

function download_resize_thumb_topxx($data, $post_id, $crawl_settings)
{
    $thumb_image_url = $data['thumbnail'];
    $convert_webp = oIsset($crawl_settings, 'crawl_convert_webp', 'off') == 'on' ? true : false;
    try {
        if (oIsset($crawl_settings, 'crawl_resize_size_thumb', 'off') == 'on') {
            $res_thumb = save_images(
                $data['thumbnail'],
                $post_id, $data['title'],
                'thumb',
                oIsset($crawl_settings, 'crawl_resize_size_thumb_w', 0),
                oIsset($crawl_settings, 'crawl_resize_size_thumb_h', 0),
                $convert_webp,
                true
            );
            $thumb_image_url = str_replace(get_site_url(), '', $res_thumb['url']);
        }
    } catch (Exception $e) {
    }
    return $thumb_image_url;
}

function download_resize_poster_topxx($data, $post_id, $crawl_settings)
{
    $poster_image_url = $data['poster'];
    $convert_webp = oIsset($crawl_settings, 'crawl_convert_webp', 'off') == 'on' ? true : false;
    try {
        if (oIsset($crawl_settings, 'crawl_resize_size_poster', 'off') == 'on') {
            if ($data['poster'] && $data['poster'] != "") {
                $res = save_images(
                    $data['poster'],
                    $post_id,
                    $data['title'],
                    'poster',
                    oIsset($crawl_settings, 'crawl_resize_size_poster_w', 0),
                    oIsset($crawl_settings, 'crawl_resize_size_poster_h', 0),
                    $convert_webp
                );
                $poster_image_url = str_replace(get_site_url(), '', $res['url']);
            }
        }
    } catch (Exception $e) {
    }
    return $poster_image_url;
}

function save_images_topxx($image_url, $post_id, $posttitle, $suffix, $max_w, $max_h, $is_webp = false, $set_thumb = false)
{
    // Khởi tạo curl để tải về hình ảnh
    $ch = curl_init($image_url);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36");
    $file = curl_exec($ch);
    curl_close($ch);

    $postname = sanitize_title($posttitle);
    $file_extension = $is_webp ? 'webp' : 'jpg';
    $im_name = "$postname-$post_id-$suffix.$file_extension";
    $res = wp_upload_bits($im_name, '', $file);
    if (!$res['error'] && $file) {
        $image_path = $res['file'];
        $editor = wp_get_image_editor($image_path);
        if (!is_wp_error($editor)) {
            $editor->resize($max_w, $max_h, false);
            $editor->save($image_path, $is_webp ? 'image/webp' : 'image/jpeg');
        }
        insert_attachment($res['file'], $post_id, $set_thumb);
    }
    return $res;
}

function insert_attachment_topxx($file, $post_id, $set_thumb)
{
    $dirs = wp_upload_dir();
    $filetype = wp_check_filetype($file);
    $attachment = array(
        'guid' => $dirs['baseurl'] . '/' . _wp_relative_upload_path($file),
        'post_mime_type' => $filetype['type'],
        'post_title' => preg_replace('/\.[^.]+$/', '', basename($file)),
        'post_content' => '',
        'post_status' => 'inherit'
    );
    $attach_id = wp_insert_attachment($attachment, $file, $post_id);
    $attach_data = wp_generate_attachment_metadata($attach_id, $file);
    wp_update_attachment_metadata($attach_id, $attach_data);
    if ($set_thumb != false) set_post_thumbnail($post_id, $attach_id);
    return $attach_id;
}

function get_list_episode_topxx($sourcePage, $post_id)
{
    $episode_list = array();

    if (isset($sourcePage["data"]["sources"]) && is_array($sourcePage["data"]["sources"])) {
        $index = 1;

        foreach ($sourcePage["data"]["sources"] as $source) {
            $episode_list[] = array(
                'server_name' => 'Topxx #' . $index,
                'is_ai'       => false,
                'server_data' => array(
                    array(
                        'name'       => 'Full',
                        'slug'       => 'full',
                        'filename'   => 'Full',
                        'link_embed' => isset($source["link"]) ? $source["link"] : '',
                        'link_m3u8'  => isset($source["m3u8"]) ? $source["m3u8"] : '',
                    )
                ),
            );

            $index++;
        }
    }

    update_post_meta($post_id, 'ophim_episode_list', $episode_list);

    return json_encode($episode_list);
}

function slugify_topxx($str, $divider = '-')
{
    $str = trim(mb_strtolower($str));
    $str = preg_replace('/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/', 'a', $str);
    $str = preg_replace('/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/', 'e', $str);
    $str = preg_replace('/(ì|í|ị|ỉ|ĩ)/', 'i', $str);
    $str = preg_replace('/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/', 'o', $str);
    $str = preg_replace('/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/', 'u', $str);
    $str = preg_replace('/(ỳ|ý|ỵ|ỷ|ỹ)/', 'y', $str);
    $str = preg_replace('/(đ)/', 'd', $str);
    $str = preg_replace('/[^a-z0-9-\s]/', '', $str);
    $str = preg_replace('/([\s]+)/', $divider, $str);
    return $str;
}

function getStatus_topxx($status)
{
    $hl_status = "completed";
    return $hl_status;
}