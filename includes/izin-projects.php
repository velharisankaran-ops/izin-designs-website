<?php
/**
 * Project bid request storage, REST endpoint, uploads, and WordPress admin screen.
 */

if (!defined('ABSPATH')) {
    exit;
}

if (defined('IZIN_PROJECTS_LOADED')) {
    return;
}

define('IZIN_PROJECTS_LOADED', true);

function izin_projects_table_name() {
    global $wpdb;
    return $wpdb->prefix . 'izin_projects';
}

function izin_projects_install() {
    global $wpdb;

    $table_name = izin_projects_table_name();
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE {$table_name} (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        client_name VARCHAR(190) NOT NULL,
        phone VARCHAR(80) NOT NULL,
        email VARCHAR(190) DEFAULT '' NOT NULL,
        project_type VARCHAR(190) DEFAULT '' NOT NULL,
        location VARCHAR(190) DEFAULT '' NOT NULL,
        budget_range VARCHAR(190) DEFAULT '' NOT NULL,
        property_size_sqft VARCHAR(190) DEFAULT '' NOT NULL,
        expected_start_date VARCHAR(120) DEFAULT '' NOT NULL,
        project_description LONGTEXT NULL,
        attachment_urls LONGTEXT NULL,
        attachment_names LONGTEXT NULL,
        status VARCHAR(80) DEFAULT 'New' NOT NULL,
        source_url TEXT NULL,
        referrer TEXT NULL,
        ip_address VARCHAR(100) DEFAULT '' NOT NULL,
        device_type VARCHAR(80) DEFAULT '' NOT NULL,
        browser VARCHAR(120) DEFAULT '' NOT NULL,
        operating_system VARCHAR(120) DEFAULT '' NOT NULL,
        language VARCHAR(80) DEFAULT '' NOT NULL,
        screen_size VARCHAR(80) DEFAULT '' NOT NULL,
        viewport_size VARCHAR(80) DEFAULT '' NOT NULL,
        timezone VARCHAR(120) DEFAULT '' NOT NULL,
        cookies_enabled TINYINT(1) DEFAULT 0 NOT NULL,
        utm_source VARCHAR(190) DEFAULT '' NOT NULL,
        utm_medium VARCHAR(190) DEFAULT '' NOT NULL,
        utm_campaign VARCHAR(190) DEFAULT '' NOT NULL,
        utm_content VARCHAR(190) DEFAULT '' NOT NULL,
        utm_term VARCHAR(190) DEFAULT '' NOT NULL,
        user_agent LONGTEXT NULL,
        created_at DATETIME NOT NULL,
        PRIMARY KEY  (id),
        KEY created_at (created_at),
        KEY status (status)
    ) {$charset_collate};";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
}

function izin_projects_register_rest_routes() {
    register_rest_route('izin-projects/v1', '/submit', array(
        'methods'             => 'POST',
        'callback'            => 'izin_projects_submit_rest',
        'permission_callback' => '__return_true',
    ));
}
add_action('rest_api_init', 'izin_projects_register_rest_routes');

function izin_projects_get_ip_address() {
    if (function_exists('izin_leads_get_ip_address')) {
        return izin_leads_get_ip_address();
    }

    return '';
}

function izin_projects_detect_device_type($user_agent) {
    if (function_exists('izin_leads_detect_device_type')) {
        return izin_leads_detect_device_type($user_agent);
    }

    return '';
}

function izin_projects_detect_browser($user_agent) {
    if (function_exists('izin_leads_detect_browser')) {
        return izin_leads_detect_browser($user_agent);
    }

    return '';
}

function izin_projects_detect_operating_system($user_agent) {
    if (function_exists('izin_leads_detect_operating_system')) {
        return izin_leads_detect_operating_system($user_agent);
    }

    return '';
}

function izin_projects_allowed_mimes() {
    return array(
        'jpg'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png'  => 'image/png',
        'webp' => 'image/webp',
        'pdf'  => 'application/pdf',
        'doc'  => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    );
}

