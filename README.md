# Car Rental API

A RESTful API for managing car rentals built with Laravel, featuring authentication, payment processing, and comprehensive documentation.

## Features

- Authentication with Laravel Sanctum
- Car management (CRUD operations)
- Rental booking system
- Payment processing with Stripe
- Role-based authorization
- Filtering and pagination

## Tech Stack

- PHP 8.2
- Laravel 12.1
- PostgeSQL
- Stripe API
- Laravel Sanctum

## Installation

1. Clone the repository:
```bash
git clone https://github.com/Youcode-Classe-E-2024-2025/Khawla-Boukniter_CarRentalAPI.git
```

2. Install dependencies:
```bash
composer install
```

3. Configure environment:
```bash
cp .env.example .env
php artisan key:generate
```

4. Set up database and Stripe credentials in .env:
```
DB_CONNECTION=pgsql
DB_HOST=localhost
DB_PORT=5432
DB_DATABASE=database
DB_USERNAME=username
DB_PASSWORD=password

STRIPE_KEY=your_stripe_key
STRIPE_SECRET=your_stripe_secret
```

## API Testing

A Postman collection is included for testing all API endpoints.

To use it:
1. Import `CarRental.postman_collection.json` into Postman
2. Set up environment variables:
   - `BASE_URL`: Your API base URL (e.g. http://localhost:8000)
   - `TOKEN`: Authentication token after login

## API Endpoints

### Authentication
- POST `/api/register`
  ```json
  {
    "name": "string",
    "email": "string",
    "password": "string"
  }
  ```
- POST `/api/login`
  ```json
  {
    "email": "string",
    "password": "string"
  }
  ```
- POST `/api/logout` (Requires authentication)

### Cars
- GET `/api/cars` - List all cars
  - Query parameters:
    - make: Filter by manufacturer
    - model: Filter by model
    - year: Filter by year
    - min_price: Minimum price
    - max_price: Maximum price
- POST `/api/cars` (Requires authentication)
  ```json
  {
    "make": "string",
    "model": "string",
    "year": "integer",
    "price": "number"
  }
  ```
- GET `/api/cars/{id}` - Get specific car
- PUT `/api/cars/{id}` (Requires authentication)
- DELETE `/api/cars/{id}` (Requires authentication)

### Rentals
- GET `/api/rentals` - List all rentals
  - Query parameters:
    - user_id: Filter by user
    - car_id: Filter by car
    - start_date: Filter by start date
    - end_date: Filter by end date
    - status: Filter by status

### Payments
- GET `/api/payments` - List all payments
  - Query parameters:
    - rental_id: Filter by rental
    - min_amount: Minimum amount
    - max_amount: Maximum amount
    - payment_date: Filter by date
    - status: Filter by status

- POST `/api/payments` (Requires authentication)
  ```json
  {
    "rental_id": "integer",
    "amount": "number",
    "payment_date": "YYYY-MM-DD"
  }
  ```

- GET `/api/payments/{id}` - Get payment details
- PUT `/api/payments/{id}` (Requires authentication)
- DELETE `/api/payments/{id}` (Requires authentication)

## Response Examples

### Successful Registration
```json
{
    "user": {
        "name": "John Doe",
        "email": "john@example.com",
        "id": 1
    },
    "token": "1|mAYHOGPBpmfV1tNKLAxOJmSx7B37SnJShGbMf5kQ"
}
```

### Car Listing
```json
{
    "data": [
        {
            "id": 1,
            "make": "Toyota",
            "model": "Camry",
            "year": 2023,
            "price": "35000.00",
            "is_available": true
        }
    ],
    "current_page": 1,
    "per_page": 10
}
```

## Security

- Authentication using Laravel Sanctum
- Authorization using Laravel Policies
- Input validation
- CSRF protection
- Rate limiting on authentication endpoints

## Error Handling

The API returns appropriate HTTP status codes and error messages:

- 200: Success
- 201: Created
- 400: Bad Request
- 401: Unauthorized
- 403: Forbidden
- 404: Not Found
- 422: Validation Error
- 500: Server Error
