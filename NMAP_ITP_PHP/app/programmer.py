import sys
import schedule
import time
import os ,sys, stat

#Function to execute the Analyzer scheduledly
def Analyzer():
     os.system("python3 ./app/Analyzer.py")
     
#Function to execute the pdfReport scheduledly
def pdfReport():
     os.system("python3 ./app/pdfReport.py")
  

def run_daily_schedule():
    schedule.every().minute.do(Analyzer)
    while True:
        schedule.run_pending()

def run_weekly_schedule():
    schedule.every().week.do(Analyzer)
    while True:
        schedule.run_pending()

def run_monthly_schedule():
    schedule.every(30).day.do(Analyzer)
    while True:
        schedule.run_pending()

def run_minutely_schedule():
    schedule.every().minute.do(Analyzer)
    while True:
        schedule.run_pending()

def get_config():
    with open("./app/config.txt", "r") as file:
        frequency = file.read()
    return frequency

def run_schedule():
    frequency = get_config()
    if frequency == "daily":
        run_daily_schedule()
    elif frequency == "weekly":
        run_weekly_schedule()
    elif frequency == "monthly":
        run_monthly_schedule()
    elif frequency == "minutely":
        run_minutely_schedule()

run_schedule()