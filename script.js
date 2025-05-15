function showForm(formId){
    document.querySelectorAll('.form-box').forEach(form => form.classList.remove("active"));
    document.getElementById(formId).classList.add("active");
}

await fetch(`tasks_handler.php/${id}`, { method: 'DELETE' });

fetch(`tasks_handler.php/${id}`, {
    method: 'PUT',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ title, description })
});
