## PoxPulse is now open source! 

I have not had the time to keep this project updated and maintained for years. If anyone would like to pick it up and contribute, I am happy to review code changes via pull request and continue to host at www.poxpulse.com.

Be warned: the code is undocumented and sparsely commented. Also half a decade old. **Please do not expect quality!** This was put together early in my career and rather hastily.

Top of the list of needed features is a new parsing script to get data into the database. Desert Owl Games setup an XML/JSON API a couple year ago. The API has flavor text, rune art, icons and all of the data that I previously had to scrape or enter manually. A quality parser script using the new API could be set to run on a schedule so that poxpulse is always up to date!

Examples of API access

http://www.poxnora.com/api/feed.do
http://www.poxnora.com/api/feed.do?t=json
http://www.poxnora.com/api/feed.do?r=conditions
http://www.poxnora.com/api/feed.do?r=mechanics
http://www.poxnora.com/api/feed.do?t=json&r=conditions
http://www.poxnora.com/api/feed.do?t=json&r=mechanics

The old rune displays would need updated too, since Desert Owl Games made significant changes.

**Tech:**

* PHP/Codeigniter
* MySQL
* JQuery / JQuery UI / JS
* DataTables for JQuery UI
* HTML/CSS

The MySQL database is in poxpulse.sql ready to be imported. Structure only.






