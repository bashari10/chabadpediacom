# סטטוס – chabadpedia.com

עודכן: 24.11.2026

## סביבה
- MediaWiki 1.43.1, עיצוב Vector 2022, ‏PHP 8.3, ‏MariaDB, שפת האתר: `en`
- שרת: 8 ליבות, ‏15GB RAM (כ־3.4GB זמינים), דיסק 154GB (39GB פנויים)
- משתמש `chabadpediacom` **בלי sudo/root**. אין גישה לקונפיגורציית nginx (‏CloudPanel). ‏PHP-FPM 8.1, פורט 16002
- Cloudflare לפני האתר: בקשות אוטומטיות מקבלות 403 (challenge). בדיקות נעשו מול השרת ישירות (`--resolve ...:127.0.0.1`)
- crontab: יצירת sitemap כל לילה ב־03:30. Docker לא מותקן. ffmpeg 6.1 מותקן (24.11). espeak-ng ו־lame מותקנים (24.11). גישת root קיימת דרך SSH עם מפתח

## מה בוצע
- ✅ התחברות SSH עם מפתח (מפתח `id_rsa` מהמחשב שלך). תוקנו הרשאות תיקיית הבית ל־750
- ✅ גיבוי למאגר פרטי `bashari10/chabadpediacom` ב־GitHub: קוד האתר ובסיס הנתונים דחוס ומפוצל (`db/`)
- ✅ גיבוי תמונות בשרת: `~/backups/chabadpedia-images-2026-11-24.tar` (‏4.6GB)
- ✅ מפתח Deploy בשרת (`~/.ssh/github_backup_deploy`, כינוי `github-backup`)

## מצב המשימות (מסמך "משימות שרת")
| # | משימה | מצב בפועל |
|---|---|---|
| 1 | חיפוש | ✅ האינדקס נבנה מחדש (24.11): ‏25,052 שורות, אחת לכל דף. Rebbe → ‏11,042, ‏Lubavitch → ‏3,360, ‏Tanya → ‏1,676. החיפוש המלא עובד. ההשלמה האוטומטית תלויה ב־rest.php (סעיף 2) |
| 2 | `rest.php` (השלמה אוטומטית) | ✅ תוקן (24.11): בלוק `location ^~ /rest.php/` עם fastcgi ישיר ל־`127.0.0.1:16002` ו־`REQUEST_URI $request_uri`. ‏`/rest.php/v1/search/title?q=Rebbe` מחזיר JSON |
| 3 | Sitemap | ✅ נוצר ב־`sitemap/` (24.11): ‏16 קבצים, ‏11,945 ערכים בלי הפניות. מוגש כ־`text/xml`. ‏cron כל לילה ב־03:30, לוג ב־`~/logs/sitemap.log` |
| 4 | robots.txt | ✅ הוחלף לפי המסמך (24.11). הגרסה הישנה, כולל Crawl-delay ל־Bing ו־Google, שמורה ב־`~/backups/removed-from-webroot/` |
| 5 | canonical | ✅ הוגדר `$wgEnableCanonicalServerLink = true` (24.11). נבדק ב־Main_Page ובדף הפניה |
| 6 | נגן אודיו (TimedMediaHandler) | ✅ בצד השרת: `[[File:x.mp3]]` מ־cphe מוצג כ־`<audio>` עם מקור תקין (200 audio/mpeg). טבלת `transcode` קיימת, ולכן update.php לא נדרש. הקישורים בדפים הם בעיקר `[[Media:...]]`, כלומר קישור ישיר ולא נגן. לא נבדק בדפדפן. ffmpeg הותקן (24.11): תמונה ממוזערת לסרטון `ניגון_לשבת_ויום_טוב.mp4` נוצרה מחדש. המרות VP9 ישנות נכשלו (timeout), אבל הקובץ הוא h264/aac ומתנגן ישירות בכל דפדפן |
| 7 | שלושה קבצים פגומים | ✅ הסיבה: אלה דפי הפניה באתר העברי, וה־API שלו מחזיר את היעד עם התחילית `קובץ:`, שהאתר האנגלי לא הכיר. נוסף `$wgNamespaceAliases['קובץ'/'תמונה'] = NS_FILE`. שלושת הקבצים נטענים, וה־API מחזיר 200. לא נמחק כלום |
| 8 | Google Analytics באתר העברי | ✅ הושבת (24.11) דרך חיבור ChabadPediaCoIl: השורות של ההרחבה ושל `$wgGoogleAnalyticsAccount` הפכו להערות. אין קוד GA בדף הבית וב־Common.js |
| 9 | Shraga לקבוצת bot | ✅ כבר חבר ב־bot (וגם ב־sysop, bureaucrat ו־interface-admin) |
| 10 | רישיון | ✅ אנגלית (24.11): קנייני, `All rights to the text are reserved to Nostalgius Inc.`, ‏`Project:Terms of Use`, בלי url ובלי אייקון. נבדק ב־siteinfo ובתחתית הדף. הוסר `<img>` ריק של סמל הרישיון (`unset($wgFooterIcons['copyright'])`). ✅ עברית (24.11): `כל הזכויות על הטקסט שמורות ל-Nostalgius Inc.`, ‏`Project:תנאי שימוש`. נבדק ב־siteinfo |
| 11 | Phonos | ✅ eSpeak ו־lame הותקנו (24.11). `$wgPhonosEspeak` מצביע על `~/bin/phonos-espeak`, סקריפט שמוסיף את `en` לרשימת הקולות. נבדק עם `lang="en"`, בלי lang, עם `en-us` ועם `he`: כולם מייצרים mp3 תקין שמוגש כ־200. ‏`{{Audio|file.mp3}}` מנגן בלי שגיאה. לא נבדק בדפדפן |
| 12 | שאלות לבירור | ✅ נענו (ראה למטה) |

