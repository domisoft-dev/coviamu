<?php
/*
================================================================================
* Project:       https://github.com/domisoft-dev/coviamu
* File:          database.php
* Author:        domisoft-dev
* Description:   Clase para la conexión a la base de datos MySQL del sistema COVIAMU.
================================================================================
* Classes:
* - Database
*   - __construct(): inicializa la conexión a la base de datos usando mysqli.
*   - $conn: propiedad pública que almacena la conexión activa.
================================================================================
* Notes:
* - Se detiene la ejecución si la conexión falla mostrando el error.
================================================================================
* Libraries: None
================================================================================
*/

class Database {
    public $conn;

    public function __construct() {
        $this->conn = new mysqli("localhost","root","","Domisoft");
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }
}