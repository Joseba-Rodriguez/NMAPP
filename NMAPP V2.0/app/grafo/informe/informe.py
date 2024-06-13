import dash
import dash_core_components as dcc
import dash_html_components as html
import dash_table
import plotly.graph_objs as go
import pandas as pd
import geoip2.database
import folium
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
if df['cve_list'].explode().dropna().empty:
    cve_counts = pd.DataFrame(columns=['cve', 'score', 'link'])
else:
    cve_counts = df['cve_list'].explode().dropna().apply(pd.Series)
    cve_counts.columns = ['cve', 'score', 'link']
    cve_counts['score'] = cve_counts['score'].astype(float)

top_cves = cve_counts['cve'].value_counts().reset_index()
top_cves.columns = ['cve', 'count']
if not cve_counts.empty:
    top_cves = top_cves.merge(cve_counts[['cve', 'score']].drop_duplicates(), on='cve').sort_values(by='count', ascending=False).head(10)
else:
    top_cves = pd.DataFrame(columns=['cve', 'count', 'score'])

# Calcular los puertos con más vulnerabilidades
port_vulns = df.explode('cve_list').groupby('port').size().reset_index(name='count').sort_values(by='count', ascending=False).head(10)

# Calcular las IPs con más conexiones abiertas
ip_connections = df['ip'].value_counts().head(10).reset_index()
ip_connections.columns = ['ip', 'count']

# Calcular las vulnerabilidades por hora
df['ts'] = pd.to_datetime(df['ts'])
vulns_by_hour = df.resample('h', on='ts').size().reset_index(name='count')

# Calcular el porcentaje de IPs con vulnerabilidad y sin vulnerabilidad
ips_with_vulnerabilities = df[df['cve_str'].str.contains('CVE')]['ip'].nunique()
ips_without_vulnerabilities = df[~df['cve_str'].str.contains('CVE')]['ip'].nunique()
total_ips = df['ip'].nunique()
percent_with_vulns = (ips_with_vulnerabilities / total_ips) * 100
percent_without_vulns = (ips_without_vulnerabilities / total_ips) * 100

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
if not top_cves.empty:
    top_cves['color'] = top_cves['score'].apply(get_color)
else:
    top_cves['color'] = []

# Inicializar la aplicación Dash
app = dash.Dash(__name__)
app.title = "Análisis de Vulnerabilidades de Red con Nmap"

