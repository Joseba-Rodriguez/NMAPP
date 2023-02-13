import os, stat

def set_permissions(file_list, permissions):
    for file in file_list:
        os.chmod(file, permissions)
        if file == "./datos.xml":
            try:
                os.remove(file)
            except FileNotFoundError:
                pass

file_list = [ "./ipsReporte.txt", "./storer.py", "./programmer.py", "./Analyzer.py" , "./envioIPs.php"]
permissions = stat.S_IRWXU | stat.S_IRWXG | stat.S_IRWXO
set_permissions(file_list, permissions)