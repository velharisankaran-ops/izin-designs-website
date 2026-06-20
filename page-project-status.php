<?php
/**
 * Project status page template.
 */

get_header();
get_template_part('template-parts/site-nav');

$token = sanitize_text_field(wp_unslash($_GET['token'] ?? ''));
$project = $token !== '' && function_exists('izin_projects_get_project_by_token') ? izin_projects_get_project_by_token($token) : null;
$has_access = $project && !empty($project->status_token);

if (!$has_access) {
    global $wp_query;
    $wp_query->set_404();
    status_header(404);
    nocache_headers();
}
?>

<main class="project-status-page">
  <section class="project-status-shell">
    <?php if (!$has_access) : ?>
      <div class="project-status-card">
        <span class="izin-small-label">Project Status</span>
        <h1>Secure status link not available.</h1>
        <p>This project status link is missing, invalid, or no longer active. Contact IZIN Designs if you need a fresh access link.</p>
      </div>
    <?php else : ?>
      <div class="project-status-card">
        <span class="izin-small-label">Project Status</span>
        <h1><?php echo esc_html($project->status); ?></h1>
        <p>Shared by IZIN Designs for your project request. This page shows only the current status update and quotation files prepared for you.</p>

        <div class="project-status-summary">
          <article>
            <h2>Last Updated</h2>
            <p><?php echo esc_html($project->status_updated_at ?: $project->created_at); ?></p>
          </article>
          <article>
            <h2>Quotation Files</h2>
            <p><?php echo empty(izin_projects_decode_json_list($project->quotation_urls)) ? esc_html__('Not shared yet', 'izin-designs-theme') : esc_html__('Available below', 'izin-designs-theme'); ?></p>
          </article>
        </div>

        <?php if (!empty($project->client_notes)) : ?>
          <div class="project-status-note">
            <h2>Note from the team</h2>
            <p><?php echo nl2br(esc_html($project->client_notes)); ?></p>
          </div>
        <?php endif; ?>

        <div class="project-status-files">
          <h2>Quotation & Proposal Files</h2>
          <?php $quotation_urls = izin_projects_decode_json_list($project->quotation_urls); ?>
          <?php $quotation_names = izin_projects_decode_json_list($project->quotation_names); ?>

          <?php if (empty($quotation_urls)) : ?>
            <p>No quotation files have been shared yet. The IZIN team will update this page when documents are ready.</p>
          <?php else : ?>
            <div class="project-status-file-list">
              <?php foreach ($quotation_urls as $index => $url) : ?>
                <a class="project-status-file" href="<?php echo esc_url($url); ?>" target="_blank" rel="noopener noreferrer">
                  <strong><?php echo esc_html($quotation_names[$index] ?? sprintf(__('Quotation File %d', 'izin-designs-theme'), $index + 1)); ?></strong>
                  <span><?php esc_html_e('Open document', 'izin-designs-theme'); ?></span>
                </a>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
      </div>
    <?php endif; ?>
  </section>
</main>

<?php get_template_part('template-parts/site-footer'); ?>
<?php get_footer(); ?>
