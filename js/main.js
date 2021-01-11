// Fetch form for custom Bootstrap validation styles
const form = document.querySelector('.needs-validation')

// Prevent submission if form is invalid
form.addEventListener('submit', function (event) {
    if (!form.checkValidity()) {
        event.preventDefault()
        event.stopPropagation()
    }

    form.classList.add('was-validated')
}, false)

