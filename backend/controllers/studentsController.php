<?php
/**
*    File        : backend/controllers/studentsController.php
*    Project     : CRUD PHP
*    Author      : Tecnologías Informáticas B - Facultad de Ingeniería - UNMdP
*    License     : http://www.gnu.org/licenses/gpl.txt  GNU GPL 3.0
*    Date        : Mayo 2025
*    Status      : Prototype
*    Iteration   : 3.0 ( prototype )
*/

require_once("./repositories/students.php");

function handleGet($conn) 
{
    if (isset($_GET['id'])) 
    {
        $student = getStudentById($conn, $_GET['id']);
        echo json_encode($student);
    } 
    //2.0
    else if (isset($_GET['page']) && isset($_GET['limit'])) 
    {
        $page = (int)$_GET['page'];
        $limit = (int)$_GET['limit'];
        $offset = ($page - 1) * $limit;

        $students = getPaginatedStudents($conn, $limit, $offset);
        $total = getTotalStudents($conn);

        echo json_encode([
            'students' => $students, // ya es array
            'total' => $total        // ya es entero
        ]);
    }
    else
    {
        $students = getAllStudents($conn); // ya es array
        echo json_encode($students);
    }
}

function handlePost($conn) 
{
    $input = json_decode(file_get_contents("php://input"), true);

    // --- INICIO DE LA MODIFICACIÓN (PASO 2) ---

    // 1. Verificar si el email ya existe usando la función del Paso 1
    $existingStudent = getStudentByEmail($conn, $input['email']);

    // 2. Comprobar el resultado
    if ($existingStudent) 
    {
        // 3a. Si $existingStudent NO es null, el email ya existe.
        // Devolvemos un error 409 (Conflict) para que el frontend lo sepa.
        http_response_code(409); 
        echo json_encode(["error" => "El email ingresado ya está registrado"]);
    } 
    else 
    {
        // 3b. Si $existingStudent ES null, el email está libre.
        // Procedemos a crear el estudiante como antes.
        $result = createStudent($conn, $input['fullname'], $input['email'], $input['age']);
        
        if ($result['inserted'] > 0) 
        {
            echo json_encode(["message" => "Estudiante agregado correctamente"]);
        } 
        else 
        {
            http_response_code(500);
            echo json_encode(["error" => "No se pudo agregar"]);
        }
    }
    // --- FIN DE LA MODIFICACIÓN ---
}

function handlePut($conn) 
{
    $input = json_decode(file_get_contents("php://input"), true);

    $result = updateStudent($conn, $input['id'], $input['fullname'], $input['email'], $input['age']);
    if ($result['updated'] > 0) 
    {
        echo json_encode(["message" => "Actualizado correctamente"]);
    } 
    else 
    {
        http_response_code(500);
        echo json_encode(["error" => "No se pudo actualizar"]);
    }
}

function handleDelete($conn) 
{
    $input = json_decode(file_get_contents("php://input"), true);

    $result = deleteStudent($conn, $input['id']);
    if ($result['deleted'] > 0) 
    {
        echo json_encode(["message" => "Eliminado correctamente"]);
    } 
    else 
    {
        http_response_code(500);
        echo json_encode(["error" => "No se pudo eliminar"]);
    }
}
?>