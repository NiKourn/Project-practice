# Project-practice
 A practice project (will be renamed later)

## TODO
- Need to create ? child classes for getting db and checking if tables are spooled and populated, need to think of hierarchy of how it's going to be working with the db (install, create, get, insert etc)
- Need to implement a head class that is inserting everything (pretty much done)
- Need to rethink how Title is going to work (maybe another class or function)
- Need to insert option_value and option_name in options table
- Need to make SQL statements for a bunch of stuff
- Search for actions or something to run stuff from different parts of the theme
- Write a class for erroring with bootstrap stuff
- Need to calculate how dbinstall will run maybe not in constructor but init() at header maybe and check at constructor for the db everytime else nothing runs
- Redirects logic needs work at dbinstall class on constructor

### v beta0.1.0
- Fixed nonce working
- Reworked PDO connection erroring
- Fixed redirects (pending)
- When docker implementation is done, finish feature into develop branch
- Redirect implemented
- Docker implementation and bug fixes regarding database creation
- Created dbinstall and create_db classes
- Create database automatically with jSon (db-info.json) over dbinstall class
- Create file structure
