# IZIN Designs Website

Static website files for the IZIN Designs interior studio site.

## Files

- `index.html` - main website page
- `styles.css` - responsive styling
- `script.js` - mobile navigation and header behavior
- `assets/izin-hero-interior.png` - generated hero/interior image

## Local preview

Open `index.html` in a browser, or serve the folder with any static server.

## WordPress / Hostinger File Manager deployment

Upload these files into the target public folder, commonly `public_html` for a static site or the active theme/custom page area if this is being embedded into WordPress.

For a WordPress theme page, copy the HTML body content into the page/template and keep `styles.css`, `script.js`, and `assets/izin-hero-interior.png` reachable from the same relative paths, or adjust the paths after upload.

Before launch, replace the contact form `mailto:hello@izindesigns.com` with the studio's real email or a WordPress form handler.

## Safer WordPress / Elementor deployment

Use the plugin package instead of replacing WordPress PHP files.

1. Upload `izin-designs-landing.zip` in WordPress Admin > Plugins > Add New > Upload Plugin, or upload/extract it to `wp-content/plugins/izin-designs-landing` using Hostinger File Manager.
2. Activate **IZIN Designs Landing Page**.
3. Create a new WordPress page.
4. Edit the page with Elementor.
5. Add a Shortcode widget with `[izin_designs_landing]`.
6. Publish the page.

This method does not edit WordPress core, the active theme, Elementor, `wp-config.php`, or `.htaccess`.
