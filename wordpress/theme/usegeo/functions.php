<?php

if (!defined('ABSPATH')) {
    exit;
}

function usegeo_theme_setup() {
    load_theme_textdomain('usegeo', get_template_directory() . '/languages');

    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo', array(
        'height'      => 48,
        'width'       => 240,
        'flex-height' => true,
        'flex-width'  => true,
    ));
    add_theme_support('custom-line-height');
    add_theme_support('custom-spacing');
    add_theme_support('align-wide');
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
    ));

    register_nav_menus(array(
        'primary' => __('Primary Menu', 'usegeo'),
        'footer'  => __('Footer Menu', 'usegeo'),
    ));
}
add_action('after_setup_theme', 'usegeo_theme_setup');

function usegeo_force_local_admin_email($email) {
    $site_url = home_url('/');

    if (strpos($site_url, 'usegeo-local.local') === false) {
        return $email;
    }

    return 'kevin_wang_hd@163.com';
}
add_filter('pre_option_admin_email', 'usegeo_force_local_admin_email');

function usegeo_sync_local_admin_email() {
    $site_url = home_url('/');

    if (strpos($site_url, 'usegeo-local.local') === false) {
        return;
    }

    $target_email = 'kevin_wang_hd@163.com';
    $current_email = get_option('admin_email');

    if ($current_email !== $target_email) {
        update_option('admin_email', $target_email);
    }

    // Clear WordPress pending admin email confirmation on local environments.
    delete_option('new_admin_email');
}
add_action('init', 'usegeo_sync_local_admin_email', 5);

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

    register_post_type('usegeo_case_submission', array(
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

function usegeo_seed_local_demo_posts() {
    $site_url = home_url('/');

    if (strpos($site_url, 'usegeo-local.local') === false) {
        return;
    }

    if (get_option('usegeo_seed_demo_posts_v1') === 'done') {
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
            'title'   => 'AI 时代，为什么你的品牌会在推荐结果中“消失”？',
            'slug'    => 'why-brands-disappear-in-ai-results',
            'excerpt' => '很多 B2B 品牌并不是没有内容，而是没有形成足够清晰、足够结构化、足够可被 AI 理解的内容信号。',
            'content' => '<p>过去几年，大多数品牌做内容的逻辑都很一致：围绕搜索需求布局关键词，争取搜索结果里的曝光，然后把流量导进官网、落地页和销售漏斗。</p><p>但 AI 时代改变了一件关键的事：客户不一定先看到搜索结果页，再点进你的网站；他们可能直接在 ChatGPT、Claude、Perplexity 或 Gemini 里得到一个“总结好的答案”。在这个过程中，品牌是否会被提到，取决于模型能不能从已有内容里识别出你的品牌、理解你的业务定位，并把你放进合适的比较语境里。</p><p>很多品牌会出现一个新的现象：搜索里还能找到自己，但 AI 推荐里几乎“消失”了。这个“消失”通常不是因为你完全没有内容，而是因为你的内容没有形成 AI 可消费的信号。</p><h2>常见原因</h2><ul><li>品牌介绍页太弱，只会说空泛价值，不会清晰定义你是谁、解决什么问题</li><li>解决方案页不完整，缺少明确的场景、对象和使用方式</li><li>没有对比页，导致模型在回答“谁和谁有什么区别”时找不到可引用材料</li><li>案例内容太少，无法提供可信的业务证据</li><li>内容分散在博客、落地页、PDF 和外部平台，缺少统一的结构化表达</li></ul><p>在搜索引擎时代，这些问题可能只是影响排名效率；但在 AI 推荐时代，它们会直接影响模型是否有足够把握提到你。</p><p><strong>如果你想快速判断自己的品牌是否已经具备基本的 AI 可见性，可以先回到首页，使用品牌 AI 曝光度诊断器做一次初步检查。</strong></p>',
        ),
        array(
            'title'   => 'GEO 和 SEO 到底有什么不同？',
            'slug'    => 'geo-vs-seo',
            'excerpt' => 'SEO 关注搜索引擎抓取、索引和排名，GEO 关注品牌在生成式推荐环境中的提及、引用和表达质量。',
            'content' => '<p>很多人在第一次听到 GEO 的时候，第一反应是：这是不是就是 SEO 换了个说法？这个理解不完全错，但也不对。</p><p>SEO 解决的是一个经典问题：如何让搜索引擎更容易发现你的内容、理解你的页面主题，并把你排在更前的位置。GEO 关注的问题更进一步：当用户直接向 AI 提问时，你会不会被提到？当模型总结某个领域时，你会不会成为候选答案？</p><h2>核心区别</h2><ul><li>SEO 更像争取被展示</li><li>GEO 更像争取被纳入答案</li><li>SEO 看重排名和点击</li><li>GEO 看重提及、引用和推荐语境</li></ul><p>两者当然有重叠，但在 B2B 场景里，GEO 会更强调：页面是否能清晰定义品牌和解决方案，是否存在可比较的竞品对比内容，内容是否形成系统性的知识网络，而不是零散文章。</p><p><strong>如果你想知道自己当前更像是“有 SEO、没 GEO”，还是已经具备初步 AI 可见性，可以直接使用首页的品牌 AI 曝光度诊断器做一次快速判断。</strong></p>',
        ),
        array(
            'title'   => '为什么竞品对比页是 GEO 的高价值内容资产',
            'slug'    => 'why-comparison-pages-matter-for-geo',
            'excerpt' => '竞品对比页天然具备品牌实体、比较语境和决策信号，容易成为模型组织答案的材料来源。',
            'content' => '<p>B2B 客户很少只问“这是什么”。他们更常问的是：这个产品和另一个产品有什么区别？谁更适合我们？我们应该选 A 还是 B？</p><p>在生成式推荐时代，这类问题反而变得更频繁。用户面对 AI 时，会自然地用更接近对话的方式提问，这正是竞品对比页价值被重新放大的原因。</p><h2>为什么重要</h2><ul><li>页面同时包含你的品牌和竞品品牌，模型容易识别比较关系</li><li>如果页面说明“什么团队适合谁”，更容易进入推荐答案</li><li>功能表、适用对象、价格区间、部署方式等结构化信号更容易被抽取</li><li>比较型页面天然接近购买决策，比普通博客文章更接近真实转化</li></ul><p>一个真正有 GEO 价值的竞品对比页，应该做到：明确比较对象、明确适用团队、明确差异点、明确推荐逻辑、明确你为什么更适合某类用户。</p><p><strong>如果你想知道自己的品牌是否已经具备足够的比较型内容信号，可以先用首页的品牌 AI 曝光度诊断器做一次初步评估。</strong></p>',
        ),
        array(
            'title'   => '你的内容很多，为什么 AI 还是不推荐你？',
            'slug'    => 'why-ai-still-does-not-recommend-you',
            'excerpt' => '内容多，不代表内容有效。很多品牌拥有博客、白皮书、落地页和案例，但这些内容没有形成清晰的品牌表达和结构化信号。',
            'content' => '<p>很多团队在讨论 GEO 的时候，最常见的一句困惑是：我们已经写了很多内容，为什么 AI 还是不提我们？</p><p>这个问题很现实。你可能已经有很多博客文章、官网多个落地页、若干案例，甚至还做过 SEO。但这些资产的存在，并不自动等于你已经具备了 AI 可见性。</p><h2>常见结构问题</h2><ul><li>内容主题分散，文章彼此之间缺少统一结构</li><li>品牌表达不清，首页、About、功能页说法不一致</li><li>页面类型不完整，缺品牌页、解决方案页、对比页或案例页</li><li>单篇文章回答小问题，但无法支撑完整业务场景</li><li>案例和证据太弱，难以形成高置信度推荐</li></ul><p>AI 推荐需要的不是“内容总量”，而是“可被理解和复述的内容结构”。这也是为什么很多品牌在内容上投入不少，但 AI 推荐中依然存在感很弱。</p><p><strong>如果你想先判断自己的内容到底是“多但无效”，还是“已经有 GEO 基础”，可以先用首页的品牌 AI 曝光度诊断器做一次快速检查。</strong></p>',
        ),
        array(
            'title'   => '如何用 30 分钟判断品牌有没有 GEO 机会',
            'slug'    => 'how-to-evaluate-geo-opportunity-in-30-minutes',
            'excerpt' => '在重投入之前，品牌完全可以先用一个轻量流程判断 GEO 是否值得做。',
            'content' => '<p>很多团队一听到 GEO，会马上把它想得很重：要不要上新工具？要不要接大模型 API？要不要重做整个内容体系？其实在大多数情况下，你完全可以先用 30 分钟做一个轻量判断。</p><h2>30 分钟检查法</h2><ol><li>看首页是否能清楚回答“你是谁、解决什么问题、适合谁”</li><li>看有没有核心解决方案页</li><li>看有没有比较型内容</li><li>看有没有案例和证据</li><li>做一次初步诊断</li></ol><p>通过这 5 步，你通常可以把品牌分成三类：基础很弱、有基础但结构不完整、基础较好。对大多数团队来说，这个检查的价值不是完美判断，而是避免在没搞清问题之前过度投入。</p><p><strong>如果你想立即做这 30 分钟检查里的最后一步，现在就可以回到首页，使用品牌 AI 曝光度诊断器做一次快速评估。</strong></p>',
        ),
        array(
            'title'   => '内容自动化为什么会成为 GEO 的增长杠杆',
            'slug'    => 'why-content-automation-is-a-geo-growth-lever',
            'excerpt' => 'GEO 不只是可见性问题，也是内容生产效率问题。品牌要持续构建可被推荐的内容网络，就必须解决内容密度和边际成本。',
            'content' => '<p>很多团队在讨论 GEO 的时候，会先把注意力放在“模型会不会推荐我”。但真正进入执行阶段后，很快就会遇到另一个问题：即使知道该做什么，我们有没有能力持续把这些内容做出来？</p><p>这就是为什么内容自动化会成为 GEO 的关键杠杆。GEO 并不只依赖一两篇文章，它更依赖一个逐步成形的内容网络，包括品牌介绍页、解决方案页、行业页、场景页、竞品对比页、案例页和方法论文章。</p><h2>自动化真正的价值</h2><ul><li>更快形成页面矩阵</li><li>更快覆盖核心场景</li><li>更快补足品牌与比较信号</li><li>更低成本地建设可被推荐的内容结构</li></ul><p>真正有效的方式通常是：人工确定优先级和结构，自动化帮助提高生产效率，最终仍由人控制质量和业务表达。</p><p><strong>如果你想快速评估内容自动化可能带来的商业空间，可以先用首页的 AIGC 营销内容 ROI 试算器做一次基础测算，再回到品牌 AI 曝光度诊断器看你的品牌可见性基础。</strong></p>',
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

    update_option('usegeo_seed_demo_posts_v1', 'done');
}
add_action('init', 'usegeo_seed_local_demo_posts', 20);

remove_action('init', 'usegeo_seed_local_demo_posts', 20);

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

function usegeo_body_classes($classes) {
    $classes[] = 'usegeo-theme';

    if (is_front_page()) {
        $classes[] = 'usegeo-page-front';
    }

    if (is_single()) {
        $classes[] = 'usegeo-page-single';
    }

    if (is_page()) {
        $classes[] = 'usegeo-page-standard';
    }

    return $classes;
}
add_filter('body_class', 'usegeo_body_classes');

function usegeo_render_flash_notice($type, $message) {
    if (!$message) {
        return;
    }
    ?>
    <div class="usegeo-flash usegeo-flash--<?php echo esc_attr($type); ?>" role="status">
        <p><?php echo esc_html($message); ?></p>
    </div>
    <?php
}

function usegeo_get_lead_notice() {
    $status = isset($_GET['lead_status']) ? sanitize_key(wp_unslash($_GET['lead_status'])) : '';

    if ($status === 'success') {
        return array(
            'type'    => 'success',
            'message' => __('深度报告申请已收到，我们会尽快通过企业邮箱联系你。', 'usegeo'),
        );
    }

    if ($status === 'success_no_mail') {
        return array(
            'type'    => 'success',
            'message' => __('深度报告申请已收到。后台 Lead 已保存，但邮件通知未成功发出，请到后台查看。', 'usegeo'),
        );
    }

    if ($status === 'invalid') {
        return array(
            'type'    => 'error',
            'message' => __('请填写有效的企业邮箱和职位后再提交。', 'usegeo'),
        );
    }

    if ($status === 'failed') {
        return array(
            'type'    => 'error',
            'message' => __('提交失败，请稍后重试。', 'usegeo'),
        );
    }

    return null;
}

function usegeo_get_case_submission_notice() {
    $status = isset($_GET['case_submission_status']) ? sanitize_key(wp_unslash($_GET['case_submission_status'])) : '';

    if ($status === 'success') {
        return array(
            'type'    => 'success',
            'message' => __('案例提交已收到。UseGEO 会先在后台完成筛选、匿名处理和结构化整理，再决定是否进入精选案例池。', 'usegeo'),
        );
    }

    if ($status === 'invalid') {
        return array(
            'type'    => 'error',
            'message' => __('请至少填写有效邮箱、公司/品牌类型、问题、已执行动作和结果反馈后再提交。', 'usegeo'),
        );
    }

    if ($status === 'failed') {
        return array(
            'type'    => 'error',
            'message' => __('案例提交失败，请稍后重试。', 'usegeo'),
        );
    }

    return null;
}

function usegeo_send_lead_notification($lead_id, $work_email, $company_type, $help_intent, $support_level, $scanner_query) {
    $admin_email = get_option('admin_email');

    if (!$admin_email || !is_email($admin_email)) {
        return array(
            'sent'      => false,
            'recipient' => $admin_email,
            'error'     => 'invalid_admin_email',
        );
    }

    $subject = sprintf('[UseGEO] New full assessment lead from %s', $work_email);

    $message_lines = array(
        'A new UseGEO full assessment lead has been submitted.',
        '',
        'Lead ID: ' . $lead_id,
        'Work email: ' . $work_email,
        'Company type: ' . ($company_type ?: '(empty)'),
        'Help intent: ' . ($help_intent ?: '(empty)'),
        'Support level: ' . ($support_level ?: '(empty)'),
        'Website input: ' . ($scanner_query ?: '(empty)'),
        'Submitted at: ' . current_time('mysql'),
        '',
        'View in WordPress admin:',
        admin_url('post.php?post=' . $lead_id . '&action=edit'),
    );

    $headers = array('Content-Type: text/plain; charset=UTF-8');

    delete_transient('usegeo_last_mail_error');
    $sent = wp_mail($admin_email, $subject, implode("\n", $message_lines), $headers);
    $error = get_transient('usegeo_last_mail_error');

    return array(
        'sent'      => (bool) $sent,
        'recipient' => $admin_email,
        'error'     => $error ? $error : '',
    );
}

function usegeo_capture_mail_failure($wp_error) {
    if (!$wp_error instanceof WP_Error) {
        return;
    }

    set_transient('usegeo_last_mail_error', $wp_error->get_error_message(), MINUTE_IN_SECONDS * 10);
}
add_action('wp_mail_failed', 'usegeo_capture_mail_failure');

function usegeo_handle_lead_capture() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        wp_safe_redirect(home_url('/'));
        exit;
    }

    check_admin_referer('usegeo_capture_lead', 'usegeo_lead_nonce');

    $work_email = isset($_POST['work_email']) ? sanitize_email(wp_unslash($_POST['work_email'])) : '';
    $company_type = isset($_POST['company_type']) ? sanitize_key(wp_unslash($_POST['company_type'])) : '';
    $help_intent = isset($_POST['help_intent']) ? sanitize_key(wp_unslash($_POST['help_intent'])) : '';
    $support_level = isset($_POST['support_level']) ? sanitize_key(wp_unslash($_POST['support_level'])) : '';
    $scanner_query = isset($_POST['scanner_query']) ? esc_url_raw(wp_unslash($_POST['scanner_query'])) : '';
    $redirect_to = isset($_POST['redirect_to']) ? esc_url_raw(wp_unslash($_POST['redirect_to'])) : home_url('/#scanner');
    $company_type_options = usegeo_get_lead_company_type_options();

    if (
        !$work_email ||
        !is_email($work_email) ||
        !$company_type ||
        !isset($company_type_options[$company_type]) ||
        !$help_intent ||
        !$support_level ||
        !$scanner_query
    ) {
        wp_safe_redirect(add_query_arg('lead_status', 'invalid', $redirect_to));
        exit;
    }

    $post_id = wp_insert_post(array(
        'post_type'   => 'usegeo_lead',
        'post_status' => 'publish',
        'post_title'  => sprintf('%s - %s', $work_email, current_time('mysql')),
    ), true);

    if (is_wp_error($post_id)) {
        wp_safe_redirect(add_query_arg('lead_status', 'failed', $redirect_to));
        exit;
    }

    update_post_meta($post_id, 'work_email', $work_email);
    update_post_meta($post_id, 'company_type', $company_type);
    update_post_meta($post_id, 'help_intent', $help_intent);
    update_post_meta($post_id, 'support_level', $support_level);
    update_post_meta($post_id, 'scanner_query', $scanner_query);
    update_post_meta($post_id, 'submitted_at', current_time('mysql'));
    update_post_meta($post_id, 'source', 'full_assessment_gate');

    $mail_result = usegeo_send_lead_notification($post_id, $work_email, $company_type, $help_intent, $support_level, $scanner_query);
    $mail_sent = !empty($mail_result['sent']);
    update_post_meta($post_id, 'notification_sent', $mail_sent ? 'yes' : 'no');
    update_post_meta($post_id, 'notification_recipient', isset($mail_result['recipient']) ? $mail_result['recipient'] : '');
    update_post_meta($post_id, 'notification_error', isset($mail_result['error']) ? $mail_result['error'] : '');

    $redirect_status = $mail_sent ? 'success' : 'success_no_mail';
    wp_safe_redirect(add_query_arg('lead_status', $redirect_status, $redirect_to));
    exit;
}
add_action('admin_post_nopriv_usegeo_capture_lead', 'usegeo_handle_lead_capture');
add_action('admin_post_usegeo_capture_lead', 'usegeo_handle_lead_capture');

function usegeo_handle_case_submission() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        wp_safe_redirect(home_url('/#community'));
        exit;
    }

    check_admin_referer('usegeo_submit_case', 'usegeo_case_nonce');

    $redirect_to = isset($_POST['redirect_to']) ? esc_url_raw(wp_unslash($_POST['redirect_to'])) : home_url('/#community');
    $contributor_name = isset($_POST['contributor_name']) ? sanitize_text_field(wp_unslash($_POST['contributor_name'])) : '';
    $contact_email = isset($_POST['contact_email']) ? sanitize_email(wp_unslash($_POST['contact_email'])) : '';
    $company_type = isset($_POST['company_type']) ? sanitize_text_field(wp_unslash($_POST['company_type'])) : '';
    $industry = isset($_POST['industry']) ? sanitize_text_field(wp_unslash($_POST['industry'])) : '';
    $business_stage = isset($_POST['business_stage']) ? sanitize_text_field(wp_unslash($_POST['business_stage'])) : '';
    $core_problem = isset($_POST['core_problem']) ? sanitize_textarea_field(wp_unslash($_POST['core_problem'])) : '';
    $geo_actions = isset($_POST['geo_actions']) ? sanitize_textarea_field(wp_unslash($_POST['geo_actions'])) : '';
    $pages_updated = isset($_POST['pages_updated']) ? sanitize_textarea_field(wp_unslash($_POST['pages_updated'])) : '';
    $results_seen = isset($_POST['results_seen']) ? sanitize_textarea_field(wp_unslash($_POST['results_seen'])) : '';
    $lessons_learned = isset($_POST['lessons_learned']) ? sanitize_textarea_field(wp_unslash($_POST['lessons_learned'])) : '';
    $allow_anonymous = isset($_POST['allow_anonymous']) ? 'yes' : 'no';

    if (!$contact_email || !is_email($contact_email) || !$company_type || !$core_problem || !$geo_actions || !$results_seen) {
        wp_safe_redirect(add_query_arg('case_submission_status', 'invalid', $redirect_to));
        exit;
    }

    $title_parts = array_filter(array(
        $contributor_name ? $contributor_name : 'Anonymous contributor',
        $company_type,
    ));

    $post_id = wp_insert_post(array(
        'post_type'   => 'usegeo_case_submission',
        'post_status' => 'publish',
        'post_title'  => implode(' - ', $title_parts) . ' - ' . current_time('mysql'),
    ), true);

    if (is_wp_error($post_id)) {
        wp_safe_redirect(add_query_arg('case_submission_status', 'failed', $redirect_to));
        exit;
    }

    update_post_meta($post_id, 'contributor_name', $contributor_name);
    update_post_meta($post_id, 'contact_email', $contact_email);
    update_post_meta($post_id, 'company_type', $company_type);
    update_post_meta($post_id, 'industry', $industry);
    update_post_meta($post_id, 'business_stage', $business_stage);
    update_post_meta($post_id, 'core_problem', $core_problem);
    update_post_meta($post_id, 'geo_actions', $geo_actions);
    update_post_meta($post_id, 'pages_updated', $pages_updated);
    update_post_meta($post_id, 'results_seen', $results_seen);
    update_post_meta($post_id, 'lessons_learned', $lessons_learned);
    update_post_meta($post_id, 'allow_anonymous', $allow_anonymous);
    update_post_meta($post_id, 'review_status', 'pending_review');
    update_post_meta($post_id, 'processing_lane', 'unassigned');
    update_post_meta($post_id, 'editorial_notes', '');
    update_post_meta($post_id, 'submitted_at', current_time('mysql'));
    update_post_meta($post_id, 'source', 'community_case_form');

    wp_safe_redirect(add_query_arg('case_submission_status', 'success', $redirect_to));
    exit;
}
add_action('admin_post_nopriv_usegeo_submit_case', 'usegeo_handle_case_submission');
add_action('admin_post_usegeo_submit_case', 'usegeo_handle_case_submission');

