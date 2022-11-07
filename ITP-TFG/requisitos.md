# Requisitos

## Descripción general

La primera fase de un ataque, tal y como se define en la matriz Mitre, es la de reconocimiento. Esto se refiere a la recogida de información sobre el objetivos, escaneando la red, identificando objetivos y extrayendo el maximo de información sobre ellos para poder realizar los ataque mas efectivos posibles.

Se ha identificado una necesidad en ITP que afecta a la visibilidad exterior de los servicios expuestos y el riesgo que estos pueden suponer. ITP dispone de multiples sedes de diferentes tipos y en diferentes ubicaciones en el mundo. Algunas de estas sedes disponen de servicios publicados a internet, siendo mantenidas por diferentes equipos. El primer paso para su adecuada protección es la visibilidad sobre la plataforma.

## Necesidades

En ITP se ha detectado una cierta falta de visibilidad en relación a lo que esta expuesto a Internet. Por ello, se pretende implantar un servicio ubicado en un entorno cloud ajeno a las redes corporativas. Este servicio debe investigar de manera periodica cual es la visibilidad y vulnerabilidad de estas redes desde el punto de vista de un hipotetico atacante externo.

El servicio que se describe en este documento se compone de los siguientes elementos:

- Un servicio de descubrimiento que, dados unos rangos de direcciones IP, identifique hosts y puertos a la escucha. Este servicio, ademas de dar una vision de los elementos expuestos, si se ejecuta de manera periodica, permitirá generar alertas en caso de que se descubran nuevos servicios (ampliando la superficie de ataque) o se detecte la desaparición de alguno.
- Un servicio de identificación, que permita conocer sistemas y versiones que se han detectado. Tambien es deseable obtener las vulnerabilidades que podrian ser explotadas en ese servicio, sean teoricas (por ser vulnerabilidades asociadas al servicio y versión identificados segun fuentes publicas) o detectadas (en el caso de que se puedan confirmar mediante una herramienta de chequeo). Este servicio debería permitir la gestión de los estados de las vulnerabilidades, para contemplar los diferentes estados por los que puede pasar (descubierta, confirmada, explotada, descartada, resuelta).
- Un servicio de explotación que permita confirmar tecnicamente los descubrimientos obtenidos. En este caso, puede no ser posible integrar en la interfaz la explotación, pero se deberían proveer las instrucciones o referencias para su ejecución manual.

Existen multiples herramientas libres que pueden cumplir estas funciones. El objetivo es integrarlas y utilizarlas, en la medida de lo posible, desde la interfaz que se crea. Concretamente, en esta fase se han utilizado como referencia las siguientes:

- Nmap para el descubrimiento de red, hosts y puertos.
- Nmap vulners para chequear las vulnerabilidades teoricas.
- Metasploit para la explotación y confirmación tecnica de las vulnerabilidades detectadas.

La primera fase del proyecto deberia incluir una fase de investigación de herramientas disponibles y una comparativa que confirme la selección inicial o identifique opciones mas adecuadas. En esta versión inicial se espera que las herramientas utilizadas sean de software libre. Las herramientas pueden ser tanto en linea de comando como librerias disponibles para el lenguaje de programación utilizado.

Fases:

- Investigación y selección de herramientas.
- Instalar servidor.
- Configuración y ajuste de herramientas.
- Desarrollo interfaz y programador.
- Configuracion y validación.

## Requisitos identificados

### Funcionales

Investigación previa:

- Investigación sobre herramientas de descubrimiento de red.
- Investigación sobre servicios publicos de información de vulnerabilidades y herramientas de consulta.
- Investigación sobre herramientas de explotación de vulnerabilidades.
- Selección de las herramientas mas adecuadas para su implementación en la herramienta.

Servicio de descubrimiento:

- Sobre un listado de ips y rangos de IPS es preciso descubrir que hosts y servicios estan publicados.
- Ejecutar periodicamente esa inspeccion segun una frecuencia configurable.
- Almacenar los elementos localizados en BBDD.
- Frente a la ultima revisión, identificar las diferencias, nuevos hosts, hosts desaparecidos, nuevos servicios...
- Generar una notificación via email indicando los cambios sobre la anterior revisión.
- Generar un informe sobre el estado de las redes.

Servicio de identificación:

- Identificar de los sistemas descubiertos, SO, servicio, software, version... Añadir esta información a la BBDD.
- Consultar la resolución inversa de las maquinas identificadas para obtener un nombre valido.
- En caso de cambios de versiones, generar una notificación especifica.
- Consultar a servicios publicos de información sobre posibles vulnerabilides que afecten a los servicios identificados.
- Gestionar los cambios de estado de sistemas y servicios, fecha de descubrimiento, cambios de versiones, identificativos...
- Gestionar los cambios de estado de vulnerabilidades, descubierta, confirmada, explotada, descartada, resuelta. 
- Generar de un informe que identifique vulnerabilidades conocidas y confirmadas con tiempos.

Servicio de explotacion:

- Identificar la herramienta o script que permita la explotación de una vulnerabilidad.
- Confirmar la explotabilidad de la vulnerabilidad.

Interfaz web:

- Interfaz que permita:
  - Añadir ips o rangos de ips a inspeccionar y etiquetarlos para facilitar su gestión.
  - Configurar de las frecuencias de inspección.
  - Configurar de las notificaciones
  - Gestionar estado de sistemas, servicios y vulnerabilidades.

### Tecnicos

- Plataforma Linux
- Herramientas de Software libre
- Instalación sencilla descargando mediante Git
- Codigo mantenible
- Fichero de configuración para conexion a BBDD
- Logs de actividad y funcionamiento
- Control de acceso

- Envio de notificaciones por email

- Documentación de instalación y mantenimiento
- Documentación para desarrolladores
- Documentación de usuario

- Sera preciso excepcionar la dirección IP del servidor en las plataformas de busqueda de amenazas para evitar que sea bloqueada. Esto no debe hacerse en las politicas de los Firewalls, sino que deberian aplicarse las restriciones comunes a cualquier acceso externo.

## Posible mejoras

A continuación se identifican posibles funcionalidades que podrian incorporarse al roadmap de la herramienta, aunque exceden la necesidad identificada inicialmente:

- Medición de disponibilidad y ancho de banda. Mediante la integración de MRTG o herramienta similar se puede ofrecer una vision de la disponibilidad de los servicios.
- Recopilación de informacion publicada en DNS, identificar servidores autoritativos, resolucion de registros especiales (DMARC, DKIM, SPF)...
- Generación de informes en formato MS Word, PDF...
- Generación de graficas que acompañen a la interfaz y los informes, timeline de hosts, timeline de vulnerabilidades, graficas de barras, contadores...

## Referencias

Guia de estilo de proyectos Python

- https://pep8.org/
- https://docs.python-guide.org/writing/structure/

