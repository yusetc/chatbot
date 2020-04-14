import axios from 'axios';
import env from '../environment/Environment'

/**
 * Axios basic configuration
 * Some general configuration can be added like timeout, headers, params etc. More details can be found on https://github.com/axios/axios
 * */
const config = {
    baseURL: env.apiBaseUrl,
    headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json'
    }
};

const httpClient = axios.create(config);

/**
 * Auth interceptors
 * @description Configuration related to AUTH token can be done in interceptors.
 * Currenlty it is just doing nothing but idea to to show the capability of axios and its interceptors
 * In future, interceptors can be created into separate files and consumed into multiple http clients
 * @param {*} config
 */
const authInterceptor = config => {
    /** add auth token */
    const token = localStorage.token;
    config.headers.Authorization = `Bearer ${token}`;
    return config;
};


const loggerInterceptor = config => {
    /** Add logging here */

    return config;
};

/** Adding the request interceptors */
httpClient.interceptors.request.use(authInterceptor);
httpClient.interceptors.request.use(loggerInterceptor);

/** Adding the response interceptors */
httpClient.interceptors.response.use(
    response => {
        return response;
    },
    error => {
        /** Do something with response error */
        return Promise.reject(error);
    }
);

export default httpClient
