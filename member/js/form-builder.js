document.addEventListener("DOMContentLoaded", function () {
    // Function to handle visibility of name fields
    function handleNameFields() {
        const fullName = document.getElementById("fullName");
        const splitName = document.getElementById("splitName");
        const noName = document.getElementById("noName");
        const fullNameDiv = document.getElementById("fullNameDiv");
        const splitNameDiv = document.getElementById("splitNameDiv");

        if (fullName.checked) {
            fullNameDiv.classList.remove("d-none");
            splitNameDiv.classList.add("d-none");
        } else if (splitName.checked) {
            splitNameDiv.classList.remove("d-none");
            fullNameDiv.classList.add("d-none");
        } else if (noName.checked) {
            fullNameDiv.classList.add("d-none");
            splitNameDiv.classList.add("d-none");
        }
    }

    // Function to handle visibility of phone field
    function handlePhoneField() {
        const phone = document.getElementById("phone");
        const phoneDiv = document.getElementById("phoneDiv");

        if (phone.checked) {
            phoneDiv.classList.remove("d-none");
        } else {
            phoneDiv.classList.add("d-none");
        }
    }

    // Function to handle visibility of human check field
    function handleHumanField() {
        const human = document.getElementById("human");
        const humanDiv = document.getElementById("humanDiv");

        if (human.checked) {
            humanDiv.classList.remove("d-none");
        } else {
            humanDiv.classList.add("d-none");
        }
    }

    // Add event listeners for name radio buttons
    document.getElementById("fullName").addEventListener("change", handleNameFields);
    document.getElementById("splitName").addEventListener("change", handleNameFields);
    document.getElementById("noName").addEventListener("change", handleNameFields);

    // Add event listeners for phone checkbox
    document.getElementById("phone").addEventListener("change", handlePhoneField);

    // Add event listeners for human checkbox
    document.getElementById("human").addEventListener("change", handleHumanField);

    // Initial load handling
    handleNameFields();
    handlePhoneField();
    handleHumanField();
});
