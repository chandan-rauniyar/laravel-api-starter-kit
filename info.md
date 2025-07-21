# Laravel E-Commerce API - Authentication System

A complete Laravel API authentication system with user registration, login, email verification, password reset, and OTP functionality.

## 🚀 Features

- ✅ User Registration with Email Verification
- ✅ User Login with JWT-like Tokens (Laravel Sanctum)
- ✅ Email Verification via OTP
- ✅ Password Reset with Old Password
- ✅ Forgot Password with OTP
- ✅ Secure Token-based Authentication
- ✅ Professional Email Templates
- ✅ Input Validation & Security
- ✅ Rate Limiting (Built-in Laravel)

## 📋 Prerequisites

- PHP >= 8.1
- Composer
- MySQL/PostgreSQL
- Node.js & NPM (for frontend assets)

## 🛠️ Installation & Setup

### 1. Clone the Repository
```bash
git clone <your-repository-url>
cd E-Commerse_API
```

### 2. Install Dependencies
```bash
composer install
npm install
```

### 3. Environment Setup
Copy the `.env.example` file and configure your environment:
```bash
cp .env.example .env
```

### 4. Generate Application Key
```bash
php artisan key:generate
```

### 5. Configure Database
Update your `.env` file with database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 6. Configure Mail Settings
Add these settings to your `.env` file:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your_email@gmail.com
MAIL_FROM_NAME="Your App Name"
```

**Note:** For Gmail, you may need to:
1. Enable 2-Step Verification
2. Generate an App Password
3. Use the App Password instead of your regular password

### 7. Run Migrations
```bash
php artisan migrate
```

### 8. Clear Caches
```bash
php artisan config:clear
php artisan route:clear
php artisan cache:clear
```

### 9. Start Development Server
```bash
php artisan serve
```

Your API will be available at: `http://localhost:8000`

## 📚 API Documentation

### Base URL
```
http://localhost:8000/api
```

### Authentication Headers
For protected routes, include:
```
Authorization: Bearer your_access_token
Accept: application/json
Content-Type: application/json
```

## 🔐 Authentication Endpoints

### 1. User Registration
**Endpoint:** `POST /api/register`

**Request Body:**
```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123"
}
```

**Response (Success - 201):**
```json
{
    "access_token": "1|your_generated_token_here",
    "token_type": "Bearer",
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "email_verified_at": null,
        "created_at": "2024-06-01T12:00:00.000000Z",
        "updated_at": "2024-06-01T12:00:00.000000Z"
    },
    "message": "Registration successful. Please verify your email address."
}
```

**Response (Error - 409):**
```json
{
    "message": "User already exists with this email."
}
```

### 2. User Login
**Endpoint:** `POST /api/login`

**Request Body:**
```json
{
    "email": "john@example.com",
    "password": "password123"
}
```

**Response (Success - 200):**
```json
{
    "access_token": "1|your_generated_token_here",
    "token_type": "Bearer",
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "email_verified_at": "2024-06-01T12:00:00.000000Z",
        "created_at": "2024-06-01T12:00:00.000000Z",
        "updated_at": "2024-06-01T12:00:00.000000Z"
    }
}
```

**Response (Error - 401):**
```json
{
    "message": "Invalid credentials"
}
```

### 3. Email Verification

#### 3.1 Send Verification OTP
**Endpoint:** `POST /api/email/verify/send`

**Request Body:**
```json
{
    "email": "john@example.com"
}
```

**Response (Success - 200):**
```json
{
    "message": "Verification OTP sent to your email."
}
```

**Response (Error - 400):**
```json
{
    "message": "Email already verified."
}
```

#### 3.2 Verify Email with OTP
**Endpoint:** `POST /api/email/verify`

**Request Body:**
```json
{
    "email": "john@example.com",
    "otp": "123456"
}
```

**Response (Success - 200):**
```json
{
    "message": "Email verified successfully."
}
```

**Response (Error - 400):**
```json
{
    "message": "Invalid or expired OTP."
}
```

### 4. Password Management

#### 4.1 Reset Password (with old password)
**Endpoint:** `POST /api/reset-password` *(Requires Authentication)*

**Headers:**
```
Authorization: Bearer your_access_token
Content-Type: application/json
Accept: application/json
```

**Request Body:**
```json
{
    "old_password": "currentPassword123",
    "new_password": "newPassword456"
}
```

**Response (Success - 200):**
```json
{
    "message": "Password updated successfully."
}
```

**Response (Error - 400):**
```json
{
    "message": "Old password is incorrect."
}
```

#### 4.2 Forgot Password (OTP Flow)

**Step 1: Request OTP**
**Endpoint:** `POST /api/forgot-password`

**Request Body:**
```json
{
    "email": "john@example.com"
}
```

**Response (Success - 200):**
```json
{
    "message": "OTP sent to your email."
}
```

**Step 2: Verify OTP**
**Endpoint:** `POST /api/verify-otp`

**Request Body:**
```json
{
    "email": "john@example.com",
    "otp": "123456"
}
```

**Response (Success - 200):**
```json
{
    "message": "OTP verified. Use the reset token to set a new password.",
    "reset_token": "long_random_token_here"
}
```

**Step 3: Set New Password**
**Endpoint:** `POST /api/set-new-password`

**Request Body:**
```json
{
    "email": "john@example.com",
    "reset_token": "long_random_token_here",
    "new_password": "newPassword456"
}
```

**Response (Success - 200):**
```json
{
    "message": "Password changed successfully."
}
```

## 🧪 Testing the API

### Using PowerShell (Windows)

