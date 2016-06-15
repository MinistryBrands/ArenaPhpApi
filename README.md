# ArenaPhpApi

The Arena.class.php is a PHP wrapper around the Shelby Arena REST API. It does not implement all of the API endpoints, but adding support for additional endpoints should take about 10 lines of code. If you use and expand this, please consider making a Pull Request so that we can improve this for everyone.

The example folder contains a sample website that uses the API. 

- It pulls groups from Arena
- Displays Them
- Allows users to select to join a group
- Searches for the person by name
- If a match is not found, the person is created and added to the group
- If a match is  found, the person is added to the group