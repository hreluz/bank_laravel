# Bank API Documentation

## Overview

This API provides functionalities for basic banking operations, such as depositing and withdrawing funds, managing bank accounts, and generating transaction reports. The API is built using Laravel and adheres to RESTful principles.

## Authentication

The API uses Laravel Sanctum for authentication. To access protected routes, users must be authenticated by providing a valid token.

### Authentication Routes

- **POST `/v1/login`**  
  Logs in a user and returns an authentication token.

- **POST `/v1/register`**  
  Registers a new user.

- **GET `/v1/authenticated`**  
  Confirms if the user is authenticated.

## Routes

### 1. **Companies**

- **POST `/v1/companies`**  
  Creates a new company.  
  **Requires Authentication:** Yes

### 2. **Bank Accounts**

- **POST `/v1/bank_accounts`**  
  Creates a new bank account for a client or company.  
  **Requires Authentication:** Yes

- **GET `/v1/bank_accounts/{bank_account}`**  
  Retrieves the balance of the specified bank account.  
  **Requires Authentication:** Yes

### 3. **Transactions**

- **POST `/v1/transactions/{bank_account}`**  
  Creates a new transaction (deposit or withdrawal) for the specified bank account.
  It requires to send the `amount`, and the `type` `('ADD' or 'SUBTRACT')`

  **Requires Authentication:** Yes

- **GET `/v1/transactions/recent/{bank_account}`**  
  Lists the most recent transactions for the specified bank account.  
  **Requires Authentication:** Yes

### 4. **Reports**

- **GET `/v1/reports/clients-filtered-transactions-by-month`**  
  Retrieves a report of client transactions filtered by month.  
  **Requires Authentication:** Yes

- **GET `/v1/reports/transactions-filtered-by-10k-and-city`**  
  Retrieves a report of transactions filtered by amounts greater than 10k and city.  
  **Requires Authentication:** Yes

- **GET `/v1/reports/bank_accounts.balance.annual/{bank_account}`**  
  Retrieves the annual balance report for the specified bank account.  
  **Requires Authentication:** Yes

## Error Handling

The API returns standard HTTP status codes to indicate the success or failure of an API request. For example:

- **200 OK**: The request was successful.
- **401 Unauthorized**: The request requires user authentication.
- **404 Not Found**: The requested resource could not be found.
- **500 Internal Server Error**: An error occurred on the server.

## Example Requests

### Register a New User

```bash
curl -X POST 'https://your-api-url/api/v1/register' \
-H 'Content-Type: application/json' \
-d '{
  "name": "John Doe",
  "email": "john.doe@example.com",
  "password": "password"
}'
```

### Authenticate a User

```bash
curl -X POST 'https://your-api-url/api/v1/login' \
-H 'Content-Type: application/json' \
-d '{
  "email": "john.doe@example.com",
  "password": "password"
}'
```

### Create a New Bank Account

For a Client Bank Account

```bash
curl -X POST 'https://your-api-url/api/v1/bank_accounts' \
-H 'Authorization: Bearer your-token' \
-H 'Content-Type: application/json' \
-d '{
  "account_name": "Main Account",
  "owner_id"": 1,
  "initial_balance": 1000,
  "city": "New York"
}'
```
For a Company Bank Account

```bash
curl -X POST 'https://your-api-url/api/v1/bank_accounts' \
-H 'Authorization: Bearer your-token' \
-H 'Content-Type: application/json' \
-d '{
  "company_id": 1,
  "account_name": "Main Account",
  "initial_balance": 1000,
  "city": "New York"
}'
```

Get Account Balance

```bash
curl -X GET 'https://your-api-url/api/v1/bank_accounts/{bank_account}' \
-H 'Authorization: Bearer your-token' \
-H 'Content-Type: application/json'
```

Create a New Transaction (Deposit/Withdrawal) (types: ADD, SUBTRACT)
```bash
curl -X POST 'https://your-api-url/api/v1/transactions/{bank_account}' \
-H 'Authorization: Bearer your-token' \
-H 'Content-Type: application/json' \
-d '{
"type": "ADD",
"amount": 500,
"city": "London"
}'
```

List Recent Transactions

```bash
curl -X GET 'https://your-api-url/api/v1/transactions/recent/{bank_account}' \
-H 'Authorization: Bearer your-token' \
-H 'Content-Type: application/json'
```

Get Clients Filtered Transactions by Month Report

```bash
curl -X GET 'https://your-api-url/api/v1/reports/clients-filtered-transactions-by-month' \
-H 'Authorization: Bearer your-token' \
-H 'Content-Type: application/json'
```

Get Transactions Filtered by 10k and City Report
```bash
curl -X GET 'https://your-api-url/api/v1/reports/transactions-filtered-by-10k-and-city' \
-H 'Authorization: Bearer your-token' \
-H 'Content-Type: application/json'
```

Get Annual Balance Report for Bank Account
```bash
curl -X GET 'https://your-api-url/api/v1/reports/bank_accounts.balance.annual/{bank_account}' \
-H 'Authorization: Bearer your-token' \
-H 'Content-Type: application/json'
```

## Conclusion

This API provides a set of endpoints to manage bank accounts, perform transactions, and generate reports. Ensure that all requests are properly authenticated where required. For further assistance, please refer to the full API documentation or contact support.
