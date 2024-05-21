import dash
import dash_core_components as dcc
import dash_html_components as html
import dash_table
import plotly.graph_objs as go
import geoip2.database
import folium
import pandas as pd
import flask
import io
from dash.dependencies import Input, Output

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
        folium.Marker(location=[latitud, longitud], popup=ip).add_to(mapa)
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

# Resumen completo de los datos
total_connections = len(df)
report_time = df['ts'].max()

# Inicializar la aplicación Dash
app = dash.Dash(__name__)
app.title = "Dashboard de Ciberseguridad"

# Definir el diseño del dashboard
app.layout = html.Div([
    html.H1("Dashboard de Ciberseguridad", style={'text-align': 'center', 'color': 'white'}),

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
    ], style={'margin-bottom': '20px'}),

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
        ], style={'width': '45%', 'display': 'inline-block', 'vertical-align': 'top', 'margin': '1%'}),

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
        ], style={'width': '45%', 'display': 'inline-block', 'vertical-align': 'top', 'margin': '1%'}),

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
        ], style={'width': '45%', 'display': 'inline-block', 'vertical-align': 'top', 'margin': '1%'})
    ]),

    # Sección para mostrar detalles adicionales
    html.Div([
        # Lista de IPs con más conexiones abiertas
        html.Div([
            html.H3("Top 10 IPs con Más Conexiones Abiertas", style={'text-align': 'center', 'color': 'white'}),
            html.Ul([html.Li(ip, style={'color': 'white'}) for ip in ip_connections.index])
        ], style={'width': '45%', 'display': 'inline-block', 'vertical-align': 'top', 'margin': '1%'}),

        # Resumen completo de los datos
        html.Div([
            html.H3("Resumen Completo de los Datos", style={'text-align': 'center', 'color': 'white'}),
            html.Table([
                html.Tr([html.Th('Total de Conexiones', style={'color': 'white'}), html.Td(total_connections, style={'color': 'white'})]),
                html.Tr([html.Th('Hora del Informe', style={'color': 'white'}), html.Td(str(report_time), style={'color': 'white'})]),
            ])
        ], style={'width': '45%', 'display': 'inline-block', 'vertical-align': 'top', 'margin': '1%'}),

        # Mapa de ubicaciones de IP
        html.Div([
            html.H3("Mapa de Ubicaciones de IP", style={'text-align': 'center', 'color': 'white'}),
            html.Iframe(srcDoc=open('mapa_ips.html', 'r').read(), width='100%', height='600', style={'border': 'none'})
        ], style={'width': '90%', 'display': 'inline-block', 'vertical-align': 'top', 'margin': '1%'})
    ], style={'text-align': 'center'}),

    # Botón para descargar el dashboard completo
    html.Div([
        html.Button("Descargar Dashboard", id="btn-download", style={'backgroundColor': 'black', 'color': 'white'}),
        dcc.Download(id="download-dashboard")
    ], style={'text-align': 'center', 'margin-top': '20px'})
], style={'backgroundColor': 'black', 'font-family': 'Arial, sans-serif'})

# Callback para la descarga del dashboard completo
@app.callback(
    Output("download-dashboard", "data"),
    Input("btn-download", "n_clicks"),
    prevent_initial_call=True,
)
def download_dashboard(n_clicks):
    # Generar el HTML del dashboard completo
    dashboard_html = flask.render_template_string(app.layout.to_plotly_json())
    return dcc.send_file(io.BytesIO(dashboard_html.encode()), "dashboard.html")

# Ejecutar la aplicación
if __name__ == '__main__':
    app.run_server(debug=True, host='0.0.0.0')