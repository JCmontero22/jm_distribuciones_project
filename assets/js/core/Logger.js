const Logger = {
    enabled: true,

    debug(message, data = "") {
        if (!this.enabled) return;
        console.debug(`[DEBUG] ${message}`, data);
    },

    info(message, data = "") {
        if (!this.enabled) return;
        console.info(`[INFO] ${message}`, data);
    },

    warning(message, data = "") {
        if (!this.enabled) return;
        console.warn(`[WARN] ${message}`, data);
    },

    error(message, data = "") {
        console.error(`[ERROR] ${message}`, data);
    }
};

window.Logger = Logger;
