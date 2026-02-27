<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asignar Student a Course</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            border-bottom: 3px solid #4CAF50;
            padding-bottom: 10px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #45a049;
        }
        .message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .nav {
            margin: 20px 0;
        }
        .nav a {
            color: #4CAF50;
            text-decoration: none;
        }
        .nav a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Asignar Student a Course</h1>

        <?php if (isset($message)): ?>
            <div class="message <?= strpos($message, 'Error') !== false ? 'error' : 'success' ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="/assign-student">
            <div class="form-group">
                <label for="student_id">Selecciona Student:</label>
                <select name="student_id" id="student_id" required>
                    <option value="">-- Escoge un student --</option>
                    <?php foreach ($students as $student): ?>
                        <option value="<?= $student->getId() ?>">
                            Student ID: <?= $student->getId() ?> - Enrollment: <?= htmlspecialchars($student->getEnrollmentNumber()) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="course_id">Selecciona Course:</label>
                <select name="course_id" id="course_id" required>
                    <option value="">-- Escoge un course --</option>
                    <?php foreach ($courses as $course): ?>
                        <option value="<?= $course->getId() ?>">
                            <?= htmlspecialchars($course->getName()) ?> (<?= htmlspecialchars($course->getCode()) ?>) - <?= $course->getCredits() ?> credits
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit">Asigna Student</button>
        </form>

        <div class="nav">
            <a href="/student">← Atras</a>
        </div>
    </div>
</body>
</html>
