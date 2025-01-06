/**
 * nonce.js
 * A simple script to handle nonce integration and secure interactions
 */

document.addEventListener("DOMContentLoaded", () => {
    // Check if the nonce is set globally
    if (typeof cdwNonce === "undefined" || !cdwNonce.nonce) {
        return; // Nonce is not defined, exit silently
    }

    // Check if ajaxurl is defined
    if (typeof ajaxurl === "undefined" || ajaxurl === "") {
        return; // AJAX URL is not defined, exit silently
    }

    /**
     * Function to send a secure AJAX request
     * @param {string} action - The AJAX action name
     * @param {object} data - Additional data to send
     * @param {function} onSuccess - Callback for successful response
     * @param {function} onError - Callback for error response
     */
    function sendSecureRequest(action, data = {}, onSuccess = () => {}, onError = () => {}) {
        if (!action) {
            return; // Action is required, exit silently
        }

        // Ensure action and nonce are included
        data.action = action;
        data._ajax_nonce = cdwNonce.nonce;

        // Send the AJAX request
        jQuery.ajax({
            url: ajaxurl,
            type: "POST",
            data: data,
            dataType: "json", // Expect JSON response
            success: function (response) {
                if (response.success) {
                    onSuccess(response.data || response);
                } else {
                    onError(response);
                }
            },
            error: function (error) {
                onError(error);
            },
        });
    }

    // Example usage of the secure AJAX function
    const exampleButton = document.getElementById("exampleButton");
    if (exampleButton) {
        exampleButton.addEventListener("click", () => {
            sendSecureRequest(
                "cdw_secure_action",
                { exampleData: "Some Data" },
                (response) => {
                    // Handle successful response silently
                },
                (error) => {
                    // Handle error silently
                }
            );
        });
    }
});
