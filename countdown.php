<?php
/**
 * Renders a self-contained, Bootstrap-styled countdown timer.
 * This function can be called anywhere in your HTML body.
 */
function display_countdown_timer() {
    
    // --- 1. SET YOUR TARGET DATE HERE ---
    // Use the format "YYYY-MM-DD HH:MM:SS"
    // I've kept your original date of "September 5, 2026"
    $target_date = "2026-09-05 00:00:00";

    // --- 2. HTML Structure (Using Bootstrap Alert classes) ---
    // The <style> block is gone. We now use Bootstrap's "alert" component.
    echo '
    <div class="alert alert-warning text-center mb-4" role="alert">
        <strong>This page will expire in:</strong>
        
        <span id="countdown-timer" class="fw-bold text-danger ms-2">Loading...</span>
    </div>
    ';

    // --- 3. JavaScript (No changes needed, but now cleaner) ---
    // We inject the $target_date variable into the script.
    echo '
    <script>
        // Kept your DOMContentLoaded wrapper, which is good practice!
        document.addEventListener("DOMContentLoaded", function() {
            
            // We use the PHP variable to set the date
            const countDownDate = new Date("' . $target_date . '").getTime();
            const timerElement = document.getElementById("countdown-timer");

            if (!timerElement) {
                console.error("Countdown timer element not found.");
                return; // Stop if the element doesn\'t exist
            }

            const interval = setInterval(function() {
                const now = new Date().getTime();
                const distance = countDownDate - now;

                // Time calculations
                const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                // Display the result
                timerElement.innerHTML = days + "d " + hours + "h " + minutes + "m " + seconds + "s ";

                // If the countdown is over, show "EXPIRED"
                if (distance < 0) {
                    clearInterval(interval);
                    timerElement.innerHTML = "EXPIRED";
                    
                    // Bonus: Change the alert box from yellow (warning) to red (danger)
                    const alertBox = timerElement.closest(".alert");
                    if (alertBox) {
                        alertBox.classList.remove("alert-warning");
                        alertBox.classList.add("alert-danger");
                    }
                }
            }, 1000);
        });
    </script>
    ';
}
?>