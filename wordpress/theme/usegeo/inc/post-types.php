<?php

if (!defined('ABSPATH')) {
    exit;
}

function usegeo_register_post_types() {
    register_post_type('usegeo_lead', array(
        'labels' => array(
            'name'          => __('UseGEO Leads', 'usegeo'),
            'singular_name' => __('UseGEO Lead', 'usegeo'),
        ),
        'public'              => false,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'menu_position'       => 25,
        'menu_icon'           => 'dashicons-email-alt',
        'supports'            => array('title'),
        'capability_type'     => 'post',
        'has_archive'         => false,
        'rewrite'             => false,
        'query_var'           => false,
        'publicly_queryable'  => false,
        'exclude_from_search' => true,
        'show_in_rest'        => false,
    ));

    register_post_type('usegeo_case', array(
        'labels' => array(
            'name'          => __('UseGEO Case Submissions', 'usegeo'),
            'singular_name' => __('UseGEO Case Submission', 'usegeo'),
        ),
        'public'              => false,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'menu_position'       => 26,
        'menu_icon'           => 'dashicons-portfolio',
        'supports'            => array('title'),
        'capability_type'     => 'post',
        'has_archive'         => false,
        'rewrite'             => false,
        'query_var'           => false,
        'publicly_queryable'  => false,
        'exclude_from_search' => true,
        'show_in_rest'        => false,
    ));
}
add_action('init', 'usegeo_register_post_types');


