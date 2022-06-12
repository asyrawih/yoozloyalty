import axios from 'axios';

export default function () {
    async function initializeApi(params) {
        try {
            const response = await axios.get("/admin/plans/initialize", { params });

            return response.data;
        } catch (exception) {
            if (exception && exception.response) {
                throw exception.response.data;
            }
        }
    }

    async function datatableApi(params) {
        try {
            const response = await axios.get("/admin/plans", { params });

            return response.data;
        } catch (exception) {
            if (exception && exception.response) {
                throw exception.response.data;
            }
        }
    }

    async function storeApi(payload) {
        try {
            const response = await axios.post("/admin/plans", payload);

            return response.data;
        } catch (exception) {
            if (exception && exception.response) {
                throw exception.response.data;
            }
        }
    }

    async function showApi(id) {
        try {
            const response = await axios.get(`/admin/plans/${id}`);

            return response.data;
        } catch (exception) {
            if (exception && exception.response) {
                throw exception.response.data;
            }
        }
    }

    async function updateApi(payload, id) {
        try {
            const response = await axios.put(`/admin/plans/${id}`, payload);

            return response.data;
        } catch (exception) {
            if (exception && exception.response) {
                throw exception.response.data;
            }
        }
    }

    async function destroyApi(id) {
        try {
            const response = await axios.delete(`/admin/plans/${id}`);

            return response.data;
        } catch (exception) {
            if (exception && exception.response) {
                throw exception.response.data;
            }
        }
    }

    async function massdeleteApi(payload) {
        try {
            const response = await axios.post('/admin/plans/massdelete', payload);

            return response.data;
        } catch (exception) {
            if (exception && exception.response) {
                throw exception.response.data;
            }
        }
    }

    return { initializeApi, datatableApi, storeApi, showApi, updateApi, destroyApi, massdeleteApi };
}
