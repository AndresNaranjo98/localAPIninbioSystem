var ejercicio = [
  {
    "id": 1,
    "nombre": "Rafael",
    "sueldo": 1300,
  },
  {
    "id": 2,
    "nombre": "Andres",
    "sueldo": 3300,
  },
  {
    "id": 3,
    "nombre": "Martin",
    "sueldo": 4300,
  }
]

suma = 0;
empleadoMayor = "";
prom = 0;
let promedio = ejercicio.forEach((valor) => {
    suma += valor["sueldo"];
    prom = (suma)/ejercicio.length;
});


let mayor = ejercicio.forEach((valor) => {
if(valor.sueldo > prom){
    empleadoMayor += valor.nombre;
}
});
console.log("El Sueldo Promedio De Los Trabajadores Es: "+prom);
console.log("Empleado(s) Con Un Sueldo Mayor Al Promedio Son: "+empleadoMayor);