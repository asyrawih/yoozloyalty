import axios from 'axios';

export default function () {
    async function validateLinkTokenApi(payload) {
        try {
            const response = await axios.post('/staff/points/validate-link-token', payload);

            return response.data;
        } catch (exception) {
            if (exception && exception.response) {
                throw exception.response.data;
            }
        }
    }

    async function getSegmentsApi(params) {
        try {
            const response = await axios.get('/staff/segments', { params });

            return response.data;
        } catch (exception) {
            if (exception && exception.response) {
                throw exception.response.data;
            }
        }
    }

    async function creditCustomerByTokenApi(payload) {
        try {
            const response = await axios.post('/staff/points/push/credit', payload);

            return response.data;
        } catch (exception) {
            if (exception && exception.response) {
                throw exception.response.data;
            }
        }
    }

    async function creditCustomerApi(payload) {
        try {
            const response = await axios.post('/staff/points/customer/credit', payload);

            return response.data;
        } catch (exception) {
            if (exception && exception.response) {
                throw exception.response.data;
            }
        }
    }

    return {
        validateLinkTokenApi,
        getSegmentsApi,
        creditCustomerByTokenApi,
        creditCustomerApi
    };
}
