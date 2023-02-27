"""
This module contains a script that periodically runs a task based on the last selection
of a button stored in a PostgreSQL database.
"""

import time
import os
import psycopg2
import schedule

# Open the file with the data to connect to the database
with open('./app/postgresConfiguration.txt', encoding='utf-8') as f:
    for line in f:
        name, value = line.strip().split('=')
        os.environ[name] = value

user = os.environ['DB_USER']
password = os.environ['DB_PASS']
hostdb = os.environ['DB_HOST']
database = os.environ['DB_DB']


def get_last_selection():
    """
    This function retrieves the last selection of the button from the database.

    Returns:
        The last selection of the button.
    """
    # Connect to the database
    conexion = psycopg2.connect(
        host=hostdb, database=database, user=user, password=password)
    cursor = conexion.cursor()

    # Get the last selection of the button
    cursor.execute("SELECT selection FROM buttons ORDER BY id DESC LIMIT 1")
    last_selection = cursor.fetchone()[0]

    # Close the database connection
    cursor.close()
    conexion.close()

    return last_selection


last_selection = None


def main_task():
    """
    This is the main task that will be executed.
    """
    # Execute the main task here
    os.system("python3 ./app/Analyzer.py")


# Infinite loop
while True:
    # Check the last selection of the button every 2 seconds
    new_selection = get_last_selection()

    # If the selection has changed, cancel the current scheduled task and schedule a new one
    if new_selection != last_selection:
        schedule.clear()
        if new_selection == "2Weeks":
            schedule.every(14).days.at("00:00").do(main_task)
            os.system("python3 ./app/excelReport.py")
        elif new_selection == "monthly":
            schedule.every(30).days.at("00:00").do(main_task)
            os.system("python3 ./app/excelReport.py")
        elif new_selection == "now":
            main_task()
            os.system("python3 ./app/excelReport.py")

        last_selection = new_selection

    # Run any pending scheduled tasks
    schedule.run_pending()

    # Sleep for 2 seconds before checking the last selection of the button again
    time.sleep(2)
