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
app.css.append_css({
    'external_url': 'https://codepen.io/chriddyp/pen/bWLwgP.css'
})

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
        ], style={'float': 'left', 'width': '30%', 'display': 'inline-block', 'verticalAlign': 'top', 'padding': '30px', 'backgroundColor': '#2E2E2E', 'borderRadius': '10px', 'boxShadow': '0 4px 8px rgba(0, 0, 0, 0.2)'}),

        html.Div([
            html.H2("CVEs por IP", style={
                'color': '#FFFFFF', 'textAlign': 'center', 'fontFamily': 'Arial, sans-serif', 'fontSize': '24px', 'fontWeight': 'bold',
                'backgroundColor': '#2E2E2E', 'padding': '10px', 'borderRadius': '10px'
            }),
            dash_table.DataTable(
                id='cves-by-ip-table',
                columns=[
                    {'name': 'IP', 'id': 'ip'},
                    {'name': 'CVEs', 'id': 'cve_str', 'presentation': 'markdown'}
                ],
                data=df[['ip', 'cve_str']].to_dict('records'),
                style_table={'width': '100%', 'margin': 'auto', 'overflowX': 'auto'},
                style_header={'backgroundColor': '#2E2E2E', 'color': '#FFFFFF'},
                style_cell={'backgroundColor': '#1E1E1E', 'color': '#FFFFFF'},
                page_size=10,
                page_action='native'
            ),
        ], style={'float': 'left', 'width': '30%', 'display': 'inline-block', 'verticalAlign': 'top', 'padding': '30px', 'backgroundColor': '#2E2E2E', 'borderRadius': '10px', 'boxShadow': '0 4px 8px rgba(0, 0, 0, 0.2)'}),

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
                style_cell={'backgroundColor': '#1E1E1E', 'color': '#FFFFFF'}
            ),
        ], style={'float': 'left', 'width': '30%', 'display': 'inline-block', 'verticalAlign': 'top', 'padding': '30px', 'backgroundColor': '#2E2E2E', 'borderRadius': '10px', 'boxShadow': '0 4px 8px rgba(0, 0, 0, 0.2)'}),
    ], style={'margin': '20px auto', 'width': '90%', 'backgroundColor': '#2E2E2E', 'borderRadius': '10px', 'padding': '20px', 'boxShadow': '0 4px 8px rgba(0, 0, 0, 0.2)'}),

    html.Div([
        html.Div([
            dcc.Graph(
                id='common-ports-graph',
                style={'backgroundColor': '#1E1E1E'}
            )
        ], style={'width': '50%', 'display': 'inline-block', 'verticalAlign': 'top', 'padding': '30px', 'backgroundColor': '#2E2E2E', 'borderRadius': '10px', 'boxShadow': '0 4px 8px rgba(0, 0, 0, 0.2)'}),

        html.Div([
            dcc.Graph(
                id='ports-vulnerabilities-graph',
                style={'backgroundColor': '#1E1E1E'}
            )
        ], style={'width': '50%', 'display': 'inline-block', 'verticalAlign': 'top', 'padding': '30px', 'backgroundColor': '#2E2E2E', 'borderRadius': '10px', 'boxShadow': '0 4px 8px rgba(0, 0, 0, 0.2)'}),
    ], style={'margin': '20px auto', 'width': '90%', 'backgroundColor': '#2E2E2E', 'borderRadius': '10px', 'padding': '20px', 'boxShadow': '0 4px 8px rgba(0, 0, 0, 0.2)'}),

    html.Div([
        dcc.Graph(
            id='cves-frequency-graph',
            style={'backgroundColor': '#1E1E1E'}
        )
    ], style={'width': '90%', 'display': 'inline-block', 'verticalAlign': 'top', 'padding': '30px', 'backgroundColor': '#2E2E2E', 'borderRadius': '10px', 'boxShadow': '0 4px 8px rgba(0, 0, 0, 0.2)', 'margin': '20px auto'}),

    html.Div([
        dcc.Graph(
            id='vulnerabilities-timeline-graph',
            style={'backgroundColor': '#1E1E1E'}
        )
    ], style={'width': '90%', 'display': 'inline-block', 'verticalAlign': 'top', 'padding': '30px', 'backgroundColor': '#2E2E2E', 'borderRadius': '10px', 'boxShadow': '0 4px 8px rgba(0, 0, 0, 0.2)', 'margin': '20px auto'})
], style={'backgroundColor': '#1E1E1E', 'fontFamily': 'Arial, sans-serif', 'padding': '20px'})

