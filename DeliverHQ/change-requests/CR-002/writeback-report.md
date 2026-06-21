# Writeback Report: LLM Summary 启用与验证

## 归档日期

2026-06-21

## 代码变更

- `wordpress/theme/usegeo/inc/scanner-llm.php`
  - 新增 `usegeo_scan_get_llm_base_url()`
  - 默认模型从 `gpt-4o-mini` 调整为 `gpt-5.4-mini`
  - `wp_remote_post()` 改为使用可配置 base URL

## 文档更新

- `request.md`：记录代理 base_url 与模型选择
- `acceptance-spec.md`：补充 `USEGEO_LLM_BASE_URL` 配置契约
- `design/design-decisions.md`：记录代理 base_url 与默认模型决策
- `human-decisions.md`：记录 Kevin 的 base_url / model 裁决
- `traceability.yml`：映射 scanner-llm.php
- `verification-manifest.yml`：增加 fallback / llm / secret-scan 验证入口
- `review-report.md` / `quality-report.md`：记录门禁结果

## 知识沉淀

本次无新规则。

项目知识：

1. UseGEO 的 LLM provider 需要支持 OpenAI-compatible 代理，不能硬编码官方 OpenAI endpoint。
2. 代理地址使用 `USEGEO_LLM_BASE_URL` 注入，支持填 `/v1` 或完整 `/chat/completions`。
3. 默认模型用 `gpt-5.4-mini`，需要更强时可改 `USEGEO_LLM_MODEL=gpt-5.5`。
4. 生产 key 仍必须由 Kevin 手工配置，不能进入 Git。

## 可追溯性

- `traceability.yml` 覆盖 `scanner-llm.php`。
- `evidence/changed-files.json` 记录实际代码变更。
- ReviewGate / QualityGate 均已 PASS。

## 后续动作

1. 合并并部署本次 base_url 支持。
2. Kevin 在生产 `wp-config.php` 配置：
   - `USEGEO_LLM_API_KEY`
   - `USEGEO_LLM_BASE_URL=https://chat.aiprox.net`
   - `USEGEO_LLM_MODEL=gpt-5.4-mini`
3. 清缓存。
4. 跑：
   - `verify_llm_config.py --mode llm`
   - `verify_llm_config.py --mode secret-scan`
