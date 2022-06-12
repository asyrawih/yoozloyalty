import axios from 'axios';

export default function () {
    async function index(params = {}) {
        try {
            const response = await axios.get('/staff/customers', { params });

            return response.data;
        } catch (exception) {
            if (exception && exception.response) {
                throw exception.response.data;
            }
        }
    }

    async function store(payload = {}) {
        try {
            const response = await axios.post('/staff/customers', payload, {
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

    async function show(identifier, params = {}) {
        try {
            const response = await axios.get(`/staff/customers/${identifier}`, { params });

            return response.data;
        } catch (exception) {
            if (exception && exception.response) {
                throw exception.response.data;
            }
        }
    }

    return {
        index,
        store,
        show,
    };
}
