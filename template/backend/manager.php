<style>
    .topxx-wrap { width: 100%; padding: 20px 30px; box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
    .topxx-hero { background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%); border-radius: 16px; padding: 36px 40px; color: #fff; margin-bottom: 28px; box-shadow: 0 8px 32px rgba(0,0,0,0.3); display: flex; align-items: center; gap: 28px; }
    .topxx-hero-logo { width: 72px; height: 72px; border-radius: 14px; object-fit: contain; background: rgba(255,255,255,0.08); padding: 6px; }
    .topxx-hero-text { flex: 1; }
    .topxx-hero-title { font-size: 36px; font-weight: 800; letter-spacing: 2px; background: linear-gradient(90deg, #e94560, #ff6b6b); -webkit-background-clip: text; -webkit-text-fill-color: transparent; margin: 0 0 4px 0; }
    .topxx-hero-ver { background: #e94560; color: #fff; font-size: 11px; padding: 2px 10px; border-radius: 20px; font-weight: 600; margin-left: 10px; vertical-align: middle; }
    .topxx-hero-desc { font-size: 15px; line-height: 1.7; color: #b8c4d8; margin: 8px 0 0 0; }
    .topxx-hero-desc strong { color: #e94560; }
    .topxx-links { display: flex; gap: 10px; margin-top: 14px; flex-wrap: wrap; }
    .topxx-links a { display: inline-flex; align-items: center; gap: 6px; background: rgba(255,255,255,0.1); color: #fff; padding: 6px 14px; border-radius: 8px; text-decoration: none; font-size: 13px; font-weight: 500; transition: background 0.2s; }
    .topxx-links a:hover { background: rgba(233,69,96,0.4); }
    .topxx-links a svg { flex-shrink: 0; vertical-align: middle; }
    .topxx-card { background: #fff; border-radius: 12px; padding: 28px 30px; margin-bottom: 20px; box-shadow: 0 2px 12px rgba(0,0,0,0.06); }
    .topxx-card h2 { margin: 0 0 18px 0; color: #1a1a2e; font-size: 20px; border-bottom: 3px solid #e94560; padding-bottom: 8px; display: inline-block; }
    .topxx-card h3 { margin: 0 0 6px 0; color: #1a1a2e; font-size: 15px; font-weight: 600; }
    .topxx-card p, .topxx-card li { color: #444; line-height: 1.8; font-size: 14px; }
    .topxx-card a { color: #e94560; text-decoration: none; font-weight: 500; }
    .topxx-card a:hover { text-decoration: underline; }
    .topxx-feat-list { list-style: none; padding: 0; margin: 12px 0; }
    .topxx-feat-list li { padding: 7px 0 7px 26px; position: relative; }
    .topxx-feat-list li::before { content: "\2713"; position: absolute; left: 0; color: #e94560; font-weight: bold; font-size: 15px; }
    .topxx-feat-list li strong { color: #0f3460; }
    .topxx-doc-section { background: #f8f9ff; border-left: 4px solid #e94560; padding: 16px 20px; border-radius: 0 8px 8px 0; margin-bottom: 14px; }
    .topxx-doc-section.alt { border-left-color: #0f3460; }
    .topxx-doc-section h3 { margin: 0 0 8px 0; color: #1a1a2e; font-size: 15px; font-weight: 600; }
    .topxx-doc-section p { margin: 4px 0; color: #555; font-size: 13.5px; line-height: 1.7; }
    .topxx-doc-section ol, .topxx-doc-section ul { margin: 6px 0 4px 18px; padding: 0; }
    .topxx-doc-section ol li, .topxx-doc-section ul li { margin: 3px 0; color: #555; font-size: 13.5px; line-height: 1.7; }
    .topxx-doc-section code { background: #e8ecf4; padding: 1px 6px; border-radius: 4px; font-size: 12.5px; color: #0f3460; }
    .topxx-doc-section .topxx-path { color: #888; font-style: italic; font-size: 12.5px; display: block; margin-top: 4px; }
    .topxx-doc-note { background: #fff8e1; border-left: 4px solid #ffc107; padding: 12px 18px; border-radius: 0 8px 8px 0; margin: 14px 0; font-size: 13px; color: #6d5600; line-height: 1.7; }
    .topxx-doc-note strong { color: #e65100; }
    .topxx-api-table { width: 100%; border-collapse: collapse; margin: 10px 0; font-size: 13px; }
    .topxx-api-table th { background: #f0f4ff; text-align: left; padding: 8px 12px; color: #1a1a2e; font-weight: 600; border-bottom: 2px solid #dde3f0; }
    .topxx-api-table td { padding: 7px 12px; border-bottom: 1px solid #eef1f7; color: #444; }
    .topxx-api-table code { background: #e8ecf4; padding: 1px 5px; border-radius: 3px; font-size: 12px; }
    .topxx-footer { text-align: center; padding: 16px; color: #999; font-size: 12px; }
    .topxx-footer a { color: #e94560; text-decoration: none; }
</style>

<div class="topxx-wrap">

    <div class="topxx-hero">
        <img src="https://topxx.vip/storage/uploads/logo/logo-1766542694.png" alt="TOPXX" class="topxx-hero-logo">
        <div class="topxx-hero-text">
            <div>
                <span class="topxx-hero-title">TOPXX</span>
                <span class="topxx-hero-ver">v1.0.1</span>
            </div>
            <p class="topxx-hero-desc">
                <strong>Topxx.vip</strong> &mdash; Hệ thống kho dữ liệu phim tập trung, cung cấp đầy đủ metadata
                (tiêu đề, mô tả, poster, diễn viên, thể loại, quốc gia, nguồn phát) cho các nền tảng xem phim
                thông qua API đơn giản và linh hoạt.
            </p>
            <div class="topxx-links">
                <a href="https://topxx.vip" target="_blank"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg> Website</a>
                <a href="https://topxx.vip/api" target="_blank"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg> API Docs</a>
                <a href="https://t.me/hotrokhophim" target="_blank"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg> Telegram</a>
                <a href="https://github.com/ofilmcms" target="_blank"><svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0C5.37 0 0 5.37 0 12c0 5.31 3.435 9.795 8.205 11.385.6.105.825-.255.825-.57 0-.285-.015-1.23-.015-2.235-3.015.555-3.795-.735-4.035-1.41-.135-.345-.72-1.41-1.23-1.695-.42-.225-1.02-.78-.015-.795.945-.015 1.62.87 1.845 1.23 1.08 1.815 2.805 1.305 3.495.99.105-.78.42-1.305.765-1.605-2.67-.3-5.46-1.335-5.46-5.925 0-1.305.465-2.385 1.23-3.225-.12-.3-.54-1.53.12-3.18 0 0 1.005-.315 3.3 1.23.96-.27 1.98-.405 3-.405s2.04.135 3 .405c2.295-1.56 3.3-1.23 3.3-1.23.66 1.65.24 2.88.12 3.18.765.84 1.23 1.905 1.23 3.225 0 4.605-2.805 5.625-5.475 5.925.435.375.81 1.095.81 2.22 0 1.605-.015 2.895-.015 3.3 0 .315.225.69.825.57A12.02 12.02 0 0 0 24 12c0-6.63-5.37-12-12-12z"/></svg> GitHub</a>
            </div>
        </div>
    </div>

    <div class="topxx-card">
        <h2>Giới thiệu TOPXX</h2>
        <div>
            <p><strong style="color: #0f3460;">TOPXX (Topxx.vip)</strong> là kho dữ liệu phim tập trung, cập nhật nhanh, chất lượng cao và ổn định. Tốc độ phát cực nhanh với đường truyền băng thông cao, đảm bảo đáp ứng lượng xem phim trực tuyến lớn. Đồng thời giúp nhà phát triển website phim giảm thiểu chi phí lưu trữ và streaming.</p>
            <p>Dữ liệu được cung cấp qua API bao gồm:</p>
            <ul class="topxx-feat-list">
                <li><strong>Tiêu đề & Mô tả</strong> &mdash; Tên phim gốc (tiếng Anh/gốc), tên tiếng Việt, nội dung tóm tắt chi tiết</li>
                <li><strong>Hình ảnh chất lượng cao</strong> &mdash; Poster, thumbnail, ảnh preview (tối đa 6 ảnh hover)</li>
                <li><strong>Diễn viên & Đạo diễn</strong> &mdash; Thông tin ekip sản xuất, avatar diễn viên</li>
                <li><strong>Thể loại & Quốc gia</strong> &mdash; Phân loại chính xác, hỗ trợ đa ngôn ngữ (Việt/Anh)</li>
                <li><strong>Nguồn phát (Embed)</strong> &mdash; Server phát ổn định, tốc độ cao, băng thông lớn, Full HD</li>
                <li><strong>Cập nhật liên tục</strong> &mdash; Phim mới, tập mới được đồng bộ hàng ngày tự động 24/7</li>
            </ul>
            <p>API được cung cấp <strong style="color: #e94560;">hoàn toàn miễn phí</strong>, không giới hạn request, không cần đăng ký tài khoản.</p>
        </div>
    </div>

    <div class="topxx-card">
        <h2>Hướng dẫn sử dụng chi tiết</h2>

        <div class="topxx-doc-section">
            <h3>1. Sửa lỗi 404 (Bắt buộc sau khi cài đặt)</h3>
            <p>Sau khi kích hoạt plugin, các trang phim/thể loại/quốc gia có thể bị lỗi 404. Đây là do WordPress chưa cập nhật rewrite rules.</p>
            <ol>
                <li>Vào <strong>Cài đặt</strong> &rarr; <a href="/wp-admin/options-permalink.php"><strong>Đường dẫn tĩnh</strong></a> (Permalinks).</li>
                <li>Không cần thay đổi gì, chỉ cần nhấn nút <strong>Lưu thay đổi</strong> ở cuối trang.</li>
                <li>WordPress sẽ tự động tạo lại rewrite rules và lỗi 404 sẽ được khắc phục.</li>
            </ol>
            <div class="topxx-doc-note">
                <strong>Lưu ý:</strong> Mỗi khi bạn thay đổi cấu trúc đường dẫn (slug) của phim, thể loại, quốc gia... trong phần <strong>TOPXX Permalink Settings</strong>, bạn cần vào lại trang này và nhấn Lưu để cập nhật.
            </div>
        </div>

        <div class="topxx-doc-section alt">
            <h3>2. Cấu hình đường dẫn (Permalink)</h3>
            <p>Plugin tự động thêm phần <strong>TOPXX Permalink Settings</strong> vào trang Cài đặt &rarr; Đường dẫn tĩnh. Tại đây bạn có thể tùy chỉnh slug cho từng loại:</p>
            <table class="topxx-api-table">
                <thead>
                    <tr><th>Mục</th><th>Slug mặc định</th><th>Ví dụ URL</th></tr>
                </thead>
                <tbody>
                    <tr><td>Trang phim</td><td><code>movie</code></td><td>domain.com/<strong>movie</strong>/ten-phim</td></tr>
                    <tr><td>Xem phim</td><td><code>xem-phim</code></td><td>domain.com/<strong>xem-phim</strong>/ten-phim/tap-1-sv-0</td></tr>
                    <tr><td>Thể loại</td><td><code>genres</code></td><td>domain.com/<strong>genres</strong>/hanh-dong</td></tr>
                    <tr><td>Quốc gia</td><td><code>regions</code></td><td>domain.com/<strong>regions</strong>/nhat-ban</td></tr>
                    <tr><td>Diễn viên</td><td><code>actors</code></td><td>domain.com/<strong>actors</strong>/ten-dien-vien</td></tr>
                    <tr><td>Đạo diễn</td><td><code>directors</code></td><td>domain.com/<strong>directors</strong>/ten-dao-dien</td></tr>
                    <tr><td>Danh mục</td><td><code>categories</code></td><td>domain.com/<strong>categories</strong>/phim-le</td></tr>
                    <tr><td>Tags</td><td><code>tags</code></td><td>domain.com/<strong>tags</strong>/tu-khoa</td></tr>
                    <tr><td>Năm</td><td><code>years</code></td><td>domain.com/<strong>years</strong>/2026</td></tr>
                </tbody>
            </table>
            <span class="topxx-path">Cài đặt &rarr; Đường dẫn tĩnh &rarr; cuộn xuống phần "TOPXX Permalink Settings"</span>
        </div>

        <div class="topxx-doc-section">
            <h3>3. Crawl phim từ Topxx.vip</h3>
            <p>Đây là tính năng chính của plugin &mdash; thu thập dữ liệu phim từ API Topxx.vip về WordPress.</p>

            <p><strong>3.1. Crawl thủ công:</strong></p>
            <ol>
                <li>Vào <strong>Cài đặt TOPXX</strong> &rarr; <a href="/wp-admin/admin.php?page=ofim-manager-crawl-topxx"><strong>Crawl Topxx</strong></a> &rarr; Tab <strong>Thủ công</strong>.</li>
                <li><strong>Chọn ngôn ngữ ưu tiên:</strong> Tiếng Việt hoặc Tiếng Anh (ảnh hưởng đến tiêu đề, mô tả phim).</li>
                <li><strong>Bỏ qua thể loại:</strong> Tích chọn các thể loại bạn không muốn crawl &rarr; Nhấn <strong>Lưu cấu hình</strong>.</li>
                <li><strong>Cấu hình hình ảnh:</strong> Tùy chọn tải & resize thumb/poster, chuyển đổi WebP.</li>
                <li>Nhập <strong>Page Crawl</strong> (From/To) &mdash; ví dụ: From <code>1</code> To <code>10</code> để crawl 10 trang đầu.</li>
                <li>URL API mặc định: <code>https://topxx.vip/api/v1/movies/latest</code></li>
                <li>Nhấn <strong>Get List Movies</strong> để lấy danh sách link phim.</li>
                <li>(Tùy chọn) Nhấn <strong>Trộn Link</strong> để xáo trộn thứ tự &mdash; tránh trùng lặp content giữa các website.</li>
                <li>Nhấn <strong>Crawl Movies</strong> để bắt đầu thu thập. Kết quả sẽ hiển thị ở ô Thành công / Lỗi bên dưới.</li>
            </ol>

            <p><strong>3.2. Crawl tự động (Crontab):</strong></p>
            <ol>
                <li>Vào tab <strong>Tự động</strong> trong trang Crawl Topxx.</li>
                <li>Thiết lập <strong>Secret Key</strong> &rarr; Nhấn <strong>Lưu mật khẩu</strong>.</li>
                <li>Tích chọn <strong>Kích hoạt</strong> để bật chế độ tự động.</li>
                <li>Cấu hình crontab trên server (SSH/cPanel):
                    <br><code>*/10 * * * * cd /path/to/wp-content/plugins/xphim-plugin/ && php -q schedule-topxx.php {secret_key}</code></li>
                <li>Hệ thống sẽ tự động crawl theo lịch, kết quả ghi vào file log hiển thị trên trang admin.</li>
            </ol>

            <div class="topxx-doc-note">
                <strong>Mẹo:</strong> Hàng ngày nên crawl 10&ndash;20 trang đầu để cập nhật tập mới và phim mới. Sử dụng nút "Trộn Link" để thay đổi thứ tự crawl, giúp nội dung website không bị trùng lặp với các site khác.
            </div>
        </div>

        <div class="topxx-doc-section alt">
            <h3>4. Quản lý phim</h3>
            <p>Sau khi crawl, phim sẽ xuất hiện trong mục <strong>TOPXX</strong> trên sidebar WordPress Admin.</p>
            <ul>
                <li><strong>Danh sách phim:</strong> Vào <a href="/wp-admin/edit.php?post_type=ophim"><strong>TOPXX</strong> &rarr; <strong>Danh sách</strong></a> &mdash; xem, tìm kiếm, lọc phim nổi bật, xem views/thumb/poster.</li>
                <li><strong>Thêm phim mới:</strong> Vào <a href="/wp-admin/post-new.php?post_type=ophim"><strong>TOPXX</strong> &rarr; <strong>Thêm phim</strong></a> &mdash; nhập tiêu đề, nội dung, và điền thông tin trong box <strong>Thông tin TOPXX</strong>.</li>
                <li><strong>Chỉnh sửa phim:</strong> Click vào tên phim trong danh sách để sửa. Các trường thông tin bao gồm:
                    <ul>
                        <li><strong>Trạng thái:</strong> Sắp chiếu / Đang chiếu / Hoàn thành</li>
                        <li><strong>Tiêu đề gốc:</strong> Tên phim bằng ngôn ngữ gốc</li>
                        <li><strong>Thời lượng:</strong> Độ dài phim (ví dụ: 120 phút)</li>
                        <li><strong>Tổng tập:</strong> Số tập của phim bộ</li>
                        <li><strong>Chất lượng:</strong> HD, Full HD, CAM...</li>
                        <li><strong>Ảnh thumb & poster:</strong> URL hoặc upload trực tiếp</li>
                    </ul>
                </li>
                <li><strong>Tập phim:</strong> Khi chỉnh sửa phim, box <strong>Tập phim</strong> cho phép quản lý danh sách server và các tập (link embed, link m3u8).</li>
            </ul>
        </div>

        <div class="topxx-doc-section">
            <h3>5. Quản lý Taxonomy (Phân loại)</h3>
            <p>Plugin tạo các taxonomy riêng cho phim, quản lý tại sidebar <strong>TOPXX</strong>:</p>
            <table class="topxx-api-table">
                <thead>
                    <tr><th>Taxonomy</th><th>Mô tả</th><th>Đường dẫn Admin</th></tr>
                </thead>
                <tbody>
                    <tr><td>Thể loại</td><td>Hành động, Kinh dị, Tình cảm...</td><td><a href="/wp-admin/edit-tags.php?taxonomy=ophim_genres&post_type=ophim">TOPXX &rarr; Thể loại</a></td></tr>
                    <tr><td>Quốc gia</td><td>Việt Nam, Nhật Bản, Mỹ...</td><td><a href="/wp-admin/edit-tags.php?taxonomy=ophim_regions&post_type=ophim">TOPXX &rarr; Quốc gia</a></td></tr>
                    <tr><td>Diễn viên</td><td>Tên diễn viên (có avatar)</td><td><a href="/wp-admin/edit-tags.php?taxonomy=ophim_actors&post_type=ophim">TOPXX &rarr; Diễn viên</a></td></tr>
                    <tr><td>Đạo diễn</td><td>Tên đạo diễn</td><td><a href="/wp-admin/edit-tags.php?taxonomy=ophim_directors&post_type=ophim">TOPXX &rarr; Đạo diễn</a></td></tr>
                    <tr><td>Danh mục</td><td>Phim lẻ, Phim bộ, Hoạt hình...</td><td><a href="/wp-admin/edit-tags.php?taxonomy=ophim_categories&post_type=ophim">TOPXX &rarr; Danh mục</a></td></tr>
                    <tr><td>Tags</td><td>Từ khóa liên quan</td><td><a href="/wp-admin/edit-tags.php?taxonomy=ophim_tags&post_type=ophim">TOPXX &rarr; Tags</a></td></tr>
                    <tr><td>Năm</td><td>Năm phát hành</td><td><a href="/wp-admin/edit-tags.php?taxonomy=ophim_years&post_type=ophim">TOPXX &rarr; Năm</a></td></tr>
                </tbody>
            </table>
        </div>

        <div class="topxx-doc-section alt">
            <h3>6. Thêm Menu điều hướng</h3>
            <p>Để hiển thị thể loại, quốc gia... trên menu website:</p>
            <ol>
                <li>Vào <strong>Giao diện</strong> &rarr; <a href="/wp-admin/nav-menus.php"><strong>Menu</strong></a>.</li>
                <li>Tạo menu mới hoặc chọn menu hiện có, đặt vị trí hiển thị là <strong>Primary Menu</strong>.</li>
                <li>Ở góc trên bên phải, nhấn <strong>Tùy chọn hiển thị</strong> (Screen Options).</li>
                <li>Tích chọn các mục của TOPXX: <strong>Thể loại</strong>, <strong>Quốc gia</strong>, <strong>Danh mục</strong>, <strong>Diễn viên</strong>...</li>
                <li>Các mục này sẽ xuất hiện ở cột bên trái &rarr; Tích chọn và nhấn <strong>Thêm vào Menu</strong>.</li>
                <li>Sắp xếp thứ tự bằng cách kéo thả &rarr; Nhấn <strong>Lưu Menu</strong>.</li>
            </ol>
        </div>

        <div class="topxx-doc-section">
            <h3>7. Thêm Widget</h3>
            <p>Hiển thị nội dung phim ở sidebar, footer hoặc các vùng widget của theme:</p>
            <ol>
                <li>Vào <strong>Giao diện</strong> &rarr; <a href="/wp-admin/widgets.php"><strong>Widget</strong></a>.</li>
                <li>Tìm các widget liên quan đến phim trong danh sách bên trái.</li>
                <li>Kéo thả widget vào vùng hiển thị mong muốn (Sidebar, Footer...).</li>
                <li>Cấu hình tiêu đề, số lượng hiển thị &rarr; Nhấn <strong>Lưu</strong>.</li>
            </ol>
        </div>

        <div class="topxx-doc-section alt">
            <h3>8. Thay đổi Logo website</h3>
            <ol>
                <li>Vào <strong>Giao diện</strong> &rarr; <a href="/wp-admin/customize.php"><strong>Tùy biến</strong></a> (Customize).</li>
                <li>Chọn <strong>Nhận dạng site</strong> (Site Identity).</li>
                <li>Nhấn <strong>Chọn logo</strong> &rarr; Upload hoặc chọn ảnh từ thư viện &rarr; Nhấn <strong>Xuất bản</strong>.</li>
            </ol>
        </div>

        <div class="topxx-doc-section">
            <h3>9. Số phim hiển thị mỗi trang</h3>
            <ol>
                <li>Vào <strong>Cài đặt</strong> &rarr; <a href="/wp-admin/options-reading.php"><strong>Đọc</strong></a> (Reading).</li>
                <li>Tại mục <strong>"Hiển thị nhiều nhất"</strong>, nhập số lượng phim muốn hiển thị trên mỗi trang (ví dụ: <code>20</code>).</li>
                <li>Nhấn <strong>Lưu thay đổi</strong>.</li>
            </ol>
            <div class="topxx-doc-note">
                <strong>Lưu ý:</strong> Con số này ảnh hưởng đến tất cả các trang archive (danh sách phim, thể loại, quốc gia, diễn viên...).
            </div>
        </div>

    </div>

    <div class="topxx-card">
        <h2>Liên hệ & Hỗ trợ</h2>
        <table class="topxx-api-table">
            <tbody>
                <tr><td style="width: 140px;"><strong>Website</strong></td><td><a href="https://topxx.vip" target="_blank">https://topxx.vip</a></td></tr>
                <tr><td><strong>API Documentation</strong></td><td><a href="https://topxx.vip/api" target="_blank">https://topxx.vip/api</a></td></tr>
                <tr><td><strong>Nhóm Telegram</strong></td><td><a href="https://t.me/hotrokhophim" target="_blank">https://t.me/hotrokhophim</a> (Topxx.vip - Kho Phim API Data)</td></tr>
                <tr><td><strong>GitHub</strong></td><td><a href="https://github.com/ofilmcms" target="_blank">https://github.com/ofilmcms</a> (Plugin + Themes)</td></tr>
            </tbody>
        </table>
    </div>

    <div class="topxx-footer">
        TOPXX Plugin v1.0.1 &mdash; <a href="https://topxx.vip" target="_blank">topxx.vip</a> &mdash; &copy; <?php echo date('Y'); ?>
    </div>

</div>
