
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
  
#This is a test
schedule.every(10).minutes.do(Analyzer)

#schedule.every().day.at("00:00").do(Analyzer)
#schedule.every().day.at("06:00").do(pdfReport)

while True:
    schedule.run_pending()
    time.sleep(1)



    
