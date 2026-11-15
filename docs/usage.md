## Input options

| Option | Description | Default |
|--------|-------------|---------|
| Allowed File Types | A comma-separated list of the file extensions, that can be uploaded and selected, i.e. `pdf,docx,zip`. Overrides the `fileplus.allowed_file_types` system setting. | The `upload_files` system setting |
| Upload Path | The path in the media source for uploaded files. Overrides the `fileplus.upload_path` system setting. | The media source root |
| Show Title Field | Allow the user to enter a title for the file. | No |
| Show Description Field | Allow the user to enter a description for the file. | No |

The upload path can contain the following placeholders: `[[+year]]`,
`[[+month]]`, `[[+day]]`, `[[+user]]`, `[[+username]]`, `[[+resource_id]]` and
`[[+tv_id]]`.

The file type and the upload path of an upload are checked with the options of
the template variable on the server. The media source checks the file types in
its `allowedFileTypes` property and the `upload_files` and `upload_maxsize`
system settings additionally.

## Output options

| Option | Description |
|--------|-------------|
| Output Chunk | A chunk for the template variable output. Without a chunk, the url of the file is returned. |

## Placeholders

The following placeholders are available in the output chunk:

| Placeholder | Description |
|-------------|-------------|
| url | URL of the file |
| file | Path of the file in the media source |
| filename | Filename with extension |
| name | Filename without extension |
| extension | File extension |
| size | File size in bytes (0 for files outside of the file system, i.e. in an S3 media source) |
| size_formatted | Formatted file size, i.e. 1.4 MB |
| title | Title |
| description | Description |

## Snippet

The FilePlus snippet can be used as alternative to the template variable output.

| Property | Description | Default |
|----------|-------------|---------|
| tvname | Name of the File+ template variable. | - |
| docid | Resource where the template variable value is received from. | Current resource |
| value | File+ value, i.e. of a MIGX field. Used instead of the template variable value. | - |
| source | Media source ID, if the value is passed with the value property. | Default media source |
| tpl | Template chunk for the snippet output. Without a template chunk, the URL of the file is returned. | FilePlus.file |

```
[[FilePlus? &tvname=`download` &tpl=`FilePlus.file`]]
```

## System settings

| Key | Description | Default |
|-----|-------------|---------|
| fileplus.allowed_file_types | A comma-separated list of the file extensions, that can be uploaded and selected in the File+ template variables. | - |
| fileplus.upload_path | The path in the media source for uploaded files. | - |
| fileplus.debug | Log debug information in the MODX error log and load the unminified scripts. | No |

## Template variable value

The template variable value is a JSON encoded object:

```json
{
  "file": "downloads/brochure.pdf",
  "title": "Brochure",
  "description": "Our brochure as PDF"
}
```

A plain file path (i.e. of a file template variable, that was changed to
File+) is used as file without a title and a description.

## MIGX and Collections

Use `FilePlus.ColumnRenderer` as renderer of a grid column to show the
filename (or the title) of an File+ value.