function usegeo_seed_local_demo_posts_v2() {
    $site_url = home_url('/');

    if (strpos($site_url, 'usegeo-local.local') === false) {
        return;
    }

    if (get_option('usegeo_seed_demo_posts_v2') === 'done') {
        return;
    }

    $category = term_exists('GEO', 'category');
    if (!$category) {
        $category = wp_insert_term('GEO', 'category', array(
            'slug' => 'geo',
        ));
    }

    $category_id = 0;
    if (is_array($category) && isset($category['term_id'])) {
        $category_id = (int) $category['term_id'];
    } elseif (is_array($category) && isset($category['term_taxonomy_id'])) {
        $category_id = (int) $category['term_taxonomy_id'];
    } elseif (is_numeric($category)) {
        $category_id = (int) $category;
    }

    $posts = array(
        array(
            'title'   => 'Why Brands Disappear From AI Recommendations',
            'slug'    => 'why-brands-disappear-in-ai-results',
            'excerpt' => 'Many B2B brands do not lack content. They lack structured, machine-readable signals that AI systems can confidently reuse.',
            'content' => '<p>In search, a weak page might still rank for a long-tail query. In AI-driven recommendations, weak structure usually means your brand gets skipped entirely.</p><p>Large language models need clear evidence about who you serve, what problem you solve, and why your solution belongs in a recommendation set. When that evidence is fragmented across vague homepage copy, thin solution pages, and scattered blog posts, your brand becomes difficult to cite.</p><h2>Common reasons brands disappear</h2><ul><li>Your homepage does not clearly define your market, use case, or buying context</li><li>Your solution pages are thin and fail to explain how the product fits real workflows</li><li>You have no comparison pages, so models have no clean source for competitive context</li><li>Your case studies are too light to act as proof</li><li>Your best material lives across PDFs, decks, and off-site profiles instead of your site architecture</li></ul><p>If you want to see whether your site has the baseline signals needed for AI visibility, start with a simple assessment before investing in bigger execution work.</p>',
        ),
        array(
            'title'   => 'GEO vs SEO: What Actually Changes',
            'slug'    => 'geo-vs-seo',
            'excerpt' => 'SEO focuses on discovery and ranking. GEO focuses on whether your brand gets included, cited, and framed well inside AI-generated answers.',
            'content' => '<p>GEO is not a replacement for SEO, but it does change the target outcome. SEO helps search engines discover and rank your pages. GEO helps language models understand when your brand belongs in an answer.</p><h2>The practical difference</h2><ul><li>SEO optimizes for visibility in a results page</li><li>GEO optimizes for inclusion inside an answer</li><li>SEO relies heavily on ranking and clicks</li><li>GEO relies more on clear entity signals, structured explanations, comparison context, and supporting proof</li></ul><p>For B2B teams, this means strong branded pages, solution pages, comparison pages, and case studies matter more than a blog archive full of loosely related articles.</p>',
        ),
        array(
            'title'   => 'Why Comparison Pages Are High-Leverage GEO Assets',
            'slug'    => 'why-comparison-pages-matter-for-geo',
            'excerpt' => 'Comparison pages naturally contain the brand entities, decision context, and structured differences that AI systems can reuse in answers.',
            'content' => '<p>Prospects rarely ask only, "What is this product?" They ask which option fits their team, budget, and workflow. That is exactly why comparison pages are so valuable in AI search and recommendation environments.</p><h2>Why they matter</h2><ul><li>They place your brand next to known alternatives in a clear comparison frame</li><li>They explain who each option is for, which helps models form recommendation logic</li><li>They often include structured details like pricing approach, implementation model, and use-case fit</li><li>They sit much closer to a real buying decision than generic informational posts</li></ul><p>A useful comparison page should clearly state the alternatives, the tradeoffs, the ideal customer for each option, and why your product is the better fit in specific scenarios.</p>',
        ),
        array(
            'title'   => 'Why AI Still Does Not Recommend You Even If You Publish a Lot',
            'slug'    => 'why-ai-still-does-not-recommend-you',
            'excerpt' => 'Content volume alone does not create AI visibility. What matters is whether your content forms a coherent structure that can be understood and reused.',
            'content' => '<p>Many teams assume that publishing more content should lead to more brand mentions in AI tools. In practice, lots of content often means lots of inconsistency.</p><h2>Typical structural issues</h2><ul><li>Your articles cover many topics but never build a clear narrative around your brand</li><li>Your homepage, about page, and solution pages describe the company in different ways</li><li>You are missing critical page types like comparison pages, use-case pages, or proof-heavy case studies</li><li>Your articles answer small questions but do not support a larger commercial story</li><li>Your evidence base is too weak to support confident recommendations</li></ul><p>AI systems do not reward content quantity by default. They reward content that is easy to interpret, connect, and restate.</p>',
        ),
        array(
            'title'   => 'How to Evaluate GEO Opportunity in 30 Minutes',
            'slug'    => 'how-to-evaluate-geo-opportunity-in-30-minutes',
            'excerpt' => 'You do not need a massive program to decide whether GEO matters for your brand. A lightweight assessment can reveal whether the opportunity is real.',
            'content' => '<p>Before you invest in tooling, workflows, or outside help, you can usually tell a lot by auditing the core parts of your public site.</p><h2>A simple 30-minute GEO check</h2><ol><li>Can your homepage clearly explain who you help, what you solve, and for whom?</li><li>Do you have strong solution pages?</li><li>Do you have comparison content that reflects real buying questions?</li><li>Do you have proof in the form of customer stories or outcomes?</li><li>Do your core pages create a coherent picture of your brand?</li></ol><p>This quick pass will not replace a deep audit, but it is enough to tell whether GEO is likely to be a real leverage point or just noise for your team right now.</p>',
        ),
        array(
            'title'   => 'Why Content Automation Can Become a GEO Growth Lever',
            'slug'    => 'why-content-automation-is-a-geo-growth-lever',
            'excerpt' => 'GEO is not only a visibility problem. It is also a content throughput problem. Brands need repeatable ways to build a denser, more useful content graph.',
            'content' => '<p>Once teams understand what GEO requires, the next bottleneck becomes execution capacity. You may know you need better branded pages, stronger comparison content, deeper use-case pages, and more proof. The harder part is producing that system consistently.</p><h2>Where automation helps</h2><ul><li>Building page outlines faster</li><li>Expanding coverage across priority scenarios</li><li>Improving comparison and brand-signal density</li><li>Lowering the marginal cost of publishing useful supporting content</li></ul><p>The right model is not full automation with no judgment. It is human-led prioritization with automation used to accelerate production, improve consistency, and reduce time-to-publish.</p>',
        ),
    );

    foreach ($posts as $post) {
        $existing_post = get_page_by_path($post['slug'], OBJECT, 'post');

        $postarr = array(
            'post_title'   => $post['title'],
            'post_name'    => $post['slug'],
            'post_excerpt' => $post['excerpt'],
            'post_content' => $post['content'],
            'post_status'  => 'publish',
            'post_type'    => 'post',
        );

        if ($category_id) {
            $postarr['post_category'] = array($category_id);
        }

        if ($existing_post instanceof WP_Post) {
            $postarr['ID'] = $existing_post->ID;
            wp_update_post($postarr);
            continue;
        }

        wp_insert_post($postarr);
    }

    update_option('usegeo_seed_demo_posts_v2', 'done');
}
add_action('init', 'usegeo_seed_local_demo_posts_v2', 20);


