<?php

if (!defined('ABSPATH')) {
    exit;
}


function usegeo_scan_substr($text, $length) {
    if (function_exists('mb_substr')) {
        return mb_substr($text, 0, $length);
    }
    return substr($text, 0, $length);
}

function usegeo_scan_dom_text($node) {
    return trim(preg_replace('/\s+/', ' ', html_entity_decode($node->textContent, ENT_QUOTES | ENT_HTML5, 'UTF-8')));
}

function usegeo_scan_meta_content(DOMXPath $xpath, $name) {
    $query = sprintf('//meta[translate(@name,"ABCDEFGHIJKLMNOPQRSTUVWXYZ","abcdefghijklmnopqrstuvwxyz")="%s"]/@content', strtolower($name));
    $nodes = $xpath->query($query);
    if ($nodes && $nodes->length) {
        return trim($nodes->item(0)->nodeValue);
    }
    return '';
}

function usegeo_scan_collect_headings(DOMXPath $xpath, $tag, $limit) {
    $items = array();
    $nodes = $xpath->query('//' . $tag);
    if ($nodes) {
        foreach ($nodes as $node) {
            $text = usegeo_scan_dom_text($node);
            if ($text !== '') {
                $items[] = usegeo_scan_substr($text, 512);
            }
            if (count($items) >= $limit) {
                break;
            }
        }
    }
    return $items;
}

function usegeo_scan_schema_types(DOMXPath $xpath) {
    $types = array();
    $nodes = $xpath->query('//script[@type="application/ld+json"]');
    if (!$nodes) {
        return $types;
    }

    foreach ($nodes as $node) {
        $json = trim($node->textContent);
        $decoded = json_decode($json, true);
        if (!$decoded) {
            continue;
        }
        $stack = is_array($decoded) ? array($decoded) : array();
        while ($stack) {
            $item = array_pop($stack);
            if (!is_array($item)) {
                continue;
            }
            if (isset($item['@type'])) {
                foreach ((array) $item['@type'] as $type) {
                    $types[] = sanitize_text_field((string) $type);
                }
            }
            foreach ($item as $value) {
                if (is_array($value)) {
                    $stack[] = $value;
                }
            }
        }
    }

    return array_values(array_unique(array_slice($types, 0, 50)));
}

function usegeo_scan_detect_signals($text, $schema_types, $canonical, $robots) {
    $lower = strtolower($text);
    $contains_any = function ($needles) use ($lower) {
        foreach ($needles as $needle) {
            if (strpos($lower, $needle) !== false) {
                return true;
            }
        }
        return false;
    };

    return array(
        'has_case_study_terms' => $contains_any(array('case study', 'case studies', 'customer story', 'results')),
        'has_customer_terms' => $contains_any(array('customer', 'client', 'testimonial', 'trusted by')),
        'has_comparison_terms' => $contains_any(array('compare', 'comparison', 'alternative', 'alternatives', 'versus', ' vs ')),
        'has_pricing_terms' => $contains_any(array('pricing', 'plans', 'cost', 'roi')),
        'has_use_case_terms' => $contains_any(array('use case', 'use cases', 'for teams', 'for agencies', 'for b2b')),
        'has_faq_schema' => in_array('FAQPage', $schema_types, true),
        'has_organization_schema' => in_array('Organization', $schema_types, true) || in_array('Corporation', $schema_types, true),
        'has_canonical' => (bool) $canonical,
        'is_indexable' => strpos(strtolower($robots), 'noindex') === false,
    );
}

function usegeo_scan_parse_html($html, $final_url = '') {
    if (!class_exists('DOMDocument')) {
        return new WP_Error('parse_failed', 'We could not analyze this page. Please try a different URL.');
    }

    $previous = libxml_use_internal_errors(true);
    $dom = new DOMDocument();
    $loaded = $dom->loadHTML('<?xml encoding="utf-8" ?>' . $html);
    libxml_clear_errors();
    libxml_use_internal_errors($previous);

    if (!$loaded) {
        return new WP_Error('parse_failed', 'We could not analyze this page. Please try a different URL.');
    }

    $xpath = new DOMXPath($dom);
    $title_nodes = $xpath->query('//title');
    $title = ($title_nodes && $title_nodes->length) ? usegeo_scan_dom_text($title_nodes->item(0)) : '';
    $meta_description = usegeo_scan_meta_content($xpath, 'description');
    $robots = usegeo_scan_meta_content($xpath, 'robots');
    $h1 = usegeo_scan_collect_headings($xpath, 'h1', 10);
    $h2 = usegeo_scan_collect_headings($xpath, 'h2', 30);

    $canonical = null;
    $canonical_nodes = $xpath->query('//link[translate(@rel,"ABCDEFGHIJKLMNOPQRSTUVWXYZ","abcdefghijklmnopqrstuvwxyz")="canonical"]/@href');
    if ($canonical_nodes && $canonical_nodes->length) {
        $canonical = esc_url_raw($canonical_nodes->item(0)->nodeValue);
    }

    $schema_types = usegeo_scan_schema_types($xpath);
    $body_nodes = $xpath->query('//body');
    $body_text = ($body_nodes && $body_nodes->length) ? usegeo_scan_dom_text($body_nodes->item(0)) : usegeo_scan_dom_text($dom->documentElement);
    $word_count = str_word_count(wp_strip_all_tags($body_text));
    $detected_signals = usegeo_scan_detect_signals($body_text . ' ' . $title . ' ' . $meta_description, $schema_types, $canonical, $robots);

    if ($title === '' && empty($h1)) {
        return new WP_Error('parse_failed', 'We could not analyze this page. Please try a different URL.');
    }

    return array(
        'title' => usegeo_scan_substr($title, 512),
        'meta_description' => usegeo_scan_substr($meta_description, 1024),
        'h1' => $h1,
        'h2' => $h2,
        'schema_types' => $schema_types,
        'canonical' => $canonical,
        'word_count' => max(0, (int) $word_count),
        'detected_signals' => $detected_signals,
        '_body_excerpt' => usegeo_scan_substr($body_text, 4000),
        '_robots' => $robots,
        '_final_url' => $final_url,
    );
}
