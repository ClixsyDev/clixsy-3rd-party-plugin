const cf7PhoneValidation = () => {
  let forms = getElements('form');

  const getUnmaskedValue = (value) => {
    return value.replace(/\D/g, ''); // remove all non-digits
  };

  if (ifSelectorExist(forms)) {
    Array.from(forms).forEach(form => {
      let phoneInputs = getElements('input[type="tel"]', form);

      if (ifSelectorExist(phoneInputs)) {
        Array.from(phoneInputs).forEach(element => {

          element.addEventListener('blur', () => {
            let value = getUnmaskedValue(element.value);
            if (value.length > 3) {
              value = `(${value.slice(0, 3)}) ${value.slice(3)}`;
            }
            if (value.length > 9) {
              value = `${value.slice(0, 9)}-${value.slice(9, 13)}`;
            }
						if (getUnmaskedValue(value).length !== 10) {
							element.style.border = '2px solid red'; 
						} else {
							element.style.border = ''; 
						}
            element.value = value;
          });

        });

        let submitButton = getElement('.btn', form);
        if (submitButton) {
          submitButton.addEventListener('click', function (event) {
            let phoneInvalid = Array.from(phoneInputs).some(input => {
              let unmaskedValue = getUnmaskedValue(input.value);
              if (unmaskedValue.length !== 10) {
                input.style.border = '2px solid red'; // Add red border if not 10 digits
                return true;
              } else {
                input.style.border = ''; // Reset border if valid
                return false;
              }
            });

            if (phoneInvalid) {
              event.preventDefault(); // Prevent form from submitting
              return false;
            }
          });
        }
      }
    });
  }
}

document.addEventListener("DOMContentLoaded", function() {
	cf7PhoneValidation();
});
