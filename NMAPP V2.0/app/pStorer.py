import os
import unittest
from unittest.mock import patch, MagicMock
import psycopg2
from storer import parse_for_individual, parse_for_now, raw_parser
class TestRawParser(unittest.TestCase):

    @patch.dict(os.environ, {
        'DB_USER': 'user',
        'DB_PASS': 'pass',
        'DB_HOST': 'host',
        'DB_DB': 'db'
    })
    def test_parse_for_individual(self):
        ip = '192.168.0.1'
        hostname = 'host1'
        portnum = 80
        protocol = 'tcp'
        service = 'http'
        versioning = 'Apache/2.4.29'
        cve_str = 'CVE-2017-15715'
        with psycopg2.connect() as conn:
            with conn.cursor() as cursor:
                cursor.execute("DROP TABLE IF EXISTS nmapIndividual")
                cursor.execute("""
                    CREATE TABLE nmapIndividual (
                        ip varchar(255),
                        hostname varchar(255),
                        port int,
                        protocol varchar(5),
                        service varchar(255),
                        version varchar(255),
                        cve_str varchar(255)
                    );
                """)
                parse_for_individual(ip, hostname, portnum, protocol, service, versioning, cve_str)
                cursor.execute("SELECT * FROM nmapIndividual WHERE ip=%s", (ip,))
                result = cursor.fetchone()
                self.assertIsNotNone(result)
                self.assertEqual(result[0], ip)
                self.assertEqual(result[1], hostname)
                self.assertEqual(result[2], portnum)
                self.assertEqual(result[3], protocol)
                self.assertEqual(result[4], service)
                self.assertEqual(result[5], versioning)
                self.assertEqual(result[6], cve_str)

    @patch.dict(os.environ, {
        'DB_USER': 'user',
        'DB_PASS': 'pass',
        'DB_HOST': 'host',
        'DB_DB': 'db'
    })
    def test_parse_for_now(self):
        ip = '192.168.0.1'
        hostname = 'host1'
        portnum = 80
        protocol = 'tcp'
        service = 'http'
        versioning = 'Apache/2.4.29'
        cve_str = 'CVE-2017-15715'
        with psycopg2.connect() as conn:
            with conn.cursor() as cursor:
                cursor.execute("DROP TABLE IF EXISTS nmapNow")
                cursor.execute("""
                    CREATE TABLE nmapNow (
                        ip varchar(255),
                        hostname varchar(255),
                        port int,
                        protocol varchar(5),
                        service varchar(255),
                        version varchar(255),
                        cve_str varchar(255)
                    );
                """)
                parse_for_now(ip, hostname, portnum, protocol, service, versioning, cve_str)
                cursor.execute("SELECT * FROM nmapNow WHERE ip=%s", (ip,))
                result = cursor.fetchone()
                self.assertIsNotNone(result)
                self.assertEqual(result[0], ip)
                self.assertEqual(result[1], hostname)
                self.assertEqual(result[2], portnum)
                self.assertEqual(result[3], protocol)
                self.assertEqual(result[4], service)
                self.assertEqual(result[5], versioning)
                self.assertEqual(result[6], cve_str)

    @patch.dict(os.environ, {
        'DB_USER': 'user',
        'DB_PASS': 'pass',
        'DB_HOST': 'host',
        'DB_DB': 'db'
    })

    @patch('xml.etree.ElementTree.parse')
    @patch('psycopg2.connect')
    def test_raw_parser(self, mock_connect, mock_parse):
        # Set up mock objects
        mock_cursor = MagicMock()
        mock_connect.return_value.cursor.return_value = mock_cursor
        mock_input_file = MagicMock()
        mock_root = MagicMock()
        mock_summary = MagicMock()

        # Configure mock objects
        mock_parse.return_value.getroot.return_value = mock_root
        mock_root.find.return_value.find.return_value.get.return_value = mock_summary
        mock_summary.return_value = '1 hosts up'

        # Run raw_parser
        raw_parser(1)

        # Check that the correct SQL commands were executed
        mock_cursor.execute.assert_any_call("DELETE FROM nmapNow")
        mock_cursor.execute.assert_any_call("DELETE FROM stats")
        mock_cursor.execute.assert_any_call("INSERT INTO lastAnalyze SELECT * FROM nmapIndividual")
        mock_cursor.execute.assert_any_call("DELETE FROM nmapIndividual")

        # Check that the connect method was called with the correct arguments
        mock_connect.assert_called_once_with(host='test_host', database='test_database',
                                              user='test_user', password='test_password')

        # Check that the cursor method was called with the correct arguments
        mock_connect.return_value.cursor.assert_called_once()

        # Check that the parse method was called with the correct arguments
        mock_parse.assert_called_once_with(mock_input_file)
if __name__ == '__main__':
    unittest.main()
