$(document).ready(function() {
    const entriesListTable = $('#entries-list-table').DataTable({
        searching: false,
        pagingType: "simple_numbers",
        columnDefs: [
            { orderable: true, targets: [0, 1] }, 
            { orderable: false, targets: [2] }           
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
