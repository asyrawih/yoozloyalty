import axios from 'axios';

export default function () {
    async function initializeApi() {
        try {
            const response = await axios.get("/user/plan-billings/initialize");

            return response.data;
        } catch (exception) {
            if (exception && exception.response) {
                throw exception.response.data;
            }
        }
    }

    async function statApi() {
        try {
            const response = await axios.get("/user/plan-billings/stat");

            return response.data;
        } catch (exception) {
            if (exception && exception.response) {
                throw exception.response.data;
            }
        }
    }

    async function plansApi() {
        try {
            const response = await axios.get("/user/plan-billings/plans");

            return response.data;
        } catch (exception) {
            if (exception && exception.response) {
                throw exception.response.data;
            }
        }
    }

    async function checkoutApi(payload) {
        try {
            const response = await axios.post("/user/plan-billings/checkout", payload);

            return response.data;
        } catch (exception) {
            if (exception && exception.response) {
                throw exception.response.data;
            }
        }
    }

    async function confirmApi(payload) {
        try {
            const response = await axios.post("/user/plan-billings/confirm", payload, {
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

    async function cancelApi(order) {
        try {
            const response = await axios.post(`/user/plan-billings/${order}/cancel`);

            return response.data;
        } catch (exception) {
            if (exception && exception.response) {
                throw exception.response.data;
            }
        }
    }

    return { initializeApi, statApi, plansApi, checkoutApi, confirmApi, cancelApi };
}
