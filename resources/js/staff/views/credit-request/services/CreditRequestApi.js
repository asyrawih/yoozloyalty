import axios from "axios";

export default function () {
    async function index(params) {
        try {
            const response = await axios.get('/staff/credit-request', { params });

            return response.data;
        } catch (exception) {
            if (exception && exception.response) {
                throw exception.response.data;
            }
        }
    }

    async function update(id, payload) {
        try {
            const response = await axios.put(`/staff/credit-request/${id}`, payload);

            return response.data;
        } catch (exception) {
            if (exception && exception.response) {
                throw exception.response.data;
            }
        }
    }

    return {
        index,
        update,
    };
}
