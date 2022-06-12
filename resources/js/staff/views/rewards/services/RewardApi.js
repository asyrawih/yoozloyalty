import axios from 'axios';
import _ from 'lodash';

export default function () {
    async function getRewardListApi(params) {
        try {
            const response = await axios.get('/staff/rewards', { params });

            return response.data;
        } catch (exception) {
            if (exception && exception.response) {
                throw exception.response.data;
            }
        }
    }

    async function getSegementListApi(params) {
        try {
            const response = await axios.get('/staff/segments', { params });

            return _.toPairs(response.data);
        } catch (exception) {
            if (exception && exception.response) {
                throw exception.response.data;
            }
        }
    }

    async function getActiveMerchantCodeApi(params) {
        try {
            const response = await axios.get('/staff/rewards/merchant/active-code', { params });

            return response.data;
        } catch (exception) {
            if (exception && exception.response) {
                throw exception.response.data;
            }
        }
    }

    async function requestActiveMerchantCode(payload) {
        try {
            const response = await axios.post('/staff/rewards/merchant/generate-code', payload);

            return response.data;
        } catch (exception) {
            if (exception && exception.response) {
                throw exception.response.data;
            }
        }
    }

    async function validateLinkTokenApi(payload) {
        try {
            const response = await axios.post('/staff/rewards/validate-link-token', payload);

            return response.data;
        } catch (exception) {
            if (exception && exception.response) {
                throw exception.response.data;
            }
        }
    }

    async function requestOtpCodeApi(params) {
        try {
            const response = await axios.get('/staff/auth/otp', { params });

            return response.data;
        } catch (exception) {
            if (exception && exception.response) {
                throw exception.response.data;
            }
        }
    }

    async function verifyOtpCodeApi(payload) {
        try {
            const response = await axios.post('/staff/auth/otp', payload);

            return response.data;
        } catch (exception) {
            if (exception && exception.response) {
                throw exception.response.data;
            }
        }
    }

    async function creditCustomerApi(payload) {
        try {
            const response = await axios.post('/staff/rewards/push/redemption', payload);

            return response.data;
        } catch (exception) {
            if (exception && exception.response) {
                throw exception.response.data;
            }
        }
    }

    async function creditCustomer2Api(payload) {
        try {
            const response = await axios.post('/staff/rewards/customer/credit', payload);

            return response.data;
        } catch (exception) {
            if (exception && exception.response) {
                throw exception.response.data;
            }
        }
    }

    return {
        getRewardListApi,
        getSegementListApi,
        getActiveMerchantCodeApi,
        requestActiveMerchantCode,
        validateLinkTokenApi,
        requestOtpCodeApi,
        verifyOtpCodeApi,
        creditCustomerApi,
        creditCustomer2Api
    };
}
