# Design Spec: Real Scanner v1 真实诊断升级

> B 端 UI 设计规格。本次改造复用现有首页 scanner UI 框架，替换数据源 + 新增 evidence 区 + 错误状态展示。
> 不涉及全新视觉设计；视觉规范沿用现有主题（base.css / homepage.css / single.css）。

## 1. 设计范围

### 1.1 不变区域（复用现有）

| 区域 | 文件 | 说明 |
|---|---|---|
| Scanner 输入区 | `template-parts/scanner/scanner.php` L28-L35 | URL 输入 + Run assessment 按钮，结构不变 |
| Loading 区 | `scanner.php` L63-L78 | 三段进度（Read site structure / Map AI signals / Prepare next steps），结构不变 |
| 雷达图 | `scanner.php` L104-L123 | SVG radar chart，结构不变，数据源替换 |
| 总分展示 | `scanner.php` L82-L86 | 分数 + grade label，结构不变，数据源替换 |
| 模型分数 | `scanner.php` L88-L101 | ChatGPT / Claude / Gemini 三列，结构不变 |
| Lead 表单 | `scanner.php` L136-L217 | 字段不变，新增隐藏字段携带 scan snapshot |

### 1.2 修改区域

| 区域 | 文件 | 改动内容 |
|---|---|---|
| Findings 区 | `scanner.php` L125-L134 | 数据源从前端模拟改为 API 真实 findings |
| Summary 区 | `scanner.php` L126-L128 | 数据源改为 LLM summary 或规则版 fallback |
| Lead 表单隐藏字段 | `scanner.php` L147-L151 | 新增 hidden inputs 携带 scan snapshot |

### 1.3 新增区域

| 区域 | 文件 | 说明 |
|---|---|---|
| Evidence 区 | `scanner.php`（结果区内新增） | 展示真实抓取信号：title / H1 / schema / detected signals / word count |
| 错误状态区 | `scanner.php`（结果区内新增） | 按 error_code 映射友好文案，替代 raw error |
| 后台 lead 列表列 | `inc/admin.php` | 新增 `scan_total_score` + `weakest_dimension` 两列 |
| 邮件正文 | `inc/forms.php` | 新增 5 行 scan 摘要 |

## 2. 前端改动规格

### 2.1 scanner.js 改造

**当前行为**：用户输入 URL → 前端模拟生成分数 → 展示结果。

**改造后行为**：

```
用户输入 URL → 检查 feature flag
  ├── flag=true → POST /wp-json/usegeo/v1/scan → 渲染真实结果
  └── flag=false → 走当前模拟逻辑（30 天保留期）
```

**API 调用**：
- 方法：`POST`
- URL：`/wp-json/usegeo/v1/scan`
- Headers：`Content-Type: application/json`
- Body：`{ "url": "用户输入" }`
- nonce：通过 `wp_localize_script` 注入或 `X-WP-Nonce` header

**成功响应渲染**：
1. 总分 → `data-usegeo-score`
2. 5 维度分 → 雷达图 `data-usegeo-radar-val-1` 到 `data-usegeo-radar-val-5`
3. 模型分 → `data-usegeo-model-gpt` / `data-usegeo-model-claude` / `data-usegeo-model-gemini`（由 total + 维度分推导或直接用 API 返回）
4. findings → `data-usegeo-findings`（替换为 API 返回的 findings 列表）
5. summary → `data-usegeo-summary`（LLM summary 或规则版 fallback）
6. evidence 区 → 新增 DOM 区块，展示 `extracted` 关键字段

**错误响应渲染**：
- 隐藏 loading 区
- 显示错误状态区
- 按 `error_code` 映射文案：

