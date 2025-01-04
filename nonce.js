/**
 * nonce.js
 * A simple script to handle nonce integration and secure interactions
 */

// Ensure the DOM is fully loaded before running any scripts
document.addEventListener("DOMContentLoaded", () => {
    // Check if the nonce is set globally
    if (typeof cdwNonce === "undefined" || cdwNonce === "") {
        return;
    }

    /**
     * Function to send a secure AJAX request
     * @param {string} action - The AJAX action name
     * @param {object} data - Additional data to send
     * @param {function} onSuccess - Callback for successful response
     * @param {function} onError - Callback for error response
     */
    function sendSecureRequest(action, data = {}, onSuccess = null, onError = null) {
        // Ensure action and nonce are included
        data.action = action;
        data._ajax_nonce = cdwNonce.nonce;

        // Send the AJAX request
        jQuery.ajax({
            url: ajaxurl, // WordPress provides this global variable
            type: "POST",
            data: data,
            success: function (response) {
                if (typeof onSuccess === "function") {
                    onSuccess(response);
                }
            },
            error: function (error) {
                if (typeof onError === "function") {
                    onError(error);
                }
            },
        });
    }

    // Example usage of the secure AJAX function
    document.getElementById("exampleButton")?.addEventListener("click", () => {
        sendSecureRequest(
            "cdw_secure_action",
            { exampleData: "Some Data" },
            (response) => alert("Request successful: " + response.message),
            (error) => alert("Request failed: " + error.statusText)
        );
    });
});
