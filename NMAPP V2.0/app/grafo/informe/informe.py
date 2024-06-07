import dash
import dash_core_components as dcc
import dash_html_components as html
import dash_table
import plotly.graph_objs as go
import pandas as pd
import geoip2.database
import folium

# Leer el archivo CSV
df = pd.read_csv('/usr/src/app/app/grafo/lastanalyze_Scan.csv')

# Convertir NaN en la columna 'cve_str' a cadenas vacías
df['cve_str'] = df['cve_str'].fillna('')

# Crear un objeto de la base de datos GeoLite2
reader = geoip2.database.Reader('/usr/src/app/app/grafo/informe/GeoLite2-City.mmdb')

# Obtener la lista de direcciones IP del CSV
ip_list = df['ip'].tolist()

# Crear un mapa centrado en una ubicación inicial
mapa = folium.Map(location=[0, 0], zoom_start=2, tiles='cartodbdark_matter')

# Iterar sobre cada dirección IP en la lista y añadir marcadores al mapa
for ip in ip_list:
    try:
        response = reader.city(ip)
        latitud = response.location.latitude
        longitud = response.location.longitude
        folium.CircleMarker(
            location=[latitud, longitud],
            radius=5,
            popup=f"{ip}",
            color='red' if df[df['ip'] == ip]['cve_str'].str.contains('CVE').any() else 'green',
            fill=True,
            fill_color='red' if df[df['ip'] == ip]['cve_str'].str.contains('CVE').any() else 'green'
        ).add_to(mapa)
    except Exception as e:
        print(f"Error obteniendo coordenadas para la IP {ip}: {str(e)}")

# Guardar el mapa como un archivo HTML
mapa.save('mapa_ips.html')

# Procesar la columna 'cve_str' para obtener CVEs, criticidad y enlaces
df['cve_list'] = df['cve_str'].apply(lambda x: [cve.split('\t') for cve in x.split(';') if cve])

# Calcular el resumen de vulnerabilidades
total_connections = len(df)
total_vulnerabilities = sum([len(cves) for cves in df['cve_list']])
critical_vulnerabilities = sum([sum([float(cve[1]) >= 7.0 for cve in cves]) for cves in df['cve_list']])

vulnerability_summary = [
    {'category': 'Total Conexiones', 'value': total_connections},
    {'category': 'Total Vulnerabilidades', 'value': total_vulnerabilities},
    {'category': 'Vulnerabilidades Críticas', 'value': critical_vulnerabilities},
]

# Calcular los puertos más comunes y servicios asociados
top_ports_services = df.groupby(['port', 'service']).size().reset_index(name='count').sort_values(by='count', ascending=False).head(10)

# Calcular las CVEs más frecuentes y su criticidad
cve_counts = df['cve_list'].explode().dropna().apply(pd.Series).rename(columns={0: 'cve', 1: 'score', 2: 'link'})
cve_counts['score'] = cve_counts['score'].astype(float)
top_cves = cve_counts['cve'].value_counts().reset_index()
top_cves.columns = ['cve', 'count']
top_cves = top_cves.merge(cve_counts[['cve', 'score']].drop_duplicates(), on='cve').sort_values(by='count', ascending=False).head(10)

# Calcular los puertos con más vulnerabilidades
port_vulns = df.explode('cve_list').groupby('port').size().reset_index(name='count').sort_values(by='count', ascending=False).head(10)


# Calcular las IPs con más conexiones abiertas
ip_connections = df['ip'].value_counts().head(10).reset_index()
ip_connections.columns = ['ip', 'count']

# Calcular las vulnerabilidades por hora
df['ts'] = pd.to_datetime(df['ts'])
vulns_by_hour = df.resample('h', on='ts').size().reset_index(name='count')

# Función para asignar colores según la criticidad de la CVE
def get_color(score):
    if score >= 8.0:
        return 'red'
    elif score >= 7.0:
        return 'orange'
    elif score >= 4.0:
        return 'yellow'
    else:
        return 'green'

# Aplicar colores según la criticidad
top_cves['color'] = top_cves['score'].apply(get_color)

# Inicializar la aplicación Dash
app = dash.Dash(__name__)
app.title = "Análisis de Vulnerabilidades de Red con Nmap"

# Definir el estilo general del dashboard
external_stylesheets = ['https://codepen.io/chriddyp/pen/bWLwgP.css']

