    (function () {
        const key = "dashboardReloaded";
        const shouldReload = sessionStorage.getItem(key) === null;

        if (shouldReload) {
            sessionStorage.setItem(key, "true");
            // Forzar recarga sin caché
            window.location.replace(window.location.href);
        } else {
            // Ya se recargó una vez, no más recargas
        }
    })();