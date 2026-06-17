<?php
/**
 * Archive template.
 */

get_header();
get_template_part('template-parts/site-nav');
?>

<main class="archive-main">
  <section class="archive-shell">
    <?php izin_designs_render_breadcrumbs(); ?>
    <header class="archive-header">
      <span><?php esc_html_e('IZIN Insights', 'izin-designs-theme'); ?></span>
      <h1><?php the_archive_title(); ?></h1>
      <?php if (get_the_archive_description()) : ?>
        <div class="archive-description"><?php echo wp_kses_post(wpautop(get_the_archive_description())); ?></div>
      <?php endif; ?>
    </header>

    <?php if (have_posts()) : ?>
      <div class="post-grid">
        <?php while (have_posts()) : the_post(); ?>
          <?php get_template_part('template-parts/content', 'card'); ?>
        <?php endwhile; ?>
      </div>

      <div class="archive-pagination">
        <?php the_posts_pagination(); ?>
      </div>
    <?php else : ?>
      <p class="archive-empty"><?php esc_html_e('No articles found yet.', 'izin-designs-theme'); ?></p>
    <?php endif; ?>
  </section>
</main>

<?php get_template_part('template-parts/site-footer'); ?>
<?php get_footer(); ?>
