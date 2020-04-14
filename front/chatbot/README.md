# Frontend Vuejs SPA for Bank Operations Chatbot

## Project setup
In order to install the project dependencies you need an update nodejs installation with npm installer tool.
After check the requirements just run:
```
npm install
```
### Configuration
- Edit .env file to configure API endpoint to match with the Project Rest API.

### Compiles and hot-reloads for development
```
npm run serve
```

### Compiles and minifies for production
```
npm run build
```

### Lints and fixes files
```
npm run lint
```

### Commands documentation
#### After configure the app and run it, you can operate with the app using these commands:


**:help**

Shows commands documentation, if the user is not logged in then only shows log in and register help.

**:register**

Shows modal window for user registration.

**:login**

Shows modal window for user log in.

**:logout**

Log out the user.

**:balance [currency-code]**

Shows actual account balance, if [country-code] is specified then shows the balance converted to this currency

**:deposit (amount) [currency-code]**

Increment the account balance using the "amount" value, if [currency-code] is specified the is used as base currency to increment the balance.
 
**:withdraw (amount) [currency-code]**

Decrement the account balance using the "amount" value, if [currency-code] is specified the is used as base currency to increment the balance.

**:currency [currency-code]**

Without the [currency-code] shows actual currency for the user balance and set a new currency if this parameter is specified

**:exchange (currency-code-from) (currency-code-to) (amount)**

This is an exchange tool for convert any money amount from one currency to another, using updated rates from FixerIO API.  

