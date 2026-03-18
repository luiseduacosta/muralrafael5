# AGENTS.md - Mural de Estágios (CakePHP 5)

Guidelines for agentic coding agents working in this repository.

## Project Overview

- **Type**: CakePHP 5 web application
- **Purpose**: Internship bulletin board for ESS/UFRJ
- **PHP Version**: >=8.1
- **Database**: MySQL/MariaDB
- **Plugins**: Authentication, Authorization, CakePdf, MobileDetect

## Directory Structure

```
src/
├── Application.php       # Main application bootstrap
├── Controller/           # CakePHP controllers (extends AppController)
├── Model/Table/          # CakePHP Table classes
├── Model/Entity/         # CakePHP Entity classes
├── Policy/               # Authorization policies
├── View/Helper/          # View helpers
templates/                # View templates (.php)
tests/
├── TestCase/             # Unit/integration tests
├── Fixture/              # Database fixtures
config/                   # Application configuration
```

## Build / Lint / Test Commands

```bash
composer test                       # Run all tests
vendor/bin/phpunit                   # Run all tests (verbose)
vendor/bin/phpunit path/to/Test.php  # Run single test file
vendor/bin/phpunit --filter method   # Run specific test method
composer cs-check                    # Check code style (phpcs)
composer cs-fix                      # Auto-fix code style (phpcbf)
composer check                       # Full check (test + cs-check)
composer stan                        # Static analysis (optional)
```

## Code Style Guidelines

- Use PHP 8.1+ features (named arguments, readonly properties)
- Always use `declare(strict_types=1);` at the top of PHP files
- Indentation: 4 spaces (not tabs), except YAML (2 spaces)
- Line endings: LF; final newline required

### Naming Conventions

| Type | Convention | Example |
|------|------------|---------|
| Classes | PascalCase | `UsersTable`, `EstagiariosController` |
| Methods/Variables | camelCase | `initialize()`, `$user` |
| Constants | UPPER_CASE | `MAX_LENGTH` |
| Files | Match class name | `UsersTable.php` |
| Tables | Plural in DB | `alunos`, `estagiarios` |
| DB Columns | snake_case | `user_id`, `data_nascimento` |

### Imports

- Use explicit `use` statements, sorted alphabetically
- Order: internal CakePHP → external → internal app

```php
use App\Model\Entity\Aluno;
use App\Policy\AlunoPolicy;
use Authorization\IdentityInterface;
use Authorization\Policy\Result;
use Cake\Controller\Controller;
use Cake\ORM\Table;
```

### Types

- Use PHP 8 native return types (`void`, `int`, `string`)
- Use union types where appropriate (`int|string`)
- Use `mixed` for uncertain returns

## Code Examples

```php
// Controller
class AppController extends Controller
{
    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('Flash');
        $this->loadComponent('Authentication.Authentication');
        $this->loadComponent('Authorization.Authorization');
    }
}

// Entity
class Aluno extends Entity
{
    protected array $_accessible = [
        'nome' => true,
        'registro' => true,
    ];
}

// Table
class AlunosTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('alunos');
        $this->setAlias('Alunos');
        $this->setDisplayField('nome');
        $this->setPrimaryKey('id');
    }
}
```

## Error Handling

- Use CakePHP exceptions: `NotFoundException`, `ForbiddenException`
- Flash messages: `$this->Flash->success/error/warning/info()`
- Redirect after Flash: `return $this->redirect(['action' => 'index']);`

## Testing

- Tests in `tests/TestCase/{Type}/` matching src structure
- Name: `{ClassName}Test.php`; fixtures in `tests/Fixture/`

## Authorization / Policies

The project uses CakePHP Authorization plugin with user categories:

| Categoria | Role |
|-----------|------|
| 1 | Administrador (Admin) |
| 2 | Aluno (Student) |
| 3 | Professor |
| 4 | Supervisor |

### Policy Types

- **`{Model}Policy.php`** - Entity policies (e.g., `AlunoPolicy.php`)
- **`{Model}TablePolicy.php`** - Table policies for index
- Implement `BeforePolicyInterface` for global pre-authorization

### Checking User Roles

```php
$user_data = $identity->getOriginalData();
$user_data['administrador_id']  // Admin
$user_data['aluno_id']          // Student
$user_data['professor_id']      // Professor
$user_data['supervisor_id']     // Supervisor
```

## Configuration

- Database: `config/app_local.php`
- Environment: `config/.env`
- Routes: `config/routes.php`

## PHPCS

- Uses CakePHP coding standard (see `phpcs.xml`)
- Controllers exempt from return type hints
- Run `composer cs-fix` to auto-apply fixes
