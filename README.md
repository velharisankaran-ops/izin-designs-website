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

## Deployment Workflow

Use this repository as the only editing source for the live theme.

Standard flow:

```text
Edit locally
Preview locally
git status
git add ...
git commit -m "..."
git push origin main
.\scripts\deploy-theme.ps1
Verify live routes
```

Live theme target:

```text
/home/u658377134/domains/izindesigns.com/public_html/wp-content/themes/izin-designs-theme
```

### SSH Alias

Install the local SSH alias once:

```powershell
.\scripts\install-ssh-alias.ps1
```

Then server login becomes:

```powershell
ssh izin-hostinger
```

### Deploy Script

Default full theme deploy:

```powershell
.\scripts\deploy-theme.ps1
```

Dry run:

```powershell
.\scripts\deploy-theme.ps1 -DryRun
```

Deploy only specific files or folders:

```powershell
.\scripts\deploy-theme.ps1 -Path functions.php,includes,frontend
```

Deploy without cache purge:

```powershell
.\scripts\deploy-theme.ps1 -NoCachePurge
```

The script:

```text
- uploads only the theme allowlist
- never touches plugins, uploads, or WordPress core
- extracts into the live theme directory
- purges LiteSpeed and WordPress cache by default
- prints the main routes to verify after deploy
```
