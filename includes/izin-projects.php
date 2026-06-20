<?php
/**
 * Project bid request storage, admin pipeline, status sharing, and token page helpers.
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

function izin_projects_statuses() {
    return array(
        'New',
        'Under Review',
        'Site Visit Scheduled',
        'Quotation Sent',
        'Closed',
    );
}

function izin_projects_status_descriptions() {
    return array(
        'New'                  => 'Your request has been received and queued for review.',
        'Under Review'         => 'The team is reviewing your scope, size, and budget requirements.',
        'Site Visit Scheduled' => 'Project review is moving into site-level coordination and planning.',
        'Quotation Sent'       => 'Your proposal files are ready and available below.',
        'Closed'               => 'This request has been completed or formally closed by the team.',
    );
}

function izin_projects_status_index($status) {
    $statuses = izin_projects_statuses();
    $index = array_search($status, $statuses, true);

    return $index === false ? 0 : (int) $index;
}

function izin_projects_status_steps($current_status) {
    $statuses = izin_projects_statuses();
    $descriptions = izin_projects_status_descriptions();
    $current_index = izin_projects_status_index($current_status);
    $steps = array();

    foreach ($statuses as $index => $status) {
        $state = 'upcoming';
        if ($index < $current_index) {
            $state = 'completed';
        } elseif ($index === $current_index) {
            $state = 'current';
        }

        $steps[] = array(
            'label'       => $status,
            'state'       => $state,
            'description' => $descriptions[$status] ?? '',
        );
    }

    return $steps;
}

function izin_projects_should_show_quotation_files($status) {
    return in_array($status, array('Quotation Sent', 'Closed'), true);
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
        status_updated_at DATETIME NULL,
        internal_notes LONGTEXT NULL,
        client_notes LONGTEXT NULL,
        status_token VARCHAR(120) DEFAULT '' NOT NULL,
        status_token_created_at DATETIME NULL,
        quotation_urls LONGTEXT NULL,
        quotation_names LONGTEXT NULL,
        quotation_sent_at DATETIME NULL,
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
        KEY status (status),
        KEY status_token (status_token)
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

function izin_projects_attachment_mimes() {
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

function izin_projects_quotation_mimes() {
    return array(
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

function izin_projects_upload_files($field, $allowed_mimes, $max_files, $max_bytes, $invalid_type_message) {
    $files = izin_projects_normalize_files_array($field);

    if (empty($files)) {
        return array();
    }

    if (count($files) > $max_files) {
        return new WP_Error('izin_project_file_count', sprintf(__('You can upload up to %d files.', 'izin-designs-theme'), (int) $max_files), array('status' => 400));
    }

    require_once ABSPATH . 'wp-admin/includes/file.php';

    $uploaded = array();

    foreach ($files as $file) {
        if (!empty($file['error']) && (int) $file['error'] !== UPLOAD_ERR_OK) {
            return new WP_Error('izin_project_file_error', __('One of the uploaded files could not be processed.', 'izin-designs-theme'), array('status' => 400));
        }

        $file_type = wp_check_filetype_and_ext($file['tmp_name'], $file['name'], $allowed_mimes);

        if (empty($file_type['ext']) || empty($file_type['type'])) {
            return new WP_Error('izin_project_file_type', $invalid_type_message, array('status' => 400));
        }

        if (!empty($file['size']) && (int) $file['size'] > $max_bytes) {
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

function izin_projects_handle_attachments() {
    return izin_projects_upload_files(
        'attachments',
        izin_projects_attachment_mimes(),
        5,
        8 * 1024 * 1024,
        __('Only JPG, JPEG, PNG, WEBP, PDF, DOC, and DOCX files are allowed.', 'izin-designs-theme')
    );
}

function izin_projects_handle_quotation_uploads() {
    return izin_projects_upload_files(
        'quotation_files',
        izin_projects_quotation_mimes(),
        5,
        8 * 1024 * 1024,
        __('Only PDF, DOC, and DOCX quotation files are allowed.', 'izin-designs-theme')
    );
}

function izin_projects_decode_json_list($value) {
    $items = json_decode((string) $value, true);
    return is_array($items) ? $items : array();
}

function izin_projects_encode_json_list($items) {
    return wp_json_encode(array_values(array_filter((array) $items, 'strlen')));
}

function izin_projects_merge_uploaded_files($existing_urls, $existing_names, $new_files) {
    $urls = izin_projects_decode_json_list($existing_urls);
    $names = izin_projects_decode_json_list($existing_names);

    foreach ($new_files as $file) {
        $urls[] = $file['url'];
        $names[] = $file['name'];
    }

    return array(
        'urls'  => izin_projects_encode_json_list($urls),
        'names' => izin_projects_encode_json_list($names),
    );
}

function izin_projects_get_project($project_id) {
    global $wpdb;

    izin_projects_install();

    return $wpdb->get_row(
        $wpdb->prepare('SELECT * FROM ' . izin_projects_table_name() . ' WHERE id = %d', $project_id)
    );
}

function izin_projects_get_project_by_token($token) {
    global $wpdb;

    izin_projects_install();

    return $wpdb->get_row(
        $wpdb->prepare('SELECT * FROM ' . izin_projects_table_name() . ' WHERE status_token = %s', $token)
    );
}

function izin_projects_generate_unique_token() {
    global $wpdb;

    do {
        $token = wp_generate_password(48, false, false);
        $exists = (int) $wpdb->get_var(
            $wpdb->prepare('SELECT COUNT(1) FROM ' . izin_projects_table_name() . ' WHERE status_token = %s', $token)
        );
    } while ($exists > 0);

    return $token;
}

function izin_projects_status_url($token) {
    return add_query_arg('token', rawurlencode($token), home_url('/project-status/'));
}

function izin_projects_send_status_email($project, $status_url) {
    $to = sanitize_email($project->email);

    if (!is_email($to)) {
        return new WP_Error('izin_project_email', __('The client email address is not valid.', 'izin-designs-theme'));
    }

    $quotation_files = izin_projects_decode_json_list($project->quotation_names);
    $client_note = trim((string) $project->client_notes);

    $message_lines = array(
        'Hello ' . $project->client_name . ',',
        '',
        'Your IZIN Designs project request has been updated.',
        '',
        'Current status: ' . $project->status,
        'Status page: ' . $status_url,
    );

    if ($client_note !== '') {
        $message_lines[] = '';
        $message_lines[] = 'Message from the team:';
        $message_lines[] = $client_note;
    }

    if (!empty($quotation_files)) {
        $message_lines[] = '';
        $message_lines[] = 'Quotation files are now available on your secure status page.';
    }

    $message_lines[] = '';
    $message_lines[] = 'Regards,';
    $message_lines[] = 'IZIN Designs Interior Studio';

    $sent = wp_mail(
        $to,
        __('Your IZIN Designs Project Status Update', 'izin-designs-theme'),
        implode("\n", $message_lines)
    );

    if (!$sent) {
        return new WP_Error('izin_project_email_send', __('Status email could not be sent.', 'izin-designs-theme'));
    }

    return true;
}

function izin_projects_notify_admin($project_id, $project_data, $attachments) {
    $admin_email = get_option('admin_email');

    if (!is_email($admin_email)) {
        return;
    }

    $admin_url = admin_url('admin.php?page=izin-projects&project_id=' . (int) $project_id);
    $attachment_lines = array();

    foreach ($attachments as $attachment) {
        $attachment_lines[] = sprintf('%s - %s', $attachment['name'], $attachment['url']);
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
        'Manage request: ' . $admin_url,
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
    $now = current_time('mysql');

    $project_data = array(
        'client_name'            => $client_name,
        'phone'                  => $phone,
        'email'                  => $email,
        'project_type'           => $project_type,
        'location'               => $location,
        'budget_range'           => $budget_range,
        'property_size_sqft'     => $property_size_sqft,
        'expected_start_date'    => $expected_start_date,
        'project_description'    => $project_description,
        'attachment_urls'        => izin_projects_encode_json_list(wp_list_pluck($attachments, 'url')),
        'attachment_names'       => izin_projects_encode_json_list(wp_list_pluck($attachments, 'name')),
        'status'                 => 'New',
        'status_updated_at'      => $now,
        'internal_notes'         => '',
        'client_notes'           => '',
        'status_token'           => '',
        'status_token_created_at'=> null,
        'quotation_urls'         => izin_projects_encode_json_list(array()),
        'quotation_names'        => izin_projects_encode_json_list(array()),
        'quotation_sent_at'      => null,
        'source_url'             => esc_url_raw($request->get_param('source_url')),
        'referrer'               => esc_url_raw($request->get_param('referrer')),
        'ip_address'             => izin_projects_get_ip_address(),
        'device_type'            => izin_projects_detect_device_type($user_agent),
        'browser'                => izin_projects_detect_browser($user_agent),
        'operating_system'       => izin_projects_detect_operating_system($user_agent),
        'language'               => sanitize_text_field($request->get_param('language')),
        'screen_size'            => sanitize_text_field($request->get_param('screen_size')),
        'viewport_size'          => sanitize_text_field($request->get_param('viewport_size')),
        'timezone'               => sanitize_text_field($request->get_param('timezone')),
        'cookies_enabled'        => $cookies_enabled,
        'utm_source'             => sanitize_text_field($request->get_param('utm_source')),
        'utm_medium'             => sanitize_text_field($request->get_param('utm_medium')),
        'utm_campaign'           => sanitize_text_field($request->get_param('utm_campaign')),
        'utm_content'            => sanitize_text_field($request->get_param('utm_content')),
        'utm_term'               => sanitize_text_field($request->get_param('utm_term')),
        'user_agent'             => $user_agent,
        'created_at'             => $now,
    );

    $inserted = $wpdb->insert(izin_projects_table_name(), $project_data);

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

function izin_projects_require_admin() {
    if (!current_user_can('manage_options')) {
        wp_die(esc_html__('You do not have permission to manage project requests.', 'izin-designs-theme'));
    }
}

function izin_projects_delete_admin_action() {
    izin_projects_require_admin();

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

function izin_projects_update_admin_action() {
    izin_projects_require_admin();

    $project_id = absint($_POST['project_id'] ?? 0);
    check_admin_referer('izin_update_project_' . $project_id);

    $project = izin_projects_get_project($project_id);
    if (!$project) {
        wp_safe_redirect(admin_url('admin.php?page=izin-projects'));
        exit;
    }

    $status = sanitize_text_field($_POST['status'] ?? '');
    if (!in_array($status, izin_projects_statuses(), true)) {
        $status = 'New';
    }

    global $wpdb;
    $wpdb->update(
        izin_projects_table_name(),
        array(
            'status'            => $status,
            'status_updated_at' => current_time('mysql'),
            'internal_notes'    => sanitize_textarea_field($_POST['internal_notes'] ?? ''),
            'client_notes'      => sanitize_textarea_field($_POST['client_notes'] ?? ''),
        ),
        array('id' => $project_id),
        array('%s', '%s', '%s', '%s'),
        array('%d')
    );

    wp_safe_redirect(add_query_arg('izin_project_updated', '1', admin_url('admin.php?page=izin-projects&project_id=' . $project_id)));
    exit;
}
add_action('admin_post_izin_update_project', 'izin_projects_update_admin_action');

function izin_projects_upload_quotation_admin_action() {
    izin_projects_require_admin();

    $project_id = absint($_POST['project_id'] ?? 0);
    check_admin_referer('izin_upload_quotation_' . $project_id);

    $project = izin_projects_get_project($project_id);
    if (!$project) {
        wp_safe_redirect(admin_url('admin.php?page=izin-projects'));
        exit;
    }

    $uploaded = izin_projects_handle_quotation_uploads();

    if (is_wp_error($uploaded)) {
        wp_safe_redirect(add_query_arg('izin_project_upload_error', rawurlencode($uploaded->get_error_message()), admin_url('admin.php?page=izin-projects&project_id=' . $project_id)));
        exit;
    }

    if (!empty($uploaded)) {
        $merged = izin_projects_merge_uploaded_files($project->quotation_urls, $project->quotation_names, $uploaded);

        global $wpdb;
        $wpdb->update(
            izin_projects_table_name(),
            array(
                'quotation_urls' => $merged['urls'],
                'quotation_names'=> $merged['names'],
            ),
            array('id' => $project_id),
            array('%s', '%s'),
            array('%d')
        );
    }

    wp_safe_redirect(add_query_arg('izin_project_quotation_uploaded', '1', admin_url('admin.php?page=izin-projects&project_id=' . $project_id)));
    exit;
}
add_action('admin_post_izin_upload_quotation', 'izin_projects_upload_quotation_admin_action');

function izin_projects_generate_token_admin_action() {
    izin_projects_require_admin();

    $project_id = absint($_POST['project_id'] ?? 0);
    check_admin_referer('izin_generate_token_' . $project_id);

    $project = izin_projects_get_project($project_id);
    if (!$project) {
        wp_safe_redirect(admin_url('admin.php?page=izin-projects'));
        exit;
    }

    $token = izin_projects_generate_unique_token();

    global $wpdb;
    $wpdb->update(
        izin_projects_table_name(),
        array(
            'status_token'            => $token,
            'status_token_created_at' => current_time('mysql'),
        ),
        array('id' => $project_id),
        array('%s', '%s'),
        array('%d')
    );

    wp_safe_redirect(add_query_arg('izin_project_token_generated', '1', admin_url('admin.php?page=izin-projects&project_id=' . $project_id)));
    exit;
}
add_action('admin_post_izin_generate_project_token', 'izin_projects_generate_token_admin_action');

function izin_projects_send_status_email_admin_action() {
    izin_projects_require_admin();

    $project_id = absint($_POST['project_id'] ?? 0);
    check_admin_referer('izin_send_status_email_' . $project_id);

    $project = izin_projects_get_project($project_id);
    if (!$project) {
        wp_safe_redirect(admin_url('admin.php?page=izin-projects'));
        exit;
    }

    global $wpdb;

    if (empty($project->status_token)) {
        $project->status_token = izin_projects_generate_unique_token();
        $project->status_token_created_at = current_time('mysql');

        $wpdb->update(
            izin_projects_table_name(),
            array(
                'status_token'            => $project->status_token,
                'status_token_created_at' => $project->status_token_created_at,
            ),
            array('id' => $project_id),
            array('%s', '%s'),
            array('%d')
        );
    }

    $status_url = izin_projects_status_url($project->status_token);
    $sent = izin_projects_send_status_email($project, $status_url);

    if (is_wp_error($sent)) {
        wp_safe_redirect(add_query_arg('izin_project_email_error', rawurlencode($sent->get_error_message()), admin_url('admin.php?page=izin-projects&project_id=' . $project_id)));
        exit;
    }

    $wpdb->update(
        izin_projects_table_name(),
        array(
            'quotation_sent_at' => current_time('mysql'),
        ),
        array('id' => $project_id),
        array('%s'),
        array('%d')
    );

    wp_safe_redirect(add_query_arg('izin_project_status_email_sent', '1', admin_url('admin.php?page=izin-projects&project_id=' . $project_id)));
    exit;
}
add_action('admin_post_izin_send_project_status_email', 'izin_projects_send_status_email_admin_action');

function izin_projects_notice($message, $type = 'success') {
    ?>
    <div class="notice notice-<?php echo esc_attr($type); ?> is-dismissible">
        <p><?php echo esc_html($message); ?></p>
    </div>
    <?php
}

function izin_projects_admin_notices() {
    if (!empty($_GET['izin_project_deleted'])) {
        izin_projects_notice(__('Project request deleted.', 'izin-designs-theme'));
    }

    if (!empty($_GET['izin_project_updated'])) {
        izin_projects_notice(__('Project request updated.', 'izin-designs-theme'));
    }

    if (!empty($_GET['izin_project_quotation_uploaded'])) {
        izin_projects_notice(__('Quotation files uploaded.', 'izin-designs-theme'));
    }

    if (!empty($_GET['izin_project_token_generated'])) {
        izin_projects_notice(__('Secure status link generated.', 'izin-designs-theme'));
    }

    if (!empty($_GET['izin_project_status_email_sent'])) {
        izin_projects_notice(__('Client status email sent.', 'izin-designs-theme'));
    }

    if (!empty($_GET['izin_project_upload_error'])) {
        izin_projects_notice(wp_unslash($_GET['izin_project_upload_error']), 'error');
    }

    if (!empty($_GET['izin_project_email_error'])) {
        izin_projects_notice(wp_unslash($_GET['izin_project_email_error']), 'error');
    }
}

function izin_projects_render_file_links($urls_json, $names_json) {
    $urls = izin_projects_decode_json_list($urls_json);
    $names = izin_projects_decode_json_list($names_json);

    if (empty($urls)) {
        echo '<small>' . esc_html__('No files', 'izin-designs-theme') . '</small>';
        return;
    }

    foreach ($urls as $index => $url) {
        printf(
            '<a href="%1$s" target="_blank" rel="noopener noreferrer">%2$s</a><br>',
            esc_url($url),
            esc_html($names[$index] ?? sprintf(__('File %d', 'izin-designs-theme'), $index + 1))
        );
    }
}

function izin_projects_admin_detail_page($project) {
    $status_link = !empty($project->status_token) ? izin_projects_status_url($project->status_token) : '';
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(sprintf(__('Project Request #%d', 'izin-designs-theme'), (int) $project->id)); ?></h1>
        <p><a href="<?php echo esc_url(admin_url('admin.php?page=izin-projects')); ?>">&larr; <?php esc_html_e('Back to all requests', 'izin-designs-theme'); ?></a></p>
        <?php izin_projects_admin_notices(); ?>

        <div class="izin-project-admin-grid">
            <div class="izin-project-admin-panel">
                <h2><?php esc_html_e('Client Request', 'izin-designs-theme'); ?></h2>
                <table class="widefat striped">
                    <tbody>
                        <tr><td><?php esc_html_e('Client', 'izin-designs-theme'); ?></td><td><?php echo esc_html($project->client_name); ?></td></tr>
                        <tr><td><?php esc_html_e('Phone', 'izin-designs-theme'); ?></td><td><a href="<?php echo esc_url('tel:' . preg_replace('/[^0-9+]/', '', $project->phone)); ?>"><?php echo esc_html($project->phone); ?></a></td></tr>
                        <tr><td><?php esc_html_e('Email', 'izin-designs-theme'); ?></td><td><a href="<?php echo esc_url('mailto:' . $project->email); ?>"><?php echo esc_html($project->email); ?></a></td></tr>
                        <tr><td><?php esc_html_e('Project Type', 'izin-designs-theme'); ?></td><td><?php echo esc_html($project->project_type); ?></td></tr>
                        <tr><td><?php esc_html_e('Location', 'izin-designs-theme'); ?></td><td><?php echo esc_html($project->location); ?></td></tr>
                        <tr><td><?php esc_html_e('Budget', 'izin-designs-theme'); ?></td><td><?php echo esc_html($project->budget_range); ?></td></tr>
                        <tr><td><?php esc_html_e('Property Size', 'izin-designs-theme'); ?></td><td><?php echo esc_html($project->property_size_sqft); ?></td></tr>
                        <tr><td><?php esc_html_e('Expected Start Date', 'izin-designs-theme'); ?></td><td><?php echo esc_html($project->expected_start_date); ?></td></tr>
                        <tr><td><?php esc_html_e('Submitted At', 'izin-designs-theme'); ?></td><td><?php echo esc_html($project->created_at); ?></td></tr>
                        <tr><td><?php esc_html_e('Description', 'izin-designs-theme'); ?></td><td><?php echo nl2br(esc_html($project->project_description)); ?></td></tr>
                        <tr><td><?php esc_html_e('Reference Files', 'izin-designs-theme'); ?></td><td><?php izin_projects_render_file_links($project->attachment_urls, $project->attachment_names); ?></td></tr>
                    </tbody>
                </table>
            </div>

            <div class="izin-project-admin-panel">
                <h2><?php esc_html_e('Pipeline', 'izin-designs-theme'); ?></h2>
                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                    <input type="hidden" name="action" value="izin_update_project">
                    <input type="hidden" name="project_id" value="<?php echo esc_attr($project->id); ?>">
                    <?php wp_nonce_field('izin_update_project_' . $project->id); ?>

                    <p>
                        <label for="izin-project-status"><strong><?php esc_html_e('Stage', 'izin-designs-theme'); ?></strong></label><br>
                        <select id="izin-project-status" name="status">
                            <?php foreach (izin_projects_statuses() as $status): ?>
                                <option value="<?php echo esc_attr($status); ?>" <?php selected($project->status, $status); ?>><?php echo esc_html($status); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </p>

                    <p>
                        <label for="izin-project-client-notes"><strong><?php esc_html_e('Client Status Note', 'izin-designs-theme'); ?></strong></label><br>
                        <textarea id="izin-project-client-notes" name="client_notes" rows="5" class="large-text"><?php echo esc_textarea($project->client_notes); ?></textarea>
                    </p>

                    <p>
                        <label for="izin-project-internal-notes"><strong><?php esc_html_e('Internal Notes', 'izin-designs-theme'); ?></strong></label><br>
                        <textarea id="izin-project-internal-notes" name="internal_notes" rows="6" class="large-text"><?php echo esc_textarea($project->internal_notes); ?></textarea>
                    </p>

                    <p><button class="button button-primary" type="submit"><?php esc_html_e('Save Pipeline Update', 'izin-designs-theme'); ?></button></p>
                </form>

                <hr>

                <h2><?php esc_html_e('Quotation Files', 'izin-designs-theme'); ?></h2>
                <p><?php izin_projects_render_file_links($project->quotation_urls, $project->quotation_names); ?></p>

                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="izin_upload_quotation">
                    <input type="hidden" name="project_id" value="<?php echo esc_attr($project->id); ?>">
                    <?php wp_nonce_field('izin_upload_quotation_' . $project->id); ?>
                    <p><input type="file" name="quotation_files[]" accept=".pdf,.doc,.docx,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" multiple></p>
                    <p><button class="button" type="submit"><?php esc_html_e('Upload Quotation Files', 'izin-designs-theme'); ?></button></p>
                </form>

                <hr>

                <h2><?php esc_html_e('Client Status Access', 'izin-designs-theme'); ?></h2>
                <?php if ($status_link !== ''): ?>
                    <p><a href="<?php echo esc_url($status_link); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html($status_link); ?></a></p>
                <?php else: ?>
                    <p><?php esc_html_e('No secure status link generated yet.', 'izin-designs-theme'); ?></p>
                <?php endif; ?>
                <p><?php echo esc_html(sprintf(__('Status updated: %s', 'izin-designs-theme'), $project->status_updated_at ?: __('Not yet', 'izin-designs-theme'))); ?></p>
                <p><?php echo esc_html(sprintf(__('Link generated: %s', 'izin-designs-theme'), $project->status_token_created_at ?: __('Not yet', 'izin-designs-theme'))); ?></p>
                <p><?php echo esc_html(sprintf(__('Status email sent: %s', 'izin-designs-theme'), $project->quotation_sent_at ?: __('Not yet', 'izin-designs-theme'))); ?></p>

                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="display:inline-block; margin-right:12px;">
                    <input type="hidden" name="action" value="izin_generate_project_token">
                    <input type="hidden" name="project_id" value="<?php echo esc_attr($project->id); ?>">
                    <?php wp_nonce_field('izin_generate_token_' . $project->id); ?>
                    <button class="button" type="submit"><?php esc_html_e('Generate New Link', 'izin-designs-theme'); ?></button>
                </form>

                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="display:inline-block;">
                    <input type="hidden" name="action" value="izin_send_project_status_email">
                    <input type="hidden" name="project_id" value="<?php echo esc_attr($project->id); ?>">
                    <?php wp_nonce_field('izin_send_status_email_' . $project->id); ?>
                    <button class="button button-primary" type="submit"><?php esc_html_e('Email Status Link to Client', 'izin-designs-theme'); ?></button>
                </form>
            </div>

            <div class="izin-project-admin-panel">
                <h2><?php esc_html_e('Tracking', 'izin-designs-theme'); ?></h2>
                <table class="widefat striped">
                    <tbody>
                        <tr><td><?php esc_html_e('Source URL', 'izin-designs-theme'); ?></td><td><?php if (!empty($project->source_url)) : ?><a href="<?php echo esc_url($project->source_url); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html($project->source_url); ?></a><?php endif; ?></td></tr>
                        <tr><td><?php esc_html_e('Referrer', 'izin-designs-theme'); ?></td><td><?php if (!empty($project->referrer)) : ?><a href="<?php echo esc_url($project->referrer); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html($project->referrer); ?></a><?php endif; ?></td></tr>
                        <tr><td><?php esc_html_e('Device', 'izin-designs-theme'); ?></td><td><?php echo esc_html(trim($project->device_type . ' / ' . $project->browser . ' / ' . $project->operating_system, ' /')); ?></td></tr>
                        <tr><td><?php esc_html_e('Screen', 'izin-designs-theme'); ?></td><td><?php echo esc_html(trim($project->screen_size . ' viewport ' . $project->viewport_size)); ?></td></tr>
                        <tr><td><?php esc_html_e('Language', 'izin-designs-theme'); ?></td><td><?php echo esc_html($project->language); ?></td></tr>
                        <tr><td><?php esc_html_e('Timezone', 'izin-designs-theme'); ?></td><td><?php echo esc_html($project->timezone); ?></td></tr>
                        <tr><td><?php esc_html_e('IP', 'izin-designs-theme'); ?></td><td><?php echo esc_html($project->ip_address); ?></td></tr>
                        <tr><td><?php esc_html_e('UTM', 'izin-designs-theme'); ?></td><td><?php echo esc_html(trim($project->utm_source . ' / ' . $project->utm_medium . ' / ' . $project->utm_campaign, ' /')); ?></td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php
}

function izin_projects_admin_page() {
    global $wpdb;

    izin_projects_install();

    $project_id = absint($_GET['project_id'] ?? 0);
    if ($project_id > 0) {
        $project = izin_projects_get_project($project_id);
        if ($project) {
            izin_projects_admin_detail_page($project);
            return;
        }
    }

    $projects = $wpdb->get_results('SELECT * FROM ' . izin_projects_table_name() . ' ORDER BY created_at DESC, id DESC LIMIT 200');
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Izin Projects', 'izin-designs-theme'); ?></h1>
        <p><?php esc_html_e('Latest quotation and bid requests submitted from the website.', 'izin-designs-theme'); ?></p>
        <?php izin_projects_admin_notices(); ?>

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

            .izin-project-admin-grid {
                display: grid;
                grid-template-columns: minmax(0, 1.1fr) minmax(0, 1.1fr);
                gap: 20px;
                margin-top: 20px;
            }

            .izin-project-admin-panel {
                background: #ffffff;
                border: 1px solid #dcdcde;
                padding: 20px;
            }

            .izin-project-admin-panel h2 {
                margin-top: 0;
            }
        </style>

        <table class="widefat striped">
            <thead>
                <tr>
                    <th style="width: 42px;"><span class="screen-reader-text"><?php esc_html_e('Actions', 'izin-designs-theme'); ?></span></th>
                    <th><?php esc_html_e('Date', 'izin-designs-theme'); ?></th>
                    <th><?php esc_html_e('Client', 'izin-designs-theme'); ?></th>
                    <th><?php esc_html_e('Project Type', 'izin-designs-theme'); ?></th>
                    <th><?php esc_html_e('Budget', 'izin-designs-theme'); ?></th>
                    <th><?php esc_html_e('Start Date', 'izin-designs-theme'); ?></th>
                    <th><?php esc_html_e('Stage', 'izin-designs-theme'); ?></th>
                    <th><?php esc_html_e('Quotation', 'izin-designs-theme'); ?></th>
                    <th><?php esc_html_e('Client Link', 'izin-designs-theme'); ?></th>
                    <th><?php esc_html_e('Manage', 'izin-designs-theme'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($projects)): ?>
                    <tr>
                        <td colspan="10"><?php esc_html_e('No project requests found yet.', 'izin-designs-theme'); ?></td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($projects as $project): ?>
                        <?php $quotation_urls = izin_projects_decode_json_list($project->quotation_urls); ?>
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
                            <td>
                                <strong><?php echo esc_html($project->client_name); ?></strong><br>
                                <small><a href="<?php echo esc_url('mailto:' . $project->email); ?>"><?php echo esc_html($project->email); ?></a></small>
                            </td>
                            <td><?php echo esc_html($project->project_type); ?></td>
                            <td><?php echo esc_html($project->budget_range); ?></td>
                            <td><?php echo esc_html($project->expected_start_date); ?></td>
                            <td>
                                <?php echo esc_html($project->status); ?><br>
                                <small><?php echo esc_html($project->status_updated_at ?: $project->created_at); ?></small>
                            </td>
                            <td><?php echo empty($quotation_urls) ? esc_html__('No', 'izin-designs-theme') : esc_html__('Attached', 'izin-designs-theme'); ?></td>
                            <td>
                                <?php if (!empty($project->status_token)): ?>
                                    <small><?php echo esc_html($project->quotation_sent_at ? __('Emailed', 'izin-designs-theme') : __('Generated', 'izin-designs-theme')); ?></small>
                                <?php else: ?>
                                    <small><?php esc_html_e('Not created', 'izin-designs-theme'); ?></small>
                                <?php endif; ?>
                            </td>
                            <td><a class="button button-secondary" href="<?php echo esc_url(admin_url('admin.php?page=izin-projects&project_id=' . absint($project->id))); ?>"><?php esc_html_e('Manage', 'izin-designs-theme'); ?></a></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php
}
