import os
import sqlite3
import requests

def Start():
  db_path = os.environ.get('DB_PATH')
  if db_path is None:
      raise ValueError("DB_PATH environment variable not set")
  conn = sqlite3.connect(db_path)

  # Query database INSCRIPTIONS table

  # for every property
    # Get the property title by concatenating address number and street
    # Add description
        # Add english addenda from inscription
        # Add french addenda from inscriptiom
    # Find property type
    # Get sale or rent status
    # get price
    # get number of bedrooms
    # get number of bathrooms
    # get area size
    # get property ID
    # get year built
    # add location info:
        # Address (Same as property title)
        # Country in this case canada
        # City; get from municipalities table
        # postal code

  for row in cursor2:
    print(row)

if __name__ == "__main__":
  Start()