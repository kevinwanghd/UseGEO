# UseGEO 项目文档使用说明

> **核心理念**：文档就是你跟 AI 沟通的主要界面。改文档，AI 就跟着变。
> v3.0 升级：文档不再只是说明，而是可以驱动测试系统执行——它是开发上下文、测试输入、质量规则、验收标准、回归资产、组织记忆。

---

## 文档全景

```
docs/
├── CONTEXT.md                 ← 🟢 AI 上下文总入口 + 三级质检漏斗
├── architecture.md            ← 🔵 三张蓝图① 系统分层 + 高风险模块 + 依赖
├── interfaces.md              ← 🔵 三张蓝图② 接口契约 + 安全约束 + 钩子
├── data-model.md              ← 🔵 三张蓝图③ 数据模型 + 完整性约束 + ER
├── decisions.md               ← 🟡 设计决策 WHY + 隐性知识 + 需求变更日志 + 错题本
├── rules.md                   ← 🔴 可执行约束 + 门禁等级 + 成熟度 + 检测方式
├── verification.md            ← ⚪ 验证 + 质检 + SpecGate 门禁
├── templates/
│   ├── meeting-notes.md       ← 🟣 会议纪要模板（正式需求评审）
│   ├── requirement-change.md  ← 🟠 需求变更模板（口头/聊天小需求）
│   ├── acceptance-spec.md     ← 📋 验收规格模板（v3.0 — Spec Agent 产出）
│   ├── quality-report.md      ← 📊 质检报告模板（v3.0 — Quality Agent 产出）
│   └── mistake-entry.md       ← 📝 错题条目模板（v3.0 — Memory Agent 产出）
├── product/                   ← 产品文档（PRD、设计指南）
├── engineering/               ← 工程文档（开发计划、部署手册）
└── reports/                   ← 阶段报告（日期命名，一次性文档）
```

**颜色含义**：
- 🟢 必读入口 | 🔵 三张蓝图（改对应代码前必读） | 🟡 理解"为什么" | 🔴 写代码前必读 + SpecGate 检查
- ⚪ 验证+质检门禁 | 🟣 正式需求模板 | 🟠 轻量需求模板
- 📋 验收规格 | 📊 质检报告 | 📝 错题沉淀

---

## 怎么读：不同角色的读取路径

### 👨‍💻 你（人）

| 你要做的事 | 读什么 |
|-----------|--------|
| 了解项目全貌 | `CONTEXT.md`（5 分钟看完） |
| 理解某个设计为什么这样做 | `decisions.md` 找对应条目 |
| 回忆有哪些约束 | `rules.md` 扫一遍（注意 Gate 列） |
| 确认接口参数 | `interfaces.md` |
| 写需求评审纪要 | `templates/meeting-notes.md` 照着填 |
| 记录口头/聊天的小需求变更 | `templates/requirement-change.md` 照着填 |
| 回忆某个需求为什么改 | `decisions.md` 需求变更日志 |
| 查看某个需求的验收标准 | 对应的 `acceptance-spec` |
| 查看质检结果 | 对应的 `quality-report` |
| 回忆 AI 犯过什么错 | `decisions.md` 错题本 |

### 🤖 AI — 六阶段角色切换

AI 在不同阶段扮演不同角色，遵循不同的文档约束：

| 阶段 | 角色 | 读取 | 产出 |
|------|------|------|------|
| 需求 | Spec Agent | meeting-notes / requirement-change | acceptance-spec |
| 装载 | Context Agent | CONTEXT.md + 三级质检漏斗 | context-pack（内存态） |
| 开发 | Dev Agent | rules.md + architecture.md | 代码变更 |
| 测试 | Test Agent | acceptance-spec + rules.md | 测试计划 + 测试代码 |
| 验收 | Quality Agent | acceptance-spec + rules.md + verification.md | quality-report |
| 归档 | Memory Agent | quality-report + 纠错记录 | rules 追加 + 错题追加 + 成熟度更新 |

---

## 怎么跑：六步闭环 v3.0

```
1. 装载（Context Agent）    → 按三级质检漏斗精准取出该用的几条 + 质量约束
2. 验收规格（Spec Agent）   → 从会议纪要/需求变更生成 acceptance-spec
3. 开发（Dev Agent）        → 按 rules.md 约束实现代码
4. 测试（Test Agent）       → 生成测试计划 + 测试代码
5. 质检（Quality Agent）    → SpecGate 门禁 + 输出 quality-report
6. 归档（Memory Agent）     → 沉淀错题 + 更新成熟度 + 检查蓝图同步
```

**v2.x → v3.0 核心变化**：
- 新增步骤 2：每个需求（无论大小）都必须有验收规格
- 新增步骤 5：SpecGate 是强制卡点，不通过不能交付
- 步骤 6 升级：纠错不只是追加规则，还要追加错题 + 检查是否需要回归测试

---

## 怎么改：日常维护指南

### 🔧 最常见的 8 种操作

#### 1. 想让 AI 知道新约束

编辑 `rules.md`，在对应严重性区域追加一行：

```markdown
| 23 | 新约束的一句话描述 | P1 | draft | static: 检测方式 | 来源 |
```

**重要**：只追加，不改已有规则。来源必须标注。Gate/Maturity/Detection 必须填写。

#### 2. 纠正了 AI 的代码错误

**不要只说"改一下"**。三步走：

1. 纠正代码
2. 在 `rules.md` 的"待追加规则"区域追加规则
3. 在 `decisions.md` 的对应错题本追加条目

