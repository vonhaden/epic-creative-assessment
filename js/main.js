// Fetch form for custom Bootstrap validation styles
const form = document.querySelector('.needs-validation')

// Create the bootstrap modal for letting the user know their form was submitted
const modal = new bootstrap.Modal(document.getElementById('submissionModal'));

// Check validation and send form
form.addEventListener('submit', function (event) {

    // Prevent default submit action
    event.preventDefault()

    // If the form does not validate, prevent submission
    if (!form.checkValidity() || !grecaptcha.getResponse()) {
        event.stopPropagation()
    }

    // Mark the form as validated
    form.classList.add('was-validated');

    // Send data to PHP
    let formData = getFromValues(form);
    const request = new XMLHttpRequest();
    request.open('POST', 'contact.php', true);
    request.send(formData);


    // Handle response from server
    request.onreadystatechange = function () {
        if (request.status === 200) {
            console.log(request.responseText);

            // Show the Modal
            modal.show();
            document.getElementById('submissionModalBody').innerHTML = request.responseText;

            // Reset ReCaptcha
            grecaptcha.reset();

        }
    };

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