@app.callback(
    [
        Output('ip-connections-table', 'data'),
        Output('cves-by-ip-table', 'data'),
        Output('vulnerability-summary-table', 'data'),
        Output('common-ports-graph', 'figure'),
        Output('ports-vulnerabilities-graph', 'figure'),
        Output('cves-frequency-graph', 'figure'),
        Output('vulnerabilities-timeline-graph', 'figure')
    ],
    [Input('ip-dropdown', 'value')]
)
def update_dashboard(selected_ip):
    if selected_ip:
        filtered_df = df[df['ip'] == selected_ip]
    else:
        filtered_df = df

    ip_connections_data = ip_connections if not selected_ip else filtered_df['ip'].value_counts().head(10).reset_index().rename(columns={'index': 'ip', 'ip': 'count'})
    cves_by_ip_data = filtered_df[['ip', 'cve_str']].to_dict('records')
    vulnerability_summary_data = vulnerability_summary if not selected_ip else [
        {'category': 'Total Conexiones', 'value': len(filtered_df)},
        {'category': 'Total Vulnerabilidades', 'value': sum([len(cves) for cves in filtered_df['cve_list']])},
        {'category': 'Vulnerabilidades Críticas', 'value': sum([sum([float(cve[1]) >= 7.0 for cve in cves]) for cves in filtered_df['cve_list']])}
    ]

    ips_summary_data = ip_connections_data.to_dict('records')

    common_ports_figure = go.Figure(
        data=[
            go.Bar(
                x=top_ports_services['port'].astype(str) + " (" + top_ports_services['service'] + ")",
                y=top_ports_services['count'],
                marker_color='rgb(26, 118, 255)'
            )
        ],
        layout=go.Layout(
            title="Top 10 Puertos Más Comunes y sus Servicios",
            paper_bgcolor='#1E1E1E',
            plot_bgcolor='#1E1E1E',
            font=dict(color='white')
        )
    )

    ports_vulnerabilities_figure = go.Figure(
        data=[
            go.Bar(
                x=port_vulns['port'].astype(str),
                y=port_vulns['count'],
                marker_color='rgb(55, 83, 109)'
            )
        ],
        layout=go.Layout(
            title="Top 10 Puertos con Más Vulnerabilidades",
            paper_bgcolor='#1E1E1E',
            plot_bgcolor='#1E1E1E',
            font=dict(color='white')
        )
    )

    cves_frequency_figure = go.Figure(
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
            paper_bgcolor='#1E1E1E',
            plot_bgcolor='#1E1E1E',
            font=dict(color='white'),
            barmode='stack',
            xaxis={'title': 'CVE'},
            yaxis={'title': 'Cantidad'},
        )
    )

    vulnerabilities_timeline_figure = go.Figure(
        data=[
            go.Scatter(
                x=vulns_by_hour['ts'],
                y=vulns_by_hour['count'],
                mode='lines',
                line=dict(color='rgb(26, 118, 255)')
            )
        ],
        layout=go.Layout(
            title="Timeline de Vulnerabilidades",
            paper_bgcolor='#1E1E1E',
            plot_bgcolor='#1E1E1E',
            font=dict(color='white')
        )
    )

    return (
        ip_connections_data.to_dict('records'),
        cves_by_ip_data,
        vulnerability_summary_data,
        common_ports_figure,
        ports_vulnerabilities_figure,
        cves_frequency_figure,
        vulnerabilities_timeline_figure
    )

if __name__ == '__main__':
    app.run_server(debug=True, host='0.0.0.0', port=80)
