## Symfony Demo Project

### Features
- Docker
- Makefile
- Symfony 7
- PHPStan max level
- Laravel Pint for code style
- Deptrac for analyse file paths
- PHPUnit
- PHPBench
- GitHub Actions
- GitVerse CI\CD

### Instruction
Clone repository

```bash
https://github.com/Simtel/symfony-project
```

Build containers 

```bash
make build
```

Start containers
```bash
make up
```

Create db and db_test databases in adminer in http://localhost:8080

Install composer packages
```bash
make composer-install
```

Execute migrations
```bash
make migrate
```

Запустить тесты
```bash
make test
```
