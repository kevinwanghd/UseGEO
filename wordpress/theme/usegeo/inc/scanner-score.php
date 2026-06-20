<?php

if (!defined('ABSPATH')) {
    exit;
}

function usegeo_scan_clamp_score($score) {
    return max(0, min(100, (int) round($score)));
}

function usegeo_scan_signal_score($hits, $total) {
    if ($total <= 0) {
        return 0;
    }
    return usegeo_scan_clamp_score(($hits / $total) * 100);
}

function usegeo_scan_calculate_scores($extracted) {
    $signals = isset($extracted['detected_signals']) && is_array($extracted['detected_signals']) ? $extracted['detected_signals'] : array();

    $entity_hits = 0;
    $entity_hits += !empty($extracted['title']) ? 1 : 0;
    $entity_hits += !empty($extracted['h1']) ? 1 : 0;
    $entity_hits += !empty($extracted['meta_description']) ? 1 : 0;
    $entity_hits += !empty($signals['has_organization_schema']) ? 1 : 0;

    $structure_hits = 0;
    $structure_hits += !empty($extracted['h1']) ? 1 : 0;
    $structure_hits += (isset($extracted['h2']) && count($extracted['h2']) >= 2) ? 1 : 0;
    $structure_hits += (isset($extracted['word_count']) && (int) $extracted['word_count'] >= 300) ? 1 : 0;
    $structure_hits += !empty($signals['has_canonical']) ? 1 : 0;

    $proof_hits = 0;
    foreach (array('has_case_study_terms', 'has_customer_terms', 'has_pricing_terms') as $key) {
        $proof_hits += !empty($signals[$key]) ? 1 : 0;
    }

    $comparison_hits = 0;
    foreach (array('has_comparison_terms', 'has_use_case_terms', 'has_pricing_terms') as $key) {
        $comparison_hits += !empty($signals[$key]) ? 1 : 0;
    }

    $extractability_hits = 0;
    $extractability_hits += !empty($signals['is_indexable']) ? 1 : 0;
    $extractability_hits += !empty($extracted['title']) ? 1 : 0;
    $extractability_hits += !empty($extracted['canonical']) ? 1 : 0;
    $extractability_hits += !empty($extracted['schema_types']) ? 1 : 0;
    $extractability_hits += !empty($extracted['h1']) ? 1 : 0;

    $scores = array(
        'entity_clarity' => usegeo_scan_signal_score($entity_hits, 4),
        'content_structure' => usegeo_scan_signal_score($structure_hits, 4),
        'proof_signals' => usegeo_scan_signal_score($proof_hits, 3),
        'comparison_readiness' => usegeo_scan_signal_score($comparison_hits, 3),
        'ai_extractability' => usegeo_scan_signal_score($extractability_hits, 5),
    );

    $weights = apply_filters('usegeo_scan_dimension_weights', array(
        'entity_clarity' => 0.2,
        'content_structure' => 0.2,
        'proof_signals' => 0.2,
        'comparison_readiness' => 0.2,
        'ai_extractability' => 0.2,
    ));

    $total = 0;
    $weight_total = 0;
    foreach ($scores as $dimension => $score) {
        $weight = isset($weights[$dimension]) ? (float) $weights[$dimension] : 0.2;
        $total += $score * $weight;
        $weight_total += $weight;
    }
    $scores['total'] = usegeo_scan_clamp_score($weight_total > 0 ? $total / $weight_total : array_sum($scores) / 5);

    return array(
        'total' => $scores['total'],
        'entity_clarity' => $scores['entity_clarity'],
        'content_structure' => $scores['content_structure'],
        'proof_signals' => $scores['proof_signals'],
        'comparison_readiness' => $scores['comparison_readiness'],
        'ai_extractability' => $scores['ai_extractability'],
    );
}

function usegeo_scan_grade($total) {
    if ($total >= 80) {
        return 'Strong readiness';
    }
    if ($total >= 65) {
        return 'Emerging readiness';
    }
    return 'Early readiness';
}

function usegeo_scan_generate_findings($extracted, $scores) {
    $findings = array();
    $signals = isset($extracted['detected_signals']) ? $extracted['detected_signals'] : array();

    if (empty($extracted['title']) || empty($extracted['h1'])) {
        $findings[] = array(
            'severity' => 'high',
            'dimension' => 'entity_clarity',
            'title' => 'Brand and category clarity needs work',
            'evidence' => 'The scan could not find both a clear page title and H1 on the homepage.',
            'recommendation' => 'Make the homepage title and H1 clearly state who you help, what problem you solve, and the category you belong in.',
        );
    }

    if (empty($signals['has_comparison_terms'])) {
        $findings[] = array(
            'severity' => 'high',
            'dimension' => 'comparison_readiness',
            'title' => 'Comparison coverage appears weak',
            'evidence' => 'No comparison, alternative, versus, or competitor-related language was detected on the homepage.',
            'recommendation' => 'Add comparison or alternative pages for high-intent AI recommendation questions.',
        );
    }

    if (empty($signals['has_case_study_terms']) && empty($signals['has_customer_terms'])) {
        $findings[] = array(
            'severity' => 'medium',
            'dimension' => 'proof_signals',
            'title' => 'Proof signals are limited',
            'evidence' => 'The homepage did not show clear customer, case study, testimonial, or result language.',
            'recommendation' => 'Add customer outcomes, case studies, or proof snippets that AI systems can safely reuse.',
        );
    }

    if (empty($extracted['schema_types'])) {
        $findings[] = array(
            'severity' => 'medium',
            'dimension' => 'ai_extractability',
            'title' => 'Structured data was not detected',
            'evidence' => 'No JSON-LD schema types were detected on the homepage.',
            'recommendation' => 'Add Organization and FAQ schema where appropriate to improve machine readability.',
        );
    }

    if (isset($extracted['word_count']) && (int) $extracted['word_count'] < 300) {
        $findings[] = array(
            'severity' => 'low',
            'dimension' => 'content_structure',
            'title' => 'Homepage copy may be too thin',
            'evidence' => 'The homepage contains fewer than 300 detected words.',
            'recommendation' => 'Add concise but specific sections for use cases, proof, comparisons, and next steps.',
        );
    }

    if (count($findings) < 3) {
        $findings[] = array(
            'severity' => 'low',
            'dimension' => 'content_structure',
            'title' => 'Core structure is present',
            'evidence' => 'The homepage includes title, headings, or enough text for a baseline directional scan.',
            'recommendation' => 'Prioritize the weakest scoring dimension before adding broad content volume.',
        );
    }

    return array_slice($findings, 0, 5);
}
