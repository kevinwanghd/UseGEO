# Hermes Coding Knowledge Base

## 这是什么
这是 UseGEO 项目的 Hermes 编码知识库。

它不是业务文档，也不是产品文档。
它的作用是给 Hermes 和其他开发 Agent 提供一套稳定的执行规则，让它们在这个项目里：

- 找对入口
- 改对目录
- 控制范围
- 少犯重复错误
- 改完知道怎么验证
- 逐步积累长期有效的项目知识

一句话理解：

**这是 UseGEO 项目的 Agent 工作手册。**

---

## 为什么需要这套文件

UseGEO 当前阶段有几个很容易让 Agent 出错的点：

- 项目是 **WordPress-first**
- 运行时代码在 `wordpress/theme/usegeo/`
- `src/` 存在，但当前不是默认运行时主入口
- 首页、Scanner、Lead Capture、CTA 都属于关键转化路径
- 当前目标是 MVP 快速验证，不是过度工程化

如果没有一套明确的项目知识文件，Agent 很容易：

- 改错目录
- 误把 `src/` 当主入口
- 过早插件化
- 过早框架化
- 顺手重构太多
- 没验证就说完成

这套知识库就是为了解决这些问题。

---

## 当前目录结构

```text
docs/hermes-coding/
  README.md
  INDEX.md
  ROUTING.md

  playbooks/
    source-of-truth.md
    project-map.md

  principles/
    execution-policy.md

  workflows/
    verification-workflow.md

  memory/
    project-context.md
    anti-patterns.md
```

---

## 最小使用方式

### 任务开始前
先读：
1. `playbooks/source-of-truth.md`
2. `playbooks/project-map.md`
3. `ROUTING.md`

### 执行任务时
遵守：
4. `principles/execution-policy.md`

### 准备收尾时
检查：
5. `workflows/verification-workflow.md`

这就是最小闭环。

---

## Hermes 的一句话工作方式

**先看 `source-of-truth.md` 和 `project-map.md`，再按 `ROUTING.md` 选择任务分支；执行时遵守 `execution-policy.md`，收尾前按 `verification-workflow.md` 验证，并用 memory 文件避免重复犯错。**
