<?php namespace Core;

class Wysiwyg
{
    protected static $html_footer;
    protected static $default = 'summernote';

    public static function init( string $id, array $o = null )
    {
        return self::summernote( $id, $o );
    }

    public static function head() { }
    public static function body() { }

/**
 * Generowanie edytora summernote
 * @param  string $id
 * @param  array  $o
 * @return string
 */
    protected static function summernote( string $id, array $o )
    {
        $height = (!empty( $o['height'] ) ? $o['height'] : 300 );
        $mode = (!empty( $o['mode'] ) ? $o['mode'] : 'default');

        switch( $mode ) {
            default:
                $toolbar = '[' .
					'[\'style\', [\'style\', \'addclass\']],' .
					'[\'font\', [\'bold\', \'underline\', \'clear\']],' .
					'[\'para\', [\'ul\', \'ol\', \'paragraph\']],' .
					'[\'table\', [\'table\']],' .
					'[\'insert\', [\'link\', \'picture\', \'videoAttributes\',\'media\', \'grid\',]],' .
					'[\'view\', [\'fullscreen\', \'codeview\', \'help\']]' .
				']';
            break;

            case "lite":
                $toolbar = '[' .
					'[\'style\', [\'style\']],' .
					'[\'font\', [\'bold\', \'italic\', \'underline\', \'clear\']],' .
					'[\'para\', [\'ul\', \'ol\', \'paragraph\']],' .
					'[\'insert\', [\'link\']],' .
					'[\'view\', [\'fullscreen\', \'codeview\', \'help\']]' .
				']';
            break;
        }

        $html[] = '<script type="text/javascript">$(document).ready(function() {';

        if (!is_array($id)) {
			$html[] = '$("' . $id . '").summernote({disableDragAndDrop: true, height: ' . $height . ',lang: \'pl-PL\',
				addclass: {
					debug: false,
					classTags: [{title:"Lista zielona", value:"list-green"}]
				},
				toolbar: ' . $toolbar . ',
				popover: {image: [' .
                '[\'resize\', [\'resizeFull\',\'resizeHalf\',\'resizeQuarter\',\'resizeNone\']],' .
                '[\'custom\', [\'imageShapes\']],' .
				'[\'float\', [\'floatLeft\',\'floatRight\',\'floatNone\']],[\'remove\', [\'removeMedia\']],[\'custom\', [\'imageTitle\']],' .
				'],table: [' .
					'[\'add\', [\'addRowDown\', \'addRowUp\', \'addColLeft\', \'addColRight\']],' .
					'[\'delete\', [\'deleteRow\', \'deleteCol\', \'deleteTable\']],' .
					'[\'custom\', [\'mergeCell\',\'mergeRow\']],'.
					'[\'custom\', [\'tableStyles\']],'.
				  '],},
				callbacks: { onImageUpload: function( file ) {' .
                    'summernote_uploader( file, "' . $id . '" );},
					onMediaDelete: function(target) { summernote_delete_file(target[0].src); }' .
				'},
				codemirror: {
					theme: \'hopscotch\',
                    mode: \'text/html\',
					htmlMode: true,
					lineNumbers: true,
					tabMode: \'indent\'}' .
            '});';
        }
        $html[] = '});</script>';

        self::$html_footer = implode($html);
        return self::$html_footer;
    }

}
