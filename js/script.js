document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('form'); // Select all forms
    forms.forEach(form => {
        form.addEventListener('submit', function(event) {
            let isValid = true;
            const inputs = form.querySelectorAll('input[required], textarea[required], select[required]');

            inputs.forEach(input => {
                if (!input.value.trim()) {
                    isValid = false;
                    input.classList.add('error'); // Add error class for styling
                    event.preventDefault(); // Prevent form submission
                    displayErrorMessage(input, 'This field is required');

                } else {
                  input.classList.remove('error');
                  clearErrorMessage(input);
                }
            });

            if (!isValid) {
                event.preventDefault(); // Prevent form submission if any field is invalid
            }
        });
    });

    function displayErrorMessage(inputElement, message) {
      let errorSpan = inputElement.nextElementSibling;
      if (errorSpan && errorSpan.classList.contains('error-message')) {
        errorSpan.textContent = message;
      } else {
        errorSpan = document.createElement('span');
        errorSpan.classList.add('error-message');
        errorSpan.textContent = message;
        inputElement.parentNode.insertBefore(errorSpan, inputElement.nextSibling);
      }

    }

    function clearErrorMessage(inputElement) {
        const errorSpan = inputElement.nextElementSibling;
        if (errorSpan && errorSpan.classList.contains('error-message')) {
            errorSpan.remove();
        }
    }
});