function usegeo_get_case_review_status_options() {
    return array(
        'pending_review' => __('Pending Review', 'usegeo'),
        'reviewing'      => __('Reviewing', 'usegeo'),
        'approved'       => __('Approved', 'usegeo'),
        'needs_followup' => __('Needs Follow-up', 'usegeo'),
        'archived'       => __('Archived', 'usegeo'),
    );
}

function usegeo_get_case_processing_lane_options() {
    return array(
        'unassigned'      => __('Unassigned', 'usegeo'),
        'featured_pool'   => __('Featured Pool', 'usegeo'),
        'article_pipeline'=> __('Article Pipeline', 'usegeo'),
        'followup_pool'   => __('Follow-up Pool', 'usegeo'),
        'archive_only'    => __('Archive Only', 'usegeo'),
    );
}

function usegeo_render_primary_menu() {
    if (has_nav_menu('primary')) {
        wp_nav_menu(array(
            'theme_location' => 'primary',
            'container'      => false,
            'menu_class'     => 'usegeo-menu usegeo-menu--primary',
            'fallback_cb'    => false,
        ));
        return;
    }
    ?>
    <ul class="usegeo-menu usegeo-menu--primary">
        <li><a href="<?php echo esc_url(home_url('/#scanner')); ?>"><?php esc_html_e('诊断工具', 'usegeo'); ?></a></li>
        <li><a href="<?php echo esc_url(home_url('/#content')); ?>"><?php esc_html_e('案例文章', 'usegeo'); ?></a></li>
        <li><a href="<?php echo esc_url(home_url('/about')); ?>"><?php esc_html_e('关于', 'usegeo'); ?></a></li>
    </ul>
    <?php
}