这就是侠客汇的 **PRD 错题本** v3.0——每次纠正 AI，必须沉淀为一个规则、一个测试，或一个反向检索锚点。

#### 3. 想让 AI 理解某个设计为什么这样做

编辑 `decisions.md`，在"已确认的设计决策"表追加：

```markdown
| 决策描述 | WHY 原因 | 影响范围 | 确认人 |
```

#### 4. 架构/接口/数据模型变了

编辑对应蓝图文件 + **生成 acceptance-spec 验证变更** + **检查 rules.md 是否需要追加约束**。

#### 5. 纠正了 decisions.md 的异常模式

- **是有意设计** → 移到"已确认的设计决策"，记录 WHY
- **是临时方案** → 标记为 `[临时方案]`
- **是 bug/疏忽** → 标记为 `[待修复]`，加到 `rules.md` 待追加规则 + 对应错题本

#### 6. 口头/聊天来了个小需求

1. 在 `decisions.md` 的"需求变更日志"追加一行
2. 用 `templates/acceptance-spec.md` 生成简化验收规格（只需 Must + Must Not + Done Definition）
3. AI 实现后，检查蓝图是否需要同步
4. 跑一遍 SpecGate（简化流程）

#### 7. 查看质检结果 / SpecGate 未通过

1. 查看 `quality-report.md`（AI 自动生成）
2. 针对 Failed Constraints 修复
3. 针对 Missing Tests 补充测试
4. 针对 Human Decisions Required 做决策
5. 修复后重跑 SpecGate

#### 8. 发现质检漏检

**这是最需要重视的情况**——测试通过了但线上出问题。

1. 在 `decisions.md` 的**质量错题本**追加条目
2. **必须新增回归测试**（质量错题不新增测试 = 同样的问题会再次漏检）
3. 检查 acceptance-spec 模板是否需要补充检查项
4. 更新 CONTEXT.md 反向检索锚点

---

## 门禁等级速查

| 等级 | 违反时 | 成熟度 draft | 成熟度 verified | 成熟度 proven |
|------|--------|-------------|----------------|--------------|
| P0 Block | 阻断交付 | 提示 | 阻断，可 override | 强阻断 |
| P1 Must Fix | 默认阻断 | 提示 | 阻断，可 override | 强阻断 |
| P2 Warning | 提示 | 提示 | 提示 | 阻断，可 override |
| P3 Suggestion | 仅建议 | 仅建议 | 仅建议 | 提示 |

---

## 知识成熟度

| 成熟度 | 含义 | 升级门槛 | 门禁强度 |
|--------|------|---------|---------|
| `draft` | 初稿，未验证 | — | 只提示，不阻断 |
| `verified` | 被多个需求验证 | ≥3 个不同需求引用 | 默认阻断，可 override |
| `proven` | 足够可靠 | ≥5 个不同需求引用 | 强阻断，必须修复或负责人批准 |

**当前所有文件都是 `draft`**。每次跑完需求后，更新被验证的知识条目的成熟度。

---

## 冲突处理优先级

不同来源冲突时：

1. 当前运行时代码（`wordpress/theme/usegeo/`）
2. 组织记忆文件（`docs/*.md`）
3. 主产品文档（`docs/product/`）
4. 主工程文档（`docs/engineering/`）
5. reports 历史文档
6. 口头猜测或旧记忆

---

## 已验证的最佳实践

> 基于 UseGEO company_size 需求对照实验（文档约束 vs 裸跑）

### 核心洞察

**文档约束不是让 AI 变聪明，而是让 AI 变可预测。**

裸跑模式：AI 可能偶然做对也可能偶然做错——结果不可预测。
文档约束模式：AI 行为被限定在可预测范围内，唯一变数是"没有规则覆盖的边界情况"。

### 实验数据

| 维度 | 裸跑模式 | 文档约束模式 | 改善 |
|------|---------|------------|------|
| 避免错误数 | 0/6（预期全部发生） | 5/6 避免 | 83% |
| 唯一漏网错误 | — | Options 函数放错文件 | 无现成规则覆盖 |
| 纠正方式 | 事后人工发现 | SpecGate 逐条检查 | 从偶然到系统化 |
| 沉淀质量 | 无 | 规则 + Gate + detection + maturity | 从一句话到可执行约束 |

### 6 条实操要点

1. **规则覆盖是关键瓶颈**：83% 错误被已有规则避免，唯一漏网的是没有规则覆盖的职责归属问题。每次发现新错误，立即补充规则。

2. **小需求的 acceptance-spec 可以极简**：只填 Must + Must Not + Done Definition 三项就够，不需要填满所有 8 个字段。

3. **三文件同步是最常见的规则类型**：任何涉及"一个概念分布在多个文件"的场景，都应该建立同步规则。

4. **职责归属是最容易遗漏的规则**：冷启动时主动建立"哪个文件负责什么"的归属规则。

5. **v2.1 → v3.0 的差异不在"多做一步质检"**：v2.1 是离散的、依赖人触发的；v3.0 SpecGate 是连续的、系统化执行的。

6. **成熟度不是标签，是工程约束**：draft 只提示不阻断，verified 阻断可 override，proven 强阻断。成熟度升级应该自然发生，不要人工批量升级。

---

## 一句话规则

**组织记忆是活文档，不是报告。改文档，AI 就跟着变。追加不改，来源必标，人审再入。每次纠错必须沉淀——一个规则、一个测试，或一个反向检索锚点。**
