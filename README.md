# IZIN Designs Landing Page Plugin

This repository is structured so Hostinger Git can deploy it directly into this folder:

```text
public_html/wp-content/plugins/izin-designs-landing/
```

The final server structure should be:

```text
public_html/wp-content/plugins/izin-designs-landing/izin-designs-landing.php
public_html/wp-content/plugins/izin-designs-landing/frontend/index.html
public_html/wp-content/plugins/izin-designs-landing/frontend/styles.css
public_html/wp-content/plugins/izin-designs-landing/frontend/script.js
```

After deploying:

1. Open WordPress Admin.
2. Go to Plugins.
3. Activate **IZIN Designs Landing Page**.
4. Edit the target page with Elementor.
5. Add a Shortcode widget.
6. Use:

```text
[izin_designs_landing]
```

This plugin does not edit WordPress core, Elementor, the active theme, `wp-config.php`, or `.htaccess`.