# Definir el diseño del dashboard
app.layout = html.Div([
    html.H1("NMAPP Dashboard report", style={'color': 'black', 'textAlign': 'center', 'fontFamily': 'Arial, sans-serif', 'fontSize': '32px', 'fontWeight': 'bold'}),
    # Mostrar el archivo HTML del mapa
    html.Div([
        html.Iframe(srcDoc=open('mapa_ips.html', 'r').read(), width='100%', height='600')
    ]),
    # Sección para mostrar las tablas en la misma fila
    html.Div([
        # Tabla de IPs con más conexiones abiertas
        html.Div([
            html.H2("Top 10 IPs con Más Conexiones Abiertas", style={'color': 'black', 'textAlign': 'center', 'fontFamily': 'Arial, sans-serif', 'fontSize': '24px', 'fontWeight': 'bold'}),
            dash_table.DataTable(
                columns=[
                    {'name': 'IP', 'id': 'ip'},
                    {'name': 'Conexiones Abiertas', 'id': 'count'}
                ],
                data=ip_connections.to_dict('records'),
                style_table={'width': '100%', 'margin': 'auto'},
                style_header={'backgroundColor': 'black', 'color': 'white'},
                style_cell={'backgroundColor': 'grey', 'color': 'white'},
                page_size=10,
                page_action='native'
            ),
        ], style={'float': 'left','width': '30%', 'display': 'inline-block', 'verticalAlign': 'top', 'padding': '30px'}),

        html.Div([
            html.H2("CVEs por IP", style={'color': 'black', 'textAlign': 'center', 'fontFamily': 'Arial, sans-serif', 'fontSize': '24px', 'fontWeight': 'bold'}),
            dash_table.DataTable(
                columns=[
                    {'name': 'IP', 'id': 'ip'},
                    {'name': 'CVEs', 'id': 'cve_str', 'presentation': 'markdown'}
                ],
                data=df[['ip', 'cve_str']].to_dict('records'),
                style_table={'width': '100%', 'margin': 'auto', 'overflowX': 'auto'},  # Límite horizontal
                style_header={'backgroundColor': 'black', 'color': 'white'},
                style_cell={'backgroundColor': 'grey', 'color': 'white'},
                page_size=10,  # Mostrar solo 10 filas a la vez
                page_action='native',  # Habilitar paginación
            ),
        ], style={'float': 'left','width': '30%', 'display': 'inline-block', 'verticalAlign': 'top', 'padding': '30px'}),

        # Tabla de resumen de vulnerabilidades
        html.Div([
            html.H2("Resumen de Vulnerabilidades", style={'color': 'black', 'textAlign': 'center', 'fontFamily': 'Arial, sans-serif', 'fontSize': '24px', 'fontWeight': 'bold'}),
            dash_table.DataTable(
                columns=[
                    {'name': 'Categoría', 'id': 'category'},
                    {'name': 'Valor', 'id': 'value'}
                ],
                data=vulnerability_summary,
                style_table={'width': '100%', 'margin': 'auto'},
                style_header={'backgroundColor': 'black', 'color': 'white'},
                style_cell={'backgroundColor': 'grey', 'color': 'white'}
                
            ),
        ], style={'float': 'left','width': '30%', 'display': 'inline-block', 'verticalAlign': 'top', 'padding': '30px'}),
    ]),

    html.Div([
        # Gráfico de puertos más comunes
        html.Div([
            dcc.Graph(
                figure=go.Figure(
                    data=[
                        go.Bar(
                            x=top_ports_services['port'].astype(str) + " (" + top_ports_services['service'] + ")",
                            y=top_ports_services['count'],
                            marker_color='rgb(26, 118, 255)'
                        )
                    ],
                    layout=go.Layout(
                        title="Top 10 Puertos Más Comunes y sus Servicios",
                        paper_bgcolor='black',
                        plot_bgcolor='black',
                        font=dict(color='white')
                    )
                )
            )
        ], style={'width': '50%', 'display': 'inline-block', 'verticalAlign': 'top'}),
        
        # Gráfico de puertos con más vulnerabilidades
        html.Div([
            dcc.Graph(
                figure=go.Figure(
                    data=[
                        go.Bar(
                            x=port_vulns['port'].astype(str),
                            y=port_vulns['count'],
                            marker_color='rgb(55, 83, 109)'
                        )
                    ],
                    layout=go.Layout(
                        title="Top 10 Puertos con Más Vulnerabilidades",
                        paper_bgcolor='black',
                        plot_bgcolor='black',
                        font=dict(color='white')
                    )
                )
            )
        ], style={'width': '50%', 'display': 'inline-block', 'verticalAlign': 'top'}),


        # Gráfico de CVEs más frecuentes con criticidad
        html.Div([
            dcc.Graph(
                figure=go.Figure(
                    data=[
                        go.Bar(
                            x=top_cves['cve'],
                            y=top_cves['count'],
                            marker=dict(color=top_cves['color']),
                            name='Cantidad',
                            hoverinfo='text',
                            text=top_cves['score'],
                        )
                    ],
                    layout=go.Layout(
                        title="Top 10 CVEs Más Frecuentes con Criticidad",
                        paper_bgcolor='black',
                        plot_bgcolor='black',
                        font=dict(color='white'),
                        barmode='stack',  # Apilar las barras
                        xaxis={'title': 'CVE'},  # Título del eje X
                        yaxis={'title': 'Cantidad'},  # Título del eje Y
                    )
                )
            )
        ], style={'width': '100%', 'display': 'inline-block', 'verticalAlign': 'top'}),

    ]),

    # Sección para el gráfico de vulnerabilidades por hora
    html.Div([
        dcc.Graph(
            figure=go.Figure(
                data=[
                    go.Scatter(
                        x=vulns_by_hour['ts'],
                        y=vulns_by_hour['count'],
                        mode='lines',
                        line=dict(color='rgb(26, 118, 255)')
                    )
                ],
                layout=go.Layout(
                    title="Vulnerabilidades por Hora",
                    paper_bgcolor='black',
                    plot_bgcolor='black',
                    font=dict(color='white')
                )
            )
        )
    ], style={'width': '100%', 'display': 'inline-block', 'verticalAlign': 'top', 'margin': '%'})


])

if __name__ == '__main__':
    app.run_server(debug=True, host='0.0.0.0', port=8050)
