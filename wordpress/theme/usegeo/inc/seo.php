<?php

if (!defined('ABSPATH')) {
    exit;
}

function usegeo_get_schema_site_url() {
    return home_url('/');
}

function usegeo_get_schema_description($post_id = 0) {
    if (is_front_page()) {
        return __('Scan your B2B website for GEO readiness and unlock a prioritized AI visibility action report without a sales call or subscription.', 'usegeo');
    }
    if ($post_id) {
        $source = has_excerpt($post_id) ? get_the_excerpt($post_id) : wp_strip_all_tags(get_post_field('post_content', $post_id));
        return wp_trim_words($source, 30, '');
    }

    $description = get_bloginfo('description', 'display');

    if ($description) {
        return $description;
    }

    return __('UseGEO helps B2B teams learn generative engine optimization, run a lightweight GEO scanner, and evaluate partner follow-up without hype.', 'usegeo');
}

function usegeo_schema_clean_text($text) {
    $text = wp_strip_all_tags((string) $text);
    $text = html_entity_decode($text, ENT_QUOTES, get_bloginfo('charset'));
    $text = preg_replace('/\s+/', ' ', $text);
    return trim($text);
}

function usegeo_schema_current_url() {
    if (is_singular()) {
        return get_permalink();
    }

    if (is_front_page()) {
        return home_url('/');
    }

    global $wp;
    $path = isset($wp->request) ? $wp->request : '';
    return home_url($path ? '/' . ltrim($path, '/') . '/' : '/');
}

function usegeo_extract_visible_faq_items($post_id) {
    $content = get_post_field('post_content', $post_id);

    if (!$content || stripos($content, 'FAQ') === false) {
        return array();
    }

    $rendered = apply_filters('the_content', $content);
    $faq_position = stripos($rendered, 'FAQ');

    if ($faq_position === false) {
        return array();
    }

    $faq_html = substr($rendered, $faq_position);
    $items = array();

    if (!preg_match_all('/<h([23])[^>]*>(.*?)<\/h\1>(.*?)(?=<h[23][^>]*>|$)/is', $faq_html, $matches, PREG_SET_ORDER)) {
        return array();
    }

    foreach ($matches as $match) {
        $question = usegeo_schema_clean_text($match[2]);
        $answer = usegeo_schema_clean_text($match[3]);

        if (!$question || strpos($question, '?') === false || !$answer) {
            continue;
        }

        $items[] = array(
            '@type' => 'Question',
            'name' => $question,
            'acceptedAnswer' => array(
                '@type' => 'Answer',
                'text' => wp_trim_words($answer, 90, ''),
            ),
        );

        if (count($items) >= 8) {
            break;
        }
    }

    return $items;
}

function usegeo_build_organization_schema() {
    return array(
        '@type' => 'Organization',
        '@id' => usegeo_get_schema_site_url() . '#organization',
        'name' => 'UseGEO',
        'url' => usegeo_get_schema_site_url(),
        'description' => usegeo_get_schema_description(),
    );
}

function usegeo_build_website_schema() {
    return array(
        '@type' => 'WebSite',
        '@id' => usegeo_get_schema_site_url() . '#website',
        'url' => usegeo_get_schema_site_url(),
        'name' => get_bloginfo('name') ?: 'UseGEO',
        'description' => usegeo_get_schema_description(),
        'publisher' => array('@id' => usegeo_get_schema_site_url() . '#organization'),
        'inLanguage' => get_bloginfo('language'),
    );
}

function usegeo_build_webpage_schema($post_id = 0) {
    $url = $post_id ? get_permalink($post_id) : usegeo_schema_current_url();
    $name = $post_id ? get_the_title($post_id) : wp_get_document_title();

    return array(
        '@type' => 'WebPage',
        '@id' => trailingslashit($url) . '#webpage',
        'url' => $url,
        'name' => usegeo_schema_clean_text($name),
        'description' => usegeo_get_schema_description($post_id),
        'isPartOf' => array('@id' => usegeo_get_schema_site_url() . '#website'),
        'publisher' => array('@id' => usegeo_get_schema_site_url() . '#organization'),
        'inLanguage' => get_bloginfo('language'),
    );
}

function usegeo_build_article_schema($post_id) {
    return array(
        '@type' => 'Article',
        '@id' => trailingslashit(get_permalink($post_id)) . '#article',
        'mainEntityOfPage' => array('@id' => trailingslashit(get_permalink($post_id)) . '#webpage'),
        'headline' => usegeo_schema_clean_text(get_the_title($post_id)),
        'description' => usegeo_get_schema_description($post_id),
        'datePublished' => get_the_date(DATE_W3C, $post_id),
        'dateModified' => get_the_modified_date(DATE_W3C, $post_id),
        'author' => array('@id' => usegeo_get_schema_site_url() . '#organization'),
        'publisher' => array('@id' => usegeo_get_schema_site_url() . '#organization'),
        'inLanguage' => get_bloginfo('language'),
    );
}

function usegeo_build_faq_schema($post_id) {
    $items = usegeo_extract_visible_faq_items($post_id);

    if (!$items) {
        return null;
    }

    return array(
        '@type' => 'FAQPage',
        '@id' => trailingslashit(get_permalink($post_id)) . '#faq',
        'mainEntity' => $items,
    );
}

function usegeo_output_json_ld() {
    if (is_admin() || is_404() || is_search()) {
        return;
    }

    $post_id = is_singular() ? get_queried_object_id() : 0;
    $graph = array(
        usegeo_build_organization_schema(),
        usegeo_build_website_schema(),
        usegeo_build_webpage_schema($post_id),
    );

    if ($post_id && is_single()) {
        $graph[] = usegeo_build_article_schema($post_id);
    }

    if ($post_id) {
        $faq_schema = usegeo_build_faq_schema($post_id);
        if ($faq_schema) {
            $graph[] = $faq_schema;
        }
    }

    $payload = array(
        '@context' => 'https://schema.org',
        '@graph' => array_values(array_filter($graph)),
    );
    ?>
    <script type="application/ld+json"><?php echo wp_json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?></script>
    <?php
}
add_action('wp_head', 'usegeo_output_json_ld', 6);

function usegeo_front_page_document_title($title) {
    return is_front_page() ? 'UseGEO — Automated GEO Readiness Scanner & Action Report' : $title;
}
add_filter('pre_get_document_title', 'usegeo_front_page_document_title', 20);

function usegeo_output_front_page_meta() {
    if (!is_front_page()) {
        return;
    }
    ?>
    <meta name="description" content="Scan your B2B website for GEO readiness and unlock a prioritized AI visibility action report without a sales call or subscription.">
    <meta property="og:title" content="UseGEO — Automated GEO Readiness Scanner & Action Report">
    <meta property="og:description" content="Turn homepage evidence into a focused 30-day GEO action plan.">
    <?php
}
add_action('wp_head', 'usegeo_output_front_page_meta', 5);