| error_code | 用户文案 |
|---|---|
| invalid_url | Please enter a valid website URL (e.g., https://yourcompany.com) |
| blocked_url | This URL cannot be scanned. Please enter a public website URL. |
| fetch_timeout | The scan timed out. Please try again in a moment. |
| fetch_failed | We could not reach this website. Please check the URL and try again. |
| non_html_response | This URL does not return a web page. Please enter a website URL. |
| parse_failed | We could not analyze this page. Please try a different URL. |
| rate_limited | You have run several scans recently. Please wait a minute and try again. |
| internal_error | Something went wrong on our end. Please try again later. |

**scan snapshot 携带**：
- scan 成功后，将关键数据写入 hidden fields：
  - `scan_total_score`
  - `scan_entity_clarity` / `scan_content_structure` / `scan_proof_signals` / `scan_comparison_readiness` / `scan_ai_extractability`
  - `scan_completed_at`
  - `scan_findings_summary`（findings 拼接文本）
  - `scan_llm_summary`（LLM summary 全文，如有）
- scan 失败时，hidden fields 留空，lead 表单仍可提交

### 2.2 Evidence 区设计

位置：findings 区下方，lead 表单上方。

结构（B 端低保真，HTML 文本为主）：

```html
<div class="usegeo-scanner-evidence" data-usegeo-evidence>
  <span class="usegeo-scanner-evidence__label">What we found on your site</span>
  <dl>
    <dt>Page title</dt>
    <dd data-usegeo-evidence-title>...</dd>
    <dt>H1</dt>
    <dd data-usegeo-evidence-h1>...</dd>
    <dt>Schema detected</dt>
    <dd data-usegeo-evidence-schema>Organization</dd>
    <dt>Word count</dt>
    <dd data-usegeo-evidence-wordcount>820</dd>
    <dt>Proof signals</dt>
    <dd data-usegeo-evidence-proof>case study, results</dd>
    <dt>Comparison signals</dt>
    <dd data-usegeo-evidence-comparison>Not detected</dd>
  </dl>
</div>
```

样式：沿用 `.usegeo-card` 卡片风格，不需要新视觉规范。

## 3. 后台改动规格

### 3.1 lead 列表新增列

文件：`inc/admin.php` → `usegeo_lead_admin_columns`

新增两列（在 `scanner_query` 列之后、`notification_status` 列之前）：

```php
$columns['scan_total_score'] = __('GEO Score', 'usegeo');
$columns['weakest_dimension'] = __('Weakest Dimension', 'usegeo');
```

渲染逻辑：
- `scan_total_score`：读取 `scan_total_score` post meta，显示数字（如 72）；旧 lead 无值则显示 `-`。
- `weakest_dimension`：从 5 个 `scan_*` 维度 meta 中取最低值对应的维度名，显示文本（如 `comparison_readiness`）；旧 lead 无值则显示 `-`。

两列默认可见，可通过 filter `usegeo_lead_admin_columns_visible` 关闭。

### 3.2 邮件正文改动

文件：`inc/forms.php` → `usegeo_send_lead_notification`

在现有邮件正文末尾（`Submitted at:` 之后）追加：

```
GEO Readiness Score: {scan_total_score or (scan unavailable)}
Weakest Dimension: {weakest_dimension or (scan unavailable)}
Top Finding: {findings[0].title or (scan unavailable)}
Recommended Next Step: {llm_summary.top_recommendations[0] or fallback rule text or (scan unavailable)}
```

Website 行已有（`scanner_query`），不重复添加。

## 4. 后端模块设计

### 4.1 新增文件

| 文件 | 职责 |
|---|---|
| `inc/scanner-api.php` | 注册 REST route `POST /wp-json/usegeo/v1/scan`，编排抓取→解析→评分→LLM→响应 |
| `inc/scanner-fetch.php` | URL 校验 + SSRF 防护 + HTML 抓取（`wp_remote_get`） |
| `inc/scanner-parse.php` | HTML 解析（`DOMDocument`）→ 提取 title / meta / H1 / H2 / schema / canonical / signals |
| `inc/scanner-score.php` | 规则评分引擎（Q1 信号清单 + 权重） |
| `inc/scanner-llm.php` | LLM 调用 + 降级（`wp_remote_post` → OpenAI） |
| `inc/scanner-ratelimit.php` | rate limit 逻辑（Transient） |

### 4.2 加载方式

在 `functions.php` 或 `inc/setup.php` 中注册：

```php
require_once get_template_directory() . '/inc/scanner-api.php';
// scanner-api.php 内部 require 其余 scanner-*.php
```

不修改 `functions.php` 现有 require 顺序（CR-LEGACY-SCAN 基线约束）。

### 4.3 REST route 注册

```php
register_rest_route('usegeo/v1', '/scan', array(
    'methods'  => 'POST',
    'callback' => 'usegeo_scan_endpoint',
    'permission_callback' => '__return_true', // 公开端点，靠 nonce/origin 校验
));
```

nonce/origin 校验在 `usegeo_scan_endpoint` 内部执行，与 `usegeo_verify_public_form_nonce` 逻辑一致。

## 5. Feature Flag 实现

### 5.1 后端

```php
function usegeo_is_real_scanner_enabled() {
    if (defined('USEGEO_REAL_SCANNER_ENABLED') && !USEGEO_REAL_SCANNER_ENABLED) {
        return false;
    }
    return (bool) apply_filters('usegeo_real_scanner_enabled', true);
}
```

- WP 常量 `USEGEO_REAL_SCANNER_ENABLED = false` 优先。
- filter 默认 true。
- 通过 `wp_localize_script` 传给前端 `scanner.js`。

### 5.2 前端

scanner.js 在 init 时读取 `window.usegeoScannerConfig.realScannerEnabled`：
- true → 走真实 API 路径。
- false → 走当前模拟逻辑。
- 不需要刷新页面切换（通过 `wp_localize_script` 在页面加载时决定）。

## 6. 视觉规范

沿用现有主题，不新增：

- 色彩：`assets/css/base.css` 中的 CSS 变量（`--usegeo-color-*`）
- 字体：Inter（已在 `inc/assets.php` 中 enqueue）
- 间距：沿用 `.usegeo-card` / `.usegeo-shell` 的 padding/margin
- 圆角：沿用现有按钮 / 卡片圆角
- 响应式断点：沿用现有 media query

## 7. DesignGate 检查点（B 端）

- [x] `design/metadata.yml` 声明 `ui_type: B端`
- [x] `design/design-decisions.md` 已记录 Q1-Q5 裁决 + D1-D7 技术选型
- [x] `design/hi-fi-spec.md` 已写实际内容（无 `{{}}` 模板变量）
- [x] 视觉规范沿用现有主题（色彩/字体/间距已引用）
