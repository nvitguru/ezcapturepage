document.addEventListener("DOMContentLoaded", function () {
    // Function to update image source and class management
    function updateSelection(groupName, imgId, noImgId, selectionClass, iconClass, folder) {
        const imgElement = document.getElementById(imgId);

        document.querySelectorAll(`input[name='${groupName}']`).forEach(function (input) {
            input.addEventListener('change', function () {
                if (this.checked) {
                    if (this.id === noImgId) {
                        imgElement.classList.add('d-none');
                    } else {
                        let imgSrc = `https://ezcapturepage.com/images/${folder}/${this.id}.png`;
                        imgElement.src = imgSrc;
                        imgElement.classList.remove('d-none');
                    }

                    document.querySelectorAll(`.${selectionClass}.${groupName}`).forEach(function (div) {
                        div.classList.remove(selectionClass);
                        div.querySelector(`.${iconClass}`).classList.add('d-none');
                    });

                    if (this.id !== noImgId) {
                        this.closest('div').classList.add(selectionClass, groupName);
                        this.closest('div').querySelector(`.${iconClass}`).classList.remove('d-none');
                    }
                }
            });
        });
    }

    // Function to update image source and class management for button
    function updateButtonSelection(groupName, imgId, selectionClass, iconClass, folder) {
        const imgElement = document.getElementById(imgId);

        document.querySelectorAll(`input[name='${groupName}']`).forEach(function (input) {
            input.addEventListener('change', function () {
                if (this.checked) {
                    let imgSrc = `https://ezcapturepage.com/images/${folder}/${this.id}.png`;
                    imgElement.src = imgSrc;
                    imgElement.classList.remove('d-none');

                    document.querySelectorAll(`.${selectionClass}.${groupName}`).forEach(function (div) {
                        div.classList.remove(selectionClass);
                        div.querySelector(`.${iconClass}`).classList.add('d-none');
                    });

                    this.closest('div').classList.add(selectionClass, groupName);
                    this.closest('div').querySelector(`.${iconClass}`).classList.remove('d-none');
                }
            });
        });
    }

    // Function to set initial state based on checked input
    function setInitialState(groupName, imgId, noImgId, selectionClass, iconClass, folder) {
        const checkedInput = document.querySelector(`input[name='${groupName}']:checked`);
        const imgElement = document.getElementById(imgId);

        if (checkedInput) {
            if (checkedInput.id === noImgId) {
                imgElement.classList.add('d-none');
            } else {
                let imgSrc = `https://ezcapturepage.com/images/${folder}/${checkedInput.id}.png`;
                imgElement.src = imgSrc;
                imgElement.classList.remove('d-none');
            }

            if (checkedInput.id !== noImgId) {
                checkedInput.closest('div').classList.add(selectionClass, groupName);
                checkedInput.closest('div').querySelector(`.${iconClass}`).classList.remove('d-none');
            }
        }
    }

    // Function to set initial state based on checked input for buttons
    function setInitialButtonState(groupName, imgId, selectionClass, iconClass, folder) {
        const checkedInput = document.querySelector(`input[name='${groupName}']:checked`);
        const imgElement = document.getElementById(imgId);

        if (checkedInput) {
            let imgSrc = `https://ezcapturepage.com/images/${folder}/${checkedInput.id}.png`;
            imgElement.src = imgSrc;
            imgElement.classList.remove('d-none');

            checkedInput.closest('div').classList.add(selectionClass, groupName);
            checkedInput.closest('div').querySelector(`.${iconClass}`).classList.remove('d-none');
        }
    }

    // Apply the functions to the header and subheader radios
    updateSelection('header', 'page-header-img', 'noHeader', 'piece-selected', 'selected-check', 'header');
    setInitialState('header', 'page-header-img', 'noHeader', 'piece-selected', 'selected-check', 'header');

    updateSelection('subheader', 'page-subheader-img', 'noSubheader', 'piece-selected', 'selected-check', 'subheader');
    setInitialState('subheader', 'page-subheader-img', 'noSubheader', 'piece-selected', 'selected-check', 'subheader');

    // Apply the functions to the button radios
    updateButtonSelection('button', 'page-button-img', 'piece-selected', 'selected-check', 'button');
    setInitialButtonState('button', 'page-button-img', 'piece-selected', 'selected-check', 'button');

    // Apply the functions to the background radios
    function updateBackgroundSelection() {
        const backgroundGroupName = 'background';
        const noBackgroundId = 'noBackground';
        const selectionClass = 'piece-selected';
        const iconClass = 'selected-check';

        document.querySelectorAll(`input[name='${backgroundGroupName}']`).forEach(function (input) {
            input.addEventListener('change', function () {
                if (this.checked) {
                    document.querySelectorAll(`.${selectionClass}.${backgroundGroupName}`).forEach(function (div) {
                        div.classList.remove(selectionClass);
                        div.querySelector(`.${iconClass}`).classList.add('d-none');
                    });

                    if (this.id !== noBackgroundId) {
                        this.closest('div').classList.add(selectionClass, backgroundGroupName);
                        this.closest('div').querySelector(`.${iconClass}`).classList.remove('d-none');
                    }
                }
            });
        });

        // Set initial state based on checked input from database
        const checkedInput = document.querySelector(`input[name='${backgroundGroupName}']:checked`);
        if (checkedInput && checkedInput.id !== noBackgroundId) {
            checkedInput.closest('div').classList.add(selectionClass, backgroundGroupName);
            checkedInput.closest('div').querySelector(`.${iconClass}`).classList.remove('d-none');
        }
    }

    updateBackgroundSelection();

    // Video Embed functionality
    const videoEmbedInput = document.getElementById('videoEmbed');
    const pageVideoImg = document.getElementById('page-video-img');
    const videoDiv = document.getElementById('videoDiv');
    const videoSource = document.getElementById('videoSource');
    const noVideoCheckbox = document.getElementById('noVideo');

    videoEmbedInput.addEventListener('input', function () {
        if (videoEmbedInput.value.trim() !== '') {
            pageVideoImg.classList.add('d-none');
            videoDiv.classList.remove('d-none');
            videoSource.src = videoEmbedInput.value;
            noVideoCheckbox.checked = false;
        } else {
            pageVideoImg.classList.remove('d-none');
            videoDiv.classList.add('d-none');
            videoSource.src = '';
        }
    });

    noVideoCheckbox.addEventListener('change', function () {
        if (this.checked) {
            pageVideoImg.classList.add('d-none');
            videoDiv.classList.add('d-none');
            videoSource.src = '';
            videoEmbedInput.value = '';
        } else if (videoEmbedInput.value.trim() !== '') {
            pageVideoImg.classList.add('d-none');
            videoDiv.classList.remove('d-none');
            videoSource.src = videoEmbedInput.value;
        } else {
            pageVideoImg.classList.remove('d-none');
            videoDiv.classList.add('d-none');
            videoSource.src = '';
        }
    });

    // Set initial state for video embed
    if (videoEmbedInput.value.trim() !== '') {
        pageVideoImg.classList.add('d-none');
        videoDiv.classList.remove('d-none');
        videoSource.src = videoEmbedInput.value;
        noVideoCheckbox.checked = false;
    } else {
        pageVideoImg.classList.remove('d-none');
        videoDiv.classList.add('d-none');
        videoSource.src = '';
    }

    // Ensure page-video-img is hidden if noVideo is selected on page load
    if (noVideoCheckbox.checked) {
        pageVideoImg.classList.add('d-none');
        videoDiv.classList.add('d-none');
        videoSource.src = '';
    }

    const disclaimerTextarea = document.getElementById("disclaimer");
    const pageDisclaimerImg = document.getElementById("page-disclaimer-img");
    const disclaimerP = document.getElementById("disclaimerP");
    const disclaimerText = document.getElementById("disclaimerText");

    // Function to update the disclaimer text
    function updateDisclaimerText() {
        const text = disclaimerTextarea.value;
        if (text.trim() !== "") {
            pageDisclaimerImg.classList.add("d-none");
            disclaimerP.classList.remove("d-none");
            disclaimerText.textContent = text;
        } else {
            pageDisclaimerImg.classList.remove("d-none");
            disclaimerP.classList.add("d-none");
        }
    }

    // Event listener for textarea input
    disclaimerTextarea.addEventListener("input", updateDisclaimerText);

    // Check initial content from the database
    if (disclaimerTextarea.value.trim() !== "") {
        updateDisclaimerText();
    }

    const formRadios = document.getElementById('formRadios');
    const formTemplate = document.getElementById('formTemplate');
    const selectedForm = document.getElementById('selectedForm');

    // Function to show selected form details
    const showFormDetails = (formID) => {
        // Fetch form details from the server
        fetch(`fetchForm.php?formID=${formID}`)
            .then(response => response.json())
            .then(data => {
                // Populate the form details based on the response
                if (data) {
                    console.log('Form data:', data);
                    // Hide form template and show selected form
                    formTemplate.classList.add('d-none');
                    selectedForm.classList.remove('d-none');

                    // Update form fields based on data
                    const fullNameDiv = document.getElementById('fullNameDiv');
                    const splitNameDiv = document.getElementById('splitNameDiv');
                    const phoneDiv = document.getElementById('phoneDiv');
                    const humanDiv = document.getElementById('humanDiv');

                    if (fullNameDiv) {
                        if (data.formName == 1) {
                            fullNameDiv.classList.remove('d-none');
                            splitNameDiv.classList.add('d-none');
                        } else if (data.formName == 2) {
                            fullNameDiv.classList.add('d-none');
                            splitNameDiv.classList.remove('d-none');
                        }
                    }

                    if (phoneDiv) {
                        if (data.formPhone) {
                            phoneDiv.classList.remove('d-none');
                        } else {
                            phoneDiv.classList.add('d-none');
                        }
                    }

                    if (humanDiv) {
                        if (data.formHuman) {
                            humanDiv.classList.remove('d-none');
                        } else {
                            humanDiv.classList.add('d-none');
                        }
                    }
                }
            })
            .catch(error => console.error('Error fetching form details:', error));
    };

    // Event listener for form radio buttons
    formRadios.addEventListener('change', function (event) {
        if (event.target.name === 'form') {
            const selectedFormID = event.target.value;
            showFormDetails(selectedFormID);
        }
    });

    // Initial load: check if a form is already selected
    const initiallySelectedForm = document.querySelector('input[name="form"]:checked');
    if (initiallySelectedForm) {
        showFormDetails(initiallySelectedForm.value);
    }
});
document.addEventListener("DOMContentLoaded", function () {
    const noVideoCheckbox = document.getElementById("noVideo");
    const videoEmbedInput = document.getElementById("videoEmbed");

    // Function to toggle input state
    const toggleVideoInput = () => {
        if (noVideoCheckbox.checked) {
            videoEmbedInput.value = ""; // Clear input when disabled
            videoEmbedInput.setAttribute("disabled", "disabled");
        } else {
            videoEmbedInput.removeAttribute("disabled");
        }
    };

    // Attach event listener
    noVideoCheckbox.addEventListener("change", toggleVideoInput);

    // Initial toggle state on page load
    toggleVideoInput();
});
document.addEventListener("DOMContentLoaded", function () {
    // Add event listeners for all accordion buttons
    const accordionButtons = document.querySelectorAll(".accordionBtn");

    accordionButtons.forEach(button => {
        button.addEventListener("click", function () {
            // Check if the accordion is being expanded
            const targetId = this.getAttribute("data-bs-target");
            const targetElement = document.querySelector(targetId);

            if (targetElement && targetElement.classList.contains("show") === false) {
                // Scroll to the top of the page
                window.scrollTo({
                    top: 0,
                    behavior: "smooth" // Smooth scrolling
                });
            }
        });
    });
});