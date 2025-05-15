<?php 
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Activity Logs</title>
    <link rel="stylesheet" href="index.css" />
</head>
<body>
    <div class="box">
        <h2>Activity Logs for <?= $_SESSION['name'] ?></h2>
        <ul id="log-list"></ul>
        <a href="user_page.php">← Back to Tasks</a>
    </div>

    <script>
        async function fetchLogs() {
            const res = await fetch('activity-logs');
            const logs = await res.json();
            const list = document.getElementById('log-list');
            logs.forEach(log => {
                const li = document.createElement('li');
                li.textContent = `${log.action} task #${log.task_id} on ${log.timestamp}`;
                list.appendChild(li);
            });
        }
        window.onload = fetchLogs;
    </script>
</body>
</html>
