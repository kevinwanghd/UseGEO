# Routing Guide

## 目的
根据任务类型，快速决定 Hermes 应该先读取哪些文档和代码位置。
避免一上来读错目录、改错入口、信错文档。

---

## 总规则

1. **先判断任务类型，再加载对应文档**
2. **先读本地 source of truth，再行动**
3. **默认最小改动范围**
4. **第一次失败后先分析，不连续盲试**
5. **未验证前，不宣称完成**

---

## 默认起步顺序

任何任务开始前，默认先读：

1. `docs/hermes-coding/playbooks/source-of-truth.md`
2. `docs/hermes-coding/playbooks/project-map.md`

---

## 任务路由

### 如果任务是“先理解项目 / 理解模块”
先读：
1. `docs/hermes-coding/playbooks/project-map.md`
2. `docs/hermes-coding/playbooks/source-of-truth.md`

### 如果任务是“新增功能”
先读：
1. `docs/hermes-coding/playbooks/source-of-truth.md`
2. `docs/hermes-coding/playbooks/project-map.md`
3. `docs/product/usegeo-prd.md`
4. `docs/product/usegeo-design-guidelines.md`
5. `docs/engineering/usegeo-development-plan.md`
6. `docs/engineering/wordpress-integration.md`

### 如果任务是“修 Bug”
先读：
1. `docs/hermes-coding/playbooks/source-of-truth.md`
2. `docs/hermes-coding/playbooks/project-map.md`

### 如果任务是“改 UI / 样式 / 文案”
先读：
1. `docs/hermes-coding/playbooks/source-of-truth.md`
2. `docs/hermes-coding/playbooks/project-map.md`
3. `docs/product/usegeo-design-guidelines.md`

### 如果任务是“改 Lead Capture / 表单提交”
先读：
1. `docs/hermes-coding/playbooks/source-of-truth.md`
2. `docs/hermes-coding/playbooks/project-map.md`

### 如果任务是“做 Code Review”
先读：
1. `docs/hermes-coding/playbooks/source-of-truth.md`
2. `docs/hermes-coding/playbooks/project-map.md`
3. `docs/engineering/code-and-git-standards.md`

### 如果任务是“验证是否完成”
先读：
1. `docs/hermes-coding/playbooks/project-map.md`
2. `docs/hermes-coding/playbooks/source-of-truth.md`

---

## 一句话使用方式

每次执行任务前，Hermes 应默认这样做：

1. 先读 `source-of-truth.md`
2. 再读 `project-map.md`
3. 根据任务类型走本文件对应分支
4. 最后再决定具体读取哪些代码文件和执行哪些修改
