# תוכנית עבודה – chabadpedia.com

לפני כל שלב: גיבוי של `LocalSettings.php` (`LocalSettings.php.bak_<date>`). אחרי כל שלב: בדיקה מול השרת.
✋ = צריך החלטה, הרשאה או גישה שאין לי.

## שלב 0 – אבטחה 🚨 דחוף
- [x] להעביר את `dump.sql`, את `LocalSettings.php.bak_*` ואת `adminer.php` אל `~/backups/removed-from-webroot/` (בוצע 24.11.2026)
- [x] להחליף את `$wgSecretKey`, את `$wgUpgradeKey` ואת `$wgAuthenticationTokenVersion` (בוצע 24.11.2026. כל המשתמשים נותקו)
- [ ] ✋ להחליף את סיסמת בסיס הנתונים ב־CloudPanel, ואז לעדכן את `$wgDBpassword`
- [ ] לחסום קבצי `.md` ו־`.bak` בתיקיית האתר, או להעביר אותם אל מחוץ לה (`LocalSettings.php.bak_*` חושף סיסמאות אם הוא מוגש כטקסט!)
- [ ] לתקן את `$wgDebugLogFile` (או להסיר אותו)

## שלב 1 – הגדרות ב־LocalSettings.php (סעיפים 5, 9)
- [x] `$wgEnableCanonicalServerLink = true;` ולבדוק שמופיע `<link rel="canonical">`
- [x] להוסיף את Shraga לקבוצת bot (התברר שהוא כבר בקבוצה):
  `php maintenance/run.php createAndPromote --force --custom-groups=bot Shraga`
  (או דרך `Special:UserRights/Shraga`)

## שלב 2 – חיפוש (סעיף 1)
- [x] לבדוק בממשק ולהשוות את מספר הדפים לכיסוי של `searchindex`
- [x] `php maintenance/run.php rebuildtextindex` (דקה אחת. 27,969→25,052 שורות)
- [x] לוודא ש־`$wgDisableSearchUpdate` לא מוגדר ושה־job queue מתרוקן (`showJobs`)
- [x] (לא נדרש. התור ריק וברירת המחדל של `$wgJobRunRate` פעילה) להוסיף cron של `runJobs` אם `$wgJobRunRate` מאפס אותו

## שלב 3 – robots.txt ו־Sitemap (סעיפים 3, 4)
- [x] להחליף את `robots.txt` בתוכן מהמסמך
- [x] `mkdir sitemap` ולהריץ `generateSitemap` עם `--fspath=$PWD/sitemap`
- [x] cron יומי (עם `--skip-redirects`): `crontab -e` → `30 3 * * * cd ~/htdocs/chabadpedia.com && php maintenance/run.php generateSitemap ...`
- [x] לבדוק ש־`/sitemap/sitemap-index-chabadpedia.xml` מוגש כ־XML (מוגש כ־text/xml, לא היה צריך לשנות את nginx). ✋ אם nginx משכתב אותו לויקי, צריך לתקן ב־CloudPanel

## שלב 4 – rest.php (סעיף 2) ✋
- [x] לבדוק את כלל השכתוב (כנראה `location /` → `index.php`)
- [x] נוסף ב־CloudPanel → Vhost בלוק try_files. התוצאה: 308, כי REQUEST_URI מאבד את הנתיב
- [x] הוחלף בבלוק fastcgi ישיר עם `fastcgi_param REQUEST_URI $request_uri` ו־`fastcgi_pass 127.0.0.1:16002`
- [x] לבדוק ש־`/rest.php/v1/search/title?q=Rebbe&limit=5` מחזיר JSON
- אין לי גישה לקונפיגורציית nginx, אז צריך שתעשה את זה ב־CloudPanel או שתיתן לי גישה

## שלב 5 – מדיה (סעיפים 6, 7)
- [x] לפתוח דף עם mp3 ולזהות את השגיאה (בשרת הנגן מוצג תקין. הדפים משתמשים בעיקר ב־Media:) (ForeignAPIRepo מ־chabadpedia.co.il)
- [ ] (לא נדרש ל־mp3. נשאר לווידאו) להסיר את `$wgFFmpegLocation` (ffmpeg לא מותקן) או להתקין ffmpeg ✋ (root)
- [x] `php maintenance/run.php update` (לא נדרש. טבלת transcode קיימת)
- [x] שלושת הקבצים הפגומים: תוקן עם alias לתחילית `קובץ:`, בלי מחיקה

## שלב 6 – רשות (סעיפים 10, 11) ✋
- [ ] רישיון: לעדכן `$wgRightsText`, `$wgRightsUrl` ו־`$wgRightsIcon` אחרי שתבחר
- [ ] Phonos: להגדיר מנוע TTS (למשל Google, דורש מפתח API), או לבטל את ההרחבה

## שלב 7 – האתר העברי (סעיף 8) ✋
- [ ] chabadpedia.co.il בשרת אחר? צריך גישה. שם: לבטל את Google Analytics Integration או למחוק את `$wgGoogleAnalyticsAccount`

## לעתיד – AI ותוספים (סעיף 12)
- Wanda, CirrusSearch ו־Elasticsearch: צריך root ו־Docker, וגם להפסיק לבזבז זיכרון (כרגע 12GB בשימוש. לבדוק מה צורך)
- Kartographer וייצוא ל־PDF: לבדוק אחרי שיהיו הרשאות root
