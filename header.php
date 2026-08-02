<?php
defined('ABSPATH') || exit;
$clinic_name = get_option('mdbk_clinic_name', get_bloginfo('name'));
$clinic_logo_id = (int) get_option('mdbk_clinic_logo', 0);
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<header class="medbook-header">
    <div class="medbook-header-inner">
        <a class="medbook-brand" href="<?php echo esc_url(home_url('/')); ?>">
            <?php if ($clinic_logo_id) : ?>
                <?php echo wp_get_attachment_image($clinic_logo_id, array(36, 36), false, array('alt' => esc_attr($clinic_name))); ?>
            <?php else : ?>
                <span class="medbook-brand-mark">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.29 1.51 4.04 3 5.5l7 7Z"></path></svg>
                </span>
            <?php endif; ?>
            <span><?php echo esc_html($clinic_name); ?></span>
        </a>

        <button type="button" class="medbook-nav-toggle" id="medbook-nav-toggle" aria-label="<?php esc_attr_e('Menu', 'medbook'); ?>" aria-expanded="false">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
        </button>

        <nav class="medbook-nav" id="medbook-nav">
            <?php
            if (has_nav_menu('medbook-primary')) {
                wp_nav_menu(array(
                    'theme_location' => 'medbook-primary',
                    'container'      => false,
                    'menu_class'     => '',
                    'fallback_cb'    => false,
                ));
            }
            ?>
        </nav>
    </div>
</header>
