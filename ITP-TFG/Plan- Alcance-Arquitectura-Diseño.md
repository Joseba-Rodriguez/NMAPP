# Plan - Diseño  
En cuanto al diseño se refiere, se va a tratar de simplificar y agrupar todos los bloques de tal forma que el usuario sea capaz de lanzar un comando y pueda correr dicha máquina local de forma simple. 

Para el análisis se ha investigado distintas herramientas, tales como: 
Nessus 
Netcat
Port Authority
Advanced Port Scanner
Network Scanner by MiTeC
Nmap
PortQryUI
NetScanTools
Angry IP Scanner
MASSCAN

En cuanto al analisis de ips se refiere se quiere optar por utilizar la última versión de Nmap 7.93 ya que es de código abierto, contiene todas las configuraciones necesarias para el proyecto(analisis, host, vulnerabilidades y scripts para metaexploit):
Nmap ( " Network Mapper " ) es una herramienta de código abierto para la exploración de redes y la auditoría de seguridad. Fue diseñado para escanear rápidamente grandes redes, aunque funciona bien contra hosts individuales. Nmap utiliza paquetes de IP sin procesar de formas novedosas para determinar qué hosts están disponibles en la red, qué servicios (nombre y versión de la aplicación) ofrecen esos hosts, qué sistemas operativos (y versiones de SO) están ejecutando, qué tipo de filtros de paquetes/cortafuegos están en uso, y docenas de otras características. Si bien Nmap se usa comúnmente para auditorías de seguridad, muchos administradores de sistemas y redes lo encuentran útil para tareas rutinarias como el inventario de redes, la administración de programas de actualización de servicios y el monitoreo del tiempo de actividad del host o del servicio.

El resultado de Nmap es una lista de objetivos escaneados, con información adicional sobre cada uno según las opciones utilizadas. La clave entre esa información es la " tabla de puertos interesantes " . Esa tabla enumera el número de puerto y el protocolo, el nombre del servicio y el estado. El estado es open, filtered, closedo unfiltered. Open significa que una aplicación en la máquina de destino está escuchando conexiones/paquetes en ese puerto. Filtered significa que un firewall, un filtro u otro obstáculo en la red está bloqueando el puerto para que Nmap no pueda saber si es openo closed. Closed los puertos no tienen ninguna aplicación que los escuche, aunque podrían abrirse en cualquier momento. Los puertos se clasifican como unfiltered cuando responden a las sondas de Nmap, pero Nmap no puede determinar si están abiertos o cerrados. Nmap informa las combinaciones de estado open|filtered yclosed|filtered cuando no puede determinar cuál de los dos estados describe un puerto. La tabla de puertos también puede incluir detalles de la versión del software cuando se ha solicitado la detección de la versión. Cuando se solicita un escaneo de protocolo IP ( -sO), Nmap proporciona información sobre los protocolos IP admitidos en lugar de los puertos de escucha.

Además de la interesante tabla de puertos, Nmap puede proporcionar más información sobre los objetivos, incluidos los nombres de DNS inversos, las conjeturas del sistema operativo, los tipos de dispositivos y las direcciones MAC.

Se utilizará un sistema de despliegue docker y se levantará mediante Compose, para lanzar dos servicios principalmente: 
    - 1) sistema web 
    - 2) base de datos


1) En cuanto al sistema web se refiere se utilizará Apache. Dicho servicio web observará un index.html que contendrá el código HTML5 de la página en cuestion. Este archivo contendrá diferentes partes:
  - Añadir ips o rangos de ips a inspeccionar y etiquetarlos para facilitar su gestión.
  - Configurar de las frecuencias de inspección.
  - Configurar de las notificaciones (emails)
  - Gestionar estado de sistemas, servicios y vulnerabilidades.

2) La base de datos del sistema se basará en un sistema sql denominado MySql. La utilización de este tipo de base de datos es debido a su velocidad y así cómo el software fue diseñado desde un principio, pensando principalmente en la rapidez. No es caro. MySQL es gratis bajo la licencia GPL de código abierto, y el costo por licencia comercial es muy razonable. -Fácil de usar. Está será descrita desde un Diagrama de BBDD pendiente por ver(Lucidchart, creately, VisualParadigm)


# Descripcion de alto nivel;

Para el desarrollo se va a buscar principalmente desarrollo de plataformas de Software Libre para tratar de acercarnos a lo que sería un "atacante" utilizando como sistema operativo, Linux - Ubuntu 22.04.

Durante el proyecto será esencial mantener una sincronización con el software GitHub para ir almacenando el código de forma sincrona con el proyecto y así tratar siempre el codigo por si alguien al finalizar al proyecto decide utilizarlo, este se hará público.

Tanto el codigo python3, docker, bbdd y html deberá ir acompañado de una explicación tanto por un manual README como la explicación línea por línea. Esto es así debido a que ITP necesita tener constancia de cada una de las cosas que se va a analizar con esta máquina. Así mismo estos manuales servirán para editar el proyecto si en un futuro se desea y así sea mas legible para el usuario que vaya a realizarlo. La base de datos MySql contendrá un fichero de configuración para la guia de conexión a la BBDD.

Otro dato importante en cuanto a la máquinam, se va a tratar de los logs tanto de actividad en cuanto a cuando se lanza o de para la máquina, como un control de acceso a la misma. Esta tarea la llevará a cabo el equipo de SAP para comprobar que todo usuario que consiga acceder a la máquina sea legítimo.

Existirá una ejecución Cron con la que se lanzará la máquina de forma semanal, junto con un correo eléctronico con las amenazas, descubrimientos o falta de ips. Junto con la fecha de este cron se enviará un correo electrónico al correo de ITSecurity@itpaero.com  con el que se podrá consultar todas aquellas amenazas detectadas por el análisis semanal. Para ello se utilizará Webhooks desde la web de DockerHub con la imagen del proyecto subida y con un script realizado desde script.google.com . 

Sera preciso excepcionar la dirección IP del servidor en las plataformas de busqueda de amenazas para evitar que sea bloqueada. Esto no debe hacerse enlas politicas de los Firewalls, sino que deberian aplicarse las restriciones comunes a cualquier acceso externo.


Para la realización del proyecto será necesario un plan con un desglose de tareas con un Gantt que describa los tiempos necesariosa para cada tarea. GanttProject será la elegida debido a que es de código libre, está disponible en los distintos sistemas operativos, contiene todo lo necesario para la realizacion del proyecto y es manejable y accesible. 

Se utilizará la plataforma Online Overleaf para la realizacion del proyecto en código LaTex.
