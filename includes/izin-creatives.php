<?php
/**
 * IZIN Creatives service enquiry storage, REST endpoint, and admin screen.
 */

if (!defined('ABSPATH')) {
    exit;
}

if (defined('IZIN_CREATIVES_LOADED')) {
    return;
}

define('IZIN_CREATIVES_LOADED', true);

function izin_creatives_table_name() {
    global $wpdb;
    return $wpdb->prefix . 'izin_creatives_enquiries';
}

function izin_creatives_install() {
    global $wpdb;

    $table_name = izin_creatives_table_name();
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE {$table_name} (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        service_needed VARCHAR(190) DEFAULT '' NOT NULL,
        business_name VARCHAR(190) NOT NULL,
        business_category VARCHAR(190) DEFAULT '' NOT NULL,
        contact_person VARCHAR(190) NOT NULL,
        phone VARCHAR(80) NOT NULL,
        email VARCHAR(190) DEFAULT '' NOT NULL,
        service_location VARCHAR(190) DEFAULT '' NOT NULL,
        target_customer TEXT NULL,
        services_offered TEXT NULL,
        instagram_url TEXT NULL,
        facebook_url TEXT NULL,
        google_profile TEXT NULL,
        website_url TEXT NULL,
        required_support TEXT NULL,
        main_goal VARCHAR(190) DEFAULT '' NOT NULL,
        monthly_ad_budget VARCHAR(120) DEFAULT '' NOT NULL,
        expected_start_date VARCHAR(120) DEFAULT '' NOT NULL,
        campaign_focus TEXT NULL,
        preferred_language VARCHAR(120) DEFAULT '' NOT NULL,
        approval_contact VARCHAR(190) DEFAULT '' NOT NULL,
        notes TEXT NULL,
        content_assets TEXT NULL,
        content_approval_person VARCHAR(190) DEFAULT '' NOT NULL,
        lead_followup_person VARCHAR(190) DEFAULT '' NOT NULL,
        preferred_reporting_date VARCHAR(120) DEFAULT '' NOT NULL,
        preferred_channel VARCHAR(190) DEFAULT '' NOT NULL,
        source_url TEXT NULL,
        ip_address VARCHAR(100) DEFAULT '' NOT NULL,
        user_agent TEXT NULL,
        created_at DATETIME NOT NULL,
        PRIMARY KEY  (id),
        KEY created_at (created_at)
    ) {$charset_collate};";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
}

function izin_creatives_get_ip_address() {
    $ip_keys = array('HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR');

    foreach ($ip_keys as $key) {
        if (empty($_SERVER[$key])) {
            continue;
        }

        $ip = sanitize_text_field(wp_unslash($_SERVER[$key]));
        $ip = trim(explode(',', $ip)[0]);

        if ($ip !== '') {
            return $ip;
        }
    }

    return '';
}

function izin_creatives_checkbox_list($value) {
    if (is_array($value)) {
        return implode(', ', array_map('sanitize_text_field', wp_unslash($value)));
    }

    return sanitize_text_field($value);
}

function izin_creatives_register_rest_routes() {
    register_rest_route('izin-creatives/v1', '/enquiry', array(
        'methods' => 'POST',
        'callback' => 'izin_creatives_submit_rest',
        'permission_callback' => '__return_true',
    ));
}
add_action('rest_api_init', 'izin_creatives_register_rest_routes');

