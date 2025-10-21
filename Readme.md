
## Разработка

Применение миграции для RBAC
```bash
php yii migrate --migrationPath=@yii/rbac/migrations
```

Создать аккаунт владельца и базовые роли
```bash
php yii rbac/init
```

Статический анализ
```bash
 vendor/bin/phpstan analyse
```
