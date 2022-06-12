import axios from 'axios';

export default function () {
    async function indexApi(params) {
        try {
            const response = await axios.get("/admin/bank-account-types", { params });

            return response.data;
        } catch (exception) {
            if (exception && exception.response) {
                throw exception.response.data;
            }
        }
    }

    async function storeApi(payload) {
        try {
            const response = await axios.post("/admin/bank-account-types", payload);

            return response.data;
        } catch (exception) {
            if (exception && exception.response) {
                throw exception.response.data;
            }
        }
    }

    async function showApi(id) {
        try {
            const response = await axios.get(`/admin/bank-account-types/${id}`);

            return response.data;
        } catch (exception) {
            if (exception && exception.response) {
                throw exception.response.data;
            }
        }
    }

    async function updateApi(id, payload) {
        try {
            const response = await axios.put(`/admin/bank-account-types/${id}`, payload);

            return response.data;
        } catch (exception) {
            if (exception && exception.response) {
                throw exception.response.data;
            }
        }
    }

    async function deleteApi(id) {
        try {
            const response = await axios.delete(`/admin/bank-account-types/${id}`);

            return response.data;
        } catch (exception) {
            if (exception && exception.response) {
                throw exception.response.data;
            }
        }
    }

    return { indexApi, storeApi, showApi, updateApi, deleteApi };
}
