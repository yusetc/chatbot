import authService from '../services/auth'
import bankService from '../services/bank'
export default {
    name: 'ChatBox',
    data: () => ({
        message: '',
        messages: [],
        password: '',
        formError: '',
        passwordConfirm: '',
        name: '',
        email: '',
        def_currency: '',
        def_amount: '',
        isRegister: false,
        isLogin: false,
        username: '',
        commands: [':login', ':register', ':logout', ':balance', ':deposit', ':withdraw', ':exchange', ':currency', ':help']
    }),
    mounted() {
        this.showWelcome()
    },
    methods: {
        sendMessage(message, author) {
            if ( message === '') {
                return
            }
            this.messages.push({
                text: message,
                author: author
            })
            if (this.commands.indexOf(message.toLowerCase().split(' ')[0]) === -1 && author === 'client')
            {
                this.messages.push({
                    text: 'Invalid command',
                    author: 'server'
                })
            }
            if (message.toLowerCase().startsWith(':login')) {
                this.$modal.show('loginForm')
            }
            if (message.toLowerCase().startsWith(':register')) {
                this.formError = ''
                this.$modal.show('registerForm')
            }
            if (message.toLowerCase().startsWith(':logout')) {
                localStorage.token = ''
                this.sendMessage('User logged out', 'server')
            }
            if (message.toLowerCase().startsWith(':balance')) {
                this.processBalance(message)
            }
            if (message.toLowerCase().startsWith(':deposit')) {
                this.processDeposit(message)
            }
            if (message.toLowerCase().startsWith(':withdraw')) {
                this.processWithdraw(message)
            }
            if (message.toLowerCase().startsWith(':exchange')) {
                this.processExchange(message)
            }
            if (message.toLowerCase().startsWith(':currency')) {
                this.processCurrency(message)
            }
            if (message.toLowerCase().startsWith(':help')) {
                this.showHelp()
            }

            this.message = ''

            this.$nextTick(() => {
                this.$refs.chatbox.scrollTop = this.$refs.chatbox.scrollHeight
            })
        },
        isLoggedIn () {
          if (localStorage.token !== '') {
              return true
          }
          return false
        },
        processLogin() {
            authService.login({"username": this.username, "password": this.password})
                .then( (response) => {
                    localStorage.token = response.token
                    this.sendMessage('User logged in', 'server')
                })
                .catch((error) => {
                    if (error.response.data.code === 401) {
                        this.sendMessage(error.response.data.message, 'server');
                    }
                })

            this.$modal.hide('loginForm')
        },
        processRegister() {
            if (this.password === this.passwordConfirm) {
                authService.register({
                    "name": this.name,
                    "email": this.email,
                    "password": this.password,
                    "currency": this.def_currency ,
                    "deposit": this.def_amount
                })
                .then( (response) => {
                    this.$modal.hide('registerForm')
                    this.sendMessage('Welcome ' + response.result + ', Now you can log in', 'server')
                })
                .catch((error) => {
                    this.formError = error.response.data.detail
                })
                return
            }
            this.formError = 'Password confirmation not match'
        },
        processBalance(message) {
            let dataArray = message.split(' ');
            bankService.getBalance({
                params: {
                    currency: dataArray[1]
                }
            })
                .then( (response) => {
                    this.sendMessage('Balance: ' + response.balance.toFixed(2) + ' ' + response.currency, 'server');
                    }
                )
                .catch( (error) => {
                    if (error.response.status === 409) {
                        this.sendMessage('Invalid balance data request', 'server')
                        return
                    }
                    if (error.response.status === 401) {
                        this.sendMessage('Session expired, please, log in again', 'server')
                        return
                    }
                    this.sendMessage('Unknown Error', 'server')
                })
        },
        processDeposit(message) {
            let dataArray = message.split(' ');
            bankService.deposit(
                {
                        'amount': dataArray[1],
                        'currency': dataArray[2]
                })
                .then( (response) => {
                        this.sendMessage('Balance: ' + response.balance.toFixed(2) + ' ' + response.currency, 'server');
                    }
                )
                .catch( (error) => {
                    if (error.response.status === 409) {
                        this.sendMessage('Invalid deposit data', 'server')
                        return
                    }
                    if (error.response.status === 401) {
                        this.sendMessage('Session expired, please, log in again', 'server')
                        return
                    }
                    this.sendMessage('Unknown Error', 'server')
                })
        },
        processWithdraw(message) {
            let dataArray = message.split(' ');
            bankService.withdraw(
                {
                    'amount': dataArray[1],
                    'currency': dataArray[2]
                })
                .then( (response) => {
                        this.sendMessage('Balance: ' + response.balance.toFixed(2) + ' ' + response.currency, 'server');
                    }
                )
                .catch( (error) => {
                    if (error.response.status === 409) {
                        this.sendMessage('Invalid withdraw data', 'server')
                        return
                    }
                    if (error.response.status === 401) {
                        this.sendMessage('Session expired, please, log in again', 'server')
                        return
                    }
                    this.sendMessage('Unknown Error', 'server')
                })
        },
        processExchange(message) {
            let dataArray = message.split(' ');
            if (dataArray.length === 4) {
                bankService.exchange({
                    params: {
                        currencyFrom: dataArray[1],
                        currencyTo: dataArray[2],
                        amount: dataArray[3]
                    }
                })
                .then( (response) => {
                    this.sendMessage('Exchange result: ' + response.result.toFixed(2) + ' ' + dataArray[2], 'server')
                })
                .catch( (error) => {
                    if (error.response.status === 409) {
                        this.sendMessage('Invalid exchange data', 'server')
                        return
                    }
                    if (error.response.status === 401) {
                        this.sendMessage('Session expired, please, log in again', 'server')
                        return
                    }
                    this.sendMessage('Unknown Error', 'server')
                })
            }
        },
        processCurrency(message) {
            let dataArray = message.split(' ');
            if (dataArray.length === 1) {
                bankService.getCurrency()
                .then( (response) => {
                    this.sendMessage('Actual default currency: ' + response.result, 'server')
                })
                .catch( (error) => {
                    if (error.response.status === 401) {
                        this.sendMessage('Session expired, please, log in again', 'server')
                        return
                    }
                    this.sendMessage('Unknown Error', 'server')
                })
                return
            }

            bankService.setCurrency({
                'currency': dataArray[1]
            })
            .then( (response) => {
                this.sendMessage('New default currency: ' + response.result, 'server')
            })
            .catch( (error) => {
                if (error.response.status === 409) {
                    this.sendMessage('Invalid currency', 'server')
                    return
                }
                if (error.response.status === 401) {
                    this.sendMessage('Session expired, please, log in again', 'server')
                    return
                }
                this.sendMessage('Unknown Error', 'server')
            })


        },
        showWelcome() {
            this.sendMessage('Welcome to Bank Operations Chatbot!, type ":help" for commands', 'server')
        },
        showLoginHelp() {
            let loginHelp = 'You must register and log in to use chatbot operations<br>' +
            'For register you can use the command ":register" and for log in ":login"'
            this.sendMessage(loginHelp, 'server')
        },
        showHelp() {
            if (!this.isLoggedIn()) {
                this.showLoginHelp()
                return
            }
            let help = 'To get the account balance use ":balance [currency-code]"<br>' +
                'where "currency-code" is an optional parameter to obtain balance<br>' +
                'in an specific currency, Ex. :balance USD<br>' +
                'To get actual currency use ":currency" and to change it ":currency [currency-code]"<br>' +
                'To make a deposit use ":deposit (amount) [currency-code]" Ex. :deposit 10 EUR<br>' +
                'To make a withdraw use ":withdraw (amount) [currency-code]" Ex. :withdraw 10 USD<br>' +
                'To make money exchanges use ":exchange (currency-code-from) (currency-code-to) (amount)"<br>' +
                'Ex. :exchange USD EUR 10'

            this.sendMessage(help, 'server')
        }
    }
}