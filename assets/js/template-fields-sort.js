const table = document.getElementById('sortable-fields');

if (table) {
    const reorderUrl = document.querySelector('#reorder-url').value;

    Sortable.create(table, {
        animation: 150,
        onEnd: function () {
            const order = [...document.querySelectorAll('#sortable-fields tr')].map((row, index) => ({
                id: row.querySelector('.field-id').textContent.trim(),
                position: index + 1
            }));

            fetch(reorderUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ order: order }),
            }).then(response => {
                if (!response.ok) {
                    alert('Wystąpił błąd przy zapisie kolejności.');
                }
            });
        }
    });
}
