<?php 
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Page</title>
    <link rel="stylesheet" href="./index.css"/>
</head>
<body style="background-color: #f0f0f0;">
    <div class="box">
        <h1>Welcome, <span><?= $_SESSION['name']; ?></span></h1>
        <p>This is an <span>User</span>Page</p>
<div class="task-container">
    <h2>Your Tasks</h2>
    <form id="task-form">
        <input type="text" id="task-title" placeholder="Task Title" required />
        <textarea id="task-desc" placeholder="Task Description" required></textarea>
        <button type="submit">Add Task</button>
    </form>

    <ul id="task-list">
    </ul>

    <a href="activity_logs.php">View Activity Logs</a>
</div>

<script>
    async function fetchTasks() {
        const res = await fetch('tasks.php');
        const tasks = await res.json();
        const list = document.getElementById('task-list');
        list.innerHTML = '';
        tasks.forEach(task => {
            const li = document.createElement('li');
            li.innerHTML = `
                <strong>${task.title}</strong><br>
                <p>${task.description}</p>
                <button onclick="deleteTask(${task.id})">Delete</button>
                <button onclick="editTask(${task.id}, '${task.title}', \`${task.description}\`)">Edit</button>
            `;
            list.appendChild(li);
        });
    }

    async function addTask(e) {
        e.preventDefault();
        const title = document.getElementById('task-title').value;
        const description = document.getElementById('task-desc').value;
        await fetch('tasks.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ title, description })
        });
        e.target.reset();
        fetchTasks();
    }

    async function deleteTask(id) {
        await fetch(`tasks/${id}`, { method: 'DELETE' });
        fetchTasks();
    }

    function editTask(id, oldTitle, oldDesc) {
        const title = prompt("Edit Title:", oldTitle);
        const description = prompt("Edit Description:", oldDesc);
        if (title && description) {
            fetch(`tasks/${id}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ title, description })
            }).then(fetchTasks);
        }
    }

    document.getElementById('task-form').addEventListener('submit', addTask);
    window.onload = fetchTasks;
</script>

        <button onclick="window.location.href='logout.php'">Logout</button>
    </div>
</body>
</html>