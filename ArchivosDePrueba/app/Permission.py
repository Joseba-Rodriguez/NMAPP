import os, stat

def set_permissions(file_list, permissions):
    for file in file_list:
        os.chmod(file, permissions)

file_list = ["./ips.txt", "./datos.xml", "./storer.py", "./programmer.py", "./DBVulners.py", "./Analyzer.py"]
permissions = stat.S_IRWXO
set_permissions(file_list, permissions)