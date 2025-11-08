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
  <title>线路检测 · LATENCY CHECK (High Contrast)</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@500;700&family=Nunito:wght@400;700&family=Share+Tech+Mono&display=swap');

    :root{
      /* 主题色：梦境系，但提高可读性 */
      --pink:  #F6D4EF;
      --lilac: #C5B0FF;
      --mint:  #A8F0C3;
      --lemon: #FFF39C;
      --sky:   #D8F2FF;

      /* 提高对比的文字与描边 */
      --txt:   #221a39;            /* 深墨紫：主文字 */
      --sub:   #4a4562;            /* 次要文字 */
      --ink:   #1b1430;

      --bg-1:  #f9f7ff;
      --bg-2:  #ffffff;

      --glass: rgba(255,255,255,.90);  /* 更实的玻璃，降低透明 */
      --stroke: rgba(160,140,255,.50); /* 更深边框 */
      --chip:  #ffffff;                /* 控件底色更白 */

      --ok:  #1e9b6e;
      --mid: #a87912;
      --bad: #c53f4e;
      --fail:#a53e7e;

      --shadow-soft: 0 10px 28px rgba(120,100,200,.18);
      --inner: 0 0 0 1px rgba(255,255,255,.9) inset;

      --focus: #6c58ff; /* 可视焦点色 */
    }

    *{box-sizing:border-box}
    html,body{height:100%}
    body{
      margin:0;
      color:var(--txt);
      background:
        radial-gradient(1100px 700px at 75% -10%, rgba(197,176,255,.25), transparent 60%),
        radial-gradient(800px 520px at -20% 90%, rgba(168,240,195,.20), transparent 60%),
        linear-gradient(160deg, var(--bg-1), var(--bg-2));
      font-family:'Nunito', system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
      overflow:hidden;
      display:flex; align-items:center; justify-content:center;
      padding:22px;
    }

    /* 降低装饰层对比影响 */
    .grain{
      position:fixed; inset:0; pointer-events:none; opacity:.15; mix-blend-mode:multiply;
      background-image: radial-gradient(rgba(0,0,0,.06) 0.5px, transparent 0.5px);
      background-size: 3px 3px;
      animation: shimmer 18s ease-in-out infinite;
    }
    @keyframes shimmer{
      0%,100%{ filter: hue-rotate(0deg) blur(0px) }
      50%{ filter: hue-rotate(12deg) blur(.2px) }
    }

    .blobs{
      position:fixed; inset:-10% -10% -10% -10%; pointer-events:none;
      filter: blur(26px) saturate(1.05);
      opacity:.28; /* 显著降低透明度，避免压文字 */
    }
    .blob{ position:absolute; width:320px; height:320px; border-radius:50%;
      transform: translate(-50%,-50%);
      animation: floatxy var(--t,16s) ease-in-out infinite alternate;
    }
    .b1{ left:14%; top:18%; background:radial-gradient(circle at 30% 30%, var(--pink), transparent 60%); --t:17s }
    .b2{ left:78%; top:20%; background:radial-gradient(circle at 70% 40%, var(--mint), transparent 60%); --t:20s }
    .b3{ left:22%; top:80%; background:radial-gradient(circle at 50% 50%, var(--lilac), transparent 60%); --t:19s }
    .b4{ left:86%; top:70%; background:radial-gradient(circle at 40% 60%, var(--lemon), transparent 60%); --t:21s }
    @keyframes floatxy{
      0%   { transform: translate(-50%,-50%) rotate(0deg) scale(1) }
      100% { transform: translate(-46%,-54%) rotate(6deg) scale(1.04) }
    }

    .wrap{ width:min(880px, 100%); position:relative; z-index:2; }

    header{ text-align:center; margin-bottom:16px; }
    .title{
      font-family:'Poppins',sans-serif;
      font-weight:700;
      letter-spacing:.01em;
      font-size:clamp(22px,3.2vw,34px);
      color:var(--ink);                      /* 直接使用实色以保证对比 */
      text-shadow: 0 2px 0 rgba(255,255,255,.7);
    }
    .subtitle{
      margin-top:6px; font-size:14px; color:var(--sub);
    }

    .refresh-wrap{ text-align:center; margin: 18px 0 22px; }
    .refresh-btn{
      border:2px solid rgba(120,100,200,.55);
      cursor:pointer;
      padding:12px 22px; border-radius:14px;
      background:
        linear-gradient(90deg, #ffffff, #fffdf3 60%, #fff7fb);
      color:var(--txt);
      font-weight:800; letter-spacing:.3px;
      box-shadow: 0 6px 16px rgba(170,150,255,.20);
      transition: transform .15s ease, filter .15s ease, box-shadow .15s ease;
    }
    .refresh-btn:hover{ transform: translateY(-1px); filter: saturate(1.04) }
    .refresh-btn:active{ transform: translateY(0) scale(.985) }
    .refresh-btn:focus-visible{ outline:3px solid var(--focus); outline-offset:2px }

    .card{
      position:relative;
      padding:18px 16px 16px;
      background: var(--glass);              /* 更实、更白 */
      border:1px solid var(--stroke);
      border-radius:18px;
      backdrop-filter: blur(8px);
      box-shadow: var(--shadow-soft);
      overflow:hidden;
      animation: cardPop .45s ease both;
    }
    @keyframes cardPop{
      from{ transform: translateY(6px); opacity:.0 }
      to  { transform: translateY(0);   opacity:1 }
    }

    .ribbon{
      position:absolute; left:-10%; right:-10%; top:-26px; height:66px; pointer-events:none; opacity:.45;
      background:
        repeating-linear-gradient(90deg,
          rgba(246,212,239,.9) 0 40px,
          rgba(255,246,156,.9) 40px 80px,
          rgba(168,240,195,.9) 80px 120px,
          rgba(197,176,255,.9) 120px 160px);
      filter: blur(12px) saturate(1.05);
      transform: rotate(-2deg);
      animation: ribbonWave 10s ease-in-out infinite alternate;
    }
    @keyframes ribbonWave{
      0%{ transform: rotate(-2deg) translateY(0) }
      100%{ transform: rotate(2deg) translateY(4px) }
    }

    .rows{ display:flex; flex-direction:column; gap:12px; margin-top:16px }

    .row{
      --rnd: 0deg;
      --rndx: 0px;
      display:grid;
      grid-template-columns: 1fr auto auto;
      gap:10px; align-items:center;
      padding:12px;
      background:#ffffff;                     /* 实白 */
      border:1.5px solid rgba(120,100,200,.45);
      border-radius:12px;
      box-shadow: 0 8px 18px rgba(80,60,140,.12);
      transform: translateX(var(--rndx)) rotate(var(--rnd));
      transition: transform .35s cubic-bezier(.22,.61,.36,1), box-shadow .25s ease, border-color .25s ease;
    }
    .row:hover{
      transform: translateX(calc(var(--rndx) * .5)) rotate(calc(var(--rnd) * .5)) translateY(-1px);
      border-color: rgba(120,100,200,.75);
      box-shadow: 0 12px 26px rgba(80,60,140,.18);
    }

    /* URL 输入：白底深字，高对比 */
    .url{
      width:100%;
      border:2px solid rgba(120,100,200,.55);
      outline:none;
      background:#ffffff;
      color:#1e1735;                          /* 深色文字 */
      padding:12px 14px;
      border-radius:10px;
      font-size:15px;
      text-align:center;
      font-weight:800;
      font-family:'Share Tech Mono', ui-monospace, Consolas, Menlo, monospace;
      letter-spacing:.25px;
      text-shadow:none;                        /* 去掉多余柔光 */
      box-shadow: 0 2px 0 rgba(0,0,0,.03) inset;
      caret-color:#6c58ff;
    }
    .url::selection{ background: #e7dbff }     /* 明显的选中对比 */
    .url:focus{ outline:3px solid var(--focus); outline-offset:2px; }

    /* ping 小牌：白底深字，状态色仅文字，不影响底色可读性 */
    .ping{
      min-width:110px; text-align:center;
      font-weight:900; font-variant-numeric: tabular-nums;
      padding:10px 12px; border-radius:10px;
      background:#ffffff;
      border:2px solid rgba(120,100,200,.45);
      color:#2c2450;
      box-shadow: 0 2px 0 rgba(0,0,0,.03) inset;
    }
    .ping.ok{  color:var(--ok) }
    .ping.mid{ color:var(--mid) }
    .ping.bad{ color:var(--bad) }
    .ping.fail{color:var(--fail) }

    /* 访问按钮：提高文字与背景对比，保留梦幻配色 */
    .btn{
      border:2px solid rgba(120,100,200,.55);
      cursor:pointer;
      padding:10px 14px; border-radius:12px;
      background:
        linear-gradient(90deg, #ffffff, #fffbe0 55%, #ffeefe);
      color:#1f1836;                           /* 深色文字提高对比 */
      font-weight:900; letter-spacing:.3px;
      text-transform: uppercase;
      box-shadow: 0 8px 18px rgba(170,150,255,.18);
      transition: transform .12s ease, filter .12s ease;
    }
    .btn:hover{ transform: translateY(-1px) rotate(.3deg); filter: saturate(1.03) }
    .btn:active{ transform: translateY(0) scale(.985) }
    .btn:focus-visible{ outline:3px solid var(--focus); outline-offset:2px }

    .legend{
      display:flex; gap:12px; flex-wrap:wrap;
      margin-top:16px; font-size:13px; color:var(--txt);
      justify-content:center;
    }
    .dot{
      width:12px; height:12px; border-radius:50%;
      display:inline-block; margin-right:6px;
      box-shadow: 0 0 0 2px rgba(0,0,0,.06) inset;
      border:1px solid rgba(0,0,0,.08);
    }
    .d-ok{  background:#c8f4dc }
    .d-mid{ background:#fff1b8 }
    .d-bad{ background:#ffc7d0 }
    .d-fail{ background:#ffd3ee }

    /* 可访问性：统一键盘焦点 */
    :is(.refresh-btn, .url, .btn){ transition: box-shadow .12s ease, outline-color .12s ease }

    @media(max-width:560px){
      .row{ grid-template-columns: 1fr 1fr; }
      .ping{ order:3; grid-column:1 / -1; }
    }
  </style>

  <script>
    // 原有逻辑保持：ping + 刷新
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

    // 轻随机：保留梦境“无序”，但幅度较小以保证排版稳定
    function injectRandomness(){
      document.querySelectorAll('.row').forEach(row=>{
        const rot = (Math.random()*1.2 - 0.6).toFixed(2) + 'deg';  // -0.6° ~ 0.6°
        const shift = (Math.random()*4 - 2).toFixed(1) + 'px';     // -2px ~ 2px
        row.style.setProperty('--rnd', rot);
        row.style.setProperty('--rndx', shift);
      });
    }

    // 轻量上升花屑（保持低对比）
    function spawnPetals(){
      const container = document.body;
      for(let i=0;i<10;i++){
        const p = document.createElement('div');
        p.className = 'petal';
        const size = 6 + Math.random()*8;
        p.style.width = size+'px'; p.style.height = size+'px';
        p.style.left = Math.random()*100+'vw';
        p.style.bottom = (-10 - Math.random()*20)+'px';
        p.style.setProperty('--tx', (Math.random()*36 - 18)+'px');
        p.style.setProperty('--dur', (12 + Math.random()*8)+'s');
        p.style.opacity = .18 + Math.random()*.22;  // 更低透明度，避免干扰
        container.appendChild(p);
      }
    }

    document.addEventListener('DOMContentLoaded', ()=>{
      injectRandomness();
      spawnPetals();
    });
  </script>

  <style>
    /* 花屑：降低饱和与透明以免抢眼 */
    .petal{
      position:fixed; border-radius:50%;
      background-image: radial-gradient(circle at 30% 30%, rgba(255,255,255,.9), transparent 65%);
      mix-blend-mode: screen;
      animation: rise var(--dur) linear infinite;
    }
    .petal::before{
      content:"";
      position:absolute; inset:0; border-radius:50%;
      background:
        radial-gradient(circle at 40% 40%, rgba(246,212,239,.75), transparent 60%),
        radial-gradient(circle at 60% 60%, rgba(255,243,156,.75), transparent 70%),
        radial-gradient(circle at 35% 65%, rgba(168,240,195,.75), transparent 60%);
      filter: blur(1.5px) saturate(1.0);
    }
    @keyframes rise{
      0%   { transform: translateX(0) translateY(0) rotate(0deg) }
      50%  { transform: translateX(var(--tx)) translateY(-55vh) rotate(180deg) }
      100% { transform: translateX(calc(var(--tx) * -1)) translateY(-110vh) rotate(360deg) }
    }
  </style>
</head>
<body>
  <div class="grain"></div>
  <div class="blobs">
    <div class="blob b1"></div>
    <div class="blob b2"></div>
    <div class="blob b3"></div>
    <div class="blob b4"></div>
  </div>

  <div class="wrap">
    <header>
      <div class="title"> 线路检测 · LATENCY CHECK</div>
      <div class="subtitle">请访问延迟数字小的网址</div>
    </header>

    <div class="refresh-wrap">
      <button class="refresh-btn" onclick="refreshPage()">✿ 刷新可用线路</button>
    </div>

    <section class="card" role="region" aria-label="线路检测结果">
      <div class="ribbon" aria-hidden="true"></div>

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
