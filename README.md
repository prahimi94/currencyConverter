# Currency Conversion Service
This project is a service that provides conversions of amounts in one currency to another currency using real-time conversion rates as provided by [Swop.cx](https://swop.cx/). The service is programmed with PHP (Laravel framework) including caching, validation and testing for better performance. It is built with Docker for consistent deployment from development, to staging, to production. Additional security measures, including CSRF and CSP, have been implemented.

## Features
- Currency conversion based on real-time exchange rates from [Swop.cx](https://swop.cx/)
- Input validation and error handling
- Caching for improved performance
- CSRF and CSP security protections
- Dockerized application for easy deployment
- Vue.js user interface for smooth interactions with the currency conversion service
- Format output using [Web i18n](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Intl/NumberFormat) framework

## Technologies Used
- PHP (Framework of choice)
- Vue.js
- Docker (Alpine-based images)
- CSRF & CSP for security
- Redis (for caching)

## Setup and Installation

### Prerequisites
- PHP (version 8.x or above)
- Composer
- Node.js (for Vue.js)
- Docker

### 1. Clone the Repository
```bash
git clone https://github.com/prahimi94/currencyConverter
cd currencyConverter
```

# 2. Install PHP Dependencies
Run the following command to install PHP dependencies:
```bash
composer install
```

# 3. Install Vue.js Dependencies
Run the following command to install Vue.js dependencies:
```bash
npm install
```

# 4. Configure Environment
Make a .env as a copy of .env.example file. Make sure to configure the .env file with the necessary API keys and settings, including:

Swop.cx API key

Redis configuration

# 5. Run the Application with Docker
You can run the application in a Docker container for consistent deployment:
```bash
docker-compose up
```
This will start both the backend(Laravel) and frontend services(Vue.js). The project will be available at http://localhost:8083.

# 6. Access the Currency Conversion API
To interact with the API, you can leverage both Rest and Graphql Apis. 

## Rest api's example:
Currenies: **Get** request to **"/api/rest/currencies"** endpoint.

Convert: **Post** request to **"/api/rest/convert"** endpoint.
Request Format:
```bash
{
    "from": "CHF",
    "to": "UYU",
    "amount": 20
}
```
Example Request:
```bash
curl --location 'http://127.0.0.1:8083/api/rest/convert' \
--header 'Accept: application/json' \
--header 'X-CSRF-TOKEN: LlAD3q15iIPw3NvoBrhLPmo7yVor6wufMjSEFHo3' \
--header 'Content-Type: application/json' \
--data '{
    "from": "CHF",
    "to": "UYU",
    "amount": 20
}'
```
Example Request:
```bash
{
    "success": true,
    "data": 1014.19,
    "message": null
}
```

## Graphqls api's example:
Currenies: **Get** request to **"/api/graphql/currencies"** endpoint.

Convert: **Post** request to **"/api/graphql/convert"** endpoint.

**Request and response format are the same with Rest apis.**

# 7. Security
CSRF protection is enabled for API requests.

CSP headers are set to protect against cross-site scripting (XSS) attacks.

# 8. Testing
Run tests to ensure the correctness of the service:
```bash
php artisan test
```
