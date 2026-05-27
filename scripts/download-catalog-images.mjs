/**
 * Extrai URLs http(s) de arquivos PHP do seed, faz download único por URL e
 * reescreve os arquivos com caminhos locais `/images/catalog/...`.
 *
 * Uso (na raiz do projeto): node scripts/download-catalog-images.mjs
 */
import crypto from "node:crypto";
import fs from "node:fs/promises";
import path from "node:path";
import { fileURLToPath } from "node:url";

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const root = path.resolve(__dirname, "..");
const OUT_DIR = path.join(root, "public", "images", "catalog");

const FILES_TO_REWRITE = [
  path.join(root, "database", "seeders", "data", "devfood-catalog.php"),
  path.join(root, "database", "seeders", "DevFoodSeeder.php"),
];

async function fetchBuffer(url, retries = 3) {
  for (let i = 0; i < retries; i++) {
    const res = await fetch(url, {
      headers: { "User-Agent": "DevFood-asset-fetch/1" },
      redirect: "follow",
    });
    if (res.ok) return Buffer.from(await res.arrayBuffer());
    if (i === retries - 1)
      throw new Error(`${res.status} ${res.statusText} para ${url}`);
    await new Promise((r) => setTimeout(r, 800 * (i + 1)));
  }
  throw new Error("unreachable");
}

function suggestedFilename(urlStr) {
  let u;
  try {
    u = new URL(urlStr);
  } catch {
    return `file-${crypto.createHash("md5").update(urlStr).digest("hex").slice(0, 16)}`;
  }

  let base = path.basename(decodeURIComponent(u.pathname));
  if (!base.includes(".") || base.length < 3) {
    const ext =
      urlStr.includes("images.unsplash.com") ||
      urlStr.includes("photo-") ||
      /\.jpg\b/i.test(urlStr)
        ? "jpg"
        : "png";
    base = `${crypto.createHash("md5").update(urlStr).digest("hex").slice(0, 16)}.${ext}`;
  }

  const safe = base.replace(/[^\w.-]/g, "_");
  const seen = /** @type {Map<string, number>} */ (suggestedFilename._seen ||= new Map());
  const idx = seen.get(safe) ?? 0;
  seen.set(safe, idx + 1);
  if (idx === 0) return safe;

  const dot = safe.lastIndexOf(".");
  const stem = dot > 0 ? safe.slice(0, dot) : safe;
  const ext = dot > 0 ? safe.slice(dot) : "";
  return `${stem}-${idx}${ext}`;
}
suggestedFilename._seen = new Map();

async function main() {
  suggestedFilename._seen = new Map();

  const urlSet = new Set();
  for (const f of FILES_TO_REWRITE) {
    const txt = await fs.readFile(f, "utf8");
    const re = /https?:\/\/[^\s'",)<>]+/gi;
    let m;
    while ((m = re.exec(txt))) {
      let u = m[0].replace(/\)+$/, "").replace(/,+$/, "");
      urlSet.add(u);
    }
  }

  console.log(`${urlSet.size} URLs únicas.`);

  await fs.mkdir(OUT_DIR, { recursive: true });

  /** @type {Map<string, string>} */
  const urlToRelative = new Map();

  let n = 0;
  const sorted = [...urlSet].sort();
  for (const url of sorted) {
    const fname = suggestedFilename(url);
    const disk = path.join(OUT_DIR, fname);
    if (!(await fs.stat(disk).catch(() => null))) {
      const buf = await fetchBuffer(url);
      await fs.writeFile(disk, buf);
      n++;
      console.log("+", fname);
    }
    urlToRelative.set(url, `/images/catalog/${fname}`);
  }

  for (const f of FILES_TO_REWRITE) {
    let txt = await fs.readFile(f, "utf8");
    for (const [remote, relative] of urlToRelative) {
      txt = txt.split(remote).join(relative);
    }
    await fs.writeFile(f, txt, "utf8");
    console.log("Rewrite:", path.relative(root, f));
  }

  console.log(`Downloads novos: ${n}. Pasta: ${path.relative(root, OUT_DIR)}`);
}

main().catch((e) => {
  console.error(e);
  process.exit(1);
});
