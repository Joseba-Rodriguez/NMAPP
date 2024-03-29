import os
import stat

def set_permissions(file_list, permissions):
    """
    Sets file permissions for the specified list of files.

    :param file_list: a list of file paths to set permissions for.
    :param permissions: an integer representing the file permissions to set.
    """
    for file in file_list:
        os.chmod(file, permissions)

FILE_LIST = [ "./ipsReporte.txt", "./storer.py", "./programmer.py", "./Analyzer.py", "./DBVulners.py" , "./envioIPs.php", "./ipsNow.txt", "./index2.php", "./datos.xml"]
FILE_PERMISSIONS = stat.S_IRWXU | stat.S_IRWXG | stat.S_IRWXO
set_permissions(FILE_LIST, FILE_PERMISSIONS)
