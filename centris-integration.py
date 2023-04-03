import os
import sqlite3
import requests

def Start():
  db_path = os.environ.get('DB_PATH')
  if db_path is None:
      raise ValueError("DB_PATH environment variable not set")
  conn = sqlite3.connect(db_path)
  conn.text_factory = lambda x: str(x, 'iso-8859-1')

  # Query database INSCRIPTIONS table
  cursor = conn.execute('SELECT NO_INSCRIPTION, PRIX_DEMANDE, PRIX_LOCATION_DEMANDE, NO_CIVIQUE_DEBUT, \
    NO_CIVIQUE_FIN, NOM_RUE_COMPLET, APPARTEMENT, CODE_POSTAL, ANNEE_CONTRUCTION, NB_PIECES,\
    NB_CHAMBRES, NB_SALLES_BAINS, NB_SALLES_EAU, SUPERFICIE_TERRAIN, SUPERFICIE_HABITABLE, SUPERFICIE_BATIMENT, LATITUDE, LONGITUDE, \
    URL_DESC_DETAILLEE, MUN_CODE, ADDENDA_COMPLET_A, ADDENDA_COMPLET_F \
    FROM INSCRIPTIONS')

  # for every property
  for row in cursor:

    # Get the property title by concatenating address number and street
    if row[3]: title = f'{row[3]} ' + row[5] + row[6]
    elif row[4]: title = f'{row[4]} ' + row[5] + row[6]
    else: title = row[5]

    # Add description
      # Add english addenda from inscription
    description_english = row[20]
      # Add french addenda from inscriptiom
    description_french = row[21]

    # Find property type

    # Get sale or rent status

    # get price

    # get number of bedrooms & bathrooms
    bedrooms = row[10]
    bathrooms = row[11]

    # get area size
    lot_size = row[13]
    living_size = row[14]
    building_size = row[15]

    # get property ID
    id = row[0]

    # get year built
    year = row[8]

    # get City from municipalities table
    city = conn.execute(f'SELECT DESCRIPTION FROM MUNICIPALITES WHERE CODE={row[19]}').fetchone()[0]

    # add location info:
    location = [{'address': title, 'country': 'Canada', 'City': city, 'postal_code': row[7], 'latitude': row[16], 'longitude': row[17]}]

    print(f'{id}')
    print(f'title: {title}')
    print('--------------------')
    print(f'english description: {description_english}')
    print('********************')
    print(f'french description: {description_french}')
    print('--------------------')
    print(f'bedrooms: {bedrooms}')
    print(f'bathrooms: {bathrooms}')
    print(f'lot_size: {lot_size}')
    print(f'living_size: {living_size}')
    print(f'building_size: {building_size}')
    print(f'year: {year}')
    print(f'location: {location}')
    print('#######################################################################')
    
    



if __name__ == "__main__":
  Start()