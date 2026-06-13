<?php
/**
 * Front page template.
 */

get_header();
?>

<?php
$frontend_path = get_template_directory() . '/frontend/index.html';

if (file_exists($frontend_path)) {
    $html = file_get_contents($frontend_path);

    if ($html !== false) {
        $body = $html;

        if (preg_match('/<body[^>]*>(.*)<\/body>/is', $html, $matches)) {
            $body = $matches[1];
        }

        $body = str_replace('<link rel="stylesheet" href="styles.css">', '', $body);
        $body = str_replace('<script src="script.js"></script>', '', $body);
        $body = str_replace('href="frontend/', 'href="' . esc_url(get_template_directory_uri() . '/frontend/'), $body);
        $body = str_replace('src="frontend/', 'src="' . esc_url(get_template_directory_uri() . '/frontend/'), $body);

        echo $body; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }
} else {
    echo '<main><p>IZIN Designs theme frontend file is missing.</p></main>';
}
?>

<?php
get_footer();
