# Execution Policy

## 目的
约束 Hermes 在 UseGEO 项目中的默认执行方式，减少误判、误改和范围失控。

---

## 总原则

1. **先理解任务，再执行**
2. **先看本地代码和文档，再给建议**
3. **先确认 source of truth，再修改**
4. **默认最小改动范围**
5. **先定位根因，再修 Bug**
6. **未验证前，不宣称完成**
7. **第一次失败后先分析，不连续盲试**

---

## UseGEO 项目级核心约束

### 1. WordPress-first
- 运行时代码优先放在 `wordpress/theme/usegeo/`
- 页面结构优先使用 WordPress 模板
- 复用模块优先放在 `template-parts/`
- 共享资源优先走 WordPress enqueue

### 2. MVP 优先级高于过度工程化
当前阶段优先打通：首页 Hero、GEO Scanner、Lead Capture、文章 CTA、ROI 工具。

默认不要：
- 过早插件化
- 过早引入复杂抽象
- 为未来假设设计过多层

### 3. 主转化路径优先级最高
以下内容属于高优先级关键路径：
- 首页 Hero
- Scanner 模块
- 留资表单
- `usegeo_handle_lead_capture`
- Success / invalid / failed 状态回流
- 文章页 CTA

---

## 执行前必须做的事
1. 判断任务类型
2. 读取 `source-of-truth.md`、`project-map.md`、`ROUTING.md`
3. 确认修改目标目录是否在 `wordpress/theme/usegeo/`
4. 明确允许修改哪些文件、哪些不该碰

---

## 默认禁止事项
- 不看代码就提重构建议
- 不确认入口就修改文件
- 默认改 `src/` 解决运行时问题
- 默认插件化
- 默认框架化
- 顺手修一堆无关问题
- 未验证就说完成
- 第一次失败后连续盲试

---

## 一句话工作方式
**先看 `source-of-truth.md` 和 `project-map.md`，确认任务真正落点在 `wordpress/theme/usegeo/`，按最小范围修改，改完验证主路径，失败先分析，不盲试。**
