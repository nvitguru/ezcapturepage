document.addEventListener("DOMContentLoaded", () => {
    "use strict";

    const BASE_IMG_URL = "https://ezcapturepage.com/images";

    // ---------- DOM Helpers ----------
    const qs = (sel, root = document) => root.querySelector(sel);
    const qsa = (sel, root = document) => Array.from(root.querySelectorAll(sel));

    const addHidden = (el) => el && el.classList.add("d-none");
    const removeHidden = (el) => el && el.classList.remove("d-none");

    const clearSelection = (selectionClass, groupName, iconClass) => {
        qsa(`.${selectionClass}.${groupName}`).forEach((div) => {
            div.classList.remove(selectionClass);

            const icon = div.querySelector(`.${iconClass}`);
            if (icon) addHidden(icon);
        });
    };

    const markSelected = (input, selectionClass, groupName, iconClass) => {
        const wrapper = input.closest("div");
        if (!wrapper) return;

        wrapper.classList.add(selectionClass, groupName);

        const icon = wrapper.querySelector(`.${iconClass}`);
        if (icon) removeHidden(icon);
    };

    // ---------- Color Handling ----------
    const getCurrentColor = () => {
        const checked = qs("input[name='color']:checked");
        // fallback keeps JS stable even if color radios are missing
        return checked ? checked.value : "blue";
    };

    const setImgFromId = (imgEl, folder, id, colorScoped = false) => {
        if (!imgEl) return;

        if (colorScoped) {
            const color = getCurrentColor();
            imgEl.src = `${BASE_IMG_URL}/${folder}/${color}/${id}.png`;
        } else {
            imgEl.src = `${BASE_IMG_URL}/${folder}/${id}.png`;
        }

        removeHidden(imgEl);
    };

    // Update selector thumbnails in the accordion when color changes.
    // This updates <img src="../images/header/{color}/{name}.png"> and <img src="../images/button/{color}/{name}.png">
    const updateColorScopedThumbnails = (folder, newColor) => {
        // Looks for images in the selection area that include `/images/{folder}/...`
        // Example: ../images/header/red/something.png
        const imgs = qsa(`img[src*="/images/${folder}/"]`);
        imgs.forEach((img) => {
            const src = img.getAttribute("src") || "";

            // Replace the color segment after `/images/{folder}/`
            // ../images/header/blue/foo.png -> ../images/header/red/foo.png
            const pattern = new RegExp(`(/images/${folder}/)([^/]+)(/)`, "i");
            if (pattern.test(src)) {
                img.setAttribute("src", src.replace(pattern, `$1${newColor}$3`));
            }
        });
    };

    const refreshColorDependentPreview = () => {
        const color = getCurrentColor();

        // Update header preview if selected and not "noHeader"
        const headerChecked = qs("input[name='header']:checked");
        if (headerChecked && headerChecked.id && headerChecked.id !== "noHeader") {
            const headerImg = document.getElementById("page-header-img");
            setImgFromId(headerImg, "header", headerChecked.id, true);
        }

        // Update button preview if selected
        const buttonChecked = qs("input[name='button']:checked");
        if (buttonChecked && buttonChecked.id) {
            const buttonImg = document.getElementById("page-button-img");
            setImgFromId(buttonImg, "button", buttonChecked.id, true);
        }

        // Update the selector thumbnails in the accordions
        updateColorScopedThumbnails("header", color);
        updateColorScopedThumbnails("button", color);
    };

    const bindColorSelection = () => {
        const colorInputs = qsa("input[name='color']");
        if (!colorInputs.length) return;

        colorInputs.forEach((input) => {
            input.addEventListener("change", () => {
                if (input.checked) {
                    refreshColorDependentPreview();
                }
            });
        });

        // Initial paint
        refreshColorDependentPreview();
    };

    // ---------- Piece Selection (header/subheader) ----------
    const bindPieceSelection = ({
                                    groupName,
                                    imgId,
                                    noId,
                                    selectionClass,
                                    iconClass,
                                    folder,
                                    colorScoped = false
                                }) => {
        const imgEl = document.getElementById(imgId);
        const inputs = qsa(`input[name='${groupName}']`);
        if (!inputs.length) return;

        const apply = (input) => {
            if (!input) return;

            // Update image
            if (input.id === noId) {
                addHidden(imgEl);
            } else {
                setImgFromId(imgEl, folder, input.id, colorScoped);
            }

            // Update selection UI
            clearSelection(selectionClass, groupName, iconClass);

            if (input.id !== noId) {
                markSelected(input, selectionClass, groupName, iconClass);
            }
        };

        inputs.forEach((input) => {
            input.addEventListener("change", () => {
                if (input.checked) apply(input);
            });
        });

        // Initial state
        apply(qs(`input[name='${groupName}']:checked`));
    };

    // ---------- Button Selection ----------
    const bindButtonSelection = ({
                                     groupName,
                                     imgId,
                                     selectionClass,
                                     iconClass,
                                     folder,
                                     colorScoped = false
                                 }) => {
        const imgEl = document.getElementById(imgId);
        const inputs = qsa(`input[name='${groupName}']`);
        if (!inputs.length) return;

        const apply = (input) => {
            if (!input) return;

            setImgFromId(imgEl, folder, input.id, colorScoped);

            clearSelection(selectionClass, groupName, iconClass);
            markSelected(input, selectionClass, groupName, iconClass);
        };

        inputs.forEach((input) => {
            input.addEventListener("change", () => {
                if (input.checked) apply(input);
            });
        });

        // Initial state
        apply(qs(`input[name='${groupName}']:checked`));
    };

    // ---------- Background Selection ----------
    const bindBackgroundSelection = () => {
        const groupName = "background";
        const noId = "noBackground";
        const selectionClass = "piece-selected";
        const iconClass = "selected-check";

        const inputs = qsa(`input[name='${groupName}']`);
        if (!inputs.length) return;

        const apply = (input) => {
            if (!input) return;

            clearSelection(selectionClass, groupName, iconClass);

            if (input.id !== noId) {
                markSelected(input, selectionClass, groupName, iconClass);
            }
        };

        inputs.forEach((input) => {
            input.addEventListener("change", () => {
                if (input.checked) apply(input);
            });
        });

        // Initial state
        const checked = qs(`input[name='${groupName}']:checked`);
        if (checked) apply(checked);
    };

    // ---------- Video Embed ----------
    const initVideoEmbed = () => {
        const videoEmbedInput = document.getElementById("videoEmbed");
        const pageVideoImg = document.getElementById("page-video-img");
        const videoDiv = document.getElementById("videoDiv");
        const videoSource = document.getElementById("videoSource");
        const noVideoCheckbox = document.getElementById("noVideo");

        if (!videoEmbedInput || !noVideoCheckbox) return;

        const render = () => {
            const hasEmbed = videoEmbedInput.value.trim() !== "";

            if (noVideoCheckbox.checked) {
                addHidden(pageVideoImg);
                addHidden(videoDiv);
                if (videoSource) videoSource.src = "";
                videoEmbedInput.value = "";
                videoEmbedInput.setAttribute("disabled", "disabled");
                return;
            }

            videoEmbedInput.removeAttribute("disabled");

            if (hasEmbed) {
                addHidden(pageVideoImg);
                removeHidden(videoDiv);
                if (videoSource) videoSource.src = videoEmbedInput.value;
            } else {
                removeHidden(pageVideoImg);
                addHidden(videoDiv);
                if (videoSource) videoSource.src = "";
            }
        };

        videoEmbedInput.addEventListener("input", () => {
            if (videoEmbedInput.value.trim() !== "") {
                noVideoCheckbox.checked = false;
            }
            render();
        });

        noVideoCheckbox.addEventListener("change", render);

        // Initial state
        render();
    };

    // ---------- Disclaimer ----------
    const initDisclaimer = () => {
        const disclaimerTextarea = document.getElementById("disclaimer");
        const pageDisclaimerImg = document.getElementById("page-disclaimer-img");
        const disclaimerP = document.getElementById("disclaimerP");
        const disclaimerText = document.getElementById("disclaimerText");

        if (!disclaimerTextarea) return;

        const render = () => {
            const text = disclaimerTextarea.value;

            if (text.trim() !== "") {
                addHidden(pageDisclaimerImg);
                removeHidden(disclaimerP);
                if (disclaimerText) disclaimerText.textContent = text;
            } else {
                removeHidden(pageDisclaimerImg);
                addHidden(disclaimerP);
            }
        };

        disclaimerTextarea.addEventListener("input", render);

        // Initial state
        render();
    };

    // ---------- Form Details ----------
    const initFormSelection = () => {
        const formRadios = document.getElementById("formRadios");
        const formTemplate = document.getElementById("formTemplate");
        const selectedForm = document.getElementById("selectedForm");

        if (!formRadios) return;

        const fullNameDiv = document.getElementById("fullNameDiv");
        const splitNameDiv = document.getElementById("splitNameDiv");
        const phoneDiv = document.getElementById("phoneDiv");
        const humanDiv = document.getElementById("humanDiv");

        const applyFormData = (data) => {
            if (!data) return;

            if (formTemplate) addHidden(formTemplate);
            if (selectedForm) removeHidden(selectedForm);

            if (fullNameDiv && splitNameDiv) {
                if (String(data.formName) === "1") {
                    removeHidden(fullNameDiv);
                    addHidden(splitNameDiv);
                } else if (String(data.formName) === "2") {
                    addHidden(fullNameDiv);
                    removeHidden(splitNameDiv);
                }
            }

            if (phoneDiv) {
                data.formPhone ? removeHidden(phoneDiv) : addHidden(phoneDiv);
            }

            if (humanDiv) {
                data.formHuman ? removeHidden(humanDiv) : addHidden(humanDiv);
            }
        };

        const showFormDetails = (formID) => {
            if (!formID) return;

            fetch(`fetchForm.php?formID=${encodeURIComponent(formID)}`)
                .then((response) => response.json())
                .then(applyFormData)
                .catch((error) => console.error("Error fetching form details:", error));
        };

        formRadios.addEventListener("change", (event) => {
            if (event.target && event.target.name === "form") {
                showFormDetails(event.target.value);
            }
        });

        // Initial load
        const initiallySelectedForm = qs('input[name="form"]:checked');
        if (initiallySelectedForm) {
            showFormDetails(initiallySelectedForm.value);
        }
    };

    // ---------- Accordion Scroll ----------
    const initAccordionScroll = () => {
        const buttons = qsa(".accordionBtn");
        if (!buttons.length) return;

        buttons.forEach((btn) => {
            btn.addEventListener("click", function () {
                const targetId = this.getAttribute("data-bs-target");
                if (!targetId) return;

                const targetEl = qs(targetId);
                if (targetEl && !targetEl.classList.contains("show")) {
                    window.scrollTo({ top: 0, behavior: "smooth" });
                }
            });
        });
    };

    // ---------- Bindings ----------
    // Color changes affect Header + Button preview + thumbnails
    bindColorSelection();

    bindPieceSelection({
        groupName: "header",
        imgId: "page-header-img",
        noId: "noHeader",
        selectionClass: "piece-selected",
        iconClass: "selected-check",
        folder: "header",
        colorScoped: true
    });

    bindPieceSelection({
        groupName: "subheader",
        imgId: "page-subheader-img",
        noId: "noSubheader",
        selectionClass: "piece-selected",
        iconClass: "selected-check",
        folder: "subheader",
        colorScoped: false
    });

    bindButtonSelection({
        groupName: "button",
        imgId: "page-button-img",
        selectionClass: "piece-selected",
        iconClass: "selected-check",
        folder: "button",
        colorScoped: true
    });

    bindBackgroundSelection();
    initVideoEmbed();
    initDisclaimer();
    initFormSelection();
    initAccordionScroll();
});
