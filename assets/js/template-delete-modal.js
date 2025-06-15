const openDeleteModal = (button) => {
    const id = $(button).data('id');
    const name = $(button).data('name');

    $('#deleteModalMessage').text(`Czy na pewno chcesz usunąć szablon "${name}" i wszystkie jego wpisy?`);

    const form = $('#deleteForm');

    form.attr('action', `/${window.locale}/template/${id}/delete`);

    $('#deleteToken').val(window.templateDeleteCsrfTokens[id]);

    $('#deleteModal').css('display', 'flex');
};

const closeDeleteModal = () => {
    $('#deleteModal').css('display', 'none');
};
