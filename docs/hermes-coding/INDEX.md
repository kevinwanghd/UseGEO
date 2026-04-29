# Hermes Coding Index

## 目的
这是 UseGEO 项目的 Hermes 知识库导航页。

如果不确定从哪里开始，默认先看：
1. `playbooks/source-of-truth.md`
2. `playbooks/project-map.md`
3. `ROUTING.md`

---

## 最常用入口

### 任务开始前
先看：
- `playbooks/source-of-truth.md`
- `playbooks/project-map.md`
- `ROUTING.md`

### 想知道项目当前长期背景
看：
- `memory/project-context.md`

### 想避免常见错误
看：
- `memory/anti-patterns.md`

### 想知道默认执行方式
看：
- `principles/execution-policy.md`

### 想知道完成前如何验证
看：
- `workflows/verification-workflow.md`

---

## 文件结构总览

```text
docs/hermes-coding/
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

## 一句话使用方式

**任务开始先看 `source-of-truth.md` 和 `project-map.md`，再按 `ROUTING.md` 选分支；执行时遵守 `execution-policy.md`，收尾前按 `verification-workflow.md` 验证。**
