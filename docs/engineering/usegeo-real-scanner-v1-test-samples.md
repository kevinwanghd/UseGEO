# Real Scanner v1 测试样本

本文档记录 RS-16 准备的 5 个测试网站样本，用于验收 Real Scanner v1 功能。

## 测试样本

### 样本 1: B2B SaaS 官网

**URL**: `https://linear.app`

**预期**:
- 有清晰的 title 和 H1
- 有 Organization schema
- 有 pricing/use case 相关内容
- 分数应该在 70-90 之间

**验收点**:
- [ ] 能正常抓取
- [ ] 返回有效 JSON
- [ ] title 和 H1 都能检测到
- [ ] 至少有 3 个 schema types

---

### 样本 2: Agency / Consultancy 官网

**URL**: `https://www.deptagency.com`

**预期**:
- 有案例研究相关信号
- 有 comparison/alternative 语言
- 有客户证明相关词汇
- 分数应该在 50-75 之间

**验收点**:
- [ ] 能正常抓取
- [ ] detected_signals 有多个 true
- [ ] 有 comparison_terms 检测到

---

### 样本 3: 内容站 / Blog

**URL**: `https://ahrefs.com/blog`

**预期**:
- 有大量 H2 标题
- 可能有 FAQ schema
- 可能有 Article schema
- 分数应该在 60-80 之间

**验收点**:
- [ ] 能正常抓取
- [ ] h2 数量较多
- [ ] schema_types 包含 Article 或 FAQPage

---

### 样本 4: 电商站

**URL**: `https://shopify.com`

**预期**:
- 有 Product schema
- 有 pricing 相关内容
- 有 use case 相关内容
- 分数应该在 60-85 之间

**验收点**:
- [ ] 能正常抓取
- [ ] schema_types 包含 Product
- [ ] detected_signals.has_pricing_terms 为 true

---

### 样本 5: 故意错误 URL

**URL**: `https://this-domain-does-not-exist-12345.com`

**预期**:
- 返回 `fetch_failed` 或 `fetch_timeout`
- 不崩溃
- 前端显示友好错误

**验收点**:
- [ ] 返回合适的错误码 (502/504)
- [ ] error_code 是 `fetch_failed` 或类似
- [ ] 前端不显示技术错误

---

## 额外测试场景

### SSRF 防护测试

| URL | 预期结果 |
|-----|----------|
| `http://localhost` | blocked_url |
| `http://127.0.0.1` | blocked_url |
| `http://192.168.1.1` | blocked_url |
| `file:///etc/passwd` | blocked_url |
| `ftp://example.com` | blocked_url |

### URL 格式测试

| URL | 预期结果 |
|-----|----------|
| `not-a-url` | invalid_url |
| 无 URL | 400 Bad Request |

---

## Rate Limit 测试

连续请求超过 6 次后应返回 429 `rate_limited`。

---

## LLM 关闭场景测试

配置 `USEGEO_LLM_API_KEY` 为空时：
- [ ] scanner 仍然返回结果
- [ ] llm_summary.available 为 false
- [ ] 使用 fallback summary
