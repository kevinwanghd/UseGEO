# Source of Truth

## 目的
明确 UseGEO 项目里哪些文件、目录和文档才是默认应该相信的来源。

---

## 总原则

1. **运行时代码优先以 `wordpress/theme/usegeo/` 为准**
2. **产品需求优先以 `docs/product/` 下主文档为准**
3. **工程实施优先以 `docs/engineering/` 下主文档为准**
4. **阶段性结论属于参考信息，不自动覆盖主文档**
5. **`src/` 当前不是运行时 source of truth**
6. **不确定时，先查本文件，再查 `project-map.md`**

---

## 运行时代码的 Source of Truth

### 主运行时目录
- `wordpress/theme/usegeo/`

### 结论
以下内容默认以 `wordpress/theme/usegeo/` 为准：
- 页面模板
- 主题逻辑
- 共享样式
- 共享脚本
- Lead Capture 表单处理
- Scanner / ROI 运行时模块

---

## 产品需求的 Source of Truth

### 主文档
- `docs/product/usegeo-prd.md`
- `docs/product/usegeo-design-guidelines.md`

---

## 工程实施的 Source of Truth

### 主文档
- `docs/engineering/usegeo-development-plan.md`
- `docs/engineering/wordpress-integration.md`
- `docs/engineering/usegeo-mvp-task-breakdown.md`
- `docs/engineering/code-and-git-standards.md`

---

## `src/` 目录的定位

### 当前定位
- 前端源文件区
- 原型区
- 上游开发区
- 将来如需构建流程时的候选源目录

### 当前限制
- **不是运行时主入口**
- **不是页面最终渲染的默认来源**
- **不能默认覆盖 WordPress theme 中的真实实现**

---

## 冲突处理规则

如果不同来源冲突，默认按以下顺序判断：

1. **当前运行时代码**
2. **主产品文档**
3. **主工程文档**
4. **reports 历史文档**
5. **口头猜测或旧记忆**