# Definir el estilo general del dashboard
app.layout = html.Div([
    html.H1("NMAPP Dashboard Report", style={
        'color': '#FFFFFF', 'textAlign': 'center', 'fontFamily': 'Arial, sans-serif', 'fontSize': '32px', 'fontWeight': 'bold',
        'backgroundColor': '#2E2E2E', 'padding': '20px', 'borderRadius': '10px'
    }),
    html.Div([
        html.Iframe(srcDoc=open('mapa_ips.html', 'r').read(), width='100%', height='600', style={'border': '2px solid #FFFFFF', 'borderRadius': '10px'})
    ], style={'margin': '20px auto', 'width': '90%'}),

    html.Div([
        html.H2("Filtrar por IP", style={
            'color': '#FFFFFF', 'textAlign': 'center', 'fontFamily': 'Arial, sans-serif', 'fontSize': '24px', 'fontWeight': 'bold',
            'backgroundColor': '#2E2E2E', 'padding': '10px', 'borderRadius': '10px'
        }),
        dcc.Dropdown(
            id='ip-dropdown',
            options=[{'label': ip, 'value': ip} for ip in df['ip'].unique()],
            placeholder='Selecciona una IP',
            style={'width': '50%', 'margin': 'auto', 'color': '#000000'}
        )
    ], style={'textAlign': 'center', 'margin': '20px auto', 'width': '90%'}),

    html.Div([
        html.Div([
            html.H2("Top 10 IPs con Más Conexiones Abiertas", style={
                'color': '#FFFFFF', 'textAlign': 'center', 'fontFamily': 'Arial, sans-serif', 'fontSize': '24px', 'fontWeight': 'bold',
                'backgroundColor': '#2E2E2E', 'padding': '10px', 'borderRadius': '10px'
            }),
            dash_table.DataTable(
                id='ip-connections-table',
                columns=[
                    {'name': 'IP', 'id': 'ip'},
                    {'name': 'Conexiones Abiertas', 'id': 'count'}
                ],
                data=ip_connections.to_dict('records'),
                style_table={'width': '100%', 'margin': 'auto'},
                style_header={'backgroundColor': '#2E2E2E', 'color': '#FFFFFF'},
                style_cell={'backgroundColor': '#1E1E1E', 'color': '#FFFFFF'},
                page_size=10,
                page_action='native'
            ),
        ], style={'float': 'left', 'width': '45%', 'display': 'inline-block', 'verticalAlign': 'top', 'padding': '30px', 'backgroundColor': '#2E2E2E', 'borderRadius': '10px', 'boxShadow': '0 4px 8px rgba(0, 0, 0, 0.2)'}),

        html.Div([
            html.H2("CVEs por IP", style={
                'color': '#FFFFFF', 'textAlign': 'center', 'fontFamily': 'Arial, sans-serif', 'fontSize': '24px', 'fontWeight': 'bold',
                'backgroundColor': '#2E2E2E', 'padding': '10px', 'borderRadius': '10px'
            }),
            dash_table.DataTable(
                id='ip-cves-table',
                columns=[
                    {'name': 'IP', 'id': 'ip'},
                    {'name': 'CVEs', 'id': 'cve_str'}
                ],
                data=[],
                style_table={'width': '100%', 'margin': 'auto'},
                style_header={'backgroundColor': '#2E2E2E', 'color': '#FFFFFF'},
                style_cell={'backgroundColor': '#1E1E1E', 'color': '#FFFFFF'},
                page_size=10,
                page_action='native'
            )
        ], style={'float': 'right', 'width': '45%', 'display': 'inline-block', 'verticalAlign': 'top', 'padding': '30px', 'backgroundColor': '#2E2E2E', 'borderRadius': '10px', 'boxShadow': '0 4px 8px rgba(0, 0, 0, 0.2)'})
    ], style={'display': 'flex', 'justifyContent': 'space-between', 'width': '90%', 'margin': 'auto', 'marginTop': '20px'}),

    html.Div([
        html.Div([
            html.H2("Top 10 Puertos y Servicios", style={
                'color': '#FFFFFF', 'textAlign': 'center', 'fontFamily': 'Arial, sans-serif', 'fontSize': '24px', 'fontWeight': 'bold',
                'backgroundColor': '#2E2E2E', 'padding': '10px', 'borderRadius': '10px'
            }),
            dash_table.DataTable(
                id='ports-services-table',
                columns=[
                    {'name': 'Puerto', 'id': 'port'},
                    {'name': 'Servicio', 'id': 'service'},
                    {'name': 'Conexiones', 'id': 'count'}
                ],
                data=top_ports_services.to_dict('records'),
                style_table={'width': '100%', 'margin': 'auto'},
                style_header={'backgroundColor': '#2E2E2E', 'color': '#FFFFFF'},
                style_cell={'backgroundColor': '#1E1E1E', 'color': '#FFFFFF'},
                page_size=10,
                page_action='native'
            ),
        ], style={'float': 'left', 'width': '45%', 'display': 'inline-block', 'verticalAlign': 'top', 'padding': '30px', 'backgroundColor': '#2E2E2E', 'borderRadius': '10px', 'boxShadow': '0 4px 8px rgba(0, 0, 0, 0.2)'}),

        html.Div([
            html.H2("Top 10 CVEs", style={
                'color': '#FFFFFF', 'textAlign': 'center', 'fontFamily': 'Arial, sans-serif', 'fontSize': '24px', 'fontWeight': 'bold',
                'backgroundColor': '#2E2E2E', 'padding': '10px', 'borderRadius': '10px'
            }),
            dash_table.DataTable(
                id='top-cves-table',
                columns=[
                    {'name': 'CVE', 'id': 'cve'},
                    {'name': 'Frecuencia', 'id': 'count'},
                    {'name': 'Puntuación', 'id': 'score'}
                ],
                data=top_cves.to_dict('records'),
                style_table={'width': '100%', 'margin': 'auto'},
                style_header={'backgroundColor': '#2E2E2E', 'color': '#FFFFFF'},
                style_cell={'backgroundColor': '#1E1E1E', 'color': '#FFFFFF'},
                page_size=10,
                page_action='native',
                style_data_conditional=[
                    {
                        'if': {'column_id': 'score', 'filter_query': '{score} >= 8'},
                        'backgroundColor': 'red',
                        'color': 'white'
                    },
                    {
                        'if': {'column_id': 'score', 'filter_query': '{score} >= 7 && {score} < 8'},
                        'backgroundColor': 'orange',
                        'color': 'white'
                    },
                    {
                        'if': {'column_id': 'score', 'filter_query': '{score} >= 4 && {score} < 7'},
                        'backgroundColor': 'yellow',
                        'color': 'black'
                    },
                    {
                        'if': {'column_id': 'score', 'filter_query': '{score} < 4'},
                        'backgroundColor': 'green',
                        'color': 'white'
                    }
                ]
            )
        ], style={'float': 'right', 'width': '45%', 'display': 'inline-block', 'verticalAlign': 'top', 'padding': '30px', 'backgroundColor': '#2E2E2E', 'borderRadius': '10px', 'boxShadow': '0 4px 8px rgba(0, 0, 0, 0.2)'})
    ], style={'display': 'flex', 'justifyContent': 'space-between', 'width': '90%', 'margin': 'auto', 'marginTop': '20px'}),

    html.Div([
        html.H2("Vulnerabilidades por Hora", style={
            'color': '#FFFFFF', 'textAlign': 'center', 'fontFamily': 'Arial, sans-serif', 'fontSize': '24px', 'fontWeight': 'bold',
            'backgroundColor': '#2E2E2E', 'padding': '10px', 'borderRadius': '10px'
        }),
        dcc.Graph(
            id='vulns-by-hour-graph',
            figure={
                'data': [
                    go.Scatter(
                        x=vulns_by_hour['ts'],
                        y=vulns_by_hour['count'],
                        mode='lines+markers',
                        marker={'color': 'red'}
                    )
                ],
                'layout': go.Layout(
                    title='Número de Vulnerabilidades por Hora',
                    xaxis={'title': 'Hora'},
                    yaxis={'title': 'Número de Vulnerabilidades'},
                    plot_bgcolor='#2E2E2E',
                    paper_bgcolor='#2E2E2E',
                    font={'color': '#FFFFFF'}
                )
            }
        )
    ], style={'width': '90%', 'margin': '20px auto'}),

    html.Div([
        html.H2("Resumen de Vulnerabilidades", style={
            'color': '#FFFFFF', 'textAlign': 'center', 'fontFamily': 'Arial, sans-serif', 'fontSize': '24px', 'fontWeight': 'bold',
            'backgroundColor': '#2E2E2E', 'padding': '10px', 'borderRadius': '10px'
        }),
        dash_table.DataTable(
            id='vulnerability-summary-table',
            columns=[
                {'name': 'Categoría', 'id': 'category'},
                {'name': 'Valor', 'id': 'value'}
            ],
            data=vulnerability_summary,
            style_table={'width': '100%', 'margin': 'auto'},
            style_header={'backgroundColor': '#2E2E2E', 'color': '#FFFFFF'},
            style_cell={'backgroundColor': '#1E1E1E', 'color': '#FFFFFF'},
            page_size=10,
            page_action='native'
        )
    ], style={'width': '90%', 'margin': '20px auto'}),

    html.Div([
        html.H2("Porcentaje de IPs con y sin Vulnerabilidades", style={
            'color': '#FFFFFF', 'textAlign': 'center', 'fontFamily': 'Arial, sans-serif', 'fontSize': '24px', 'fontWeight': 'bold',
            'backgroundColor': '#2E2E2E', 'padding': '10px', 'borderRadius': '10px'
        }),
        dcc.Graph(
            id='ip-vulns-pie-chart',
            figure={
                'data': [
                    go.Pie(
                        labels=['Con Vulnerabilidad', 'Sin Vulnerabilidad'],
                        values=[percent_with_vulns, percent_without_vulns],
                        marker={'colors': ['red', 'green']}
                    )
                ],
                'layout': go.Layout(
                    title='Porcentaje de IPs con y sin Vulnerabilidades',
                    plot_bgcolor='#2E2E2E',
                    paper_bgcolor='#2E2E2E',
                    font={'color': '#FFFFFF'}
                )
            }
        )
    ], style={'width': '90%', 'margin': '20px auto'})
])

# Callback para actualizar la tabla de CVEs por IP cuando se selecciona una IP
@app.callback(
    Output('ip-cves-table', 'data'),
    [Input('ip-dropdown', 'value')]
)
def update_ip_cves_table(selected_ip):
    if selected_ip is None:
        return []
    ip_data = df[df['ip'] == selected_ip][['ip', 'cve_str']].to_dict('records')
    return ip_data

if __name__ == '__main__':
    app.run_server(debug=True, host='0.0.0.0', port=8050)
