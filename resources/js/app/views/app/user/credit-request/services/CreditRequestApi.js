import axios from 'axios';

export default function () {
    async function campaignsApi(params = {}) {
        try {
            const response = await axios.get("/user/credit-requests/campaigns", { params });

            return response.data;
        } catch (exception) {
            if (exception && exception.response) {
                throw exception.response.data;
            }
        }
    }

    async function datatableApi(params = {}) {
        try {
            const response = await axios.get("/user/credit-requests", { params });

            return response.data;
        } catch (exception) {
            if (exception && exception.response) {
                throw exception.response.data;
            }
        }
    }

    async function storeApi(payload = {}) {
        try {
            const response = await axios.post("/user/credit-requests", payload);

            return response.data;
        } catch (exception) {
            if (exception && exception.response) {
                throw exception.response.data;
            }
        }
    }

    async function updateApi(id = null, payload = {}) {
        try {
            const response = await axios.put(`/user/credit-requests/${id}`, payload);

            return response.data;
        } catch (exception) {
            if (exception && exception.response) {
                throw exception.response.data;
            }
        }
    }

    async function deleteApi(id = null) {
        try {
            const response = await axios.delete(`/user/credit-requests/${id}`);

            return response.data;
        } catch (exception) {
            if (exception && exception.response) {
                throw exception.response.data;
            }
        }
    }

    async function importApi(payload = {}) {
        try {
            const response = await axios.post(`/user/credit-requests/import`, payload, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            });

            return response.data;
        } catch (exception) {
            if (exception && exception.response) {
                throw exception.response.data;
            }
        }
    }

    async function bulkActionsApi(payload = {}) {
        try {
            const response = await axios.post(`/user/credit-requests/bulk-actions`, payload);

            return response.data;
        } catch (exception) {
            if (exception && exception.response) {
                throw exception.response.data;
            }
        }
    }

    return {
        campaignsApi,
        datatableApi,
        storeApi,
        updateApi,
        deleteApi,
        importApi,
        bulkActionsApi
    };
}
