$(document).ready(function() {
    const templatesTable = $('#templates-table').DataTable({
        searching: false,
        pagingType: "simple_numbers",
        columnDefs: [
            { orderable: true, targets: [0, 1, 2, 3] }, 
            { orderable: false, targets: [4] }           
        ],
        language: {
            lengthMenu: "Pokaż _MENU_ rekordów",
            info: "Wyświetlono _START_ do _END_ z _TOTAL_ rekordów",
            infoEmpty: "Brak rekordów do wyświetlenia",
            infoFiltered: "(filtrowano z _MAX_ wszystkich rekordów)",
            zeroRecords: "Brak pasujących rekordów",
            paginate: {
                previous: "Poprzednia",
                next: "Następna"
            }
        }
    });
});
