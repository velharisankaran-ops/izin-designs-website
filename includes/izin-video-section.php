<?php
/**
 * Shared video rail markup and metadata for the theme and shortcode plugin.
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('izin_designs_homepage_videos')) {
    function izin_designs_homepage_videos() {
        return array(
            array(
                'slug'          => 'izin-designs-youtube-short',
                'title'         => 'Izin Designs Featured Short',
                'kicker'        => 'Featured Video',
                'description'   => 'A featured Izin Designs short showcasing recent interior work and studio style.',
                'type'          => 'youtube',
                'embed_url'     => 'https://www.youtube.com/embed/2_LoA7vSiA8',
                'watch_url'     => 'https://www.youtube.com/watch?v=2_LoA7vSiA8',
                'thumbnail_url' => 'https://i.ytimg.com/vi/2_LoA7vSiA8/hqdefault.jpg',
            ),
            array(
                'slug'          => 'rahul-rajagopal-project-video',
                'title'         => 'Rahul Rajagopal Project Video',
                'kicker'        => 'Project Video',
                'description'   => 'A project walkthrough video from Izin Designs featuring recent residential interior work.',
                'type'          => 'mp4',
                'content_url'   => 'https://izindesigns.com/wp-content/uploads/2026/06/Rahul-Rajagopal.mp4',
                'watch_url'     => 'https://izindesigns.com/wp-content/uploads/2026/06/Rahul-Rajagopal.mp4',
            ),
            array(
                'slug'          => 'jayasurya-client-testimonial',
                'title'         => 'Jayasurya Client Testimonial',
                'kicker'        => 'Client Testimonial',
                'description'   => 'A client testimonial video from Jayasurya about the Izin Designs interior experience.',
                'type'          => 'mp4',
                'content_url'   => 'https://izindesigns.com/wp-content/uploads/2026/06/Jayasurya-and-his-wife.mp4',
                'watch_url'     => 'https://izindesigns.com/wp-content/uploads/2026/06/Jayasurya-and-his-wife.mp4',
            ),
            array(
                'slug'          => 'lakshmi-flat-testimonial',
                'title'         => 'Lakshmi New Flat Testimonial',
                'kicker'        => 'Client Testimonial',
                'description'   => 'A testimonial video sharing the Izin Designs experience for Lakshmi\'s new flat.',
                'type'          => 'mp4',
                'content_url'   => 'https://izindesigns.com/wp-content/uploads/2026/06/Lakshmis-new-flat.mp4',
                'watch_url'     => 'https://izindesigns.com/wp-content/uploads/2026/06/Lakshmis-new-flat.mp4',
            ),
        );
    }
}

if (!function_exists('izin_designs_video_section_markup')) {
    function izin_designs_video_section_markup() {
        $videos = izin_designs_homepage_videos();

        ob_start();
        ?>
      <style id="izin-video-rail-critical-css">
        .shorts-section {
          padding: 42px 0;
          background: #ffffff;
        }

        .video-section-head {
          max-width: 1240px;
          margin: 0 auto 18px;
          padding: 0 24px;
        }

        .video-section-head small,
        .izin-video-meta small {
          display: block;
          margin-bottom: 8px;
          font-size: 10px;
          font-weight: 500;
          letter-spacing: 0.07em;
          text-transform: uppercase;
          color: #c24c7a;
        }

        .video-section-head h2 {
          margin: 0;
          font-family: Georgia, "Times New Roman", serif;
          font-size: clamp(28px, 4vw, 44px);
          line-height: 1.05;
          font-weight: 400;
          color: #171717;
        }

        .video-slider {
          --video-card-width: 340px;
          max-width: 1240px;
          margin: 0 auto;
          padding: 0 24px 16px;
          display: flex;
          gap: 18px;
          overflow-x: auto;
          overflow-y: hidden;
          scroll-snap-type: x mandatory;
          scroll-padding-inline: 24px;
          scrollbar-gutter: stable;
          overscroll-behavior-inline: contain;
          -webkit-overflow-scrolling: touch;
        }

        .video-card {
          flex: 0 0 var(--video-card-width);
          max-width: var(--video-card-width);
          scroll-snap-align: start;
        }

        .izin-shorts-video {
          width: 100%;
          aspect-ratio: 9 / 16;
          border-radius: 18px;
          overflow: hidden;
          box-shadow: 0 18px 45px rgba(0, 0, 0, 0.18);
          background: #000000;
        }

        .izin-shorts-video iframe,
        .izin-shorts-video video {
          width: 100%;
          height: 100%;
          display: block;
          object-fit: cover;
          border: 0;
        }

        .izin-video-meta {
          padding-top: 12px;
        }

        .izin-video-meta h3 {
          margin: 0;
          font-size: 16px;
          line-height: 1.35;
          font-weight: 500;
          color: #171717;
        }

        .izin-video-meta p {
          margin: 8px 0 0;
          font-size: 13px;
          line-height: 1.6;
          color: #606060;
        }

        .izin-video-meta a {
          display: inline-flex;
          margin-top: 10px;
          font-size: 12px;
          line-height: 1.4;
          color: #171717;
          text-decoration: underline;
          text-underline-offset: 3px;
        }

        @media (max-width: 767px) {
          .shorts-section {
            padding: 34px 0;
          }

          .video-section-head,
          .video-slider {
            padding-left: 16px;
            padding-right: 16px;
          }

          .video-slider {
            --video-card-width: 60vw;
            gap: 14px;
            scroll-padding-inline: 16px;
          }

          .izin-video-meta h3 {
            font-size: 14px;
          }

          .izin-video-meta p,
          .izin-video-meta a {
            display: none;
          }
        }
      </style>

      <section class="shorts-section" id="videos">
        <div class="video-section-head">
          <small>Videos</small>
          <h2>Recent work and client stories</h2>
        </div>

        <div class="video-slider video-rail" aria-label="Izin Designs video slider">
          <?php foreach ($videos as $video) : ?>
            <article class="video-card" id="<?php echo esc_attr($video['slug']); ?>">
              <div class="izin-shorts-video">
                <?php if ($video['type'] === 'youtube') : ?>
                  <iframe
                    src="<?php echo esc_url($video['embed_url']); ?>"
                    title="<?php echo esc_attr($video['title']); ?>"
                    loading="lazy"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                    allowfullscreen
                  ></iframe>
                <?php else : ?>
                  <video
                    controls
                    playsinline
                    preload="metadata"
                    aria-label="<?php echo esc_attr($video['title']); ?>"
                  >
                    <source src="<?php echo esc_url($video['content_url']); ?>" type="video/mp4">
                  </video>
                <?php endif; ?>
              </div>

              <div class="izin-video-meta">
                <small><?php echo esc_html($video['kicker']); ?></small>
                <h3><?php echo esc_html($video['title']); ?></h3>
                <p><?php echo esc_html($video['description']); ?></p>
                <a href="<?php echo esc_url($video['watch_url']); ?>">
                  <?php echo $video['type'] === 'youtube' ? esc_html__('Watch on YouTube', 'izin-designs-theme') : esc_html__('Open video file', 'izin-designs-theme'); ?>
                </a>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
      </section>
        <?php

        return (string) ob_get_clean();
    }
}

if (!function_exists('izin_designs_inject_video_section')) {
    function izin_designs_inject_video_section($html) {
        $html = preg_replace('/\s*<section class="izin-package-section" id="packages">.*?<\/section>\s*/is', "\n", $html, 1);
        $video_section = izin_designs_video_section_markup();
        $html = preg_replace('/\s*<section class="shorts-section" id="videos">.*?<\/section>\s*/is', "\n" . $video_section . "\n", $html, 1);

        if (strpos($html, 'id="videos"') !== false) {
            return $html;
        }

        if (strpos($html, '<section class="izin-solutions-section"') !== false) {
            return str_replace('<section class="izin-solutions-section"', $video_section . "\n\n      " . '<section class="izin-solutions-section"', $html);
        }

        return $html . "\n" . $video_section;
    }
}
