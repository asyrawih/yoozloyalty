import axios from 'axios';

export default function () {
    async function indexApi(params) {
        try {
            const response = await axios.get('/user/setting/legals', { params });

            return response.data;
        } catch (exception) {
            if (exception && exception.response) {
                throw exception.response.data;
            }
        }
    }

    async function updateApi(payload) {
        try {
            const response = await axios.post('/user/setting/legals', payload);

            return response.data;
        } catch (exception) {
            if (exception && exception.response) {
                throw exception.response.data;
            }
        }
    }

    return { indexApi, updateApi };
};
