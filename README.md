# IZIN Designs WordPress Codebase

This repository can be deployed as a custom WordPress theme:

```text
public_html/wp-content/themes/izin-designs-theme/
```

WordPress requires these root theme files, which are included:

```text
style.css
functions.php
index.php
front-page.php
header.php
footer.php
frontend/
```

After deploying the theme, preview it from:

```text
WordPress Admin > Appearance > Themes > IZIN Designs Theme > Live Preview
```

Do not delete the current live theme until this theme is tested.

## Optional Plugin Use

This repository also still contains the shortcode plugin file:

```text
izin-designs-landing.php
```

If deploying as a plugin instead, deploy to:

```text
public_html/wp-content/plugins/izin-designs-landing/
```

and use:

```text
[izin_designs_landing]
```

This codebase does not edit WordPress core, Elementor, `wp-config.php`, or `.htaccess`.

## Lead Capture

The consultation form posts to:

```text
/wp-json/izin-leads/v1/submit
```

Submitted enquiries are saved in the WordPress database table:

```text
wp_izin_leads
```

Admins can view leads from:

```text
WordPress Admin > Izin Leads
```

After saving, the visitor is redirected to WhatsApp with the enquiry details.
