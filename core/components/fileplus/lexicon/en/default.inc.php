<?php
/**
 * Default Lexicon Entries for File+
 *
 * @package fileplus
 * @subpackage lexicon
 */
$_lang['fileplus'] = 'File+';
$_lang['fileplus.title'] = 'Title';
$_lang['fileplus.description'] = 'Description';
$_lang['fileplus.decimal_separator'] = '.';
/** Input options render **/
$_lang['fileplus.input_section'] = 'File+ Options';
$_lang['fileplus.input_section_desc'] = 'Please read the <a href="https://jako.github.io/FilePlus/usage/" target="_blank">documentation</a> for more information about the options.';
$_lang['fileplus.allowedFileTypes'] = 'Allowed File Types';
$_lang['fileplus.allowedFileTypes_desc'] = '(Optional) A comma-separated list of the file extensions, that can be uploaded and selected, i.e. pdf,docx,zip. Overrides the fileplus.allowed_file_types system setting. Without both, the upload_files system setting is used.';
$_lang['fileplus.uploadPath'] = 'Upload Path';
$_lang['fileplus.uploadPath_desc'] = '(Optional) The path in the media source for uploaded files. Overrides the fileplus.upload_path system setting. Available placeholders: [[+year]], [[+month]], [[+day]], [[+user]], [[+username]], [[+resource_id]], [[+tv_id]].';
$_lang['fileplus.allowTitle'] = 'Show Title Field';
$_lang['fileplus.allowTitle_desc'] = 'Allow the user to enter a title for the file.';
$_lang['fileplus.allowDescription'] = 'Show Description Field';
$_lang['fileplus.allowDescription_desc'] = 'Allow the user to enter a description for the file.';
/** Output options render **/
$_lang['fileplus.outputChunk'] = 'Output Chunk';
$_lang['fileplus.outputChunk_desc'] = 'Select a chunk for the template variable output. Leave blank for the url of the file.';
/** Placeholder descriptions */
$_lang['fileplus.placeholder.url'] = 'URL of the file';
$_lang['fileplus.placeholder.file'] = 'Path of the file in the media source';
$_lang['fileplus.placeholder.filename'] = 'Filename with extension';
$_lang['fileplus.placeholder.name'] = 'Filename without extension';
$_lang['fileplus.placeholder.extension'] = 'File extension';
$_lang['fileplus.placeholder.size'] = 'File size in bytes (0 for files outside of the file system)';
$_lang['fileplus.placeholder.size_formatted'] = 'Formatted file size';
$_lang['fileplus.placeholder.title'] = 'Title';
$_lang['fileplus.placeholder.description'] = 'Description';
/** Upload browser **/
$_lang['fileplus.select_file'] = 'Select file …';
$_lang['fileplus.open_file'] = 'Open file';
$_lang['fileplus.uploading'] = 'Uploading the file …';
$_lang['fileplus.error_file_not_set'] = 'No file specified.';
$_lang['fileplus.error_upload'] = 'The file could not be uploaded.';
$_lang['fileplus.error_extension'] = 'The file type [[+extension]] is not allowed. Allowed file types: [[+allowed]]';