## האתר העברי (chabadpedia.co.il)
- ✅ robots.txt ו־sitemap (24.11): ‏`/sitemap/sitemap-index-chabadpediacoil.xml`, ‏23 קבצים, ‏12,773 ערכים במרחב הראשי. שניהם מוגשים כקבצים (200)
- ✅ cron לילי ב־04:00 (24.11), לוג ב־`~/logs/sitemap.log` של המשתמש chabadpedia

## ממצאים נוספים
- PHP opcache: שינוי ב־`LocalSettings.php` נקלט רק אחרי כמה שניות
- `/README.md` ושאר קבצי הטקסט בתיקיית האתר **נגישים לציבור**. לכן הקבצים האלה נשמרים במאגר הגיבוי ולא בתיקיית האתר
- ✅ `$wgDebugLogFile` (נתיב שלא קיים) הושבת. לדיבאג זמני: `~/logs/mediawiki-debug.log`
- ✅ **תוקן ב־24.11.2026:** שלושת הקבצים הבאים הועברו אל `~/backups/removed-from-webroot/` (הרשאות 700). עכשיו הם מחזירים 404, או מפנים לדף ויקי ריק. לא נמצאו קבצים רגישים נוספים. קודם לכן הם היו נגישים לציבור (HTTP 200):
  - `dump.sql`: דאמפ מלא של בסיס הנתונים (משתמשים, האשים של סיסמאות, אימיילים)
  - `LocalSettings.php.bak_20260222_040418`: סיסמת בסיס הנתונים ו־`$wgSecretKey`, מוגש כטקסט גולמי
  - `adminer.php`: ממשק כניסה לבסיס הנתונים
  - Cloudflare challenge מקשה על בוטים, אבל לא חוסם גולש אנושי
- ✅ הוחלפו `$wgSecretKey`, ‏`$wgUpgradeKey` ו־`$wgAuthenticationTokenVersion` (‏1→2). עותק קודם: `~/backups/removed-from-webroot/LocalSettings.php.before-key-rotation-2026-11-24`
- ❌ סיסמת בסיס הנתונים עדיין לא הוחלפה (צריך CloudPanel)
- `$wgMainCacheType = CACHE_DB`: טבלת `objectcache` ירדה מ־1,716MB ל־1,380MB אחרי OPTIMIZE (24.11, ‏11 שניות). רק 13 רשומות מתוך 733 אלף פגו, ולכן אין מה למחוק. ‏486MB מהנפח הם מטמון `diff` (‏166 אלף השוואות גרסאות, כנראה מבוטים). ה־robots.txt החדש חוסם אותם, והנפח אמור לרדת תוך כשבוע
- דיסק: 33GB פנויים (הגיבויים ב־`~/backups` תופסים כ־5.6GB)

## תשובות לסעיף 12
- **סוג שרת:** VPS, אבל למשתמש הזה אין root/sudo. כדי להתקין שירותים צריך גישת root או את ממשק CloudPanel
- **RAM:** ‏15GB סך הכול, כ־3.4GB זמינים. Elasticsearch עם 2GB אפשרי, אבל צפוף
- **Docker:** לא מותקן. צריך root
- **הרשאות:** יש כתיבה ל־`extensions/` ול־`LocalSettings.php`. אפשר להגדיר cron למשתמש (`crontab -e`)
