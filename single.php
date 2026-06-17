<?php
/**
 * Single post template.
 */

get_header();
get_template_part('template-parts/site-nav');
?>

<main class="post-page">
  <?php while (have_posts()) : the_post(); ?>
    <?php
    $featured_id = get_post_thumbnail_id();
    $featured_alt = $featured_id ? izin_designs_get_post_image_alt($featured_id, get_the_title()) : '';
    $categories = get_the_category();
    ?>
    <article <?php post_class('post-article'); ?>>
      <div class="post-shell">
        <?php izin_designs_render_breadcrumbs(); ?>

        <header class="post-header">
          <?php if (!empty($categories)) : ?>
            <a class="post-category" href="<?php echo esc_url(get_category_link($categories[0]->term_id)); ?>">
              <?php echo esc_html($categories[0]->name); ?>
            </a>
          <?php endif; ?>

          <h1><?php the_title(); ?></h1>

          <div class="post-meta">
            <span><?php echo esc_html(get_the_date()); ?></span>
            <span><?php echo esc_html__('Updated', 'izin-designs-theme'); ?> <?php echo esc_html(get_the_modified_date()); ?></span>
            <span><?php echo esc_html(get_the_author()); ?></span>
          </div>
        </header>

        <?php if (has_post_thumbnail()) : ?>
          <figure class="post-featured-image">
            <?php echo wp_get_attachment_image($featured_id, 'izin-featured-large', false, array('loading' => 'eager', 'alt' => $featured_alt)); ?>
          </figure>
        <?php endif; ?>

        <section class="post-content">
          <?php the_content(); ?>
          <?php wp_link_pages(); ?>
        </section>

        <section class="post-author-box">
          <strong><?php echo esc_html(get_the_author()); ?></strong>
          <?php if (get_the_author_meta('description')) : ?>
            <p><?php echo esc_html(get_the_author_meta('description')); ?></p>
          <?php else : ?>
            <p><?php esc_html_e('Author at IZIN Insights covering interiors, design decisions, materials and project planning.', 'izin-designs-theme'); ?></p>
          <?php endif; ?>
        </section>

        <footer class="post-footer">
          <?php the_tags('<div class="post-tags">', '', '</div>'); ?>

          <nav class="post-pagination" aria-label="<?php esc_attr_e('Post navigation', 'izin-designs-theme'); ?>">
            <div><?php previous_post_link('%link', esc_html__('Previous Post', 'izin-designs-theme')); ?></div>
            <div><?php next_post_link('%link', esc_html__('Next Post', 'izin-designs-theme')); ?></div>
          </nav>
        </footer>
      </div>
    </article>

    <?php
    $related_args = array(
        'post_type' => 'post',
        'posts_per_page' => 3,
        'post__not_in' => array(get_the_ID()),
        'ignore_sticky_posts' => true,
    );

    if (!empty($categories)) {
        $related_args['category__in'] = wp_list_pluck($categories, 'term_id');
    }

    $related_posts = new WP_Query($related_args);
    ?>

    <?php if ($related_posts->have_posts()) : ?>
      <section class="post-related">
        <div class="post-shell">
          <div class="section-heading">
            <span><?php esc_html_e('IZIN Insights', 'izin-designs-theme'); ?></span>
            <h2><?php esc_html_e('Related Articles', 'izin-designs-theme'); ?></h2>
          </div>
          <div class="post-grid">
            <?php while ($related_posts->have_posts()) : $related_posts->the_post(); ?>
              <?php get_template_part('template-parts/content', 'card'); ?>
            <?php endwhile; ?>
          </div>
        </div>
      </section>
      <?php wp_reset_postdata(); ?>
    <?php endif; ?>
  <?php endwhile; ?>
</main>

<?php get_template_part('template-parts/site-footer'); ?>
<?php get_footer(); ?>
