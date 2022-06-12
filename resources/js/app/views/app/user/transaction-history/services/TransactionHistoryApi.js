import axios from 'axios';

export default function () {
    async function campaignsApi(params = {}) {
        try {
            const response = await axios.get("/user/transaction-histories/campaigns", { params });

            return response.data;
        } catch (exception) {
            if (exception && exception.response) {
                throw exception.response.data;
            }
        }
    }

    async function datatableApi(params = {}) {
        try {
            const response = await axios.get("/user/transaction-histories", { params });

            return response.data;
        } catch (exception) {
            if (exception && exception.response) {
                throw exception.response.data;
            }
        }
    }

    async function importApi(payload = {}) {
        try {
            const response = await axios.post(`/user/transaction-histories/import`, payload, {
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

    return {
        campaignsApi,
        datatableApi,
        importApi,
    };
}