function usegeo_render_footer_menu() {
    if (has_nav_menu('footer')) {
        wp_nav_menu(array(
            'theme_location' => 'footer',
            'container'      => false,
            'menu_class'     => 'usegeo-menu usegeo-menu--footer',
            'fallback_cb'    => false,
        ));
        return;
    }
    ?>
    <ul class="usegeo-menu usegeo-menu--footer">
        <li><a href="<?php echo esc_url(home_url('/about')); ?>"><?php esc_html_e('关于我们', 'usegeo'); ?></a></li>
        <li><a href="<?php echo esc_url(home_url('/privacy-policy')); ?>"><?php esc_html_e('隐私政策', 'usegeo'); ?></a></li>
        <li><a href="<?php echo esc_url(home_url('/cookie-policy')); ?>"><?php esc_html_e('Cookie 声明', 'usegeo'); ?></a></li>
    </ul>
    <?php
}

function usegeo_get_estimated_reading_time($post_id = 0) {
    $post = get_post($post_id ?: get_the_ID());

    if (!$post) {
        return 1;
    }

    $word_count = str_word_count(wp_strip_all_tags($post->post_content));
    $minutes = (int) ceil(max($word_count, 80) / 220);

    return max(1, $minutes);
}

function usegeo_get_card_excerpt($post_id = 0, $word_limit = 18) {
    $post = get_post($post_id ?: get_the_ID());

    if (!$post) {
        return '';
    }

    $source = $post->post_excerpt ? $post->post_excerpt : wp_strip_all_tags($post->post_content);
    return wp_trim_words($source, $word_limit);
}

