import axios from 'axios';

export default function () {
    async function initializeApi(payload) {
        try {
            const response = await axios.post("/user/setting/smtp-services/initialize", payload);

            return response.data;
        } catch (exception) {
            if (exception && exception.response) {
                throw exception.response.data;
            }
        }
    }

    async function datatableApi(params) {
        try {
            const response = await axios.get("/user/setting/smtp-services", { params });

            return response.data;
        } catch (exception) {
            if (exception && exception.response) {
                throw exception.response.data;
            }
        }
    }

    async function storeApi(payload) {
        try {
            const response = await axios.post("/user/setting/smtp-services", payload);

            return response.data;
        } catch (exception) {
            if (exception && exception.response) {
                throw exception.response.data;
            }
        }
    }

    async function showApi(id) {
        try {
            const response = await axios.get(`/user/setting/smtp-services/${id}`);

            return response.data;
        } catch (exception) {
            if (exception && exception.response) {
                throw exception.response.data;
            }
        }
    }

    async function updateApi(payload, id) {
        try {
            const response = await axios.put(`/user/setting/smtp-services/${id}`, payload);

            return response.data;
        } catch (exception) {
            if (exception && exception.response) {
                throw exception.response.data;
            }
        }
    }

    async function deleteApi(id) {
        try {
            const response = await axios.delete(`/user/setting/smtp-services/${id}`);

            return response.data;
        } catch (exception) {
            if (exception && exception.response) {
                throw exception.response.data;
            }
        }
    }

    async function websitesApi(id) {
        try {
            const response = await axios.get(`/user/setting/smtp-services/websites/${id}`);

            return response.data;
        } catch (exception) {
            if (exception && exception.response) {
                throw exception.response.data;
            }
        }
    }

    return {
        initializeApi,
        datatableApi,
        storeApi,
        showApi,
        updateApi,
        deleteApi,
        websitesApi,
    };
}