function izin_projects_normalize_files_array($field) {
    if (empty($_FILES[$field])) {
        return array();
    }

    $files = $_FILES[$field];

    if (!is_array($files['name'])) {
        return array($files);
    }

    $normalized = array();
    $count = count($files['name']);

    for ($index = 0; $index < $count; $index++) {
        if (empty($files['name'][$index])) {
            continue;
        }

        $normalized[] = array(
            'name'     => $files['name'][$index],
            'type'     => $files['type'][$index] ?? '',
            'tmp_name' => $files['tmp_name'][$index] ?? '',
            'error'    => $files['error'][$index] ?? UPLOAD_ERR_NO_FILE,
            'size'     => $files['size'][$index] ?? 0,
        );
    }

    return $normalized;
}

function izin_projects_handle_attachments() {
    $files = izin_projects_normalize_files_array('attachments');

    if (empty($files)) {
        return array();
    }

    if (count($files) > 5) {
        return new WP_Error('izin_project_file_count', __('You can upload up to 5 files.', 'izin-designs-theme'), array('status' => 400));
    }

    $allowed_mimes = izin_projects_allowed_mimes();

    require_once ABSPATH . 'wp-admin/includes/file.php';

    $uploaded = array();

    foreach ($files as $file) {
        if (!empty($file['error']) && (int) $file['error'] !== UPLOAD_ERR_OK) {
            return new WP_Error('izin_project_file_error', __('One of the uploaded files could not be processed.', 'izin-designs-theme'), array('status' => 400));
        }

        $file_type = wp_check_filetype_and_ext($file['tmp_name'], $file['name'], $allowed_mimes);

        if (empty($file_type['ext']) || empty($file_type['type'])) {
            return new WP_Error('izin_project_file_type', __('Only JPG, JPEG, PNG, WEBP, PDF, DOC, and DOCX files are allowed.', 'izin-designs-theme'), array('status' => 400));
        }

        if (!empty($file['size']) && (int) $file['size'] > 8 * 1024 * 1024) {
            return new WP_Error('izin_project_file_size', __('Each file must be 8 MB or smaller.', 'izin-designs-theme'), array('status' => 400));
        }

        $upload = wp_handle_upload($file, array(
            'test_form' => false,
            'mimes'     => $allowed_mimes,
        ));

        if (!empty($upload['error'])) {
            return new WP_Error('izin_project_file_upload', sanitize_text_field($upload['error']), array('status' => 500));
        }

        $uploaded[] = array(
            'url'  => esc_url_raw($upload['url'] ?? ''),
            'name' => sanitize_file_name($file['name']),
            'type' => sanitize_text_field($upload['type'] ?? ''),
        );
    }

    return $uploaded;
}

function izin_projects_notify_admin($project_id, $project_data, $attachments) {
    $admin_email = get_option('admin_email');

    if (!is_email($admin_email)) {
        return;
    }

    $admin_url = admin_url('admin.php?page=izin-projects');
    $attachment_lines = array();

    foreach ($attachments as $attachment) {
        $attachment_lines[] = sprintf(
            '%s - %s',
            $attachment['name'],
            $attachment['url']
        );
    }

    $message_lines = array(
        'A new project quotation request was submitted on the website.',
        '',
        'Submission ID: ' . (int) $project_id,
        'Client Name: ' . $project_data['client_name'],
        'Phone: ' . $project_data['phone'],
        'Email: ' . $project_data['email'],
        'Project Type: ' . $project_data['project_type'],
        'Location: ' . $project_data['location'],
        'Budget Range: ' . $project_data['budget_range'],
        'Property Size (Sq.Ft): ' . $project_data['property_size_sqft'],
        'Expected Start Date: ' . $project_data['expected_start_date'],
        'Status: ' . $project_data['status'],
        '',
        'Project Description:',
        $project_data['project_description'],
        '',
        'Attachments:',
        empty($attachment_lines) ? 'No files uploaded.' : implode("\n", $attachment_lines),
        '',
        'Admin inbox: ' . $admin_url,
    );

    wp_mail(
        $admin_email,
        sprintf(__('New Bid Project Request #%d', 'izin-designs-theme'), (int) $project_id),
        implode("\n", $message_lines)
    );
}

