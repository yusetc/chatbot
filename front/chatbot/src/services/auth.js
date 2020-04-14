import httpClient from '../helpers/HttpClientLogin'
export default  {
    login(data) {
        return httpClient.post('/auth/login', data).then(res=>res.data);
    },
    register(data) {
        return httpClient.post('/auth/register', data).then(res=>res.data);
    },
}