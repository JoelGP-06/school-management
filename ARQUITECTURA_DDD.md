# Explicación de la Arquitectura DDD - School Management System

## 📚 Índice

1. [Introducción a DDD](#introducción-a-ddd)
2. [Capa de Dominio](#capa-de-dominio)
3. [Capa de Aplicación](#capa-de-aplicación)
4. [Capa de Infraestructura](#capa-de-infraestructura)
5. [Flujo de Datos](#flujo-de-datos)
6. [Principios Aplicados](#principios-aplicados)
7. [Ventajas de esta Arquitectura](#ventajas-de-esta-arquitectura)

---

## 🎯 Introducción a DDD

**Domain-Driven Design (DDD)** es un enfoque de desarrollo de software que se centra en:

- Modelar el dominio del negocio de forma precisa
- Separar la lógica de negocio de los detalles técnicos
- Crear un lenguaje ubicuo entre desarrolladores y expertos del dominio
- Mantener el código organizado en capas con responsabilidades claras

### Capas Principales

```
┌─────────────────────────────────────┐
│      INFRASTRUCTURE LAYER           │  ← Detalles técnicos
│  (Controllers, Repositories, Views) │
├─────────────────────────────────────┤
│      APPLICATION LAYER              │  ← Casos de uso
│        (Services)                   │
├─────────────────────────────────────┤
│        DOMAIN LAYER                 │  ← Lógica de negocio
│  (Entities, Value Objects, Repos)  │
└─────────────────────────────────────┘
```

**Regla fundamental**: Las dependencias apuntan HACIA el dominio, nunca al revés.

---

## 🏛️ Capa de Dominio

### Propósito
Contener **toda la lógica de negocio** sin depender de frameworks, bases de datos o detalles de implementación.

### Componentes

#### 1. **Entidades (Entities)**
Son objetos con identidad única que persisten en el tiempo.

**Ejemplo: Teacher**
```php
class Teacher
{
    private ?int $id;                    // Identidad
    private int $userId;                 // Relación
    private string $specialty;           // Atributo del dominio
    private ?int $departmentId;          // Relación
    
    // Método de dominio
    public function assignToDepartment(int $departmentId): void
    {
        $this->departmentId = $departmentId;
    }
}
```

**Características**:
- Tienen identidad (ID)
- Contienen lógica de negocio (métodos)
- No son anémicas (no son solo getters/setters)
- No conocen la persistencia

#### 2. **Value Objects**
Objetos inmutables sin identidad, definidos por sus atributos.

**Ejemplo: Email**
```php
class Email
{
    private string $value;
    
    public function __construct(string $email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException("Invalid email");
        }
        $this->value = $email;
    }
}
```

**Características**:
- Inmutables
- Autovalidados
- Sin identidad propia
- Reemplazables

#### 3. **Interfaces de Repositorio**
Contratos que definen cómo persistir/recuperar entidades, SIN implementación.

**Ejemplo: UserRepositoryInterface**
```php
interface UserRepositoryInterface
{
    public function save(User $user): void;
    public function findById(int $id): ?User;
    public function findByEmail(Email $email): ?User;
}
```

**Características**:
- Solo interfaces (contratos)
- No conocen detalles de persistencia
- El dominio define QUÉ necesita, no CÓMO se hace

### Reglas del Dominio

✅ **Permitido**:
- Lógica de negocio pura
- Validaciones de reglas de negocio
- Relaciones entre entidades
- Definir interfaces de servicios externos

❌ **Prohibido**:
- SQL o acceso a base de datos
- Referencias a HTTP, frameworks, librerías externas
- Lógica de presentación
- Detalles de infraestructura

---

## 🔧 Capa de Aplicación

### Propósito
Orquestar los **casos de uso** del sistema usando las entidades del dominio.

### Componentes

#### Servicios de Aplicación
Coordinan las operaciones entre múltiples entidades para cumplir un caso de uso.

**Ejemplo: AssignTeacherDepartmentService**
```php
class AssignTeacherDepartmentService
{
    private TeacherRepositoryInterface $teacherRepository;
    private DepartmentRepositoryInterface $departmentRepository;
    
    public function execute(int $teacherId, int $departmentId): void
    {
        // 1. Obtener entidades del dominio
        $teacher = $this->teacherRepository->findById($teacherId);
        $department = $this->departmentRepository->findById($departmentId);
        
        // 2. Validar existencia
        if (!$teacher) throw new \RuntimeException("Teacher not found");
        if (!$department) throw new \RuntimeException("Department not found");
        
        // 3. Ejecutar lógica de dominio
        $teacher->assignToDepartment($departmentId);
        
        // 4. Persistir cambios
        $this->teacherRepository->save($teacher);
    }
}
```

### Características

✅ **Responsabilidades**:
- Coordinar casos de uso
- Obtener entidades de repositorios
- Llamar métodos de dominio
- Persistir cambios
- Manejar transacciones (si es necesario)

❌ **NO debe**:
- Contener lógica de negocio (va en el dominio)
- Conocer detalles de HTTP, bases de datos
- Manipular directamente atributos de entidades
- Crear entidades con lógica compleja (usar factories si es necesario)

### Flujo de un Caso de Uso

```
Usuario → Controller → Application Service → Domain Entities → Repository
                                                    ↓
                                            Lógica de Negocio
```

---

## 🔌 Capa de Infraestructura

### Propósito
Implementar todos los **detalles técnicos** que el dominio necesita pero no debe conocer.

### Componentes

#### 1. **Persistencia (Repositories)**
Implementaciones concretas de las interfaces del dominio.

**Ejemplo: InMemoryTeacherRepository**
```php
class InMemoryTeacherRepository implements TeacherRepositoryInterface
{
    private array $teachers = [];
    private int $nextId = 1;
    
    public function save(Teacher $teacher): void
    {
        if ($teacher->getId() === null) {
            $teacher->setId($this->nextId++);
        }
        $this->teachers[$teacher->getId()] = $teacher;
    }
    
    public function findById(int $id): ?Teacher
    {
        return $this->teachers[$id] ?? null;
    }
}
```

**Ventajas**:
- Fácil de reemplazar (InMemory → PDO → Doctrine)
- Permite testing sin base de datos
- El dominio no cambia si cambias el motor de persistencia

#### 2. **Routing**
Mapeo de URLs a controladores.

**Ejemplo: Router**
```php
class Router
{
    private array $routes = [];
    
    public function get(string $path, callable $handler): void
    {
        $this->routes[] = ['method' => 'GET', 'path' => $path, 'handler' => $handler];
    }
    
    public function dispatch(string $method, string $uri): void
    {
        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $route['path'] === $uri) {
                call_user_func($route['handler']);
                return;
            }
        }
        http_response_code(404);
    }
}
```

#### 3. **Controllers**
Manejan las peticiones HTTP y delegan a los servicios de aplicación.

**Ejemplo: AssignmentController**
```php
class AssignmentController
{
    public function assignTeacher(): void
    {
        // 1. Obtener datos de la petición
        $teacherId = (int)$_POST['teacher_id'];
        $departmentId = (int)$_POST['department_id'];
        
        // 2. Delegar al servicio de aplicación
        $service = new AssignTeacherDepartmentService(...);
        $service->execute($teacherId, $departmentId);
        
        // 3. Renderizar vista
        require 'view.php';
    }
}
```

**Reglas de los Controllers**:
- ❌ NO contienen lógica de negocio
- ✅ Solo coordinan entrada/salida HTTP
- ✅ Delegan a servicios de aplicación
- ✅ Preparan datos para las vistas

#### 4. **Views**
Templates de presentación (HTML + PHP).

---

## 🔄 Flujo de Datos Completo

### Ejemplo: Asignar Profesor a Departamento

```
1. USUARIO                        2. INFRASTRUCTURE           3. APPLICATION              4. DOMAIN
   │                                 │                           │                          │
   POST /assign-teacher              │                           │                          │
   teacher_id=1                      │                           │                          │
   department_id=1                   │                           │                          │
   │                                 │                           │                          │
   └──────────────────────────────>  Router                      │                          │
                                     │                           │                          │
                                     dispatch()                  │                          │
                                     │                           │                          │
                                     AssignmentController        │                          │
                                     │                           │                          │
                                     assignTeacher()             │                          │
                                     │                           │                          │
                                     │──────────────────────────> AssignTeacher             │
                                     │                           DepartmentService          │
                                     │                           │                          │
                                     │                           execute(1, 1)              │
                                     │                           │                          │
                                     │                           │─────────────────────────> Teacher::assignTo
                                     │                           │                          Department(1)
                                     │                           │                          │
                                     │                           │                          [Lógica de negocio]
                                     │                           │                          │
                                     │                           │ <────────────────────────┘
                                     │                           │                          
                                     │                           save(teacher)              
                                     │                           │                          
                                     TeacherRepository <─────────┘                          
                                     │                                                      
                                     [Persistir en DB/Memoria]                              
                                     │                                                      
                                     render(view) ──────────────────────────────────────────> Vista HTML
```

---

## 🎯 Principios Aplicados

### 1. **Inversión de Dependencias (DIP)**
```
❌ MAL: Domain → Infrastructure
✅ BIEN: Infrastructure → Domain
```

El dominio define interfaces, la infraestructura las implementa.

### 2. **Separación de Responsabilidades (SRP)**
- **Domain**: Lógica de negocio
- **Application**: Orquestación de casos de uso
- **Infrastructure**: Detalles técnicos

### 3. **Open/Closed Principle**
Puedes cambiar la implementación de repositorios sin modificar el dominio:
```php
// Cambiar de InMemory a PDO
$teacherRepo = new PDOTeacherRepository($pdo);
// El servicio no cambia
$service = new AssignTeacherDepartmentService($teacherRepo, $deptRepo);
```

### 4. **Lenguaje Ubicuo**
Las clases usan términos del negocio:
- `Teacher`, `Student`, `Department` (no `TeacherModel`, `StudentDTO`)
- `assignToDepartment()` (no `setDepartmentId()`)

---

## ✅ Ventajas de esta Arquitectura

### 1. **Testabilidad**
```php
// Test sin base de datos real
$teacherRepo = new InMemoryTeacherRepository();
$service = new AssignTeacherDepartmentService($teacherRepo, $deptRepo);
```

### 2. **Mantenibilidad**
- Cambios en UI no afectan al dominio
- Cambios en base de datos no afectan a la lógica de negocio
- Cada capa tiene responsabilidades claras

### 3. **Escalabilidad**
Puedes:
- Cambiar de MySQL a PostgreSQL
- Cambiar de PHP templates a React
- Añadir cache sin tocar el dominio

### 4. **Reusabilidad**
El dominio puede usarse en:
- Aplicación web
- API REST
- CLI
- Workers en background

### 5. **Comprensibilidad**
```php
// Código de dominio legible
$teacher->assignToDepartment($departmentId);

// vs código procedimental
UPDATE teachers SET department_id = ? WHERE id = ?
```

---

## 📋 Checklist de Buenas Prácticas

### Domain Layer
- [ ] Entidades tienen métodos de negocio (no son anémicas)
- [ ] No hay referencias a HTTP, DB, frameworks
- [ ] Value Objects validan sus propios datos
- [ ] Solo interfaces de repositorios

### Application Layer
- [ ] Servicios coordinan, no contienen lógica de negocio
- [ ] Usan interfaces de repositorios
- [ ] Cada servicio = un caso de uso
- [ ] No conocen detalles de HTTP

### Infrastructure Layer
- [ ] Controllers delegan a servicios
- [ ] Repositorios implementan interfaces del dominio
- [ ] Vistas solo presentan datos
- [ ] Fácil de cambiar implementaciones

---

## 🎓 Conclusión

Esta arquitectura permite:

1. **Claridad**: Cada capa tiene un propósito definido
2. **Flexibilidad**: Cambiar tecnologías sin reescribir todo
3. **Calidad**: Testear lógica de negocio fácilmente
4. **Evolución**: Añadir funcionalidades sin romper lo existente

**El dominio es el rey** - todo lo demás son detalles de implementación que pueden cambiar.
