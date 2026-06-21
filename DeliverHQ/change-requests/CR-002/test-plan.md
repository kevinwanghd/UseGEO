# Test Plan: LLM Summary 启用与验证

## 测试范围

- 配置能力：`USEGEO_LLM_BASE_URL`、`USEGEO_LLM_MODEL`、`USEGEO_LLM_API_KEY`
- fallback：无 key 时 `llm_summary.available=false`
- secret：公开源码/REST 响应不暴露 key
- 生产人工验证：配置 key 后 `llm_summary.available=true`

## 自动验证

1. `node --check wordpress/theme/usegeo/assets/js/scanner.js`
2. `python DeliverHQ/change-requests/CR-001/evidence/static_verify.py`
3. 静态断言：`scanner-llm.php` 包含 `usegeo_scan_get_llm_base_url`，默认模型为 `gpt-5.4-mini`，`wp_remote_post()` 不再硬编码官方 OpenAI endpoint。
4. 生产 fallback 验证：`python DeliverHQ/change-requests/CR-002/evidence/verify_llm_config.py --mode fallback`

## 人工/生产验证

1. Kevin 在 `wp-config.php` 填入：
   - `USEGEO_LLM_API_KEY`
   - `USEGEO_LLM_BASE_URL=https://chat.aiprox.net`
   - `USEGEO_LLM_MODEL=gpt-5.4-mini`
2. 清缓存。
3. 运行：
   - `python DeliverHQ/change-requests/CR-002/evidence/verify_llm_config.py --mode llm`
   - `python DeliverHQ/change-requests/CR-002/evidence/verify_llm_config.py --mode secret-scan`

## 通过标准

- base_url 支持已合并部署。
- 配置前 fallback 通过。
- 配置后 LLM available=true。
- secret scan 不发现 `sk-` 或代理 key 片段。