function izin_projects_submit_rest(WP_REST_Request $request) {
    global $wpdb;

    if (sanitize_text_field($request->get_param('website')) !== '') {
        return new WP_REST_Response(array(
            'success' => true,
            'message' => 'Ignored.',
        ), 200);
    }

    $client_name = sanitize_text_field($request->get_param('client_name'));
    $phone = sanitize_text_field($request->get_param('phone'));
    $email = sanitize_email($request->get_param('email'));
    $project_type = sanitize_text_field($request->get_param('project_type'));
    $location = sanitize_text_field($request->get_param('location'));
    $budget_range = sanitize_text_field($request->get_param('budget_range'));
    $property_size_sqft = sanitize_text_field($request->get_param('property_size_sqft'));
    $expected_start_date = sanitize_text_field($request->get_param('expected_start_date'));
    $project_description = sanitize_textarea_field($request->get_param('project_description'));

    if (
        $client_name === '' ||
        $phone === '' ||
        $email === '' ||
        $project_type === '' ||
        $location === '' ||
        $budget_range === '' ||
        $property_size_sqft === '' ||
        $expected_start_date === '' ||
        $project_description === ''
    ) {
        return new WP_REST_Response(array(
            'success' => false,
            'message' => 'All required fields must be completed.',
        ), 400);
    }

    if (!is_email($email)) {
        return new WP_REST_Response(array(
            'success' => false,
            'message' => 'Invalid email address.',
        ), 400);
    }

    $attachments = izin_projects_handle_attachments();

    if (is_wp_error($attachments)) {
        return new WP_REST_Response(array(
            'success' => false,
            'message' => $attachments->get_error_message(),
        ), (int) ($attachments->get_error_data()['status'] ?? 400));
    }

    izin_projects_install();

    $user_agent = sanitize_textarea_field(wp_unslash($_SERVER['HTTP_USER_AGENT'] ?? ''));
    $cookies_enabled = sanitize_text_field($request->get_param('cookies_enabled')) === '1' ? 1 : 0;

    $project_data = array(
        'client_name'         => $client_name,
        'phone'               => $phone,
        'email'               => $email,
        'project_type'        => $project_type,
        'location'            => $location,
        'budget_range'        => $budget_range,
        'property_size_sqft'  => $property_size_sqft,
        'expected_start_date' => $expected_start_date,
        'project_description' => $project_description,
        'attachment_urls'     => wp_json_encode(wp_list_pluck($attachments, 'url')),
        'attachment_names'    => wp_json_encode(wp_list_pluck($attachments, 'name')),
        'status'              => 'New',
        'source_url'          => esc_url_raw($request->get_param('source_url')),
        'referrer'            => esc_url_raw($request->get_param('referrer')),
        'ip_address'          => izin_projects_get_ip_address(),
        'device_type'         => izin_projects_detect_device_type($user_agent),
        'browser'             => izin_projects_detect_browser($user_agent),
        'operating_system'    => izin_projects_detect_operating_system($user_agent),
        'language'            => sanitize_text_field($request->get_param('language')),
        'screen_size'         => sanitize_text_field($request->get_param('screen_size')),
        'viewport_size'       => sanitize_text_field($request->get_param('viewport_size')),
        'timezone'            => sanitize_text_field($request->get_param('timezone')),
        'cookies_enabled'     => $cookies_enabled,
        'utm_source'          => sanitize_text_field($request->get_param('utm_source')),
        'utm_medium'          => sanitize_text_field($request->get_param('utm_medium')),
        'utm_campaign'        => sanitize_text_field($request->get_param('utm_campaign')),
        'utm_content'         => sanitize_text_field($request->get_param('utm_content')),
        'utm_term'            => sanitize_text_field($request->get_param('utm_term')),
        'user_agent'          => $user_agent,
        'created_at'          => current_time('mysql'),
    );

    $inserted = $wpdb->insert(
        izin_projects_table_name(),
        $project_data,
        array(
            '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s',
            '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%s', '%s',
            '%s', '%s', '%s', '%s', '%s', '%s',
        )
    );

    if (!$inserted) {
        return new WP_REST_Response(array(
            'success' => false,
            'message' => 'Project request could not be saved.',
        ), 500);
    }

    $project_id = (int) $wpdb->insert_id;
    izin_projects_notify_admin($project_id, $project_data, $attachments);

    return new WP_REST_Response(array(
        'success'    => true,
        'project_id' => $project_id,
    ), 201);
}

