<?php
require_once './config/db.php';

// 随机获取两个 URL
$stmt = $pdo->query("SELECT url FROM urls ORDER BY RAND() LIMIT 2");
$urls = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="zh">
<head>
  <meta charset="UTF-8" />
  <title>线路检测</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600&family=Orbitron:wght@500;700&family=Share+Tech+Mono&family=JetBrains+Mono:wght@400;600&family=IBM+Plex+Mono:wght@400;600&display=swap');

    :root{
      --bg-1:#0e1220;
      --bg-2:#131a2a;
      --glass:rgba(255,255,255,.10);
      --stroke:rgba(255,255,255,.18);
      --txt:#eaf6ff;
      --sub:#b9d3ff;
      --neon:#57e9ff;
      --neon2:#b06bff;
      --ok:#33ff99;
      --mid:#ffd766;
      --bad:#ff5b5b;
      --chip:#1a2236;
    }

    *{box-sizing:border-box}
    html,body{height:100%}
    body{
      margin:0;
      color:var(--txt);
      font-family:'Inter',system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial;
      background:
        radial-gradient(1200px 800px at 75% -10%, rgba(176,107,255,.25), transparent 60%),
        radial-gradient(900px 600px at -10% 90%, rgba(87,233,255,.20), transparent 60%),
        linear-gradient(160deg, var(--bg-1), var(--bg-2));
      overflow-x:hidden;
      display:flex;
      align-items:center;
      justify-content:center;
      padding:24px;
    }

    .grid{
      position:fixed; inset:0; pointer-events:none; opacity:.22;
      background:
        linear-gradient(transparent 95%, rgba(255,255,255,.18) 100%) 0 0/100% 28px,
        linear-gradient(90deg, transparent 95%, rgba(255,255,255,.18) 100%) 0 0/28px 100%;
      mask-image: radial-gradient(1200px 700px at 50% 40%, #000 55%, transparent 100%);
    }
    .stars{
      position:fixed; inset:0; pointer-events:none; mix-blend-mode:screen; opacity:.35;
      background-image: radial-gradient(#9ff 1px, transparent 1px);
      background-size:3px 3px;
      mask-image: radial-gradient(900px 500px at 60% 30%, #000 60%, transparent 100%);
    }

    .wrap{width:min(780px,100%)}

    header{text-align:center;margin-bottom:18px}
    .title{
      font-family:'Orbitron',sans-serif;
      letter-spacing:.04em;
      font-weight:700;
      font-size:clamp(22px,3.2vw,32px);
      text-shadow:0 0 12px rgba(87,233,255,.6),0 0 28px rgba(176,107,255,.4);
    }
    .subtitle{margin-top:6px;color:var(--sub);font-size:14px;opacity:.88;}

    .refresh-wrap{text-align:center;margin:18px 0 24px;}
    .refresh-btn{
      border:none;cursor:pointer;
      padding:12px 22px;
      border-radius:12px;
      font-family:'Orbitron',sans-serif;
      font-weight:600;
      font-size:16px;
      letter-spacing:.5px;
      background:
        radial-gradient(120% 140% at 0% 0%, rgba(176,107,255,.55), transparent 42%),
        linear-gradient(90deg,#3ad6ff,#9b6bff);
      color:#07111a;
      box-shadow:0 10px 22px rgba(58,214,255,.28),0 0 0 1px rgba(255,255,255,.18) inset;
      transition:transform .15s ease,filter .15s ease,box-shadow .15s ease;
    }
    .refresh-btn:hover{transform:translateY(-2px);filter:saturate(1.1);}
    .refresh-btn:active{transform:scale(.97);}

    .card{
      position:relative;
      padding:18px 16px;
      background:var(--glass);
      border:1px solid var(--stroke);
      border-radius:16px;
      backdrop-filter:blur(10px);
      box-shadow:0 0 0 1px rgba(87,233,255,.12) inset,0 8px 22px rgba(0,0,0,.35);
      overflow:hidden;
    }

    .rows{display:flex;flex-direction:column;gap:12px}
    .row{
      display:grid;
      grid-template-columns:1fr auto auto;
      gap:10px;
      align-items:center;
      padding:12px;
      background:rgba(255,255,255,.06);
      border:1px solid rgba(255,255,255,.12);
      border-radius:12px;
      transition:transform .25s ease,box-shadow .25s ease,border-color .25s ease;
    }
    .row:hover{
      transform:translateY(-2px);
      border-color:rgba(87,233,255,.35);
      box-shadow:0 8px 22px rgba(87,233,255,.12),0 0 0 1px rgba(87,233,255,.10) inset;
    }

    /* ==== 赛博风 URL 字体样式 ==== */
    .url{
      width:100%;border:none;outline:none;
      font-family:'Share Tech Mono','JetBrains Mono','IBM Plex Mono',ui-monospace,SFMono-Regular,Menlo,Monaco,Consolas,"Courier New",monospace;
      font-weight:600;
      letter-spacing:.3px;
      font-variant-ligatures:none;
      font-feature-settings:"zero" 1;
      background:linear-gradient(180deg,rgba(255,255,255,.12),rgba(255,255,255,.08));
      color:#eaf6ff;
      padding:12px 14px;
      border-radius:10px;
      font-size:15px;
      text-align:center;
      text-shadow:0 0 6px rgba(87,233,255,.45),0 0 14px rgba(176,107,255,.25);
      caret-color:#9b6bff;
    }
    .url::selection{background:rgba(176,107,255,.35);}

    .ping{
      min-width:92px;text-align:center;
      font-weight:600;font-variant-numeric:tabular-nums;
      padding:10px 12px;border-radius:10px;
      background:var(--chip);border:1px solid rgba(255,255,255,.12);
      text-shadow:0 0 10px rgba(255,255,255,.25);
    }
    .ping.ok{color:var(--ok)}.ping.mid{color:var(--mid)}.ping.bad{color:var(--bad)}.ping.fail{color:#ff6b9a}

    .btn{
      border:none;cursor:pointer;padding:10px 14px;
      border-radius:10px;font-weight:600;
      background:linear-gradient(90deg,#3ad6ff,#9b6bff);
      color:#07111a;
      box-shadow:0 10px 22px rgba(58,214,255,.28),0 0 0 1px rgba(255,255,255,.18) inset;
    }

    .legend{display:flex;gap:10px;flex-wrap:wrap;margin-top:14px;font-size:12px;color:var(--sub);justify-content:center;}
    .dot{width:10px;height:10px;border-radius:50%;display:inline-block;margin-right:6px}
    .d-ok{background:var(--ok)}.d-mid{background:var(--mid)}.d-bad{background:var(--bad)}.d-fail{background:#ff6b9a}

    @media(max-width:520px){
      .row{grid-template-columns:1fr 1fr;}
      .ping{order:3;grid-column:1/-1;}
    }
  </style>
  <script>
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
  <div class="grid"></div>
  <div class="stars"></div>

  <div class="wrap">
    <header>
      <div class="title">线路检测系统</div>
      <div class="subtitle">即时延迟可视</div>
    </header>

    <div class="refresh-wrap">
      <button class="refresh-btn" onclick="refreshPage()"> 刷新可用线路 </button>
    </div>

    <section class="card">
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
