import axios from 'axios';

export default function () {
    async function datatableApi(params) {
        try {
            const response = await axios.get("/admin/plan-orders", { params });

            return response.data;
        } catch (exception) {
            if (exception && exception.response) {
                throw exception.response.data;
            }
        }
    }

    async function approvedApi(order) {
        try {
            const response = await axios.post(`/admin/plan-orders/${order}/approved`);

            return response.data;
        } catch (exception) {
            if (exception && exception.response) {
                throw exception.response.data;
            }
        }
    }

    async function rejectedApi(order) {
        try {
            const response = await axios.post(`/admin/plan-orders/${order}/rejected`);

            return response.data;
        } catch (exception) {
            if (exception && exception.response) {
                throw exception.response.data;
            }
        }
    }

    return { datatableApi, approvedApi, rejectedApi };
}
