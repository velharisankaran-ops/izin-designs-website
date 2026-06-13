<?php
/**
 * Plugin Name: IZIN Designs Landing Page
 * Description: Adds a safe shortcode for the IZIN Designs landing page without editing WordPress core, the active theme, or Elementor files.
 * Version: 1.0.0
 * Author: Velnex
 * License: GPL-2.0-or-later
 */

if (!defined('ABSPATH')) {
    exit;
}

function izin_designs_landing_shortcode() {
    $frontend_dir = plugin_dir_path(__FILE__) . 'frontend/';
    $frontend_url = plugin_dir_url(__FILE__) . 'frontend/';
    $html_path = $frontend_dir . 'index.html';

    if (!file_exists($html_path)) {
        return '<p>IZIN Designs page files are missing.</p>';
    }

    $html = file_get_contents($html_path);
    if ($html === false) {
        return '<p>IZIN Designs page could not be loaded.</p>';
    }

    $base_tag = '<base href="' . esc_url($frontend_url) . '">';
    $resize_script = <<<'HTML'
<script>
(function () {
  var sendHeight = function () {
    var height = Math.max(
      document.body ? document.body.scrollHeight : 0,
      document.documentElement ? document.documentElement.scrollHeight : 0
    );
    parent.postMessage({ type: "izinDesignsLandingHeight", height: height }, "*");
  };

  window.addEventListener("load", sendHeight);
  window.addEventListener("resize", sendHeight);

  if ("ResizeObserver" in window && document.body) {
    new ResizeObserver(sendHeight).observe(document.body);
  }

  setTimeout(sendHeight, 250);
  setTimeout(sendHeight, 1000);
})();
</script>
HTML;

    if (stripos($html, '<head>') !== false) {
        $html = preg_replace('/<head>/i', '<head>' . $base_tag, $html, 1);
    }

    if (stripos($html, '</body>') !== false) {
        $html = preg_replace('/<\/body>/i', $resize_script . '</body>', $html, 1);
    }

    $frame_id = 'izin-designs-landing-' . wp_generate_uuid4();
    $srcdoc = esc_attr($html);

    ob_start();
    ?>
    <div class="izin-designs-landing-wrap">
        <iframe
            id="<?php echo esc_attr($frame_id); ?>"
            class="izin-designs-landing-frame"
            title="IZIN Designs landing page"
            srcdoc="<?php echo $srcdoc; ?>"
            loading="lazy"
            sandbox="allow-forms allow-scripts allow-same-origin allow-top-navigation-by-user-activation"
        ></iframe>
    </div>
    <style>
      .izin-designs-landing-wrap {
        width: 100%;
        margin: 0;
        padding: 0;
      }

      .izin-designs-landing-frame {
        display: block;
        width: 100%;
        min-height: 100vh;
        border: 0;
        overflow: hidden;
      }
    </style>
    <script>
      (function () {
        var frame = document.getElementById(<?php echo wp_json_encode($frame_id); ?>);
        if (!frame) return;

        window.addEventListener("message", function (event) {
          if (!event.data || event.data.type !== "izinDesignsLandingHeight") return;
          var height = parseInt(event.data.height, 10);
          if (!Number.isFinite(height) || height < 400) return;
          frame.style.height = height + "px";
        });
      })();
    </script>
    <?php
    return ob_get_clean();
}

add_shortcode('izin_designs_landing', 'izin_designs_landing_shortcode');
