(function () {
    const base = window.APP_BASE || '';
    const statusEl = document.getElementById('ajax-status');

    function setStatus(text, isError) {
        if (!statusEl) return;
        statusEl.textContent = text;
        statusEl.className = 'ajax-status' + (isError ? ' error' : ' success');
    }

    document.querySelectorAll('.btn-delete-product').forEach(function (btn) {
        btn.addEventListener('click', async function () {
            const id = this.getAttribute('data-id');

            if (!confirm('Fshi produktin #' + id + ' pa rifreskuar faqen?')) {
                return;
            }

            try {
                const res = await fetch(base + '/api/products.php?id=' + encodeURIComponent(id), {
                    method: 'DELETE',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                });
                const data = await res.json();

                if (data.success) {
                    const row = document.getElementById('product-row-' + id);
                    if (row) row.remove();
                    setStatus(data.message || 'U fshi me sukses.', false);
                } else {
                    setStatus(data.error || 'Gabim.', true);
                }
            } catch (e) {
                setStatus('Gabim rrjeti gjatë AJAX.', true);
            }
        });
    });
})();
