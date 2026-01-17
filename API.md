# API Documentation

## Overview

The Bela-Bela Institute Management System provides several API endpoints for managing the application. All API endpoints require proper authentication and CSRF protection.

## Authentication

Most API endpoints require:
- **Admin Role**: For administrative operations
- **Student Role**: For student-specific operations
- **CSRF Token**: Required for POST requests

## Endpoints

### Notifications API

#### Get Notifications
```
GET /api/notifications.php
```

Returns unread notifications for the current user.

**Response:**
```json
[
  {
    "id": 1,
    "title": "New Task",
    "message": "A new task has been assigned",
    "link": "/student/task_view.php?id=123",
    "is_read": 0,
    "created_at": "2024-01-15 10:30:00"
  }
]
```

#### Mark Notification as Read
```
POST /api/notifications_mark_read.php
```

**Parameters:**
- `id` (int): Notification ID
- `csrf` (string): CSRF token

**Response:**
```json
{
  "success": true
}
```

#### Mark All Notifications as Read
```
POST /api/notifications_mark_all_read.php
```

**Parameters:**
- `csrf` (string): CSRF token

**Response:**
```json
{
  "success": true
}
```

### Articles API

#### List Articles
```
GET /public/api/articles.php?action=list
```

**Query Parameters:**
- `action` (string): Must be "list"
- `limit` (int, optional): Number of articles to return (default: 10)
- `offset` (int, optional): Offset for pagination (default: 0)

**Response:**
```json
{
  "articles": [
    {
      "id": 1,
      "title": "Article Title",
      "slug": "article-title",
      "excerpt": "Article excerpt...",
      "featured_image": "/uploads/articles/image.png",
      "published_at": "2024-01-15 10:30:00",
      "views": 150
    }
  ],
  "total": 25
}
```

#### Get Single Article
```
GET /public/api/articles.php?action=get&slug=article-slug
```

**Query Parameters:**
- `action` (string): Must be "get"
- `slug` (string): Article slug

**Response:**
```json
{
  "id": 1,
  "title": "Article Title",
  "slug": "article-slug",
  "content": "Full article content...",
  "author": "Author Name",
  "featured_image": "/uploads/articles/image.png",
  "tags": ["tag1", "tag2"],
  "published_at": "2024-01-15 10:30:00",
  "views": 150
}
```

### Admin API

All admin endpoints require admin role authentication.

#### Create Task
```
POST /admin/api/create_task.php
```

**Parameters:**
- `title` (string, required): Task title
- `type` (string): "topic" or "project" (default: "project")
- `description` (string, optional): Task description
- `course_id` (int, optional): Course ID
- `assigned_user` (int, optional): Assigned user ID
- `status` (string): Task status (default: "backlog")
- `csrf` (string): CSRF token

**Response:**
Redirects to tasks list on success.

#### Update Task
```
POST /admin/api/update_task.php
```

**Parameters:**
- `id` (int, required): Task ID
- `title` (string, required): Task title
- `type` (string): "topic" or "project"
- `description` (string, optional): Task description
- `assigned_user_id` (int, optional): Assigned user ID
- `status` (string): Task status
- `url` (string, optional): Task URL
- `csrf` (string): CSRF token

**Response:**
```json
{
  "success": true
}
```

#### Delete Task
```
POST /admin/api/delete_task.php
```

**Parameters:**
- `id` (int, required): Task ID
- `csrf` (string): CSRF token

**Response:**
Redirects to tasks list on success.

#### Update Course Order
```
POST /admin/api/update_course_order.php
```

**Parameters:**
- `columns` (object): Column order mapping
- `csrf` (string): CSRF token

**Response:**
```json
{
  "success": true
}
```

#### Update Course Status
```
POST /admin/api/update_course_status.php
```

**Parameters:**
- `id` (int, required): Course ID
- `status` (string): New status
- `csrf` (string): CSRF token

**Response:**
```json
{
  "success": true
}
```

#### Grant Review Override
```
POST /admin/api/grant_review_override.php
```

**Parameters:**
- `task_id` (int, required): Task ID
- `user_id` (int, required): User ID
- `csrf` (string): CSRF token

**Response:**
```json
{
  "success": true
}
```

#### Revoke Review Override
```
POST /admin/api/revoke_review_override.php
```

**Parameters:**
- `task_id` (int, required): Task ID
- `user_id` (int, required): User ID
- `csrf` (string): CSRF token

**Response:**
```json
{
  "success": true
}
```

#### User Search
```
GET /admin/api/user_search.php?q=search_term
```

**Query Parameters:**
- `q` (string): Search query

**Response:**
```json
[
  {
    "id": 1,
    "full_name": "John Doe",
    "email": "john@example.com",
    "role": "student"
  }
]
```

## Error Responses

All API endpoints may return error responses:

**400 Bad Request:**
```json
{
  "error": "Invalid parameters"
}
```

**403 Forbidden:**
```json
{
  "error": "Forbidden"
}
```

**405 Method Not Allowed:**
```json
{
  "error": "Method not allowed"
}
```

**500 Internal Server Error:**
```json
{
  "error": "Internal server error"
}
```

## Security Notes

1. **CSRF Protection**: All POST requests require a valid CSRF token
2. **Authentication**: Most endpoints require user authentication
3. **Authorization**: Admin endpoints require admin role
4. **Input Validation**: All inputs are validated and sanitized
5. **SQL Injection Prevention**: All queries use prepared statements
6. **XSS Prevention**: All outputs are properly escaped

## Rate Limiting

Login endpoints are rate-limited to prevent brute force attacks:
- Default: 5 attempts per 15 minutes per IP/email combination
- Configurable in `app/config.php`

## Best Practices

1. Always include CSRF tokens in POST requests
2. Validate all user inputs on the client side before sending
3. Handle errors gracefully
4. Use HTTPS in production
5. Implement proper error logging
