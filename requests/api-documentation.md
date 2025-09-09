# CreateCMS API Documentation

## Authentication

All API requests require authentication using a Bearer token in the Authorization header.

### Register a New User

**Endpoint:** `/requests/?endpoint=Register`

**Method:** `POST`

**Request Body:**
```json
{
  "username": "newuser",
  "password": "password123",
  "email": "user@example.com",
  "phone": "1234567890"
}
```

**Response:**
```json
{
  "ok": true,
  "error": "0",
  "status": "successful",
  "data": {
    "username": "newuser",
    "email": "user@example.com",
    "phone": "1234567890",
    "token": "generated_token_value",
    "status": "0",
    "date": "2025-09-10 12:34:56",
    "type": "1"
  }
}
```

**Notes:**
- New users are registered with status "0" (pending approval)
- Users are registered as type "1" (employee)
- No authentication required for this endpoint

### Login to Get Token

**Endpoint:** `/requests/?endpoint=Login`

**Method:** `POST`

**Request Body:**
```json
{
  "username": "your_username",
  "password": "your_password"
}
```

**Response:**
```json
{
  "ok": true,
  "error": "0",
  "status": "successful",
  "data": {
    "token": "generated_token_value",
    "userId": "1",
    "username": "your_username",
    "userType": 0,
    "phone": "1234567890"
  }
}
```

**Notes:**
- The token changes with every login for security
- Use this token for all subsequent API requests
- User type: 0 = admin, 1 = employee

### Authentication Header Format

Include this header with all API requests:
```
Authorization: Bearer your_token_here
```

## Users

### List All Users

**Endpoint:** `/requests/?endpoint=Users`

**Method:** `GET`

**Access:** Admin only

### Get Specific User

**Endpoint:** `/requests/?endpoint=Users&id={userId}`

**Method:** `GET`

**Access:** Admin only

### Create New User

**Endpoint:** `/requests/?endpoint=Users`

**Method:** `POST`

**Request Body:**
```json
{
  "username": "newuser",
  "password": "password123",
  "email": "user@example.com",
  "phone": "1234567890"
}
```

**Access:** Admin only

### Update User

**Endpoint:** `/requests/?endpoint=Users`

**Method:** `PUT`

**Request Body:**
```json
{
  "id": "1",
  "email": "updated@example.com",
  "phone": "9876543210",
  "status": "0"
}
```

**Access:** Admin only

### Delete User

**Endpoint:** `/requests/?endpoint=Users&id={userId}`

**Method:** `DELETE`

**Access:** Admin only

## Employees

### List All Employees

**Endpoint:** `/requests/?endpoint=Employees`

**Method:** `GET`

**Access:** Admin for management, Employee for read-only

### Get Specific Employee

**Endpoint:** `/requests/?endpoint=Employees&id={employeeId}`

**Method:** `GET`

**Access:** Admin and Employee

### Create New Employee

**Endpoint:** `/requests/?endpoint=Employees`

**Method:** `POST`

**Request Body:**
```json
{
  "username": "newemployee",
  "password": "password123",
  "email": "employee@example.com",
  "phone": "1234567890",
  "name": "Employee Name"
}
```

**Access:** Admin only

### Update Employee

**Endpoint:** `/requests/?endpoint=Employees`

**Method:** `PUT`

**Request Body:**
```json
{
  "id": "1",
  "name": "Updated Name",
  "email": "updated@example.com",
  "phone": "9876543210",
  "status": "0"
}
```

**Access:** Admin only

### Delete Employee

**Endpoint:** `/requests/?endpoint=Employees&id={employeeId}`

**Method:** `DELETE`

**Access:** Admin only

## Clients (Leads and Customers)

### List All Clients

**Endpoint:** `/requests/?endpoint=Clients`

**Method:** `GET`

**Optional Parameters:**
- `type`: Filter by client type (0 = lead, 1 = customer)
- `status`: Filter by status (0 = active, 1 = inactive, 2 = deleted)

**Access:** Admin only

### Get Specific Client

**Endpoint:** `/requests/?endpoint=Clients&id={clientId}`

**Method:** `GET`

**Access:** Admin only

### Create New Lead/Customer

**Endpoint:** `/requests/?endpoint=Clients`

**Method:** `POST`

**Request Body:**
```json
{
  "name": "Client Name",
  "email": "client@example.com",
  "phone": "1234567890",
  "address": "Client Address",
  "type": "0", 
  "notes": "Additional notes"
}
```

**Notes:**
- `type`: 0 = lead, 1 = customer

**Access:** Admin only

### Update Client (Convert Lead to Customer)

**Endpoint:** `/requests/?endpoint=Clients`

**Method:** `PUT`

**Request Body:**
```json
{
  "id": "1",
  "name": "Updated Name",
  "type": "1",
  "notes": "Converted to customer"
}
```

**Access:** Admin only

### Delete Client

**Endpoint:** `/requests/?endpoint=Clients&id={clientId}`

**Method:** `DELETE`

**Access:** Admin only

## Projects

### List All Projects

**Endpoint:** `/requests/?endpoint=Projects`

**Method:** `GET`

