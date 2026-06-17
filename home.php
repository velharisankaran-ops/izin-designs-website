<?php
/**
 * Blog index template.
 */

get_header();
get_template_part('template-parts/site-nav');
?>

<main class="archive-main">
  <section class="archive-shell">
    <?php izin_designs_render_breadcrumbs(); ?>
    <header class="archive-header">
      <span><?php esc_html_e('IZIN Insights', 'izin-designs-theme'); ?></span>
      <h1><?php esc_html_e('IZIN Insights', 'izin-designs-theme'); ?></h1>
      <div class="archive-description">
        <p><?php esc_html_e('Interior ideas, project guidance, cost considerations, material notes and planning insights from IZIN Designs Interior Studio.', 'izin-designs-theme'); ?></p>
      </div>
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
      <p class="archive-empty"><?php esc_html_e('No insight articles published yet.', 'izin-designs-theme'); ?></p>
    <?php endif; ?>
  </section>
</main>

<?php get_template_part('template-parts/site-footer'); ?>
<?php get_footer(); ?>
