import axios from 'axios';

export default function () {
    async function index(params = {}) {
        try {
            const response = await axios.get('/staff/history', { params });

            return response.data;
        } catch (exception) {
            if (exception && exception.response) {
                throw exception.response.data;
            }
        }
    }

    return {
        index,
    };
}
