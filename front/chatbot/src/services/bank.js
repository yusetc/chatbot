import httpClient from '../helpers/HttpClientLogin'
export default  {
    getBalance(queryParams) {
        return httpClient.get('/bank/balance', queryParams).then(res=>res.data);
    },
    setBalance(data) {
        return httpClient.post('/bank/balance', data).then(res=>res.data);
    },
    deposit(data) {
        return httpClient.post('/bank/deposit', data).then(res=>res.data);
    },
    withdraw(data) {
        return httpClient.post('/bank/withdraw', data).then(res=>res.data);
    },
    setCurrency(data) {
        return httpClient.post('/bank/currency', data).then(res=>res.data);
    },
    getCurrency() {
        return httpClient.get('/bank/currency').then(res=>res.data);
    },
    exchange(queryParams) {
        return httpClient.get('/bank/exchange', queryParams).then(res=>res.data);
    },
}