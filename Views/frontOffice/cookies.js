// Fonction pour définir un cookie
function setCookie(name, value, days) {
    const date = new Date();
    date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
    const expires = "expires=" + date.toUTCString();
    document.cookie = `${name}=${value}; ${expires}; path=/; SameSite=Lax`;
}

// Fonction pour obtenir la valeur d'un cookie
function getCookie(name) {
    const cookies = document.cookie.split("; ");
    for (let i = 0; i < cookies.length; i++) {
        const cookie = cookies[i].split("=");
        if (cookie[0] === name) return cookie[1];
    }
    return null;
}

// Fonction pour supprimer un cookie
function deleteCookie(name) {
    document.cookie = `${name}=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;`;
}

// Affiche la bannière des cookies si le consentement n'existe pas
if (!getCookie("cookieConsent")) {
    document.getElementById("cookie-banner").style.display = "block";
}

// Gère l'acceptation des cookies
document.getElementById("accept-cookies").addEventListener("click", function () {
    setCookie("cookieConsent", "accepted", 30); // Consentement accepté pour 30 jours
    document.getElementById("cookie-banner").style.display = "none";
    alert("Merci d'avoir accepté les cookies !");
});

// Gère le refus des cookies
document.getElementById("reject-cookies").addEventListener("click", function () {
    setCookie("cookieConsent", "rejected", 30); // Consentement refusé pour 30 jours
    document.getElementById("cookie-banner").style.display = "none";
    alert("Vous avez refusé les cookies.");
});

// Réinitialise les cookies
document.getElementById("reset-cookies").addEventListener("click", function () {
    deleteCookie("cookieConsent");
    alert("Les cookies ont été réinitialisés.");
    location.reload(); // Recharge la page pour réafficher la bannière
});