function izin_projects_admin_menu() {
    add_menu_page(
        __('Izin Projects', 'izin-designs-theme'),
        __('Izin Projects', 'izin-designs-theme'),
        'manage_options',
        'izin-projects',
        'izin_projects_admin_page',
        'dashicons-portfolio',
        28
    );
}
add_action('admin_menu', 'izin_projects_admin_menu');

function izin_projects_delete_admin_action() {
    if (!current_user_can('manage_options')) {
        wp_die(esc_html__('You do not have permission to delete project requests.', 'izin-designs-theme'));
    }

    $project_id = absint($_GET['project_id'] ?? 0);

    if ($project_id <= 0) {
        wp_safe_redirect(admin_url('admin.php?page=izin-projects'));
        exit;
    }

    check_admin_referer('izin_delete_project_' . $project_id);

    global $wpdb;
    izin_projects_install();
    $wpdb->delete(izin_projects_table_name(), array('id' => $project_id), array('%d'));

    wp_safe_redirect(add_query_arg('izin_project_deleted', '1', admin_url('admin.php?page=izin-projects')));
    exit;
}
add_action('admin_post_izin_delete_project', 'izin_projects_delete_admin_action');

function izin_projects_decode_json_list($value) {
    $items = json_decode((string) $value, true);
    return is_array($items) ? $items : array();
}

