// =====================================================================================================================
// Show the form
// =====================================================================================================================

// Fetch the button
const showFormBtn = document.querySelector('.showFormBtn');

// On click, hide the button and show the form
showFormBtn.addEventListener("click", () => {
    showFormBtn.classList.add('d-none');
    form.classList.remove('d-none');
})


// =====================================================================================================================
// Form Handling
// =====================================================================================================================

// Fetch form for custom Bootstrap validation styles
const form = document.querySelector('.needs-validation')

// Create the bootstrap modal for letting the user know their form was submitted
const modal = new bootstrap.Modal(document.getElementById('submissionModal'));

// Submit click event
// Check validation, sends the form, alerts the user
form.addEventListener('submit', function (event) {

    // Validation
    // -----------------------------------------------------------------------------------
    // Prevent default submit action
    event.preventDefault()

    // If the form does not validate, prevent submission
    if (!form.checkValidity() || !grecaptcha.getResponse()) {
        event.stopPropagation()
    }

    // Mark the form as validated
    form.classList.add('was-validated');



    // Sending the Form
    // -----------------------------------------------------------------------------------
    let formData = getFromValues(form);
    const request = new XMLHttpRequest();
    request.open('POST', 'contact.php', true);
    request.send(formData);



    // Handle response from server
    // -----------------------------------------------------------------------------------
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



// =====================================================================================================================
// Auxiliary Functions
// =====================================================================================================================

// Make the form button clickable once the captcha is completed
function onCaptchaComplete(){
    const submitButton = document.querySelector('.submit-btn');

    submitButton.classList.replace('btn-secondary', 'btn-primary')
    submitButton.removeAttribute('disabled');
}


// Get the values from the form and prep them for sending
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
