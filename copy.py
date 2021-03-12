import mysql.connector

link = mysql.connector.connect(
  host="localhost",
  user="u",
  password="p",
  database="db"
)


link.close()
