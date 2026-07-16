<?php
/**
 * IZIN Creatives page template.
 *
 * Template Name: IZIN Creatives
 */

get_header();
?>

<?php get_template_part('template-parts/site-nav'); ?>

<?php $show_extended_sections = (bool) apply_filters('izin_creatives_show_extended_sections', false); ?>

<main class="creatives-page">
  <section class="creatives-hero">
    <div class="creatives-hero-copy">
      <h1 class="creatives-hero-title">
        <span class="creatives-hero-title-main">Signature Services</span>
      </h1>
      <p class="creatives-hero-services">Creative support built around your business</p>
    </div>
  </section>

  <section class="creatives-service-showcase" aria-labelledby="creatives-service-title">
    <h2 class="sr-only" id="creatives-service-title">Core creative services</h2>
    <div class="creatives-service-cards">
      <?php
      $creative_services = array(
          array(
              'key' => 'graphic-design',
              'title' => 'Graphic Design',
              'summary' => 'Brand identity, social creatives, print and event design.',
              'starting_price' => 'Services from Rs. 400',
              'image' => 'graphic-design.jpg',
              'alt' => 'Colour and brand design materials arranged on a creative workspace',
          ),
          array(
              'key' => 'digital-marketing',
              'title' => 'Digital Marketing',
              'summary' => 'SEO, paid advertising and social growth campaigns.',
              'starting_price' => 'Services from Rs. 8,500',
              'image' => 'digital-marketing.jpg',
              'alt' => 'Digital campaign analytics displayed on a laptop',
          ),
          array(
              'key' => 'web-development',
              'title' => 'Web Development',
              'summary' => 'Landing pages, CMS websites and custom applications.',
              'starting_price' => 'Services from Rs. 12,000',
              'image' => 'web-development.jpg',
              'alt' => 'Website development workspace with code displayed on a laptop',
          ),
      );

      foreach ($creative_services as $index => $service) :
      ?>
        <button class="creatives-service-card" type="button" data-creatives-service="<?php echo esc_attr($service['key']); ?>" aria-haspopup="dialog">
          <img src="<?php echo esc_url(get_template_directory_uri() . '/frontend/assets/creatives/' . $service['image']); ?>" alt="<?php echo esc_attr($service['alt']); ?>" width="1200" height="900" loading="<?php echo $index === 0 ? 'eager' : 'lazy'; ?>" decoding="async">
          <div class="creatives-service-card-copy">
            <h3><?php echo esc_html($service['title']); ?></h3>
            <p><?php echo esc_html($service['summary']); ?></p>
            <span class="creatives-service-price"><?php echo esc_html($service['starting_price']); ?></span>
            <span class="creatives-service-link">View Services &amp; Rates</span>
          </div>
        </button>
      <?php endforeach; ?>
    </div>
  </section>

  <?php
  $creative_rate_panels = array(
      'graphic-design' => array(
          'title' => 'Graphic Design',
          'service_value' => 'Graphic Design',
          'groups' => array(
              array('Branding', array(
                  array('Logo Design', 'Rs. 3,000+'),
                  array('Brand Strategy', 'Rs. 7,500+'),
                  array('Full Identity Kit', 'Rs. 15,000+'),
              )),
              array('Marketing Materials', array(
                  array('Social Posters', 'Rs. 400 - 800'),
                  array('Product Catalog', 'Rs. 1,200+'),
                  array('Print Media', 'Rs. 800+'),
              )),
              array('Event Design', array(
                  array('Wedding Invite Set', 'Rs. 2,500+'),
                  array('E-Invites', 'Rs. 950+'),
                  array('Event Branding', 'Custom quote'),
              )),
              array('Business Documents', array(
                  array('Pitch Deck Design', 'Rs. 3,000+'),
                  array('Proposal Templates', 'Rs. 1,500+'),
                  array('Letterheads / Business Cards', 'Rs. 800+'),
              )),
          ),
      ),
      'digital-marketing' => array(
          'title' => 'Digital Marketing',
          'service_value' => 'Digital Marketing',
          'groups' => array(
              array('Campaign & Growth', array(
                  array('SEO Campaign / month', 'Rs. 10,000+'),
                  array('Ads Management', 'Rs. 8,500+'),
                  array('Social Growth Pack', 'Rs. 15,000+'),
              )),
          ),
      ),
      'web-development' => array(
          'title' => 'Web Development',
          'service_value' => 'Web Development',
          'groups' => array(
              array('Web & App', array(
                  array('Landing Page', 'Rs. 12,000+'),
                  array('Full CMS Website', 'Rs. 25,000+'),
                  array('Custom Mobile App', 'Custom quote'),
              )),
          ),
      ),
  );
  ?>

  <div class="creatives-rate-dialog" data-creatives-rate-dialog hidden>
    <button class="creatives-rate-dialog-backdrop" type="button" data-creatives-rate-close aria-label="Close service rates"></button>
    <section class="creatives-rate-dialog-sheet" role="dialog" aria-modal="true" aria-labelledby="creatives-rate-dialog-title">
      <header class="creatives-rate-dialog-head">
        <div>
          <small>Services &amp; starting rates</small>
          <h2 id="creatives-rate-dialog-title" data-creatives-rate-title>Service Rates</h2>
        </div>
        <button class="creatives-rate-dialog-close" type="button" data-creatives-rate-close aria-label="Close">
          <span class="material-symbols-outlined" aria-hidden="true">close</span>
        </button>
      </header>

      <div class="creatives-rate-dialog-body">
        <?php foreach ($creative_rate_panels as $panel_key => $panel) : ?>
          <div class="creatives-rate-panel" data-creatives-rate-panel="<?php echo esc_attr($panel_key); ?>" data-service-value="<?php echo esc_attr($panel['service_value']); ?>" hidden>
            <?php foreach ($panel['groups'] as $group) : ?>
              <section class="creatives-rate-group">
                <h3><?php echo esc_html($group[0]); ?></h3>
                <dl>
                  <?php foreach ($group[1] as $rate) : ?>
                    <div>
                      <dt><?php echo esc_html($rate[0]); ?></dt>
                      <dd><?php echo esc_html($rate[1]); ?></dd>
                    </div>
                  <?php endforeach; ?>
                </dl>
              </section>
            <?php endforeach; ?>
            <p class="creatives-rate-note">Final pricing depends on scope, content volume, integrations and delivery timeline.</p>
            <button class="creatives-rate-enquire" type="button" data-creatives-rate-enquire>Enquire for this service</button>
          </div>
        <?php endforeach; ?>
      </div>
    </section>
  </div>

  <?php if ($show_extended_sections) : ?>
  <nav class="creatives-mobile-index" aria-label="IZIN Creatives page sections">
    <a href="#creatives-about">About</a>
    <a href="#creatives-clients">Clients</a>
    <a href="#creatives-process">Process</a>
    <a href="#creatives-rates">Rates</a>
    <a class="is-primary" href="#creatives-form">Enquire</a>
  </nav>

  <section class="creatives-section creatives-collapsible" id="creatives-about">
    <details class="creatives-mobile-accordion" data-mobile-collapsible>
      <summary class="creatives-mobile-summary">About IZIN Creatives</summary>
      <div class="creatives-mobile-panel">
        <div class="creatives-section-head">
          <span>About</span>
          <h2>About IZIN Creatives</h2>
          <p>IZIN Creatives is a creative and digital marketing service initiative built to help businesses present themselves clearly, communicate consistently, and manage their digital visibility through a structured service process.</p>
        </div>
        <div class="creatives-card-grid">
          <article><h3>Who We Are</h3><p>IZIN Creatives is an extended service initiative from IZIN Designs Interior Studio, created to support businesses with brand direction, social media content, digital profile setup, campaign creatives, and lead-focused digital marketing activities.</p></article>
          <article><h3>What We Support</h3><p>IZIN Creatives supports businesses that need regular content, clear digital presentation, profile optimization, campaign planning, creative production, publishing support, and monthly performance review.</p></article>
          <article><h3>How We Work</h3><p>The work begins with understanding the business, then moves into brand direction, digital profile setup, content planning, creative development, campaign execution, monitoring, and monthly improvement.</p></article>
          <article><h3>Service Approach</h3><p>IZIN Creatives does not treat digital marketing as only posting. The focus is to connect business clarity, visual consistency, content, campaigns, enquiry flow, and reporting into one managed process.</p></article>
        </div>
      </div>
    </details>
  </section>

  <section class="creatives-section creatives-collapsible" id="creatives-clients">
    <details class="creatives-mobile-accordion" data-mobile-collapsible>
      <summary class="creatives-mobile-summary">Client Groups</summary>
      <div class="creatives-mobile-panel">
        <div class="creatives-section-head">
          <span>Target Audience</span>
          <h2>Client groups IZIN Creatives can serve</h2>
          <p>The service can be structured for businesses that need regular content, better digital presentation, social media management, local visibility, lead campaigns, and marketing coordination.</p>
        </div>
        <div class="creatives-card-grid three">
          <article><h3>Interior & Design Businesses</h3><p>Interior studios, architects, modular brands, fit-out teams, renovation providers, furniture businesses, and design-related service companies.</p></article>
          <article><h3>Local Service Businesses</h3><p>Clinics, salons, educational institutes, consultancies, professional offices, repair services, and other location-based businesses.</p></article>
          <article><h3>Retail & Lifestyle Brands</h3><p>Stores, boutiques, food brands, wellness brands, product sellers, cafes, and customer-facing businesses that need regular visibility.</p></article>
          <article><h3>Startups & SMEs</h3><p>Growing businesses that need basic brand presentation, content structure, launch campaigns, and digital marketing support.</p></article>
          <article><h3>Real Estate & Construction</h3><p>Builders, developers, contractors, suppliers, property service providers, and related businesses that depend on trust and enquiries.</p></article>
          <article><h3>Professional Partners</h3><p>Consultants, agencies, freelancers, photographers, video creators, web developers, and service partners who can support delivery.</p></article>
        </div>
      </div>
    </details>
  </section>

  <section class="creatives-section creatives-collapsible" id="creatives-process">
    <details class="creatives-mobile-accordion" data-mobile-collapsible>
      <summary class="creatives-mobile-summary">Service Process</summary>
      <div class="creatives-mobile-panel">
        <div class="creatives-section-head">
          <span>Service Process</span>
          <h2>Digital marketing process flow</h2>
          <p>This delivery process shows how work moves from discovery to monthly improvement.</p>
        </div>
        <div class="creatives-flow">
          <?php
          $flow_steps = array(
              array('Business Understanding', 'Study the business type, services, products, audience, location, pricing, enquiry process, competition, and current digital presence.'),
              array('Brand Identity Direction', 'Prepare or correct logo usage, colour direction, font direction, visual style, design consistency, and communication tone.'),
              array('Digital Profile Setup', 'Correct Instagram, Facebook, Google Business Profile, WhatsApp Business, website links, contact details, and service descriptions.'),
              array('Marketing Planning', 'Plan monthly themes, content pillars, campaign direction, posting schedule, creative requirements, and lead-generation focus.'),
              array('Creative Production', 'Create social posts, carousels, reels support, WhatsApp creatives, ad creatives, captions, and platform-ready marketing material.'),
              array('Approval & Publishing', 'Share drafts, collect corrections, finalize creatives, schedule posts, publish updates, and maintain approved content flow.'),
              array('Campaign Execution', 'Set up paid campaigns, audience targeting, lead forms or WhatsApp enquiry flow, budget monitoring, and creative testing.'),
              array('Monitoring & Coordination', 'Review content response, campaign performance, enquiry quality, client feedback, business updates, and improvement points.'),
              array('Monthly Review', 'Prepare performance report, campaign summary, lead observations, digital improvement notes, and next-month action plan.'),
          );
          foreach ($flow_steps as $index => $step) :
          ?>
            <div class="creatives-flow-step">
              <span><?php echo esc_html(str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT)); ?></span>
              <h3><?php echo esc_html($step[0]); ?></h3>
              <p><?php echo esc_html($step[1]); ?></p>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </details>
  </section>

  <section class="creatives-section creatives-rates creatives-collapsible" id="creatives-rates">
    <?php
    $rate_groups = array(
        'Business Understanding' => array(
            array('Business Study', 'Study the business type, services, products, audience, location, pricing, and enquiry process.', 'Rs. 2,500'),
            array('Current Digital Presence Review', 'Review Instagram, Facebook, Google Business Profile, WhatsApp, website, and enquiry flow.', 'Rs. 3,000'),
            array('Competitor Review', 'Basic review of 3 to 5 competitors in the same business category or location.', 'Rs. 2,000'),
            array('Marketing Gap Analysis', 'Identify gaps in visibility, content, trust, enquiry flow, profile setup, and customer communication.', 'Rs. 3,000'),
            array('Business Audit Summary', 'Prepare a short report with observations and recommended digital marketing direction.', 'Rs. 2,500'),
        ),
        'Brand Identity Direction' => array(
            array('Basic Brand Direction', 'Prepare basic logo usage, colour direction, font direction, and visual style.', 'Rs. 5,000'),
            array('Social Media Visual Style', 'Define post style, carousel style, reel cover style, ad creative style, and design consistency.', 'Rs. 4,000'),
            array('Communication Tone Direction', 'Define caption tone, customer-facing language, call-to-action style, and message clarity.', 'Rs. 3,000'),
            array('Brand Guideline Sheet', 'Prepare a simple brand direction sheet for digital use.', 'Rs. 6,000'),
            array('Content Language Direction', 'Define whether the brand should communicate in English, Malayalam, Manglish, or mixed style.', 'Rs. 2,000'),
        ),
        'Digital Profile Setup' => array(
            array('Instagram Profile Optimization', 'Correct bio, profile image, contact button, highlights, service clarity, and link structure.', 'Rs. 2,500'),
            array('Facebook Page Setup', 'Correct page category, cover image, about section, contact details, service details, and page structure.', 'Rs. 2,500'),
            array('Google Business Profile Setup', 'Optimize category, description, services, photos, location, contact, and business details.', 'Rs. 4,000'),
            array('WhatsApp Business Setup', 'Set up profile, catalogue, greeting message, quick replies, and enquiry flow.', 'Rs. 2,500'),
            array('Website / Landing Page Review', 'Review website links, WhatsApp button, enquiry form, contact visibility, and basic landing flow.', 'Rs. 3,000'),
            array('Complete Profile Setup Package', 'Setup or correct Instagram, Facebook, Google Business Profile, WhatsApp Business, and basic links.', 'Rs. 10,000'),
        ),
        'Marketing Planning' => array(
            array('Monthly Content Calendar', 'Plan monthly themes, post topics, content pillars, campaign ideas, and posting schedule.', 'Rs. 4,000'),
            array('Content Pillar Planning', 'Define content categories such as business showcase, education, trust, offers, FAQs, and lead content.', 'Rs. 2,500'),
            array('Campaign Direction Plan', 'Plan campaign objective, service focus, offer, audience, location, budget direction, and lead flow.', 'Rs. 3,000'),
            array('Creative Requirement List', 'Prepare list of required photos, videos, captions, ad creatives, reels, and post formats.', 'Rs. 2,000'),
            array('Monthly Marketing Plan', 'Prepare complete monthly marketing direction with content, campaign, and execution priorities.', 'Rs. 6,000'),
        ),
        'Creative Production & Publishing' => array(
            array('Social Media Poster', 'Static post design for services, offers, announcements, brand visibility, or customer awareness.', 'Rs. 500'),
            array('Carousel Design', 'Multi-slide carousel for education, service explanation, package details, or offer communication.', 'Rs. 1,500'),
            array('Reel Support', 'Reel idea, cover design, caption, text overlay direction, and basic edit support.', 'Rs. 1,000'),
            array('WhatsApp Creative', 'Offer poster, service-sharing creative, festival creative, enquiry creative, or customer update creative.', 'Rs. 400'),
            array('Ad Creative', 'Meta ad poster, carousel ad, lead ad creative, or campaign visual.', 'Rs. 800'),
            array('Caption Writing', 'Caption with service explanation, location focus, call-to-action, and hashtags where required.', 'Rs. 250'),
            array('Monthly Creative Set', '12 static posts, 4 carousels, 4 WhatsApp creatives, and captions.', 'Rs. 12,000'),
            array('Monthly Publishing Management', 'Manage approval, scheduling, posting, and Google Business updates for the month.', 'Rs. 5,000'),
        ),
        'Campaign Execution & Monitoring' => array(
            array('Meta Campaign Setup', 'Set up Facebook and Instagram campaign with objective, audience, placement, budget, copy, and creative.', 'Rs. 4,000'),
            array('Lead Form Setup', 'Create lead form with customer questions, contact fields, and enquiry structure.', 'Rs. 2,000'),
            array('WhatsApp Enquiry Campaign Setup', 'Set up WhatsApp campaign flow, message direction, CTA, and enquiry route.', 'Rs. 2,500'),
            array('Campaign Monitoring', 'Monitor budget usage, leads, cost per lead, creative response, and basic adjustments.', 'Rs. 5,000'),
            array('Monthly Campaign Management', 'Set up, monitor, test, and review paid campaign activity for the month.', 'Rs. 8,000'),
        ),
        'Monthly Review & Package Conversion' => array(
            array('Monthly Performance Report', 'Prepare report covering content output, campaign result, leads, observations, and next actions.', 'Rs. 3,000'),
            array('Foundation Setup', 'Includes business understanding, brand direction, and digital profile setup.', 'Rs. 15,000'),
            array('Monthly Content Management', 'Includes planning, creatives, publishing, and monthly review.', 'Rs. 18,000 / month'),
            array('Lead Campaign Support', 'Includes planning, creatives, campaigns, monitoring, and review.', 'Rs. 25,000 / month'),
            array('Full Digital Management', 'Includes all process stages as a complete digital marketing management process.', 'Rs. 40,000 / month'),
        ),
    );

    ?>
    <details class="creatives-mobile-accordion creatives-mobile-accordion-rates" data-mobile-collapsible>
      <summary class="creatives-mobile-summary">Rate Card</summary>
      <div class="creatives-mobile-panel">
        <div class="creatives-section-head">
          <span>Rate Card</span>
          <h2>Digital marketing process rate card</h2>
          <p>Starting rates only. Final pricing may change based on business category, number of platforms, content volume, urgency, quality requirement, and campaign scope.</p>
        </div>

        <div class="creatives-rate-mobile-summary">
          <article><strong>Foundation Setup</strong><span>Business understanding, brand direction, and profile setup.</span><em>Rs. 15,000</em></article>
          <article><strong>Monthly Content Management</strong><span>Planning, creatives, publishing, and monthly review.</span><em>Rs. 18,000 / month</em></article>
          <article><strong>Lead Campaign Support</strong><span>Planning, creatives, campaigns, monitoring, and review.</span><em>Rs. 25,000 / month</em></article>
          <article><strong>Full Digital Management</strong><span>Complete digital marketing management process.</span><em>Rs. 40,000 / month</em></article>
        </div>

        <details class="creatives-rate-mobile-detail">
          <summary>View detailed service breakdown</summary>
          <div class="creatives-rate-mobile-detail-inner">
            <?php foreach ($rate_groups as $group_title => $rows) : ?>
              <div class="creatives-rate-table">
                <h3><?php echo esc_html($group_title); ?></h3>
                <div class="creatives-rate-row is-head"><span>Service</span><span>Description</span><span>Price</span></div>
                <?php foreach ($rows as $row) : ?>
                  <div class="creatives-rate-row">
                    <strong><?php echo esc_html($row[0]); ?></strong>
                    <span><?php echo esc_html($row[1]); ?></span>
                    <em><?php echo esc_html($row[2]); ?></em>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php endforeach; ?>
          </div>
        </details>
      </div>
    </details>
  </section>
  <?php endif; ?>

  <section class="creatives-section" id="creatives-form">
    <div class="creatives-enquiry-title">
      <strong>PROJECT ENQUIRY:</strong>
      <span>Tell us what you need</span>
    </div>

    <div class="creatives-process-rail" aria-label="Service process">
      <?php foreach (array('Requirement Shared', 'Team Review', 'Scope & Quote', 'Start') as $index => $label) : ?>
        <article class="creatives-process-step">
          <strong><?php echo esc_html(str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT)); ?></strong>
          <span><?php echo esc_html($label); ?></span>
        </article>
      <?php endforeach; ?>
    </div>

    <form class="career-form-card creatives-form-card" data-izin-creatives-form>
      <input class="izin-honeypot" type="text" name="website" tabindex="-1" autocomplete="off" aria-hidden="true">
      <div class="creatives-form-progress" aria-label="Form steps">
        <article class="creatives-form-progress-step is-active" data-step-indicator="1">
          <strong>Step 1</strong>
          <span>Requirement</span>
        </article>
        <article class="creatives-form-progress-step" data-step-indicator="2">
          <strong>Step 2</strong>
          <span>Contact</span>
        </article>
      </div>

      <section class="creatives-form-stage is-active" data-form-stage="1">
        <div class="creatives-form-stage-head">
          <span>Step 1 of 2</span>
          <h3>Requirement Details</h3>
        </div>
        <div class="career-form-grid">
          <label><span>Service Needed *</span>
            <select name="service_needed" required data-step-required="1">
              <option value="">Select a service</option>
              <option value="Graphic Design">Graphic Design</option>
              <option value="Digital Marketing">Digital Marketing</option>
              <option value="Web Development">Web Development</option>
              <option value="Complete Digital Support">Complete Digital Support</option>
            </select>
          </label>
          <label><span>Business / Brand Name *</span><input type="text" name="business_name" required data-step-required="1"></label>
        </div>
        <div class="career-form-grid">
          <label><span>Service Location *</span><input type="text" name="service_location" required data-step-required="1"></label>
          <label><span>Monthly Budget *</span>
            <select name="monthly_ad_budget" required data-step-required="1">
              <option value="">Select a range</option>
              <option value="Below Rs. 15,000">Below Rs. 15,000</option>
              <option value="Rs. 15,000 - Rs. 25,000">Rs. 15,000 - Rs. 25,000</option>
              <option value="Rs. 25,000 - Rs. 40,000">Rs. 25,000 - Rs. 40,000</option>
              <option value="Above Rs. 40,000">Above Rs. 40,000</option>
              <option value="Need guidance">Need guidance</option>
            </select>
          </label>
        </div>

        <div class="creatives-form-actions is-next">
          <button class="career-submit is-secondary" type="button" data-step-next>Next</button>
        </div>
      </section>

      <section class="creatives-form-stage" data-form-stage="2" hidden>
        <div class="creatives-form-stage-head">
          <span>Step 2 of 2</span>
          <h3>Contact Details</h3>
        </div>
        <div class="career-form-grid">
          <label><span>Contact Person *</span><input type="text" name="contact_person" required data-step-required="2"></label>
          <label><span>Phone / WhatsApp *</span><input type="tel" name="phone" required data-step-required="2"></label>
        </div>
        <div class="career-form-grid">
          <label><span>Email</span><input type="email" name="email"></label>
        </div>

        <details class="creatives-optional-details">
          <summary>More details (optional)</summary>
          <div class="creatives-optional-body">
            <div class="career-form-grid">
              <label><span>Business Category</span><input type="text" name="business_category"></label>
              <label><span>Expected Start Date</span><input type="text" name="expected_start_date"></label>
            </div>
            <label><span>Main Goal</span><textarea name="main_goal" rows="3"></textarea></label>
            <label><span>Main Products / Services Offered</span><textarea name="services_offered" rows="3"></textarea></label>
            <label><span>Target Customer</span><textarea name="target_customer" rows="3"></textarea></label>

            <div class="career-form-grid">
              <label><span>Instagram Link / Username</span><input type="text" name="instagram_url"></label>
              <label><span>Facebook Page Link</span><input type="text" name="facebook_url"></label>
            </div>
            <div class="career-form-grid">
              <label><span>Google Business Profile</span><input type="text" name="google_profile"></label>
              <label><span>Website / Landing Page</span><input type="text" name="website_url"></label>
            </div>

            <fieldset class="creatives-checkset">
              <legend>Required Support</legend>
              <label><input type="checkbox" name="required_support[]" value="Profile setup"> Profile setup</label>
              <label><input type="checkbox" name="required_support[]" value="Content planning"> Content planning</label>
              <label><input type="checkbox" name="required_support[]" value="Creative design"> Creative design</label>
              <label><input type="checkbox" name="required_support[]" value="Posting support"> Posting support</label>
              <label><input type="checkbox" name="required_support[]" value="Paid ads"> Paid ads</label>
              <label><input type="checkbox" name="required_support[]" value="Monthly report"> Monthly report</label>
            </fieldset>

            <div class="career-form-grid">
              <label><span>Preferred Language</span><input type="text" name="preferred_language"></label>
              <label><span>Preferred Communication Channel</span><input type="text" name="preferred_channel"></label>
            </div>
            <label><span>Offer / Campaign Focus</span><textarea name="campaign_focus" rows="3"></textarea></label>
            <label><span>Notes / Specific Requirements</span><textarea name="notes" rows="4"></textarea></label>

            <fieldset class="creatives-checkset">
              <legend>Content & Access Readiness</legend>
              <label><input type="checkbox" name="content_assets[]" value="Logo available"> Logo available</label>
              <label><input type="checkbox" name="content_assets[]" value="Brand colours available"> Brand colours available</label>
              <label><input type="checkbox" name="content_assets[]" value="Business photos available"> Business photos available</label>
              <label><input type="checkbox" name="content_assets[]" value="Videos available"> Videos available</label>
              <label><input type="checkbox" name="content_assets[]" value="Social media access ready"> Social media access ready</label>
              <label><input type="checkbox" name="content_assets[]" value="Ad account access ready"> Ad account access ready</label>
            </fieldset>

            <div class="career-form-grid">
              <label><span>Content Approval Person</span><input type="text" name="content_approval_person"></label>
              <label><span>Lead Follow-up Person</span><input type="text" name="lead_followup_person"></label>
            </div>
            <label><span>Preferred Reporting Date</span><input type="text" name="preferred_reporting_date"></label>
          </div>
        </details>

        <div class="creatives-form-actions">
          <button class="career-submit is-secondary" type="button" data-step-back>Back</button>
          <button class="career-submit" type="submit">Submit Requirement</button>
        </div>
      </section>

      <p class="career-form-status" data-izin-creatives-status aria-live="polite"></p>
    </form>
  </section>
</main>

<?php get_template_part('template-parts/site-footer'); ?>

<?php
get_footer();
