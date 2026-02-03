document.addEventListener("DOMContentLoaded", function () {
    // Check if user is logged in (we can check if the Login button exists, or set a JS variable in php)
    // Better: let PHP output a role or ID variable.
    // For now, let's look for the ".btn-logout" with text "Login" (which means guest) or lack of "Logout".

    // Actually, header.php logic:
    // If guest: "Login" button exists (class btn-logout with green background or just text)
    // If logged in: "Logout" button exists.

    const loginBtn = document.querySelector('a[href="login.php"]');
    const isGuest = loginBtn !== null;

    if (!isGuest) {
        // User is logged in, clear timer if any? Or just do nothing.
        localStorage.removeItem("guestTimerStart");
        return;
    }

    const LIMIT_SECONDS = 600;           // 10 minutes
    const LIMIT_MS = LIMIT_SECONDS * 1000;  // convert to milliseconds
    const LOCK_KEY = "guestTimerStart";
    const REGISTERED_KEY = "isRegistered"; // We might need this if we want to track local "registration" but real registration is DB based.
    // Actually user said "if the user finish the register then finish login now the user continue".
    // So real login clears the guest state.

    let startTime = localStorage.getItem(LOCK_KEY);

    if (!startTime) {
        startTime = Date.now();
        localStorage.setItem(LOCK_KEY, startTime);
    }

    function checkTimer() {
        const elapsed = Date.now() - parseInt(startTime);
        if (elapsed > LIMIT_MS) {
            showRegisterModal();
        }
    }

    // Check every second
    setInterval(checkTimer, 1000);
    checkTimer(); // initial check

    function showRegisterModal() {
        // Create modal if not exists
        let modal = document.getElementById("guest-modal");
        if (!modal) return;

        modal.style.display = "flex";
        document.body.style.overflow = "hidden"; // Disable scroll
    }
});