function izin_creatives_submit_rest(WP_REST_Request $request) {
    global $wpdb;

    if (sanitize_text_field($request->get_param('website')) !== '') {
        return new WP_REST_Response(array(
            'success' => true,
            'message' => 'Ignored.',
        ), 200);
    }

    $business_name = sanitize_text_field($request->get_param('business_name'));
    $service_needed = sanitize_text_field($request->get_param('service_needed'));
    $contact_person = sanitize_text_field($request->get_param('contact_person'));
    $phone = sanitize_text_field($request->get_param('phone'));
    $email = sanitize_email($request->get_param('email'));

    if ($service_needed === '' || $business_name === '' || $contact_person === '' || $phone === '') {
        return new WP_REST_Response(array(
            'success' => false,
            'message' => 'Service, business name, contact person and phone are required.',
        ), 400);
    }

    if ($email !== '' && !is_email($email)) {
        return new WP_REST_Response(array(
            'success' => false,
            'message' => 'Invalid email address.',
        ), 400);
    }

    izin_creatives_install();

    $data = array(
        'service_needed' => $service_needed,
        'business_name' => $business_name,
        'business_category' => sanitize_text_field($request->get_param('business_category')),
        'contact_person' => $contact_person,
        'phone' => $phone,
        'email' => $email,
        'service_location' => sanitize_text_field($request->get_param('service_location')),
        'target_customer' => sanitize_textarea_field($request->get_param('target_customer')),
        'services_offered' => sanitize_textarea_field($request->get_param('services_offered')),
        'instagram_url' => esc_url_raw($request->get_param('instagram_url')),
        'facebook_url' => esc_url_raw($request->get_param('facebook_url')),
        'google_profile' => sanitize_text_field($request->get_param('google_profile')),
        'website_url' => esc_url_raw($request->get_param('website_url')),
        'required_support' => izin_creatives_checkbox_list($request->get_param('required_support')),
        'main_goal' => sanitize_text_field($request->get_param('main_goal')),
        'monthly_ad_budget' => sanitize_text_field($request->get_param('monthly_ad_budget')),
        'expected_start_date' => sanitize_text_field($request->get_param('expected_start_date')),
        'campaign_focus' => sanitize_textarea_field($request->get_param('campaign_focus')),
        'preferred_language' => sanitize_text_field($request->get_param('preferred_language')),
        'approval_contact' => sanitize_text_field($request->get_param('approval_contact')),
        'notes' => sanitize_textarea_field($request->get_param('notes')),
        'content_assets' => izin_creatives_checkbox_list($request->get_param('content_assets')),
        'content_approval_person' => sanitize_text_field($request->get_param('content_approval_person')),
        'lead_followup_person' => sanitize_text_field($request->get_param('lead_followup_person')),
        'preferred_reporting_date' => sanitize_text_field($request->get_param('preferred_reporting_date')),
        'preferred_channel' => sanitize_text_field($request->get_param('preferred_channel')),
        'source_url' => esc_url_raw($request->get_param('source_url')),
        'ip_address' => izin_creatives_get_ip_address(),
        'user_agent' => sanitize_textarea_field(wp_unslash($_SERVER['HTTP_USER_AGENT'] ?? '')),
        'created_at' => current_time('mysql'),
    );

    $inserted = $wpdb->insert(
        izin_creatives_table_name(),
        $data,
        array_fill(0, count($data), '%s')
    );

    if (!$inserted) {
        return new WP_REST_Response(array(
            'success' => false,
            'message' => 'Enquiry could not be saved.',
        ), 500);
    }

    $admin_link = admin_url('admin.php?page=izin-creatives-enquiries');
    $message = "New IZIN Creatives enquiry\n\n"
        . "Service: " . $data['service_needed'] . "\n"
        . "Business: {$business_name}\n"
        . "Contact: {$contact_person}\n"
        . "Phone: {$phone}\n"
        . "Email: {$email}\n"
        . "Main Goal: " . $data['main_goal'] . "\n"
        . "Required Support: " . $data['required_support'] . "\n\n"
        . "Review in WordPress admin: {$admin_link}";

    wp_mail(
        array('izindesignskochi@gmail.com', 'info@izindesigns.com'),
        'New IZIN Creatives enquiry',
        $message
    );

    return new WP_REST_Response(array(
        'success' => true,
        'enquiry_id' => (int) $wpdb->insert_id,
    ), 201);
}

function izin_creatives_admin_menu() {
    add_menu_page(
        __('IZIN Creatives', 'izin-designs-theme'),
        __('IZIN Creatives', 'izin-designs-theme'),
        'manage_options',
        'izin-creatives-enquiries',
        'izin_creatives_admin_page',
        'dashicons-megaphone',
        28
    );
}
add_action('admin_menu', 'izin_creatives_admin_menu');

function izin_creatives_admin_page() {
    global $wpdb;

    izin_creatives_install();

    $enquiries = $wpdb->get_results("SELECT * FROM " . izin_creatives_table_name() . " ORDER BY created_at DESC, id DESC LIMIT 200");
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('IZIN Creatives Enquiries', 'izin-designs-theme'); ?></h1>
        <p><?php esc_html_e('Latest digital marketing service enquiries submitted from the website.', 'izin-designs-theme'); ?></p>

        <table class="widefat striped">
            <thead>
                <tr>
                    <th><?php esc_html_e('Date', 'izin-designs-theme'); ?></th>
                    <th><?php esc_html_e('Business', 'izin-designs-theme'); ?></th>
                    <th><?php esc_html_e('Service', 'izin-designs-theme'); ?></th>
                    <th><?php esc_html_e('Contact', 'izin-designs-theme'); ?></th>
                    <th><?php esc_html_e('Goal', 'izin-designs-theme'); ?></th>
                    <th><?php esc_html_e('Support', 'izin-designs-theme'); ?></th>
                    <th><?php esc_html_e('Notes', 'izin-designs-theme'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($enquiries)): ?>
                    <tr><td colspan="7"><?php esc_html_e('No enquiries yet.', 'izin-designs-theme'); ?></td></tr>
                <?php else: ?>
                    <?php foreach ($enquiries as $enquiry): ?>
                        <tr>
                            <td><?php echo esc_html($enquiry->created_at); ?></td>
                            <td>
                                <strong><?php echo esc_html($enquiry->business_name); ?></strong><br>
                                <small><?php echo esc_html($enquiry->business_category); ?></small>
                            </td>
                            <td><?php echo esc_html($enquiry->service_needed ?? ''); ?></td>
                            <td>
                                <?php echo esc_html($enquiry->contact_person); ?><br>
                                <a href="<?php echo esc_url('tel:' . $enquiry->phone); ?>"><?php echo esc_html($enquiry->phone); ?></a>
                                <?php if (!empty($enquiry->email)): ?>
                                    <br><a href="<?php echo esc_url('mailto:' . $enquiry->email); ?>"><?php echo esc_html($enquiry->email); ?></a>
                                <?php endif; ?>
                            </td>
                            <td><?php echo esc_html($enquiry->main_goal); ?></td>
                            <td><?php echo esc_html($enquiry->required_support); ?></td>
                            <td>
                                <?php echo esc_html(wp_trim_words((string) $enquiry->notes, 28)); ?>
                                <?php if (!empty($enquiry->source_url)): ?>
                                    <br><small><a href="<?php echo esc_url($enquiry->source_url); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e('Source page', 'izin-designs-theme'); ?></a></small>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php
}
