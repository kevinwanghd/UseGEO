<?php

if (!defined('ABSPATH')) {
    exit;
}

function usegeo_asset_version($relative_path) {
    $absolute_path = get_template_directory() . $relative_path;

    if (!file_exists($absolute_path)) {
        return null;
    }

    return (string) filemtime($absolute_path);
}


function usegeo_get_gtm_id() {
    if (defined('USEGEO_GTM_ID') && USEGEO_GTM_ID) {
        return (string) USEGEO_GTM_ID;
    }

    return (string) apply_filters('usegeo_gtm_id', '');
}


function usegeo_get_ga4_measurement_id() {
    if (defined('USEGEO_GA4_MEASUREMENT_ID') && USEGEO_GA4_MEASUREMENT_ID) {
        return (string) USEGEO_GA4_MEASUREMENT_ID;
    }

    return (string) apply_filters('usegeo_ga4_measurement_id', '');
}


function usegeo_enqueue_assets() {
    $theme_uri = get_template_directory_uri();

    wp_enqueue_style(
        'usegeo-fonts',
        'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap',
        array(),
        null
    );

    wp_enqueue_style(
        'usegeo-base',
        $theme_uri . '/assets/css/base.css',
        array(),
        usegeo_asset_version('/assets/css/base.css')
    );

    wp_enqueue_script(
        'usegeo-tracking',
        $theme_uri . '/assets/js/tracking.js',
        array(),
        usegeo_asset_version('/assets/js/tracking.js'),
        true
    );

    wp_enqueue_script(
        'usegeo-mobile-nav',
        $theme_uri . '/assets/js/mobile-nav.js',
        array(),
        usegeo_asset_version('/assets/js/mobile-nav.js'),
        true
    );

    if (is_front_page()) {
        wp_enqueue_style(
            'usegeo-homepage',
            $theme_uri . '/assets/css/homepage.css',
            array('usegeo-base'),
            usegeo_asset_version('/assets/css/homepage.css')
        );

        wp_enqueue_script(
            'usegeo-scanner',
            $theme_uri . '/assets/js/scanner.js',
            array(),
            usegeo_asset_version('/assets/js/scanner.js'),
            true
        );

        wp_enqueue_script(
            'usegeo-roi',
            $theme_uri . '/assets/js/roi.js',
            array(),
            usegeo_asset_version('/assets/js/roi.js'),
            true
        );
    }
}
add_action('wp_enqueue_scripts', 'usegeo_enqueue_assets');


function usegeo_output_gtm_head() {
    $gtm_id = trim(usegeo_get_gtm_id());

    if (!$gtm_id) {
        return;
    }
    ?>
    <!-- Google Tag Manager -->
    <script>
        (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start': new Date().getTime(),event:'gtm.js'});
        var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','<?php echo esc_js($gtm_id); ?>');
    </script>
    <!-- End Google Tag Manager -->
    <?php
}
add_action('wp_head', 'usegeo_output_gtm_head', 1);


function usegeo_output_gtm_body() {
    $gtm_id = trim(usegeo_get_gtm_id());

    if (!$gtm_id) {
        return;
    }
    ?>
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?php echo esc_attr($gtm_id); ?>"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
    <?php
}
add_action('wp_body_open', 'usegeo_output_gtm_body', 1);


function usegeo_output_ga4_tag() {
    $measurement_id = trim(usegeo_get_ga4_measurement_id());

    if (!$measurement_id || usegeo_get_gtm_id()) {
        return;
    }
    ?>
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo esc_attr($measurement_id); ?>"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '<?php echo esc_js($measurement_id); ?>');
    </script>
    <?php
}
add_action('wp_head', 'usegeo_output_ga4_tag', 2);

