# 🚀 Symfony Demo Project

[![PHP Version](https://img.shields.io/badge/PHP-8.4+-blue.svg)](https://www.php.net/)
[![Symfony Version](https://img.shields.io/badge/Symfony-7.x-green.svg)](https://symfony.com/)
[![PHPStan Level](https://img.shields.io/badge/PHPStan-max%20level-brightgreen.svg)](https://phpstan.org/)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)

A modern, Docker-based Symfony 7 demo project designed for scalable PHP application development with strong emphasis on code quality, testing, and CI/CD readiness.

## 📋 Table of Contents

- [Features](#-features)
- [Technology Stack](#-technology-stack)
- [Architecture](#-architecture)
- [Requirements](#-requirements)
- [Quick Start](#-quick-start)
- [Development](#-development)
- [API Documentation](#-api-documentation)
- [Testing](#-testing)
- [Code Quality](#-code-quality)
- [Contributing](#-contributing)

## ✨ Features

- 🐳 **Dockerized Environment** - Complete containerized development setup
- 🏗️ **Modern Architecture** - Domain-Driven Design with clean separation of concerns
- 🔍 **Static Analysis** - PHPStan at maximum level for bulletproof code
- 🎨 **Code Formatting** - Laravel Pint for consistent code style
- 📊 **Architecture Analysis** - Deptrac for dependency rule enforcement
- 🧪 **Comprehensive Testing** - Unit, feature, and performance tests
- 🔄 **CI/CD Ready** - GitHub Actions and GitVerse integration
- 📡 **REST API** - Full-featured API with authentication
- 📧 **Email & Notifications** - Built-in mailing and notification system
- 🛠️ **Developer Tools** - Makefile automation and Xdebug support

## 🛠️ Technology Stack

### Backend Framework
- **[Symfony 7](https://symfony.com/)** - The leading PHP framework for web applications
- **[PHP 8.5+](https://www.php.net/)** - Latest PHP version with modern features

### Database & ORM
- **[Doctrine ORM](https://www.doctrine-project.org/)** - Object-relational mapping for PHP
- **[Doctrine Migrations](https://www.doctrine-project.org/projects/migrations.html)** - Database schema versioning
- **[MySQL](https://www.mysql.com/)** - Reliable relational database

### Code Quality & Analysis
- **[PHPStan](https://phpstan.org/)** - Static analysis tool (Level: MAX)
- **[Laravel Pint](https://laravel.com/docs/pint)** - Code style fixer
- **[Deptrac](https://qossmic.github.io/deptrac/)** - Dependency rule analysis
- **[Rector](https://getrector.org/)** - Automated code refactoring

### Testing & Performance
- **[PHPUnit](https://phpunit.de/)** - Unit and feature testing framework
- **[PHPBench](https://github.com/phpbench/phpbench)** - Performance benchmarking

### Development & Deployment
- **[Docker](https://www.docker.com/)** - Containerization platform
- **[Docker Compose](https://docs.docker.com/compose/)** - Multi-container application management
- **[Adminer](https://www.adminer.org/)** - Database management interface
- **Make** - Build automation tool

### Messaging & Communication
- **[Symfony Messenger](https://symfony.com/doc/current/messenger.html)** - Message handling and queues
- **[Symfony Mailer](https://symfony.com/doc/current/mailer.html)** - Email sending capabilities
- **[Symfony Notifier](https://symfony.com/doc/current/notifier.html)** - Multi-channel notifications

## 🏛️ Architecture

The project follows **Domain-Driven Design (DDD)** principles with a modular monolith approach:

```
src/
├── Context/
│   ├── Common/           # Shared functionality
│   │   ├── Application/  # Use cases and DTOs
│   │   ├── Domain/       # Business logic and entities
│   │   └── Infrastructure/ # Controllers, repositories, external services
│   ├── User/             # User management context
│   └── Project/          # Project-specific features
├── DataFixtures/         # Database seeders
└── Kernel.php           # Application kernel
```

### Design Patterns Used
- **Repository Pattern** - Data access abstraction
- **Command Pattern** - Encapsulated requests
- **Service Layer** - Business logic encapsulation
- **Event-Driven Architecture** - Loose coupling via events
- **CQRS** - Command Query Responsibility Segregation

## 📋 Requirements

- **Docker** 20.10+
- **Docker Compose** 2.0+
- **Make** (for automation)
- **Git** (for version control)

## 🚀 Quick Start

### 1. Clone the Repository

```bash
git clone https://github.com/Simtel/symfony-project.git
cd symfony-project
```

### 2. Build and Start Containers

```bash
# Build Docker containers
make build

# Start the application
make up
```

### 3. Database Setup

1. Open Adminer at [http://localhost:8080](http://localhost:8080)
2. Create databases: `db` and `db_test`

### 4. Install Dependencies and Run Migrations

```bash
# Install Composer packages
make composer-install

# Run database migrations
make migrate
```

### 5. Verify Installation

```bash
# Run tests to ensure everything works
make test

# Check API endpoint
curl http://localhost:8000/api/test
```

## 🔧 Development

### Available Make Commands

| Command | Description |
|---------|-------------|
| `make build` | Build Docker containers |
| `make up` | Start containers |
| `make down` | Stop containers |
| `make cli` | Enter PHP container |
| `make xcli` | Enter PHP container with Xdebug |
| `make composer-install` | Install Composer dependencies |
| `make migrate` | Run database migrations |
| `make rollback` | Rollback last migration |
| `make to-migration` | Generate new migration |
| `make test` | Run all tests |
| `make testf FILTER=TestName` | Run filtered tests |
| `make phpstan` | Run static analysis |
| `make pint` | Fix code style |
| `make bench` | Run performance benchmarks |

### Development Workflow

1. **Start Development Environment**
   ```bash
   make up
   make cli  # Enter container for development
   ```

2. **Make Changes**
   - Edit code in your preferred IDE
   - Follow PSR-12 coding standards
   - Write tests for new features

3. **Quality Checks**
   ```bash
   make phpstan  # Static analysis
   make pint     # Code formatting
   make test     # Run tests
   ```

4. **Database Changes**
   ```bash
   make to-migration  # Generate migration
   make migrate       # Apply migration
   ```

## 📡 API Documentation

The project provides a comprehensive REST API for managing users, configurations, logs, and various testing functions.

### Base URL
```
http://localhost:8000/api
```

### Authentication
Some endpoints require authentication via API key.

### Core Endpoints

#### Health Check
```http
GET /api/test
```
**Response:**
```json
{
  "test": true,
  "time": "2025-08-29T10:30:00+00:00",
  "message": "API is working correctly"
}
```

#### Request Mapping Test
```http
POST /api/test-map-request
Content-Type: application/json

{
  "name": "test",
  "value": "example"
}
```

#### Email Testing
```http
GET /api/test-email
```

#### Notification Testing
```http
GET /api/test-notify
```

### User Management

#### Get User Information
```http
GET /api/user/{id}
```
**Response:**
```json
{
  "id": 1,
  "name": "John Doe",
  "email": "john@example.com",
  "locations": [...],
  "contacts": [...]
}
```

#### Find User by Name
```http
GET /api/user/find/{userName}
```

#### Add Location to User
```http
PUT /api/user/{userId}/location/{locationId}
```
**Response:**
```json
{
  "message": "Location successfully added to user"
}
```

#### Calculate User Access
```http
POST /api/user/{userId}/calculate-access
```
**Response:**
```json
{
  "message": "User access rights successfully calculated"
}
```

#### Get Users by Location
```http
GET /api/users/{locationId}
```
**Response:**
```json
{
  "users": [...],
  "message": "Users found"
}
```

### Configuration Management

#### List Configurations
```http
GET /api/config/list
```
**Response:**
```json
[
  {
    "id": 1,
    "name": "app_name",
    "value": "Symfony Demo",
    "author": {...}
  }
]
```

#### Create Configuration
```http
POST http://localhost:8000/api/config
Content-Type: application/json

{
  "name": "setting_name",
  "value": "setting_value"
}
```
**Response:**
```json
{
  "message": "Configuration successfully created",
  "config": {
    "id": 2,
    "name": "setting_name",
    "value": "setting_value"
  }
}
```

#### Delete Configuration
```http
DELETE http://localhost:8000/api/config/{id}
```
**Response:**
```json
{
  "message": "Конфигурация успешно удалена"
}
```

#### Error Responses for Delete Operation

| Status Code | Description | Response |
|-------------|-------------|----------|
| `422 Unprocessable Entity` | Invalid UUID format | ```json {"error": true, "message": "Ошибка валидации данных", "details": {"violations": {"id": "Некорректный формат ID конфигурации"}}}``` |
| `404 Not Found` | Configuration not found | ```json {"message": "Конфигурация с указанным ID не найдена"}``` |
| `403 Forbidden` | Access denied | Unauthorized access |

### Log Management

#### List Logs
```http
GET /api/log/list
```
**Response:**
```json
{
  "logs": [
    {
      "id": 1,
      "user": "John Doe",
      "action": "user_created",
      "createdAt": "2025-08-29 10:30:00",
      "url": "/api/log/1"
    }
  ]
}
```

#### Get Log Details
```http
GET /api/log/{id}
```
**Response:**
```json
{
  "log": {
    "id": 1,
    "action": "user_created",
    "data": {...},
    "author": {...},
    "createdAt": "2025-08-29T10:30:00+00:00"
  }
}
```

### HTTP Status Codes

| Code | Description |
|------|-------------|
| `200 OK` | Successful request |
| `201 Created` | Resource successfully created |
| `400 Bad Request` | Data validation error |
| `401 Unauthorized` | Authentication required |
| `404 Not Found` | Resource not found |
| `500 Internal Server Error` | Internal server error |

### Error Format

All errors are returned in the following format:
```json
{
  "error": "Error description",
  "code": 400,
  "details": {
    "field": "Field error details"
  }
}
```

### Usage Examples

#### Create Configuration with curl
```bash
curl -X POST http://localhost:8000/api/config \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_API_KEY" \
  -d '{"name": "debug_mode", "value": "true"}'
```

#### Get User Information
```bash
curl -X GET http://localhost:8000/api/user/1 \
  -H "Authorization: Bearer YOUR_API_KEY"
```

## 🧪 Testing

The project includes comprehensive testing capabilities:

### Test Types
- **Unit Tests** - Testing individual components in isolation
- **Feature Tests** - Testing complete features and API endpoints
- **Performance Tests** - Benchmarking critical operations

### Running Tests

```bash
# Run all tests
make test

# Run specific test file
make testf FILTER=ConfigTest

# Run tests with coverage (inside container)
bin/phpunit --coverage-html coverage

# Run benchmarks
make bench
```

### Test Structure
```
tests/
├── Bench/           # Performance benchmarks
├── Command/         # CLI command tests
├── Feature/         # Feature/integration tests
├── Unit/Context/    # Unit tests organized by context
└── *.php           # Test helpers and base classes
```

## 🎯 Code Quality

This project maintains high code quality through automated tools:

### Static Analysis
```bash
# Run PHPStan at maximum level
make phpstan
```

### Code Formatting
```bash
# Fix code style issues
make pint
```

### Architecture Analysis
```bash
# Check dependency rules
vendor/bin/deptrac analyze
```

### Quality Gates
- ✅ PHPStan Level MAX compliance
- ✅ PSR-12 coding standards
- ✅ 100% test coverage for critical paths
- ✅ No circular dependencies
- ✅ Clean architecture boundaries

## 🤝 Contributing

1. **Fork the repository**
2. **Create a feature branch**
   ```bash
   git checkout -b feature/amazing-feature
   ```
3. **Make your changes**
   - Follow coding standards
   - Add tests for new features
   - Update documentation
4. **Run quality checks**
   ```bash
   make phpstan
   make pint
   make test
   ```
5. **Commit your changes**
   ```bash
   git commit -m 'Add amazing feature'
   ```
6. **Push to the branch**
   ```bash
   git push origin feature/amazing-feature
   ```
7. **Open a Pull Request**

### Development Guidelines
- Follow Domain-Driven Design principles
- Write meaningful test cases
- Keep controllers thin, services focused
- Use type hints and return types
- Document complex business logic
- Maintain backward compatibility

---

**Built with ❤️ using Symfony 7 and modern PHP practices**
