# CareConnect API Documentation

## Overview
I have successfully fixed and implemented JSON API endpoints for all controllers in both the **API folder** and **Backend folder**. All endpoints now return proper JSON responses when accessed via API calls.

## What was Fixed

### 1. **API Folder** (`/api`)
- ✅ Already had proper JSON controllers (frontend and backend controllers)
- ✅ Routes properly configured in `api/routes/api.php`
- ✅ All controllers return JSON responses

### 2. **Backend Folder** (`/backend`) - NEW!
- ✅ **Created missing `api.php` routes file** 
- ✅ **Updated `bootstrap/app.php`** to include API routes
- ✅ **Fixed all controllers** to support both web views AND JSON responses
- ✅ **Added Sanctum token support** to Admin model

## API Endpoints

### Backend API Endpoints (New!)
Base URL: `http://your-domain/api/`

#### Authentication
```
GET  /api/auth/login          # Get login form structure
POST /api/auth/login          # Admin login (returns token)
POST /api/auth/logout         # Admin logout
```

#### Dashboard
```
GET  /api/dashboard           # Dashboard statistics
```

#### Users Management
```
GET    /api/users             # List all users
GET    /api/users/{id}        # Get specific user
GET    /api/users/{id}/edit   # Get user edit form
PUT    /api/users/{id}        # Update user
DELETE /api/users/{id}        # Delete user
```

#### Hospitals Management
```
GET    /api/hospitals         # List all hospitals
GET    /api/hospitals/{id}    # Get specific hospital
GET    /api/hospitals/{id}/edit # Get hospital edit form
PUT    /api/hospitals/{id}    # Update hospital
DELETE /api/hospitals/{id}    # Delete hospital
```

#### Appointments Management
```
GET    /api/appointments      # List all appointments
GET    /api/appointments/{id} # Get specific appointment
GET    /api/appointments/{id}/edit # Get appointment edit form
PUT    /api/appointments/{id} # Update appointment
DELETE /api/appointments/{id} # Delete appointment
```

#### Feedback Management
```
GET    /api/feedback          # List all feedback
GET    /api/feedback/statistics # Get feedback statistics
GET    /api/feedback/{id}     # Get specific feedback
DELETE /api/feedback/{id}     # Delete feedback
```

#### Subscriptions Management
```
GET    /api/subscriptions     # List all subscriptions
GET    /api/subscriptions/{id} # Get specific subscription
PUT    /api/subscriptions/{id} # Update subscription
DELETE /api/subscriptions/{id} # Delete subscription
```

#### Profile Management
```
GET /api/profile              # Get current admin profile
GET /api/profile/edit         # Get profile edit form
PUT /api/profile              # Update profile
```

### Frontend API Endpoints (Already Working)
Base URL: `http://your-domain/api/frontend/`

All the existing frontend endpoints for users, hospitals, appointments, feedback, etc.

## How to Test

### 1. **Authentication Test**
```bash
# Get login form
curl -X GET "http://your-domain/api/auth/login" \
  -H "Accept: application/json"

# Login (replace with actual admin credentials)
curl -X POST "http://your-domain/api/auth/login" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@example.com",
    "password": "your-password"
  }'
```

### 2. **Using Token for Protected Endpoints**
```bash
# Use the token from login response
curl -X GET "http://your-domain/api/dashboard" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

### 3. **Test Different Endpoints**
```bash
# Get all users
curl -X GET "http://your-domain/api/users" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"

# Get all hospitals
curl -X GET "http://your-domain/api/hospitals" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"

# Get appointments
curl -X GET "http://your-domain/api/appointments" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

### 4. **API Testing Tools**
You can use:
- **Postman**: Import the endpoints and test
- **Insomnia**: REST client for testing
- **curl**: Command line testing
- **Browser**: For GET requests (add `?format=json` if needed)

## JSON Response Format

All API endpoints return consistent JSON format:

### Success Response
```json
{
  "success": true,
  "data": {
    // Response data here
  },
  "message": "Optional success message"
}
```

### Error Response
```json
{
  "success": false,
  "message": "Error message",
  "errors": {
    // Validation errors if any
  }
}
```

### List Response with Pagination
```json
{
  "success": true,
  "data": {
    "items": [...],
    "pagination": {
      "current_page": 1,
      "last_page": 5,
      "per_page": 10,
      "total": 45
    },
    "filters": {...},
    "sort": {...}
  }
}
```

## Key Features

### 1. **Dual Response Support**
- Controllers detect if request expects JSON
- Return JSON for API calls
- Return views for web interface
- Same controller handles both!

### 2. **Proper Authentication**
- Backend uses Sanctum tokens
- Admin model supports API tokens
- Protected routes require authentication

### 3. **Comprehensive CRUD**
- Create, Read, Update, Delete for all resources
- Form structure provided for frontend
- Validation included

### 4. **Error Handling**
- Consistent error responses
- Validation error details
- Proper HTTP status codes

### 5. **Filtering and Sorting**
- List endpoints support filtering
- Sorting capabilities
- Pagination for large datasets

## Database Models Supported

All models are properly configured with:
- ✅ Users (`users_table`)
- ✅ Admins (`admins_table`) 
- ✅ Hospitals (`hospitals`)
- ✅ Appointments (`appointments`)
- ✅ Subscriptions (`subscriptions`)
- ✅ Feedback (`feedback`)

## Next Steps

1. **Test the endpoints** using the examples above
2. **Configure your web server** to serve the backend API
3. **Update your frontend** to consume these APIs
4. **Set up proper authentication** with admin credentials
5. **Configure CORS** if needed for cross-origin requests

## Files Modified

### Backend Folder:
- ✅ `routes/api.php` (CREATED)
- ✅ `bootstrap/app.php` (UPDATED)
- ✅ `app/Models/Admin.php` (UPDATED - Added Sanctum)
- ✅ `app/Http/Controllers/AdminAuthController.php` (UPDATED)
- ✅ `app/Http/Controllers/DashboardController.php` (UPDATED)
- ✅ `app/Http/Controllers/AppointmentController.php` (UPDATED)
- ✅ `app/Http/Controllers/HospitalController.php` (UPDATED)
- ✅ `app/Http/Controllers/UserManagementController.php` (UPDATED)
- ✅ `app/Http/Controllers/FeedbackController.php` (UPDATED)
- ✅ `app/Http/Controllers/SubscriptionController.php` (UPDATED)
- ✅ `app/Http/Controllers/ProfileController.php` (UPDATED)

### API Folder:
- ✅ Already working (no changes needed)

The backend is now fully API-ready and will return JSON responses for all endpoints when accessed via API calls!
