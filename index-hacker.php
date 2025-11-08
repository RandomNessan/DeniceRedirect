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
  <title>线路检测 · Hacker Theme</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <style>
    /* 字体：黑客风等宽 + 科幻衬底 */
    @import url('https://fonts.googleapis.com/css2?family=Share+Tech+Mono&family=JetBrains+Mono:wght@400;600&display=swap');

    :root{
      --bg:#050a06;             /* 近黑绿底 */
      --bg-2:#080f0b;           /* 叠加暗底 */
      --grid:rgba(0,255,130,.08);
      --txt:#d9ffe6;            /* 发光浅绿字 */
      --sub:#7affb7;            /* 次要文字 */
      --accent:#00ff84;         /* 终端荧光绿 */
      --accent-2:#2be38c;       /* 次绿色 */
      --warn:#ffd95e;           /* 中延迟 */
      --bad:#ff5e5e;            /* 高延迟 */
      --fail:#ff6b9a;           /* 失败/离线 */
      --chip:#0b140f;           /* 芯片板色块 */
      --stroke:rgba(0,255,130,.22);
      --scanline:rgba(0,0,0,.12);
      --shadow: 0 8px 24px rgba(0,0,0,.6);
    }

    *{box-sizing:border-box}
    html,body{height:100%}
    body{
      margin:0;
      color:var(--txt);
      background:
        radial-gradient(1200px 800px at 70% -10%, rgba(0,255,132,.06), transparent 60%),
        radial-gradient(900px 600px at -10% 90%, rgba(0,255,132,.05), transparent 60%),
        linear-gradient(160deg, var(--bg), var(--bg-2));
      font-family:'JetBrains Mono','Share Tech Mono',ui-monospace,Consolas,Menlo,monospace;
      letter-spacing:.15px;
      overflow:hidden; /* 让扫描线和雨滴铺满不出现滚动条 */
      display:flex; align-items:center; justify-content:center;
      padding:22px;
    }

    /* 绿色网格（黑客界面常见） */
    .grid{
      position:fixed; inset:0; pointer-events:none; opacity:.35;
      background:
        linear-gradient(transparent 95%, var(--grid) 100%) 0 0/100% 28px,
        linear-gradient(90deg, transparent 95%, var(--grid) 100%) 0 0/28px 100%;
      mask-image: radial-gradient(1000px 600px at 60% 40%, #000 60%, transparent 100%);
    }

    /* 扫描线（轻） */
    .scan{
      position:fixed; inset:0; pointer-events:none; mix-blend-mode:overlay;
      background: repeating-linear-gradient(180deg, transparent 0 2px, var(--scanline) 3px 3px);
      opacity:.35;
      animation: scanMove 8s linear infinite;
    }
    @keyframes scanMove{
      0%{ transform: translateY(-2%) }
      100%{ transform: translateY(2%) }
    }

    /* 轻量“矩阵雨”（不影响性能） */
    .rain{
      position:fixed; inset:0; pointer-events:none; overflow:hidden; opacity:.25;
    }
    .drop{
      position:absolute;
      width:1px; height:90px;
      background: linear-gradient(to bottom, rgba(0,255,132,0) 0%, rgba(0,255,132,.65) 70%, rgba(0,255,132,0) 100%);
      filter: blur(.2px);
      animation: fall var(--t) linear infinite;
      left: var(--x);
      top: -100px;
    }
    @keyframes fall{
      100%{ transform: translateY(120vh) }
    }

    .wrap{width:min(820px, 100%); position:relative; z-index:2;}

    header{text-align:center;margin-bottom:16px}
    .title{
      font-family:'Share Tech Mono', monospace;
      font-weight:600;
      font-size: clamp(20px, 3.2vw, 30px);
      color:var(--accent);
      text-shadow: 0 0 10px rgba(0,255,132,.55), 0 0 22px rgba(0,255,132,.35);
      letter-spacing:.12em;
    }
    .subtitle{
      margin-top:6px; font-size:13px; color:var(--sub);
      opacity:.9;
    }

    .refresh-wrap{ text-align:center; margin: 18px 0 22px; }
    .refresh-btn{
      border:1px solid var(--stroke); cursor:pointer;
      padding:11px 20px; border-radius:8px;
      background: linear-gradient(180deg, rgba(0,255,132,.12), rgba(0,0,0,.0));
      color:var(--accent);
      font-weight:600; letter-spacing:.3px;
      text-shadow:0 0 10px rgba(0,255,132,.6);
      box-shadow: 0 0 0 2px rgba(0,255,132,.08) inset, var(--shadow);
      transition: transform .15s ease, box-shadow .15s ease, filter .15s ease;
    }
    .refresh-btn:hover{ transform: translateY(-1px); filter: saturate(1.08) }
    .refresh-btn:active{ transform: translateY(0) scale(.98) }

    .card{
      position:relative;
      padding:16px;
      background: rgba(0,0,0,.25);
      border:1px solid var(--stroke);
      border-radius:12px;
      box-shadow: var(--shadow);
      backdrop-filter: blur(6px);
      overflow:hidden;
    }
    .card::before{
      content:">_ terminal/trace online";
      position:absolute; top:10px; left:12px;
      color:rgba(0,255,132,.6); font-size:12px;
      letter-spacing:.1em;
      text-shadow: 0 0 8px rgba(0,255,132,.4);
      pointer-events:none;
    }

    .rows{display:flex; flex-direction:column; gap:10px; margin-top:18px}

    .row{
      display:grid;
      grid-template-columns: 1fr auto auto;
      gap:10px; align-items:center;
      padding:12px;
      background: linear-gradient(180deg, rgba(0,255,132,.06), rgba(0,0,0,.0));
      border:1px solid rgba(0,255,132,.18);
      border-radius:10px;
      transition: transform .25s ease, border-color .25s ease, box-shadow .25s ease;
    }
    .row:hover{
      transform: translateY(-2px);
      border-color: rgba(0,255,132,.35);
      box-shadow: 0 8px 18px rgba(0,255,132,.12), 0 0 0 1px rgba(0,255,132,.10) inset;
    }

    /* URL 输入框：终端样式 */
    .url{
      width:100%;
      border:1px solid rgba(0,255,132,.22); outline:none;
      background: linear-gradient(180deg, rgba(0,0,0,.45), rgba(0,0,0,.3));
      color:var(--txt);
      padding:12px 14px;
      border-radius:8px;
      font-size:14px;
      text-align:center;
      font-weight:600;
      font-family:'JetBrains Mono','Share Tech Mono',ui-monospace,Consolas,Menlo,monospace;
      letter-spacing:.25px;
      text-shadow: 0 0 6px rgba(0,255,132,.45);
      caret-color: var(--accent);
      position:relative;
      box-shadow: 0 0 0 2px rgba(0,255,132,.06) inset;
    }
    .url::selection{ background: rgba(0,255,132,.28) }
    .url:focus{ box-shadow: 0 0 0 2px rgba(0,255,132,.16) inset }

    /* ping 显示为“芯片灯” */
    .ping{
      min-width:110px; text-align:center;
      font-weight:700; font-variant-numeric: tabular-nums;
      padding:10px 12px; border-radius:8px;
      background: var(--chip);
      border:1px solid rgba(0,255,132,.22);
      text-shadow: 0 0 8px rgba(255,255,255,.2), 0 0 16px rgba(0,255,132,.25);
      box-shadow: 0 0 0 2px rgba(0,255,132,.06) inset;
    }
    .ping.ok{  color:var(--accent) }
    .ping.mid{ color:var(--warn) }
    .ping.bad{ color:var(--bad)  }
    .ping.fail{color:var(--fail) }

    /* 访问按钮：终端绿块 */
    .btn{
      border:1px solid rgba(0,255,132,.25); cursor:pointer;
      padding:10px 14px; border-radius:8px;
      background: linear-gradient(180deg, rgba(0,255,132,.22), rgba(0,0,0,.0));
      color:#00ff84;
      font-weight:800; letter-spacing:.2px;
      text-transform: uppercase;
      text-shadow: 0 0 10px rgba(0,255,132,.65);
      box-shadow: 0 10px 22px rgba(0,255,132,.15), 0 0 0 2px rgba(0,255,132,.08) inset;
      transition: transform .15s ease, filter .15s ease;
    }
    .btn:hover{ transform: translateY(-1px); filter: saturate(1.1) }
    .btn:active{ transform: translateY(0) scale(.98) }

    .legend{
      display:flex; gap:12px; flex-wrap:wrap;
      margin-top:16px; font-size:12px; color:var(--sub);
      justify-content:center; opacity:.9;
    }
    .dot{width:10px;height:10px;border-radius:2px;display:inline-block;margin-right:6px; box-shadow:0 0 8px rgba(0,255,132,.55)}
    .d-ok{background:var(--accent)} .d-mid{background:var(--warn)} .d-bad{background:var(--bad)} .d-fail{background:var(--fail)}

    /* 顶部状态栏（终端感） */
    .statusbar{
      display:flex; gap:10px; flex-wrap:wrap;
      justify-content:center;
      font-size:12px; color:var(--sub); opacity:.95;
    }
    .badge{
      padding:6px 10px; border-radius:6px;
      background:rgba(0,255,132,.08);
      border:1px solid rgba(0,255,132,.22);
      box-shadow: 0 0 0 2px rgba(0,255,132,.06) inset;
    }

    @media(max-width:560px){
      .row{ grid-template-columns: 1fr 1fr; }
      .ping{ order:3; grid-column:1 / -1; }
    }
  </style>

  <script>
    // 轻量 ping：维持主逻辑，只调样式 class
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

    // 生成少量“矩阵雨”滴条（性能友好）
    function spawnRain(){
      const rain = document.querySelector('.rain');
      if(!rain) return;
      const drops = 28;  // 数量适中
      for(let i=0;i<drops;i++){
        const d = document.createElement('div');
        d.className = 'drop';
        d.style.setProperty('--x', Math.random()*100 + 'vw');
        d.style.setProperty('--t', (4 + Math.random()*6) + 's');
        d.style.opacity = (0.35 + Math.random()*0.4).toFixed(2);
        rain.appendChild(d);
      }
    }
    document.addEventListener('DOMContentLoaded', spawnRain);
  </script>
</head>
<body>
  <!-- 背景层 -->
  <div class="grid"></div>
  <div class="scan"></div>
  <div class="rain"></div>

  <div class="wrap">
    <header>
      <div class="title">[ LATENCY CHECKER · 线路检测系统 ]</div>
      <div class="subtitle">trace: latency · status · route</div>
      <div class="statusbar" aria-hidden="true" style="margin-top:10px;">
        <span class="badge">session: active</span>
        <span class="badge">privilege: user</span>
      </div>
    </header>

    <div class="refresh-wrap">
      <button class="refresh-btn" onclick="refreshPage()">▶ 刷新可用线路</button>
    </div>

    <section class="card" role="region" aria-label="线路检测结果">
      <div class="rows">
        <?php foreach ($urls as $index => $url): ?>
          <div class="row">
            <input type="text" class="url" value="<?= htmlspecialchars($url['url'], ENT_QUOTES) ?>" readonly>
            <span class="ping" id="ping<?= $index ?>">测试中...</span>
            <button class="btn" onclick="window.open('<?= htmlspecialchars($url['url'], ENT_QUOTES) ?>', '_blank')">访问</button>
          </div>
          <script>pingURL("<?= htmlspecialchars($url['url'], ENT_QUOTES) ?>", "ping<?= $index ?>");</script>
        <?php endforeach; ?>
      </div>

      <div class="legend">
        <span><i class="dot d-ok"></i>&lt; 500ms</span>
        <span><i class="dot d-mid"></i>500–999ms</span>
        <span><i class="dot d-bad"></i>&ge; 1000ms</span>
        <span><i class="dot d-fail"></i>无法连接</span>
      </div>
    </section>
  </div>
</body>
</html>
