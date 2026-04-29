# UseGEO 文档管理说明

## 1. 目标

这套文档管理方法的目标只有三个：

1. 文档容易找。
2. 同一主题只有一个主版本。
3. 项目早期不因为文档过多而失控。

这套规则参考了 `D:\Code\NexifyAI` 的做法，但做了明显收敛，更适合 `UseGEO` 当前阶段。

## 2. 总体原则

### 2.1 少目录

`UseGEO` 只保留 3 个核心文档目录：

- `docs/product`
- `docs/engineering`
- `docs/reports`

不要再继续往上分更多一级大类，除非项目进入多人协作和长期维护阶段。

### 2.2 一事一主文档

同一主题只允许有一个主文档，避免同时存在：

- `PRD-v1`
- `PRD-final`
- `PRD-new`
- `PRD-最新版`

这类重复文件。

正确做法：

- 持续更新主文档
- 只有在需要保留阶段性结论或交付记录时，才新增报告型文档

### 2.3 产品文档与过程文档分开

- 长期有效的内容放 `product` 或 `engineering`
- 一次性的分析、交付记录、验收记录放 `reports`

这样后续不会把临时文件和长期规范混在一起。

## 3. 目录结构

建议固定为：

```text
docs/
  README.md
  product/
    usegeo-prd.md
    usegeo-design-guidelines.md
  engineering/
    usegeo-development-plan.md
  reports/
```

说明：

- `docs/README.md`：文档导航和规则入口
- `docs/product/`：产品定义、设计说明、业务逻辑
- `docs/engineering/`：开发计划、技术落地、上线清单
- `docs/reports/`：阶段性分析、验收记录、交付总结

## 4. 每个目录放什么

## 4.1 `docs/product`

放长期有效、定义“要做什么”的文档：

- PRD
- 设计说明
- 页面结构说明
- 核心交互定义

不要放：

- 测试结果
- 会议纪要
- 某次开发日报

## 4.2 `docs/engineering`

放长期有效、定义“怎么做”的文档：

- 开发计划
- 页面拆分
- 组件拆分
- WordPress 集成方式
- 上线前检查清单

不要放：

- 产品背景分析
- 市场研究
- 临时想法草稿

## 4.3 `docs/reports`

只放阶段性输出：

- 需求对齐记录
- 验收报告
- 上线复盘
- gap analysis

命名要带日期，例如：

- `2026-04-21-mvp-docs-bootstrap.md`
- `2026-04-25-homepage-qa-report.md`

## 5. 命名规范

### 5.1 主文档

主文档使用稳定命名，不带版本号：

- `usegeo-prd.md`
- `usegeo-design-guidelines.md`
- `usegeo-development-plan.md`

原因：

- 这样引用稳定
- 后续更新不会制造新文件
- 团队更容易知道哪个是当前版本

### 5.2 报告文档

报告类文件使用日期前缀：

- `YYYY-MM-DD-topic.md`

例如：

- `2026-04-21-mvp-scope-review.md`
- `2026-04-28-launch-checklist-review.md`

## 6. 新建文档判断规则

新增文档前，先问 3 个问题：

1. 这是不是已有主文档的一部分？
2. 这份内容是长期有效，还是一次性记录？
3. 如果半年后再看，这份文档还有独立价值吗？

判断标准：

- 如果是已有主题的新内容，直接更新原文档
- 如果是阶段性交付或分析，放 `reports`
- 如果只是零散想法，不要立刻建新文档

## 7. 推荐工作流

### 新需求进入时

1. 更新 `docs/product/usegeo-prd.md`
2. 如果影响设计，再更新 `docs/product/usegeo-design-guidelines.md`
3. 如果准备进入开发，再更新 `docs/engineering/usegeo-development-plan.md`

### 开发过程中

- 不要新建“临时 PRD”
- 不要新建“最终设计稿 v7”
- 需要记录阶段结论时，写到 `docs/reports`

### 上线前后

- 验收结果写到 `docs/reports`
- 如果发现长期规范变了，再回写主文档

## 8. 当前阶段建议

对 `UseGEO` 当前规模，建议先只保留以下 3 份主文档：

- `docs/product/usegeo-prd.md`
- `docs/product/usegeo-design-guidelines.md`
- `docs/engineering/usegeo-development-plan.md`

这已经足够支撑：

- 产品讨论
- 设计执行
- 开发排期

在没有多人并行和复杂交付之前，不建议继续增加目录。

## 9. 禁止事项

以下做法应尽量避免：

- 在仓库根目录继续堆新的 `.md`
- 同一主题同时保留多个版本文件
- 需求、设计、开发、验收混在同一个文档里
- 用 `final`、`最新版`、`修正版` 这类不稳定命名

## 10. 一句话规则

**主文档长期维护，报告文档按日期沉淀，根目录不堆 Markdown。**
