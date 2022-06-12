import axios from 'axios';

export default function () {
    async function initializeApi() {
        try {
            const response = await axios.get("/admin/payment");

            return response.data;
        } catch (exception) {
            if (exception && exception.response) {
                throw exception.response.data;
            }
        }
    }

    async function updateApi(payload) {
        try {
            const response = await axios.post("/admin/payment", payload);

            return response.data;
        } catch (exception) {
            if (exception && exception.response) {
                throw exception.response.data;
            }
        }
    }

    return { initializeApi, updateApi };
}
