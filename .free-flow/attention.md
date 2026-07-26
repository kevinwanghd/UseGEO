---
name: attention
description: UseGEO 项目启动必读——每次 Claude Code 会话启动时加载。
---

# UseGEO 启动必读

## 项目基本信息

- **项目名**：UseGEO
- **描述**：WordPress-first GEO/AI visibility education, assessment, and lead capture
- **阶段**：MVP
- **运行时源码**：`wordpress/theme/usegeo/`（**真相源**）
- **原型空间**：`src/`（上游/原型，非运行时真相）
- **打包产物**：`dist/`（不可编辑）

## 构建命令

无本地构建/测试命令。验证在本地或 staging WordPress 环境手动进行：

```bash
git status --short                    # 检查本地变更
ls wordpress/theme/usegeo             # 检查活跃主题
```

### 验证路径

- **首页漏斗**：Homepage → Scanner → Lead form
- **内容漏斗**：Article page → Sidebar CTA → Homepage scanner → Lead form
- **后台确认**：WP admin → UseGEO Leads / Case Submissions

## 目录禁区

- `src/` 是原型空间，**不要当作运行时真相**
- `dist/` 是打包产物，不可编辑
- `node_modules/` 禁止直接修改

## 栈约定

### 代码组织

```
src/
├── modules/         # geo-visibility-scanner, roi-calculator
├── shared/
│   ├── styles/      # 全局变量、通用样式
│   ├── utils/      # 通用函数
│   └── components/ # 按钮、输入框、卡片、表单块、CTA 块
└── templates/       # 页面级组合结构

wordpress/theme/usegeo/   # 运行时主题（真相源）
wordpress/plugins/        # 插件或 shortcode 封装
```

### Git 分支

- `main`
- `feature/<topic>`
- `fix/<topic>`
- `docs/<topic>`

### Commit 标准

简化版 conventional commits：

```
feat(scope): summary    # 新功能
fix(scope): summary     # 修复问题
docs(scope): summary    # 文档修改
refactor(scope): summary # 重构
style(scope): summary   # 纯样式调整
chore(scope): summary   # 杂项维护
```

## 易错点

1. **真相源混淆**：运行时代码在 `wordpress/theme/usegeo/`，不是在 `src/`
2. **无本地构建**：WordPress 主题验证需要部署到 WordPress 环境
3. **WordPress 集成**：自定义工具用 `shortcode` 或自定义 HTML 模块嵌入

## gstack

使用 `/browse` 进行所有网页浏览。禁止使用 `mcp__claude-in-chrome__*` 工具。

## 治理规则（详见 `.governance/` 和 `governance/`）

- AI 编辑证据追加到 `.governance/ai-evidence.jsonl`
- 测试运行用 `record_test_run.py` 包装
- Commit trailer（AI-Usage 等）由 git hook 自动写入
- MR 用 `create_mr.py --why "<任务背景>"` 创建

## 关键上下文文件

| 文件 | 约束内容 |
|------|----------|
| `docs/CONTEXT.md` | 三层质量漏斗 + 上下文加载策略 |
| `docs/architecture.md` | 系统层级、依赖、高风险模块 |
| `docs/interfaces.md` | 表单路由、hooks、参数契约、安全约束 |
| `docs/data-model.md` | Post types、meta fields、ER diagram |
| `docs/decisions.md` | 设计决策 WHY、隐式知识、踩坑记录 |
| `docs/rules.md` | 可执行约束 + gate 级别 + 成熟度 |
| `docs/verification.md` | 验收流程 + SpecGate + 质量报告输出 |

## inbox / journal 落点

- **灵感收件箱**：`.free-flow/inbox/`
- **工作日志**：`.free-flow/journal/`
- **运行痕迹**（AI 证据）：`.free-flow/runtime/`（已 gitignore）
