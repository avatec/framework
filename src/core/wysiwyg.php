<?php namespace Core;

class Wysiwyg
{
    protected static $default = 'summernote';

    public static function init($o)
    {
    }

    public static function head()
    {
    }

    public static function body()
    {
    }

    public static function ckeditor($id, $o = null)
    {
        $token = '123456789';
        $html[] = '<script src="https://cdn.ckeditor.com/ckeditor5/22.0.0/classic/ckeditor.js"></script>';
        $html[] = '<script>ClassicEditor.create( document.querySelector( \'' . $id . '\' ),' .
        'plugins: [ SimpleUploadAdapter ], simpleUpload: { uploadUrl: \'/api/standard/system/CkEditorUploader\', withCredentials: true, headers: { \'X-CSRF-TOKEN\': \'' . $token . '\' }} )' .
        '.then( editor => { console.log( editor ); } )' .
        '.catch( error => { console.error( error ); } );' .
        '</script>';
        return implode($html);
    }

    public static function summernote($o)
    {
        if (empty($o['id'])) {
            return '<p class="alert alert-danger">Summernote requires id param</p>';
        } else {
            $id = $o['id'];
        }

        $height = (!empty( $o['height'] ) ? $o['height'] : 250);

        $html[] = '<script type="text/javascript">' .
        '$(document).ready(function() {';
        if (!is_array($id)) {
            $html[] = '$("' . $id . '").summernote({' . PHP_EOL .
                'disableDragAndDrop: true, height: ' . $height . ', lang: \'pl-PL\',' . PHP_EOL .
                'cleaner:{ ' .
                    'action: \'both\',' .
                    'newline: \'<br>\',' .
                    'notStyle: \'position:absolute;top:0;left:0;right:0\',' .
                    'keepHtml: true,' .
                    'keepOnlyTags: [\'<p>\', \'<i>\', \'<br>\', \'<ul>\', \'<ol>\', \'<li>\', \'<b>\', \'<strong>\',\'<i>\', \'<a>\'],' .
                    'keepClasses: false,' .
                    'badAttributes: [\'style\', \'start\', \'class\', \'data\'],' .
                    'limitChars: false,' .
                    'limitDisplay: \'both\',' .
                    'limitStop: false},' . PHP_EOL .
                'toolbar: [' . PHP_EOL .
                    '[\'style\', [\'bold\', \'italic\', \'underline\', \'clear\']],' . PHP_EOL .
                    '[\'color\', [\'color\']],' . PHP_EOL .
                    '[\'insert\', [\'table\',\'link\',\'picture\']],' . PHP_EOL .
                    '[\'para\', [\'ul\', \'ol\', \'paragraph\']],' . PHP_EOL .
                    '[\'view\', [\'image\', \'codeview\']],' . PHP_EOL .
                    '[\'custom\', [\'\']]' . PHP_EOL .
                '],' .
                'popover: {' . PHP_EOL .
                'table: [' .
                '[\'add\', [\'addRowDown\', \'addRowUp\', \'addColLeft\', \'addColRight\', \'toggle\']],' .
                '[\'delete\', [\'deleteRow\', \'deleteCol\', \'deleteTable\']],' .
                '[\'custom\', [\'tableHeaders\']]' .
                '],' . PHP_EOL .
                'image: [' .PHP_EOL .
                '[\'imagesize\', [\'imageSize100\',\'imageSize50\',\'imageSize25\']],' .PHP_EOL .
                '[\'float\', [\'floatLeft\',\'floatRight\',\'floatNone\']],[\'remove\', [\'removeMedia\']],[\'custom\', [\'imageTitle\']],' .PHP_EOL .
                ']},callbacks: { onImageUpload: function( file ) {' . PHP_EOL .
                    'summernote_uploader( file, "' . $id . '" );},' . PHP_EOL .
					'onMediaDelete: function(target) { summernote_delete_file(target[0].src); }' .PHP_EOL .
                '}});';
        }
        $html[] = '});' .
        '</script>';

        return implode($html);
    }
}
