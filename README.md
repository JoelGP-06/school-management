# School Management System

Sistema de gestión escolar desarrollado con arquitectura DDD (Domain-Driven Design) en PHP puro.

## 🎯 Objetivo del Proyecto

Este proyecto es una aplicación académica que demuestra la implementación de:
- Arquitectura DDD (Domain-Driven Design)
- Patrón MVC para Request/Response
- Routing manual sin frameworks
- Persistencia desacoplada mediante repositorios
- Tests unitarios con PHPUnit

## 📁 Estructura del Proyecto

```
school-management/
├── src/
│   ├── Domain/              # Capa de Dominio (lógica de negocio pura)
│   ├── Application/         # Capa de Aplicación (casos de uso)
│   └── Infrastructure/      # Capa de Infraestructura (implementaciones)
├── tests/                   # Tests unitarios
├── public/                  # Punto de entrada web
└── vendor/                  # Dependencias de Composer
```

## 🏗️ Arquitectura por Capas

### 1. Capa de Dominio (Domain)
**Responsabilidad**: Contiene la lógica de negocio pura, sin dependencias externas.

**Componentes**:
- **Entidades**: User, Teacher, Student, Department, Course
- **Value Objects**: Email (validación de formato)
- **Interfaces de Repositorios**: Contratos para persistencia

**Características**:
- Sin dependencias de infraestructura
- Reglas de negocio encapsuladas
- Modelos ricos (no anémicos)

### 2. Capa de Aplicación (Application)
**Responsabilidad**: Orquesta los casos de uso del sistema.

**Servicios implementados**:
- `AssignTeacherDepartmentService`: Asigna un profesor a un departamento
- `AssignStudentCourseService`: Asigna un estudiante a un curso

**Características**:
- Coordinan entidades de dominio
- No contienen lógica de negocio (está en el dominio)
- Usan repositorios a través de interfaces

### 3. Capa de Infraestructura (Infrastructure)
**Responsabilidad**: Implementaciones técnicas y adaptadores.

**Componentes**:
- **Persistencia**: Repositorios InMemory (pueden reemplazarse por PDO/Doctrine)
- **Routing**: Router manual sin frameworks
- **Controllers**: HomeController, AssignmentController
- **Views**: Templates PHP puros

**Características**:
- Implementa interfaces del dominio
- Maneja detalles técnicos (HTTP, base de datos, etc.)
- Puede reemplazarse sin afectar el dominio

## 🚀 Instalación

### Requisitos
- PHP >= 8.0
- Composer

### Pasos

1. **Clonar/descargar el proyecto**
```bash
cd school-management
```

2. **Instalar dependencias**
```bash
composer install
```

3. **Ejecutar servidor de desarrollo**
```bash
php -S localhost:8000 -t public
```

4. **Acceder a la aplicación**
```
http://localhost:8000
```

## 🧪 Ejecutar Tests

```bash
vendor/bin/phpunit
```

### Tests Implementados

1. **AssignTeacherDepartmentServiceTest**
   - Asignación exitosa de profesor a departamento
   - Validación de profesor no encontrado
   - Validación de departamento no encontrado
   - Múltiples asignaciones

2. **AssignStudentCourseServiceTest**
   - Asignación exitosa de estudiante a curso
   - Validación de estudiante no encontrado
   - Validación de curso no encontrado
   - Reasignación de estudiante a otro curso

## 📋 Casos de Uso Implementados

### Caso de Uso 1: Asignación de Profesor a Departamento

**Secuencia**:
1. Crear User (si no existe)
2. Crear Teacher (si no existe)
3. Crear Department (si no existe)
4. Ejecutar `AssignTeacherDepartmentService`

**Ruta**: `/assign-teacher` (GET y POST)

### Caso de Uso 2: Asignación de Estudiante a Curso

**Secuencia**:
1. Crear User (si no existe)
2. Crear Student (si no existe)
3. Crear Course (si no existe)
4. Ejecutar `AssignStudentCourseService`

**Ruta**: `/assign-student` (GET y POST)

## 🌐 Rutas Disponibles

| Método | Ruta | Descripción |
|--------|------|-------------|
| GET | `/` | Portal de estudiantes (por defecto) |
| GET | `/student` | Portal de estudiantes |
| GET | `/teacher` | Portal de profesores |
| GET | `/assign-teacher` | Formulario asignación profesor |
| POST | `/assign-teacher` | Procesar asignación profesor |
| GET | `/assign-student` | Formulario asignación estudiante |
| POST | `/assign-student` | Procesar asignación estudiante |

## 🗂️ Entidades del Dominio

### User
- ID, nombre, email
- Value Object Email con validación

### Teacher
- ID, userId, especialidad, departmentId
- Método: `assignToDepartment()`

### Student
- ID, userId, número de matrícula, courseId
- Método: `assignToCourse()`

### Department
- ID, nombre, código

### Course
- ID, nombre, código, créditos

## 💾 Persistencia

El proyecto usa **repositorios InMemory** por defecto (datos en memoria).

**Ventajas**:
- No requiere configuración de base de datos
- Ideal para desarrollo y testing
- Fácil de reemplazar por repositorios PDO/Doctrine

**Para cambiar a base de datos real**:
1. Crear implementaciones PDO de las interfaces
2. Modificar el bootstrap en `public/index.php`
3. Las capas Domain y Application NO cambian

## 🎓 Principios DDD Aplicados

1. **Separación de capas**: Domain, Application, Infrastructure
2. **Inversión de dependencias**: Infraestructura depende del dominio
3. **Repositorios**: Abstracción de persistencia
4. **Entidades ricas**: Lógica en el dominio, no en servicios
5. **Value Objects**: Email con validación propia
6. **Servicios de aplicación**: Orquestan casos de uso

## 📝 Notas de Desarrollo

- **Sin frameworks**: Routing y controllers manuales
- **PSR-4**: Autoloading de clases
- **PHPUnit 10**: Framework de testing
- **Código limpio**: Separación clara de responsabilidades
- **Académico**: Estructura didáctica para aprendizaje

## 👥 Datos de Ejemplo

El sistema incluye datos iniciales:
- 4 usuarios
- 2 profesores
- 2 estudiantes
- 3 departamentos
- 3 cursos

## 🔧 Próximas Mejoras Posibles

- [ ] Persistencia con PDO/MySQL
- [ ] Validaciones más robustas
- [ ] Autenticación de usuarios
- [ ] API REST
- [ ] Más casos de uso (eliminar, actualizar)
- [ ] Frontend con JavaScript

## 📄 Licencia

Proyecto académico sin licencia específica.
