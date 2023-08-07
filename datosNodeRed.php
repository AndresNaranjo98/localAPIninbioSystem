<?php
class Herramienta{
    
    require_once 'conEjemplo.php';
    $conexion = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);

	public function ingresar_datos($brix, $temperatura, $ph, $alcvol, $eficiencia, $volumen){
		$sql = "INSERT INTO monitoreo values (?, ?, ?, ?, ?, ?)";
		$stmt = $conexion->prepare($sql);

		$stmt->bindValue(1, $brix);
		$stmt->bindValue(2, $temperatura);
		$stmt->bindValue(3, $ph);
		$stmt->bindValue(4, $alcvol);
        $stmt->bindValue(5, $eficiencia);
        $stmt->bindValue(6, $volumen);

		if($stmt->execute()){
			echo "Ingreso Exitoso";
		}else{
			echo "no se pudo registrar datos";
		}
	}
}
?>