// Fetch form for custom Bootstrap validation styles
const form = document.querySelector('.needs-validation')

// Check validation and send form
form.addEventListener('submit', function (event) {
    // event.preventDefault()

    // If the form does not validate, prevent submission
    if (!form.checkValidity() || !grecaptcha.getResponse()) {
        event.preventDefault()
        event.stopPropagation()
    }

    // Mark the form as validated
    form.classList.add('was-validated');

    // Send data to PHP
    let formData = getFromValues(form);
    const request = new XMLHttpRequest();

    request.open('POST', 'contact.php', true);
    request.send(formData);

}, false)


// Make the form button clickable upon captcha completion
function onCaptchaComplete(){
    const submitButton = document.querySelector('.submit-btn');

    submitButton.classList.replace('btn-secondary', 'btn-primary')
    submitButton.removeAttribute('disabled');
}


// Get form responses and prep them for sending
function getFromValues(form){
    let formData = new FormData;

    for ( let i = 0; i < form.elements.length; i++ ) {
        let element = form.elements[i];

        if(element.name){
            formData.append(element.name, element.value);
        }
    }

    return formData;
}
