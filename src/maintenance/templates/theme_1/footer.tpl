{* Footer Template *}
</div>
<footer class="text-center py-3">
        <span>Maintenance System</span>
        <span> | Version: {$software_version|default:'0.0.0'}</span>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
<script>
    document.querySelectorAll('.tableSearch').forEach(function(input) {
        input.addEventListener('input', function() {
            var filter = this.value.toLowerCase();
            var tableId = this.getAttribute('data-table');
            var table = document.getElementById(tableId);
            if (!table) return;
            var rows = table.querySelectorAll('tbody tr');
            rows.forEach(function(row) {
                var text = row.textContent.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
            });
        });
    });
</script>
</html>
