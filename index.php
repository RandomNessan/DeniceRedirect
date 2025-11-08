<?php
require_once './config/db.php';

// 随机获取两个 URL（保持逻辑不变）
$stmt = $pdo->query("SELECT url FROM urls ORDER BY RAND() LIMIT 2");
$urls = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="zh">
<head>
  <meta charset="UTF-8" />
  <title>线路检测 · Latency Check</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <style>
    /* 商业风：清爽稳重的字体组合（中英混排友好） */
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Noto+Sans+SC:wght@400;600;700&family=JetBrains+Mono:wght@600&display=swap');

    :root{
      /* 品牌与语义色 */
      --brand:#2563eb;        /* 品牌蓝（按钮/强调） */
      --brand-2:#0ea5e9;      /* 辅助青（细节） */
      --text:#0f172a;         /* 标题/主文本（近黑）*/
      --muted:#475569;        /* 次文本（深灰蓝）*/
      --border:#e2e8f0;       /* 边框 */
      --fill:#ffffff;         /* 组件底色 */
      --page:#f7f8fb;         /* 页面背景 */
      --ok:#16a34a;           /* 低延迟 */
      --mid:#ca8a04;          /* 中延迟 */
      --bad:#dc2626;          /* 高延迟 */
      --fail:#a21caf;         /* 失败 */
      --focus:#38bdf8;        /* 可视焦点 */

      --radius:14px;
      --shadow-sm: 0 1px 2px rgba(16,24,40,.04), 0 1px 1px rgba(16,24,40,.02);
      --shadow-md: 0 8px 24px rgba(15,23,42,.08);
    }

    *{box-sizing:border-box}
    html,body{height:100%}
    body{
      margin:0;
      font-family:'Inter','Noto Sans SC',system-ui,-apple-system,"Segoe UI",Roboto,"Helvetica Neue",Arial;
      color:var(--text);
      background:var(--page);
      display:flex; align-items:center; justify-content:center;
      padding:24px;
    }

    /* 顶部细线品牌动画（不干扰内容，可形成“进度/活跃感”） */
    .top-accent{
      position:fixed; top:0; left:0; right:0; height:3px; overflow:hidden; z-index:999;
      background: linear-gradient(90deg, var(--brand), var(--brand-2));
      mask: linear-gradient(90deg, transparent, #000 20%, #000 80%, transparent);
    }
    .top-accent::after{
      content:""; position:absolute; inset:0;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,.6), transparent);
      transform: translateX(-100%);
      animation: bar 3.6s ease-in-out infinite;
    }
    @keyframes bar{
      0%{ transform: translateX(-100%); }
      50%{ transform: translateX(0%); }
      100%{ transform: translateX(100%); }
    }

    .wrap{ width:min(860px, 100%); }

    header{ text-align:center; margin-bottom:18px; animation: fadeIn .5s ease both; }
    .title{
      font-weight:700; letter-spacing:.2px;
      font-size: clamp(22px, 3.2vw, 32px);
    }
    .subtitle{
      margin-top:6px; color:var(--muted); font-size:14px;
    }
    @keyframes fadeIn{
      from{ opacity:0; transform: translateY(6px) }
      to{ opacity:1; transform: translateY(0) }
    }

    .toolbar{
      display:flex; align-items:center; justify-content:center; gap:12px;
      margin: 12px 0 18px;
    }
    .btn-refresh{
      border:1px solid var(--border); background:var(--fill); color:var(--text);
      padding:10px 16px; border-radius:10px; font-weight:600;
      transition: transform .12s ease, box-shadow .12s ease, border-color .12s ease;
      box-shadow: var(--shadow-sm);
    }
    .btn-refresh:hover{ transform: translateY(-1px); border-color:#cbd5e1; box-shadow: var(--shadow-md); }
    .btn-refresh:active{ transform: translateY(0); }
    .btn-refresh:focus-visible{ outline:3px solid var(--focus); outline-offset:2px; }

    .card{
      background:var(--fill);
      border:1px solid var(--border);
      border-radius: var(--radius);
      box-shadow: var(--shadow-md);
      padding: 18px 16px;
      animation: rise .4s ease both;
    }
    @keyframes rise{
      from{ opacity:0; transform: translateY(8px) }
      to{   opacity:1; transform: translateY(0) }
    }

    .rows{ display:flex; flex-direction:column; gap:10px; }

    .row{
      display:grid; grid-template-columns: 1fr auto auto; gap:10px; align-items:center;
      padding:12px;
      background:#fff;
      border:1px solid var(--border);
      border-radius:12px;
      transition: transform .15s ease, box-shadow .15s ease, border-color .15s ease;
    }
    .row:hover{
      transform: translateY(-1px);
      border-color:#cbd5e1;
      box-shadow: var(--shadow-sm);
    }

    .url{
      width:100%;
      border:1px solid #dbe2ea; outline:none;
      background:#fff; color:var(--text);
      padding:12px 14px; border-radius:10px;
      font-size:15px; text-align:center; font-weight:700;
      font-family:'JetBrains Mono', ui-monospace, SFMono-Regular, Menlo, Consolas, "Liberation Mono", monospace;
    }
    .url::selection{ background:#e2f2ff }
    .url:focus{ outline:3px solid var(--focus); outline-offset:2px; }

    .ping{
      min-width:110px; text-align:center;
      font-weight:800; font-variant-numeric: tabular-nums;
      padding:10px 12px; border-radius:10px;
      background:#fff; border:1px solid #dbe2ea; color:var(--text);
    }
    .ping.ok{  color:var(--ok) }
    .ping.mid{ color:var(--mid) }
    .ping.bad{ color:var(--bad) }
    .ping.fail{ color:var(--fail) }

    .btn-visit{
      border:1px solid #1d4ed8; background: linear-gradient(180deg, var(--brand), #1d4ed8);
      color:#fff; font-weight:700; padding:10px 14px; border-radius:10px; cursor:pointer;
      transition: transform .12s ease, filter .12s ease, box-shadow .12s ease;
      box-shadow: 0 6px 16px rgba(37,99,235,.22);
    }
    .btn-visit:hover{ transform: translateY(-1px); filter: brightness(1.02); }
    .btn-visit:active{ transform: translateY(0) scale(.99); }
    .btn-visit:focus-visible{ outline:3px solid var(--focus); outline-offset:2px; }

    .legend{
      display:flex; gap:14px; flex-wrap:wrap; justify-content:center;
      margin-top:14px; color:var(--muted); font-size:12px;
    }
    .dot{ width:10px; height:10px; border-radius:50%; display:inline-block; margin-right:6px; }
    .d-ok{  background: var(--ok) }
    .d-mid{ background: var(--mid) }
    .d-bad{ background: var(--bad) }
    .d-fail{ background: var(--fail) }

    /* 减少动画偏好 */
    @media (prefers-reduced-motion: reduce){
      .top-accent::after, .row, .card, .btn-refresh, .btn-visit{ animation:none; transition:none }
    }

    @media(max-width:560px){
      .row{ grid-template-columns: 1fr 1fr; }
      .ping{ order:3; grid-column:1 / -1; }
    }
  </style>

  <script>
    // ping 逻辑保持不变
    async function pingURL(url, elementId) {
      const start = performance.now();
      const $el = document.getElementById(elementId);
      try {
        await fetch(url, { mode: 'no-cors' });
        const latency = Math.round(performance.now() - start);
        $el.textContent = latency + " ms";
        $el.classList.remove('ok','mid','bad','fail');
        if (latency < 500) $el.classList.add('ok');
        else if (latency < 1000) $el.classList.add('mid');
        else $el.classList.add('bad');
      } catch {
        $el.textContent = "无法连接";
        $el.classList.remove('ok','mid','bad');
        $el.classList.add('fail');
      }
    }
    function refreshPage(){ location.reload(); }
  </script>
</head>
<body>
  <div class="top-accent"></div>

  <div class="wrap">
    <header>
      <div class="title">线路检测 · Latency Check</div>
      <div class="subtitle">选择延迟更低的地址以获得更稳定的访问体验</div>
    </header>

    <div class="toolbar">
      <button class="btn-refresh" onclick="refreshPage()">刷新可用线路</button>
    </div>

    <section class="card" role="region" aria-label="线路检测结果">
      <div class="rows">
        <?php foreach ($urls as $index => $url): ?>
          <div class="row">
            <input type="text" class="url" value="<?= htmlspecialchars($url['url'], ENT_QUOTES) ?>" readonly>
            <span class="ping" id="ping<?= $index ?>">测试中...</span>
            <button class="btn-visit" onclick="window.open('<?= htmlspecialchars($url['url'], ENT_QUOTES) ?>', '_blank')">访问</button>
          </div>
          <script>pingURL("<?= htmlspecialchars($url['url'], ENT_QUOTES) ?>", "ping<?= $index ?>");</script>
        <?php endforeach; ?>
      </div>

      <div class="legend" aria-hidden="true">
        <span><i class="dot d-ok"></i>&lt; 500ms</span>
        <span><i class="dot d-mid"></i>500–999ms</span>
        <span><i class="dot d-bad"></i>&ge; 1000ms</span>
        <span><i class="dot d-fail"></i>无法连接</span>
      </div>
    </section>
  </div>
</body>
</html>
