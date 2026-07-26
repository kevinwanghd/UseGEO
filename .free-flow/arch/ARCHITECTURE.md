# UseGEO 架构现状

> 本文档描述系统层级、依赖关系和高风险模块。给工程师看的技术现状索引。

## 系统概览

UseGEO 是一个 WordPress-first MVP 站点，聚焦 GEO/AI 可见性教育和线索捕获。

```
┌─────────────────────────────────────────────────────────────┐
│                      WordPress Core                          │
│  ┌─────────────────────────────────────────────────────┐    │
│  │            wordpress/theme/usegeo/                   │    │
│  │  ┌─────────────┐  ┌─────────────┐  ┌────────────┐  │    │
│  │  │   Hero      │  │  Scanner    │  │  ROI Calc  │  │    │
│  │  │  (Landing)  │  │  Module     │  │   Module   │  │    │
│  │  └─────────────┘  └─────────────┘  └────────────┘  │    │
│  └─────────────────────────────────────────────────────┘    │
│                              │                               │
│  ┌─────────────────────────────────────────────────────┐    │
│  │              WordPress Plugins / Shortcodes          │    │
│  └─────────────────────────────────────────────────────┘    │
└─────────────────────────────────────────────────────────────┘
```

## 技术栈

| 层级 | 技术 |
|------|------|
| 框架 | WordPress |
| 前端工具 | HTML / CSS / JavaScript（原型在 `src/`）|
| 运行时 | `wordpress/theme/usegeo/` |
| 集成方式 | Custom HTML 模块 / Shortcode 插件 |

## 源码真相源

- **运行时**：`wordpress/theme/usegeo/` — 这是唯一的运行时真相
- **原型空间**：`src/` — 上游/原型，非运行时真相
- **打包产物**：`dist/` — 不可编辑

## 核心模块

### 1. Geo Visibility Scanner（品牌 AI 曝光度诊断器）

- 位置：`src/modules/geo-visibility-scanner/`（原型）/ `wordpress/theme/usegeo/`（运行时）
- 职责：输入品牌/关键词 → 评分 + 雷达图 + 表单
- 状态：MVP

### 2. ROI Calculator（AIGC 营销内容 ROI 试算器）

- 位置：`src/modules/roi-calculator/`（原型）/ `wordpress/theme/usegeo/`（运行时）
- 职责：输入文章数/获客成本/客单价 → ROI 对比图
- 状态：MVP

### 3. Lead Capture（线索捕获）

- 位置：`wordpress/theme/usegeo/`（表单）/ WordPress 后端（数据）
- 职责：表单收集企业邮箱/职位，存入 WP admin
- 状态：MVP

## 依赖关系

```text
用户输入 → Scanner/ROI 工具 → 结果展示 → Lead 表单 → WordPress 后端
     ↑                                                    ↓
  WordPress 主题层                              WP Admin (UseGEO Leads)
```

## 高风险模块

| 模块 | 风险 | 说明 |
|------|------|------|
| Scanner 工具 | 结果可信度 | MVP 使用模拟数据，需避免误导性表述 |
| Lead 表单 | 转化率 | 字段数量与线索质量需平衡 |
| WordPress 集成 | 部署依赖 | 无本地构建，需 WordPress 环境验证 |

## 上游原型

`src/` 目录是上游原型空间：
- 先在 `src/` 开发验证
- 再迁移到 `wordpress/theme/usegeo/`
- `src/` 不是 CI/CD 的目标

## 未来扩展方向

- 接入更真实的 AI 可见性检测逻辑
- 报告页或线索管理能力
- GEO 内容评分器、AI 引用追踪器等工具