function usegeo_lead_admin_columns($columns) {
    $date = isset($columns['date']) ? $columns['date'] : '';

    unset($columns['date']);

    $columns['work_email'] = __('Work Email', 'usegeo');
    $columns['job_title'] = __('Job Title', 'usegeo');
    $columns['scanner_query'] = __('Scanner Query', 'usegeo');
    $columns['notification_status'] = __('Notification', 'usegeo');
    $columns['notification_recipient'] = __('Recipient', 'usegeo');

    if ($date) {
        $columns['date'] = $date;
    }

    return $columns;
}
add_filter('manage_usegeo_lead_posts_columns', 'usegeo_lead_admin_columns');
add_filter('manage_edit-usegeo_lead_columns', 'usegeo_lead_admin_columns');

function usegeo_render_lead_admin_column($column, $post_id) {
    switch ($column) {
        case 'work_email':
            echo esc_html(get_post_meta($post_id, 'work_email', true) ?: '—');
            break;

        case 'job_title':
            echo esc_html(get_post_meta($post_id, 'job_title', true) ?: '—');
            break;

        case 'scanner_query':
            echo esc_html(get_post_meta($post_id, 'scanner_query', true) ?: '—');
            break;

        case 'notification_status':
            $sent = get_post_meta($post_id, 'notification_sent', true);
            $error = get_post_meta($post_id, 'notification_error', true);

            if ($sent === 'yes') {
                echo '<span style="color:#15803d;font-weight:700;">sent</span>';
            } elseif ($error) {
                echo '<span style="color:#b91c1c;font-weight:700;">failed</span><br>';
                echo '<span style="color:#6b7280;">' . esc_html($error) . '</span>';
            } else {
                echo '<span style="color:#92400e;font-weight:700;">not sent</span>';
            }
            break;

        case 'notification_recipient':
            echo esc_html(get_post_meta($post_id, 'notification_recipient', true) ?: '—');
            break;
    }
}
add_action('manage_usegeo_lead_posts_custom_column', 'usegeo_render_lead_admin_column', 10, 2);
add_action('manage_posts_custom_column', 'usegeo_render_lead_admin_column', 10, 2);

