<?php
function conectar(){

$servidor='localhost';
$usuario='root';
$password='';
$bd='smtp';
$conexion=new mysqli($servidor,$usuario,$password,$bd);
if($conexion->connect_errno){
    echo "error al conectarse{ $conexion->connect_errno}";

}
return $conexion;
}
?>