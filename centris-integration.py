import os
import sqlite3
import requests

def Start():
  db_path = os.environ.get('DB_PATH')
  if db_path is None:
      raise ValueError("DB_PATH environment variable not set")
  conn = sqlite3.connect(db_path)



if __name__ == "__main__":
  Start()