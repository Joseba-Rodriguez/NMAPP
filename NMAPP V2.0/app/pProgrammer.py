import unittest
import os
import psycopg2
from programmer import get_last_selection, main_task, DBVulners
class TestFunctions(unittest.TestCase):
    
    def test_get_last_selection(self):
        # Set up the environment variables needed to connect to the database
        os.environ['DB_USER'] = 'username'
        os.environ['DB_PASS'] = 'password'
        os.environ['DB_HOST'] = 'localhost'
        os.environ['DB_DB'] = 'database_name'

        # Connect to the database and insert some data
        conn = psycopg2.connect(
            host=os.environ['DB_HOST'],
            database=os.environ['DB_DB'],
            user=os.environ['DB_USER'],
            password=os.environ['DB_PASS']
        )
        cursor = conn.cursor()
        cursor.execute("INSERT INTO buttons (selection) VALUES ('monthly')")
        conn.commit()

        # Test that the function returns the correct value
        self.assertEqual(get_last_selection(), 'monthly')

        # Clean up the database
        cursor.execute("DELETE FROM buttons WHERE selection = 'monthly'")
        conn.commit()
        cursor.close()
        conn.close()

    def test_main_task(self):
        # Test that the function runs without errors
        self.assertIsNone(main_task())

    def test_DBVulners(self):
        # Test that the function runs without errors
        self.assertIsNone(DBVulners())

if __name__ == '__main__':
    unittest.main()
