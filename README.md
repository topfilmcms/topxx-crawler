# TOPXX Plugin for WordPress

**Version:** 1.0.1
**Website:** [https://topxx.vip](https://topxx.vip)
**Telegram:** [https://t.me/hotrokhophim](https://t.me/hotrokhophim)
**GitHub:** [https://github.com/ofilmcms](https://github.com/ofilmcms)

## Overview

TOPXX is a WordPress plugin that connects your site to the Topxx.vip movie database API. It provides automated and manual crawling of movie data including titles, descriptions, posters, actors, directors, genres, countries, and streaming sources.

The API is free, unlimited, and requires no registration.

## Features

- Crawl movies automatically (crontab) or manually from Topxx.vip API
- Full metadata sync: title, description, poster, thumbnail, actors, directors, genres, countries, episodes
- Manage movies, episodes, and multiple streaming servers directly in WordPress Admin
- Filter out unwanted genres during crawl
- Download & resize thumbnail/poster images with WebP conversion
- Scheduled auto-crawl via crontab — runs 24/7 in the background
- Language priority support (Vietnamese or English)

## Installation

1. Upload the `xphim-plugin` folder to `/wp-content/plugins/` on your hosting.
2. Go to **WordPress Admin** > **Plugins** > Find **TOPXX** > Click **Activate**.
3. After activation, the **TOPXX Settings** and **TOPXX** menus will appear in the admin sidebar.

## Post-Installation Setup

### Fix 404 Errors

After activating the plugin, movie/genre/country pages may return 404 errors because WordPress hasn't updated its rewrite rules yet.

1. Go to **Settings** > **Permalinks**.
2. Without changing anything, click **Save Changes** at the bottom.
3. WordPress will regenerate the rewrite rules automatically.

> **Note:** Repeat this step whenever you change the URL structure (slugs) in **TOPXX Permalink Settings**.

### Configure Permalinks

The plugin adds a **TOPXX Permalink Settings** section to **Settings** > **Permalinks**. Customizable slugs:

| Item | Default Slug | Example URL |
|------|-------------|-------------|
| Movie page | `movie` | domain.com/**movie**/movie-name |
| Watch page | `xem-phim` | domain.com/**xem-phim**/movie-name/ep-1-sv-0 |
| Genres | `genres` | domain.com/**genres**/action |
| Countries | `regions` | domain.com/**regions**/japan |
| Actors | `actors` | domain.com/**actors**/actor-name |
| Directors | `directors` | domain.com/**directors**/director-name |
| Categories | `categories` | domain.com/**categories**/single-movie |
| Tags | `tags` | domain.com/**tags**/keyword |
| Years | `years` | domain.com/**years**/2026 |

## Crawling Movies

### Manual Crawl

1. Go to **TOPXX Settings** > **Crawl Topxx** > **Manual** tab.
2. Choose language priority (Vietnamese or English).
3. Select genres to skip > Click **Save Config**.
4. Configure image options (download & resize thumb/poster, WebP conversion).
5. Set **Page Crawl** range (e.g., From `1` To `10`).
6. Click **Get List Movies** to fetch movie URLs.
7. (Optional) Click **Shuffle Links** to randomize crawl order.
8. Click **Crawl Movies** to start. Results appear in the Success/Error sections below.

### Auto Crawl (Crontab)

1. Go to the **Auto** tab in Crawl Topxx.
2. Set a **Secret Key** > Click **Save Password**.
3. Check **Activate** to enable auto mode.
4. Configure crontab on your server:

```
*/10 * * * * cd /path/to/wp-content/plugins/xphim-plugin/ && php -q schedule-topxx.php {secret_key}
```

> **Tip:** Crawl 10-20 pages daily to keep up with new episodes and movies. Use "Shuffle Links" to vary crawl order across different sites.

## Managing Movies

After crawling, movies appear under the **TOPXX** menu in WordPress Admin:

- **Movie List:** View, search, filter featured movies, check views/thumb/poster.
- **Add Movie:** Manually add a movie with all metadata fields.
- **Edit Movie:** Click any movie to edit. Fields include status, original title, duration, total episodes, quality, thumb & poster URLs.
- **Episodes:** Manage streaming servers and episode links (embed, m3u8) per movie.

## Taxonomies

The plugin registers custom taxonomies for movies:

| Taxonomy | Description |
|----------|-------------|
| Genres | Action, Horror, Romance, etc. |
| Countries | Vietnam, Japan, USA, etc. |
| Actors | Actor names (with avatars) |
| Directors | Director names |
| Categories | Single movie, Series, Animation, etc. |
| Tags | Related keywords |
| Years | Release year |

## API Reference

Base URL: `https://topxx.vip/api/v1`

Full API documentation: [https://topxx.vip/api](https://topxx.vip/api)

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/movies/today` | Movies released today |
| GET | `/movies/latest` | Latest updated movies (paginated) |
| GET | `/movies/{code}` | Movie details by code |
| GET | `/genres/{code}/movies` | Movies by genre |
| GET | `/countries/{code}/movies` | Movies by country |
| GET | `/actors` | List of actors |
| GET | `/genres` | List of genres |

## Support

- **Website:** [https://topxx.vip](https://topxx.vip)
- **API Docs:** [https://topxx.vip/api](https://topxx.vip/api)
- **Telegram:** [https://t.me/hotrokhophim](https://t.me/hotrokhophim)
- **GitHub:** [https://github.com/ofilmcms](https://github.com/ofilmcms)

## License

Free to use. API provided by Topxx.vip with no request limits and no registration required.
