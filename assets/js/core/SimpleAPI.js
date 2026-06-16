class SimpleAPI {
    constructor(endpoint) {
        this.endpoint = endpoint;
        this.url = `${CONFIG.BASE_URL}${endpoint}`;
    }

    async get(params = {}) {
        return this._request("GET", params);
    }

    async post(payload = {}) {
        return this._request("POST", payload);
    }

    _request(method, payload) {
        const isFormData = payload instanceof FormData;

        return new Promise((resolve, reject) => {
            $.ajax({
                url: this.url,
                method,
                data: payload,
                dataType: "json",
                processData: method === "GET" ? true : !isFormData,
                contentType: method === "GET" ? undefined : (isFormData ? false : "application/x-www-form-urlencoded; charset=UTF-8"),
                timeout: 30000,
                success: (response) => {
                    resolve(response);
                },
                error: (xhr, status, error) => {
                    const message = xhr?.responseJSON?.message || error || "Error de red";
                    reject({ status: xhr?.status || 0, message, error, responseText: xhr?.responseText || "" });
                }
            });
        });
    }
}

window.SimpleAPI = SimpleAPI;