#### 1. Register a User
```powershell
Invoke-WebRequest -Uri "http://localhost:8000/api/register" -Method POST -Headers @{"Content-Type"="application/json"; "Accept"="application/json"} -Body '{"name":"Test User","email":"test@example.com","password":"password123"}'
```

#### 2. Login
```powershell
Invoke-WebRequest -Uri "http://localhost:8000/api/login" -Method POST -Headers @{"Content-Type"="application/json"; "Accept"="application/json"} -Body '{"email":"test@example.com","password":"password123"}'
```

#### 3. Send Email Verification OTP
```powershell
Invoke-WebRequest -Uri "http://localhost:8000/api/email/verify/send" -Method POST -Headers @{"Content-Type"="application/json"; "Accept"="application/json"} -Body '{"email":"test@example.com"}'
```

#### 4. Verify Email with OTP
```powershell
Invoke-WebRequest -Uri "http://localhost:8000/api/email/verify" -Method POST -Headers @{"Content-Type"="application/json"; "Accept"="application/json"} -Body '{"email":"test@example.com","otp":"123456"}'
```

#### 5. Reset Password (with token)
```powershell
Invoke-WebRequest -Uri "http://localhost:8000/api/reset-password" -Method POST -Headers @{"Content-Type"="application/json"; "Accept"="application/json"; "Authorization"="Bearer your_token_here"} -Body '{"old_password":"password123","new_password":"newpassword456"}'
```

### Using cURL (Linux/Mac)

#### 1. Register a User
```bash
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"name":"Test User","email":"test@example.com","password":"password123"}'
```

#### 2. Login
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"email":"test@example.com","password":"password123"}'
```

#### 3. Send Email Verification OTP
```bash
curl -X POST http://localhost:8000/api/email/verify/send \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"email":"test@example.com"}'
```

### Using Postman

1. **Create a new collection**
2. **Set base URL:** `http://localhost:8000/api`
3. **Add headers to collection:**
   ```
   Content-Type: application/json
   Accept: application/json
   ```
4. **For authenticated requests, add:**
   ```
   Authorization: Bearer your_token_here
   ```

## 📧 Email Templates

### Email Verification Template
```
Subject: Verify Your Email Address

<h2>Email Verification</h2>
<p>Hello [User Name],</p>
<p>Your email verification OTP is: <strong>[OTP]</strong></p>
<p>This OTP is valid for 10 minutes.</p>
<p>If you didn't request this verification, please ignore this email.</p>
```

### Password Reset Template
```
Subject: Your Password Reset OTP

<h2>Password Reset OTP</h2>
<p>Your OTP is: <strong>[OTP]</strong></p>
<p>This OTP is valid for 10 minutes.</p>
```

## 🔒 Security Features

- **Password Hashing:** All passwords are hashed using Laravel's Hash facade
- **Token-based Authentication:** Using Laravel Sanctum for secure API tokens
- **Input Validation:** Comprehensive validation for all inputs
- **SQL Injection Protection:** Using Eloquent ORM with parameter binding
- **OTP Expiration:** OTPs expire after 10 minutes
- **Rate Limiting:** Built-in Laravel rate limiting for API routes
- **CORS Protection:** Configured for API security

## 📊 Database Schema

### Users Table
```sql
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

### Personal Access Tokens Table
```sql
CREATE TABLE personal_access_tokens (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tokenable_type VARCHAR(255) NOT NULL,
    tokenable_id BIGINT UNSIGNED NOT NULL,
    name TEXT NOT NULL,
    token VARCHAR(64) UNIQUE NOT NULL,
    abilities TEXT NULL,
    last_used_at TIMESTAMP NULL,
    expires_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

### OTPs Table (Password Reset)
```sql
CREATE TABLE otps (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    otp VARCHAR(255) NULL,
    expires_at TIMESTAMP NULL,
    reset_token VARCHAR(128) NULL,
    reset_token_expires_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

### Email Verification OTPs Table
```sql
CREATE TABLE email_verification_otps (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    otp VARCHAR(255) NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

## 🚨 Error Codes

| Status Code | Description |
|-------------|-------------|
| 200 | Success |
| 201 | Created (Registration) |
| 400 | Bad Request (Validation Error) |
| 401 | Unauthorized (Invalid Credentials) |
| 409 | Conflict (User Already Exists) |
| 422 | Unprocessable Entity (Validation Error) |

## 🔧 Troubleshooting

### Common Issues

1. **404 Error:**
   - Ensure server is running: `php artisan serve`
   - Check route exists: `php artisan route:list`
   - Verify URL and HTTP method

2. **Mail Not Sending:**
   - Check `.env` mail configuration
   - Verify SMTP credentials
   - For Gmail, use App Password if 2FA is enabled

3. **Database Connection Error:**
   - Verify database credentials in `.env`
   - Ensure database exists
   - Run migrations: `php artisan migrate`

4. **Token Issues:**
   - Include `Authorization: Bearer token` header
   - Ensure token is valid and not expired
   - Check if user is authenticated

### Useful Commands

```bash
# Clear all caches
php artisan config:clear
php artisan route:clear
php artisan cache:clear

# Check routes
php artisan route:list

# Check migration status
php artisan migrate:status

# Test mail
php artisan tinker
\Mail::raw('Test', function($msg){ $msg->to('your@email.com')->subject('Test'); });
```

## 📝 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## 🤝 Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📞 Support

If you encounter any issues or have questions, please:
1. Check the troubleshooting section
2. Review the error logs in `storage/logs/laravel.log`
3. Create an issue in the repository

---

**Happy Coding! 🚀** 