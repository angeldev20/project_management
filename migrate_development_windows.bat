vendor\bin\phinxwindows.bat migrate -e development
vendor\bin\phinxwindows.bat migrate -c seeddb-phinx.yml -e development
npm update
npm run pack