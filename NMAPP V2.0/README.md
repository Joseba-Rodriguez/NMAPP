# User Guide

## Login

By default, when the user enters the URL, they will be directed to the login page. In order to access the application, the user must be registered beforehand. Otherwise, they can simply enter the corresponding data in the form fields (username and password) and click the Login button. If the data is correct, you will be redirected to the web page; otherwise, different errors will be displayed depending on the mistake made.

## Registration

If the user needs to create an account to access the website, they can proceed with the account creation within the tool by clicking the "Others" button in the header. It is important to note that the user must maintain a secure password. In this case, the required fields will need to be filled in with 8 to 12 characters, including at least one uppercase letter and one number.

## Main Index

The main interface is divided into different sections:

1. Scans:
In this first section, the user can view the results of the latest Nmap scan. The extracted information from running the Nmap command is displayed, showing various details such as IP, Hostname, Port, Protocol, Device, Version, and Vulnerabilities.

Additionally, there is a download button. Clicking it will initiate the download of the displayed report in CSV format, allowing the user to review and save it. This section also includes the scheduling times for scan execution.

2. Discoveries:
One of the most important features of the tool is the ability to monitor each scan. The user can observe if there have been any new appearances or disappearances of the desired IPs or servers.

This section is divided into 3 subcategories:

    a. Appear: Displays newly discovered hosts compared to the previous scan.
    
    b. Lost: Indicates hosts that have been removed or not detected compared to the previous analysis.
    
    c. Stay: Shows hosts where no changes have been detected.

3. IP History:
To conclude the main view, the program includes a data input form where the user can enter IPs (individual or ranges) and domains. Simply click the submit button to proceed. Additionally, if the user is not satisfied with their input, they can clear the entered IP list. It is important to note that everything submitted for analysis must be in a linear format, meaning that it should be entered consecutively in a single line rather than in parts. Multiple IPs or domains can be entered, separated by a blank space. For example: 192.168.0.1-10 192.168.0.1/24 deusto.es santurtzi.net.

## Logout and Password Change

The user always has the option to log out of their account at any time, regardless of the phase they are in. It is important to note that even if the user logs out, any scheduled scans they have set up will continue to run. The option to log out can be accessed from the top part of the interface.

As shown, the user can also access the password change functionality within the session. By entering the new password, it will be updated in the database.

## NmapNow Functionality

This functionality is used to perform individual scans, separate from the scheduled scans. It is useful for quickly and comprehensively analyzing IPs. It is particularly efficient for swiftly analyzing IP addresses.

Here are the steps to use the NmapNow functionality:

1) Access the main interface of the application.
2) Look for the "NmapNow Functionality" section or a similar option in the menu or main page.
3) Within the NmapNow functionality, you will find a form where you can enter the IP address you want to scan.
4) Enter the IP address in the form and click the "Scan" or "Start Scan" button.
5) The system will perform the scan using the Nmap tool and display the results on the screen.
6) Examine the scan results, which may include information such as open ports, protocols used, detected services, vulnerabilities, and more.
7) If desired, you can download a detailed report in CSV format or save the results for future reference.
8) Repeat the process to perform additional scans on other IP addresses.

Remember that the NmapNow functionality allows you to quickly and easily obtain detailed information about a specific IP address without the need to set up a scheduled scan. Use it to gather relevant information about the services and vulnerabilities associated with a particular IP address.

## Email Configuration
The email configuration is not set by default and needs to be configured by the user by entering the username and password for both the email generation and destination.
Access /app/excelReport.py and follow the comments in the file to proceed with the configuration.

# Basic Scheduled Usage Example

    1. Enter the IPs, domains, or IP ranges in the search form (separated by spaces).
    2. Click the submit button below the form to proceed with the analysis.
    3. Select a time for the scheduled scans.
    4. Wait for the analysis after scheduling the scans.
    5. Receive the email (if configured).
    6. View the data in the Scheduled section.
