import axios from 'axios';

export default function () {
    async function datatableApi(params) {
        try {
            const response = await axios.get("/admin/admin-users", { params });

            return response.data;
        } catch (exception) {
            if (exception && exception.response) {
                throw exception.response.data;
            }
        }
    }

    async function storeApi(payload) {
        try {
            const response = await axios.post("/admin/admin-users", payload);

            return response.data;
        } catch (exception) {
            if (exception && exception.response) {
                throw exception.response.data;
            }
        }
    }

    async function showApi(id) {
        try {
            const response = await axios.get(`/admin/admin-users/${id}`);

            return response.data;
        } catch (exception) {
            if (exception && exception.response) {
                throw exception.response.data;
            }
        }
    }

    async function updateApi(payload, id) {
        try {
            const response = await axios.put(`/admin/admin-users/${id}`, payload);

            return response.data;
        } catch (exception) {
            if (exception && exception.response) {
                throw exception.response.data;
            }
        }
    }

    async function destroyApi(id) {
        try {
            const response = await axios.delete(`/admin/admin-users/${id}`);

            return response.data;
        } catch (exception) {
            if (exception && exception.response) {
                throw exception.response.data;
            }
        }
    }

    async function massdeleteApi(payload) {
        try {
            const response = await axios.post('/admin/admin-users/massdelete', payload);

            return response.data;
        } catch (exception) {
            if (exception && exception.response) {
                throw exception.response.data;
            }
        }
    }

    return { datatableApi, storeApi, showApi, updateApi, destroyApi, massdeleteApi };
}
