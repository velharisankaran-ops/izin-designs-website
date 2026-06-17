<?php
/**
 * Search results template.
 */

get_header();
get_template_part('template-parts/site-nav');
?>

<main class="archive-main">
  <section class="archive-shell">
    <?php izin_designs_render_breadcrumbs(); ?>
    <header class="archive-header">
      <span><?php esc_html_e('Search', 'izin-designs-theme'); ?></span>
      <h1><?php printf(esc_html__('Search Results for: %s', 'izin-designs-theme'), esc_html(get_search_query())); ?></h1>
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
      <div class="archive-empty">
        <p><?php esc_html_e('No matching articles were found. Try another search term.', 'izin-designs-theme'); ?></p>
        <?php get_search_form(); ?>
      </div>
    <?php endif; ?>
  </section>
</main>

<?php get_template_part('template-parts/site-footer'); ?>
<?php get_footer(); ?>
