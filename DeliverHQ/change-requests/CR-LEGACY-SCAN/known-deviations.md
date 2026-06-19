# 已知偏差清单: usegeo

> 逆向扫描中被人工判定为 **bug / 技术债** 的行为（is_real_requirement=false）。
> 这些**不应固化为需求**，而应作为改造 CR 的候选输入。

| ID | 模块 | 当前行为 | 判定 | 备注 |
|---|---|---|---|---|
| RC-004 | functions | {{AI 待填：从代码推断该模块的行为}} | bug/债 | functions.php 只是 WordPress 主题模块加载入口，属于实现组织方式，不应固化为产品需求；真正需要确认的业务能力已拆到 post-types、assets、forms、admin 等候选中。 |
| RC-006 | index | {{AI 待填：从代码推断该模块的行为}} | bug/债 | index.php 只是 WordPress 主题兜底模板，提示应使用 front-page.php 和 single.php；不承载独立产品需求，不应固化为需求。 |

**生成时间**: 2026-06-19 22:19:39