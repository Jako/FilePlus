<?php
/**
 * Default Lexicon Entries for File+
 *
 * @package fileplus
 * @subpackage lexicon
 */
$_lang['fileplus'] = 'File+';
$_lang['fileplus.title'] = 'Titel';
$_lang['fileplus.description'] = 'Beschreibung';
$_lang['fileplus.decimal_separator'] = ',';
/** Input options render **/
$_lang['fileplus.input_section'] = 'File+ Optionen';
$_lang['fileplus.input_section_desc'] = 'Weitere Informationen zu den Optionen finden Sie in der <a href="https://jako.github.io/FilePlus/usage/" target="_blank">Dokumentation</a>.';
$_lang['fileplus.allowedFileTypes'] = 'Erlaubte Dateitypen';
$_lang['fileplus.allowedFileTypes_desc'] = '(Optional) Eine kommaseparierte Liste der Dateiendungen, die hochgeladen und ausgewählt werden können, z. B. pdf,docx,zip. Überschreibt die Systemeinstellung fileplus.allowed_file_types. Ohne beide wird die Systemeinstellung upload_files verwendet.';
$_lang['fileplus.uploadPath'] = 'Upload-Pfad';
$_lang['fileplus.uploadPath_desc'] = '(Optional) Der Pfad in der Medienquelle für hochgeladene Dateien. Überschreibt die Systemeinstellung fileplus.upload_path. Verfügbare Platzhalter: [[+year]], [[+month]], [[+day]], [[+user]], [[+username]], [[+resource_id]], [[+tv_id]].';
$_lang['fileplus.allowTitle'] = 'Titelfeld anzeigen';
$_lang['fileplus.allowTitle_desc'] = 'Der Benutzer kann einen Titel für die Datei eingeben.';
$_lang['fileplus.allowDescription'] = 'Beschreibungsfeld anzeigen';
$_lang['fileplus.allowDescription_desc'] = 'Der Benutzer kann eine Beschreibung für die Datei eingeben.';
/** Output options render **/
$_lang['fileplus.outputChunk'] = 'Ausgabe-Chunk';
$_lang['fileplus.outputChunk_desc'] = 'Wählen Sie einen Chunk für die Ausgabe der Template-Variable. Ohne Chunk wird die URL der Datei ausgegeben.';
/** Placeholder descriptions */
$_lang['fileplus.placeholder.url'] = 'URL der Datei';
$_lang['fileplus.placeholder.file'] = 'Pfad der Datei in der Medienquelle';
$_lang['fileplus.placeholder.filename'] = 'Dateiname mit Endung';
$_lang['fileplus.placeholder.name'] = 'Dateiname ohne Endung';
$_lang['fileplus.placeholder.extension'] = 'Dateiendung';
$_lang['fileplus.placeholder.size'] = 'Dateigröße in Bytes (0 für Dateien außerhalb des Dateisystems)';
$_lang['fileplus.placeholder.size_formatted'] = 'Formatierte Dateigröße';
$_lang['fileplus.placeholder.title'] = 'Titel';
$_lang['fileplus.placeholder.description'] = 'Beschreibung';
/** Upload browser **/
$_lang['fileplus.select_file'] = 'Datei auswählen …';
$_lang['fileplus.open_file'] = 'Datei öffnen';
$_lang['fileplus.uploading'] = 'Die Datei wird hochgeladen …';
$_lang['fileplus.error_file_not_set'] = 'Keine Datei angegeben.';
$_lang['fileplus.error_upload'] = 'Die Datei konnte nicht hochgeladen werden.';
$_lang['fileplus.error_extension'] = 'Der Dateityp [[+extension]] ist nicht erlaubt. Erlaubte Dateitypen: [[+allowed]]';
