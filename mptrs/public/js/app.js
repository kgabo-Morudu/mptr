// Auto-hide flash messages after 5s
setTimeout(() => {
    document.querySelectorAll('.alert-dismissible').forEach(el => {
        try { new bootstrap.Alert(el).close(); } catch(e) {}
    });
}, 5000);

// Confirm all destructive forms
document.querySelectorAll('form[data-confirm]').forEach(f => {
    f.addEventListener('submit', e => {
        if (!confirm(f.dataset.confirm)) e.preventDefault();
    });
});