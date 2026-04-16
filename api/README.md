# School Management — API REST

API RESTful integrada en el proyecto `school-management`. Permite gestionar estudiantes, profesores, cursos y departamentos mediante peticiones HTTP estándar probables con Postman, Apidog o similar.

---

## Configuración del servidor

### Apache (recomanat)

Apunta el document root cap a la carpeta `api/` o configura un VirtualHost:

```apache
<VirtualHost *:80>
    DocumentRoot "/ruta/school-management/api"
    ServerName school-api.local

    <Directory "/ruta/school-management/api">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

El fitxer `api/.htaccess` ja redirigeix totes les peticions a `index.php`.

### PHP built-in server (per proves ràpides)

```bash
cd school-management/api
php -S localhost:8080
```

---

## Base URL

```
http://localhost:8080
```

---

## Format de resposta

Totes les respostes retornen JSON amb aquesta estructura:

```json
{
  "success": true,
  "message": "Descripció",
  "data": { ... }
}
```

En cas d'error:

```json
{
  "success": false,
  "message": "Descripció de l'error",
  "data": null
}
```

---

## Endpoints

### 📚 Students

| Mètode | URL | Descripció |
|--------|-----|-----------|
| GET | `/api/students` | Llista tots els estudiants |
| GET | `/api/students/{id}` | Obté un estudiant per ID |
| POST | `/api/students` | Crea un nou estudiant |
| PUT | `/api/students/{id}` | Actualitza un estudiant |
| DELETE | `/api/students/{id}` | Elimina un estudiant |

**POST /api/students** — Body (JSON):
```json
{
  "name": "Pau Casals",
  "email": "pau.casals@school.edu",
  "enrollment_number": "STU-2024-005"
}
```

**PUT /api/students/{id}** — Body (JSON, tots els camps opcionals):
```json
{
  "name": "Pau Casals",
  "email": "pau.casals@school.edu",
  "enrollment_number": "STU-2024-005",
  "course_id": 2
}
```

**Resposta exemple (GET /api/students/1)**:
```json
{
  "success": true,
  "message": "Student retrieved successfully",
  "data": {
    "id": 1,
    "enrollment_number": "STU-2024-001",
    "course_id": 1,
    "enrolled_at": "2024-01-15 10:00:00",
    "user": {
      "id": 3,
      "name": "Robert Brown",
      "email": "robert.brown@school.edu"
    }
  }
}
```

---

### 👩‍🏫 Teachers

| Mètode | URL | Descripció |
|--------|-----|-----------|
| GET | `/api/teachers` | Llista tots els professors |
| GET | `/api/teachers/{id}` | Obté un professor per ID |
| POST | `/api/teachers` | Crea un nou professor |
| PUT | `/api/teachers/{id}` | Actualitza un professor |
| DELETE | `/api/teachers/{id}` | Elimina un professor |

**POST /api/teachers** — Body (JSON):
```json
{
  "name": "Ada Lovelace",
  "email": "ada.lovelace@school.edu",
  "specialty": "Programming"
}
```

**PUT /api/teachers/{id}** — Body (JSON, tots els camps opcionals):
```json
{
  "name": "Ada Lovelace",
  "specialty": "Algorithms",
  "department_id": 2
}
```

---

### 📖 Courses

| Mètode | URL | Descripció |
|--------|-----|-----------|
| GET | `/api/courses` | Llista tots els cursos |
| GET | `/api/courses/{id}` | Obté un curs per ID |
| POST | `/api/courses` | Crea un nou curs |
| PUT | `/api/courses/{id}` | Actualitza un curs |
| DELETE | `/api/courses/{id}` | Elimina un curs |

**POST /api/courses** — Body (JSON):
```json
{
  "name": "Introduction to PHP",
  "code": "CS101",
  "credits": 3
}
```

---

### 🏛️ Departments

| Mètode | URL | Descripció |
|--------|-----|-----------|
| GET | `/api/departments` | Llista tots els departaments |
| GET | `/api/departments/{id}` | Obté un departament per ID |
| POST | `/api/departments` | Crea un nou departament |
| PUT | `/api/departments/{id}` | Actualitza un departament |
| DELETE | `/api/departments/{id}` | Elimina un departament |

**POST /api/departments** — Body (JSON):
```json
{
  "name": "Informatics Department",
  "code": "INF"
}
```

---

## Codis HTTP utilitzats

| Codi | Significat |
|------|-----------|
| 200 | OK — operació correcta |
| 201 | Created — recurs creat |
| 400 | Bad Request — dades incorrectes |
| 404 | Not Found — recurs no trobat |
| 409 | Conflict — ja existeix (codi/enrollment duplicat) |
| 422 | Unprocessable — error de lògica (p. ex. ID de curs inexistent) |

---

## Dades inicials (seed)

L'API arrenca amb dades de prova precarregades:

- **2 professors**: John Smith (Matemàtiques), Mary Johnson (Informàtica)
- **4 estudiants**: Robert Brown, Alice Wilson, Carlos García, Laia Puig
- **3 departaments**: MATH, CS, PHYS
- **4 cursos**: Calculus I, Data Structures, Algorithms, Linear Algebra
- Relacions pre-assignades: professors als seus departaments, 2 estudiants als seus cursos

---

## Estructura de fitxers afegits

```
school-management/
├── api/
│   ├── index.php              ← Entry point de l'API
│   └── .htaccess              ← Rewrite rules per Apache
└── src/
    └── Infrastructure/
        └── Http/
            ├── Request.php            ← Parseja HTTP request
            ├── ResponseJson.php       ← Respostes JSON estandarditzades
            ├── Routing/
            │   └── ApiRouter.php      ← Router amb suport {params}
            └── Controllers/
                ├── StudentsController.php
                ├── TeachersController.php
                ├── CoursesController.php
                └── DepartmentsController.php
```