**Optional Parameters:**
- `status`: Filter by status (0 = active, 1 = completed, 2 = deleted)
- `clientId`: Filter by client
- `userId`: Filter by user

**Access:** Admin only

### Get Specific Project

**Endpoint:** `/requests/?endpoint=Projects&id={projectId}`

**Method:** `GET`

**Access:** Admin only

### Create New Project

**Endpoint:** `/requests/?endpoint=Projects`

**Method:** `POST`

**Request Body:**
```json
{
  "title": "Project Title",
  "details": "Project Description",
  "clientId": "1"
}
```

**Access:** Admin only

### Update Project

**Endpoint:** `/requests/?endpoint=Projects`

**Method:** `PUT`

**Request Body:**
```json
{
  "id": "1",
  "title": "Updated Title",
  "details": "Updated Description",
  "status": "1"
}
```

**Access:** Admin only

### Delete Project

**Endpoint:** `/requests/?endpoint=Projects&id={projectId}`

**Method:** `DELETE`

**Access:** Admin only

## Tasks

### List All Tasks

**Endpoint:** `/requests/?endpoint=Tasks`

**Method:** `GET`

**Optional Parameters:**
- `status`: Filter by status (0 = pending, 1 = doing, 2 = finished)
- `projectId`: Filter by project

**Notes:**
- Admin sees all tasks
- Employee only sees tasks assigned to them

**Access:** Admin and Employee

### Get Specific Task

**Endpoint:** `/requests/?endpoint=Tasks&id={taskId}`

**Method:** `GET`

**Access:** Admin and Employee (if assigned)

### Create New Task

**Endpoint:** `/requests/?endpoint=Tasks`

**Method:** `POST`

**Request Body:**
```json
{
  "task": "Task Description",
  "expected": "2025-10-15",
  "to": "1", 
  "projectId": "5"
}
```

**Notes:**
- `to`: Employee ID to assign the task to
- Can include file upload with multipart/form-data

**Access:** Admin only

### Update Task

**Endpoint:** `/requests/?endpoint=Tasks`

**Method:** `PUT`

**Request Body:**
```json
{
  "id": "1",
  "status": "1"
}
```

**Notes:**
- Admin can update all fields
- Employee can only update status (0 = pending, 1 = doing, 2 = finished)

**Access:** Admin and Employee (if assigned)

### Delete Task

**Endpoint:** `/requests/?endpoint=Tasks&id={taskId}`

**Method:** `DELETE`

**Access:** Admin only

## Comments

### List Comments for a Task

**Endpoint:** `/requests/?endpoint=Comments&taskId={taskId}`

**Method:** `GET`

**Access:** Admin and Employee (if assigned to task)

### Add Comment

**Endpoint:** `/requests/?endpoint=Comments`

**Method:** `POST`

**Request Body:**
```json
{
  "taskId": "1",
  "comment": "Comment text"
}
```

**Access:** Admin and Employee (if assigned to task)

### Delete Comment

**Endpoint:** `/requests/?endpoint=Comments&id={commentId}`

**Method:** `DELETE`

**Notes:**
- Employee can only delete their own comments
- Admin can delete any comment

**Access:** Admin and Employee (for own comments)

## Invoices

### List All Invoices

**Endpoint:** `/requests/?endpoint=Invoices`

**Method:** `GET`

**Optional Parameters:**
- `status`: Filter by status (0 = unpaid, 1 = paid)
- `clientId`: Filter by client
- `projectId`: Filter by project

**Access:** Admin only

### Get Specific Invoice

**Endpoint:** `/requests/?endpoint=Invoices&id={invoiceId}`

**Method:** `GET`

**Access:** Admin only

### Create New Invoice

**Endpoint:** `/requests/?endpoint=Invoices`

**Method:** `POST`

**Request Body:**
```json
{
  "clientId": "1",
  "projectId": "5",
  "total": "1000.00",
  "subtotal": "900.00",
  "tax": "100.00",
  "discount": "0.00",
  "notes": "Invoice notes",
  "dueDate": "2025-10-30",
  "items": [
    {
      "description": "Item 1",
      "quantity": "1",
      "price": "500.00",
      "amount": "500.00"
    },
    {
      "description": "Item 2",
      "quantity": "2",
      "price": "250.00",
      "amount": "500.00"
    }
  ]
}
```

**Access:** Admin only

### Update Invoice

**Endpoint:** `/requests/?endpoint=Invoices`

**Method:** `PUT`

**Request Body:**
```json
{
  "id": "1",
  "status": "1",
  "notes": "Invoice paid",
  "items": [
    {
      "description": "Updated Item",
      "quantity": "1",
      "price": "600.00",
      "amount": "600.00"
    }
  ]
}
```

**Access:** Admin only

### Delete Invoice

**Endpoint:** `/requests/?endpoint=Invoices&id={invoiceId}`

**Method:** `DELETE`

**Access:** Admin only

## Response Format

All API responses follow this format:

### Success Response

```json
{
  "ok": true,
  "error": "0",
  "status": "successful",
  "data": { ... }
}
```

### Error Response

```json
{
  "ok": false,
  "error": "1",
  "status": "Error",
  "data": "Error message"
}
```