function usegeo_register_lead_meta_box() {
    add_meta_box(
        'usegeo-lead-details',
        __('Lead Details', 'usegeo'),
        'usegeo_render_lead_meta_box',
        'usegeo_lead',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'usegeo_register_lead_meta_box');

function usegeo_render_lead_meta_box($post) {
    $work_email = get_post_meta($post->ID, 'work_email', true);
    $job_title = get_post_meta($post->ID, 'job_title', true);
    $scanner_query = get_post_meta($post->ID, 'scanner_query', true);
    $submitted_at = get_post_meta($post->ID, 'submitted_at', true);
    $source = get_post_meta($post->ID, 'source', true);
    $notification_sent = get_post_meta($post->ID, 'notification_sent', true);
    $notification_recipient = get_post_meta($post->ID, 'notification_recipient', true);
    $notification_error = get_post_meta($post->ID, 'notification_error', true);
    ?>
    <table class="form-table" role="presentation">
        <tbody>
            <tr>
                <th scope="row"><?php esc_html_e('Work Email', 'usegeo'); ?></th>
                <td><?php echo esc_html($work_email ?: '—'); ?></td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e('Job Title', 'usegeo'); ?></th>
                <td><?php echo esc_html($job_title ?: '—'); ?></td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e('Scanner Query', 'usegeo'); ?></th>
                <td><?php echo esc_html($scanner_query ?: '—'); ?></td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e('Submitted At', 'usegeo'); ?></th>
                <td><?php echo esc_html($submitted_at ?: '—'); ?></td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e('Source', 'usegeo'); ?></th>
                <td><?php echo esc_html($source ?: '—'); ?></td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e('Notification Status', 'usegeo'); ?></th>
                <td>
                    <?php
                    if ($notification_sent === 'yes') {
                        echo '<strong style="color:#15803d;">sent</strong>';
                    } elseif ($notification_error) {
                        echo '<strong style="color:#b91c1c;">failed</strong>';
                    } else {
                        echo '<strong style="color:#92400e;">not sent</strong>';
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e('Notification Recipient', 'usegeo'); ?></th>
                <td><?php echo esc_html($notification_recipient ?: '—'); ?></td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e('Notification Error', 'usegeo'); ?></th>
                <td><?php echo esc_html($notification_error ?: '—'); ?></td>
            </tr>
        </tbody>
    </table>
    <?php
}

function usegeo_case_submission_admin_columns($columns) {
    $date = isset($columns['date']) ? $columns['date'] : '';

    unset($columns['date']);

    $columns['contact_email'] = __('Contact Email', 'usegeo');
    $columns['company_type'] = __('Company Type', 'usegeo');
    $columns['industry'] = __('Industry', 'usegeo');
    $columns['review_status'] = __('Review Status', 'usegeo');
    $columns['processing_lane'] = __('Processing Lane', 'usegeo');
    $columns['allow_anonymous'] = __('Anonymous', 'usegeo');

    if ($date) {
        $columns['date'] = $date;
    }

    return $columns;
}
add_filter('manage_usegeo_case_submission_posts_columns', 'usegeo_case_submission_admin_columns');
add_filter('manage_edit-usegeo_case_submission_columns', 'usegeo_case_submission_admin_columns');

function usegeo_render_case_submission_admin_column($column, $post_id) {
    switch ($column) {
        case 'contact_email':
            echo esc_html(get_post_meta($post_id, 'contact_email', true) ?: '-');
            break;

        case 'company_type':
            echo esc_html(get_post_meta($post_id, 'company_type', true) ?: '-');
            break;

        case 'industry':
            echo esc_html(get_post_meta($post_id, 'industry', true) ?: '-');
            break;

        case 'review_status':
            $status = get_post_meta($post_id, 'review_status', true) ?: 'pending_review';
            $options = usegeo_get_case_review_status_options();
            echo esc_html(isset($options[$status]) ? $options[$status] : $status);
            break;

        case 'processing_lane':
            $lane = get_post_meta($post_id, 'processing_lane', true) ?: 'unassigned';
            $lane_options = usegeo_get_case_processing_lane_options();
            echo esc_html(isset($lane_options[$lane]) ? $lane_options[$lane] : $lane);
            break;

        case 'allow_anonymous':
            echo esc_html(get_post_meta($post_id, 'allow_anonymous', true) === 'yes' ? 'yes' : 'no');
            break;
    }
}
add_action('manage_usegeo_case_submission_posts_custom_column', 'usegeo_render_case_submission_admin_column', 10, 2);

function usegeo_register_case_submission_meta_box() {
    add_meta_box(
        'usegeo-case-submission-details',
        __('Case Submission Details', 'usegeo'),
        'usegeo_render_case_submission_meta_box',
        'usegeo_case_submission',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'usegeo_register_case_submission_meta_box');

function usegeo_render_case_submission_meta_box($post) {
    wp_nonce_field('usegeo_save_case_review', 'usegeo_case_review_nonce');

    $review_status = get_post_meta($post->ID, 'review_status', true) ?: 'pending_review';
    $processing_lane = get_post_meta($post->ID, 'processing_lane', true) ?: 'unassigned';
    $editorial_notes = get_post_meta($post->ID, 'editorial_notes', true);
    $fields = array(
        'contributor_name' => __('Contributor Name', 'usegeo'),
        'contact_email'    => __('Contact Email', 'usegeo'),
        'company_type'     => __('Company Type', 'usegeo'),
        'industry'         => __('Industry', 'usegeo'),
        'business_stage'   => __('Business Stage', 'usegeo'),
        'core_problem'     => __('Core Problem', 'usegeo'),
        'geo_actions'      => __('GEO Actions', 'usegeo'),
        'pages_updated'    => __('Pages Updated', 'usegeo'),
        'results_seen'     => __('Results Seen', 'usegeo'),
        'lessons_learned'  => __('Lessons Learned', 'usegeo'),
        'allow_anonymous'  => __('Allow Anonymous', 'usegeo'),
        'submitted_at'     => __('Submitted At', 'usegeo'),
        'source'           => __('Source', 'usegeo'),
    );
    ?>
    <table class="form-table" role="presentation">
        <tbody>
            <tr>
                <th scope="row"><label for="usegeo-review-status"><?php esc_html_e('Review Status', 'usegeo'); ?></label></th>
                <td>
                    <select id="usegeo-review-status" name="usegeo_review_status">
                        <?php foreach (usegeo_get_case_review_status_options() as $value => $label) : ?>
                            <option value="<?php echo esc_attr($value); ?>" <?php selected($review_status, $value); ?>><?php echo esc_html($label); ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="usegeo-processing-lane"><?php esc_html_e('Processing Lane', 'usegeo'); ?></label></th>
                <td>
                    <select id="usegeo-processing-lane" name="usegeo_processing_lane">
                        <?php foreach (usegeo_get_case_processing_lane_options() as $value => $label) : ?>
                            <option value="<?php echo esc_attr($value); ?>" <?php selected($processing_lane, $value); ?>><?php echo esc_html($label); ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="usegeo-editorial-notes"><?php esc_html_e('Editorial Notes', 'usegeo'); ?></label></th>
                <td>
                    <textarea id="usegeo-editorial-notes" name="usegeo_editorial_notes" rows="5" style="width:100%;"><?php echo esc_textarea($editorial_notes); ?></textarea>
                    <p class="description"><?php esc_html_e('Use this field to capture anonymization needs, follow-up questions, and whether the case should become a featured example or article source.', 'usegeo'); ?></p>
                </td>
            </tr>
            <?php foreach ($fields as $key => $label) : ?>
                <tr>
                    <th scope="row"><?php echo esc_html($label); ?></th>
                    <td>
                        <?php
                        $value = get_post_meta($post->ID, $key, true);
                        if (in_array($key, array('core_problem', 'geo_actions', 'pages_updated', 'results_seen', 'lessons_learned'), true)) {
                            echo '<div style="white-space:pre-wrap;">' . esc_html($value ?: '-') . '</div>';
                        } else {
                            echo esc_html($value ?: '-');
                        }
                        ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php
}

function usegeo_save_case_submission_review_meta($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!isset($_POST['usegeo_case_review_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['usegeo_case_review_nonce'])), 'usegeo_save_case_review')) {
        return;
    }

    if (get_post_type($post_id) !== 'usegeo_case_submission') {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    $review_options = usegeo_get_case_review_status_options();
    $lane_options = usegeo_get_case_processing_lane_options();

    $review_status = isset($_POST['usegeo_review_status']) ? sanitize_key(wp_unslash($_POST['usegeo_review_status'])) : 'pending_review';
    $processing_lane = isset($_POST['usegeo_processing_lane']) ? sanitize_key(wp_unslash($_POST['usegeo_processing_lane'])) : 'unassigned';
    $editorial_notes = isset($_POST['usegeo_editorial_notes']) ? sanitize_textarea_field(wp_unslash($_POST['usegeo_editorial_notes'])) : '';

    if (!isset($review_options[$review_status])) {
        $review_status = 'pending_review';
    }

    if (!isset($lane_options[$processing_lane])) {
        $processing_lane = 'unassigned';
    }

    update_post_meta($post_id, 'review_status', $review_status);
    update_post_meta($post_id, 'processing_lane', $processing_lane);
    update_post_meta($post_id, 'editorial_notes', $editorial_notes);
}
add_action('save_post_usegeo_case_submission', 'usegeo_save_case_submission_review_meta');

remove_filter('manage_usegeo_lead_posts_columns', 'usegeo_lead_admin_columns');
remove_filter('manage_edit-usegeo_lead_columns', 'usegeo_lead_admin_columns');
remove_action('manage_usegeo_lead_posts_custom_column', 'usegeo_render_lead_admin_column', 10);
remove_action('manage_posts_custom_column', 'usegeo_render_lead_admin_column', 10);
remove_action('add_meta_boxes', 'usegeo_register_lead_meta_box');

function usegeo_lead_admin_columns_v2($columns) {
    $date = isset($columns['date']) ? $columns['date'] : '';

    unset($columns['date']);

    $columns['work_email'] = __('Work Email', 'usegeo');
    $columns['company_type'] = __('Company Type', 'usegeo');
    $columns['help_intent'] = __('Help Intent', 'usegeo');
    $columns['support_level'] = __('Support Level', 'usegeo');
    $columns['scanner_query'] = __('Website Input', 'usegeo');
    $columns['notification_status'] = __('Notification', 'usegeo');
    $columns['notification_recipient'] = __('Recipient', 'usegeo');

    if ($date) {
        $columns['date'] = $date;
    }

    return $columns;
}
add_filter('manage_usegeo_lead_posts_columns', 'usegeo_lead_admin_columns_v2');
add_filter('manage_edit-usegeo_lead_columns', 'usegeo_lead_admin_columns_v2');

function usegeo_get_help_intent_labels() {
    return array(
        'exploring_geo'          => __('Just exploring GEO', 'usegeo'),
        'prioritized_next_steps' => __('Want prioritized next steps', 'usegeo'),
        'open_to_specialist'     => __('Open to working with a GEO specialist', 'usegeo'),
    );
}

function usegeo_get_support_level_labels() {
    return array(
        'not_hiring'      => __('Not planning to hire anyone', 'usegeo'),
        'small_project'   => __('Open to a small project', 'usegeo'),
        'ongoing_support' => __('Open to ongoing support', 'usegeo'),
        'not_sure'        => __('Not sure yet', 'usegeo'),
    );
}

function usegeo_get_lead_company_type_options() {
    return array(
        'b2b_saas'          => __('B2B SaaS', 'usegeo'),
        'agency_consultancy'=> __('Agency / Consultancy', 'usegeo'),
        'marketplace'       => __('Marketplace', 'usegeo'),
        'media_content'     => __('Media / Content Business', 'usegeo'),
        'ecommerce'         => __('Ecommerce', 'usegeo'),
        'other'             => __('Other', 'usegeo'),
    );
}

function usegeo_render_lead_priority_badge($label, $tone = 'neutral') {
    $tones = array(
        'neutral' => 'background:#e5e7eb;color:#334155;',
        'info'    => 'background:#dbeafe;color:#1d4ed8;',
        'success' => 'background:#dcfce7;color:#166534;',
        'warn'    => 'background:#fef3c7;color:#92400e;',
    );

    $style = isset($tones[$tone]) ? $tones[$tone] : $tones['neutral'];

    return '<span style="display:inline-flex;align-items:center;padding:4px 10px;border-radius:999px;font-size:12px;font-weight:700;' . esc_attr($style) . '">' . esc_html($label) . '</span>';
}

function usegeo_render_lead_admin_column_v2($column, $post_id) {
    switch ($column) {
        case 'work_email':
            echo esc_html(get_post_meta($post_id, 'work_email', true) ?: '—');
            break;

        case 'company_type':
            echo esc_html(get_post_meta($post_id, 'company_type', true) ?: '—');
            break;

        case 'help_intent':
            echo esc_html(get_post_meta($post_id, 'help_intent', true) ?: '—');
            break;

        case 'support_level':
            echo esc_html(get_post_meta($post_id, 'support_level', true) ?: '—');
            break;

        case 'scanner_query':
            echo esc_html(get_post_meta($post_id, 'scanner_query', true) ?: '—');
            break;

        case 'notification_status':
            $sent = get_post_meta($post_id, 'notification_sent', true);
            $error = get_post_meta($post_id, 'notification_error', true);

            if ($sent === 'yes') {
                echo '<span style="color:#15803d;font-weight:700;">sent</span>';
            } elseif ($error) {
                echo '<span style="color:#b91c1c;font-weight:700;">failed</span><br>';
                echo '<span style="color:#6b7280;">' . esc_html($error) . '</span>';
            } else {
                echo '<span style="color:#92400e;font-weight:700;">not sent</span>';
            }
            break;

        case 'notification_recipient':
            echo esc_html(get_post_meta($post_id, 'notification_recipient', true) ?: '—');
            break;
    }
}
add_action('manage_usegeo_lead_posts_custom_column', 'usegeo_render_lead_admin_column_v2', 10, 2);

function usegeo_register_lead_meta_box_v2() {
    add_meta_box(
        'usegeo-lead-details-v2',
        __('Lead Details', 'usegeo'),
        'usegeo_render_lead_meta_box_v2',
        'usegeo_lead',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'usegeo_register_lead_meta_box_v2');

function usegeo_render_lead_meta_box_v2($post) {
    $work_email = get_post_meta($post->ID, 'work_email', true);
    $company_type = get_post_meta($post->ID, 'company_type', true);
    $help_intent = get_post_meta($post->ID, 'help_intent', true);
    $support_level = get_post_meta($post->ID, 'support_level', true);
    $scanner_query = get_post_meta($post->ID, 'scanner_query', true);
    $submitted_at = get_post_meta($post->ID, 'submitted_at', true);
    $source = get_post_meta($post->ID, 'source', true);
    $notification_sent = get_post_meta($post->ID, 'notification_sent', true);
    $notification_recipient = get_post_meta($post->ID, 'notification_recipient', true);
    $notification_error = get_post_meta($post->ID, 'notification_error', true);
    ?>
    <table class="form-table" role="presentation">
        <tbody>
            <tr>
                <th scope="row"><?php esc_html_e('Work Email', 'usegeo'); ?></th>
                <td><?php echo esc_html($work_email ?: '—'); ?></td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e('Company Type', 'usegeo'); ?></th>
                <td><?php echo esc_html($company_type ?: '—'); ?></td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e('Help Intent', 'usegeo'); ?></th>
                <td><?php echo esc_html($help_intent ?: '—'); ?></td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e('Support Level', 'usegeo'); ?></th>
                <td><?php echo esc_html($support_level ?: '—'); ?></td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e('Website Input', 'usegeo'); ?></th>
                <td><?php echo esc_html($scanner_query ?: '—'); ?></td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e('Submitted At', 'usegeo'); ?></th>
                <td><?php echo esc_html($submitted_at ?: '—'); ?></td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e('Source', 'usegeo'); ?></th>
                <td><?php echo esc_html($source ?: '—'); ?></td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e('Notification Status', 'usegeo'); ?></th>
                <td>
                    <?php
                    if ($notification_sent === 'yes') {
                        echo '<strong style="color:#15803d;">sent</strong>';
                    } elseif ($notification_error) {
                        echo '<strong style="color:#b91c1c;">failed</strong>';
                    } else {
                        echo '<strong style="color:#92400e;">not sent</strong>';
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e('Notification Recipient', 'usegeo'); ?></th>
                <td><?php echo esc_html($notification_recipient ?: '—'); ?></td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e('Notification Error', 'usegeo'); ?></th>
                <td><?php echo esc_html($notification_error ?: '—'); ?></td>
            </tr>
        </tbody>
    </table>
    <?php
}

remove_filter('manage_usegeo_lead_posts_columns', 'usegeo_lead_admin_columns_v2');
remove_filter('manage_edit-usegeo_lead_columns', 'usegeo_lead_admin_columns_v2');
remove_action('manage_usegeo_lead_posts_custom_column', 'usegeo_render_lead_admin_column_v2', 10);
remove_action('add_meta_boxes', 'usegeo_register_lead_meta_box_v2');

function usegeo_lead_admin_columns_v3($columns) {
    return usegeo_lead_admin_columns_v2($columns);
}
add_filter('manage_usegeo_lead_posts_columns', 'usegeo_lead_admin_columns_v3');
add_filter('manage_edit-usegeo_lead_columns', 'usegeo_lead_admin_columns_v3');

function usegeo_render_lead_admin_column_v3($column, $post_id) {
    if ($column === 'company_type') {
        $company_type = get_post_meta($post_id, 'company_type', true);
        $company_type_options = usegeo_get_lead_company_type_options();
        echo esc_html(isset($company_type_options[$company_type]) ? $company_type_options[$company_type] : ($company_type ?: '-'));
        return;
    }

    if ($column === 'help_intent') {
        $help_intent = get_post_meta($post_id, 'help_intent', true);
        $help_intent_labels = usegeo_get_help_intent_labels();
        $help_intent_label = isset($help_intent_labels[$help_intent]) ? $help_intent_labels[$help_intent] : $help_intent;

        if ($help_intent === 'open_to_specialist') {
            echo usegeo_render_lead_priority_badge($help_intent_label, 'success');
        } elseif ($help_intent === 'prioritized_next_steps') {
            echo usegeo_render_lead_priority_badge($help_intent_label, 'info');
        } else {
            echo esc_html($help_intent_label ?: '-');
        }
        return;
    }

    if ($column === 'support_level') {
        $support_level = get_post_meta($post_id, 'support_level', true);
        $support_level_labels = usegeo_get_support_level_labels();
        $support_level_label = isset($support_level_labels[$support_level]) ? $support_level_labels[$support_level] : $support_level;

        if ($support_level === 'ongoing_support') {
            echo usegeo_render_lead_priority_badge($support_level_label, 'success');
        } elseif ($support_level === 'small_project') {
            echo usegeo_render_lead_priority_badge($support_level_label, 'info');
        } elseif ($support_level === 'not_sure') {
            echo usegeo_render_lead_priority_badge($support_level_label, 'warn');
        } else {
            echo esc_html($support_level_label ?: '-');
        }
        return;
    }

    usegeo_render_lead_admin_column_v2($column, $post_id);
}
add_action('manage_usegeo_lead_posts_custom_column', 'usegeo_render_lead_admin_column_v3', 10, 2);

function usegeo_register_lead_meta_box_v3() {
    add_meta_box(
        'usegeo-lead-details-v3',
        __('Lead Details', 'usegeo'),
        'usegeo_render_lead_meta_box_v3',
        'usegeo_lead',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'usegeo_register_lead_meta_box_v3');

function usegeo_render_lead_meta_box_v3($post) {
    $work_email = get_post_meta($post->ID, 'work_email', true);
    $company_type = get_post_meta($post->ID, 'company_type', true);
    $help_intent = get_post_meta($post->ID, 'help_intent', true);
    $support_level = get_post_meta($post->ID, 'support_level', true);
    $scanner_query = get_post_meta($post->ID, 'scanner_query', true);
    $submitted_at = get_post_meta($post->ID, 'submitted_at', true);
    $source = get_post_meta($post->ID, 'source', true);
    $notification_sent = get_post_meta($post->ID, 'notification_sent', true);
    $notification_recipient = get_post_meta($post->ID, 'notification_recipient', true);
    $notification_error = get_post_meta($post->ID, 'notification_error', true);
    $company_type_options = usegeo_get_lead_company_type_options();
    $help_intent_labels = usegeo_get_help_intent_labels();
    $support_level_labels = usegeo_get_support_level_labels();
    $company_type_label = isset($company_type_options[$company_type]) ? $company_type_options[$company_type] : $company_type;
    $help_intent_label = isset($help_intent_labels[$help_intent]) ? $help_intent_labels[$help_intent] : $help_intent;
    $support_level_label = isset($support_level_labels[$support_level]) ? $support_level_labels[$support_level] : $support_level;
    ?>
    <table class="form-table" role="presentation">
        <tbody>
            <tr>
                <th scope="row"><?php esc_html_e('Work Email', 'usegeo'); ?></th>
                <td><?php echo esc_html($work_email ?: '-'); ?></td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e('Company Type', 'usegeo'); ?></th>
                <td><?php echo esc_html($company_type_label ?: '-'); ?></td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e('Help Intent', 'usegeo'); ?></th>
                <td>
                    <?php
                    if ($help_intent === 'open_to_specialist') {
                        echo usegeo_render_lead_priority_badge($help_intent_label, 'success');
                    } elseif ($help_intent === 'prioritized_next_steps') {
                        echo usegeo_render_lead_priority_badge($help_intent_label, 'info');
                    } else {
                        echo esc_html($help_intent_label ?: '-');
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e('Support Level', 'usegeo'); ?></th>
                <td>
                    <?php
                    if ($support_level === 'ongoing_support') {
                        echo usegeo_render_lead_priority_badge($support_level_label, 'success');
                    } elseif ($support_level === 'small_project') {
                        echo usegeo_render_lead_priority_badge($support_level_label, 'info');
                    } elseif ($support_level === 'not_sure') {
                        echo usegeo_render_lead_priority_badge($support_level_label, 'warn');
                    } else {
                        echo esc_html($support_level_label ?: '-');
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e('Website Input', 'usegeo'); ?></th>
                <td><?php echo esc_html($scanner_query ?: '-'); ?></td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e('Submitted At', 'usegeo'); ?></th>
                <td><?php echo esc_html($submitted_at ?: '-'); ?></td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e('Source', 'usegeo'); ?></th>
                <td><?php echo esc_html($source ?: '-'); ?></td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e('Notification Status', 'usegeo'); ?></th>
                <td>
                    <?php
                    if ($notification_sent === 'yes') {
                        echo '<strong style="color:#15803d;">sent</strong>';
                    } elseif ($notification_error) {
                        echo '<strong style="color:#b91c1c;">failed</strong>';
                    } else {
                        echo '<strong style="color:#92400e;">not sent</strong>';
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e('Notification Recipient', 'usegeo'); ?></th>
                <td><?php echo esc_html($notification_recipient ?: '-'); ?></td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e('Notification Error', 'usegeo'); ?></th>
                <td><?php echo esc_html($notification_error ?: '-'); ?></td>
            </tr>
        </tbody>
    </table>
    <?php
}
