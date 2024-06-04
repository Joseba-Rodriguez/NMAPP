import dash
import dash_core_components as dcc
import dash_html_components as html
import dash_table
import plotly.graph_objs as go
import plotly.express as px
import geoip2.database
import folium
import pandas as pd

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
cve_summary = {
    'total_conexiones': len(df),
    'total_vulnerabilidades': sum([len(cves) for cves in df['cve_list']]),
    'vulnerabilidades_por_conexion': sum([len(cves) for cves in df['cve_list']]) / len(df),
    'vulnerabilidades_criticas': sum([sum([float(cve[1]) >= 7.0 for cve in cves]) for cves in df['cve_list']])
}

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
ip_connections = df['ip'].value_counts().head(10)

# Calcular las vulnerabilidades por hora
df['ts'] = pd.to_datetime(df['ts'])
vulns_by_hour = df.resample('H', on='ts').size().reset_index(name='count')

# Resumen completo de los datos
total_connections = len(df)
report_time = df['ts'].max()

# Inicializar la aplicación Dash
app = dash.Dash(__name__)
app.title = "Dashboard de Ciberseguridad"

# Definir el diseño del dashboard
app.layout = html.Div([
    html.H1("Dashboard de Ciberseguridad", style={'textAlign': 'center', 'color': 'white'}),

    # Sección para mostrar el resumen de vulnerabilidades
    html.Div([
        html.H2("Resumen de Vulnerabilidades", style={'color': 'white'}),
        dash_table.DataTable(
            columns=[
                {'name': 'Total de Conexiones', 'id': 'total_conexiones'},
                {'name': 'Total de Vulnerabilidades', 'id': 'total_vulnerabilidades'},
                {'name': 'Promedio de Vulnerabilidades por Conexión', 'id': 'vulnerabilidades_por_conexion'},
                {'name': 'Vulnerabilidades Críticas', 'id': 'vulnerabilidades_criticas'}
            ],
            data=[cve_summary],
            style_table={'width': '50%', 'margin': 'auto'},
            style_header={'backgroundColor': 'black', 'color': 'white'},
            style_cell={'backgroundColor': 'grey', 'color': 'white'}
        ),
    ], style={'marginBottom': '20px'}),

    # Sección para mostrar los gráficos
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
        ], style={'width': '45%', 'display': 'inline-block', 'verticalAlign': 'top', 'margin': '1%'}),

        # Gráfico de CVEs más frecuentes con criticidad
        html.Div([
            dcc.Graph(
                figure=go.Figure(
                    data=[
                        go.Bar(
                            x=top_cves['cve'],
                            y=top_cves['count'],
                            marker=dict(color=top_cves['score'], colorscale='Viridis'),
                            text=top_cves['score'],
                            hoverinfo='text'
                        )
                    ],
                    layout=go.Layout(
                        title="Top 10 CVEs Más Frecuentes con Criticidad",
                        paper_bgcolor='black',
                        plot_bgcolor='black',
                        font=dict(color='white')
                    )
                )
            )
        ], style={'width': '45%', 'display': 'inline-block', 'verticalAlign': 'top', 'margin': '1%'}),

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
        ], style={'width': '45%', 'display': 'inline-block', 'verticalAlign': 'top', 'margin': '1%'}),

        # Gráfico de vulnerabilidades por hora
        html.Div([
            dcc.Graph(
                figure=go.Figure(
                    data=[
                        go.Scatter(
                            x=vulns_by_hour['ts'],
                            y=vulns_by_hour['count'],
                            mode='lines+markers',
                            marker_color='rgb(255, 127, 14)'
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
        ], style={'width': '45%', 'display': 'inline-block', 'verticallign': 'top', 'margin': '1%'}),
    ]),

    # Sección para mostrar la tabla de conexiones por IP
    html.Div([
        html.H2("Top 10 IPs con Más Conexiones Abiertas", style={'color': 'white'}),
        dash_table.DataTable(
            columns=[
                {'name': 'IP', 'id': 'ip'},
                {'name': 'Conexiones Abiertas', 'id': 'count'}
            ],
            data=ip_connections.reset_index().rename(columns={'index': 'ip', 'ip': 'count'}).to_dict('records'),
            style_table={'width': '20%', 'margin': 'auto'},
            style_header={'backgroundColor': 'black', 'color': 'white'},
            style_cell={'backgroundColor': 'grey', 'color': 'white'}
        ),
    ], style={'marginTop': '20px'}),

    # Mapa interactivo de IPs
    html.Div([
        html.H2("Mapa Interactivo de IPs", style={'color': 'white', 'textAlign': 'center'}),
        html.Iframe(id='map', srcDoc=open('mapa_ips.html', 'r').read(), width='100%', height='600')
    ], style={'marginTop': '20px'}),
])

# Ejecutar la aplicación
if __name__ == '__main__':
    app.run_server(debug=True, host='0.0.0.0')
