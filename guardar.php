<?php
$servername = "localhost"; // Cambia esto si tu base de datos no está en el mismo servidor
$username = "root"; // Cambia por tu usuario de la base de datos
$password = ""; // Cambia por tu contraseña de la base de datos
$dbname = "agenda_db"; // Cambia por el nombre de tu base de datos

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$html_results = "";

// Verificar si se ha enviado el formulario de guardar
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['curp']) && !isset($_POST['buscar_curp'])) {
    // Obtener datos del formulario
    $curp = $_POST['curp'];
    $nombre = $_POST['nombre'];
    $apellidos = $_POST['apellidos'];
    $domicilio = $_POST['domicilio'];
    $telefono_casa = $_POST['telefono_casa'];
    $celular = $_POST['celular'];
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $email = $_POST['email'];

    // Verificar si la CURP ya existe
    $sql = "SELECT * FROM contactos WHERE curp='$curp'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $html_results .= "<div class='record'>Error: La CURP ya existe</div>";
    } else {
        // Insertar datos en la base de datos
        $sql = "INSERT INTO contactos (curp, nombre, apellidos, domicilio, telefono_casa, celular, fecha_nacimiento, email)
                VALUES ('$curp', '$nombre', '$apellidos', '$domicilio', '$telefono_casa', '$celular', '$fecha_nacimiento', '$email')";

        if ($conn->query($sql) === TRUE) {
            $html_results .= "<div class='record'>Nuevo contacto guardado exitosamente</div>";
        } else {
            $html_results .= "<div class='record'>Error: " . $sql . "<br>" . $conn->error . "</div>";
        }
    }
}

// Verificar si se ha enviado el formulario de búsqueda
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['buscar_curp'])) {
    // Obtener CURP para buscar
    $buscar_curp = $_POST['buscar_curp'];

    // Buscar registro en la base de datos
    $sql = "SELECT * FROM contactos WHERE curp='$buscar_curp'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Mostrar resultados
        while($row = $result->fetch_assoc()) {
            $html_results .= "<div class='record'>";
            $html_results .= "<strong>CURP:</strong> " . $row["curp"] . "<br>";
            $html_results .= "<strong>Nombre:</strong> " . $row["nombre"] . " " . $row["apellidos"] . "<br>";
            $html_results .= "<strong>Domicilio:</strong> " . $row["domicilio"] . "<br>";
            $html_results .= "<strong>Teléfono de Casa:</strong> " . $row["telefono_casa"] . "<br>";
            $html_results .= "<strong>Celular:</strong> " . $row["celular"] . "<br>";
            $html_results .= "<strong>Fecha de Nacimiento:</strong> " . $row["fecha_nacimiento"] . "<br>";
            $html_results .= "<strong>Correo Electrónico:</strong> " . $row["email"] . "<br>";
            $html_results .= "</div>";
        }
    } else {
        $html_results .= "<div class='record'>No se encontraron resultados para la CURP: $buscar_curp</div>";
    }
}

// Mostrar todos los contactos
$sql = "SELECT curp FROM contactos";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $html_results .= "<div class='record'>";
        $html_results .= "<strong>CURP:</strong> " . $row["curp"] . "<br>";
        $html_results .= "</div>";
    }
} else {
    $html_results .= "<div class='record'>No hay contactos guardados.</div>";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agenda</title>
    <style>
        body {
            font-family: Times;
            background-color: #48E1E7;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
      .container {
    background-color: #E8E8E8;
    padding: 40px;
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.15);
    max-width: 900px;
    width: 100%;
    margin: 20px auto;
    display: flex;
    flex-direction: column;
    align-items: stretch;
}
        }
        .form-section, .records-section {
            flex: 1;
            margin: 0 10px;
        }
        .form-section {
            margin-right: 20px;
        }
        h1, h2 {
            text-align: center;
            color: #0E4487;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            margin-bottom: 5px;
            color: #555;
        }
        input {
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }
        button {
            padding: 10px;
            background-color: #B73A3A;
            border: none;
            border-radius: 4px;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover {
            background-color: #EFD25F;
        }
        .results-section {
            margin-top: 20px;
        }
        .record {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-section">
            <h1>Formulario de Agenda</h1>
            <form action="guardar.php" method="post">
                <label for="curp">CURP:</label>
                <input type="text" id="curp" name="curp" required>
                
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" required>
                
                <label for="apellidos">Apellidos:</label>
                <input type="text" id="apellidos" name="apellidos" required>
                
                <label for="domicilio">Domicilio:</label>
                <input type="text" id="domicilio" name="domicilio" required>
                
                <label for="telefono_casa">Teléfono de Casa:</label>
                <input type="tel" id="telefono_casa" name="telefono_casa">
                
                <label for="celular">Celular:</label>
                <input type="tel" id="celular" name="celular" required>
                
                <label for="fecha_nacimiento">Fecha de Nacimiento:</label>
                <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" required>
                
                <label for="email">Correo Electrónico:</label>
                <input type="email" id="email" name="email" required>
                
                <button type="submit">Guardar</button>
            </form>

            <div class="search-section">
                <h2>Buscar Registro</h2>
                <form action="guardar.php" method="post">
                    <label for="buscar_curp">Buscar por CURP:</label>
                    <input type="text" id="buscar_curp" name="buscar_curp" required>
                    <button type="submit">Buscar</button>
                </form>
            </div>
        </div>

        <div class="records-section">
            <h2>Contactos Guardados</h2>
            <div id="results">
                <?php echo $html_results; ?>
            </div>
        </div>
    </div>
</body>
</html>