# Hermes 全局 DeliverHQ skill 重装（npm/GitHub 源）

适用场景：用户要求用 `npm install -g github:kevinwanghd/deliverhq` 更新 DeliverHQ，并且当前 Hermes 使用 `C:/Users/kevin/AppData/Local/hermes` 作为 `HERMES_HOME`。

## 关键坑

- `deliverhq init --target hermes --global`/CLI help 可能仍指向 `~/.hermes/skills/deliverhq`。
- Kevin 的当前 Hermes 不应使用 `C:/Users/kevin/.hermes`；全局 skill 应落在 `C:/Users/kevin/AppData/Local/hermes/skills/deliverhq`。
- 旧 skill 备份不能放在 `skills/` 下，否则 Hermes 扫描到备份里的 `SKILL.md` 会导致 `skill_view("deliverhq")` 同名冲突；备份放到 `C:/Users/kevin/AppData/Local/hermes/backups/skills/`。
- Windows bash / MSYS 中，脚本内部使用 Windows-native path（`C:/...`），不要用 `/c/...`。

## 建议流程

```bash
# 1. 更新全局 npm 包
npm install -g github:kevinwanghd/deliverhq

# 2. 验证 npm 包来源和版本
npm list -g --depth=0 | grep -i deliverhq
deliverhq --help
node -e "const p=require('C:/Users/kevin/AppData/Roaming/npm/node_modules/deliverhq/package.json'); console.log(p.version, p.repository)"
```

## 同步包内 skill 到当前 Hermes home

源：`C:/Users/kevin/AppData/Roaming/npm/node_modules/deliverhq/skill`
目标：`C:/Users/kevin/AppData/Local/hermes/skills/deliverhq`
旧版备份：`C:/Users/kevin/AppData/Local/hermes/backups/skills/deliverhq-<timestamp>`

已验证的 Node.js 同步脚本：

```bash
node <<'NODE'
const fs = require('fs');
const path = require('path');
const src = 'C:/Users/kevin/AppData/Roaming/npm/node_modules/deliverhq/skill';
const dst = 'C:/Users/kevin/AppData/Local/hermes/skills/deliverhq';
const backupRoot = 'C:/Users/kevin/AppData/Local/hermes/backups/skills';
const stamp = new Date().toISOString().replace(/[-:T.Z]/g,'').slice(0,14);
const backup = path.join(backupRoot, `deliverhq-${stamp}`);
if (!fs.existsSync(src)) throw new Error(`source skill not found: ${src}`);
fs.mkdirSync(backupRoot, {recursive: true});
if (fs.existsSync(dst)) fs.renameSync(dst, backup);
fs.cpSync(src, dst, {recursive: true});
console.log(`copied=${src} -> ${dst}`);
console.log(`backup=${fs.existsSync(backup) ? backup : 'none'}`);
NODE
```

## 验证

```bash
cd C:/Users/kevin/AppData/Local/hermes/skills/deliverhq
python scripts/selftest.py
node -e "const p=require('C:/Users/kevin/AppData/Roaming/npm/node_modules/deliverhq/package.json'); console.log(p.version)"
grep -n 'version:' VERSION.yml | head
find C:/Users/kevin/AppData/Local/hermes/skills -path '*deliverhq*/SKILL.md' -print
```

验收标准：

- `npm list -g --depth=0` 显示新版 `deliverhq`。
- `deliverhq --help` 正常输出。
- Hermes skill 路径是 `C:/Users/kevin/AppData/Local/hermes/skills/deliverhq/SKILL.md`。
- `find ... -path '*deliverhq*/SKILL.md'` 在 `skills/` 下只出现一个当前 skill，不包含备份。
- `python scripts/selftest.py` 全部 PASS。
- 当前会话若未感知新版 skill，提示用户开新会话或执行 `/reload-skills`。
