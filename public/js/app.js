// Any form with a data-confirm attribute asks for confirmation first.
//<form method="POST" data-confirm-title="Archive?" data-confirm="Are you sure?" data-confirm-button="Yes, archive">
document.addEventListener('submit', function (e) {
  var form = e.target;
  if (!form.matches || !form.matches('form[data-confirm]')) return;

  e.preventDefault();   // stop the form for now

  Swal.fire({
    title: form.dataset.confirmTitle || 'Are you sure?',
    text: form.dataset.confirm,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#099C55',
    confirmButtonText: form.dataset.confirmButton || 'Yes'
  }).then(function (result) {
    if (result.isConfirmed) form.submit();   // submit() does not trigger this handler again
  });
});