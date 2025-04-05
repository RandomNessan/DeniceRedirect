<?php
require_once '../config/db.php';

// 随机获取两个 URL
$stmt = $pdo->query("SELECT url FROM urls ORDER BY RAND() LIMIT 2");
$urls = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <title>线路检测</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Orbitron', sans-serif;
        }
        body {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
            color: #fff;
        }
        h1 {
            font-size: 28px;
            text-shadow: 0 0 15px rgba(0, 255, 255, 0.8);
            margin-bottom: 20px;
        }
        .container {
            width: 90%;
            max-width: 500px;
            padding: 20px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            backdrop-filter: blur(10px);
            box-shadow: 0 0 20px rgba(0, 255, 255, 0.3);
        }
        .url-box {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px;
            margin: 10px 0;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.15);
            box-shadow: 0 0 10px rgba(0, 255, 255, 0.2);
        }
        .url-input {
            flex: 1;
            padding: 10px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
            text-align: center;
        }
        .url-input::placeholder {
            color: #ddd;
        }
        .ping-result {
            font-size: 16px;
            font-weight: bold;
            text-shadow: 0 0 8px rgba(255, 255, 255, 0.8);
            transition: color 0.3s ease-in-out;
        }
        .btn {
            padding: 10px 15px;
            font-size: 14px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            background: #00c3ff;
            color: white;
            transition: 0.3s;
        }
        .btn:hover {
            background: #0078aa;
            box-shadow: 0 0 10px rgba(0, 255, 255, 0.7);
        }
        .hidden-copyright {
            position: fixed;
            bottom: 10px;
            right: 10px;
            font-size: 14px;
            color: #fff;
            opacity: 0.7;
            user-select: none;
        }
    </style>
    <script>
        async function pingURL(url, elementId) {
            const start = performance.now();
            try {
                await fetch(url, { mode: 'no-cors' });
                const latency = Math.round(performance.now() - start);
                let resultElement = document.getElementById(elementId);
                resultElement.innerText = latency + " ms";
                
                // 根据延迟修改颜色
                if (latency < 500) {
                    resultElement.style.color = "#0f0"; // 绿色
                } else if (latency < 1000) {
                    resultElement.style.color = "#ffcc00"; // 黄色
                } else {
                    resultElement.style.color = "#ff3300"; // 红色
                }
            } catch {
                document.getElementById(elementId).innerText = "无法连接";
                document.getElementById(elementId).style.color = "#541404"; // 血色
            }
        }

        document.addEventListener("DOMContentLoaded", function () {
            const footer = document.createElement("div");
            footer.classList.add("hidden-copyright");

            const encodedText = "%u0043%u006F%u0070%u0079%u0072%u0069%u0067%u0068%u0074%u0040%u0052%u0061%u006E%u0064%u006F%u006D%u004E%u0065%u0073%u0073%u0061%u006E%u0020%u0032%u0030%u0032%u0035";
            const encodedHref = "https://github.com/RandomNessan/DeniceRedirect";


            const copyrightText = unescape(encodedText);

            footer.innerHTML = `<span>${copyrightText.replace("RandomNessan", `<a href="${encodedHref}" target="_blank" style="color:#fff;text-decoration:none;">RandomNessan</a>`)}</span>`;
            document.body.appendChild(footer);
        });
    </script>
</head>
<body>
    <h1>🚀 线路检测</h1>
    <div class="container">
        <?php foreach ($urls as $index => $url): ?>
            <div class="url-box">
                <input type="text" class="url-input" value="<?= $url['url'] ?>" readonly>
                <span class="ping-result" id="ping<?= $index ?>">测试中...</span>
                <button class="btn" onclick="window.open('<?= $url['url'] ?>', '_blank')">访问</button>
            </div>
            <script>pingURL("<?= $url['url'] ?>", "ping<?= $index ?>");</script>
        <?php endforeach; ?>
    </div>
</body>
</html>
