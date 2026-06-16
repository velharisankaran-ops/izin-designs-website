<?php
/**
 * Shared video rail markup for the theme and shortcode plugin.
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('izin_designs_video_section_markup')) {
    function izin_designs_video_section_markup() {
        return <<<'HTML'
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

        .video-section-head small {
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
            --video-card-width: 78vw;
            gap: 14px;
            scroll-padding-inline: 16px;
          }
        }
      </style>

      <section class="shorts-section" id="videos">
        <div class="video-section-head">
          <small>Videos</small>
          <h2>Recent work and client stories</h2>
        </div>

        <div class="video-slider video-rail" aria-label="Izin Designs video slider">
          <article class="video-card">
            <div class="izin-shorts-video">
              <iframe src="https://www.youtube.com/embed/2_LoA7vSiA8" title="Izin Designs YouTube Shorts Video" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
            </div>
          </article>

          <article class="video-card">
            <div class="izin-shorts-video">
              <video controls playsinline preload="metadata" aria-label="Izin Designs Rahul Rajagopal project video">
                <source src="https://izindesigns.com/wp-content/uploads/2026/06/Rahul-Rajagopal.mp4" type="video/mp4">
              </video>
            </div>
          </article>

          <article class="video-card">
            <div class="izin-shorts-video">
              <video controls playsinline preload="metadata" aria-label="Izin Designs Jayasurya and his wife client testimonial">
                <source src="https://izindesigns.com/wp-content/uploads/2026/06/Jayasurya-and-his-wife.mp4" type="video/mp4">
              </video>
            </div>
          </article>

          <article class="video-card">
            <div class="izin-shorts-video">
              <video controls playsinline preload="metadata" aria-label="Izin Designs Lakshmi new flat client testimonial">
                <source src="https://izindesigns.com/wp-content/uploads/2026/06/Lakshmis-new-flat.mp4" type="video/mp4">
              </video>
            </div>
          </article>
        </div>
      </section>
HTML;
    }
}

if (!function_exists('izin_designs_inject_video_section')) {
    function izin_designs_inject_video_section($html) {
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
