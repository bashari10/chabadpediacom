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
- [ ] לבדוק בממשק ולהשוות את מספר הדפים לכיסוי של `searchindex`
- [ ] `php maintenance/run.php rebuildtextindex`
- [ ] לוודא ש־`$wgDisableSearchUpdate` לא מוגדר ושה־job queue מתרוקן (`showJobs`)
- [ ] להוסיף cron של `runJobs` אם `$wgJobRunRate` מאפס אותו

## שלב 3 – robots.txt ו־Sitemap (סעיפים 3, 4)
- [ ] להחליף את `robots.txt` בתוכן מהמסמך
- [ ] `mkdir sitemap` ולהריץ `generateSitemap` עם `--fspath=$PWD/sitemap`
- [ ] cron יומי: `crontab -e` → `30 3 * * * cd ~/htdocs/chabadpedia.com && php maintenance/run.php generateSitemap ...`
- [ ] לבדוק ש־`/sitemap/sitemap-index-chabadpedia.xml` מוגש כ־XML. ✋ אם nginx משכתב אותו לויקי, צריך לתקן ב־CloudPanel

## שלב 4 – rest.php (סעיף 2) ✋
- [ ] לבדוק את כלל השכתוב (כנראה `location /` → `index.php`)
- [ ] להוסיף ב־CloudPanel → Vhost: `location /rest.php/ { try_files $uri $uri/ /rest.php?$query_string; }`
- [ ] לבדוק ש־`/rest.php/v1/search/title?q=Rebbe&limit=5` מחזיר JSON
- אין לי גישה לקונפיגורציית nginx, אז צריך שתעשה את זה ב־CloudPanel או שתיתן לי גישה

## שלב 5 – מדיה (סעיפים 6, 7)
- [ ] לפתוח דף עם mp3 ולזהות את השגיאה (ForeignAPIRepo מ־chabadpedia.co.il)
- [ ] להסיר את `$wgFFmpegLocation` (ffmpeg לא מותקן) או להתקין ffmpeg ✋ (root)
- [ ] `php maintenance/run.php update`
- [ ] שלושת הקבצים הפגומים: לנסות `deleteBatch`. אם זה לא מצליח, למחוק מ־`image` ומתיקיית `images/`. הגיבוי קיים

## שלב 6 – רשות (סעיפים 10, 11) ✋
- [ ] רישיון: לעדכן `$wgRightsText`, `$wgRightsUrl` ו־`$wgRightsIcon` אחרי שתבחר
- [ ] Phonos: להגדיר מנוע TTS (למשל Google, דורש מפתח API), או לבטל את ההרחבה

## שלב 7 – האתר העברי (סעיף 8) ✋
- [ ] chabadpedia.co.il בשרת אחר? צריך גישה. שם: לבטל את Google Analytics Integration או למחוק את `$wgGoogleAnalyticsAccount`

## לעתיד – AI ותוספים (סעיף 12)
- Wanda, CirrusSearch ו־Elasticsearch: צריך root ו־Docker, וגם להפסיק לבזבז זיכרון (כרגע 12GB בשימוש. לבדוק מה צורך)
- Kartographer וייצוא ל־PDF: לבדוק אחרי שיהיו הרשאות root
