import axios from 'axios';

export default function () {
    async function datatableApi() {
        try {
            const response = await axios.get("/admin/staff-roles");

            return response.data;
        } catch (exception) {
            if (exception && exception.response) {
                throw exception.response.data;
            }
        }
    }

    async function storeApi(payload) {
        try {
            const response = await axios.post('/admin/staff-roles', payload);

            return response.data;
        } catch (exception) {
            if (exception && exception.response) {
                throw exception.response.data;
            }
        }
    }

    async function showApi(id) {
        try {
            const response = await axios.get(`/admin/staff-roles/${id}`);

            return response.data;
        } catch (exception) {
            if (exception && exception.response) {
                throw exception.response.data;
            }
        }
    }

    async function updateApi(payload, id) {
        try {
            const response = await axios.put(`/admin/staff-roles/${id}`, payload);

            return response.data;
        } catch (exception) {
            if (exception && exception.response) {
                throw exception.response.data;
            }
        }
    }

    async function deleteApi(id) {
        try {
            const response = await axios.delete(`/admin/staff-roles/${id}`);

            return response.data;
        } catch (exception) {
            if (exception && exception.response) {
                throw exception.response.data;
            }
        }
    }

    return { datatableApi, storeApi, showApi, updateApi, deleteApi };
}