function usegeo_seed_local_core_pages_v1() {
    $site_url = home_url('/');

    if (strpos($site_url, 'usegeo-local.local') === false) {
        return;
    }

    if (get_option('usegeo_seed_core_pages_v1') === 'done') {
        return;
    }

    $pages = array(
        array(
            'title'   => 'About',
            'slug'    => 'about',
            'content' => '
                <p>UseGEO is an education and assessment platform focused on GEO, AI visibility, and the content structures that make brands easier to understand in AI-driven recommendation environments.</p>
                <p>The site is designed to do two things well:</p>
                <ul>
                    <li>Publish practical GEO content that helps teams understand what matters and what to do next</li>
                    <li>Offer lightweight assessment tools that help teams evaluate their current GEO readiness</li>
                </ul>
                <p>Over time, UseGEO may also recommend relevant specialists or service providers when a team wants outside help implementing what they have learned.</p>
                <p>Our goal is simple: make GEO easier to understand, easier to evaluate, and easier to act on.</p>
            ',
        ),
        array(
            'title'   => 'Privacy Policy',
            'slug'    => 'privacy-policy',
            'content' => '
                <p><strong>Effective date:</strong> April 27, 2026</p>
                <p>UseGEO provides educational content and free assessment experiences related to GEO and AI visibility. This Privacy Policy explains what information we collect, how we use it, and when we may share it.</p>
                <h2>Information we collect</h2>
                <ul>
                    <li>Information you submit through forms, such as your work email, company type, help preferences, or case submission details</li>
                    <li>Website URLs or business context you enter into assessment tools</li>
                    <li>Basic usage and analytics data, such as page visits, button clicks, and on-site interactions</li>
                </ul>
                <h2>How we use information</h2>
                <ul>
                    <li>To deliver and improve assessments, content, and site functionality</li>
                    <li>To understand which topics, tools, and pages are most useful to visitors</li>
                    <li>To respond to inquiries, follow up on submissions, and send relevant next-step information</li>
                    <li>To review whether a visitor may benefit from additional GEO support or specialist help</li>
                </ul>
                <h2>When we may share information</h2>
                <p>We do not sell personal information in the ordinary sense of selling data lists.</p>
                <p>However, if you request help, indicate interest in outside support, or ask for a recommendation, we may share relevant submission details with a suitable GEO specialist, service provider, or partner to help you take the next step.</p>
                <p>We may also use service providers that help us operate the site, handle analytics, or manage communications.</p>
                <h2>Cookies and analytics</h2>
                <p>We may use analytics and related technologies to understand site usage, measure content performance, and improve assessments and conversion flows.</p>
                <h2>Data retention</h2>
                <p>We keep submitted information for as long as it is reasonably useful for operating the site, reviewing submissions, improving our services, and handling follow-up requests.</p>
                <h2>Your choices</h2>
                <p>You can choose not to submit forms or assessment details. If you have submitted information and want us to review, update, or delete it, you can contact us using the information provided on this site.</p>
                <h2>Changes</h2>
                <p>We may update this Privacy Policy from time to time. If we make material changes, we will update the effective date on this page.</p>
            ',
        ),
        array(
            'title'   => 'Cookie Policy',
            'slug'    => 'cookie-policy',
            'content' => '
                <p><strong>Effective date:</strong> April 27, 2026</p>
                <p>UseGEO may use cookies and similar technologies to keep the site working, understand usage patterns, and improve content and assessment experiences.</p>
                <h2>What cookies may be used for</h2>
                <ul>
                    <li>Remembering basic site preferences or session behavior</li>
                    <li>Measuring page visits, tool usage, and conversion actions</li>
                    <li>Understanding which content and calls to action perform best</li>
                </ul>
                <h2>Third-party tools</h2>
                <p>We may use third-party analytics or tag management tools to understand traffic and on-site behavior. Those providers may set or read their own cookies in accordance with their own policies.</p>
                <h2>Your choices</h2>
                <p>You can usually control cookies through your browser settings, including blocking or deleting existing cookies. Disabling some cookies may affect how parts of the site function.</p>
            ',
        ),
    );

    foreach ($pages as $page) {
        $existing_page = get_page_by_path($page['slug'], OBJECT, 'page');

        $pagearr = array(
            'post_title'   => $page['title'],
            'post_name'    => $page['slug'],
            'post_content' => trim($page['content']),
            'post_status'  => 'publish',
            'post_type'    => 'page',
        );

        if ($existing_page instanceof WP_Post) {
            $pagearr['ID'] = $existing_page->ID;
            wp_update_post($pagearr);
            continue;
        }

        wp_insert_post($pagearr);
    }

    update_option('usegeo_seed_core_pages_v1', 'done');
}
add_action('init', 'usegeo_seed_local_core_pages_v1', 21);

