# Guía de uso

## Login

Por defecto, cuando el usuario se adentra en la URL, se accede a la página de inicio de sesión. Para poder acceder a la aplicación, el usuario deberá estar registrado previamente. De lo contrario, bastará con ingresar los datos correspondientes en los campos del formulario (usuario y contraseña) y pulsar el botón de Login. Si los datos son correctos, accederás a la web; de lo contrario, se mostrarán distintos errores dependiendo de la falta que se haya cometido.

## Registro

Si el usuario necesita crear una cuenta para poder acceder a la web, una vez dentro y pulsando el botón "otros" del encabezado, el usuario podrá proceder a la creación de su cuenta dentro de la herramienta. Hay que tener en cuenta que el usuario debe mantener una contraseña segura. En este caso, los campos que necesitará introducir son entre 8 y 12 caracteres, con al menos una mayúscula y un número.

## Índice principal

La interfaz principal está dividida en diferentes partes:

1. Escaneos:
En esta primera parte, el usuario podrá ver el resultado del último Nmap realizado. En este apartado se muestra la extracción obtenida tras ejecutar el comando Nmap, mostrando así las distintas partes encontradas: IP, Hostname, Port, Protocol, Device, Version y Vulnerabilities.

Además, cuenta con un botón de descarga. Si se pulsa, se procederá a la descarga del informe que se muestra en pantalla, para que el usuario pueda consultarlo y guardarlo en formato CSV. En esta fase encontramos también los horarios de programación para la realización del escaneo.

2. Descubrimientos:
Uno de los apartados más importantes dentro de la herramienta es la posibilidad de poder monitorizar cada escaneo. De tal forma que el usuario puede observar si han aparecido o desaparecido las distintas IPs o servidores que se quieran analizar.

Dividido en 3 subcategorías:

    a. Appear: Mostrará todos aquellos host nuevos que se hayan encontrado respecto al escaneo anterior.
    
    b. Lost: Señala cuáles han sido eliminados o no detectados respecto al análisis anterior.
    
    c. Stay: Muestra todos aquellos host en los que no se ha recibido ninguna novedad.

3. Historial de IPs:
Para finalizar con la vista principal, el programa cuenta con un formulario de introducción de datos, donde se deberán insertar IPs (individuales o rangos) y dominios. Bastará con pulsar el botón de enviar. Por otro lado, si el usuario no está conforme con su introducción, podrá eliminar la lista de IPs introducida. Cabe recalcar que todo lo que se quiera someter al análisis deberá ser de forma lineal, es decir, solo se puede introducir de forma seguida en una línea y no por partes. Se podrán insertar varias IPs o dominios, pero deberán estar separados por un espacio en blanco. Por ejemplo: 192.168.0.1-10 192.168.0.1/24 deusto.es santurtzi.net.

## Desconexión y cambio de contraseña

El usuario siempre tendrá la opción de desconectar su cuenta en cualquier momento, independientemente de la fase en la que se encuentre. Es importante saber que, aunque el usuario se desconecte, las programaciones que haya realizado seguirán ejecutándose. Se podrá acceder a dicha acción en la parte superior.

Tal y como se observa, también se puede acceder al cambio de contraseña del usuario actual en la sesión. Al introducir la nueva contraseña, se actualizará en la base de datos.

## Funcionalidad NmapNow

Esta funcionalidad se utiliza para realizar escaneos individuales, independientemente del escaneo programado. Es útil para analizar IPs de forma rápida y completa. Es especialmente útil para analizar direcciones IP de forma ágil.

Aquí tienes los pasos para utilizar la funcionalidad NmapNow:

1) Accede a la interfaz principal de la aplicación.
2) Busca la sección "Funcionalidad NmapNow" o una opción similar en el menú o la página principal.
3) Dentro de la funcionalidad NmapNow, encontrarás un formulario donde podrás ingresar la dirección IP que deseas escanear.
4) Ingresa la dirección IP en el formulario y haz clic en el botón "Escanear" o "Iniciar escaneo".
5) El sistema ejecutará el escaneo utilizando la herramienta Nmap y mostrará los resultados en pantalla.
6) Examina los resultados del escaneo, que pueden incluir información como puertos abiertos, protocolos utilizados, servicios detectados, vulnerabilidades, entre otros.
7) Si lo deseas, puedes descargar un informe detallado en formato CSV o guardar los resultados para futuras referencias.
8) Repite el proceso para realizar escaneos adicionales de otras direcciones IP.

Recuerda que la funcionalidad NmapNow te permite obtener información detallada sobre una dirección IP específica de manera rápida y sencilla, sin la necesidad de configurar un escaneo programado. Utilízala para obtener información relevante sobre los servicios y vulnerabilidades asociadas a una dirección IP en particular.

## Configuración de correo electrónico
La configuración del correo electrónico no está configurada por defecto, deberá ser configurada por el usuario introduciendo usuaruo y contraseña tanto de la generación de los correos como el destino.
Accede dentro de /app/excelReport.py y sigue los comentarios del archivo para proceder con la configuración.
# Ejemplo de uso básico Scheduled

    1. Introducir las IPs, dominios o rangos de IPs en el fomulario de búsqueda (separadas por espacios).
    
    2. Pulsar el boton de enviar bajo el formulario para proceder a su análisis.
    
    3. Seleccionar un tiempo para la programación de los análisis.

    4. Esperar al análisis despues de haber realizado la programación temporal.

    5. Recibir el email (Si se ha configurado).

    6. Se muestran los datos en Scheduled.


