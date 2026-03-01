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
├── View/AppView.php      # Base view class
├── View/AjaxView.php     # JSON/XML responses
└── Console/              # Console commands
tests/
├── TestCase/             # Unit/integration tests
├── Fixture/              # Database fixtures
config/                   # Application configuration
templates/              # View templates (.php)
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

- Use explicit `use` statements
- Sort alphabetically within groups
- Order: internal CakePHP → external → internal app

```php
use App\Model\Entity\Aluno;
use App\Policy\AlunoPolicy;
use Authorization\IdentityInterface;
use Authorization\Policy\Result;
use Authorization\Policy\ResultInterface;
use Cake\Controller\Controller;
use Cake\ORM\Table;
```

### Types

- Use PHP 8 native return types (`void`, `int`, `string`)
- Use union types where appropriate (`int|string`)
- Use `mixed` type for uncertain returns

### Code Examples

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

    public function validationDefault(Validator $validator): Validator
    {
        $validator->integer('id')->allowEmptyString('id', null, 'create');
        return $validator;
    }
}
```

### Error Handling

- Use CakePHP exceptions: `NotFoundException`, `ForbiddenException`
- Flash messages: `$this->Flash->success/error/warning/info()`
- Redirect after Flash: `return $this->redirect(['action' => 'index']);`

```php
try {
    $this->Authorization->authorize($aluno);
} catch (ForbiddenException $error) {
    $this->Flash->error('Erro de autorização: ' . $error->getMessage());
    return $this->redirect('/');
}
```

### Debugging

- Use `pr($var)` or `ddd($var)` for debugging (CakePHP debug functions)
- Comment out debug code before committing

### PHPCS

- Uses CakePHP coding standard (see `phpcs.xml`)
- Controllers exempt from return type hints
- Run `composer cs-fix` to auto-apply fixes

## Testing

- Tests in `tests/TestCase/{Type}/` matching src structure
- Name: `{ClassName}Test.php`; fixtures in `tests/Fixture/`
- Use CakePHP TestSuite for integration tests

## View Templates

- Templates in `templates/` with `.php` extension (CakePHP 5 uses .php, not .ctp)
- Use `templates/layout/` for page layouts
- JSON responses: use `AjaxView`

```php
// In controller for JSON response
$this->viewBuilder()->setClassName('AjaxView');
$this->set('_serialize', true);
```

## Configuration

- Database: `config/app_local.php`
- Environment: `config/.env`
- Routes: `config/routes.php`

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
- **`{Model}TablePolicy.php`** - Table policies for index (e.g., `AlunosTablePolicy.php`)
- Implement `BeforePolicyInterface` for global pre-authorization

### Policy Methods

- `canIndex()`, `canView()`, `canAdd()`, `canEdit()`, `canDelete()`
- Custom: `canCargaHoraria()`, `canDeclaracaoperiodo()`, etc.

### Policy Example with BeforePolicy

```php
class AlunoPolicy implements BeforePolicyInterface
{
    public function before(?IdentityInterface $identity, mixed $resource, string $action): ResultInterface|bool|null
    {
        if ($identity) {
            $user_data = $identity->getOriginalData();
            if ($user_data && ($user_data['administrador_id'] || $user_data['professor_id'])) {
                return true;
            }
        }
        return null;
    }
    
    public function canView(IdentityInterface $userSession, Aluno $alunoData): Result
    {
        return new Result($userSession->id == $alunoData->user_id);
    }
}
```

### Checking User Roles

```php
// Via identity data
$user_data = $identity->getOriginalData();
$user_data['administrador_id']  // Admin
$user_data['aluno_id']          // Student
$user_data['professor_id']      // Professor
$user_data['supervisor_id']     // Supervisor
```
