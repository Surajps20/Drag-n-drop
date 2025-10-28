<?php
/**
 * Renders a self-contained countdown timer.
 * This function can be called anywhere in your HTML body.
 */
function display_countdown_timer() {
    
    // We use a HEREDOC (<<<HTML) to echo a large block of code
    // without worrying about quotes or escaping.
    echo <<<HTML

    <style>
        /* --- Countdown Timer --- */
        .timer-container {
            background-color: #fff8e1; /* A light, non-intrusive yellow */
            border: 1px solid #ffe5b4;
            color: #856404; /* Dark text for contrast */
            padding: 1rem 1.5rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            text-align: center;
            font-size: 1.1rem;
            font-weight: 500;
        }

        #countdown-timer {
            font-weight: 700;
            color: #d93025; /* Red for urgency */
            margin-left: 0.5rem;
        }
    </style>

    <div class="timer-container">
        This page will expire in: 
        <span id="countdown-timer">Loading...</span>
    </div>

    <script>
        // We wrap this in DOMContentLoaded to ensure it runs
        // after the page is loaded, no matter where this file is included.
        document.addEventListener('DOMContentLoaded', function() {
        
            // Set the target date (September 5, 2026)
            const countDownDate = new Date("September 5, 2026 00:00:00").getTime();
            const timerElement = document.getElementById("countdown-timer");

            // Check if the timer element actually exists on the page
            if (timerElement) {
                const countdownInterval = setInterval(function() {
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
                        clearInterval(countdownInterval);
                        timerElement.innerHTML = "EXPIRED";
                    }
                }, 1000);
            }
        });
    </script>

HTML;
}

?>