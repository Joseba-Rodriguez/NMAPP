# NMAPP (Network Mapping Application)
![NMAPP](NMAPP%20V2.0/app/resources/logo.png)

NMAPP (Network Mapping Application) is a tool designed to perform network scans and monitor the presence of hosts and services in a given network. It provides a web interface that allows users to conduct scans, visualize the results, and efficiently manage the obtained information.

NMAPP utilizes the Nmap tool to perform network scans and presents the results in a clear and organized manner. Users can access the web interface, log in, schedule scans, analyze the obtained results, download detailed reports, and manage their account.

The tool facilitates the detection of changes in the network, the discovery of new hosts, the identification of services and vulnerabilities, and the tracking of network infrastructure evolution.

Explore NMAPP and take advantage of its functionality to obtain detailed and up-to-date information about your network! Access the /NMAPP v2.0 folder for installation instructions.

## Installation

To install NMAPP, follow these steps:

1. Install Docker by running the following command:

```sudo snap install docker```


2. Run the Permission.py  script using Python 3 to set the necessary permissions:
   
```python3 /NMAPP V2/app/Permission.py```


3. Build and launch the NMAPP application using Docker Compose inside /NMAPP V2 directory:

```docker-compose up```

4. Once the installation is complete, access the NMAPP web interface by opening your web browser and navigating to `https://localhost`. 

5. Use the following credentials to log in:
- Username: NmappAdmin
- Password: aasfaqwe12

**Important**: It is highly recommended to change the password immediately after logging in for the first time to ensure the security of your NMAPP instance.

Make sure to check the /NMAPP v2.0 folder for any additional configuration or setup instructions specific to your environment.

![Descripción de la imagen](NMAPP%20V2.0/app/resources/Total.JPG)
## License

NMAPP is licensed under the [MIT License](LICENSE). Feel free to use, modify, and distribute the software according to the terms of the license.

## Contributing

If you would like to contribute to NMAPP, please follow the guidelines outlined in the [CONTRIBUTING.md](CONTRIBUTING.md) file. We welcome your contributions and appreciate your help in making NMAPP even better.

## Support

For any questions, issues, or feedback, please open an issue in the [issue tracker](https://github.com/Joseba-Rodriguez/TFG-NMAPP) or contact us at bshadowyt@gmail.com.

## Acknowledgements

We would like to express our gratitude to the developers and contributors of Nmap for providing the powerful scanning capabilities that NMAPP relies on.

## About

NMAPP was developed with the goal of simplifying network scanning and monitoring. It was created by [Joseba Rodríguez](https://github.com/Joseba-Rodriguez) and is maintained by the NMAPP team.


---