function izin_projects_admin_page() {
    global $wpdb;

    izin_projects_install();

    $projects = $wpdb->get_results('SELECT * FROM ' . izin_projects_table_name() . ' ORDER BY created_at DESC, id DESC LIMIT 200');
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Izin Projects', 'izin-designs-theme'); ?></h1>
        <p><?php esc_html_e('Latest quotation and bid requests submitted from the website.', 'izin-designs-theme'); ?></p>

        <?php if (!empty($_GET['izin_project_deleted'])): ?>
            <div class="notice notice-success is-dismissible">
                <p><?php esc_html_e('Project request deleted.', 'izin-designs-theme'); ?></p>
            </div>
        <?php endif; ?>

        <style>
            .izin-delete-project {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 28px;
                height: 28px;
                color: #b32d2e;
                text-decoration: none;
            }

            .izin-delete-project:hover,
            .izin-delete-project:focus {
                color: #8a2424;
            }
        </style>

        <table class="widefat striped">
            <thead>
                <tr>
                    <th style="width: 42px;"><span class="screen-reader-text"><?php esc_html_e('Actions', 'izin-designs-theme'); ?></span></th>
                    <th><?php esc_html_e('Date', 'izin-designs-theme'); ?></th>
                    <th><?php esc_html_e('Client', 'izin-designs-theme'); ?></th>
                    <th><?php esc_html_e('Phone', 'izin-designs-theme'); ?></th>
                    <th><?php esc_html_e('Email', 'izin-designs-theme'); ?></th>
                    <th><?php esc_html_e('Project Type', 'izin-designs-theme'); ?></th>
                    <th><?php esc_html_e('Budget', 'izin-designs-theme'); ?></th>
                    <th><?php esc_html_e('Location', 'izin-designs-theme'); ?></th>
                    <th><?php esc_html_e('Start Date', 'izin-designs-theme'); ?></th>
                    <th><?php esc_html_e('Status', 'izin-designs-theme'); ?></th>
                    <th><?php esc_html_e('Details', 'izin-designs-theme'); ?></th>
                    <th><?php esc_html_e('Attachments', 'izin-designs-theme'); ?></th>
                    <th><?php esc_html_e('Tracking', 'izin-designs-theme'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($projects)): ?>
                    <tr>
                        <td colspan="13"><?php esc_html_e('No project requests found yet.', 'izin-designs-theme'); ?></td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($projects as $project): ?>
                        <?php
                        $attachment_urls = izin_projects_decode_json_list($project->attachment_urls);
                        $attachment_names = izin_projects_decode_json_list($project->attachment_names);
                        ?>
                        <tr>
                            <td>
                                <a
                                    class="izin-delete-project"
                                    href="<?php echo esc_url(wp_nonce_url(admin_url('admin-post.php?action=izin_delete_project&project_id=' . absint($project->id)), 'izin_delete_project_' . absint($project->id))); ?>"
                                    onclick="return confirm('<?php echo esc_js(__('Delete this project request?', 'izin-designs-theme')); ?>');"
                                    title="<?php esc_attr_e('Delete project request', 'izin-designs-theme'); ?>"
                                    aria-label="<?php esc_attr_e('Delete project request', 'izin-designs-theme'); ?>"
                                >
                                    <span class="dashicons dashicons-trash" aria-hidden="true"></span>
                                </a>
                            </td>
                            <td><?php echo esc_html($project->created_at); ?></td>
                            <td><?php echo esc_html($project->client_name); ?></td>
                            <td><a href="<?php echo esc_url('tel:' . preg_replace('/[^0-9+]/', '', $project->phone)); ?>"><?php echo esc_html($project->phone); ?></a></td>
                            <td><a href="<?php echo esc_url('mailto:' . $project->email); ?>"><?php echo esc_html($project->email); ?></a></td>
                            <td><?php echo esc_html($project->project_type); ?></td>
                            <td><?php echo esc_html($project->budget_range); ?></td>
                            <td><?php echo esc_html($project->location); ?></td>
                            <td><?php echo esc_html($project->expected_start_date); ?></td>
                            <td><?php echo esc_html($project->status); ?></td>
                            <td>
                                <strong><?php esc_html_e('Sq.Ft:', 'izin-designs-theme'); ?></strong> <?php echo esc_html($project->property_size_sqft); ?><br>
                                <small><?php echo nl2br(esc_html($project->project_description)); ?></small>
                            </td>
                            <td>
                                <?php if (empty($attachment_urls)): ?>
                                    <small><?php esc_html_e('No files', 'izin-designs-theme'); ?></small>
                                <?php else: ?>
                                    <?php foreach ($attachment_urls as $index => $attachment_url): ?>
                                        <a href="<?php echo esc_url($attachment_url); ?>" target="_blank" rel="noopener noreferrer">
                                            <?php echo esc_html($attachment_names[$index] ?? sprintf(__('Attachment %d', 'izin-designs-theme'), $index + 1)); ?>
                                        </a><br>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <small>
                                    <?php echo esc_html(trim($project->device_type . ' / ' . $project->browser . ' / ' . $project->operating_system, ' /')); ?><br>
                                    <?php if (!empty($project->source_url)): ?>
                                        <a href="<?php echo esc_url($project->source_url); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e('Page', 'izin-designs-theme'); ?></a><br>
                                    <?php endif; ?>
                                    <?php if (!empty($project->referrer)): ?>
                                        <a href="<?php echo esc_url($project->referrer); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e('Referrer', 'izin-designs-theme'); ?></a><br>
                                    <?php endif; ?>
                                    <?php if (!empty($project->ip_address)): ?>
                                        <?php echo esc_html('IP: ' . $project->ip_address); ?><br>
                                    <?php endif; ?>
                                    <?php if (!empty($project->language)): ?>
                                        <?php echo esc_html('Lang: ' . $project->language); ?><br>
                                    <?php endif; ?>
                                    <?php if (!empty($project->timezone)): ?>
                                        <?php echo esc_html('TZ: ' . $project->timezone); ?><br>
                                    <?php endif; ?>
                                    <?php echo esc_html('Cookies: ' . (!empty($project->cookies_enabled) ? 'Enabled' : 'Disabled')); ?><br>
                                    <?php if (!empty($project->screen_size) || !empty($project->viewport_size)): ?>
                                        <?php
                                        $screen_summary = trim($project->screen_size);
                                        if (!empty($project->viewport_size)) {
                                            $screen_summary .= ($screen_summary !== '' ? ' viewport ' : 'viewport ') . $project->viewport_size;
                                        }
                                        ?>
                                        <?php echo esc_html('Screen: ' . $screen_summary); ?><br>
                                    <?php endif; ?>
                                    <?php if (!empty($project->utm_source) || !empty($project->utm_medium) || !empty($project->utm_campaign)): ?>
                                        <?php echo esc_html('UTM: ' . trim($project->utm_source . ' / ' . $project->utm_medium . ' / ' . $project->utm_campaign, ' /')); ?>
                                    <?php endif; ?>
                                </small>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php
}
