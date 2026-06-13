<?php
/**
 * Default theme template.
 */

get_header();
?>

<main class="izin-wp-page">
  <?php
  if (have_posts()) {
      while (have_posts()) {
          the_post();
          the_content();
      }
  } else {
      get_template_part('front-page');
  }
  ?>
</main>

<?php
get_footer();
