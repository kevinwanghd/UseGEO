<!-- agentgate-pr-bind {"base_ref": "origin/master", "changed_paths": [".github/pull_request_template.md", ".github/workflows/agentgate.yml", "docs/governance/mr-spec.md", "docs/governance/risk-types.md"], "diff_fingerprint": "ac14233c4c3e176329c7d550c05c293067b6eea4100e9c51ec00211f765d3b39", "prepared_from_sha": "50ff23aec032ae58c83fbb1f2de0c521bcd39b9c", "schema_version": "agentgate.io/pr-description-binding/v1"} -->

## 背景

为 UseGEO 接入中心化 AgentGate GitHub 门禁，让 Pull Request 自动校验治理描述、风险扫描和基础质量信号；新增 thin workflow 后，AgentGate 在 main 更新时仓库会直接使用中心版本。

## 变更内容

新增 GitHub Actions AgentGate thin workflow，调用 kevinwanghd/AgentGate 的可复用门禁；新增 GitHub PR 模板；新增 docs/governance 下的 MR 规范和风险类型参考文档。

## 不包含的内容

无

## 自测确认

pass - python E:\ClaudeCode\AgentGate-rollout-kit-clean\scripts\agentgate.py pr verify --target-branch origin/master --config governance.config.yml；pass - git diff --check

## 风险与回滚

低风险。变更仅新增 GitHub 门禁入口、PR 模板和治理文档，不修改 WordPress 运行时代码；如 workflow 配置异常，可回滚本提交恢复原流程。

## 关联

-

---

<details>
<summary>📊 治理元数据（CI 自动采集）</summary>

- **AI-Usage**: used
- **Tested**: pass - pending AgentGate pr verify after manifest generation

</details>
