# סטטוס – chabadpedia.com

עודכן: 24.11.2026

## סביבה
- MediaWiki 1.43.1, עיצוב Vector 2022, ‏PHP 8.3, ‏MariaDB, שפת האתר: `en`
- שרת: 8 ליבות, ‏15GB RAM (כ־3.4GB זמינים), דיסק 154GB (39GB פנויים)
- משתמש `chabadpediacom` **בלי sudo/root**. אין גישה לקונפיגורציית nginx (‏CloudPanel)
- Cloudflare לפני האתר: בקשות אוטומטיות מקבלות 403 (challenge). בדיקות נעשו מול השרת ישירות (`--resolve ...:127.0.0.1`)
- אין crontab למשתמש. Docker ו־ffmpeg לא מותקנים

## מה בוצע
- ✅ התחברות SSH עם מפתח (מפתח `id_rsa` מהמחשב שלך). תוקנו הרשאות תיקיית הבית ל־750
- ✅ גיבוי למאגר פרטי `bashari10/chabadpediacom` ב־GitHub: קוד האתר ובסיס הנתונים דחוס ומפוצל (`db/`)
- ✅ גיבוי תמונות בשרת: `~/backups/chabadpedia-images-2026-11-24.tar` (‏4.6GB)
- ✅ מפתח Deploy בשרת (`~/.ssh/github_backup_deploy`, כינוי `github-backup`)

## מצב המשימות (מסמך "משימות שרת")
| # | משימה | מצב בפועל |
|---|---|---|
| 1 | חיפוש | ⚠️ ה־API מחזיר תוצאות (`Rebbe` → ‏328). ב־`searchindex` יש 27,969 שורות. צריך לבדוק בממשק ולבנות אינדקס מחדש לוודא כיסוי מלא |
| 2 | `rest.php` (השלמה אוטומטית) | ❌ ‏`/rest.php/v1/search/title` מחזיר 404. כנראה כלל השכתוב של nginx |
| 3 | Sitemap | ❌ אין. ‏`/sitemap.xml` מפנה ל־`/Sitemap.xml` (דף ויקי) |
| 4 | robots.txt | ❌ חוסם רק `/w/` ו־`/wiki/...`, נתיבים שלא קיימים |
| 5 | canonical | ✅ הוגדר `$wgEnableCanonicalServerLink = true` (24.11). נבדק ב־Main_Page ובדף הפניה |
| 6 | נגן אודיו (TimedMediaHandler) | ⚠️ ההרחבה **כבר טעונה**, אבל `$wgFFmpegLocation=/usr/bin/ffmpeg` ו־ffmpeg לא מותקן. צריך לבדוק את השגיאה בדף אמיתי |
| 7 | שלושה קבצים פגומים | ⏳ לא נבדק |
| 8 | Google Analytics באתר העברי | ⏳ אתר אחר (chabadpedia.co.il), לא בשרת הזה? לבירור |
| 9 | Shraga לקבוצת bot | ✅ כבר חבר ב־bot (וגם ב־sysop, bureaucrat ו־interface-admin) |
| 10 | רישיון | ℹ️ כרגע GFDL 1.3. מחכה להחלטה |
| 11 | Phonos | ⏳ טעון, אין מנוע TTS |
| 12 | שאלות לבירור | ✅ נענו (ראה למטה) |

## ממצאים נוספים
- `/README.md` ושאר קבצי הטקסט בתיקיית האתר **נגישים לציבור**. לכן הקבצים האלה נשמרים במאגר הגיבוי ולא בתיקיית האתר
- `$wgDebugLogFile = '/var/www/en/debug.log'`: התיקייה לא קיימת
- ✅ **תוקן ב־24.11.2026:** שלושת הקבצים הבאים הועברו אל `~/backups/removed-from-webroot/` (הרשאות 700). עכשיו הם מחזירים 404, או מפנים לדף ויקי ריק. לא נמצאו קבצים רגישים נוספים. קודם לכן הם היו נגישים לציבור (HTTP 200):
  - `dump.sql`: דאמפ מלא של בסיס הנתונים (משתמשים, האשים של סיסמאות, אימיילים)
  - `LocalSettings.php.bak_20260222_040418`: סיסמת בסיס הנתונים ו־`$wgSecretKey`, מוגש כטקסט גולמי
  - `adminer.php`: ממשק כניסה לבסיס הנתונים
  - Cloudflare challenge מקשה על בוטים, אבל לא חוסם גולש אנושי
- ✅ הוחלפו `$wgSecretKey`, ‏`$wgUpgradeKey` ו־`$wgAuthenticationTokenVersion` (‏1→2). עותק קודם: `~/backups/removed-from-webroot/LocalSettings.php.before-key-rotation-2026-11-24`
- ❌ סיסמת בסיס הנתונים עדיין לא הוחלפה (צריך CloudPanel)
- `$wgMainCacheType = CACHE_DB`: טבלת `objectcache` שוקלת 1.7GB

## תשובות לסעיף 12
- **סוג שרת:** VPS, אבל למשתמש הזה אין root/sudo. כדי להתקין שירותים צריך גישת root או את ממשק CloudPanel
- **RAM:** ‏15GB סך הכול, כ־3.4GB זמינים. Elasticsearch עם 2GB אפשרי, אבל צפוף
- **Docker:** לא מותקן. צריך root
- **הרשאות:** יש כתיבה ל־`extensions/` ול־`LocalSettings.php`. אפשר להגדיר cron למשתמש (`crontab -e`)
