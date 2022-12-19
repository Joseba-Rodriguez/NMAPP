from libnmap.process import NmapProcess
from libnmap.parser import NmapParser, NmapParserException


# print scan results from a nmap report
def print_scan(nmap_report):
    print("nmap_report_starttime{{version=\"{0}\"}} {1}".format(nmap_report.version,nmap_report.started))

    for host in nmap_report.hosts:
        if len(host.hostnames):
            tmp_host = host.hostnames.pop()
        else:
            tmp_host = host.address

        tmp_vendor = host.vendor
        if len(host.vendor) == 0 and len(host.mac) != 0:
            tmp_vendor = "SI " + host.mac[0:8]
        
        print("nmap_host{{hostname=\"{0}\",ipv4address=\"{1}\",ipv6address=\"{2}\",macaddress=\"{3}\",macvendor=\"{4}\",status=\"{5}\}} {6}".format(
            tmp_host,
            host.ipv4, host.ipv6, host.mac, tmp_vendor,
            host.status,
            1 if host.status == "up" else 0))

        for serv in host.services:
            print("nmap_service{{port=\"{0}\",service=\"{1}\",protocol=\"{2}\",state=\"{3}\"}} {4}".format(
                serv.port,
                serv.service,
                serv.protocol,
                serv.state,
                1 if serv.state == "open" else 0))

    print("Id de tiempo {0}".format(nmap_report.endtime))
    print("Total de host {0}".format(nmap_report.hosts_total))
    print("Hosts_abiertos {0}".format(nmap_report.hosts_up))
    print("Puertos cerrados {0}".format(nmap_report.hosts_down))
    print("Tiempo total de escaneo {0}".format(nmap_report.elapsed))

if __name__ == "__main__":
    report = NmapParser.parse_fromfile('./datos.xml')
    if report:
        print_scan(report)
    else:
        print("No results returned")