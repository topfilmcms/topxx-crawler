
<div id="api_table">
    <table class="options-table-responsive dt-options-table">
        <tbody>
        <tr>
            <td class="label"><label>Trạng thái</label></td>
            <td class="field">
                <?php
                $ophim_movie_status = op_get_meta('movie_status');
                ?>
                <?php
                $f = array( 'trailer' => __('Sắp chiếu', 'topxx'), 'ongoing' => __('Đang chiếu', 'topxx'), 'completed' => __('Hoàn thành', 'topxx'));
                foreach ($f as $x => $n ) { ?>
                    <label for="<?php echo $ophim_movie_status ?>_<?php echo $x ?>">
                        <input id="<?php echo $ophim_movie_status; ?>_<?php echo $x ?>" class="<?php echo $x ?>" name="ophim[ophim_movie_status]" type="radio" value="<?php echo $x ?>" <?php if (isset($ophim_movie_status)) { checked( $x, $ophim_movie_status, true ); } ?> /> <?php echo $n ?>
                    </label>
                <?php } ?>
            </td>
        </tr>
        <tr>
            <td class="label"><label>Tiêu đề gốc</label></td>
            <td class="field">

                <input name="ophim[ophim_original_title]" type="text" value="<?= op_get_meta('original_title') ?>">
            </td>
        </tr>
        <tr>
            <td class="label">
                <label>Thời lượng</label>
            </td>
            <td class="field">
                <input name="ophim[ophim_runtime]" type="text" value="<?= op_get_meta('runtime') ?>">
            </td>
        </tr>
        <tr>
            <td class="label">
                <label>Tổng tập </label>
            </td>
            <td class="field">
                <input name="ophim[ophim_total_episode]" type="text" value="<?= op_get_meta('total_episode') ?>">
            </td>
        </tr>
        <tr>
            <td class="label">
                <label>Chất lượng</label>
            </td>
            <td class="field">
                <input name="ophim[ophim_quality]" type="text" value="<?= op_get_meta('quality') ?>">
            </td>
        </tr>
        <tr>
            <td class="label">
                <label>Đường dẫn gốc </label>
            </td>
            <td class="field">
                <input name="ophim[ophim_fetch_info_url]" type="text" value="<?= op_get_meta('fetch_info_url') ?>">
            </td>
        </tr>
        <tr>
            <td class="label">
                <label>Ảnh thumb</label>
            </td>
            <td class="field">
                <input name="ophim[ophim_thumb_url]" id="thumb" type="text" value="<?= op_get_meta('thumb_url') ?>">
                <img id="thumb_url" src="<?= op_get_meta('thumb_url') ?>" alt="" style="max-height: 100px">
            </td>
        </tr>
        <tr>
            <td class="label">
            </td>
            <td class="field">
                <a href="#" class="ophim_upload_image_thumb_url button">Upload image</a>
            </td>
        </tr>
        <tr>
            <td class="label">
                <label>Ảnh poster</label>
            </td>
            <td class="field">
                <input name="ophim[ophim_poster_url]" id="poster" type="text" value="<?= op_get_meta('poster_url') ?>">
                <img id="imgPoster" src="<?= op_get_meta('poster_url') ?>" alt="" style="max-height: 100px">
            </td>
        </tr>
        <tr>
            <td class="label">
            </td>
            <td class="field">
                <a href="#" class="ophim_upload_image_button button">Upload image</a>
            </td>
        </tr>
        <tr>
            <td class="label">
                <label>Ảnh preview hover</label>
                <p style="font-size: 11px; color: #666; margin-top: 4px;">Tối đa 6 ảnh hiển thị khi hover</p>
            </td>
            <td class="field">
                <?php
                $preview_images = get_post_meta($post->ID, 'ophim_preview_images', true);
                if (!is_array($preview_images)) $preview_images = array();
                $preview_images = array_values(array_filter($preview_images));
                ?>
                <div id="ophim-preview-images-wrap" style="display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 10px;">
                    <?php foreach ($preview_images as $idx => $img_url) : ?>
                    <div class="ophim-preview-item" style="position: relative; display: inline-block;">
                        <img src="<?php echo esc_url($img_url); ?>" style="width: 120px; height: 80px; object-fit: cover; border-radius: 4px; border: 1px solid #ddd;">
                        <input type="hidden" name="ophim_preview_images[]" value="<?php echo esc_attr($img_url); ?>">
                        <button type="button" class="ophim-preview-remove" title="Xóa ảnh" style="position: absolute; top: -6px; right: -6px; background: #dc3232; color: #fff; border: none; border-radius: 50%; width: 20px; height: 20px; font-size: 14px; line-height: 18px; cursor: pointer; padding: 0;">&times;</button>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div style="display: flex; gap: 8px; align-items: center;">
                    <button type="button" class="button ophim-preview-upload-btn">Chọn từ thư viện</button>
                    <button type="button" class="button ophim-preview-add-url-btn">Thêm URL</button>
                    <button type="button" class="button ophim-preview-clear-all-btn" style="color: #dc3232;<?php echo empty($preview_images) ? ' display:none;' : ''; ?>">Xóa tất cả</button>
                </div>
                <div id="ophim-preview-url-input" style="display: none; margin-top: 8px;">
                    <input type="text" id="ophim-preview-url-field" placeholder="Nhập URL ảnh..." style="width: 70%;">
                    <button type="button" class="button ophim-preview-url-confirm">Thêm</button>
                    <button type="button" class="button ophim-preview-url-cancel">Hủy</button>
                </div>
            </td>
        </tr>
        </tbody>
    </table>
</div>
