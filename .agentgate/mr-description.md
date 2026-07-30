<!-- agentgate-pr-bind {"base_ref": "origin/master", "changed_paths": [".github/pull_request_template.md", ".github/workflows/agentgate.yml", "docs/governance/mr-spec.md", "docs/governance/risk-types.md"], "diff_fingerprint": "50c71d38919277bb22411f5e066a606529e2d3e7b42daaddeb1c7dc759de00f7", "prepared_from_sha": "ae2898e7e8799976c6c8913f80fd10d8852eaf12", "schema_version": "agentgate.io/pr-description-binding/v1"} -->

## 背景

为 UseGEO 接入中心化 AgentGate GitHub 门禁，让 Pull Request 自动校验治理描述、风险扫描和基础质量信号；workflow 引用 AgentGate 的 github-stable 稳定通道，后续只要 AgentGate 更新该稳定分支，下游仓库会自动使用新版门禁。

## 变更内容

新增 GitHub Actions AgentGate thin workflow，调用 kevinwanghd/AgentGate 的可复用门禁并引用 github-stable；新增 GitHub PR 模板；新增 docs/governance 下的 MR 规范和风险类型参考文档。

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
