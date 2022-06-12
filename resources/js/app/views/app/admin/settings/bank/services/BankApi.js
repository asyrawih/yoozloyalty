import axios from 'axios';

export default function () {
    async function datatableApi(params) {
        try {
            const response = await axios.get('/admin/bank-accounts', { params });

            return response.data;
        } catch (exception) {
            if (exception && exception.response) {
                throw exception.response.data;
            }
        }
    }

    async function storeApi(payload) {
        try {
            const response = await axios.post('/admin/bank-accounts', payload);

            return response.data;
        } catch (exception) {
            if (exception && exception.response) {
                throw exception.response.data;
            }
        }
    }

    async function showApi(id) {
        try {
            const response = await axios.get(`/admin/bank-accounts/${id}`);

            return response.data;
        } catch (exception) {
            if (exception && exception.response) {
                throw exception.response.data;
            }
        }
    }

    async function updateApi(id, payload) {
        try {
            const response = await axios.put(`/admin/bank-accounts/${id}`, payload);

            return response.data;
        } catch (exception) {
            if (exception && exception.response) {
                throw exception.response.data;
            }
        }
    }

    async function deleteApi(id) {
        try {
            const response = await axios.delete(`/admin/bank-accounts/${id}`);

            return response.data;
        } catch (exception) {
            if (exception && exception.response) {
                throw exception.response.data;
            }
        }
    }

    async function getAccountTypesApi() {
        try {
            const response = await axios.get('/admin/bank-account-types/active');

            return response.data;
        } catch (exception) {
            if (exception && exception.response) {
                throw exception.response.data;
            }
        }
    }

    return { datatableApi, storeApi, showApi, updateApi, deleteApi, getAccountTypesApi };
}
